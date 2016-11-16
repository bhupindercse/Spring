<div class="page-section" id="news">
	<div class="content-wrapper">
		<?php

			try
			{
				$newsData = News::getLatest(3);

				$first    = true;
				$news_nav = false;

				$news_count = 1;

				foreach($newsData as $data){

					if($first)
						echo '<div class="news-item-wrapper">';

					$news_url = Config::get('absolute_url').Config::get('news_url').$data['permalink'];

					echo '<div class="news-item'; if($first) echo ' active'; echo '" data-id="'.$data['permalink'].'">';
					echo 	'<div class="news-copy-section'; if(!empty($data['filename'])) echo ' with-image'; echo '">';
					echo 		'<div class="padder">';
					//echo 			'<h2>What\'s in the News</h2>';
					echo 			'<a href="'.$news_url.'" class="news-title">'.$data['title'].'</a>';
					echo 			'<div class="news_subtitle">'.$data['subtitle'].'</div>';
					echo 			'<div class="news-date">'.date("m/d/Y", strtotime($data['date_posted'])).'</div>';
					echo 			'<div class="news-content">'.$data['content'].'</div>';
					echo 		'</div>';
					echo 	'</div>';

					if(!empty($data['filename'])){
						echo '<div class="news-image-section" style="background-image:url('.Config::get('absolute_url').Config::get('news_images').rawurlencode($data['filename']).'"></div>';
					}

					echo '</div>';

					$news_nav .= '<a class="news-link'; if($first) $news_nav .= ' active'; $news_nav .= '" href="'.$news_url.'" data-id="'.$data['permalink'].'"><div class="story-name">'.$data['title'].'</div><div class="story-count">'.$news_count.'</div></a>';

					$first = false;
					$news_count++;
				}

				echo '</div>';
				
				echo '<div class="news-nav">';
				echo 	$news_nav;
				echo 	'<a class="all-link" href="'.Config::get('absolute_url').Config::get('news_url').'">See All</a>';
				echo '</div>';

			}catch(Exception $e){
				echo '<div class="news-copy-section">'.$e->getMessage().'</div>';
			}

		?>
	</div>
</div>