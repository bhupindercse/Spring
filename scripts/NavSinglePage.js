var DM = DM || {};

DM.NavSinglePage = function(){

	$(function(){
		navController();
	});

	var navController = function(){

		$("li.mobile").click(function(){
			if($("li.main-link").is(":visible"))
				$("li.main-link").removeClass("visible");
			else
				$("li.main-link").addClass("visible");
		});

	};
}();