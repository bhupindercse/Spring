var DM = DM || {};

/* @codekit-prepend "LoadScript.js" */

DM.GalleryLoader = function(){

	var absolute_url   = "",
		currentGallery = "",
		allowClickable = false,
		initialized    = false;
	
	var init = function(){

		absolute_url   = $("#base_url").html();
		currentGallery = $("#currentGallery").val();

		load_thumbs();

		$(".gallery-btn").click(function(){
			showGallery($(this));
		});
	};

	var load_thumbs = function(){

		var new_thumbs = {};
		var total_thumbs = $(".gallery-thumbs .gallery-thumbnail").length;

		if(total_thumbs && !initialized){
			// DM.LoadScript.loadScript('scripts/libs/lightbox2/js/lightbox.min.js', DM.GalleryLoader.lightBoxLoaded);
			// DM.LoadScript.loadCSS('scripts/libs/lightbox2/css/lightbox.css');
			DM.LoadScript.loadScript('scripts/libs/fancybox-2.5.1/lib/jquery.mousewheel-3.0.6.pack.js');
			DM.LoadScript.loadCSS('scripts/libs/fancybox-2.5.1/source/jquery.fancybox.css');
			DM.LoadScript.loadScript('scripts/libs/fancybox-2.5.1/source/jquery.fancybox.pack.js', DM.GalleryLoader.fancyboxLoaded);

			initialized = true;
		}

		$.each($(".gallery-thumbs .gallery-thumbnail"), function(i, element){

			// Get the source of the image
			var source  = $(element).attr("data-src");
			var thumb   = $(element).attr("data-thumb");
			var gallery = $(element).attr("data-gallery");
			var title   = $(element).attr("data-title");

			// Add the new thumb to the array (so it's kept track of in the onload() function)
			var newThumb = $('<a class="fancybox" href="'+source+'" rel="'+gallery+'" title="'+title+'"><img src="'+thumb+'" alt="'+thumb+'"></a>');
			new_thumbs[i] = {};
			new_thumbs[i]['element'] = $(element);
			new_thumbs[i]['thumb']   = newThumb;

			// Create new image
			var img = new Image();
			img.onload = function(){

				var imgLoaded = $(this);

				// Fade out the thumb before changing the src
				var thumbnail_element = new_thumbs[imgLoaded.attr("alt")]['element'];

				thumbnail_element.fadeOut(100, function(){

					// Remove the loading class indicator
					$(this).find('img').remove();

					$(this).append(new_thumbs[imgLoaded.attr("alt")]['thumb']);

					// Fade the sucker back in
					$(this).fadeIn(200);
				});
			};

			// Set the image attributes
			img.alt = i;
			img.src = thumb;
		});
	},

	fancyboxLoaded = function(){
		$(".fancybox").fancybox({
			helpers:  {
				title : {
					type : 'inside'
				}
			}
		});
	},

	showGallery = function(btn){

		if(btn.hasClass("active"))
			return;

		var permalink = btn.attr("data-permalink");

		$(".gallery-btn.active").removeClass("active");
		btn.addClass("active");

		var gallery_container = $(".gallery-thumbs");

		gallery_container.css("height", $(".gallery-thumbs").outerHeight());
		gallery_container.find("img").fadeOut(100);

		$.ajax({
			type: 'post',
			url: absolute_url+'includes/ajax/get-gallery-images.php',
			dataType: 'json',
			data: {
				'permalink': permalink
			},
			success: function(data){
				if("error" in data)
				{
					gallery_container.html(data['error']);
					animateGalleryHeight();
				}
				else
				{
					gallery_container.html(data['html']).find(".gallery-thumbnail").hide();
					animateGalleryHeight(true);
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown){
				gallery_container.html('<div class="error">'+textStatus+'<br>'+errorThrown+'</div>');
				animateGalleryHeight();
			}
		});
	},

	animateGalleryHeight = function(loadImages){

		var gallery_container = $(".gallery-thumbs");
		var thumbs            = gallery_container.find(".gallery-thumbnail");

		var old_height = gallery_container.outerHeight();
		thumbs.show();
		gallery_container.css("height", "");
		var new_height = gallery_container.outerHeight();
		thumbs.hide();
		gallery_container.css("height", old_height);

		gallery_container.animate({
			"height": new_height
		}, 100, function(){
			gallery_container.css("height", "");
			if(typeof loadImages !== "undefined" && loadImages)
				load_thumbs();
		});
	};

	return{
		init: init,
		fancyboxLoaded: fancyboxLoaded
	};
}();