<?php
	
class Input
{
	private static $data = null;

	public static function exists($type = "")
	{
		$_instance = new Input();

		if(isset($_POST))
		{
			if(!empty($_POST))
			{
				self::$data['post'] = $_POST;
				$type = 'post';
			}
		}
		if(isset($_GET))
		{
			if(!empty($_GET))
			{
				self::$data['get'] = $_GET;
				$type = 'get';
			}
		}

		if(empty($type))
			return false;

		return true;
	}

	public static function hasValue($name, $type = "post")
	{
		if(isset(self::$data[$type][$name]) && self::$data[$type][$name] !== "")
			return true;
		return false;
	}

	public static function get($name, $type = "post")
	{
		if(isset(self::$data[$type][$name]))
		{
			$var = self::$data[$type][$name];

			if(gettype($var) && !is_array($var))
				$var = stripslashes($var);
			return $var;
		}
		return '';
	}

	public static function set($name, $value, $type = "post")
	{
		self::$data[$type][$name] = $value;
	}

	public static function clearAll()
	{
		self::$data = null;
	}

	public static function clearType($type)
	{
		if(isset(self::$data[$type]))
			self::$data[$type] = null;
	}

	public static function printAll()
	{
		echo '<pre>';
		print_r(self::$data);
		echo '</pre>';
	}

	public static function format($name, $input_type, $type = "post")
	{
		if(!isset(self::$data[$type][$name]))
			return false;

		switch($input_type)
		{
			case 'phone':
				self::$data[$type][$name] = str_replace(" ", "-", self::$data[$type][$name]);
				break;
			case 'postal':
				self::$data[$type][$name] = strtoupper(str_replace(" ", "", self::$data[$type][$name]));
				break;
			case 'textarea':
				self::$data[$type][$name] = str_replace("\r\n", "<br>", self::$data[$type][$name]);
				break;
			case 'textarea-br2nl':
				self::$data[$type][$name] = str_replace("<br>", "\r\n", self::$data[$type][$name]);
				break;
			case 'price':
				if(!is_numeric(self::$data[$type][$name]))
					break;
				self::$data[$type][$name] = sprintf('%0.2f', self::$data[$type][$name]);
				break;
			case 'db-date':
				self::$data[$type][$name] = date("Y-m-d H:i:s", strtotime(self::$data[$type][$name]));
				break;
		}

		return self::$data[$type][$name];
	}

	public static function validate($name, $type){

		if(!isset(self::$data[$type][$name]))
			return false;

		switch($input_type)
		{
			case 'email':
				return preg_match(Config::get('email_pattern'), self::$data[$type][$name]);
				break;
			case 'phone':
				return preg_match(Config::get('phone_pattern'), self::$data[$type][$name]);
				break;
		}

		return false;
	}

	// public function 
}

?>