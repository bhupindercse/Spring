<?php

// =========================================================================================================
//	Used to declare 'global' variables used across the website
// =========================================================================================================
class Config
{
	private static $_instance = null;

	// =========================================================
	//	GENERAL
	// =========================================================
	private static $site_title             = "Main Spring";
	private static $site_url               = "http://mainspring.ca/";
	private static $absolute_url           = "";
	private static $dev_url                = "http://digitalmedia.ca/11_projects/16-MainSpring/";
	private static $admin_url              = "";
	private static $table_prefix           = "ms_";
	private static $default_error          = "This page is being updated.<br>Check back for info soon!";
	private static $timezone               = "America/Detroit";
	private static $ckeditor_version       = "ckeditor_4.5.3";
	private static $ckfinder_version       = "ckfinder_2.4";
	private static $jquery_ui_location_js  = "scripts/libs/jquery_ui_1.11.0.custom/jquery-ui.min.js";
	private static $jquery_ui_location_css = "scripts/libs/jquery_ui_1.11.0.custom/jquery-ui.min.css";

	// ===================================
	//	DB Connection
	// ===================================
	// These credentials are used for client side
	private static $dbConnection = array(
		// 'host'     => "localhost",
		// 'db'       => "advantage_transportation",
		// 'username' => "advantage_user",
		// 'password' => "vgr4%4lGSu)"
	);
	

	private static $database_scripts = "db_connections/db-includes/";
	private static $emailAddresses = array(
		"default" => array(
			"name"  => "Main Spring",
			"email" => "info@mainspring.ca"
		)
	);

	// ===================================================================================
	//	REGEX PATTERNS
	// ===================================================================================
	private static $email_pattern   = "/^(?:[a-zà-üA-ZÀ-Ü0-9_-]+\.?)+@(?:(?:[a-zà-üA-ZÀ-Ü0-9_-]\-?)+\.)+\w{2,4}$/";
	private static $phone_pattern   = "/(\d)?(\s|-)?(\()?(\d){3}(\))?(\s|-){1}(\d){3}(\s|-){1}(\d){4}/";
	private static $time_pattern    = "/^[0-2]?[0-9](:[0-5][0-9])?[\s]?(am|pm|AM|PM)?$/";
	private static $postal_patterns = array();
	private static $price_pattern   = '/^(?:[1-9]\d+|\d)(?:\.\d\d)?$/';

	// ===================================================================================
	//	MISC
	// ===================================================================================
	private static $enc_algorythm        = "sha256";
	private static $admin_session_name   = "";
	private static $token                = "token";
	private static $max_login_tries      = 5;
	private static $permalink_max_length = 255;

	// ===================================================================================
	//	TOKENS
	// ===================================================================================
	private static $admin_login = "admin_token";
	private static $admin_user  = "admin_user";

	// ===================================================================================
	//	GALLERY
	// ===================================================================================
	private static $gallery_url          = 'gallery/';
	private static $gallery_img_width    = 800;
	private static $gallery_thumb_width  = 197;
	private static $gallery_thumb_height = 198;
	private static $gallery_image_text   = true;
	private static $gallery_ckeditor     = false;
	private static $gallery_files        = "images/_galleries/";
	private static $gallery_file_chunks  = "images/_galleries/__chunks/";
	private static $gallery_file_types   = array(
		".jpeg" => "image/jpeg",
		".jpg"  => "image/jpeg",
		".gif"  => "image/gif",
		".png"  => "image/png"
	);

	// ===================================================================================
	//	NEWS
	// ===================================================================================
	private static $news_url          = "testimonials/";
	private static $news_images       = "images/_news/";
	private static $news_img_width    = 508;
	private static $news_rss_filename = "rss/news.xml";

	// ===================================================================================
	//	CANDY TYPES
	// ===================================================================================
	private static $type_images     = "images/_types/";
	private static $features_images     = "images/_features_images/";
	private static $types_img_width = 306;

	// ===================================================================================
	//	IMAGES URL
	// ===================================================================================
	private static $images_url     = "images/";
	private static $about_images     = "images/_about/";


	// ===================================================================================
	//	SOCIAL
	// ===================================================================================
	private static $social = array(
		'facebook' => array(
			'url' => 'https://www.facebook.com/'
		),
		'youtube' => array(
			'url' => 'https://www.youtube.com/'
		),
		'twitter' => array(
			'url' => 'https://www.twitter.com/'
		)
	);

	private function __construct()
	{
		// ===================================
		//	Check server enviroment (prod or dev)
		// ===================================
		self::checkServerEnvironment();

		// ===================================
		//	Set postal patterns
		// ===================================
		self::$postal_patterns['CA'] = "/^[ABCEGHJ-NPRSTVXY]{1}[0-9]{1}[ABCEGHJ-NPRSTV-Z]{1}[ ]?[0-9]{1}[ABCEGHJ-NPRSTV-Z]{1}[0-9]{1}$/";
		self::$postal_patterns['US'] = "/^([0-9]{5})(-[0-9]{4})?$/i";

		// ===================================
		//	Set some absolute paths
		// ===================================
		self::$absolute_url = self::$site_url;
		self::$admin_url    = self::$absolute_url."admin/";

		self::$admin_session_name = self::$table_prefix."-admin";

		self::$admin_login = self::$table_prefix.self::$admin_login;
		self::$admin_user  = self::$table_prefix.self::$admin_user;
	}

	// These credentials are used for local side
	private static function checkServerEnvironment()
	{
		if(strpos($_SERVER['HTTP_HOST'], "digitalmedia.ca") !== FALSE)
		{
			self::$site_url = self::$dev_url;
			
			self::$dbConnection = array(
				'host'     => "70.32.75.89", //"216.70.112.96",
				'db'       => "dmedia_corey",
				'username' => "dmedia_corey",
				'password' => "t35ting"
				// 'host'     => "advantagetransportation.ca",
				// 'db'       => "advantage_transportation",
				// 'username' => "advantage_user",
				// 'password' => "vgr4%4lGSu)"
			);
		}
		// }else{
			
		// 	self::$site_url = "advantagetransportation.ca";
		// 	self::$dbConnection = array(
		// 		'host'     => "localhost_adv",
		// 		'db'       => "advantage_transportation",
		// 		'username' => "advantage_user",
		// 		'password' => "vgr4%4lGSu)"
		// 	);
		// }
	}

	public static function get($path){

		if(!isset(self::$_instance)){
			self::$_instance = new Config();
		}

		if($path == null)
			die("Invalid property");

		$path = explode("/", $path);

		if(!property_exists(get_class(), $path[0]))
			die("Invalid property: ".$path[0]);

		$vars = get_class_vars(get_class());
		$settings = $vars[$path[0]];

		for($i = 1; $i < count($path); $i++){
			if(isset($settings, $path[$i]))
				$settings = $settings[$path[$i]];
		}

		return $settings;
	}
}

?>