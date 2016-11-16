<?php
	
	include("../init.php");

	echo json_encode(return_content());
	
	function return_content()
	{
		Input::exists();
		
		$base_url   = "../../";
		$return_msg = array();

		include($base_url.'includes/searches/gallery.php');
				
		$return_msg['success'] = performSearch(array(
			"q"          => Input::get('q'),
			"pg"         => Input::get('pg'),
			"order_by"   => Input::get('order_by'),
			"sort_order" => Input::get('sort_order'),
			"page"       => Input::get('gallery-edit')
		));

		return $return_msg;
	}

?>