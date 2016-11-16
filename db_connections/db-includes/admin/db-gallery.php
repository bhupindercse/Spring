<?php

// =======================================================================================================================================================
//		GALLERY FUNCTIONS
// =======================================================================================================================================================
function gallery_add()
{
	Input::exists();

	$title       = trim(Input::get('title'));
	// $featured = Input::get('featured');
	$permalink   = trim(Input::get('permalink'));
	// $content  = trim(Input::get('content'));
	$token       = Input::get('token');
	$errors      = array();

	// Check nonce token
	if(!Token::check($token, "admin_login_token"))
	{
		$errors['general'] = "Cannot verify you.";
		return $errors;
	}
	
	if(empty($title))
		$errors['title'] = "Please enter something for the title of this gallery so you can identify it later on.";
	
	if(count($errors))
		return $errors;

	// ===================================
	//	Ensure permalink is there
	// ===================================
	if(empty($permalink))
	{
		$permalink = Permalinks::createPermalink($title);
	}
	
	// ===================================================================================
	//	ADD TO DB
	// ===================================================================================
	$connection = DBConn::getInstance();
	$connection->conn->beginTransaction();
	try
	{
		$statement = "	INSERT INTO ".Config::get('table_prefix')."gallery_albums
						(title, date_added) VALUES
						(:title, Now());";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':title' => $title
		));
	}catch(Exception $e){
		$connection->conn->rollBack();
		$errors['general'] = "Error adding gallery:<br>".$e->getMessage();
		return $errors;
	}

	// ===========================================
	//	GRAB GALLERY ID
	// ===========================================
	$id = $connection->conn->lastInsertId();

	// ===================================================================================
	//	ADD PERMALINK
	// ===================================================================================
	try
	{
		$statement = "	INSERT INTO ".Config::get('table_prefix')."gallery_permalinks
						(id, gallery_id, permalink, active)
						VALUES
						(NULL, :gallery_id, :permalink, :active);";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':gallery_id' => $id,
			':permalink'  => $permalink,
			':active'     => 1
		));
	}catch(Exception $e){
		$connection->conn->rollBack();
		$errors['general'] = "Error adding gallery:<br>".$e->getMessage();
		return $errors;
	}

	// ===================================
	//	Commit transactions!
	// ===================================
	$connection->conn->commit();

	Session::set(Config::get("table_prefix").'gallery-add-success', $id);
	
	return $errors;
}

function gallery_delete()
{
	$base_url          = "../";
	$errors            = array();
	$id                = "";
	$token             = Input::get('token');
	$gallery_permalink = Input::get('gallery_permalink');
	$delete_images     = false;

	// Check nonce token
	if(!Token::check($token, "admin_login_token"))
	{
		$errors['general'] = "Cannot verify you.";
		return $errors;
	}
	
	if(Input::hasValue('id'))
		$id = Input::get('id');
	if(Input::hasValue('delete_images', 'get'))
		$delete_images = Input::get('delete_images', 'get');
	
	if(empty($id))
	{
		$errors['general'] = "Cannot determine which gallery you are trying to delete.";
		return $errors;
	}

	// ===================================================================================
	//	Store directory
	// ===================================================================================
	$dir = $base_url.Config::get('gallery_files').$gallery_permalink;
	
	// ===================================================================================
	// 	DELETE IMAGES
	// ===================================================================================
	$connection = DBConn::getInstance();
	try
	{
		$statement = "SELECT * FROM ".Config::get('table_prefix')."gallery_images WHERE gallery_id = :id;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id' => $id
		));
		if($query->rowCount())
		{
			$deletions = array();
			while($data = $query->fetch(PDO::FETCH_ASSOC)){
				$deletions[] = $data['id'];
			}

			$errors = delete_gallery_images($deletions);
			if(count($errors))
				return $errors;
		}
	}catch(Exception $e){
		$errors['general'] = "Error finding gallery images:<br>".$e->getMessage();
		return $errors;
	}

	// ===================================================================================
	//	DELETE FOLDER
	// ===================================================================================
	if(is_dir($dir) && !rmdir($dir))
	{
		$errors['general'] = "Error deleting gallery folder:<br>".$dir;
		return $errors;
	}

	// ===================================================================================
	//	DELETE IMAGES FROM DB
	// ===================================================================================
	try
	{
		$statement = "DELETE FROM ".Config::get('table_prefix')."gallery_images WHERE gallery_id = :id;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id' => $id
		));
	}catch(Exception $e){
		$errors['general'] = "Error deleting gallery images from DB:<br>".$e->getMessage();
		return $errors;
	}
	
	// ===================================================================================
	//	DELETE GALLERY FROM DB
	// ===================================================================================
	try
	{
		$statement = "DELETE FROM ".Config::get('table_prefix')."gallery_albums WHERE id = :id;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id' => $id
		));
	}catch(Exception $e){
		$errors['general'] = "Error deleting gallery:<br>".$e->getMessage();
		return $errors;
	}
	
	try
	{
		$statement = "OPTIMIZE TABLE ".Config::get('table_prefix')."gallery_albums;";
		$query = $connection->conn->prepare($statement);
		$query->execute();
	}catch(Exception $e){
		$errors['general'] = "Error optimizing system:<br>".$e->getMessage();
		return $errors;
	}
	
	return $errors;
}

