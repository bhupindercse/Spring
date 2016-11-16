<?php
	
	$base_url   = "../";
	$page_title = "Admin Area - Edit Reviews";

	include_once($base_url.'includes/init.php');

	$admin_user = new AdminUser();
	if(!$admin_user->isLoggedIn())
		header("Location: ".Config::get('admin_url')."login");
	$admin_user->setActivePageGrp("reviews");

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
		if(Input::hasValue('submit-edit'))
			$admin_user->call("admin/db-reviews", "reviews_add");
		if(Input::hasValue('submit-delete-confirm'))
			$admin_user->call("admin/db-reviews", "reviews_delete");

		if($admin_user->successfullyCalled('reviews_add'))
		{
			$success_edit = true;
			Input::clearType('post');
		}
		if($admin_user->successfullyCalled('reviews_delete'))
			$success_delete = true;
	}

	$connection = DBConn::getInstance();

	// ===================================
	//	Get existing record
	// ===================================
	if(Input::hasValue('item', 'get') && !$success_delete)
	{
		try
		{
			$statement = "	SELECT 	".config::get('table_prefix')."reviews.*
							FROM ".config::get('table_prefix')."reviews
							WHERE md5(".config::get('table_prefix')."reviews.id) = :id
							LIMIT 1;";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(':id' => Input::get('item', 'get')));
			if(!$query->rowCount())
				$admin_user->setError('general', "Unable to find the item you wanted to edit.");
			else
			{
				$data = $query->fetch(PDO::FETCH_ASSOC);

				Input::set('id', $data['id']);
				Input::set('reviewer', $data['reviewer']);
				Input::set('permalink', $data['permalink']);
				Input::set('content', $data['content']);
				Input::set('designation', $data['designation']);
				// Input::set('id', $data['id']);
				// Input::set('title', $data['title']);
				// Input::set('permalink', $data['permalink']);
				// Input::set('content', $data['content']);
				// Input::set('img', $data['filename']);

				$found = true;
			}
		}catch(Exception $e){
			$admin_user->setError('general', "Error getting item info:<br>".$e->getMessage());
		}
	}

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
					<?php if($success_edit) echo '<div class="success">You have succesfully edited a Review!</div>'; ?>
					<?php if($success_delete) echo '<div class="success">You have succesfully deleted a Review!</div>'; ?>
					<?php
						if(Session::hasValue(Config::get("table_prefix").'reviews-add-success'))
						{
							echo '<div class="section-content">';
							echo 	'<div class="success">You have succesfully added a Review!<br><a href="'.Config::get('admin_url').'reviews-add">Click here to add another.</a></div>';
							echo '</div>';

							Session::clear(Config::get("table_prefix").'reviews-add-success');
						}
					?>

					<?php if(Input::hasValue('submit-delete')){ ?>
					
					<div class="section">
						<h3>Confirm Deletion</h3>
						<div class="section-content">
							<form name="frm" id="frm" method="post">
								Are you sure you want to delete <strong><?php echo Input::get('reviewer'); ?></strong>?
								
								<input type="hidden" name="token" id="token" value="<?php echo $token; ?>">
								<input type="hidden" name="id" id="id" value="<?php echo Input::get('id'); ?>">

								<div class="btn_container">
									<button class="btn" name="delete-deny" value="delete-deny">No</button>
									<button class="btn delete-btn" name="submit-delete-confirm" value="submit-delete-confirm">Yes, Delete</button>
								</div>
							</form>
						</div>
					</div>

					<?php }elseif($found){ ?>

					<form method="post" enctype="multipart/form-data">
						
						<input type="hidden" name="permalink-script" id="permalink-script" value="permalink-check-reviews">
						<input type="hidden" name="token" id="token" value="<?php echo $token; ?>">
						<input type="hidden" name="id" id="id" value="<?php echo Input::get('id'); ?>">

						<div class="form-field<?php if($admin_user->hasError("reviewer")) echo ' error_container'; ?>">
							<label for="reviewer">Reviewer:</label>
							<?php if($admin_user->hasError('reviewer')) echo '<div class="error">'.$admin_user->getError('reviewer').'</div>'; ?>
							<input type="text" name="reviewer" id="reviewer" value="<?php echo Input::get('reviewer'); ?>" placeholder="Post reviewer" />
						</div>

						<div class="form-field<?php if($admin_user->hasError("designation")) echo ' error_container'; ?>">
							<label for="designation">Reviewer's Designation:</label>
							<?php if($admin_user->hasError('designations')) echo '<div class="error">'.$admin_user->getError('designations').'</div>'; ?>
							<input tabindex="-1" type="text" name="designation" id="designation" value="<?php echo Input::get('designation'); ?>" />
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
							<button class="btn" name="submit-edit" value="submit">Update</button>
							<button class="btn" name="submit-delete" value="submit">Delete</button>
						</div>
					</form>
					<?php }else{ ?>

					<div class="listing padded-listing">
						<div class="ajax-msg"></div>
						<div class="input-wrapper">
							<input type="text" name="search" id="search" placeholder="Search Reviews">
							<div class="search-btn icon-search" id="submit-search"></div>
						</div>
					</div>

					<div class="search-loader">
						<?php include($base_url.'includes/sections/ajax-loader.php'); ?>
					</div>
					<div class="search-listing">
					<?php
						
						include($base_url.'includes/searches/reviews.php');
						
						echo performSearch(array(
							"q"          => Input::get('q'),
							"pg"         => Input::get('pg', "get"),
							"order_by"   => Input::get('order_by'),
							"sort_order" => Input::get('sort_order')
						));

					?>
					</div>
				<?php } ?>
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