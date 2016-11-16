<?php
	
	$base_url   = "";
	$page_title = "News";

	include_once($base_url.'includes/init.php');

	Input::exists();
	$permalink = Input::get('permalink', 'get');
	Input::set('page', 'news', 'get');

	$connection = DBConn::getInstance();

	$found    = false;
	$content  = "";
	$rel_url  = "../";

	// ===================================================================================
	//	SPECIFIC POST
	// ===================================================================================
	if(!empty($permalink)){
		try
		{
			$newsPost = News::getPostByPermalink($permalink);
				
			$permalink    = $newsPost['permalink'];
			$title        = $newsPost['title'];
			$post_content = $newsPost['content'];
			$img          = $newsPost['filename'];
			$full_img_url = Config::get('absolute_url').Config::get('news_images').rawurlencode($newsPost['filename']);
			
			$post_desc    = strip_tags($post_content);

			$post_url = Config::get('absolute_url').Config::get('news_url').$newsPost['permalink'];

			$content .= '<div class="news-item-wrapper">';

			$content .= '<div class="news-item active'; $content .= '" data-id="'.$newsPost['permalink'].'">';
			$content .= 	'<div class="news-copy-section'; if(!empty($newsPost['filename'])) $content .= ' with-image'; $content .= '">';
			$content .=  		'<div class="padder">';
			$content .= 			'<h2>What\'s in the News</h2>';
			$content .= 			'<a href="'.$post_url.'" class="news-title red-txt">'.$newsPost['title'].'</a>';
			$content .= 			'<div class="news-date page-date"><span class="icon-clock"></span>'.date("d F, Y", strtotime($newsPost['date_posted'])).'</div>';
			$content .= 			'<div class="news-content">';
			$content .= 				$newsPost['content'];
			$content .= 				'<div
											class="fb-like"
											data-share="true"
											data-width="450"
											data-show-faces="true">
										</div>';
			$content .= 			'</div>';
			$content .= 		'</div>';
			$content .= 	'</div>';

			if(!empty($newsPost['filename'])){
				$content .= '<div class="news-image-section">';
				$content .= 	'<img src="'.$full_img_url.'" alt="'.$newsPost['title'].'">';
				$content .= '</div>';
			}

			$content .= '</div>';
			$content .= '</div>';

			$navItems   = News::getLatest(3);
			$news_nav   = "";
			$news_count = 0;
			foreach($navItems as $item){
				$news_count++;
				$news_url = Config::get('absolute_url').Config::get('news_url').$item['permalink'];
				$news_nav .= '<a class="news-link'; if($item['permalink'] == $permalink) $news_nav .= ' active'; $news_nav .= '" href="'.$news_url.'" data-id="'.$item['permalink'].'"><div class="story-name">'.$item['title'].'</div><div class="story-count">'.$news_count.'</div></a>';
			}

			$content .= '<div class="news-nav">';
			$content .= 	$news_nav;
			$content .= 	'<a class="all-link" href="'.Config::get('absolute_url').Config::get('news_url').'">See All</a>';
			$content .= '</div>';

			$found = true;

		}catch(Exception $e){
			$content .= '<div class="news-copy-section">'.$e->getMessage().'</div>';
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo Config::get('site_title'); if(!empty($page_title)) echo ' - '.$page_title; ?></title>

<?php include($base_url.'includes/global-includes.php'); ?>

<?php
	if($found){

		echo '<meta property="og:site_name" content="'.Config::get('site_title').'" />';
		
		if(isset($title))
			echo '<meta property="og:title" content="'.$title.'" />';
		
		// if(isset($fb_site_type))
		// 	echo '<meta property="og:type" content="'.$fb_site_type.'" />';
		
		if(isset($post_url))
			echo '<meta property="og:url" content="'.$post_url.'" />';
		
		if(isset($img) && !empty($img))
			echo '<meta property="og:image" content="'.$full_img_url.'" />';
		
		if(isset($post_desc))
			echo '<meta property="og:description" content="'.$post_desc.'" />';
	}
?>

</head>

<body class="single-page">

	<script src="<?php echo Config::get('absolute_url'); ?>scripts/fb-sdk.js"></script>
	
	<?php include($base_url.'includes/sections/header.php'); ?>
	
	<div class="row-expand">
		<div class="content-wrapper">
			<div class="page-section<?php if(!$found) echo ' section-scroller-wrapper'; ?>">
				<?php
					if($found)
						echo $content;
					else
					{
						echo '<div class="section-scroller"></div>';
						echo '<div class="scroll-more-btn"><span>More Stories</span></div>';
						echo '<div class="scroll-loading"><span>Loading</span></div>';
					}
				?>
			</div>
		</div>
	</div>
	
	<?php include($base_url.'includes/sections/footer.php'); ?>

	<?php include($base_url.'includes/script-includes.php'); ?>
	<script src="<?php echo Config::get('absolute_url'); ?>scripts/NavSinglePage-min.js"></script>

	<?php if(!$found){ ?>
	<input class="hidden_area" id="rel_url" value="<?php echo $rel_url; ?>">
	<script src="<?php echo Config::get('absolute_url'); ?>scripts/NewsScroller-min.js"></script>
	<?php } ?>
	
</body>
</html>