<?php

// =======================================================================================================================================================
//		NEWS FUNCTIONS
// =======================================================================================================================================================
function about_add()
{
	//Config::get('images_url')   = "../";

	$base_url   = "../";
	$connection = DBConn::getInstance();
	Input::exists();
		
	$id          = Input::get('id');
	$title       = trim(Input::get('title'));
	$content     = trim(Input::get('content'));
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
		$errors['title'] = "Please enter features_images title." ;
		
	if(empty($content))
		$errors['content'] = "Please enter some content for this posting.";
				
	
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

			$statement = "SELECT * FROM ".Config::get('table_prefix')."about WHERE id = :id;";
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
		if(is_file($base_url.'images/'.$current_record['image']))
		{
			if(!@unlink($base_url.'images/'.$current_record['image']))
			{
				$errors['img'] = "Unable to delete the image located at ".$base_url.'images/'.$current_record['image'];
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
		if(!is_dir($base_url.'images/') ){
			
			mkdir($base_url.'images/');
		}

		// Make sure folder exists, after creating
		if(!is_dir($base_url.'images/') ){
			$errors['img'] = $base_url.'images/'." does not exist.";
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
			$img = Utility::determineName($img, 1, $ext, 'images/');
		}catch(Exception $e){
			$errors['img'] = "Error determining main image filename:<br>".$e->getMessage();
			return $errors;
		}

		$main_pic = $img.$ext;
		$target_path = $base_url.'images/'.$main_pic;

		// $errors['img'] = $main_pic;
		// return $errors;

		// Get rid of the old image
		try
		{
			if(!empty($id) && $current_record_query->rowCount())
			{
				if(is_file($base_url.'images/'.$current_record['image']))
				{
					if(!@unlink($base_url.'images/'.$current_record['image']))
					{
						$errors['img'] = "Unable to removing the image located at ".$base_url.'images/'.$current_record['image'];
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
			$filename = $base_url.'images/'.$current_record['image'];
			
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
			$main_pic = $current_record['image'];
	}
	
	if(empty($id))
		$id = NULL;

	try
	{
		// ===========================================
		//	INSERT news INTO DB
		// ===========================================
		$statement = "	INSERT INTO ".Config::get('table_prefix')."about
						(id, 
						title, 
						description, 
						image)
						VALUES 
						(:id, 	
						:title, 		
						:description, 
						:image)
						ON DUPLICATE KEY UPDATE
						title           = :title_update,
						description     = :description_update,
						image           = :image_update;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id'                  => $id,
			':title'               => $title,
			':description'         => $content,
			':image'               => $main_pic,
			':title_update'        => $title,
			':description_update'  => $content,
			':image_update'        => $main_pic
		));
	}catch(Exception $e){
		$errors['general'] = "Error updating database: ".$e->getMessage();
		if(!empty($main_pic))
		{
			if(!@unlink(Config::get('absolute_url').'images/'.$main_pic))
				$errors['general'] .= "<br>Also, unable to delete the image you have uploaded to ".$main_pic;
		}
		return $errors;
	}
	
	// success!!
	return $errors;
}


function about_delete()
{
	$base_url   = "../";
	$connection = DBConn::getInstance();
	Input::exists();
	$errors = array();
	
	if(Input::hasValue('id'))
		$id = Input::get('id');
	else
	{
		$errors['general'] = "Error: Unable to determine the review you are trying to delete.";
		return $errors;
	}
	
		try
		{
			// =======================================================
			//	DELETE MAIN POST
			// =======================================================
			$statement = "DELETE FROM ".Config::get('table_prefix')."about WHERE id = :id;";
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
			$statement = "OPTIMIZE TABLE ".Config::get('table_prefix')."about;";
			$query = $connection->conn->prepare($statement);
			$query->execute();
		}catch(Exception $e){
			$errors['general'] = "Error optimizing system:<br>".$e->getMessage();
			return $errors;
		}
		
	// success!!
	return $errors;
}
?>