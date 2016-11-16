var DM = DM || {};

DM.NewsScroller = function(){

	var _baseURL        = "";
	var scrollContainer = null;
	var scrollerSection = null;
	var loader          = null;
	var moreBtn         = null;
	
	var current_max     = 0;
	var canLoad         = 1;

	$(function(){

		_baseURL        = $("#rel_url").val();
		scrollerSection = $(".section-scroller");
		scrollContainer = scrollerSection.parents(".section-scroller-wrapper");
		loader          = $(".scroll-loading");
		moreBtn         = $(".scroll-more-btn");

		scrollerSection.scroll(function(){
			// console.log(scrollerSection.scrollTop(), scrollerSection[0].scrollHeight - scrollerSection.height());
			if(scrollerSection.scrollTop() >= scrollerSection[0].scrollHeight - scrollerSection.height()){
				getStories();
			}
		});

		moreBtn.click(function(){
			getStories();
		});

		getStories();
	});

	var getStories = function(){

		if(!canLoad)
			return;

		var current_top = scrollerSection[0].scrollHeight - 20;

		moreBtn.hide();
		loader.show();

		$.ajax({
			type: 'post',
			url: _baseURL+'includes/ajax/get-news-stories.php',
			dataType: 'json',
			data: {
				'current_max': current_max
			},
			success: function(data){
				
				loader.hide();

				if("error" in data){
					showError(data['error']);
				}
				else
				{
					current_max = data['current_max'];
					scrollerSection.append(data['success']);
					$(".small-news-item").fadeIn(100);

					if(!data['more-stories'])
						current_top += 20;
					scrollerSection.scrollTop(current_top);

					if(!data['more-stories']){
						scrollerSection.unbind("scroll");
						canLoad = 0;
					}
					else
					{
						moreBtn.show();
					}
				}

			},
			error: function (XMLHttpRequest, textStatus, errorThrown){
				loader.hide();
				var msg = textStatus + "<br>" + errorThrown;
				showError(msg);
			}
		});
	},

	showError = function(msg){
		scrollerSection.html('<div class="error">'+msg+'</div>');
	};

}();