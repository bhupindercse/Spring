<?php
	
	$errors = array();
	$base_url = '../../../../../';

	include($base_url."includes/init.php");

	// ===================================================================================
	//	Make sure gallery folder is created
	// ===================================================================================
	if(!isset($_REQUEST['gallery-permalink']))
	{
		$errors['error'] = 'Unable to determine gallery folder to create.';
		exit(json_encode($errors));
	}
	$gallery_permalink = $_REQUEST['gallery-permalink'];
	$upload_dir = $base_url.Config::get('gallery_files').$gallery_permalink;

	// ===================================================================================
	//	Make sure all info is present and set
	// ===================================================================================
	if(!isset($_REQUEST['name']))
	{
		$errors['error'] = 'Name required';
		exit(json_encode($errors));
	}

	// ===================================================================================
	//	Determine which blob data to use
	// ===================================================================================
	if(!isset($_REQUEST['current_index']))
	{
		$errors['error'] = 'Unable to determine current file index being uploaded';
		exit(json_encode($errors));
	}
	$currentFileIndex = $_REQUEST['current_index'];

	$_SESSION['xhr-uploader'][$currentFileIndex]['caption'] = "";
	if(isset($_REQUEST['summary']))
		$_SESSION['xhr-uploader'][$currentFileIndex]['caption'] = $_REQUEST['summary'];

	// ============================================================
	// 	If the directory does not exist
	// ============================================================
	if(!is_dir($upload_dir))
	{
		if(!@mkdir($upload_dir))
		{
			$errors['error'] = 'Unable to create gallery dirctory.';
			exit(json_encode($errors));
		}
	}

	// ==========================================================
	// 	Clean file name
	// ==========================================================
	$filename  = $_REQUEST['name'];
	$pieces    = explode(".", $filename);
	$extension = ".".end($pieces);
	$new_name  = substr($filename, 0, strrpos($filename, "."));
	$permalink = Permalinks::createPermalink($new_name);

	// ===================================
	//	Make sure filename does not exist
	// ===================================
	try
	{
		$permalink = Utility::determineName($permalink, 0, $extension, Config::get('gallery_files').$gallery_permalink."/");
	}catch(Exception $e){
		$errors['error'] = $e->getMessage();
		exit(json_encode($errors));
	}

	$thumb    = $permalink.'-thumb'.$extension;
	$new_name = $permalink.$extension;

	$filename = $upload_dir.$new_name;
	$full_url = Config::get('absolute_url').Config::get('gallery_files').$gallery_permalink.$new_name;
	$full_url_thumb = Config::get('absolute_url').Config::get('gallery_files').$gallery_permalink.$thumb;

	// ==========================================================
	// 	Make sure file is acceptable type
	// ==========================================================
	if(!array_key_exists(strtolower($extension), Config::get('gallery_file_types')))
	{
		$errors['error'] = "Error: Cannot use a file of that type for a gallery image.  You are uploading a file with the following extension: ".$extension;
		exit(json_encode($errors));
	}

	$_SESSION['xhr-uploader'][$currentFileIndex]['type'] = Config::get('gallery_file_types/'.strtolower($extension));

	// ==========================================================
	// 	If file exists, return error notice
	// ==========================================================
	if(file_exists($filename))
	{
		$errors['error'] = 'Error: There is already a file with that name location at <a href="'.$full_url.'" target="_blank">'.$full_url.'</a>.';
		exit(json_encode($errors));
	}

	// ===================================================================================
	//	Give file a time that it has until it'll be cleaned up by the next upload
	// ===================================================================================
	$_SESSION['xhr-uploader'][$currentFileIndex]['gallery-dir'] = $gallery_permalink;
	$_SESSION['xhr-uploader'][$currentFileIndex]['filename']    = $new_name;
	$_SESSION['xhr-uploader'][$currentFileIndex]['thumb']       = $thumb;

	exit(json_encode($errors));

?>