<?php
	
	$base_url   = "../";
	$page_title = "Admin Area - Add Gallery";

	include_once($base_url.'includes/init.php');

	$admin_user = new AdminUser();
	if(!$admin_user->isLoggedIn())
		header("Location: ".Config::get('admin_url')."login");
	$admin_user->setActivePageGrp("gallery");

	$permalink = "";
	$errors    = array();
	$success   = false;
	
	// =================================================================
	//	IF GALLERY IS BEING CREATED
	// =================================================================
	if(Input::exists())
	{
		if(Input::hasValue('submit'))
			$admin_user->call("admin/db-gallery", "gallery_add");

		if($admin_user->successfullyCalled())
			header("Location: ".Config::get('admin_url')."gallery-edit?item=".md5(Session::get(Config::get("table_prefix").'gallery-add-success')));
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
				<div class="col col-80 content-col">
					<h3><?php echo $page_title; ?></h3>

					<?php if($admin_user->hasError('general')) echo '<div class="error">'.$admin_user->getError('general').'</div>'; ?>

					<form method="post">
						<input type="hidden" name="absolute_url" id="absolute_url" value="<?php echo Config::get('absolute_url') ?>">
						<input type="hidden" name="permalink-script" id="permalink-script" value="permalink-check-gallery">
						<input type="hidden" name="token" id="token" value="<?php echo $token; ?>">

						<div class="form-field<?php if($admin_user->hasError("title")) echo ' error_container'; ?>">
							<label for="title">Title:</label>
							<?php if($admin_user->hasError('title')) echo '<div class="error">'.$admin_user->getError('title').'</div>'; ?>
							<input type="text" name="title" id="title" value="<?php echo Input::get('title'); ?>" placeholder="Gallery title" />
						</div>

						<div class="form-field<?php if($admin_user->hasError("permalink")) echo ' error_container'; ?>">
							<label for="permalink">Permalink (don't change unless you have to):</label>
							<?php if($admin_user->hasError('permalinks')) echo '<div class="error">'.$admin_user->getError('permalinks').'</div>'; ?>
							<input tabindex="-1" type="text" name="permalink" id="permalink" value="<?php echo $permalink; ?>" class="permalink_input" />
							<div class="permalink_preloader"><img src="<?php echo Config::get('absolute_url'); ?>images/ajax-16x16.gif" alt="Loading..."></div>
							<div id="permalink_error"></div>
						</div>

						<div class="btn_container">
							<button class="btn" name="submit" value="submit">Add Gallery</button>
						</div>
					</form>
				</div>
				<div class="col col-20">
					<?php include($base_url.'includes/sections/admin-nav.php'); ?>
				</div>
			</div>
		</div>
	</div>

	<?php include($base_url.'includes/sections/footer.php'); ?>

	<?php include($base_url.'includes/script-includes.php'); ?>
	<script src="<?php echo Config::get('absolute_url'); ?>scripts/DropNav-min.js"></script>
	<script src="<?php echo Config::get('absolute_url'); ?>scripts/PermalinkChecker-min.js"></script>
	
</body>
</html>