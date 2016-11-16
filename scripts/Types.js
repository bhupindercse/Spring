var DM = DM || {};

DM.Types = function(){
	
	var _absolute_url = "";
	var _total_pages  = 0;
	var _current_page = 1;
	
	$(function(){

		_absolute_url = $("#base_url").val();

		$("#services").on("click", ".type-item", function(){
			clickItem($(this));
		});
		$("#services").on("click", ".type-nav .item", function(){
			changeItem($(this));
		});
		$("#services").on("click", ".arrow", function(){
			changeItemWithArrow($(this));
		});
	});

	var init = function(){
		getTypes(_current_page);
	},

	clickItem = function(itemElement){
		if(itemElement.hasClass("active")) return;

		$(".type-item.active").removeClass("active");
		itemElement.addClass("active");
	},

	changeItem = function(navElement){
		_current_page = navElement.attr('data-id');
		getTypes(_current_page);
	},

	changeItemWithArrow = function(arrowElement){
		
		if(_total_pages === 1)
			return;

		// Figure out current page
		if(arrowElement.hasClass(".icon-circle-left"))
			_current_page--;
		else
			_current_page++;

		if(_current_page < 1)
			_current_page = _total_pages;
		else if(_current_page > _total_pages)
			_current_page = 1;

		getTypes(_current_page);
	},

	getTypes = function(pg){
		
		var loader      = $("#services").find(".ajax-loader");
		var dfd         = $.Deferred();
		var wrapper     = $(".type-item-wrapper");
		var item_length = $(".type-item").length;

		if(item_length)
		{
			wrapper.css("height", wrapper.height());
			// var count           = $(".type-item").length;
			var items_completed = 0;

			$(".type-item").fadeOut(100, function(){

				items_completed++;

				if(item_length === items_completed)
					dfd.resolve();
			});
		}
		else
			dfd.resolve();

		dfd.done(function(){

			loader.fadeIn(100, function(){
				$.ajax({
					type: 'post',
					url: _absolute_url+'includes/ajax/get-candy-types.php',
					data: {
						'pg': pg
					},
					success: function(data){
						data = JSON.parse(data);

						loader.fadeOut(100, function(){

							if("error" in data){
								wrapper.html(data['error']);
								wrapper.removeAttr("style");
							}
							else
							{
								_total_pages = data['total-pages'];

								wrapper.html(data['content']);
								wrapper.removeAttr("style");

								if(!$(".type-item-nav .item").length && _total_pages > 1)
									$(".type-item-nav").html(data['nav']);
								else
								{
									$(".type-item-nav .active").removeClass("active");
									$(".type-item-nav .item[data-id='"+pg+"']").addClass("active");
								}

								if(_total_pages > 1)
									$(".type-arrow-nav").addClass("active");
							}
						});
					},
					error: function (XMLHttpRequest, textStatus, errorThrown){
						wrapper.append('<div class="type-item">'+textStatus+'<br>'+errorThrown+'</div>');
						wrapper.removeAttr("style");
					}
				});
			});
		});
	};

	return {
		init: init
	};

}();