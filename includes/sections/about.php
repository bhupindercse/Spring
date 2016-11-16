<?php
	include_once($base_url.'includes/init.php');
	$connection = DBConn::getInstance();
	try
	{
		$allAbout = Content::getAllAbout();
	}catch(Exception $e){
			echo "Exception while fetching About Content".$e->getMessage();
	}
?>

<div  class="page-section" id="about" >
	<div class="content">
		<div class="details_header" >
			<h1> HOW TO ACHIEVE MORE </h1>	
			<div class="header_description">
				Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vitae est at nibh porta pulvinar hendrerit mollis tellus. Aliquam vestibulum quam vulputate rhoncus elementum. Aliquam erat volutpat. Ut vel dui quis nulla semper vulputate. Nam pharetra tristique ante, ut sollicitudin nulla euismod ut. 
			</div>
		</div>	
		
		<div class="details_category_div" >
			<div class="details_category_inner_div"  >

				<?php
					if(isset($allAbout)){
						$counter = 0;
						foreach($allAbout  as $about){
							if($counter <3 ){
								echo '<div class="details_category">';
									echo '<div><img  src="'.Config::get('absolute_url').'images/'.$about['image'].'"/></div>';
									echo '<div class="about_title">'.strtoupper($about['title']).'</div>';
									echo '<div class="category_description">'.$about['description'].'</div>';
								echo '</div>';
							}
							$counter++;
						}
					}
				?>
			</div>


			<div class="details_category_inner_div"  >
				<?php
					if(isset($allAbout)){
						$counter = 0;
						foreach($allAbout  as $about){
							if($counter >2 ){
								echo '<div class="details_category">';
									echo '<div><img  src="'.Config::get('absolute_url').'images/'.$about['image'].'"/></div>';
									echo '<div class="about_title">'.strtoupper($about['title']).'</div>';
									echo '<div class="category_description">'.$about['description'].'</div>';
								echo '</div>';
							}
							$counter++;
						}
					}
				?>
			</div>

		<!-- 
			<div class="details_category_inner_div"  >
				<div class="details_category">
					<div><img  src="<?php echo Config::get('absolute_url')?>images/1icon.png"/></div>
					<div class="about_title">MANAGE EXPENSES</div>
					<div class="category_description">	
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam id porta orci, ac aliquam augue. </div>
				</div>
				<div class="details_category">
					<div><img  src="<?php echo Config::get('absolute_url')?>images/2icon.png"/></div>
					<div class="about_title">STAY ORGANIZED</div>
					<div class="category_description">	
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam id porta orci, ac aliquam augue. </div>
				</div>
				<div class="details_category">
					<div><img  src="<?php echo Config::get('absolute_url')?>images/3icon.png"/></div>
					<div class="about_title">COMMUNICATION</div>
					<div class="category_description">	
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam id porta orci, ac aliquam augue. </div>
				</div>
			</div>


			<div class="details_category_inner_div"  >
				<div class="details_category">
					<div><img  src="<?php echo Config::get('absolute_url')?>images/4icon.png"/></div>
					<div class="about_title">ASSIGN JOBS</div>
				<div class="category_description">	
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam id porta orci, ac aliquam augue. </div>
				</div>
				<div class="details_category">
					<div><img  src="<?php echo Config::get('absolute_url')?>images/5icon.png"/></div>
					<div class="about_title">TO DO'S</div>
					<div class="category_description">	
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam id porta orci, ac aliquam augue. </div>
				</div>
				<div class="details_category">
					<div><img  src="<?php echo Config::get('absolute_url')?>images/6icon.png"/></div>
					<div class="about_title">TEAM WORK</div>
					<div class="category_description">	
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam id porta orci, ac aliquam augue. </div>
				</div>
			</div>
 -->
		</div>	
	</div>
</div>

