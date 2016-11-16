<?php
	
	include('../init.php');

	echo json_encode(return_content());
	
	function return_content()
	{
		$connection = DBConn::getInstance();

		Input::exists();
		$return_msg = array();

		$group = 1;
		if(Input::hasValue('pg'))
			$group = Input::get('pg');

		$max_items  = 3;
		$data_point = ($group - 1) * $max_items;

		try
		{
			$statement = "	SELECT SQL_CALC_FOUND_ROWS *
							FROM ".Config::get('table_prefix')."types
							ORDER BY title
							LIMIT ".$max_items."
							OFFSET ".$data_point.";";
			$query = $connection->conn->prepare($statement);
			$query->execute();
			if(!$query->rowCount()){
				$return_msg['content'] = '<div class="type-item">Check back for new candy types!</div>';
				return $return_msg;
			}

			// ===================================================================================
			//  Get count
			// ===================================================================================
			$query_rows = $connection->conn->prepare('SELECT FOUND_ROWS();');
			$query_rows->execute();
			$fetch_count = $query_rows->fetch(PDO::FETCH_ASSOC);

			// ===================================
			//	Figure out nav
			// ===================================
			$total_pages = ceil($fetch_count['FOUND_ROWS()'] / $max_items);
			$nav_content = '<div class="type-nav">';
			for($i = 1; $i <= $total_pages; $i++){
				$nav_content .= '<div class="item'; if($i == $group) $nav_content .= ' active'; $nav_content .= '" data-id="'.$i.'"></div>';
			}
			$nav_content .= '</div>';
			$return_msg['nav']         = $nav_content;
			$return_msg['total-pages'] = $total_pages;
			
			$content = "";

			$active_index  = $query->rowCount() > 1 ? ceil($max_items / 2) : 1;
			$current_index = 1;

			while($data = $query->fetch(PDO::FETCH_ASSOC)){
				$content .= '<div class="type-item'; if($current_index == $active_index) $content .= ' active'; $content .= '" data-id="'.$data['permalink'].'">';
				$content .= 	'<img class="type-image" src="'.Config::get('absolute_url').Config::get('type_images').$data['filename'].'" alt="'.$data['title'].'">';
				$content .= 	'<div class="type-info">';
				// $content .= 		'<div class="type-title">Candy Types</div>';
				$content .= 		'<div class="type-sub-title">'.$data['title'].'</div>';
				$content .= 		'<div class="type-content">'.$data['content'].'</div>';
				$content .= 	'</div>';
				$content .= '</div>';

				$current_index++;
			}

			$return_msg['content'] = $content;
			$return_msg['pg'] = $group;

			return $return_msg;

		}catch(Exception $e){
			$return_msg['error'] = $e->getMessage();
		}

		return $return_msg;
	}

?>