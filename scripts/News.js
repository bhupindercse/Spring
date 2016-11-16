var DM = DM || {};

DM.News = function(){
	
	$(function(){
		$(".news-link").click(function(e){
			e.preventDefault();
			changeNewsItem($(this));
		});
	});

	var changeNewsItem = function(linkElement){
		var id          = linkElement.attr('data-id');
		var currentItem = $(".news-item.active");
		var newItem     = $(".news-item[data-id='"+id+"']");
		var wrapper     = currentItem.parents(".news-item-wrapper");

		var currentHeight = currentItem.height();
		newItem.show();
		var newHeight = newItem.height();
		newItem.hide();

		wrapper.css("height", currentHeight);
		currentItem.fadeOut(200, function(){
			currentItem.removeClass("active").removeAttr("style");

			wrapper.animate({
				'height': newHeight+"px"
			}, 200, function(){
				wrapper.removeAttr("style");
				newItem.addClass("active").removeAttr("style");
			});
		});

		$(".news-link.active").removeClass("active");
		linkElement.addClass("active");
	};
}();