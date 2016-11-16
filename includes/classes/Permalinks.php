<?php

class Permalinks
{
	public static function checkPermalink($data){

		$title          = stripslashes(trim($data['title']));
		$table          = stripslashes(trim($data['table']));
		$id             = stripslashes($data['id']);
		$field_to_check = "";
		$language       = "";
		$unique         = 1;
		$return_msg     = array();

		if(isset($data['field_to_check']))
			$field_to_check = stripslashes(trim($data['field_to_check']));

		// Language-specific
		if(isset($data['language']))
			$language = stripslashes(trim($data['language']));

		// Does the permalink have to be unique?
		if(isset($data['unique']))
			$unique = stripslashes(trim($data['unique']));
			
		// TESTING
		//$msg['permalink'] = $title;
		//echo json_encode($msg);
			
		if(empty($title))
		{
			$return_msg['permalink'] = $title;
			return $return_msg;
		}

		if(empty($table))
		{
			$return_msg['error'] = "Unable to determine the table to look for an existing permalink.  Contact the web developer for assistance.";
			return $return_msg;
		}
		
		// ===========================================
		//	CREATE PERMALINK
		// ===========================================
		$permalink = self::convert_permalink($title, $id, $table, $field_to_check, $language, $unique);
		
		// if it's an array, an error is present	
		if(!count($permalink))
			$return_msg['error'] = 'Could not create a permalink for "'.$title.'".';
		elseif(array_key_exists("error", $permalink))
		{
			$return_msg['error']     = $permalink['error'];
			$return_msg['permalink'] = $permalink['username'];
		}
		else
			$return_msg['permalink'] = $permalink['username'];

		$return_msg['language'] = $language;
		
		// Success!!
		return $return_msg;
	}

	public static function createPermalink($str, $delimiter = '-'){
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		return $clean;
	}

	public static function convert_permalink($text, $id, $db_table, $field_to_check = "", $language = "", $unique = 1){

		$return_message = array();
		$connection = DBConn::getInstance();
		
		// replace accents
		$text = self::createPermalink($text);
		
		// limit characters
		$length = strlen($text);
		if($length > Config::get('permalink_max_length'))
		{
			$text = substr($text, 0, $max_length);
			
			// strip out extra chars at the end of the string
			if(strrpos($text, "-") == $max_length - 1)
				$text = substr($text, 0, strrpos($text, "-"));
		}

		// ===================================================================================
		//	Set username
		// ===================================================================================
		$return_message['username'] = $text;

		// ===================================================================================
		//	Make sure permalink is unused/not by a different user
		// ===================================================================================
		if($unique)
		{
			try
			{
				$statement = "SELECT * FROM ".Config::get('table_prefix').$db_table;

				if(!empty($language))
				{
					$statement .= " LEFT JOIN ".Config::get('table_prefix')."pages ON
						".Config::get('table_prefix')."pages.base_id = ".Config::get('table_prefix')."pages.id";
				}

				$statement .= " WHERE ";
				if(empty($field_to_check))
					$statement .= Config::get('table_prefix').$db_table.".id ";
				else
					$statement .= Config::get('table_prefix').$db_table.".".$field_to_check." ";
				$statement .= "LIKE ?";
				$array_params[] = $text;

				// Language-specific
				if(!empty($language))
				{
					$statement .= " AND language = ?";
					$array_params[] = $language;
				}

				$statement .= ";";
				$query = $connection->conn->prepare($statement);
				
				// $return_message['error'] = $statement;
				// return $return_message;
				
				$query->execute($array_params);
			}catch(PDOException $e){
				$return_message['error'] = "Error searching for similar permalink to: ".$text.'<br>'.$e->getMessage();
				return $return_message;
			}

			$suggest_names = false;
			
			// ===================================================================================
			//	see if this post has this permalink stored. If not, you can't use it
			// ===================================================================================
			if($query->rowCount())
			{
				$data = $query->fetch(PDO::FETCH_ASSOC);
				
				if(isset($data['item_id']))
					$item_id = $data['item_id'];
				else
					$item_id = $data['id'];

				// $return_message['error'] = $item_id ;
				// return $return_message;

				if($item_id != $id || empty($data['id']))
				{
					$return_message['error'] = "'".$text."' has been used by another page.  Please type in another URL permalink.";
				}
			}
		}
		
		return $return_message;
	}
}
?>