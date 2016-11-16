<?php
	
	$base_url      = "";
	$page_title    = "Search";

	include_once($base_url.'includes/init.php');
	$connection = DBConn::getInstance();
	Input::exists();
	
	$q             = Input::get('q', 'get');
	$record_count  = 0;
	$max_items     = 10;
	
	// group of records to show
	$group         = Input::hasValue('pg', 'get') ? Input::get('pg', 'get') : 1;
	$data_point    = ($group - 1) * $max_items;
	$error         = "";
	$default_error = "We were not able to find any information in the site that relates to what you were looking for.";
	$page_title    = "Search Results for: ".$q;
	$counter       = 0;

	// echo $data_point;

	function getSearchResultsFromData($txt, $data, $title = "")
	{
		$max_length = 300;
		$results = "";
		$strippedResults = trim(strip_tags($data));

		//DEBUGGING
		//echo ' - '.$strippedResults.'<br>';

		// find the first critPos of the search term
		$critPos = strpos(strtolower($strippedResults), strtolower($txt));
		
		// See if the string was actually in the page somewhere
		if($critPos !== FALSE)
		{
			//DEBUGGING
			//echo ' - '.$critPos.'<br>';
			// Find more of the sentence found
			if($critPos)
			{
				$substr = substr($strippedResults, 0, $critPos);
				$newPosition = strpos(strrev($substr), ". ");
				
				if(($newPosition !== FALSE) && (($critPos - $newPosition) < $max_length))
					$critPos = $newPosition;
				elseif($critPos < $max_length)
					$critPos = 0;
			}

			// get a portion of the results to display		
			$results = substr($strippedResults, $critPos, $max_length);						
			$results .= " ...";
			
			$results = preg_replace("/($txt)/i", '<span class="search_criteria">\1</span>', $results);
			// $results = str_ireplace(strtolower($txt), '<span class="search-result-string">'.$txt.'</span>', strtolower($results));
		}

		return $results;
	}

	if(!empty($q)){
		try
		{
			$statement = "	SELECT SQL_CALC_FOUND_ROWS *
														
							FROM
							(
								SELECT *
														
								FROM cmha_kiosk_services 
														
								WHERE cmha_kiosk_services.content LIKE ? OR 
									  cmha_kiosk_services.title LIKE ?
														
								UNION ALL

								SELECT *
								FROM cmha_kiosk_resources 
														
								WHERE cmha_kiosk_resources.content LIKE ? OR 
									  cmha_kiosk_resources.title LIKE ?
							) as poo
														
							ORDER BY id ASC
							LIMIT ".$max_items."
							OFFSET ".$data_point.";";

			$query_vars = array();
			$query_vars[0] = "%".$q."%";
			$query_vars[1] = "%".$q."%";
			$query_vars[2] = "%".$q."%";
			$query_vars[3] = "%".$q."%";
			$query = $connection->conn->prepare($statement);
			$query->execute($query_vars);

			if($query->rowCount()){
					$i = 0;

					// ===================================================================================
					//  Get count
					// ===================================================================================
					$query_rows = $connection->conn->prepare('SELECT FOUND_ROWS();');
					$query_rows->execute();

					$fetch_count = $query_rows->fetch(PDO::FETCH_ASSOC);

					$record_count += $fetch_count['FOUND_ROWS()'];

					while($searchRecord = $query->fetch(PDO::FETCH_ASSOC)){


					$strippedContent = trim(Utility::strip_html_tags($searchRecord['content']));

					if(empty($strippedContent))
							$strippedContent = trim(Utility::strip_html_tags($searchRecord['title']));
					
					$results = getSearchResultsFromData($q, $strippedContent);


						$content_results[$counter] = '<div class="search_results"> ';
						$content_results[$counter] .= '<h1>'.$searchRecord['title'].'</h1>';
						$content_results[$counter] .= $results;
					
					
						// ====================
						//  SET UP PERMALINK
						// ====================
						$permalink = $searchRecord['permalink'];
						$content_type = $searchRecord['content_type'];
						if(empty($permalink))
							$permalink = "?p=".md5($post_id);
						
						// ========================
						//  GOTO SEARCHED PAGE URL
						// ========================
						$post_url = Config::get('absolute_url').'/'.$content_type.'/'.$permalink;
	           			$content_results[$counter] .= '<div class="visit_page"><a href="'.$post_url.'" > Click to Visit Page </a></div>';
	           			$content_results[$counter] .= '<div class="spacing_div"></div>';
						$content_results[$counter] .= '</div>';

						$counter++;
					}
				}

		}catch(Exception $e){
			$error = "Error finding search results :<br>".$e->getMessage();
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo Config::get('site_title'); if(!empty($page_title)) echo ' - '.$page_title; ?></title>

	<?php include($base_url.'includes/global-includes.php'); ?>

</head>

<body lang="en" >
	<?php include($base_url.'includes/header.php'); ?>

	<div class="content">
		<div class="search_results">
			<?php 

				include($base_url.'includes/nav_logo.php');
				echo '<h1>CMHA-Search Results</h1>';
				echo '<hr class="search_results_hr">';
				
				if(!empty($error))
					echo "<div class='error'>".$error."</div><br />";
				
				if($counter == 0)
					echo "<div class='default_error'>".$default_error."</div>";
				else
				{
					echo '<h2>Search Results for : '.$q.'</h2>';
					echo Pagination::show_pageination(array(
						"page"         => Config::get('absolute_url').basename(__FILE__, '.php'),
						"max_items"    => $max_items,
						"group"        => $group,
						"total"        => $record_count,
						"extra_params" => "&q=".$q
					));
					
					// make sure the records have somewhere to start
					if(empty($group_counter))
					{
						$group_counter = 0;
						//$max_items = $counter;
					}
					else
						$max_items = $group_counter + $max_items;
					
					echo '<span id="sized">';
					foreach($content_results as $res){
						echo '<div>'.$res."</div>";
					}
					echo '</span>';
					
					echo "<div class='search_rows_returned'>Returned ".$record_count." result(s)</div>";
				}

			?>
		</div>
	</div>
	<script src="<?php echo Config::get('absolute_url'); ?>scripts/side-nav-buttons-min.js"></script>

</body>
</html>