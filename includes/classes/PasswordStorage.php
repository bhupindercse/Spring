<?php

class PasswordStorage
{
	public static function unique_salt()
	{
    	return substr(sha1(mt_rand()),0,12);
	}
	
	public static function hash_pass($password)
	{
		return crypt($password, self::unique_salt());
	}
	
	public static function verify_password($input, $stored)
	{
		return ($stored == crypt($input, $stored));
	}
}

?>