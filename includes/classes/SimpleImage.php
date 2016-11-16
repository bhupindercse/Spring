<?php

/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
* 
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 2 
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
*/
 
class SimpleImage {
	
	var $image;
	var $image_type;
 
	function load($filename) {
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
		}
	}
	function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null) {
		if( $this->image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image, $filename,$compression);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			imagegif($this->image, $filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			imagepng($this->image, $filename);
		}
		if( $permissions != null) {
			chmod($filename,$permissions);
		}
	}
	function output($image_type = IMAGETYPE_JPEG) {
		if( $this->image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			imagegif($this->image);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}
	}
	function getWidth() {
		return imagesx($this->image);
	}
	function getHeight() {
		return imagesy($this->image);
	}
	function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}
	function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}
	function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100; 
		$this->resize($width,$height);
	}
	function resize($width,$height) {
		
		$new_image = imagecreatetruecolor($width, $height);

		/* Check if this image is PNG or GIF, then set if Transparent*/  
		if($this->image_type == IMAGETYPE_PNG)
		{
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
		}
		elseif($this->image_type == IMAGETYPE_GIF)
		{
			$transparent_index = imagecolortransparent($this->image);

			if ($transparent_index >= 0) {
				imagepalettecopy($this->image, $new_image);
				imagefill($new_image, 0, 0, $transparent_index);
				imagecolortransparent($new_image, $transparent_index);
				imagetruecolortopalette($new_image, true, 256);
			}
		}

		imagealphablending($new_image, false);
 		imagesavealpha($new_image, true);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
	}
	
	function cropHeight($height)
	{
		$new_image = imagecreatetruecolor($this->getWidth(), $height);

		if($this->image_type == IMAGETYPE_PNG){
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagefilledrectangle($new_image, 0, 0, $this->getWidth(), $height, $transparent);
		}
		elseif($this->image_type == IMAGETYPE_GIF)
		{
			$transparent_index = imagecolortransparent($this->image);

			if ($transparent_index >= 0) {
				imagepalettecopy($this->image, $new_image);
				imagefill($new_image, 0, 0, $transparent_index);
				imagecolortransparent($new_image, $transparent_index);
				imagetruecolortopalette($new_image, true, 256);
			}
		}

		imagealphablending($new_image, false);
 		imagesavealpha($new_image, true);
		imagecopy($new_image, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
	}

	function orientate()
	{
		try
		{
			$exif = @exif_read_data($this->filename);
		}catch(Exception $e){
			return $this->image;
		}

		if(!isset($exif['Orientation']))
			return $this->image;
		
		$orientation = $exif['Orientation'];

		// Fix Orientation
		switch($orientation) {
			case 3:
				$this->image = imagerotate($this->image, 180, 0);
				break;
			case 6:
				$this->image = imagerotate($this->image, -90, 0);
				break;
			case 8:
				$this->image = imagerotate($this->image, 90, 0);
				break;
		}

		return $this->image;
	}

	function getImageType()
	{
		return $this->image_type;
	}
}
?>