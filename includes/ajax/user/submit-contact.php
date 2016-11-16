<?php
	
	include_once('../../init.php');

	header('Content-Type: application/json');
	echo json_encode(performRequest());

	function performRequest(){

		$return_msg = array();

		Input::exists();
		$name     = Input::get('name');
		$email    = Input::get('email');
		$comments = Input::get('comments');
		$nonce    = Input::get('nonce');

		// Check nonce token
		if(!Token::check($nonce, "contact-request"))
		{
			$return_msg = createReturnMsg("Cannot verify you.");
			return $return_msg;
		}

		// ===================================
		//	Error check
		// ===================================
		if(empty($name)){
			$return_msg = createReturnMsg("Please enter your full name.");
			return $return_msg;
		}
		if(Input::validate('email', 'email')){
			$return_msg = createReturnMsg("Please enter a valid email address.");
			return $return_msg;
		}
		if(empty($comments)){
			$return_msg = createReturnMsg("Please enter some comments for us.");
			return $return_msg;
		}

		$comments = nl2br($comments);

		// ===================================
		//	Add to DB
		// ===================================
		$connection = DBConn::getInstance();
		try
		{
			$statement = "	INSERT INTO ".Config::get('table_prefix')."contact
							(`name`, `email`, `comments`) VALUES (:name, :email, :comments);";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(
				":name"     => $name,
				":email"    => $email,
				":comments" => $comments
			));

			Session::set('contact-submit', 1);
			$return_msg['success'] = "Thank you for your submission.";
			
		}catch(Exception $e){
			$return_msg = createReturnMsg("Error: ".$e->getMessage());
			return $return_msg;
		}

		try
		{
			$subject = "Contact Us Submission: ".date("m/d/Y");

			$mail = new DMEmailer($subject);
			// $mail->setDebug(true);
			$mail->addAddress(Config::get('emailAddresses/default/email'));
			// $mail->addAddress('erik@digitalmedia.ca');
			// $mail->addAddress('erik.gurney@hotmail.com');
			$mail->setFrom(Config::get('emailAddresses/default/email'), Config::get('emailAddresses/default/name'));

			// Email styles
			$mail->setFontSize("12px");
			$mail->setFontFamily("Arial, sans-serif");
			
			// Header
			$mail->headerColor("#FFF");
			$mail->headerBackground("#204f5f");
			$mail->addHeaderStyles(array(
				"padding"     => "18px",
				"font-weight" => "bold"
			));
			$mail->addHeader($subject);

			// Body
			$mail->addInfoElement('Name:', $name);
			$mail->addInfoElement('Email:', $email);
			$mail->addInfoElement('Comments:', $comments);

			// Footer
			// $mail->addFooter('<a href="" style="color:#FFF;text-decoration:none;">4400 Wyandotte St East</a><br><a href="" style="color:#FFF;text-decoration:none;">Windsor Ontario, N8Y 3B9</a><br><a href="" style="color:#FFF;text-decoration:none;">519-817-1176</a>');

			// Send email
			$mail->sendEmail();

		}catch(Exception $e){
			$return_msg = createReturnMsg("Error: ".$e->getMessage());
			return $return_msg;
		}

		return $return_msg;
	}

	function createReturnMsg($msg){
		$return_msg['nonce'] = Token::generate("contact-request");
		$return_msg['error'] = $msg;
		return $return_msg;
	}
	
?>