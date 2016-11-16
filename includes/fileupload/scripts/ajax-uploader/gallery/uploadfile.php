<?php

	$errors = array();
	$base_url = '../../../../../';

	include($base_url."includes/init.php");

	$upload_dir = $base_url.Config::get('gallery_file_chunks');

	// ===================================================================================
	//	Determine which blob data to use
	// ===================================================================================
	if(!isset($_REQUEST['current_index']))
	{
		$errors['error'] = 'Unable to determine current file index being uploaded';
		exit(json_encode($errors));
	}
	$currentFileIndex = $_REQUEST['current_index'];

	// ===================================================================================
	//	Make sure all info is present and set
	// ===================================================================================
	if(!isset($_SESSION['xhr-uploader'][$currentFileIndex]['filename']))
	{
		$errors['error'] = 'Name required.';
		exit(json_encode($errors));
	}
	if(!isset($_REQUEST['index']))
	{
		$errors['error'] = 'Index error.';
		exit(json_encode($errors));
	}
	if(!isset($_FILES['file']))
	{
		$errors['error'] = 'File not uploaded.';
		exit(json_encode($errors));
	}

	// ==========================================================
	//	Clean up temp folder
	//  Loop through all files.  If file's date is 
	// ==========================================================
	if(!$_REQUEST['index'])
	{
		$time_limit = strtotime("-1 hour");
		$checktime  = date("d-m-Y H:i:s", $time_limit);
		$dir        = new DirectoryIterator($base_url.Config::get('gallery_file_chunks'));
		
		foreach($dir as $fileinfo)
		{
			if(!$fileinfo->isDot())
			{
				if(date("d-m-Y H:i:s", $fileinfo->getCTime()) < $checktime)
				{
					if(!@unlink($base_url.Config::get('gallery_file_chunks').$fileinfo->getFilename()))
					{
						$errors['error'] = "Error deleting old chunk: ".$base_url.Config::get('gallery_file_chunks')->$fileinfo->getFilename();
						exit(json_encode($errors));
					}
				}
			}
		}
	}

	$target = $upload_dir.$_SESSION['xhr-uploader'][$currentFileIndex]['filename'].'-'.$_REQUEST['index'];
	move_uploaded_file($_FILES['file']['tmp_name'], $target);

	// Might execute too quickly.
	sleep(1);

	exit(json_encode($errors));

?>