var DM=DM||{};DM.Types=function(){var e="",t=0,i=1;$(function(){e=$("#base_url").val(),$("#services").on("click",".type-item",function(){n($(this))}),$("#services").on("click",".type-nav .item",function(){s($(this))}),$("#services").on("click",".arrow",function(){r($(this))})});var a=function(){c(i)},n=function(e){e.hasClass("active")||($(".type-item.active").removeClass("active"),e.addClass("active"))},s=function(e){i=e.attr("data-id"),c(i)},r=function(e){1!==t&&(e.hasClass(".icon-circle-left")?i--:i++,1>i?i=t:i>t&&(i=1),c(i))},c=function(i){var a=$("#services").find(".ajax-loader"),n=$.Deferred(),s=$(".type-item-wrapper"),r=$(".type-item").length;if(r){s.css("height",s.height());var c=0;$(".type-item").fadeOut(100,function(){c++,r===c&&n.resolve()})}else n.resolve();n.done(function(){a.fadeIn(100,function(){$.ajax({type:"post",url:e+"includes/ajax/get-candy-types.php",data:{pg:i},success:function(e){e=JSON.parse(e),a.fadeOut(100,function(){"error"in e?(s.html(e.error),s.removeAttr("style")):(t=e["total-pages"],s.html(e.content),s.removeAttr("style"),!$(".type-item-nav .item").length&&t>1?$(".type-item-nav").html(e.nav):($(".type-item-nav .active").removeClass("active"),$(".type-item-nav .item[data-id='"+i+"']").addClass("active")),t>1&&$(".type-arrow-nav").addClass("active"))})},error:function(e,t,i){s.append('<div class="type-item">'+t+"<br>"+i+"</div>"),s.removeAttr("style")}})})})};return{init:a}}();