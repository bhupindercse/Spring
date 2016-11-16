<div class="side-nav">
	<div class="nav-title">Admin Nav</div>
	<div class="link-wrapper">

		<div class="nav-header<?php if($admin_user->getActivePageGrp() == "reviews") echo ' nav-header-active'; ?>">Reviews</div>
		<div class="nav-group<?php if($admin_user->getActivePageGrp() == "reviews") echo ' nav-group-active'; ?>">
			<a href="<?php echo Config::get('admin_url'); ?>reviews-add">Add</a>
			<a href="<?php echo Config::get('admin_url'); ?>reviews-edit">Edit</a>
		</div>

		<div class="nav-header<?php if($admin_user->getActivePageGrp() == "features") echo ' nav-header-active'; ?>">Features</div>
		<div class="nav-group<?php if($admin_user->getActivePageGrp() == "features") echo ' nav-group-active'; ?>">
			<a href="<?php echo Config::get('admin_url'); ?>features-add">Add</a>
			<a href="<?php echo Config::get('admin_url'); ?>features-edit">Edit</a>
		</div>

		<div class="nav-header<?php if($admin_user->getActivePageGrp() == "about") echo ' nav-header-active'; ?>">About</div>
		<div class="nav-group<?php if($admin_user->getActivePageGrp() == "about") echo ' nav-group-active'; ?>">
			<a href="<?php echo Config::get('admin_url'); ?>about-add">Add</a>
			<a href="<?php echo Config::get('admin_url'); ?>about-edit">Edit</a>
		</div>

		<!-- 
		<div class="nav-header<?php //if($admin_user->getActivePageGrp() == "gallery") echo ' nav-header-active'; ?>">Gallery</div>
		<div class="nav-group<?php //if($admin_user->getActivePageGrp() == "gallery") echo ' nav-group-active'; ?>">
			<a href="<?php //echo Config::get('admin_url'); ?>gallery-add">Add</a>
			<a href="<?php //echo Config::get('admin_url'); ?>gallery-edit">Edit</a>
		</div>

		<div class="nav-header<?php //if($admin_user->getActivePageGrp() == "about") echo ' nav-header-active'; ?>">About</div>
		<div class="nav-group<?php //if($admin_user->getActivePageGrp() == "about") echo ' nav-group-active'; ?>">
			<a href="<?php //echo Config::get('admin_url'); ?>aboutinfo-edit">Edit</a>
		</div> -->

		<a href="<?php echo Config::get('admin_url'); ?>logout">Logout</a>
	</div>
</div>