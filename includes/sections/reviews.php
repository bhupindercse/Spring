<?php
	include_once($base_url.'includes/init.php');
	$connection = DBConn::getInstance();
	try
	{
		$reviews = Content::getReviewsInfo();
		
		$counter = 1;
		$no_border = "";
	}catch(Exception $e){
			echo "Exception while fetching Features Images ".$e->getMessage();
	}
?>

<div class="page-section" id="review" >
	
	<div class="review_header" >
		<h1> MAIN SPRING SUCCESS STORIES </h1>
	</div>	

	<div class="review_content" >
		<div class="inner_review_content">


			<?php 
				if(isset($reviews)){
					$reviews_length = count($reviews);
					foreach($reviews as $review){
						if($counter == $reviews_length){
							$no_border = "no_border";
						}
						if($counter == 1){
							$no_border_top = "no_border";
						}else{
							$no_border_top = "";
						}
							echo '<div class="review_box '.$no_border.'" >';
								echo '<div class="review_description '.$no_border_top.'">'.$review['content'].'</div>';
								echo '<h3>'.$review['reviewer'].'</h3>';
								echo '<div class="signature">'.$review['designation'].'</div>';
							echo '</div>';

							$counter++;
					}
				}
			?>

		<!-- 	<div class="review_box" >
				<div class="review_description">
					"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vitae est at nibh porta pulvinar hendrerit mollis tellus. Aliquam vestibulum quam vulputate rhoncus elementum. Aliquam erat volutpat. Ut vel dui quis nulla semper vulputate. Nam pharetra tristique ante, ut sollicitudin nulla euismod ut." 
				</div>
				<h3>JOHN DOE</h3>
				<div class="signature">MAIN SPRING, OWNER</div>
			</div>
			
			<div class="review_box" >
				<div class="review_description">
					"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vitae est at nibh porta pulvinar hendrerit mollis tellus. Aliquam vestibulum quam vulputate rhoncus elementum. Aliquam erat volutpat. Ut vel dui quis nulla semper vulputate. Nam pharetra tristique ante, ut sollicitudin nulla euismod ut." 
				</div>
				<h3>JOHN DOE</h3>
				<div class="signature">MAIN SPRING, OWNER</div>
			</div>

			<div class="review_box no_border" >
				<div class="review_description">
					"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vitae est at nibh porta pulvinar hendrerit mollis tellus. Aliquam vestibulum quam vulputate rhoncus elementum. Aliquam erat volutpat. Ut vel dui quis nulla semper vulputate. Nam pharetra tristique ante, ut sollicitudin nulla euismod ut." 
				</div>
				<h3>JOHN DOE</h3>
				<div class="signature">MAIN SPRING, OWNER</div>
			</div> -->
		</div>
	</div>
</div>