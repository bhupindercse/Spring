<?php
	
	$base_url   = "../";
	$page_title = "Admin Area - Add Reviews";

	include_once($base_url.'includes/init.php');

	$admin_user = new AdminUser();
	if(!$admin_user->isLoggedIn())
		header("Location: ".Config::get('admin_url')."login");
	$admin_user->setActivePageGrp("reviews");

	$permalink = "";
	$errors    = array();
	$success_add = false;

	// =================================================================
	//	IF GALLERY IS BEING CREATED
	// =================================================================
	if(Input::exists())
	{
		if(Input::hasValue('submit'))
			$admin_user->call("admin/db-reviews", "reviews_add");

		if($admin_user->successfullyCalled())
			$success_add = true;
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

</head>

<body class="single-page">
	
	<?php include($base_url.'includes/sections/header.php'); ?>
	
	<div class="row-expand">
		<div class="content-wrapper">
			<div class="page-section">
				<div class="col col-80 content-col">
					<h3><?php echo $page_title; ?></h3>

					<?php if($admin_user->hasError('general')) echo '<div class="error">'.$admin_user->getError('general').'</div>'; ?>

					<?php if($success_add) echo '<div class="success">You have succesfully added a Reviewer...!!</div>'; ?>


					<form method="post" enctype="multipart/form-data">
						
						<input type="hidden" name="permalink-script" id="permalink-script" value="permalink-check-types">
						<input type="hidden" name="token" id="token" value="<?php echo $token; ?>">

						<div class="form-field<?php if($admin_user->hasError("reviewer")) echo ' error_container'; ?>">
							<label for="reviewer">Reviewer Name:</label>
							<?php if($admin_user->hasError('reviewer')) echo '<div class="error">'.$admin_user->getError('reviewer').'</div>'; ?>
							<input type="text" name="reviewer" id="reviewer" value="<?php echo Input::get('reviewer'); ?>" placeholder="Post reviewer" />
						</div>

						<div class="form-field<?php if($admin_user->hasError("designation")) echo ' error_container'; ?>">
							<label for="designation">Reviewer's Designation:</label>
							<?php if($admin_user->hasError('designations')) echo '<div class="error">'.$admin_user->getError('designations').'</div>'; ?>
							<input type="text" name="designation" id="designation" value="<?php echo Input::get('designation'); ?>" class="designation_input" />
						</div>

						<div class="form-field<?php if($admin_user->hasError("content")) echo ' error_container'; ?>">
							<label>Review Content:</label>
							<?php if($admin_user->hasError("content")) echo '<div class="error">'.$admin_user->getError("content").'</div>'; ?>
							<textarea id="content" name="content"><?php echo Input::get('content'); ?></textarea>
							<script type="text/javascript">
								var editor_1 = CKEDITOR.replace('content', {
									toolbar: 'Basic'
								});
							</script>
						</div>

						<div class="btn_container">
							<button class="btn" name="submit" value="submit">Add Reviews</button>
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
	
</body>
</html>