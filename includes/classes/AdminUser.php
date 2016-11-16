<?php

class AdminUser
{
	private $base_url            = __FILE__;
	private $errors              = array();
	private $loginEnabled        = true;
	private $successfully_called = array();
	private $activePageGrp       = "";
	private $user                = NULL;

	public function __construct(){
		if(isset($_SESSION['admin_user']))
			$this->user = $_SESSION['admin_user'];
	}

	public function isLoggedIn(){
		if(!Session::hasValue(Config::get('admin_login')))
			return false;
		return true;
	}

	public function login(){

		if(!$this->loginEnabled)
			return false;
		
		$connection = DBConn::getInstance();
		
		$ip         = $_SERVER['REMOTE_ADDR'];
		$email      = Input::get('email');
		$password   = Input::get('password');
		$token      = Input::get('token');
		$last_login = date("Y-m-d H:i:s");
		$id         = "";
		
		// ============================
		//	ERROR CHECK
		// ============================
		if(empty($email) || empty($password)){
			$this->errors['email'] = "Empty email address or password.";
			return false;
		}
		if(empty($ip)){
			$this->errors['general'] = "Cannot determine your IP address.";
			return false;
		}
		if(!Token::check($token, "admin_login_token")){
			$this->errors['token'] = "Cannot log you in.";
			return false;
		}
		
		// ============================
		//	CHECK FOR USERNAME
		// ============================
		try
		{
			$statement = "SELECT *
						  FROM ".Config::get('table_prefix')."admin
						  WHERE email = :email;";
			$query = $connection->conn->prepare($statement);
			$query->execute(array('email' => $email));
			$login_result = $query->fetchAll(PDO::FETCH_ASSOC);
			
			// ============================
			//	CORRECT USERNAME/PASSWORD
			// ============================
			if(count($login_result))
			{
				foreach($login_result as $data)
				{
					if(PasswordStorage::verify_password($password, $data['password']))
					{
						$id          = $data['id'];
						$admin_level = $data['admin_level'];
					}
				}
			}
		}catch(Exception $e){
			$this->errors['general'] = 'Error finding login info:<br>'.$e->getMessage();
			return false;
		}
		
		// ============================
		//	SUCCESS
		// ============================
		try
		{
			if(empty($id))
				$successfull = 0;
			else
				$successfull = $id;
			
			$statement = "	INSERT INTO ".Config::get('table_prefix')."admin_login
							(last_login, ip, session_id, email, successfull) VALUES
							(:last_login, :ip, :session_id, :email, :successfull);";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(
				'last_login'  => $last_login,
				'ip'          => $ip,
				'session_id'  => session_id(),
				'email'       => $email,
				'successfull' => $successfull
			));
			
