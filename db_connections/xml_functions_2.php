<?php

	$xml_file_name = "";	// filename to save the rss info to
	$xml_href      = "";	// href used in rss 'stories'
	$xml           = "";	// xml file itself
	$dom           = "";	// DOM object
	$type          = "";	// type of rss file (id in database info table - "news", "blog", etc)
	
	function init_files($filename, $href, $type_in)
	{
		global $base_url;
		global $dom;
		global $xml;
		global $xml_file_name;
		global $xml_href;
		global $type;
		
		//return "Error check: ".$base_url;
		
		$xml_file_name = $base_url.$filename;
		$xml_href      = $href;
		$type          = $type_in;
		
		// test for file
		if(!is_file($xml_file_name))
			return "The RSS file does not exist at ".$xml_file_name;
		
		// load file as simpleXML
		$xml = simplexml_load_file($xml_file_name);
		
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());
		
		//echo $dom->saveXML();
		
		// success!!
		return "";
	}
	
	function save_xml()
	{
		global $dom;
		global $xml;
		global $xml_file_name;
		global $type;
		global $base_url;
		
		require_once($base_url.'includes/Global-Settings.php');
		$globals = new Globals();
		include_once($base_url.'db_connections/DBConn.php');
		$connection = new DBConn();
		
		// transfer simpleXML to DOM
		$dom->loadXML($xml->asXML());
		
		// get ready for xpath query
		$xpath = new DOMXpath($dom);
		
		try
		{
			// ===================================
			//	GET MAIN TAG INFO
			// ===================================
			$statement = "SELECT * FROM ".$globals->table_prefix."rss WHERE id = :id;";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(':id' => $type));
		}
		catch(PDOException $e){
			return "Unable to find the rss information needed to save the file:<br />".$e->getMessage();
		}
		if(!$query->rowCount())
			return "Unable to find the rss information needed to save the file.";
		else
		{
			$data = $query->fetch();
			$title_db = $data['title'];
			$link_db  = $data['link'];
			$desc_db  = $data['description'];
		}
		
		// ===================================
		//	REPLACE RSS MAIN TAGS
		// ===================================
		$nodes = $xpath->query("/");
		foreach($nodes as $node)
		{
			$title = $dom->createElement('title');
			$title->appendChild(
				$dom->createTextNode($title_db)
			);
			
			// find the node to replace and replace it
			$replaced = $node->getElementsByTagName("title")->item(0);
			$replaced->parentNode->replaceChild($title, $replaced);
			
			// --------------------------------------------------
			
			$link = $dom->createElement('link');
			$link->appendChild(
				$dom->createTextNode($link_db)
			);
			
			// find the node to replace and replace it
			$replaced = $node->getElementsByTagName("link")->item(0);
			$replaced->parentNode->replaceChild($link, $replaced);
			
			// --------------------------------------------------
			
			$desc = $dom->createElement('description');
			$desc->appendChild(
				$dom->createTextNode($desc_db)
			);
			
			// find the node to replace and replace it
			$replaced = $node->getElementsByTagName("description")->item(0);
			$replaced->parentNode->replaceChild($desc, $replaced);
		}
		
		// transfer back to simpleXML
		if(!$xml = simplexml_import_dom($dom))
			return "Error";
		
		// ===================================
		//	SAVE OUT FILE
		// ===================================
		$dom->save($xml_file_name);
		
		//echo $dom->saveXML();
		
		// success!!
		return "";
	}
	
	function add_xml($id, $title_in, $desc_in, $date_in, $permalink = "")
	{
		global $xml;
		global $dom;
		global $xml_href;
		$before = true;
		$position = 1;
		
		// transfer simpleXML to DOM
		$dom->loadXML($xml->asXML());
		
		// get ready for xpath query
		$xpath = new DOMXpath($dom);
		$has_node = false;
		
		// Get position to insert element into
		$position = find_node_by_date(strtotime($date_in));
		
		//echo $date_in;
		
		// get total count of elements
		$count = $xpath->query("//item")->length;
		if($count < $position || $count == 0)
		{
			// we will be inserting the element last (after)
			$before = false;
			
			// the position to find is the last element in the list
			$position = $count;
		}
		
		if($before)
		{
			$nodes = $xpath->query("//item[".$position."]");
			foreach($nodes as $node)
			{
				// append the title
				$contents = $dom->createElement('item');
				$title = $dom->createElement('title');
				$title->appendChild(
					$dom->createCDATASection($title_in)
				);
				$contents->appendChild($title);
				
				// append the link
				$link = $dom->createElement('link');
				$link->appendChild(
					$dom->createTextNode($xml_href.$permalink)
				);
				$contents->appendChild($link);
				
				// append the guid
				$guid = $dom->createElement('guid');
				$guid->appendChild(
					$dom->createTextNode($id)
				);
				$contents->appendChild($guid);
				
				// append the pubDate
				$pubDate = $dom->createElement('pubDate');
				$pubDate->appendChild(
					$dom->createTextNode(date('r', strtotime($date_in)))
				);
				$contents->appendChild($pubDate);
				
				// append the description
				$description = $dom->createElement('description');
				$description->appendChild(
					$dom->createCDATASection($desc_in)
				);
				$contents->appendChild($description);
				
				// append new 'item' to dom
				$node->parentNode->insertBefore($contents, $node);
				
				$has_node = true;
			}
		}
	
		// =========================================
		//	IF THERE ARE NO ITEMS TO START WITH
		// =========================================
		if(!$has_node)
		{
			// start 
			$nodes = $xpath->query("//channel");
			
			foreach($nodes as $node)
			{
				// append the title
				$contents = $dom->createElement('item');
				$title = $dom->createElement('title');
				$title->appendChild(
					$dom->createCDATASection($title_in)
				);
				$contents->appendChild($title);
				
				// append the link
				$link = $dom->createElement('link');
				$link->appendChild(
					$dom->createTextNode($xml_href.$permalink)
				);
				$contents->appendChild($link);
				
				// append the guid
				$guid = $dom->createElement('guid');
				$guid->appendChild(
					$dom->createTextNode($id)
				);
				$contents->appendChild($guid);
				
				// append the pubDate
				$pubDate = $dom->createElement('pubDate');
				$pubDate->appendChild(
					$dom->createTextNode(date('r', strtotime($date_in)))
				);
				$contents->appendChild($pubDate);
				
				// append the description
				$description = $dom->createElement('description');
				$description->appendChild(
					$dom->createCDATASection($desc_in)
				);
				$contents->appendChild($description);
				
				// append new 'item' to dom
				$node->appendChild($contents);
			}
		}
		
		//echo $dom->saveXML();
		
		// transfer back to simpleXML
		if(!$xml = simplexml_import_dom($dom))
			return "Error";
			
		// success!!
		return "";
	}
	
	function change_xml($id, $title_in, $desc_in, $date_in, $permalink = "")
	{
		global $xml;
		global $dom;
		global $xml_href;
		$found = false;
		
		// transfer simpleXML to DOM
		$dom->loadXML($xml->asXML());
		
		// get ready for xpath query
		$xpath = new DOMXpath($dom);
		
		// delete node
		$error = delete_xml($id);
		if(!empty($error))
			return $error;
		
		return add_xml($id, $title_in, $desc_in, $date_in, $permalink);
		
		/*
		// Get position to insert element into
		$position = find_node_by_date(strtotime($date_in));
		
		// get total count of elements
		$count = $xpath->query("//item")->length;
		if($count < $position || $count == 0)
		{
			// we will be inserting the element last (after)
			$before = false;
			
			// the position to find is the last element in the list
			$position = $count;
		}
		
		foreach($xpath->query("//item[guid = '".$id."']") as $node)
		{
			// ================================
			//	TITLE
			// ================================
			// create new description node
			$title = $dom->createElement('title');
			$title->appendChild(
				$dom->createCDATASection($title_in)
			);
			
			// find the node to replace and replace it
			$replaced = $node->getElementsByTagName("title")->item(0);
			$replaced->parentNode->replaceChild($title, $replaced);
			
			// ================================
			//	DESCRIPTION
			// ================================
			// create new description node
			$description = $dom->createElement('description');
			$description->appendChild(
				$dom->createCDATASection($desc_in)
			);
			
			// find the node to replace and replace it
			$replaced = $node->getElementsByTagName("description")->item(0);
			$replaced->parentNode->replaceChild($description, $replaced);
			$found = true;
			
			// ================================
			//	LINK
			// ================================
			// create new description node
			$link = $dom->createElement('link');
			$link->appendChild(
				$dom->createTextNode($xml_href.$permalink)
			);
			
			// find the node to replace and replace it
			$replaced = $node->getElementsByTagName("link")->item(0);
			$replaced->parentNode->replaceChild($link, $replaced);
		}
		
		if($found)
		{
			// transfer back to simpleXML
			if(!$xml = simplexml_import_dom($dom))
				return "Error";
		}
		else
			add_xml($id, $title_in, $desc_in, $date_in, $permalink);
			
		// success!!
		return "";
		
		*/
	}
	
	function delete_xml($id)
	{
		global $xml;
		global $dom;
		global $xml_href;
		
		// transfer simpleXML to DOM
		$dom->loadXML($xml->asXML());
		
		$xpath = new DOMXpath($dom);
		foreach($xpath->query('//item[guid="'.$id.'"]') as $node)
		{
			$node->parentNode->removeChild($node);
		}
		
		//echo $dom->saveXML();
		
		if(!$xml = simplexml_import_dom($dom))
			return "Error";
		
		// success!!
		return "";
	}
	
	function has_record($id)
	{
		global $xml;
		global $dom;
		global $xml_href;
		$found = false;
		
		// transfer simpleXML to DOM
		$dom->loadXML($xml->asXML());
		
		// get ready for xpath query
		$xpath = new DOMXpath($dom);
		
		//echo $xml_href.$id;
		
		foreach($xpath->query("//item[guid='".$id."']") as $node)
			return true;
		
		return false;
	}
	
	function find_node_by_date($date_in)
	{
		global $xml;
		global $dom;
		
		//echo $date_in.'<br />';
		
		// get ready for xpath query
		$xpath = new DOMXpath($dom);
		
		// Counter
		$i = 1;
		$found = false;
		
		foreach($xpath->query("//item") as $node)
		{
			$date_compare = $node->getElementsByTagName("pubDate")->item(0)->nodeValue;
			$date_compare = strtotime($date_compare);
			
			// if the date found is greater than the date being used
			if($date_compare > $date_in)
			{
				//echo "Less<br />".$date_in.' < '.$date_compare.'<br /><br />';
				$i++;
			}
			elseif($date_compare < $date_in)
			{
				// if the date is greater than the date being compared, store position
				//echo "More<br />".$date_in.' > '.$date_compare.'<br /><br />';
				$found = true;
				return $i;
				//$node_num = $i;
			}
		}
		
		return $i;
	}
	
	function empty_xml()
	{
		global $xml;
		global $dom;
		
		$xpath = new DOMXpath($dom);
		foreach($xpath->query('//item') as $node) {
			$node->parentNode->removeChild($node);
		}
		
		if(!$xml = simplexml_import_dom($dom))
			return "Error";
		
		// success!!
		return "";
	}
	
?>