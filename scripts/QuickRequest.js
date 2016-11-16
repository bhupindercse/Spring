var DM = DM || {};

/* global Recaptcha */

DM.QuickRequest = function(){
	
	var dfd = null;

	$(function(){

		if($("#recaptcha").length)
			createCaptcha();
		
		$("#contact-fields").on("click", "button[name='submit']", function(e){
			e.preventDefault();
			submitContact();
		});
	});

	var createCaptcha = function(){
		Recaptcha.create("6Lc_6rwSAAAAAI8rrOrnGBv8xv7L0ku5hWJ8DQM0", "recaptcha", {
			theme: "blackglass",
			tabindex: 11
		});
	},

	submitContact = function(){

		dfd = $.Deferred();

		var absolute_url  = $("#base_url").html();
		var msg_container = $("#contact-msg");
		
		var name          = $("input[name='name']").val();
		var email         = $("input[name='email']").val();
		var comments      = $("textarea[name='comments']").val();

		var nonceElement  = $("input[name='token']");
		var nonce         = nonceElement.val();
		hideMsg(msg_container);

		dfd.done(function(){

			$.ajax({
				type: 'post',
				url: absolute_url + 'includes/ajax/user/signup.php',
				dataType: 'json',
				data: {
					'name': name,
					'email': email,
					'comments': comments,
					'nonce': nonce
				},
				success: function(data){
					
					if("error" in data)
					{
						showMsg(msg_container, data['error'], 'error');
						nonceElement.val(data['nonce']);
					}
					else
					{
						$("#contact-fields").slideUp(100, function(){
							showMsg(msg_container, data['success'], 'success');
						});
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown){
					showMsg(msg_container, textStatus+'<br>'+errorThrown, 'error');
				}
			});
		});
	},

	showMsg = function(container, msg, type){

		if(typeof type === "undefined")
			type = "error";

		var msgElement = type === "error" ? '<div class="error">': '<div class="success">';
		msgElement += msg;
		msgElement += '</div>';

		container.html(msgElement).slideDown(200);
	},

	hideMsg = function(container){

		if(container.is(":visible")){
			container.slideUp(200, function(){
				dfd.resolve();
			});
		}
		else
			dfd.resolve();
	};
}();