<?php
	
	// ===================================
	//	Echo with PHP so there is no whitespace for inline-blocks
	// ===================================
	echo '<div class="header">';
	
	echo 			'<div class="mobile_menu_logo"><a href="'.Config::get('absolute_url').'"><img class="logo_image" src="'.Config::get('absolute_url').'images/logo.png"></a></div>';
	echo 			'<div class="mobile_menu"> <span style="padding-right:1em;" >MENU</span><span class="icon-menu"></span></div>';

 	echo 	'<div class="header_left_tabs_div">';

 
	echo 		'<ul class="nav">';
	// echo 	'<li class="main-link"><a class="link'; echo Input::get('page', 'get') == "home" ? ' active' : ''; echo '" href="'.Config::get('absolute_url').'" data-element="home"><img class="features_image_size" src="'.Config::get('absolute_url').'images/spring.png"></a></li>';

	echo 	'<li style="border:0px;"><a class="logo_image_a" href="'.Config::get('absolute_url').'"><img class="logo_image" src="'.Config::get('absolute_url').'images/spring.png"></a></li>';


	echo 			'<li class="main-link"><a class="link'; echo Input::get('page', 'get') == "home" ? ' active' : ''; echo '" href="'.Config::get('absolute_url').'" data-element="home">HOME</a></li>';
	
	echo 			'<li class="main-link"><a class="link'; echo Input::get('page', 'get') == "features" ? ' active' : ''; echo '" href="'.Config::get('absolute_url').'features" data-element="features">FEATURES</a></li>';
	
	echo 			'<li class="main-link"><a class="link'; echo Input::get('page', 'get') == "pricing" ? ' active' : ''; echo '" href="'.Config::get('absolute_url').'pricing" data-element="pricing">PRICING</a></li>';

	echo 			'<li class="main-link"><a class="link'; echo Input::get('page', 'get') == "about" ? ' active' : ''; echo '" href="'.Config::get('absolute_url').'about" data-element="about">ABOUT</a></li>';

	echo 			'<li class="main-link"><a class="link'; echo Input::get('page', 'get') == "review" ? ' active' : ''; echo '" href="'.Config::get('absolute_url').'review" data-element="review">REVIEWS</a></li>';

	echo 			'<li class="main-link"><a class="link'; echo Input::get('page', 'get') == "signup" ? ' active' : ''; echo '" href="'.Config::get('absolute_url').'signup" data-element="signup">SIGN UP</a></li>';
	
	echo 		'</ul>';
echo 	'</div>';


echo 	'<div class="header_search_div" >';
echo 			'<div class="form-field no-label-field" >';
	echo 			'<div class="icon-field">';
		echo 			'<div class="search_icon_div" id="submit-search"><span class="icon-search"></span></div>';
		echo 			'<input  type="text" id="search_field" value="" placeholder="SEARCH..." maxlength="255" />';
	echo 			'</div>';
echo 			'</div>';
echo 	'</div>';


	echo '</div>';

	echo '<script src="'.Config::get('absolute_url').'/scripts/searches/NewsSearch-min.js"></script>';

?>