var DM = DM || {};

/* global history */
/* global google */

/* @codekit-prepend "SignUp.js" */
/* @codekit-prepend "LoadScript.js" */

DM.Nav = function(){
console.log("inside nav js ");
	var scrolling      = false;
	var containers     = [];
    //var offsetTops     = [];
	var loaded         = [];
	var currentSection = null;
	var feature_ticker_divs = $(".feature_ticker_div");
	var timer ;

	
	$(function(){

		//console.log("i="+i);
		feature_ticker_divs.hide().first().show();

		// if(i===1){
		// 	//for(i=1; i<=3 ;i++){
		 		//progressBar(i);
		// 	//}
		// }
			//var i = 1;
		//var progression = 0;
		
	

		    // setInterval(function() 
		    // {
		    // 	console.log("inside i="+i);
		    //     //$('#progress .progress-text').text(progression + '%');
		    //     //$('#progress .progress-bar').css({'width':progression+'%'});
		    //     console.log('#progress'+i+' .progress-bar'+i+' ');
		    //     $('#progress'+i+' .progress-bar'+i+' ').animate({ width: '100%' }, 10000);
		    //     if( progression === 100 ) {
		    //        //clearInterval(progress);
		    //         feature_ticker_divs.hide();
		    //          feature_ticker_divs.eq(i).show();
		    //          i++;
		    //          setInterval(i);
		    //        // alert('33');
		    //     }else
		    //         progression += 1;
		        
		    // }, 100);
		

		containers = $(".page-section");

		navController();

		// Scroll to initial location if necessary
		currentSection = $("input[name='initial_id']").val();

		console.log("current section : "+currentSection);

		if(currentSection !== ""){
			$('#'+currentSection).animatescroll({
				scrollSpeed: 0,
				onScrollStart:function(){
					 console.log("Scroll Start");
					scrolling = true;
				},
				onScrollEnd:function(){
					 console.log("Scroll End");
					scrolling = false;
					checkSectionLoaded(currentSection);
				}
			});
		}


		$(".mobile_menu").click(function(){
			$(".header_left_tabs_div").toggle();
			$(this).toggleClass("active");
			$("#home").toggleClass("active_menu");
		});

		$(".progressbar").click(function(){
			var id = $(this).attr("id");
			changeFeature(id);
		}); 

		$(".progressPager").click(function(){
			var id = $(this).attr("id");
			changeFeature(id);
		}); 

		// Nav link click
		$(".header a.link").click(function(e){
			e.preventDefault();

			var linkUrl = $(this).attr("href");
			var element = $(this).attr("data-element");
			var title   = $(this).html();

			currentSection = element;

			console.log("pushing state 1"+currentSection);

			// Add to browser history
			history.pushState({state:1}, title, linkUrl);

			// Update page title
			// document.title = title;

			// Update nav styles
			$(".nav .active").removeClass("active");
			$(this).addClass("active");

			
			$("html, body").animate({scrollTop: $('#'+currentSection).offset().top - 75 }, 400);

			// Animate to section
			// $('#'+currentSection).animatescroll({
			// 	scrollSpeed:400, offset: 200,
			// 	onScrollStart:function(){
			// 		scrolling = true;

			// 	},
			// 	onScrollEnd:function(){
			// 		scrolling = false;
			// 		// checkSectionLoaded(currentContainerID);
			// 	}
			// });
		});

		// Manual scroll
		$(window).scroll(function(){

			if(scrolling)
				{return;}

			// console.log("Scrolling inside...");

			var top = $(this).scrollTop() + 300; // Add a lil bit to make it 'pre-load'
			var currentContainerID = null;
			// var currentContainer   = null;

			$.each(containers, function(i, elem){
				if(top >= elem.offsetTop){
					currentContainerID = $(elem).attr("id");
				}
			});

			checkSectionLoaded(currentContainerID);

			var nav_link = $(".nav a[data-element='"+currentContainerID+"']");

			$(".nav .active").removeClass("active");
			nav_link.addClass("active");

			var linkUrl = nav_link.attr("href");
			var title   = nav_link.html();

			 console.log("pushing state 2");
			if(currentSection !== currentContainerID){
				history.pushState({state:1}, title, linkUrl);
			}

			currentSection = currentContainerID;
		});

		 var i = 1;
		 moveTicker(i,0);

	});


// var moveTicker = function(i,progression){
// 	var feature_ticker_divs = $(".feature_ticker_div");
//  	var timer = setInterval(function() 
// 		    {
// 		    	//var progression = 0;
		    	
// 		    	console.log("inside i="+i+"=>"+progression);
// 		        //$('#progress .progress-text').text(progression + '%');
// 		        //$('#progress .progress-bar').css({'width':progression+'%'});
// 		       $(' #progress'+i+' ').css({ 'background-color': '#223a41' });
// 		       $('#progressPager'+i+' ').css({ color: '#fff', background: '#223a41', border: '2px solid #223a41' });
// 		        console.log('#progress'+i+' .progress-bar'+i+'');
// 				// $(' .progress'+i+' ').append('<div class="progress-bar'+i+'"></div>');
// 		  //      	$('.progress-bar'+i+' ').css({ 'background-color': '#000',  'width': '0%' });
// 		  //       $(' .progress-bar'+i+' ').animate({ width: '100%', 'background': 'red' }, 10000);
		     
// 		        if( progression === 100 ) {
//  						console.log(' .progress-bar'+(i-1)+' .progress-bar'+(i-1)+' ');
		    			
// 		            	clearInterval(timer);

// 					//	$(' .progress-bar'+i+' ').remove();
// 						//$(' .progress'+(i-1)+' ').append('<div class="progress-bar'+(i-1)+'"></div>');
// 		        		progression = 0;
// 						feature_ticker_divs.hide();
// 						feature_ticker_divs.eq(i).show();
// 						i++;

 					
//  						$('#progressPager'+(i-1)+' ').css({ color: '#000', background: '#fff', border: '2px solid #c2c2c2' });
//  						$(' #progress'+(i-1)+' ').css({ 'background-color': '#c2c2c2' });
    		 			

//     		 			if(i===4){
//     		 				i=1;
//     		 				progression = 0;
//     		 			}
//     		 			if(i===1){
//     		 				$('#progressPager3').css({ color: '#000', background: '#fff', border: '2px solid #c2c2c2' });
//  							$(' #progress3').css({ 'background-color': '#c2c2c2' });
//     		 			}
//     		 			moveTicker(i, 0);
						
// 		            //alert('33');
// 		        }else
// 		            progression += 1;
		        
// 		    }, 100);	
// 	};
		var changeFeature = function(id){
			//var feature_ticker_divs = $(".feature_ticker_div");
			var lastChar = parseInt(id.substr(id.length - 1));
			clearInterval(timer);

			moveTicker(lastChar, 0);
			feature_ticker_divs.hide();
			feature_ticker_divs.eq(lastChar-1).show();

			$('#progress'+lastChar+' ').css({ 'background-color': '#223a41' });
		    $('#progressPager'+lastChar+' ').css({ color: '#fff', background: '#223a41', border: '2px solid #223a41' });

		    $('#progressPager'+(lastChar-1)+' ').css({ color: '#000', background: '#fff', border: '2px solid #c2c2c2' });
 			$('#progress'+(lastChar-1)+' ').css({ 'background-color': '#c2c2c2' });

 			$('#progressPager'+(lastChar+1)+' ').css({ color: '#000', background: '#fff', border: '2px solid #c2c2c2' });
 			$('#progress'+(lastChar+1)+' ').css({ 'background-color': '#c2c2c2' });

 			if(lastChar===3){
	 			$('#progressPager1').css({ color: '#000', background: '#fff', border: '2px solid #c2c2c2' });
	 			$('#progress1').css({ 'background-color': '#c2c2c2' });
	 			$('.progressbar_outer_div').css({ 'background-color': '#223A41' });
 			}else{
 				$('.progressbar_outer_div').css({ 'background-color': '#c2c2c2' });
 			}
 			if(lastChar===1){
	 			$('#progressPager3').css({ color: '#000', background: '#fff', border: '2px solid #c2c2c2' });
	 			$('#progress3').css({ 'background-color': '#c2c2c2' });
 			}

		};

var moveTicker = function(i,progression){
	var feature_ticker_divs = $(".feature_ticker_div");
 	 timer = setInterval(function() 
		    {  	
		    	//console.log("inside i="+i+"=>"+progression);
		    	
		    	// if(progression === 101){
		    	// 	console.log("stop ittttttttttttttt");
		    	// 	clearInterval(timer);
		    	// }
		   
		       $('#progress'+i+' ').css({ 'background-color': '#223a41' });
		       $('#progressPager'+i+' ').css({ color: '#fff', background: '#223a41', border: '2px solid #223a41' });
		       feature_ticker_divs.hide();
				feature_ticker_divs.eq(i-1).show();
		 
		        if( progression === 50 ) {	
		            	clearInterval(timer);
						
		        		progression = 0;
						
						i++;
 						$('#progressPager'+(i-1)+' ').css({ color: '#000', background: '#fff', border: '2px solid #c2c2c2' });
 						$('#progress'+(i-1)+' ').css({ 'background-color': '#c2c2c2' });
    		 			

    		 			if(i===4){
    		 				i=1;
    		 				progression = 0;
    		 			}

						if(i===3){
				 			$('.progressbar_outer_div').css({ 'background-color': '#223A41' });
			 			}else{
			 				$('.progressbar_outer_div').css({ 'background-color': '#c2c2c2' });
			 			}



    		 	// 		if(i===1){
    		 	// 			$('#progressPager3').css({ color: '#000', background: '#fff', border: '2px solid #c2c2c2' });
 							// $(' #progress3').css({ 'background-color': '#c2c2c2' });
    		 	// 		}

    		 			moveTicker(i, 0);
		        }else
		            progression += 1;
		        
		    }, 100);

		    // if(progression === 101){
		    // 		clearInterval(timer);
		    // 	}	
	};

// var moveTicker = function(i,progression){
// 	var feature_ticker_divs = $(".feature_ticker_div");
//  //	var timer = setInterval(function() 
// 		  //  {
// 		    	//var progression = 0;
		    	
// 		    	console.log("inside i="+i+"=>"+progression);
// 		        //$('#progress .progress-text').text(progression + '%');
// 		        //$('#progress .progress-bar').css({'width':progression+'%'});
		       
// 		        console.log('#progress'+i+' .progress-bar'+i+'');

// 		       	$('.progress-bar'+(i)+' ').css({ 'background-color': '#000',  'width': '' });
		      
		     
// 		      //  if( progression === 100 ) {
// 		   				// clearInterval();
//  						//$('#progress'+(i-1)+' .progress-bar'+(i-1)+' ').css({ background: '#000' });
//  						//$(' .progress-bar'+(i-1)+' .progress-bar'+(i-1)+' ').stop();
 					
//  						console.log(' .progress-bar'+(i-1)+' .progress-bar'+(i-1)+' ');
// 		    			if(i===3){
//     		 				i=0;
//     		 				//progression = 0;
//     		 			}
// 		            	//clearInterval(timer);

// 						//$(' .progress-bar'+i+' ').remove();
// 						//$(' .progress'+(i-1)+' ').append('<div class="progress-bar'+(i-1)+'"></div>');
// 						$(' .progress-bar'+i+' ').animate({ width: '100%', background: '#000' }, 10000);
// 		        		progression = 0;
// 						feature_ticker_divs.hide();
// 						feature_ticker_divs.eq(i).show();
// 						i++;
					
// 						//$('#progress'+(i-1)+' .progress-bar'+(i-1)+' ').css({ background: 'red' });
// 						// $('#progress'+(i-1)+' .progress-bar'+(i-1)+' ').css({ width: '0%' });
// 						// $('#progress'+(i-2)+' .progress-bar'+(i-2)+' ').css({ width: '0%' });

// 						//$('#progress'+(i-2)+' .progress-bar'+(i-2)+' ').css({ background: '#000'});
//  						//$('#progressPager'+i+' ').css({ color: '#fff', background: '#223a41', border: '2px solid #223a41' });
//     		 			moveTicker(i, 0);
						
// 		            //alert('33');
// 		       //
		        
// 		   // }, 100);	
// 	};


// var moveTicker = function(i,progression){
// 	var feature_ticker_divs = $(".feature_ticker_div");
//  	var timer = setInterval(function() 
// 		    {
// 		    	//var progression = 0;
		    	
// 		    	console.log("inside i="+i+"=>"+progression);
// 		        //$('#progress .progress-text').text(progression + '%');
// 		        //$('#progress .progress-bar').css({'width':progression+'%'});
		       
// 		        console.log('#progress'+i+' .progress-bar'+i+'');

		       
// 		        $(' #progress'+i+' ').animate({ width: '100%', background: '#000' }, 10000);
		     
// 		        if( progression === 100 ) {
// 		    //     		clearInterval();
//  						//$('#progress'+(i-1)+' .progress-bar'+(i-1)+' ').css({ background: '#000' });
//  						$(' #progress'+i+' ').stop( true, true );
// 		    			if(i===3){
//     		 				i=0;
//     		 				progression = 0;

//     		 			}
// 		            	clearInterval(timer);

// 						$(' .progress'+i+' ').css({ width: '0%', background: '#c2c2c2'  });
						
// 		        		progression = 0;
// 						feature_ticker_divs.hide();
// 						feature_ticker_divs.eq(i).show();
// 						i++;
						
// 						//$('#progress'+(i-1)+' .progress-bar'+(i-1)+' ').css({ background: 'red' });
// 						// $('#progress'+(i-1)+' .progress-bar'+(i-1)+' ').css({ width: '0%' });
// 						// $('#progress'+(i-2)+' .progress-bar'+(i-2)+' ').css({ width: '0%' });

// 						//$('#progress'+(i-2)+' .progress-bar'+(i-2)+' ').css({ background: '#000'});

//     		 			moveTicker(i, 0);
						
// 		            //alert('33');
// 		        }else
// 		            progression += 1;
		        
// 		    }, 100);	
// 	};

  // var progressBar =  setInterval(function() 
		//     {
		//     	var progression = 0;
		//     	var i = 1;
		//     	console.log("inside i="+i);
		//         //$('#progress .progress-text').text(progression + '%');
		//         //$('#progress .progress-bar').css({'width':progression+'%'});
		//         console.log('#progress'+i+' .progress-bar'+i+' ');
		//         $('#progress'+i+' .progress-bar'+i+' ').animate({ width: '100%' }, 10000);
		//         if( progression === 100 ) {
		//             clearInterval(progressBar);
		//             feature_ticker_divs.hide();
		//              feature_ticker_divs.eq(i).show();
		//              i++;
		//              progressBar(i);
		//             alert('33');
		//         }else
		//             progression += 1;
		        
		//     }, 100);

		// var progressBar = setInterval(function(ik) {
		// 	var i = 1;
		// 	//alert('#progress'+i+' .progress-bar'+i+' ');
		// 	var progression = 0;
		// 	console.log('setInterval ik='+ik);
	 //        //$('#progress .progress-text').text(progression + '%');
	 //        //$('#progress .progress-bar').css({'width':progression+'%'});
	 //        $('#progress'+i+' .progress-bar'+i+' ').animate({ width: '100%' }, 10000);
	        
	 //        if( progression == 33 ) {
	 //            //clearInterval(progressBar);
	 //            feature_ticker_divs.hide();
	 //            feature_ticker_divs.eq(2).show();
	 //            alert('33');
	 //        }else
	 //            progression += 1;
	        
	 //    }, 100);

	var checkSectionLoaded = function(sectionName){

		//var newScript = "";

		if(sectionName === "features"){
			if(sectionName in loaded){
				return;
			}

			loaded[sectionName] = true;

			// newScript = document.createElement('script');
			// newScript.setAttribute('src','https://maps.googleapis.com/maps/api/js?v=3&sensor=false&callback=DM.Nav.mapInitialize');
			// document.body.appendChild(newScript);
		}
		else if(sectionName === "pricing"){
			if(sectionName in loaded){
				return;
			}

			loaded[sectionName] = true;

			// Do not load if galleries do not exist
			// if(!$('input[name="gallery-existance"]').length){
			// 	return;
			// }

			// Load gallery
			// DM.LoadScript.loadScript('scripts/GalleryLoader-min.js', function(){
			// 	DM.GalleryLoader.init();
			// });
		}
		else if(sectionName === "about"){
			if(sectionName in loaded){
				return;
			}

			loaded[sectionName] = true;

			// Load news
			//DM.LoadScript.loadScript('scripts/News-min.js');
		}
		else if(sectionName === "review"){

			//console.log("Here in services");
			//console.log(loaded);

			if(sectionName in loaded){
				return;
			}

			loaded[sectionName] = true;

			// Load news
			// DM.LoadScript.loadScript('scripts/Types-min.js', function(){
			// 	//console.log("LOADED!!!!!");
			// 	DM.Types.init();
			// });
		}
		else if(sectionName === "signup"){

			//console.log("Here in services");
			//console.log(loaded);

			if(sectionName in loaded){
				return;
			}

			loaded[sectionName] = true;

			// Load news
			// DM.LoadScript.loadScript('scripts/Types-min.js', function(){
			// 	//console.log("LOADED!!!!!");
			// 	DM.Types.init();
			// });signup
		}
	};

	var mapInitialize = function(){

		var map_element = $(".google-map");

		var lat = map_element.attr("data-lat");
		var lng = map_element.attr("data-long");

		// Create lat/lng object with destination coords
		var loc = {};
		loc.lat = lat;
		loc.lng = lng;
		var latlng = new google.maps.LatLng(loc.lat, loc.lng);
		
		var mapOptions = {
			zoom: 15,
			center: latlng,
			scrollwheel: false,
			mapTypeId: 'roadmap'
		};

		var _map = new google.maps.Map(map_element[0], mapOptions);

		var marker;
		marker = new google.maps.Marker({
			position: new google.maps.LatLng(loc.lat, loc.lng),
			map: _map
		});
	};

	var navController = function(){

		$("li.mobile").click(function(){
			if($("li.main-link").is(":visible")){
				$(this).css("width", ""); 
				$("li.main-link").removeClass("visible");

			}
			else{
				$(this).css("width", "100%"); 
				$("li.main-link").addClass("visible");
			}
		});

	};

	return{
		mapInitialize: mapInitialize
	};
}();