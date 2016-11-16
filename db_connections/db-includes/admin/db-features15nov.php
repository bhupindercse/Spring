<?php

// =======================================================================================================================================================
//		NEWS FUNCTIONS
// =======================================================================================================================================================
function features_add()
{
	//Config::get('images_url')   = "../";

	$base_url   = "../";
	$connection = DBConn::getInstance();
	Input::exists();
		
	$id          = Input::get('id');
	$title       = trim(Input::get('title'));
	$readmore    = trim(Input::get('readmore'));
	$content     = trim(Input::get('content'));
	$date_posted = trim(Input::get('date_posted'));
	$permalink   = trim(Input::get('permalink'));
	$rss         = 1;
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
	
	// ===========================================
	//	MAKE SURE RSS EXISTS
	// ===========================================
	// try
	// {
	// 	$xmlRead = new XMLParser(Config::get('news_rss_filename'));
	// }catch(Exception $e){
	// 	$errors['general'] = $e->getMessage();
	// 	return $errors;
	// }
	
	// ==============================
	//	ERROR CHECK
	// ==============================
	if(empty($id))
		$id = NULL;
	if(empty($rss))
		$rss = 0;
		
	if(isset($_FILES["img"]["name"]))
		$main_pic = $_FILES["img"]["name"];
	
	if(empty($title))
		$errors['title'] = "Please enter features_images title." ;
		
	if(empty($content))
		$errors['content'] = "Please enter some content for this posting.";
				
	// ===================================
	//	Ensure permalink is there
	// ===================================
	if(empty($permalink))
	{
		$permalink = Permalinks::createPermalink($title);
	}
	
	if(count($errors))
		return $errors;
	
	if(empty($date_posted))
		$date_posted = date("Y-m-d H:i:s");
	else	
	{
		$date_posted = strtotime($date_posted);
		$date_posted = date("Y-m-d", $date_posted)." ".date("H:i:s");
	}
	
	// ===================================================
	//	GET QUERY READY FOR POTENTIAL EXISTING RECORDS
	// ===================================================
	if(!empty($id))
	{
		try
		{
			$current_record = "";

			$statement = "SELECT * FROM ".Config::get('table_prefix')."features_images WHERE id = :id;";
			$current_record_query = $connection->conn->prepare($statement);
			$current_record_query->execute(array(':id' => $id));
			if($current_record_query->rowCount())
				$current_record = $current_record_query->fetch(PDO::FETCH_ASSOC);
		}catch(Exception $e){
			$errors['img'] = "Error finding the old image:<br>".$e->getMessage();
			return $errors;
		}
	}
	
	








if(!empty($id))
	{
		try
		{
			$current_record = "";

			$statement = "SELECT * FROM ".Config::get('table_prefix')."features_images WHERE id = :id;";
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
		if(is_file($base_url.'images/_features_images/'.$current_record['filename']))
		{
			if(!@unlink($base_url.'images/_features_images/'.$current_record['filename']))
			{
				$errors['img'] = "Unable to delete the image located at ".$base_url.'images/_features_images/'.$current_record['filename'];
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

		// Make sure folder exists, otherwise create one
		if(!is_dir($base_url.'images/_features_images/') ){
			
			mkdir($base_url.'images/_features_images/');
		}

		// Make sure folder exists, after creating
		if(!is_dir($base_url.'images/_features_images/') ){
			$errors['img'] = $base_url.'images/_features_images/'." does not exist.";
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
			$img = Utility::determineName($img, 1, $ext, 'images/_features_images/');
		}catch(Exception $e){
			$errors['img'] = "Error determining main image filename:<br>".$e->getMessage();
			return $errors;
		}

		$main_pic = $img.$ext;
		$target_path = $base_url.'images/_features_images/'.$main_pic;

		// $errors['img'] = $main_pic;
		// return $errors;

		// Get rid of the old image
		try
		{
			if(!empty($id) && $current_record_query->rowCount())
			{
				if(is_file($base_url.'images/_features_images/'.$current_record['filename']))
				{
					if(!@unlink($base_url.'images/_features_images/'.$current_record['filename']))
					{
						$errors['img'] = "Unable to removing the image located at ".$base_url.'images/_features_images/'.$current_record['filename'];
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
		//list($width, $height, $type, $attr) = getimagesize($target_path);
		$image = new SimpleImage();
		$image->load($target_path);
		$image->save($target_path);
	}
	
	// ===========================================
	//	EDITING & NEW PIC
	// ===========================================
	if(!empty($id) && !empty($main_pic))
	{
		if(!empty($current_record))
		{
			$filename = $base_url.'images/_features_images/'.$current_record['filename'];
			
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
		//	INSERT news INTO DB
		// ===========================================
		$statement = "	INSERT INTO ".Config::get('table_prefix')."features_images
						(id, 
						date_posted, 
						title, 
						readmore, 
						content, 
						filename)
						VALUES 
						(:id, 
						:date_posted, 
						:title, 
						:readmore, 
						:content, 
						:filename)
						ON DUPLICATE KEY UPDATE
						date_posted = :date_posted_update,
						title       = :title_update,
						readmore    = :readmore_update,
						content     = :content_update,
						filename    = :filename_update;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id'                 => $id,
			':date_posted'        => $date_posted,
			':title'              => $title,
			':readmore'           => $readmore,
			':content'            => $content,
			':filename'           => $main_pic,
			':date_posted_update' => $date_posted,
			':title_update'       => $title,
			':readmore_update'    => $readmore,
			':content_update'     => $content,
			':filename_update'    => $main_pic
		));
	}catch(Exception $e){
		
		$errors['general'] = "Error updating database: ".$e->getMessage();
		
		if(!empty($main_pic))
		{
			if(!@unlink(Config::get('absolute_url').Config::get('features_images').$main_pic))
				$errors['general'] .= "<br>Also, unable to delete the image you have uploaded to ".$main_pic;
		}
		
		return $errors;
	}
	
	// ===========================================
	//	OPTIMIZE
	// ===========================================
	// try
	// {
	// 	$statement = "OPTIMIZE TABLE ".Config::get('table_prefix')."features_images;";
	// 	$query = $connection->conn->prepare($statement);
	// 	$query->execute();
	// }catch(Exception $e){
	// 	$errors['general'] = "Error optimizing system:<br>".$e->getMessage();
	// 	return $errors;
	// }
	
	// $new_entry = false;
	// if($id == NULL)
	// {
	// 	$id = $connection->conn->lastInsertId();
	// 	Session::set(Config::get("table_prefix").'news-add-success', $id);
	// 	$new_entry = true;
	// }
	
	// ===========================================
	//	INSERT PERMALINK INTO DB
	// ===========================================
	// $statement = "";
	
	// // If new news, always insert new row
	// if($new_entry)
	// {
	// 	$statement = "	INSERT INTO ".Config::get('table_prefix')."features_images_permalinks
	// 					(id, item_id, permalink, active)
	// 					VALUES (:id, :item_id, :permalink, :active);";
	// 	$var_array = array(
	// 		':id'        => NULL,
	// 		':item_id'   => $id,
	// 		':permalink' => $permalink,
	// 		':active'    => 1
	// 	);
	// }
	// else
	// {
	// 	try
	// 	{
	// 		// Look for this post with the same permalink
	// 		$statement = "SELECT * FROM ".Config::get('table_prefix')."features_images_permalinks WHERE item_id = :id AND permalink LIKE :permalink;";
	// 		$query = $connection->conn->prepare($statement);
	// 		$query->execute(array(
	// 			':id'        => $id,
	// 			':permalink' => $permalink
	// 		));
	// 	}catch(Exception $e){
	// 		$errors['general'] = "<strong>Error looking for your permalink record in the system: </strong>".$e->getMessage()."<br>".$statement.'<br>';
	// 		if($current_pic !== $main_pic)
	// 		{
	// 			try{
	// 				delete_images_url_and_record($id, array(Config::get('absolute_url').Config::get('features_images').$main_pic));
	// 			}catch(Exception $e){
	// 				$errors['general'] .= $e->getMessage();
	// 			}
	// 		}
	// 		return $errors;
	// 	}
	// 	if(!$query->rowCount())
	// 	{
	// 		// This post has a new permalink, we need to insert it.
	// 		$statement = "INSERT INTO ".Config::get('table_prefix')."features_images_permalinks (id, item_id, permalink, active) VALUES (NULL, :id, :permalink, 1);";
	// 		$var_array = array(
	// 			':id' => $id,
	// 			':permalink' => $permalink
	// 		);
	// 	}
	// 	else
	// 	{
	// 		// This post is using a permalink already associated with it
	// 		$data = $query->fetch(PDO::FETCH_ASSOC);
	// 		$statement = "UPDATE ".Config::get('table_prefix')."features_images_permalinks SET active = 1 WHERE id = :id;";
	// 		$var_array = array(':id' => $data['id']);
	// 	}
		
	// 	// ===============================================================
	// 	// No matter what, need to clear 'active' state of all permalinks before update
	// 	// ===============================================================
	// 	try
	// 	{
	// 		$statement_reset = "UPDATE ".Config::get('table_prefix')."features_images_permalinks SET active = 0 WHERE item_id = :id;";
	// 		$query = $connection->conn->prepare($statement_reset);
	// 		$query->execute(array(':id' => $id));
	// 	}catch(Exception $e){
	// 		$errors['general'] = "<strong>Error setting your permalink record in the system: </strong>".$e->getMessage()."<br>".$statement_reset.'<br>';
	// 		if($current_pic !== $main_pic)
	// 		{
	// 			try{
	// 				delete_images_url_and_record($id, array(Config::get('absolute_url').Config::get('features_images').$main_pic));
	// 			}catch(Exception $e){
	// 				$errors['general'] .= $e->getMessage();
	// 			}
	// 		}
	// 		return $errors;
	// 	}
	// }
	
	// if(!empty($statement))
	// {
	// 	try
	// 	{
	// 		$query = $connection->conn->prepare($statement);
	// 		$query->execute($var_array);
	// 	}catch(Exception $e){
	// 		$errors['general'] = "<strong>Error writing permalinks: </strong>".$e->getMessage()."<br>".$statement;
	// 		if($current_pic !== $main_pic)
	// 		{
	// 			try{
	// 				delete_images_url_and_record($id, array(Config::get('absolute_url').Config::get('features_images').$main_pic));
	// 			}catch(Exception $e){
	// 				$errors['general'] .= $e->getMessage();
	// 			}
	// 		}
	// 		return $errors;
	// 	}
	// }
	
	// ===========================================
	//	XML UPDATING
	// ===========================================
	// try
	// {
	// 	if(!empty($rss))
	// 	{
	// 		// $content = nl2br($content);
	// 		// $title = stripslashes($title);
			
	// 		// $title = utf8_encode(htmlentities($title, ENT_COMPAT, 'utf-8'));

	// 		$xmlRead->addItemParameter('pubDate', 'text', $date_posted);
	// 		$xmlRead->addItemParameter('title', 'cdata', $title);
	// 		$xmlRead->addItemParameter('readmore', 'cdata', $readmore);
	// 		$xmlRead->addItemParameter('description', 'cdata', nl2br($content));
	// 		$xmlRead->addItemParameter('link', 'text', Config::get('absolute_url').'features_images/'.$permalink);
	// 		$xmlRead->add_xml($id);
			
	// 		if($new_entry)
	// 			$xmlRead->save_xml();
	// 		else
	// 			$xmlRead->change_xml($id);
	// 	}
	// 	else
	// 	{
	// 		$xmlRead->delete_xml($id);
	// 		$xmlRead->save_xml();
	// 	}
	// }catch(Exception $e){
	// 	$errors['general'] = "RSS Error:<br>".$e->getMessage();

	// 	try
	// 	{
	// 		if(!empty($main_pic))
	// 			delete_images_url_and_record($id, array(Config::get('absolute_url').Config::get('images_url').$main_pic));
	// 	}catch(Exception $e){
	// 		$errors['general'] .= $e->getMessage();
	// 	}

	// 	return $errors;
	// }
	
	// success!!
	return $errors;
}

function features_images_delete()
{
	$base_url  = "../";
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
		$statement = "SELECT ".Config::get('table_prefix')."features_images.*,
							 ".Config::get('table_prefix')."features_images_permalinks.permalink 
						FROM ".Config::get('table_prefix')."features_images
						LEFT JOIN ".Config::get('table_prefix')."features_images_permalinks ON
							".Config::get('table_prefix')."features_images.id = ".Config::get('table_prefix')."features_images_permalinks.item_id
						WHERE ".Config::get('table_prefix')."features_images.id = :id;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id' => $id
		));
	}catch(Exception $e){
		$errors['general'] = "Error finding post: ".$e->getMessage();
		return $errors;
	}
	
	if($query->rowCount())
	{
		$data = $query->fetch(PDO::FETCH_ASSOC);
		
		// store permalink for rss
		$permalink = $data['permalink'];
		
		if(is_file(Config::get('absolute_url').Config::get('features_images').$data['filename']))
		{
			if(!@unlink(Config::get('absolute_url').Config::get('features_images').$data['filename']))
			{
				$errors['general'] = "Error deleting the image for this post located at: ".Config::get('absolute_url').Config::get('features_images').$data['filename'];
				return $errors;
			}
		}
	}
	
	try
	{
		// =======================================================
		//	DELETE MAIN POST
		// =======================================================
		$statement = "DELETE FROM ".Config::get('table_prefix')."features_images WHERE id = :id;";
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
		$statement = "OPTIMIZE TABLE ".Config::get('table_prefix')."features_images;";
		$query = $connection->conn->prepare($statement);
		$query->execute();
	}catch(Exception $e){
		$errors['general'] = "Error optimizing system:<br>".$e->getMessage();
		return $errors;
	}

	try
	{
		// =======================================================
		//	DELETE PERMALINK
		// =======================================================
		$statement = "DELETE FROM ".Config::get('table_prefix')."features_images_permalinks WHERE item_id = :id;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id' => $id
		));
	}catch(Exception $e){
		$errors['general'] = "Error deleting post permalink(s):<br>".$e->getMessage();
		return $errors;
	}
	
	try
	{
		$statement = "OPTIMIZE TABLE ".Config::get('table_prefix')."features_images_permalinks;";
		$query = $connection->conn->prepare($statement);
		$query->execute();
	}catch(Exception $e){
		$errors['general'] = "Error optimizing system:<br>".$e->getMessage();
		return $errors;
	}
	
	// =======================================================
	//	DELETE XML
	// =======================================================
	// try
	// {
	// 	$xmlRead = new XMLParser(Config::get('news_rss_filename'));
	// 	$xmlRead->delete_xml($id);
	// 	$xmlRead->save_xml();
	// }catch(Exception $e){
	// 	$errors['general'] = $e->getMessage();
	// 	return $errors;
	// }
	
	// success!!
	return $errors;
}

// ===================================================================================
//	UTILITY
// ===================================================================================
function delete_images_url_and_record($id, $img_array)
{
	foreach($img_array as $img){
		if(!@unlink($img))
			throw new Exception("<br>Unable to removing the image located at ".$img);
	}

	if(!empty($id))
	{
		try
		{
			$statement = "	UPDATE ".Config::get('table_prefix')."features_images
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