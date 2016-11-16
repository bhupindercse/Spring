<?php

class Token
{
	public static function generate($token_name = ""){

		if(empty($token_name))
			$token_name = Config::get("token");

		$token = hash("sha512", uniqid() * microtime());
		$_SESSION[$token_name] = $token;
		
		return $token;
	}

	public static function check($token, $token_name = ""){

		if(empty($token_name))
			$token_name = Config::get("token");

		if(!isset($_SESSION[$token_name]))
			return false;

		$session_token = $_SESSION[$token_name];
		if(!empty($token) && isset($session_token) && $session_token == $token){
			unset($_SESSION[$token_name]);
			return true;
		}

		return false;
	}
}

?>