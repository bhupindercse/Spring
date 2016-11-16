var DM = DM || {};

DM.ClientSearch = function(){

	var _absolute_url = "";

	$(function(){

		_absolute_url = $("#base_url").html();

		$("#submit-search").click(function(e){
			e.preventDefault();
			search_results();
		});

		$("#search").keypress(function(e) {
			if(e.keyCode === 13)
			{
				e.preventDefault();
				search_results();
			}
		});

		$(".search-listing").on("click", "tr.clickable", function(){
			window.location = "?item="+$(this).attr("rel");
		});
	});

	var search_results = function(){
		$(".search-listing").fadeOut(200, function(){
			$(".ajax-loader").fadeIn(200);
			$(".search-listing").html("");

			var str = $("#search").val();

			$.ajax({
				url: _absolute_url+'includes/ajax/get-gallery-search.php',
				type: 'POST',
				data: {
					'q': str
				},
				success: process_search_results,
				error: ajax_error
			});
		});
	},

	process_search_results = function(data){
		data = JSON.parse(data);

		$(".ajax-loader").fadeOut(200, function(){
			if('error' in data)
				$(".search-listing").html(data['error']).fadeIn(200);
			else
				$(".search-listing").html(data['success']).fadeIn(200);
		});
	},

	ajax_error = function(XMLHttpRequest, textStatus, errorThrown){
		$(".ajax-loader").fadeOut(200, function(){
			$(".search-listing").html(errorThrown).fadeIn(200);
		});
	};
	
}();