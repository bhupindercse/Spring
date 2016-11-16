<div class="page-section" id="gallery">
	<div class="content-wrapper">
		
		<h2>What We’ve Done</h2>
		<h3 class="blue-txt">Photo Gallery</h3>

		<p>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed augue enim, sodales ac massa id, condimentum blandit mi. Fusce vehicula ipsum ante, a dignissim sapien volutpat eget. Ut venenatis massa felis, sed ultrices velit lobortis quis. 
		</p>

		<?php
			
			try
			{
				$galleryList       = Gallery::getGalleryList();
				$initialGallery    = null;
				$gallery_permalink = "";

				if(!count($galleryList))
					echo '<h3>Please come back later to view our galleries!</h3>';
				else
				{
					$imgList = array();

					echo '<input type="hidden" name="gallery-existance" value="'.count($galleryList).'">';

					// Show gallery nav
					echo '<div class="gallery-nav">';
					foreach($galleryList as $btn){

						echo '<div class="transparent-link gallery-btn';
						if(is_null($initialGallery)) echo ' active';
						echo '" data-permalink="'.$btn['permalink'].'">'.$btn['title'].'</div>';

						if(is_null($initialGallery)){
							$initialGallery    = Gallery::getGallery($btn['permalink']);
							$imgList[]         = Gallery::getThumbs();
							$gallery_permalink = $btn['permalink'];
						}
					}
					echo '</div>';

					// Show thumbnails
					echo '<div class="gallery-thumbs">';
					foreach($imgList as $galleryImageList){
						foreach($galleryImageList as $thumb){
							echo '<div class="gallery-thumbnail" data-src="'.$thumb['filename'].'" data-thumb="'.$thumb['thumb'].'" data-gallery="'.$gallery_permalink.'" data-title="'.str_replace('\n', '', htmlspecialchars($thumb['caption'])).'">';
							echo 	'<img class="loader" src="'.Config::get('absolute_url').'images/ajax-16x16.gif" alt="'.$thumb['thumb'].'">';
							echo '</div>';
						}
					}
					echo '</div>';
				}
			}catch(Exception $e){
				echo '<div class="error">'.$e->getMessage().'</div>';
			}

		?>
	</div>
</div>