<?php

	function performSearch($params)
	{
		$return_msg = "";

		try
		{
			$connection = DBConn::getInstance();

			$q = "";
			if(isset($params['q']))
				$q = $params['q'];

			$group = 1;
			if(isset($params['pg']) && !empty($params['pg']))
				$group = $params['pg'];
			
			$max_items = 20;
			$data_point = ($group - 1) * $max_items;

			$order_by = "date_posted";
			if(isset($params['order_by']) && !empty($params['order_by']))
				$order_by = $params['order_by'];
			
			$sort_order = "ASC";
			if(isset($params['sort_order']) && !empty($params['sort_order']))
				$sort_order = $params['sort_order'];

			$page = "";
			if(isset($params['page']) && !empty($params['page']))
				$page = $params['page'];
				
			// =====================================================
			//  Select records
			// =====================================================
			$statement = '  SELECT 	SQL_CALC_FOUND_ROWS
									'.Config::get('table_prefix').'reviews.*
						    FROM '.Config::get('table_prefix').'reviews ';

			$query_vars = array();
			if(isset($params['q']) && !empty($params['q']))
			{
				$statement .= 'WHERE title LIKE ?';
				$query_vars[] = "%".$q."%";
			}

			$statement .= ' ORDER BY '.$order_by.' '.$sort_order.'
						    LIMIT '.$max_items.'
						    OFFSET '.$data_point.';';
			$query = $connection->conn->prepare($statement);
			$query->execute($query_vars);
			if(!$query->rowCount())
				$return_msg .= 'Unable to find any results for your search.';
			else
			{
				// ===================================================================================
				//  Get count
				// ===================================================================================
				$query_rows = $connection->conn->prepare('SELECT FOUND_ROWS();');
				$query_rows->execute();
				$fetch_count = $query_rows->fetch(PDO::FETCH_ASSOC);

				// =====================================================
				//  Show pagination
				// =====================================================
				$return_msg .= Pagination::show_pageination(array(
					"page"      => $page,
					"max_items" => $max_items,
					"group"     => $group,
					"total"     => $fetch_count['FOUND_ROWS()']
				));
				
				// =====================================================
				//  move the pointer to the correct position
				// =====================================================
				$group--;
				$i = $group * $max_items + 1;
				
				$return_msg .= '<table class="display_table">';
				$return_msg .= 	'<tr class="table-header">';
				$return_msg .= 		'<th></th>';
				$return_msg .=        '<th>Reviewer</th>';
				$return_msg .=        '<th></th>';
				$return_msg .= 	'</tr>';
				
				while($data = $query->fetch(PDO::FETCH_ASSOC))
				{
					// $date_posted = date("d M Y", strtotime($data['date_posted']));
					$reviewer       = $data['reviewer'];
					
					$return_msg .= '<tr class="clickable" rel="'.md5($data['id']).'">';
					$return_msg .=    '<td class="record-number">'.$i.'</td>';
					$return_msg .=    '<td class="strong" data-id="Title">'.$reviewer.'</td>';
					$return_msg .=    '<td class="edit-cell"><div class="edit_btn">Edit</div></td>';
					$return_msg .= '</tr>';
					
					$i++;
				}
					
				$return_msg .= '</table>';
			}
		}catch(PDOException $e){
			$return_msg .= "Error:<br>".$e->getMessage();
		}

		return $return_msg;
	}

?>