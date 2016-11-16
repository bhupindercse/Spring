var DM = DM || {};

DM.PermalinkChecker = function(){

	var __baseURL      	  = "",
		__recordID        = 0,
		__fieldToCheck    = "",
		__editing         = "",
		__unique          = 1,
		__ajaxPage        = "",
		__titleField     = null,
		__permalinkField = null;

	$(function(){

		__baseURL  = $("#base_url").html();
		__ajaxPage = $("#permalink-script").length ? $("#permalink-script").val() : "permalink-check-create";

		__titleField     = $("#title");
		__permalinkField = $("#permalink");
		__recordID       = $("#id").val();

		// ===================================
		//	Position existing account loaders
		// ===================================
		$.each(__permalinkField, function(i, elem){
			positionLoader($(elem));
		});

		__titleField.keyup(function(){
			delay(function(){
				check_permalink(__titleField.val());
			}, 1000 );
		});

		__permalinkField.keyup(function(){
			delay(function(){
				check_permalink(__permalinkField.val());
			}, 1000 );
		});

		// Throttle the keyup function above
		// (otherwise, the value passed back will be old and it'll be tough to edit the field)
		var delay = (function(){
			var timer = 0;
			return function(callback, ms){
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();
	});

	// ===================================================================================
	//	Position loading icons for account searches
	// ===================================================================================
	var positionLoader = function(inputElement){

		var loader = inputElement.siblings(".permalink_preloader");

		var right_side    = inputElement.position().left + inputElement.outerWidth() - loader.outerWidth();
		var top_side      = inputElement.position().top;
		var height        = inputElement.outerHeight() - 4;
		loader.css({ "left": (right_side - 2)+"px", "top": (top_side + 2)+"px", "height": height+"px" });
	},

	check_permalink = function(text){

		$(".permalink_preloader").fadeIn(100, function(){
			$.ajax({
				type: "POST",
				url: __baseURL + 'includes/ajax/' + __ajaxPage+'.php',
				dataType: 'json',
				data: {
					'title': text,
					'id': __recordID
				},
				success: function(data){
					$(".permalink_preloader").fadeOut(100, function(){
						
						// clear fields/errors
						$("#permalink_error").html("");
						__permalinkField.val("");
						
						if("error" in data)
							showError(data['error']);
						else
							__permalinkField.val(data['permalink']);
					});
				},
				error: ajax_error
			});
		});
	},

	ajax_error = function(XMLHttpRequest, textStatus, errorThrown){
		$(".permalink_preloader").fadeOut(100, function(){
			showError(textStatus+" - "+errorThrown);
		});
	},

	showError = function(error){
		$("#permalink_error").html(error).show();
	};

	return {
		check_permalink: check_permalink
	};
}();