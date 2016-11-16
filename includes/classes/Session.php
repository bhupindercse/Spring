<?php

class Session
{
	public static function get($name){
		if(isset($_SESSION[$name]))
			return $_SESSION[$name];
		return "";
	}

	public static function hasValue($name){
		if(isset($_SESSION[$name]))
			return true;
		return false;
	}

	public static function set($name, $val){
		$_SESSION[$name] = $val;
	}

	public static function clear($name){
		if(isset($_SESSION[$name]))
			unset($_SESSION[$name]);
	}
}

?>