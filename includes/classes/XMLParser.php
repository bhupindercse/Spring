<?php

// ===================================================================================
//	NOTE: Class uses dom class because SimpleXML is not able to insert elements
//		  in specific locations within the document tree.
// ===================================================================================

class XMLParser
{
	protected $filename  = "";	// filename to save the rss info to
	protected $xml       = "";	// xml file itself
	protected $dom       = "";	// DOM object
	protected $debugging = 0;

	protected $sectionParameters = array();

	function __construct($filename, $debugging = 0){

		$this->filename  = dirname(__FILE__).'/../../'.$filename;
		$this->debugging = $debugging;
		
		if(!is_file($this->filename))
			throw new Exception("The RSS file does not exist at ".$this->filename);

		$this->xml = simplexml_load_file($this->filename);

		$this->dom = new DOMDocument('1.0');
		$this->dom->preserveWhiteSpace = false;
		$this->dom->formatOutput = true;
		$this->dom->loadXML($this->xml->asXML());

		if($this->debugging)
			$this->printDebug($this->xml, 'Loaded');

		$this->getNewId();
	}

	public function printDebug($object, $title = ""){
		
		if(!empty($title)) echo '<h2>'.$title.':</h2>';

		echo '<pre>';
		print_r($object);
		echo '</pre>';
	}

	public function save_xml(){

		$this->dom->loadXML($this->xml->asXML());
		$xpath = new DOMXpath($this->dom);
		
		// transfer back to simpleXML
		if(!$this->xml = simplexml_import_dom($this->dom))
			throw new Exception("Error loading XML.");
		
		// ===================================
		//	SAVE OUT FILE
		// ===================================
		$this->dom->save($this->filename);

		if($this->debugging)
			$this->printDebug($this->xml, 'Saved');
	}

	private function checkId($id, $idCollection){
		if(in_array($id, $idCollection)){
			$newId = ++$id;
			$id = $this->checkId($id, $idCollection);
		}

		return $id;
	}

	private function getNewId(){
		$xpath = new DOMXpath($this->dom);

		$ids = array();
		foreach($xpath->query('//item') as $node){
			$ids[] = $node->getElementsByTagName("guid")->item(0)->nodeValue;
		}
		$newID = count($ids) + 1;

		return $this->checkId($newID, $ids);
	}

	public function addItemParameter($section_name, $type, $value){

		if($section_name == "pubDate")
			$value = date('r', strtotime($value));

		$this->sectionParameters[$section_name] = array(
			'type'  => $type,
			'value' => $value
		);

		if($this->debugging){
			$this->printDebug($this->sectionParameters, 'Added item parameter');
		}
	}

	//$id, $title, $desc, $date, $permalink = ""
	public function add_xml($id = ""){

		$before   = true;
		$position = 1;
		
		$this->dom->loadXML($this->xml->asXML());
		$xpath = new DOMXpath($this->dom);
		$has_node = false;
		
		// Get position to insert element into
		if(isset($this->sectionParameters['pubDate']))
			$date = $this->sectionParameters['pubDate']['value'];
		else
			$date = date('r');
		$position = $this->find_node_by_date(strtotime($date));
		
		// get total count of elements
		$count = $xpath->query("//item")->length;
		if($count < $position || $count == 0)
		{
			// we will be inserting the element last (after)
			$before = false;
			
			// the position to find is the last element in the list
			$position = $count;
		}

		if(empty($id))
			$id = $this->getNewId();
		
		if($before)
		{
			$nodes = $xpath->query("//item[".$position."]");
			foreach($nodes as $node){

				try
				{
					$contents = $this->createNode($id);
				}catch(Exception $e){
					throw $e;
				}
				
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
			$nodes = $xpath->query("//channel");
			foreach($nodes as $node){

				$contents = $this->createNode($id);
				
				// append new 'item' to dom
				$node->appendChild($contents);
			}
		}
		
		// transfer back to simpleXML
		if(!$this->xml = simplexml_import_dom($this->dom))
			throw new Exception("Error loading XML.");

		if($this->debugging)
			$this->printDebug($this->xml, 'Added Section');

		return true;
	}

	private function createNode($id){

		if(empty($id))
			$id = $this->getNewId();

		// append the guid
		$contents = $this->dom->createElement('item');
		$guid_section = $this->dom->createElement('guid');
		$guid_section->appendChild(
			$this->dom->createTextNode($id)
		);
		$contents->appendChild($guid_section);

		// append attributes
		foreach($this->sectionParameters as $section_name => $item){
			
			$new_section = $this->dom->createElement($section_name);

			// $this->printDebug($new_section, "New section");

			switch($item['type']){
				case 'cdata':
					$new_section->appendChild($this->dom->createCDATASection($item['value']));
					break;
				case 'text':
					$new_section->appendChild($this->dom->createTextNode($item['value']));
					break;
				default:
					throw exception('Unable to determine type of section to add for '.$section_name);
					break;
			}
			$contents->appendChild($new_section);
		}
		
		return $contents;
	}

	public function change_xml($id){

		$this->dom->loadXML($this->xml->asXML());
		$xpath = new DOMXpath($this->dom);
		
		try
		{
			$this->delete_xml($id);
			$this->add_xml($id);
		}catch(Exception $e){
			throw $e;
		}
	}

	public function delete_xml($id){
		
		$this->dom->loadXML($this->xml->asXML());
		
		$xpath = new DOMXpath($this->dom);
		$node = $xpath->query('//item[guid="'.$id.'"]');
		if(!$node->length)
			throw new Exception("Item not deleted.  Not found.");

		foreach($node as $item){
			$item->parentNode->removeChild($item);
		}
		
		if(!$this->xml = simplexml_import_dom($this->dom))
			throw new Exception("Error loading XML.");

		if($this->debugging)
			$this->printDebug($this->xml, 'Deleted Section');
	}

	public function has_record($id){
		
		$this->dom->loadXML($this->xml->asXML());
		$xpath = new DOMXpath($this->dom);
		
		foreach($xpath->query("//item[guid='".$id."']") as $node)
			return true;
		
		return false;
	}

	public function find_node_by_date($date){

		$xpath = new DOMXpath($this->dom);
		$i = 1;
		
		foreach($xpath->query("//item") as $node)
		{
			$date_compare = $node->getElementsByTagName("pubDate")->item(0)->nodeValue;
			$date_compare = strtotime($date_compare);
			
			// if the date found is greater than the date being used
			if($date_compare > $date)
			{
				$i++;
			}
			elseif($date_compare < $date)
			{
				// if the date is greater than the date being compared, store position
				return $i;
			}
		}
		
		return $i;
	}

	public function empty_xml(){

		$xpath = new DOMXpath($this->dom);
		foreach($xpath->query('//item') as $node) {
			$node->parentNode->removeChild($node);
		}
		
		if(!$this->xml = simplexml_import_dom($this->dom))
			throw new Exception("Error loading XML.");
	}
}

?>