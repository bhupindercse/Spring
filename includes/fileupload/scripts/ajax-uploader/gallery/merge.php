<?php
	
	$errors = array();
	$base_url = '../../../../../';

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
	
	$temp_dir   = $base_url.Config::get('gallery_file_chunks');
	$upload_dir = $base_url.Config::get('gallery_files').$_SESSION['xhr-uploader'][$currentFileIndex]['gallery-dir'].'/';

	// ===================================================================================
	//	Make sure all variables are set and present
	// ===================================================================================
	if(!isset($_SESSION['xhr-uploader'][$currentFileIndex]['filename']))
	{
		$errors['error'] = 'Name required in session.';
		exit(json_encode($errors));
	}
	if(!isset($_REQUEST['index']))
	{
		$errors['error'] = 'Index error.';
		exit(json_encode($errors));
	}

	// ===================================
	//	Set up target/destination
	// ===================================
	$target = $temp_dir.$_SESSION['xhr-uploader'][$currentFileIndex]['filename'];
	$dst    = fopen($upload_dir.$_SESSION['xhr-uploader'][$currentFileIndex]['filename'], 'wb');

	// Store location of uploaded file for later use if needed before Global-Settings are included
	$_SESSION['xhr-uploader'][$currentFileIndex]['base-dir'] = Config::get('gallery_files').$_SESSION['xhr-uploader'][$currentFileIndex]['gallery-dir'].'/'.$_SESSION['xhr-uploader'][$currentFileIndex]['filename'];

	for($i = 0; $i < $_REQUEST['index']; $i++)
	{
		$slice = $target . '-' . $i;
		$src = fopen($slice, 'rb');
		stream_copy_to_stream($src, $dst);
		fclose($src);
		@unlink($slice);
	}

	fclose($dst);

	// ==========================================================
	// 	resize if needed
	// ==========================================================
	$img_location = $upload_dir.$_SESSION['xhr-uploader'][$currentFileIndex]['filename'];
	list($width, $height, $type, $attr) = getimagesize($img_location);
	if($width > Config::get('gallery_img_width'))
	{
		// resize image
		$image = new SimpleImage();
		$image->load($img_location);
		$image->resizeToWidth(Config::get('gallery_img_width'));
		$image->save($img_location);
	}

	// ===================================================================================
	//	Create & size thumbnail
	// ===================================================================================
	copy($img_location, $upload_dir.$_SESSION['xhr-uploader'][$currentFileIndex]['thumb']);
	$thumb_location = $upload_dir.$_SESSION['xhr-uploader'][$currentFileIndex]['thumb'];
	
	// Width
	list($width, $height, $type, $attr) = getimagesize($thumb_location);
	// $errors['error'] = $height;
	if($width !== Config::get('gallery_thumb_width'))
	{
		$image = new SimpleImage();
		$image->load($thumb_location);
		$image->resizeToWidth(Config::get('gallery_thumb_width'));
		$image->save($thumb_location);
	}

	// list($width, $height, $type, $attr) = getimagesize($thumb_location);
	// $errors['error'] .= $width." => ".$height;
	// exit(json_encode($errors));

	// Height
	list($width, $height, $type, $attr) = getimagesize($thumb_location);
	if($height > Config::get('gallery_thumb_height'))
	{
		$image = new SimpleImage();
		$image->load($thumb_location);
		$image->cropHeight(Config::get('gallery_thumb_height'));
		$image->save($thumb_location);
	}

	// Store file size
	$_SESSION['xhr-uploader'][$currentFileIndex]['size'] = filesize($upload_dir.$_SESSION['xhr-uploader'][$currentFileIndex]['filename']);

	exit(json_encode($errors));

?>