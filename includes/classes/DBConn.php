<?php

class DBConn
{
	private static $_instance = null;
	public $conn              = "";
	
	function __construct()
	{
		try
		{
			// $this->conn = new PDO('mysql:host='.$globals->dbConnection['host'].';dbname='.$globals->dbConnection['db'].';charset=utf8', $globals->dbConnection['username'], $globals->dbConnection['password']);
			$this->conn = new PDO('mysql:host='.Config::get('dbConnection/host').';dbname='.Config::get('dbConnection/db').';charset=utf8', Config::get('dbConnection/username'), Config::get('dbConnection/password'));

			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(Exception $e) {
			die('Database Error: ' . $e->getMessage());
		}
	}
	
	function __destruct()
    {
        $this->conn = NULL;
    }

    public static function getInstance(){
    	if(!isset(self::$_instance))
			self::$_instance = new DBConn();

		return self::$_instance;
    }
}

?>