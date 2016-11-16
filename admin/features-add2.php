<?php
	
	$base_url   = "../";
	$page_title = "Admin Area - Add Testimonials";

	include_once($base_url.'includes/init.php');

	$admin_user = new AdminUser();
	if(!$admin_user->isLoggedIn())
		header("Location: ".Config::get('admin_url')."login");
	$admin_user->setActivePageGrp("testimonial");

	$permalink = "";
	$errors    = array();

	// =================================================================
	//	IF GALLERY IS BEING CREATED
	// =================================================================
	if(Input::exists())
	{
		if(Input::hasValue('submit'))
			$admin_user->call("admin/db-features", "features_add");

		// if($admin_user->successfullyCalled())
		// 	header("Location: ".Config::get('admin_url')."features-edit?item=".md5(Session::get(Config::get("table_prefix").'news-add-success')));
	}

	$token = Token::generate("admin_login_token");

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo Config::get('site_title'); if(!empty($page_title)) echo ' - '.$page_title; ?></title>

<?php include($base_url.'includes/global-includes.php'); ?>

<script type="text/javascript" src="<?php echo Config::get('absolute_url').Config::get('ckeditor_version'); ?>/ckeditor.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo Config::get('absolute_url').Config::get('jquery_ui_location_css'); ?>" />
<script type="text/javascript" src="<?php echo Config::get('absolute_url').Config::get('jquery_ui_location_js'); ?>"></script>
</head>

<body class="single-page">
	
	<?php include($base_url.'includes/sections/header.php'); ?>
	
	<div class="row-expand">
		<div class="content-wrapper">
			<div class="page-section">
				<div class="col col-80 content-col">
					<h3><?php echo $page_title; ?></h3>

					<?php if($admin_user->hasError('general')) echo '<div class="error">'.$admin_user->getError('general').'</div>'; ?>

					<form method="post" enctype="multipart/form-data">
						
						<input type="hidden" name="permalink-script" id="permalink-script" value="permalink-check-news">
						<input type="hidden" name="token" id="token" value="<?php echo $token; ?>">

						<div class="form-field<?php if($admin_user->hasError("title")) echo ' error_container'; ?>">
							<label for="title">Title:</label>
							<?php if($admin_user->hasError('title')) echo '<div class="error">'.$admin_user->getError('title').'</div>'; ?>
							<input type="text" name="title" id="title" value="<?php echo Input::get('title'); ?>" placeholder="Post title" />
						</div>

						<div class="form-field<?php if($admin_user->hasError("permalink")) echo ' error_container'; ?>">
							<label for="permalink">Permalink (don't change unless you have to):</label>
							<?php if($admin_user->hasError('permalinks')) echo '<div class="error">'.$admin_user->getError('permalinks').'</div>'; ?>
							<input tabindex="-1" type="text" name="permalink" id="permalink" value="<?php echo Input::get('permalink'); ?>" class="permalink_input" />
							<div class="permalink_preloader"><img src="<?php echo Config::get('absolute_url'); ?>images/ajax-16x16.gif" alt="Loading..."></div>
							<div id="permalink_error"></div>
						</div>

						<div class="form-field<?php if($admin_user->hasError("content")) echo ' error_container'; ?>">
							<label>Content:</label>
							<?php if($admin_user->hasError("content")) echo '<div class="error">'.$admin_user->getError("content").'</div>'; ?>
							<textarea id="content" name="content"><?php echo Input::get('content'); ?></textarea>
							<script type="text/javascript">
								var editor_1 = CKEDITOR.replace('content', {
									toolbar: 'Content'
								});
							</script>
						</div>

						<div class="form-field<?php if(array_key_exists ("img", $errors)) echo ' error_container'; ?>">
							<label>Image:</label>
							<?php if($admin_user->hasError("img")) echo '<div class="error">'.$admin_user->getError("img").'</div>'; ?>
							<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
							<input type="file" name="img" id="img"  size="50" />
						</div>

						<div class="form-field date-field<?php if(array_key_exists ("date_posted", $errors)) echo ' error_container'; ?>">
							<label for="permalink">Date Posted:</label>
							<?php if($admin_user->hasError('date_posted')) echo '<div class="error">'.$admin_user->getError('date_posted').'</div>'; ?>
							<input type="text" class="date" id="date_posted" name="date_posted" value="<?php echo Input::get('date_posted'); ?>" />
						</div>

						<div class="btn_container">
							<button class="btn" name="submit" value="submit">Add Feature</button>
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