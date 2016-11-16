<?php

class Pagination
{	
	public $do_pagination    = true;
	public $pagination_query = "";
	public $base_url         = "";
	
	public static function show_pageination($params)
	{
		$current_page      = "";
		$max_items         = 10;
		$group             = 0;
		$item_count        = -1;
		$extra_query       = "";
		$extraQueryStrings = "";

		if(isset($params['page']))
			$current_page = $params['page'];
		if(isset($params['max_items']))
			$max_items = $params['max_items'];
		if(isset($params['group']))
			$group = $params['group'];
		if(isset($params['total']))
			$item_count = $params['total'];
		if(isset($params['extra_params']))
			$extraQueryStrings = $params['extra_params'];

		$msg = "";
		
		$group_counter = $group * $max_items;
		$max_group = ceil($item_count/$max_items);
		if($max_group == 0)
			$max_group = 1;
		$max_pages   = $max_group;								// What is the maximum page number
		$page_number = $group;
		$difference  = 2;
		$max_shown   = 5;

		$highest_page = $max_shown;
		$lowest_page  = 1;

		// ===================================================================================
		//	Figure out lowest/highest page
		// ===================================================================================
		if($group > $difference)
			$lowest_page = $group - $difference;

		if($max_pages > $group)
		{
			$highest_page = $group + $difference;

			if(($highest_page < $max_shown) && ($max_shown < $max_group))
				$highest_page = $max_shown;

			if($highest_page > $max_group)
				$highest_page = $max_group;
		}
		else
			$highest_page = $group;
		
		if($max_group > 1)
		{
			$msg .= '<div id="pagination_container">';
			$msg .= 	'<div class="pagination-pages">';

			$prev = $group - 1;
			if($prev < 1)
				$msg .= '<span class="counter prev-counter inactive"></span>';
			else
				$msg .= '<a class="counter prev-counter" href="'.$current_page.'?pg='.$prev.$extraQueryStrings.'"></a>';
			
			// if the page number is greater than 2
			if($page_number > 2)
			{
				// display the list
				for($i = $lowest_page; $i <= $highest_page; $i++)
				{
					if($i == $page_number)
						$msg .= '<span class="counter current_counter">'.$i.'</span>';
					else
						$msg .= '<a class="counter" href="'.$current_page.'?pg='.$i.$extraQueryStrings.'">'.$i.'</a>';
				}
			}
			// page number is under the minimum
			else
			{
				// display the list
				for($i = $lowest_page; $i <= $highest_page; $i++)
				{
					if($i == $page_number)
						$msg .= '<span class="counter current_counter">'.$i.'</span>';
					else
						$msg .= '<a class="counter" href="'.$current_page.'?pg='.$i.$extraQueryStrings.'">'.$i.'</a>';
				}
			}
			
			// show last button
			if($group < ($max_group - $difference))
			{
				$msg .= '<a class="counter';
				if($group >= $max_pages)
					$msg .= ' current_counter';
				$msg .= '" href="'.$current_page.'?pg='.$max_pages.$extraQueryStrings.'">&gt;&gt;</a>';
			}

			$next = $group + 1;
			if($next > $max_group)
				$msg .= '<span class="counter next-counter inactive"></span>';
			else
				$msg .= '<a class="counter next-counter" href="'.$current_page.'?pg='.$next.$extraQueryStrings.'"></a>';
			
			// end pagination container
			$msg .= 	'</div>';
			$msg .= '</div>';
		}
		
		return $msg;
	}
}

?>