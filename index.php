<?php
	
	$base_url   = "";
	$page_title = "";

	include_once($base_url.'includes/init.php');

	Input::exists();
	$id = Input::get('page', 'get');

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo Config::get('site_title'); if(!empty($page_title)) echo ' - '.$page_title; ?></title>
<?php include($base_url.'includes/global-includes.php'); ?>
</head>

<body>
	<input type="hidden" name="initial_id" value="<?php echo $id; ?>">
	
	<?php include($base_url.'includes/sections/header.php'); ?>
	
	<div class="row-expand">
		<?php include($base_url.'includes/sections/home.php'); ?>
		<?php include($base_url.'includes/sections/features_progress.php'); ?>
		<?php include($base_url.'includes/sections/pricing.php'); ?>
		<?php include($base_url.'includes/sections/about.php'); ?>
		<?php include($base_url.'includes/sections/reviews.php'); ?> 
		<?php include($base_url.'includes/sections/signup.php'); ?>		
		<!--    <?php //include($base_url.'- documents/utility_createPasswords.php'); ?>    -->
	</div>
	
	<?php include($base_url.'includes/sections/footer.php'); ?>
	<?php include($base_url.'includes/script-includes.php'); ?>
	
	<script src="scripts/libs/animatescroll/animatescroll.min.js"></script>
	<script src="scripts/libs/history.js/bundled/html4+html5/jquery.history.js"></script>
	<script src="scripts/Nav.js"></script>
	
</body>
</html>