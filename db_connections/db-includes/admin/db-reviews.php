<?php

// =======================================================================================================================================================
//		CANDY TYPES FUNCTIONS
// =======================================================================================================================================================
function reviews_add()
{
	$base_url   = "../";
	$connection = DBConn::getInstance();
	Input::exists();
		
	$id                    = Input::get('id');
	$reviewer              = trim(Input::get('reviewer'));
	$designation           = trim(Input::get('designation'));
	$content               = trim(Input::get('content'));
	$token                 = Input::get('token');
	$errors                = array();

	// Check nonce token
	if(!Token::check($token, "admin_login_token"))
	{
		$errors['general'] = "Cannot verify you.";
		return $errors;
	}
	
	// ==============================
	//	ERROR CHECK
	// ==============================
	if(empty($id))
		$id = NULL;
		
	try
	{
		// ===========================================
		//	INSERT ITEM INTO DB
		// ===========================================
		$statement = "	INSERT INTO ".Config::get('table_prefix')."reviews
						(id, reviewer, designation, content)
						VALUES (:id, :reviewer, :designation, :content)
						ON DUPLICATE KEY UPDATE
							reviewer     = :reviewer_update,
							designation  = :designation_update,
							content      = :content_update;";
		$query = $connection->conn->prepare($statement);
		$query->execute(array(
			':id'                 => $id,
			':reviewer'           => $reviewer,
			':designation'        => $designation,
			':content'            => $content,
			':reviewer_update'    => $reviewer,
			':designation_update' => $designation,
			':content_update'     => $content
		));
	}catch(Exception $e){
		$errors['general'] = "Error updating database: ".$e->getMessage();
		return $errors;
	}
}

function reviews_delete()
{
	$base_url   = "../";
	$connection = DBConn::getInstance();
	Input::exists();
	$errors = array();
	
	if(Input::hasValue('id'))
		$id = Input::get('id');
	else
	{
		$errors['general'] = "Error: Unable to determine the review you are trying to delete.";
		return $errors;
	}
	
		try
		{
			// =======================================================
			//	DELETE MAIN POST
			// =======================================================
			$statement = "DELETE FROM ".Config::get('table_prefix')."reviews WHERE id = :id;";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(
				':id' => $id
			));
		}catch(Exception $e){
			$errors['general'] = "Error deleting post:<br>".$e->getMessage();
			return $errors;
		}
		
		try
		{
			$statement = "OPTIMIZE TABLE ".Config::get('table_prefix')."reviews;";
			$query = $connection->conn->prepare($statement);
			$query->execute();
		}catch(Exception $e){
			$errors['general'] = "Error optimizing system:<br>".$e->getMessage();
			return $errors;
		}
		
	// success!!
	return $errors;
}

?>