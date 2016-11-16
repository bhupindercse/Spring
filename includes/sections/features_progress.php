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

<div class="progressbar_outer_div">
	<div class="progressbar" id="progress1">
	    <span class="progress-text1"></span>
	</div>
	<span id="progressPager1" class="progressPager" >1</span>
	
	<div class="progressbar" id="progress2">
	    <span class="progress-text1"></span>
	</div>
	<span  id="progressPager2" class="progressPager" >2</span>
	
	<div class="progressbar" id="progress3">
	    <span class="progress-text1"></span>
	</div>
	<span  id="progressPager3" class="progressPager" >3</span>
</div>

<div class="page-section" id="features">
<div style="padding-bottom: 19em;">
		<?php 
			if(isset($tickerImages)){
				foreach($tickerImages as $res){
					echo '<div class="feature_ticker_div" type="'.$res['link_url'].'" >';
					
						echo '<a target="_blank" href="'.$res['link_url'].'">';
							echo '<div class="features_leftPanel" >';
								echo '<div class="padder" style="background: url('.Config::get('absolute_url').Config::get('features_images').$res['filename'].') center center no-repeat" >';
								echo '</div>';
							echo '</div>';
						echo '</a>';

						echo '<div class="features_rightPanel" >';
							echo '<h1>'.$res['title'].'</h1>';
							echo '<div class="features_description">'.$res['content'].'</div>';
							echo '<div class="features_readmore"><a href="'.$res['link_url'].'" target="_blank" >'.$res['readmore'].'</a></div>';
						echo '</div>';     
					echo '</div>';
				}
			}
		?>
	</div>
</div>
<script src="<?php echo Config::get('absolute_url'); ?>scripts/cycle2-min.js"></script>