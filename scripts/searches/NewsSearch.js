var DM = DM || {};

DM.NewsSearch = function(){

	var _absolute_url = "";

	$(function(){

		_absolute_url = $("#base_url").html();

		$("#submit-search").click(function(e){
			e.preventDefault();
			
			search_results();
		});

		$("#search_field").keypress(function(e) {
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
		
		var str = $("#search_field").val();
		var header              = $(".header");
		
	//	alert("poked inside search method "+str);
		console.log("searched string = "+str);
		$.ajax({
			type: "POST",
			url: _absolute_url+"includes/ajax/get-news-search.php",
			cache: false,
			dataType: "json",
			data: {
				'q': str
			},
			success: function(data){

				if("error" in data){
					//$(".results_div").html("");
					// $(".header").append(data);
					// searchBox.append(employeeDetails);
					// errorMsg.addClass("error").html(data['error']);
				}
				else
				{
					console.log(""+process_search_results);
					console.log("data : "+data);
					//alert(data);
					header.append(data['success']);
				}
			},
			error: ajax_error
			// error: function(XMLHttpRequest, textStatus, errorThrown){
			// 	//errorMsg.addClass("error").html(textStatus+'<br>'+errorThrown);
			// }
		});



		// $(".search-listing").fadeOut(200, function(){
		// 	$(".ajax-loader").fadeIn(200);
		// 	$(".search-listing").html("");

		// 	var str = $("#search_field").val();

		// 	$.ajax({
		// 		url: _absolute_url+'includes/ajax/get-news-search.php',
		// 		type: 'POST',
		// 		data: {
		// 			'q': str
		// 		},
		// 		success: process_search_results,
		// 		error: ajax_error
		// 	});
		// });
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