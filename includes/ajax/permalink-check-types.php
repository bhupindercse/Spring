<?php

	include("../init.php");

	echo json_encode(return_content($_REQUEST));
	
	function return_content($data)
	{
		Input::exists();

		$id = isset($data['id']) ? $data['id'] : "";

		$array = array(
			"id"             => $id,
			"title"          => $data['title'],
			"table"          => "types",
			"field_to_check" => "permalink"
		);

		$return_msg = Permalinks::checkPermalink($array);
		return $return_msg;
	}

?>