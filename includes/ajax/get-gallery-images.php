<?php

	include('../init.php');

	echo json_encode(return_content());

	function return_content(){

		$return_msg = array();

		try
		{
			$msg = "";
			$imgList = array();

			Input::exists();
			$gallery_permalink = Input::get('permalink');
			$gallery           = Gallery::getGallery($gallery_permalink);
			$imgList[]         = Gallery::getThumbs();
			
			// Show thumbnails
			foreach($imgList as $galleryImageList){
				foreach($galleryImageList as $thumb){
					$msg .= '<div class="gallery-thumbnail" data-src="'.$thumb['filename'].'" data-thumb="'.$thumb['thumb'].'" data-gallery="'.$gallery_permalink.'" data-title="'.str_replace('\n', '', htmlspecialchars($thumb['caption'])).'">';
					$msg .= 	'<img class="loader" src="'.Config::get('absolute_url').'images/ajax-16x16.gif" alt="'.$thumb['thumb'].'">';
					$msg .= '</div>';
				}
			}

			$return_msg['html'] = $msg;
			return $return_msg;

		}catch(Exception $e){
			$return_msg['error'] = '<div class="error">'.$e->getMessage().'</div>';
			return $return_msg;
		}
	}

?>