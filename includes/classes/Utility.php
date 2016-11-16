<?php

class Utility
{
	public static function check_file_error($error_code)
	{
		switch ($error_code)
		{
			case UPLOAD_ERR_OK:
				return 'The file uploaded ok.';
			case UPLOAD_ERR_INI_SIZE:
				return 'The uploaded file exceeds the server\'s allowed file upload size.';
			case UPLOAD_ERR_FORM_SIZE:
				return 'The uploaded file exceeds the webpage\'s max file size.';
			case UPLOAD_ERR_PARTIAL:
				return 'The uploaded file was only partially uploaded.';
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded.';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Missing a temporary folder.';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write file to disk.';
			case UPLOAD_ERR_EXTENSION:
				return 'File upload stopped by extension.';
			default:
				return 'Unknown upload error.';
		}
	}

	public static function determineName($origName, $num, $extension, $directory, $max = false)
	{
		// If maximum tries have been reached
		if($max && $max > $num)
			throw new Exception("Unable to rename the file: ".$newName.$extension);

		$newName = $num > 1 ? $origName."-".$num : $origName;

		// If file exists, figure out next number
		if(file_exists(__DIR__.'/../../'.$directory.$newName.$extension))
		{
			$num++;
			$newName = self::determineName($origName, $num, $extension, $directory);
		}

		return $newName;
	}

	public static function generatePassword($length = 10){
		
		$alpha       = "abcdefghijklmnopqrstuvwxyz";
		$alpha_upper = strtoupper($alpha);
		$numeric     = "0123456789";
		$special     = "!@$#*%";

		// Concatinate all variables into one long string
		$chars = $alpha . $alpha_upper . $numeric . $special;

		// Suffle the value of $chars
		$chars = str_shuffle($chars);

		// Return the length of your new $chars string
		$len = strlen($chars);

		// Create empty variable that will hold your new password
		$pw = '';

		// A simple 'for' statement that will select random characters for the lenth of your password
		for ($i = 0; $i < $length; $i++)
			$pw .= substr($chars, rand(0, $len-1), 1); 

		// Shuffle everything around cause we can
		$pw = str_shuffle($pw);

		// show the password on screen
		echo $pw;
	}

	public static function formatDatepickerDate($date){
		return date("m/d/Y", strtotime($date));
	}

	public static function checkExtension($picName, $type)
	{
		// Check the file extension
		$ext = array();
		
		switch ($type)
		{
			case "pdf":
				$ext[] = '.pdf';
				$ext[] = '.PDF';
				break;
			case "img";
				$ext[] = '.gif';
				$ext[] = '.GIF';
				$ext[] = '.jpg';
				$ext[] = '.JPG';
				$ext[] = '.png';
				$ext[] = '.PNG';
				$ext[] = '.jpeg';
				$ext[] = '.JPEG';
				break;
			case "file";
				$ext[] = '.pdf';
				$ext[] = '.PDF';
				$ext[] = '.doc';
				$ext[] = '.DOC';
				$ext[] = '.docx';
				$ext[] = '.DOCX';
				break;
			default:
				return "Unable to determine the file type supposed ton check against.";
		}

		$flag = true;
		
		$photo_ext = strrchr($picName, ".");

		// search the array for the file extension
		foreach($ext as $value)
		{
			if($value == $photo_ext)
			{
				$flag = false;
				break;
			}
		}
		
		// if not found....
		if($flag)
			return false;
		
		return true;
	}

	// public static function checkIfNameExistsAndIncrement($permalink, $extension, $upload_dir, $file_suffix = 0, $currentTry = 0){
		
	// 	$maxTries = 5;
	// 	$currentTry++;

	// 	if($currentTry > $maxTries)
	// 		throw new Exception("Unable to set a filename within ".$maxTries." attempts.");

	// 	// New file name
	// 	$new_name = $file_suffix ? $filename."-".$file_suffix : $filename;

	// 	if(file_exists($dir.$new_name.$extension)){
	// 		// sleep(1);
	// 		$file_suffix++;
	// 		$new_name = self::checkIfNameExistsAndChange($permalink, $extension, $upload_dir, $file_suffix, $currentTry);
	// 	}

	// 	return $new_name;
	// }
}

?>