<?php
	
	$base_url   = "../";
	$page_title = "Admin Area - Edit Gallery";

	include_once($base_url.'includes/init.php');

	$admin_user = new AdminUser();
	if(!$admin_user->isLoggedIn())
		header("Location: ".Config::get('admin_url')."login");
	$admin_user->setActivePageGrp("gallery");

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
			$admin_user->call("admin/db-gallery", "edit_gallery_attributes");
		if(Input::hasValue('submit-delete-confirm'))
			$admin_user->call("admin/db-gallery", "gallery_delete");

		if($admin_user->successfullyCalled('edit_gallery_attributes'))
		{
			$success_edit = true;
			Input::clearType('post');
		}
		if($admin_user->successfullyCalled('gallery_delete'))
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
			$statement = "	SELECT 	".config::get('table_prefix')."gallery_albums.*,
									".config::get('table_prefix')."gallery_permalinks.permalink
							FROM ".config::get('table_prefix')."gallery_albums
							LEFT JOIN ".config::get('table_prefix')."gallery_permalinks ON
								".config::get('table_prefix')."gallery_permalinks.gallery_id = ".config::get('table_prefix')."gallery_albums.id
								AND ".config::get('table_prefix')."gallery_permalinks.active = 1
							WHERE md5(".config::get('table_prefix')."gallery_albums.id) = :id
							LIMIT 1;";
			$query = $connection->conn->prepare($statement);
			$query->execute(array(':id' => Input::get('item', 'get')));
			if(!$query->rowCount())
				$admin_user->setError('general', "Unable to find the item you wanted to edit.");
			else
			{
				$data = $query->fetch(PDO::FETCH_ASSOC);

				Input::set('id', $data['id']);
				Input::set('gallery_permalink', $data['permalink']);
				Input::set('gallery_title', $data['title']);
				// Input::set('featured', $data['featured']);
				// Input::set('content', $data['content']);

				$found = true;
			}
		}catch(Exception $e){
			$admin_user->setError('general', "Error getting gallery info:<br>".$e->getMessage());
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
<?php if(Config::get('gallery_ckeditor') || Config::get('gallery_image_text')){ ?>
<script type="text/javascript" src="<?php echo Config::get("absolute_url").Config::get('ckeditor_version'); ?>/ckeditor.js"></script>
<?php } ?>
<link type="text/css" rel="stylesheet" href="<?php echo Config::get('absolute_url').Config::get('jquery_ui_location_css'); ?>" />
<script src="<?php echo Config::get('absolute_url').Config::get('jquery_ui_location_js'); ?>"></script>

<?php if(Config::get('gallery_image_text')){ ?>
<script type="text/javascript" src="<?php echo Config::get("absolute_url").Config::get('ckeditor_version'); ?>/adapters/jquery.js"></script>
<? } ?>
<link type="text/css" rel="stylesheet" href="<?php echo Config::get("absolute_url"); ?>css/fileuploader/basic.css">
<script type="text/javascript" src="<?php echo Config::get("absolute_url"); ?>includes/fileupload/scripts/modernizr.custom.58380.js"></script>
<script type="text/javascript">
$(function(){
	
	$('#date_of_project').datepicker();

	if(Modernizr.fileinput)
	{	
		Modernizr.load({
			test: 	(window.FormData !== undefined) && Modernizr.input.multiple,
			yep : 	{ 	
						'ajax':			'<?php echo Config::get("absolute_url"); ?>includes/fileupload/scripts/ajax-uploader/gallery/uploader-v3-min.js',
						'ajaxstyles':	'<?php echo Config::get("absolute_url"); ?>css/fileuploader/ajax-uploader.css'
					},
			nope: 	{},
			callback: function (url, result, key){
				if(result)
				{
					$("body").addClass("ajax-upload");
				}
				else
					$("#disclaimer").show();
			}
		});
	}
	else
		$("#disclaimer").show();

});
</script>
<?php } ?>

</head>

