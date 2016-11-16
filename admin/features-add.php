<?php
	
	$base_url   = "../";
	$page_title = "Admin Area - Edit Reviews";

	include_once($base_url.'includes/init.php');

	$admin_user = new AdminUser();
	if(!$admin_user->isLoggedIn())
		header("Location: ".Config::get('admin_url')."login");
	$admin_user->setActivePageGrp("features");

	$errors         = array();
	$success_edit   = false;
	$success_delete = false;
	$found          = false;

	Input::exists();
	$id = Input::get('id', 'get');

	// ===================================
	//	Form Submission
	// ===================================
	if(Input::exists())
	{
		if(Input::hasValue('submit'))
			$admin_user->call("admin/db-features", "features_add");
	
		if($admin_user->successfullyCalled('features_add'))
		{
			$success_edit = true;
			Input::clearType('post');
		}
	}

	$connection = DBConn::getInstance();

	// ===================================
	//	Get existing record
	// ===================================
	
	$token = Token::generate("admin_login_token");

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo Config::get('site_title'); if(!empty($page_title)) echo ' - '.$page_title; ?></title>

<?php include($base_url.'includes/global-includes.php'); ?>

<?php if($found){ ?>
<script type="text/javascript" src="<?php echo Config::get('absolute_url').Config::get('ckeditor_version'); ?>/ckeditor.js"></script>
<?php } ?>

</head>

<body class="single-page">
	
	<?php include($base_url.'includes/sections/header.php'); ?>
	
	<div class="row-expand">
		<div class="content-wrapper">
			<div class="page-section">
				<div class="col col-80 content-col">
					<h3><?php echo $page_title; ?></h3>
					
					<?php if($admin_user->hasError('general')) echo '<div class="error">'.$admin_user->getError('general').'</div>'; ?>
					<?php if($success_edit) echo '<div class="success">You have succesfully edited Feature!</div>'; ?>
					<?php if($success_delete) echo '<div class="success">You have succesfully deleted Feature!</div>'; ?>
					<?php
						if(Session::hasValue(Config::get("table_prefix").'features-add-success'))
						{
							echo '<div class="section-content">';
							echo 	'<div class="success">You have succesfully added a Feature!<br><a href="'.Config::get('admin_url').'features-add">Click here to add another.</a></div>';
							echo '</div>';

							Session::clear(Config::get("table_prefix").'features-add-success');
						}
					?>
					<form method="post" enctype="multipart/form-data">
						
						<input type="hidden" name="permalink-script" id="permalink-script" value="permalink-check-features">
						<input type="hidden" name="token" id="token" value="<?php echo $token; ?>">
						<input type="hidden" name="id" id="id" value="<?php echo Input::get('id'); ?>">

						<div class="form-field<?php if($admin_user->hasError("title")) echo ' error_container'; ?>">
							<label for="title">Title</label>
							<?php if($admin_user->hasError('title')) echo '<div class="error">'.$admin_user->getError('title').'</div>'; ?>
							<input type="text" name="title" id="title" value="<?php echo Input::get('title'); ?>" placeholder="Post title" />
						</div>

						<div class="form-field<?php if($admin_user->hasError("readmore")) echo ' error_container'; ?>">
							<label for="readmore">ReadMore Title</label>
							<?php if($admin_user->hasError('readmore')) echo '<div class="error">'.$admin_user->getError('readmore').'</div>'; ?>
							<input tabindex="-1" type="text" name="readmore" id="readmore" value="<?php echo Input::get('readmore'); ?>" />
						</div>

						<div class="form-field<?php if($admin_user->hasError("readmore")) echo ' error_container'; ?>">
							<label for="readmore">ReadMore Link:</label>
							<?php if($admin_user->hasError('link_url')) echo '<div class="error">'.$admin_user->getError('link_url').'</div>'; ?>
							<input tabindex="-1" type="text" name="link_url" id="readmore" value="<?php echo Input::get('link_url'); ?>" />
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

						<div class="form-field<?php if(array_key_exists ("img", $errors)) echo ' error_container'; ?>">
							<label>Image:</label>
							<?php if($admin_user->hasError("img")) echo '<div class="error">'.$admin_user->getError("img").'</div>'; ?>
							<div class="img-preview">  
								<?php
									if(!Input::hasValue('img'))
										echo '<div class="image_name">No Image for this item.</div>';
									else
									{
										$file = explode("/", Input::get("img"));
										echo '<div class="image_name">Post Image: '.end($file).'</div>';
										echo '<img src="'.Config::get('absolute_url').'images/_features_images/'.Input::get("img").'" alt="Item Image">';
										echo '<label class="field_title" for="delete_pic"><input type="checkbox" name="delete_pic" id="delete_pic" value="1" /> - Delete image</label>';
									}
								?>
							</div>
							<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
							<input type="file" name="img" id="img"  size="50" />
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
	
	<?php if($found){ ?>
	<script src="<?php echo Config::get('absolute_url'); ?>scripts/PermalinkChecker-min.js"></script>
	<?php }else{ ?>
	<script src="<?php echo Config::get('absolute_url'); ?>scripts/searches/TypesSearch-min.js"></script>
	<?php } ?>
	
</body>
</html>