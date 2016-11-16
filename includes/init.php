<?php

	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
	date_default_timezone_set("America/Detroit");
	
	session_start();

	spl_autoload_register(function($class){
		include 'classes/'.$class.'.php';
	});

?>