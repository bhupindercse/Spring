<div class="page-section" id="signup">
	<?php 
		// DEBUGGING EMAILING!!
		//Session::clear('contact-submit');
	?>

	<div class="content-wrapper">
		<div class="contact-form">
			<div class="signup_header" >
				<h2>JOIN THE CLUB TODAY</h2>
				<div class="title"><b>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</b></div>
			</div>

			<?php
				echo '<div id="contact-msg" class="';
				if(!Session::hasValue('contact-submit')) echo 'hidden_area ';
				echo 'form-field">';
				if(Session::hasValue('contact-submit')){
					echo '<div class="success">Thank you for your submission.</div>';
				}
				echo '</div>';

				if(!Session::hasValue('contact-submit')){
			?>

			<form id="contact-fields" type="post">
				<div class="form-field no-label-field">
					<div class="icon-field">
						<div class="form_field_icon" style=""><span class="icon-user"></span></div>
						<input type="text" name="name" id="name" value="" placeholder="NAME*" maxlength="255" />
					</div>
				</div>

				<div class="form-field no-label-field">
					<div class="icon-field">
						<div class="form_field_icon" ><span class="icon-shop"></span></div>
						<input type="text" name="company" id="company" value="" placeholder="COMPANY NAME*" maxlength="255" />
					</div>
				</div>


				<div class="form-field no-label-field">
					<div class="icon-field">
						<div class="form_field_icon" ><span class="icon-mail"></span></div>
						<input type="email" name="email" id="email" value="" placeholder="EMAIL/USERNAME*" maxlength="255" />
					</div>
				</div>

				<div class="form-field no-label-field">
					<div class="icon-field">
						<div class="form_field_icon" ><span class="icon-lock"></span></div>
						<input type="password" name="password" id="password" value="" placeholder="PASSWORD*" maxlength="255" />
					</div>
				</div>


				<div>
					<div class="newsletter_checkbox_div">
						 <input type="checkbox" name="newsletter"/> Receive Our Newsletter
					</div>
					<div class="mandatory_fields"> * Mandatory Fields</div>
				</div>

		
				<div class="signup_button_div">
					<?php $token = Token::generate("contact-request"); ?>
					<input type="hidden" name="token" value="<?php echo $token; ?>">
					<button class="btn" name="submit" value="submit">SIGN UP</button>
				</div>
			</form>
			<?php } ?>
		</div> 

		<div class="contact-sides contact-info">
			<div>
				<div class="social-nav">
					<a class="icon-facebook" href="<?php echo Config::get('social/facebook/url'); ?>" target="_blank"></a>
					<a class="icon-instagram" href="<?php echo Config::get('social/instagram/url'); ?>" target="_blank"></a>
					<a class="icon-twitter" href="<?php echo Config::get('social/twitter/url'); ?>" target="_blank"></a>
				</div>
			</div>
		</div>
	</div>
</div>