<?php
	
	$base_url   = "../";
	$page_title = "Admin Area";

	include_once($base_url.'includes/init.php');

	$admin_user = new AdminUser();
	if(!$admin_user->isLoggedIn())
		header("Location: ".Config::get('admin_url')."login");
	$admin_user->setActivePageGrp("");

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

					Welcome to the <strong><?php echo Config::get('site_title'); ?></strong> Admin Area!<br>
					Use the menu on the left to choose what you'd like to do!
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