			if(empty($id))
			{
				$this->check_login_enabled();
				
				$this->errors['general'] = "Incorrect email address or password.";
				return false;
			}
			else
			{
				// Set the user before setting any session vars
				$this->setUser($login_result[0]);

				Session::set(Config::get('admin_login'), $id);
				Session::set('admin_level', $admin_level);
				Session::set('IsAuthorized', 1);
				
				Session::clear('login_attempts_remaining');
			}
		}catch(Exception $e){
			$this->errors['general'] = "Error logging information:<br>".$e->getMessage();
			return false;
		}

		// success
		return true;
	}

	public function logout()
	{
		if(Session::hasValue(Config::get('admin_user')))
			Session::clear(Config::get('admin_user'));

		if(Session::hasValue(Config::get('admin_login')))
			Session::clear(Config::get('admin_login'));
		
		if(Session::hasValue('IsAuthorized'))
			Session::clear('IsAuthorized');
			
		if(Session::hasValue(Config::get('table_prefix').'admin_type'))
			Session::clear(Config::get('table_prefix').'admin_type');
	}

	private function setUser($data){

		foreach($data as $key => $val){
			if($key !== "password")
				$this->user[$key] = $val;
		}

		Session::set(Config::get('admin_user'), $this->user);
	}

	public function check_login_enabled()
	{
		$connection = DBConn::getInstance();
		
		// 15 mins ago
		$time_span      = date("Y-m-d H:i:s"); 
		$max_difference = 15;
		$login_enabled  = "";
			
		Session::set('login_attempts_remaining', Config::get('max_login_tries'));
		
		// ============================
		//	Get user's IP address
		// ============================
		$ip = $_SERVER['REMOTE_ADDR'];
		
		try
		{
			// ============================
			//	FIND OUT IF THIS USER CAN ATTEMPT A LOGIN (stops page refreshes)
			// ============================
			$statement = "SELECT *
						  FROM ".Config::get('table_prefix')."admin_login
						  WHERE ip = :ip AND session_id = :session_id
						  ORDER BY last_login DESC;";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(
				'ip' => $ip,
				'session_id' => session_id()
			));
		}catch(PDOException $e){
			$login_enabled = "Error retrieving IP:<br>".$e->getMessage();
			return $login_enabled;
		}
		
		if($query->rowCount())
		{
			//print_r($res);
			// if they have tried to login three times
			$num = Config::get('max_login_tries');
			if($num > $query->rowCount())
				$num = $query->rowCount();
				
			// check last x logins
			for($i = 0; $i < $num; $i++)
			{
				$data = $query->fetch(PDO::FETCH_ASSOC);
				
				// if it was a successfull login, don't bother comparing and allow login
				if($data['successfull'])
					break;
				
				// login attempt time
				$login_time = strtotime($data['last_login']);
				
				// ===============================
				//	IF LAST LOGIN ATTEMPT, STORE THE TIME TO COMPARE
				// ===============================
				if(!$i)
				{
					$t = $login_time + 900;
					$wait_time = abs(round(($t - time())/60, 2));
					$wait_time_formatted = date("i:s", $t - time());
				}
				
				// difference of the login vs current time
				$difference = abs(round(($login_time - time())/60, 2));
				
				// if difference is greater than 15 mins, allow login
				if($difference >= $max_difference)
					break;
					
				Session::set('login_attempts_remaining', Session::get('login_attempts_remaining') - 1);

				$this->errors['loginEnabled'] = "You have ".Session::get('login_attempts_remaining')." login attempts remaining.";
				
				// if on the last try
				if($i == (Config::get('max_login_tries') - 1))
				{
					$this->loginEnabled = false;
					$this->errors['loginEnabled'] = "You have attempted too many logins and cannot login again for ".$max_difference." minutes.";
					return false;
				}
			}
		}
		
		$this->loginEnabled = true;
		return true;
	}

	public function isLoginEnabled()
	{
		if($this->loginEnabled === null)
			$this->check_login_enabled();

		return $this->loginEnabled;
	}

	public function checkSuperAccess()
	{
		$level = Session::get('admin_level');
		if(!empty($level))
			return true;

		return false;
	}

	public function create_user($data_in)
	{
		$connection = DBConn::getInstance();

		if(!isset($data_in['id']))
			$id = NULL;
		else
			$id = $data_in['id'];

		if(isset($data_in['active']))
			$active = $data_in['active'];
		elseif($id == NULL)
			$active = 1;
		else
			$active = 0;

		if(isset($data_in['level']))
			$admin_level = $data_in['level'];
		else
			$admin_level = 0;
		
		// ============================================
		//	Error check
		// ============================================
		if(!isset($data_in['email']) || !preg_match(Config::get("email_pattern"), $data_in['email']))
			$this->errors['email'] = "Please enter a real email address that can be used for this user.";

		// if(!isset($data_in['username']) || empty($data_in['username']))
		// 	$this->errors['username'] = "Please give this user a username.";

		if($id == NULL)
		{
			if(!isset($data_in['password']) || empty($data_in['password']))
				$this->errors['password'] = "Please generate a password for the user.";
		}

		if(count($this->errors))
			return $this->errors;
		
		try
		{
			// ============================================
			//	Make sure username is new
			// ============================================
			$statement = "SELECT *
						  FROM ".Config::get('table_prefix')."admin
						  WHERE email = :email AND NOT(id <=> :id);";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(
				'email' => $data_in['email'],
				':id'   => $id
			));
		}catch(PDOException $e){
			$this->errors['general'] = "Error looking for taken email address.";
			return false;
		}
		if($query->rowCount())
		{
			$this->errors['general'] = "Email address is already taken.";
			return false;
		}
		
		// ============================================
		// 	Hash password
		// ============================================
		if(!empty($data_in['password']))
			$password = PasswordStorage::hash_pass($data_in['password']);

		// BEGIN TRANSACTION!
		$connection->conn->beginTransaction();
		
		try
		{
			// ============================================
			//	Insert into db
			// ============================================
			$statement = "	INSERT INTO ".Config::get('table_prefix')."admin
							(id, email, active, admin_level";
			if(!empty($data_in['password']))
				$statement .= ", password";
			$statement .= ") VALUES (?, ?, ?, ?";

			$query_vars[] = $id;
			$query_vars[] = $data_in['email'];
			$query_vars[] = $active;
			$query_vars[] = $admin_level;

			if(!empty($data_in['password'])){
				$statement .= ", ?";
				$query_vars[] = $password;
			}
			$statement .= ")";

			$statement .= " ON DUPLICATE KEY UPDATE
				email       = ?,
				active      = ?,
				admin_level = ?";
			$query_vars[] = $data_in['email'];
			$query_vars[] = $active;
			$query_vars[] = $admin_level;

			if(!empty($data_in['password'])){
				$statement .= ", password = ?";
				$query_vars[] = $password;
			}

			$statement .= ";";
			$query = $connection->conn->prepare($statement);
			$query->execute($query_vars);
		}catch(Exception $e){
			$connection->conn->rollBack();
			$this->errors['general'] = "Error storing the new user.<br>".$e->getMessage();
			return false;
		}

		if($id == NULL)
			$id = $connection->conn->lastInsertId();

		$connection->conn->commit();
		// $connection->conn->rollBack();
		
		// ===================================
		//	OPTIMIZE
		// ===================================
		try
		{
			$statement = "OPTIMIZE TABLE ".Config::get("table_prefix")."admin;";
			$query = $connection->conn->prepare($statement);
			$query->execute();
		}catch(PDOException $e){
			$connection->conn->rollBack();
			$this->errors['general'] = "There was an error optimizing the system.<br>".$e->getMessage();
		}
		
		// Success!!
		return $id;
	}

	public function setActivePageGrp($grp){
		$this->activePageGrp = $grp;
	}
	public function getActivePageGrp(){
		return $this->activePageGrp;
	}

	private function getAdminLevel($id = NULL){
		if(!$this->isLoggedIn())
			return;

		if($id == NULL)
		{
			if(!isset($this->user['id']))
				return false;

			$id = $this->user['id'];
		}

		try
		{
			$connection = DBConn::getInstance();

			$statement = "SELECT * FROM ".Config::get('table_prefix')."admin WHERE id = :id";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(":id" => $id));
			if(!$query->rowCount())
				throw new Exception("Unable to find admin while checking level.");

			$data = $query->fetch(PDO::FETCH_ASSOC);
			return $data['admin_level'];

		}catch(Exception $e){
			throw new Exception("Error finding admin level: ".$e->getMessage());
		}
	}

	public function call($script, $function){

		// Make sure both vars are available
		if(empty($script) || empty($function)){
			$this->errors['general'] = "Unable to call ".$function." inside ".$script.".";
			return false;
		}

		$location = dirname(__FILE__).'/../../'.Config::get("database_scripts").$script.".php";

		// Make sure script exists and include it
		if(!file_exists($location))
		{
			$this->errors['general'] = $location." does not exist.";
			return false;
		}
		include_once($location);

		// Make sure function exists
		if(!is_callable($function))
		{
			$this->errors['general'] = $function." does not exist.";
			return false;
		}

		// Call function and store any errors it returned!
		$errors = call_user_func($function);
		
		if(count($errors))
			$this->errors = $errors;
		else
			$this->successfully_called[$function] = true;
	}

	public function hasError($property){
		if(array_key_exists($property, $this->errors))
			return true;
		return false;
	}

	public function hasAnyErrors(){
		return count($this->errors);
	}

	public function getError($property){
		if(array_key_exists($property, $this->errors))
			return $this->errors[$property];
		return null;
	}

	public function getAllErrors(){
		return $this->errors;
	}

	public function setError($property, $data){
		return $this->errors[$property] = $data;
	}

	public function successfullyCalled($function = ""){

		if(empty($function) && count($this->successfully_called))
			return true;
		else if(!empty($function))
		{
			if(isset($this->successfully_called[$function]))
				return true;
		}
		return false;
	}
}

?>