var DM = DM || {};

DM.DropNav = function(){
	$(function(){
		$(".nav-header").click(function(){
			$(this).toggleClass("nav-header-active");
			$(this).next(".nav-group").slideToggle(300);
		});
	});
}();