<body class="single-page">
	
	<?php include($base_url.'includes/sections/header.php'); ?>
	
	<div class="row-expand">
		<div class="content-wrapper">
			<div class="page-section">
				<div class="col col-80 content-col">
					<h3><?php echo $page_title; ?></h3>

					<?php if($success_delete) echo '<div class="success">You have succesfully deleted a photogallery!</div>'; ?>
					<?php
						if(Session::hasValue(Config::get("table_prefix").'gallery-add-success'))
						{
							echo '<div class="section-content">';
							echo 	'<div class="success">You have succesfully added a photogallery!<br><a href="'.Config::get('admin_url').'gallery-add">Click here to add another.</a></div>';
							echo '</div>';

							Session::clear(Config::get("table_prefix").'gallery-add-success');
						}
					?>

					<?php if(Input::hasValue('submit-delete')){ ?>
					
					<div class="section">
						<h3>Confirm Deletion</h3>
						<div class="section-content">
							<form name="frm" id="frm" method="post">
								Are you sure you want to delete <strong><?php echo Input::get('title'); ?></strong>?
								
								<input type="hidden" name="token" id="token" value="<?php echo $token; ?>">
								<input type="hidden" name="id" id="id" value="<?php echo Input::get('id'); ?>">
								<input type="hidden" id="gallery_permalink" name="gallery_permalink" value="<?php echo Input::get('gallery_permalink'); ?>" />

								<div class="btn_container">
									<button class="btn" name="delete-deny" value="delete-deny">No</button>
									<button class="btn delete-btn" name="submit-delete-confirm" value="submit-delete-confirm">Yes, Delete</button>
								</div>
							</form>
						</div>
					</div>

					<?php }elseif($found){ ?>

					<div class="section-errors" id="client-errors">
						<?php if($success_edit) echo '<div class="success">You have succesfully edited a photogallery!</div>'; ?>
						<?php if($admin_user->hasError('general')) echo '<div class="error">'.$admin_user->getError('general').'</div>'; ?>
						<?php 
							if($admin_user->hasError('deletions')){
								echo '<div class="error">';
								foreach($admin_user->getError('deletions') as $item){
									echo '<div>'.$item.'</div>';
								}
								echo '</div>';
							}
						?>
					</div>

					<form name="frm" id="frm" method="post">
						<input type="hidden" name="absolute_url" id="absolute_url" value="<?php echo Config::get('absolute_url') ?>">
						<input type="hidden" name="token" id="token" value="<?php echo $token; ?>">
						<input type="hidden" name="permalink-script" id="permalink-script" value="permalink-check-gallery">
						<input type="hidden" name="id" id="id" value="<?php echo Input::get('id'); ?>">
						<input type="hidden" id="gallery_permalink" name="gallery_permalink" value="<?php echo Input::get('gallery_permalink'); ?>" />
						<input type="hidden" id="gallery_id" name="gallery_id" value="<?php echo Input::get('id'); ?>" />
						<input type="hidden" id="gallery_image_text" name="gallery_image_text" value="<?php echo Config::get('gallery_image_text'); ?>" />

						<div class="form-field<?php if($admin_user->hasError("title")) echo ' error_container'; ?>">
							<label for="title">Title:</label>
							<?php if($admin_user->hasError('title')) echo '<div class="error">'.$admin_user->getError('gallery_title').'</div>'; ?>
							<input type="text" name="title" id="title" value="<?php echo Input::get('gallery_title'); ?>" placeholder="Gallery title" />
						</div>

						<div class="form-field<?php if($admin_user->hasError("permalink")) echo ' error_container'; ?>">
							<label for="permalink">Permalink (don't change unless you have to):</label>
							<?php if($admin_user->hasError('permalinks')) echo '<div class="error">'.$admin_user->getError('permalinks').'</div>'; ?>
							<input tabindex="-1" type="text" name="permalink" id="permalink" value="<?php echo Input::get('gallery_permalink'); ?>" class="permalink_input" />
							<div class="permalink_preloader"><img src="<?php echo Config::get('absolute_url'); ?>images/ajax-16x16.gif" alt="Loading..."></div>
							<div id="permalink_error"></div>
						</div>

						<?php if(Config::get('gallery_ckeditor')){ ?>
							<div class="form-field<?php if($admin_user->hasError("content")) echo ' error_container'; ?>">
								<label>Gallery Description:</label>
								<?php if($admin_user->hasError("content")) echo '<div class="error">'.$admin_user->getError("content").'</div>'; ?>
								<textarea id="content" name="content"><?php echo Input::get('content'); ?></textarea>
								<script type="text/javascript">
									var editor_1 = CKEDITOR.replace('content', {
										toolbar: 'NoStyles'
									});
								</script>
							</div>
						<?php } ?>
						
						<div id="disclaimer">
							We're sorry, you are using a browser that does not support any of the ways we have available to upload images to the photo gallery.<br />
							Please try back on a different device.
							
							<div class="examples">
								Examples of compatable devices/browsers:<br />
								<ul>
									<li>Firefox</li>
									<li>Safari on iOS6+</li>
									<li>Chrome</li>
									<li>IE8+</li>
								</ul>
							</div>
						</div>
						
						<div class="ajax-form gallery-form">
							
							<fieldset>
								<legend>New Images</legend>
								
								<div class="btn file-input-replacer">
									New Image
									<input type="file" id="file" name="file" multiple accept="image/*" onchange="Uploader.addFile()" />
								</div>
									<button id="upload" class="btn">Upload New Images</button>
							<!-- 	<button id="upload" class="btn" onclick="Uploader.prepRequest()">Upload New Images</button> -->
							
								<div id="general-errors"></div>
							</fieldset>
							
							<div class="file-list">
								<h1>File List</h1>
								<div class="main-progress progress">0%</div>
								
								<div id="files"></div>
								
								<div id="finished-files">
									<?php
										try
										{
											// ================================================
											//	GET CURRENT IMAGES
											// ================================================
											$statement = "	SELECT *
															FROM ".Config::get('table_prefix')."gallery_images
															WHERE gallery_id = :gallery_id
															ORDER BY date_added DESC;";
											$query = $connection->conn->prepare($statement);
											$query->execute(array(':gallery_id' => Input::get('id')));
										}catch(PDOException $e){
											echo '<div class="current_files_text">Error getting the current image list for this gallery:<br>'.$e->getMessage().'</div>';
										}
										
										if(!$query->rowCount())
											echo '<div class="current_files_text">There are no images currently used for this gallery.</div>';
										else
										{
											while($data = $query->fetch(PDO::FETCH_ASSOC))
											{
												echo '<div class="upload-elements" id="'.$data['id'].'">';
												echo	'<input type="hidden" name="image_id[]" value="'.$data['id'].'">';
												echo 	'<div class="img"><img src="'.Config::get('absolute_url').Config::get('gallery_files').Input::get('gallery_permalink').'/'.$data['filename'].'" alt="'.$data['filename'].'" /></div>';
												echo 	'<div class="upload-data">';
												if(Config::get('gallery_image_text'))
												{
													echo 		'<div class="upload-caption">';
													echo 			'<textarea class="ckeditor-textarea" name="caption_'.$data['id'].'">'.preg_replace('/<br(\s+)?\/?>/i', "", $data['caption']).'</textarea>';
													echo 		'</div>';
												}
												echo 		'<div class="upload-cover-image">';
												echo 			'<input type="radio" id="cover_image_'.$data['id'].'" name="cover_image" value="'.$data['id'].'"';
												if($data['cover_image']) echo ' checked="checked"';
												echo 			'>';
												echo 			'<label for="cover_image_'.$data['id'].'"> - Cover Image</label>';
												echo 		'</div>';
												echo		'<label for="delete_'.$data['id'].'" class="selectable_checkbox">';
												echo		'<input type="checkbox" name="delete[]" id="delete_'.$data['id'].'" value="'.$data['id'].'" />';
												echo		' - Delete image</label>';
												echo	'</div>';
												echo '</div>';
											}
										}
									?>
								</div>
								
							</div>
							<div class="btn_container">
								<input type="submit" name="submit-edit" class="btn" value="Update Gallery">
								<input type="submit" name="submit-delete" class="btn" value="Delete Gallery">
							</div>
						</div>
					</form>
					
					<?php

						$val = ini_get('upload_max_filesize');
						$last = strtolower($val[strlen($val)-1]);
						
						switch($last){
							// The 'G' modifier is available since PHP 5.1.0
							case 'g':
								$val *= 1024;
							case 'm':
								$val *= 1024;
							case 'k':
								$val *= 1024;
								break;
						}
					?>
					
					<input type="hidden" name="max-file-size" id="max-file-size" value="<?php echo $val; ?>">
					<?php }else{ ?>

					<div class="listing padded-listing">
						<div class="ajax-msg"></div>
						<div class="input-wrapper">
							<input type="text" name="search" id="search" placeholder="Search galleries">
							<div class="search-btn icon-search" id="submit-search"></div>
						</div>
					</div>

					<div class="search-loader">
						<?php include($base_url.'includes/sections/ajax-loader.php'); ?>
					</div>
					<div class="search-listing">
					<?php
						
						include($base_url.'includes/searches/gallery.php');
						
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
	<?php if(!$found){ ?>
	<script src="<?php echo Config::get('absolute_url'); ?>scripts/searches/GallerySearch-min.js"></script>
	<?php }else{ ?>
	<script src="<?php echo Config::get('absolute_url'); ?>scripts/PermalinkChecker-min.js"></script>
	<?php } ?>
	
</body>
</html>