function delete_gallery_images($deletions = array())
{
	Input::exists();
	$base_url          = "../";
	$token             = Input::get('token');
	$gallery_permalink = Input::get('gallery_permalink');
	$errors            = array();
	
	if(Input::hasValue('delete'))
		$deletions = Input::get('delete');
	
	if(!count($deletions))
	{
		$errors['general'] = "You have not selected any images to delete.";
		return $errors;
	}

	$connection = DBConn::getInstance();
	
	// ==========================================================
	// 	Get images
	// ==========================================================
	try
	{
		$statement = "SELECT * FROM ".Config::get('table_prefix')."gallery_images WHERE id IN (";
		foreach($deletions as $item)
			$statement .= $item.",";
		$statement = substr($statement, 0, strlen($statement) - 1);
		$statement .= ");";
		$query = $connection->conn->prepare($statement);
		$query->execute();
	}catch(Exception $e){
		$errors['general'] = "Error finding filenames to delete:<br>".$e->getMessage();
	}
	
	if(!$query->rowCount())
		$errors['general'] = "Unable to find any of those filenames.";
	else
	{
		while($data = $query->fetch(PDO::FETCH_ASSOC)){
			$item = array();
			$item['img']   = $data['filename'];
			$item['thumb'] = $data['thumb'];
			$filenames[] = $item;
		}
	}
	
	if(count($errors))
		return $errors;
	
	// ==========================================================
	//  Delete physical img files
	// ==========================================================
	foreach($filenames as $item){
		if(!@unlink($base_url.Config::get('gallery_files').$gallery_permalink.'/'.$item['img']))
			$errors['deletions'][] = "Unable to delete main image because it was not found: ".$item['img'];
		// if(!@unlink($base_url.Config::get('gallery_files').$gallery_permalink.'/'.$item['thumb']))
		// 	$errors['deletions'][] = "Unable to delete thumb because it was not found: ".$item['thumb'];
	}

	if(count($errors))
		return $errors;

	// ==========================================================
	// 	Delete images from DB
	// ==========================================================
	try
	{
		$statement = "DELETE FROM ".Config::get('table_prefix')."gallery_images WHERE id IN (";
		foreach($deletions as $item)
			$statement .= $item.",";
		$statement = substr($statement, 0, strlen($statement) - 1);
		$statement .= ");";
		$query = $connection->conn->prepare($statement);
		$query->execute();
	}catch(Exception $e){
		$errors['general'] = "Error deleting image records from DB:<br>".$e->getMessage();
	}
	
	// success!!
	return $errors;
}

