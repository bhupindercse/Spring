<?php
	include_once($base_url.'includes/init.php');
	$connection = DBConn::getInstance();
	try
	{
		$tickerImages = Content::getFeaturesInfo();
	}catch(Exception $e){
			echo "Exception while fetching Features Images ".$e->getMessage();
	}
?>


<div class="page-section" id="features">

<div>
	<div class="cycle-pager"></div>
</div>


<div class="cycle-slideshow" 
	data-cycle-fx=scrollHorz
	data-cycle-timeout=2000
	data-cycle-pager=".cycle-pager"
	data-cycle-slides="> div"
	>

<!-- <div class="cycle-slideshow" 
    data-cycle-fx=scrollHorz
    data-cycle-timeout=0
    data-cycle-slides="> div"
    data-cycle-pager="#custom-pager"
    data-cycle-pager-template="<strong><a href=#> {{slideNum}} </a></strong>"
    > -->
<!-- <div class="cycle-slideshow" 
    data-cycle-fx=scrollHorz
    data-cycle-timeout=2000
    data-cycle-caption="#alt-caption"
    data-cycle-caption-template="{{alt}}"
    > -->
		<?php 
			if(isset($tickerImages)){
				foreach($tickerImages as $res){

				echo '<div class="feature_ticker_div" type="'.$res['link_url'].'" >';
					echo '<a target="_blank" href="'.$res['link_url'].'">';
						echo '<div class="features_leftPanel" >';
							echo '<div class="padder" style="background: url('.Config::get('absolute_url').Config::get('ticker_images').$res['image_name'].') center center no-repeat" >';
								//echo '<img class="features_image_size" src="'.Config::get('absolute_url').Config::get('ticker_images').$res['image_name'].'">';
							echo '</div>';
						echo '</div>';
					echo '</a>';

					echo '<div class="features_rightPanel" >';
							echo '<h1>'.$res['title'].'</h1>';

							echo '<div class="features_description">'.$res['details'].'</div>';

							echo '<div class="features_readmore"><a href="'.$res['link_url'].'">'.$res['readmore'].'</a></div>';
						echo '</div>';     
				echo '</div>';
		
				}

			}
		?>
	</div>
<div id="custom-pager" class="center"></div>
<!-- <div id="alt-caption" class="center"></div> -->
</div>

<script src="<?php echo Config::get('absolute_url'); ?>scripts/cycle2-min.js"></script>
