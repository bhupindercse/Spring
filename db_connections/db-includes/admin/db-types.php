<?php

// =======================================================================================================================================================
//		CANDY TYPES FUNCTIONS
// =======================================================================================================================================================
function types_add()
{
	$base_url   = "../";
	$connection = DBConn::getInstance();
	Input::exists();
		
	$id          = Input::get('id');
	$title       = trim(Input::get('title'));
	$content     = trim(Input::get('content'));
	$link        = trim(Input::get('link'));
	$permalink   = trim(Input::get('permalink'));
	$main_pic    = "";
	$delete_pic  = Input::get('delete_pic');
	$current_pic = Input::get('current_pic');
	$token       = Input::get('token');
	$errors      = array();

	// Check nonce token
	if(!Token::check($token, "admin_login_token"))
	{
		$errors['general'] = "Cannot verify you.";
		return $errors;
	}
	
	// ==============================
	//	ERROR CHECK
	// ==============================
	if(empty($id))
		$id = NULL;
		
	if(isset($_FILES["img"]["name"]))
		$main_pic = $_FILES["img"]["name"];
	
	if(empty($title))
		$errors['title'] = "Please enter a title.";
	if((empty($main_pic) && empty($current_pic)) || (!empty($delete_pic) && empty($main_pic)))
		$errors['img'] = "Please upload an image for this candy type.";
				
	// ===================================
	//	Ensure permalink is there
	// ===================================
	if(empty($permalink))
	{
		$permalink = Permalinks::createPermalink($title);
	}
	
	if(count($errors))
		return $errors;
	
	// ===================================================
	//	GET QUERY READY FOR POTENTIAL EXISTING RECORDS
	// ===================================================
	if(!empty($id))
	{
		try
		{
			$current_record = "";

			$statement = "SELECT * FROM ".Config::get('table_prefix')."types WHERE id = :id;";
			$current_record_query = $connection->conn->prepare($statement);
			$current_record_query->execute(array(':id' => $id));
			if($current_record_query->rowCount())
				$current_record = $current_record_query->fetch(PDO::FETCH_ASSOC);
		}catch(Exception $e){
			$errors['img'] = "Error finding the old image:<br>".$e->getMessage();
			return $errors;
		}
	}
	
	// ===========================================
	//	DELETE PIC (will always be during editing)
	// ===========================================
	if(!empty($delete_pic) && !empty($current_record))
	{	
		if(is_file($base_url.Config::get('type_images').$current_record['filename']))
		{
			if(!@unlink($base_url.Config::get('type_images').$current_record['filename']))
			{
				$errors['img'] = "Unable to delete the image located at ".$base_url.Config::get('type_images').$current_record['filename'];
				return $errors;
			}
		}
	}
	
	// ===================================================================================
	//	UPLOAD NEW MAIN IMAGE
	// ===================================================================================
	if(!empty($main_pic))
	{
		// see if pic name already exists
		$ext = substr($main_pic, strrpos($main_pic, "."));
		$img = substr($main_pic, 0, strrpos($main_pic, "."));

		// Make sure folder exists
		if(!is_dir($base_url.Config::get('type_images'))){
			// mkdir($base_url.Config::get('type_images'));
			$errors['img'] = $base_url.Config::get('type_images')." does not exist.";
			return $errors;
		}
		
		// Check extension
		if(!Utility::checkExtension($main_pic, "img"))
		{
			$errors['img'] = "You cannot upload a file of that type here.";
			return $errors;
		}

		// Get a name that does not exist
		try{
			$img = Utility::determineName($img, 1, $ext, Config::get('type_images'));
		}catch(Exception $e){
			$errors['img'] = "Error determining main image filename:<br>".$e->getMessage();
			return $errors;
		}

		$main_pic = $img.$ext;
		$target_path = $base_url.Config::get('type_images').$main_pic;

		// $errors['img'] = $main_pic;
		// return $errors;

		// Get rid of the old image
		try
		{
			if(!empty($id) && $current_record_query->rowCount())
			{
				if(is_file($base_url.Config::get('type_images').$current_record['filename']))
				{
					if(!@unlink($base_url.Config::get('type_images').$current_record['filename']))
					{
						$errors['img'] = "Unable to removing the image located at ".$base_url.Config::get('type_images').$current_record['filename'];
						return $errors;
					}
				}
			}
		}catch(Exception $e){
			$errors['img'] = "Error finding the image to remove:<br>".$e->getMessage();
			return $errors;
		}
		
		// Upload the main pic
		if(!move_uploaded_file($_FILES['img']['tmp_name'], $target_path))
		{
			$errors['img'] = Utility::check_file_error($_FILES['img']['error']);
			return $errors;
		}
			
		// ===========================
		//	Resize to width
		// ===========================
		list($width, $height, $type, $attr) = getimagesize($target_path);
		if($width != Config::get('types_img_width'))
		{
			$image = new SimpleImage();
			$image->load($target_path);
			$image->resizeToWidth(Config::get('types_img_width'));
			$image->save($target_path);
		}
	}
	
	// ===========================================
	//	EDITING & NEW PIC
	// ===========================================
	if(!empty($id) && !empty($main_pic))
	{
		if(!empty($current_record))
		{
			$filename = $base_url.Config::get('type_images').$current_record['filename'];
			
			if(is_file($filename))
			{
				if(!@unlink($filename))
				{
					$errors['img'] = "Unable to delete the old image from: ".$filename;
					return $errors;
				}
			}
		}
	}
	// ===========================================
	//	EDITING & SAME PIC
	// ===========================================
	elseif(!empty($id) && empty($main_pic) && empty($delete_pic))
	{
		if(!empty($current_record))
			$main_pic = $current_record['filename'];
	}
	
	if(empty($id))
		$id = NULL;
	
	try
	{
		// ===========================================
		//	INSERT ITEM INTO DB
		// ===========================================
		$statement = "	INSERT INTO ".Config::get('table_prefix')."types
						(id, title, permalink, content, link, filename)
						VALUES (:id, :title, :permalink, :content, :link, :filename)
						ON DUPLICATE KEY UPDATE
							title     = :title_update,
							permalink = :permalink_update,
							content   = :content_update,
							link      = :link_update,
							filename  = :filename_update;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id'               => $id,
			':title'            => $title,
			':permalink'        => $permalink,
			':content'          => $content,
			':link'             => $link,
			':filename'         => $main_pic,
			':title_update'     => $title,
			':permalink_update' => $permalink,
			':content_update'   => $content,
			':link_update'      => $link,
			':filename_update'  => $main_pic
		));
	}catch(Exception $e){
		
		$errors['general'] = "Error updating database: ".$e->getMessage();
		
		// If new and image upload, OR if existing and just uploaded an image
		if((is_null($id) && !empty($main_pic)) || !(is_null($id) && !empty($main_pic) && !empty($current_pic)))
		{
			try
			{
				if(is_null($id))
					@unlink($base_url.Config::get('type_images').$main_pic);
				else
					delete_type_images_and_record($id, array($main_pic));
			}catch(Exception $e){
				$errors['general'] .= $e->getMessage();
			}
		}
		
		return $errors;
	}
	
	// ===========================================
	//	OPTIMIZE
	// ===========================================
	try
	{
		$statement = "OPTIMIZE TABLE ".Config::get('table_prefix')."types;";
		$query = $connection->conn->prepare($statement);
		$query->execute();
	}catch(Exception $e){
		$errors['general'] = "Error optimizing system:<br>".$e->getMessage();
		return $errors;
	}
	
	if($id == NULL)
	{
		$id = $connection->conn->lastInsertId();
		Session::set(Config::get("table_prefix").'types-add-success', $id);
	}
	
	// success!!
	return $errors;
}