function edit_gallery_attributes()
{
	$base_url = "../";

	Input::exists();
	$token     = Input::get('token');
	$title     = Input::get('title');
	$permalink = trim(Input::get('permalink'));
	$captions  = array();
	$errors    = array();

	// Check nonce token
	if(!Token::check($token, "admin_login_token")){
		$errors['general'] = "Cannot verify you.";
		return $errors;
	}

	// ===================================
	//	Ensure permalink is there
	// ===================================
	if(empty($permalink))
	{
		$permalink = Permalinks::createPermalink($title);
	}
	
	// ==============================================================================
	//	ERROR CHECK
	// ==============================================================================
	$gallery_id = "";
	$image_ids  = array();

	$gallery_id = Input::get('gallery_id');
	
	if(empty($gallery_id))
		$errors['general'] = "Unable to determine the gallery you are trying to edit.";
		
	if(Input::hasValue('cover_image'))
		$cover_image = Input::get('cover_image');

	if(Input::hasValue('image_id'))
		$image_ids = Input::get('image_id');

	if(empty($title))
		$errors['general'] = "Please enter a title for this gallery.";
	
	if(count($errors))
		return $errors;
		
	// ==============================================================================
	//	CHECK FOR DELETIONS
	// ==============================================================================
	if(Input::hasValue('delete'))
	{
		$errors = delete_gallery_images(Input::get('delete'));
		if(count($errors))
			return $errors;
	}

	$connection = DBConn::getInstance();
	$connection->conn->beginTransaction();

	// ===================================
	//	UPDATE TITLE
	// ===================================
	try
	{
		$statement = "	UPDATE ".Config::get('table_prefix')."gallery_albums
						SET title = :title
						WHERE id = :id;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			":title" => $title,
			":id"    => $gallery_id
		));
	}catch(Exception $e){
		$connection->conn->rollBack();
		$errors['general'] = "Error updating gallery title:<br>".$e->getMessage();
		return $errors;
	}

	// ===================================
	//	Get old permalink
	// ===================================
	try
	{
		$statement = "	SELECT *
						FROM ".Config::get('table_prefix')."gallery_permalinks
						WHERE gallery_id = :gallery_id AND
							  active = 1;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(':gallery_id' => $gallery_id));
		if(!$query->rowCount()){
			$errors['general'] = "Cannot find old permalink.";
			return $errors;
		}

		$data = $query->fetch(PDO::FETCH_ASSOC);
		$old_permalink = $data['permalink'];

	}catch(Exception $e){
		$connection->conn->rollBack();
		$errors['general'] = "Error finding old permalink:<br>".$e->getMessage();
		return $errors;
	}
	

	// ===================================
	//	UPDATE PERMALINK
	// ===================================
	try
	{
		$statement = "	UPDATE ".Config::get('table_prefix')."gallery_permalinks
						SET permalink = :permalink
						WHERE gallery_id = :gallery_id AND
							  active = 1;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			":permalink"  => $permalink,
			":gallery_id" => $gallery_id
		));
	}catch(Exception $e){
		$connection->conn->rollBack();
		$errors['general'] = "Error updating gallery permalink:<br>".$e->getMessage();
		return $errors;
	}

	// ===================================
	//	RENAME IMAGE DIRECTORY
	// ===================================
	rename($base_url.Config::get('gallery_files').$old_permalink, $base_url.Config::get('gallery_files').$permalink);

	$connection->conn->commit();
	$connection->conn->beginTransaction();
	
	// ==============================================================================
	//	UPDATE CAPTIONS
	// ==============================================================================
	if(Config::get('gallery_image_text') && count($image_ids))
	{
		try
		{
			$query_array = array();
			$statement = "	UPDATE ".Config::get('table_prefix')."gallery_images SET caption = CASE";
			foreach($image_ids as $id)
			{
				$statement .= " WHEN id = ? THEN ?";
				$query_array[] = $id;
				$query_array[] = nl2br(Input::get('caption_'.$id));
			}
			$statement .= " ELSE caption
							END;";
			$query = $connection->conn->prepare($statement);
			$query->execute($query_array);
		}catch(Exception $e){
			$connection->conn->rollBack();
			$errors['general'] = "Error updating captions:<br>".$e->getMessage();
			return $errors;
		}
	}

	// ==============================================================================
	//	UPDATE COVER IMAGE
	// ==============================================================================
	if(count($image_ids))
	{
		try
		{
			// First, reset all cover images to zero
			$query_array = array();
			$statement = "	UPDATE ".Config::get('table_prefix')."gallery_images
							SET cover_image = 0 WHERE id IN (";
			foreach($image_ids as $id)
			{
				$statement .= "?,";
				$query_array[] = $id;
			}
			$statement = substr($statement, 0, strlen($statement) - 1);
			$statement .= ");";
			$query = $connection->conn->prepare($statement);
			$query->execute($query_array);

			// Second, update
			if(!empty($cover_image))
			{
				$statement = "	UPDATE ".Config::get('table_prefix')."gallery_images
								SET cover_image = 1 WHERE id = :id;";
				$query = $connection->conn->prepare($statement);
				$query->execute(array(":id" => $cover_image));
			}
		}catch(Exception $e){
			$connection->conn->rollBack();
			$errors['general'] = "Error updating cover image:<br>".$e->getMessage();
			return $errors;
		}
	}
	
	// ==============================================================================
	//	OPTIMIZE SYSTEM
	// ==============================================================================
	try
	{
		$statement = "OPTIMIZE TABLE ".Config::get('table_prefix')."gallery_images;";
		$query = $connection->conn->prepare($statement);
		$query->execute();
	}catch(Exception $e){
		$connection->conn->rollBack();
		$errors['general'] = "Error optimizing system:<br>".$e->getMessage();
		return $errors;
	}

	// ===================================
	//	Commit transactions!
	// ===================================
	$connection->conn->commit();
	
	// success!!
	return $errors;
}

?>