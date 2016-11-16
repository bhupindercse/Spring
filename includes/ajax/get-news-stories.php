<?php
	
	include('../init.php');

	echo json_encode(return_content());
	
	function return_content()
	{
		$connection = DBConn::getInstance();

		Input::exists();
		$return_msg = array();
		$current_max = Input::get('current_max');
		$limit = 4;

		try
		{
			$stories = News::getStoriesAtIndex($current_max, $limit);
			$current_max += count($stories['stories']);

			$html = "";
			$max_length = 100;

			foreach($stories['stories'] as $data){

				$news_url = Config::get('absolute_url').Config::get('news_url').$data['permalink'];

				$html .= '<div class="news-item small-news-item" data-id="'.$data['permalink'].'">';
				$html .= 	'<div class="news-copy-section'; if(!empty($data['filename'])) $html .= ' with-image'; $html .= '">';
				$html .= 		'<div class="padder">';
				$html .= 			'<a href="'.$news_url.'" class="news-title red-txt">'.$data['title'].'</a>';
				$html .= 			'<div class="news-date page-date"><span class="icon-clock"></span>'.date("d F, Y", strtotime($data['date_posted'])).'</div>';

				$content = substr(strip_tags($data['content']), 0, $max_length)." ...";

				$html .= 			'<div class="news-content">';
				$html .=				'<div>';
				$html .= 					$content;
				$html .=				'</div>';
				$html .=				'<div
											class="fb-like"
											data-share="true"
											data-width="100"
											data-layout="button"
											data-show-faces="false"
											data-href="'.$news_url.'">
										</div>';
				$html .= 			'</div>';
				$html .= 		'</div>';
				$html .= 	'</div>';

				if(!empty($data['filename'])){
					$html .= '<div class="news-image-section" style="background-image:url('.Config::get('absolute_url').Config::get('news_images').rawurlencode($data['filename']).'"></div>';
				}

				$html .= '</div>';
			}

			// DEBUGGING!!
			// $html = '<pre>'.print_r($stories, true).'</pre>';

			$return_msg['more-stories'] = $current_max < $stories['count'] ? 1 : 0;
			$return_msg['current_max']  = $current_max;
			$return_msg['success']      = $html;

		}catch(Exception $e){
			$return_msg['success'] = '<div class="padder">'.$e->getMessage().'</div>';
		}

		return $return_msg;
	}

?>