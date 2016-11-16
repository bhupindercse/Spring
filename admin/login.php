<?php
	
	$base_url   = "../";
	$page_title = "Admin Area - Login";

	include_once($base_url.'includes/init.php');

	$admin_user = new AdminUser();
	if($admin_user->isLoggedIn())
		header("Location: ".Config::get('admin_url'));

	// ===================================
	//  Verify login enabled
	// ===================================
	$admin_user->check_login_enabled();

	// ===================================
	//  Verify login enabled
	// ===================================
	if(Input::exists())
	{
		$admin_user->login();
		if($admin_user->isLoggedIn())
			header('Location: '.Config::get('admin_url'));

		// echo '<pre>';
		// $err = $admin_user->getAllErrors();
		// print_r($err);
		// echo '</pre>';
	}

	$token = Token::generate("admin_login_token");

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo Config::get('site_title'); if(!empty($page_title)) echo ' - '.$page_title; ?></title>

<?php include($base_url.'includes/global-includes.php'); ?>

</head>

<body class="single-page">
	
	<?php include($base_url.'includes/sections/header.php'); ?>
	
	<div class="row-expand">
		<div class="content-wrapper">
			<div class="page-section">
				<div class="content-col">
					<h3><?php echo $page_title; ?></h3>

					<?php

						if($errors = $admin_user->getAllErrors())
						{
							echo '<div class="error">';
							foreach($errors as $error)
								echo '<div>'.$error.'</div>';
							echo '</div>';
						}

					?>

					<form class="centered-form" name="login" id="frm" method="post">
						<div class="form-field no-label-field<?php if($admin_user->hasError("email")) echo ' error_container'; ?>">
							<label for="email">Email:</label>
							<input type="text" name="email" id="email" value="<?php echo Input::get('email'); ?>" placeholder="Email" />
						</div>

						<div class="form-field no-label-field<?php if($admin_user->hasError("password")) echo ' error_container'; ?>">
							<label for="password">Password:</label>
							<input type="password" name="password" id="password" value="" placeholder="" />
						</div>

						<input type="hidden" name="token" value="<?php echo $token; ?>">

						<button class="btn" name="submit" value="submit">Log In</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php include($base_url.'includes/sections/footer.php'); ?>

	<?php include($base_url.'includes/script-includes.php'); ?>
	<script src="<?php echo Config::get('absolute_url'); ?>scripts/DropNav-min.js"></script>
	
</body>
</html>