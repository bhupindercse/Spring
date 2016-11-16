<?php

	$return_msg = array();
	$base_url   = '../../../../../';

	include($base_url."includes/init.php");

	// ===================================================================================
	//	Determine which blob data to use
	// ===================================================================================
	if(!isset($_REQUEST['current_index']))
	{
		$errors['error'] = 'Unable to determine current file index being uploaded';
		exit(json_encode($errors));
	}
	$currentFileIndex = $_REQUEST['current_index'];

	$base_dir = $base_url.$_SESSION['xhr-uploader'][$currentFileIndex]['base-dir'];

	// ===================================
	//	Populate variables
	// ===================================
	$gallery_id = $_POST['gallery_id'];
	$caption    = nl2br($_SESSION['xhr-uploader'][$currentFileIndex]['caption']);
	$filename   = $_SESSION['xhr-uploader'][$currentFileIndex]['filename'];
	$thumb      = $_SESSION['xhr-uploader'][$currentFileIndex]['thumb'];

	// ===================================
	//	Format DB vars
	// ===================================
	$date_added = date("Y-m-d H:i:s");

	// ==========================================================
	//  Add to database
	// ==========================================================
	$connection = DBConn::getInstance();
	try
	{
		$statement = "	INSERT INTO ".Config::get('table_prefix')."gallery_images
						(`gallery_id`, `date_added`, `filename`, `thumb`, `caption`)
						VALUES
						(:gallery_id, :date_added, :filename, :thumb, :caption);";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':gallery_id' => $gallery_id,
			':date_added' => $date_added,
			':filename'   => $filename,
			':thumb'      => $thumb,
			':caption'    => $caption
		));
	}catch(PDOException $e){
		// Try to remove the file just uploaded
		$return_msg['error'] = returnErrorDeleteFile('Error adding the file to our database:<br>'.$e->getMessage(), $base_dir.$_SESSION['xhr-uploader'][$currentFileIndex]['filename'], $base_dir.$_SESSION['xhr-uploader'][$currentFileIndex]['thumb']);
		exit(json_encode($return_msg));
	}

	// $return_msg['error'] = returnErrorDeleteFile("MEH", $base_dir.$_SESSION['xhr-uploader']['base-dir']);
	// exit(json_encode($return_msg));

	// ===================================
	//	Unset xhr session data
	// ===================================
	unset($_SESSION['xhr-uploader'][$currentFileIndex]);
	
	// ==================================================
	//	Set up returned data
	// ==================================================
	// $return_array['pubdate']  = date("Y/m/d g:i a", strtotime($pubdate));
	// $return_array['filename'] = $globals->absolute_url.$globals->gallery_images.$gallery_id.'/'.$new_name;
	// $return_array['id']       = $connection->conn->lastInsertId();
	// $return_array['num']      = $num;

	// $return_msg['mobile-debugger'] = $new_name;
	// $return_msg['success']         = $return_array;
	exit(json_encode($return_msg));

	function returnErrorDeleteFile($error, $file, $thumb)
	{
		$msg = $error;
		if(!@unlink($file))
			$msg .= "<br>Unable to delete the file uploaded.";
		if(!@unlink($thumb))
			$msg .= "<br>Unable to delete the thumbnail uploaded.";

		return $msg;
	}

?>