function types_delete()
{
	$base_url   = "../";
	$connection = DBConn::getInstance();
	Input::exists();
	$errors = array();
	
	if(Input::hasValue('id'))
		$id = Input::get('id');
	else
	{
		$errors['general'] = "Error: Unable to determine the post you are trying to delete.";
		return $errors;
	}
	
	try
	{	
		// =======================================================
		//	DELETE PIC
		// =======================================================
		// $statement = "SELECT ".Config::get('table_prefix')."types.*,
		// 					FROM ".Config::get('table_prefix')."types
		// 				WHERE ".Config::get('table_prefix')."types.id = :id;";
			$statement = "SELECT *
							FROM ".Config::get('table_prefix')."types
						WHERE id = :id;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id' => $id
		));
	}catch(Exception $e){
		$errors['general'] = "Error finding item: ".$e->getMessage();
		return $errors;
	}
	
	if($query->rowCount())
	{
		$data = $query->fetch(PDO::FETCH_ASSOC);
		
		// store permalink for rss
		$permalink = $data['permalink'];
		
		if(is_file($base_url.Config::get('type_images').$data['filename']))
		{
			if(!@unlink($base_url.Config::get('type_images').$data['filename']))
			{
				$errors['general'] = "Error deleting the image for this post located at: ".$base_url.Config::get('type_images').$data['filename'];
				return $errors;
			}
		}
	}
	
	try
	{
		// =======================================================
		//	DELETE MAIN POST
		// =======================================================
		$statement = "DELETE FROM ".Config::get('table_prefix')."types WHERE id = :id;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id' => $id
		));
	}catch(Exception $e){
		$errors['general'] = "Error deleting post:<br>".$e->getMessage();
		return $errors;
	}
	
	try
	{
		$statement = "OPTIMIZE TABLE ".Config::get('table_prefix')."types;";
		$query = $connection->conn->prepare($statement);
		$query->execute();
	}catch(Exception $e){
		$errors['general'] = "Error optimizing system:<br>".$e->getMessage();
		return $errors;
	}
	
	// success!!
	return $errors;
}

// ===================================================================================
//	UTILITY
// ===================================================================================
function delete_type_images_and_record($id, $img_array)
{
	foreach($img_array as $img){
		if(!@unlink($img))
			throw new Exception("<br>Unable to removing the image located at ".$img);
	}

	if(!empty($id))
	{
		try
		{
			$statement = "	UPDATE ".Config::get('table_prefix')."types
							SET filename = ''
							WHERE id = :id;";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(":id" => $id));
		}catch(Exception $e){
			throw new Exception("<br>Error removing image from the database: ".$e->getMessage());
		}
	}
}

?>