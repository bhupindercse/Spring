var DM=DM||{};DM.NewsSearch=function(){var e="";$(function(){e=$("#base_url").html(),$("#submit-search").click(function(e){e.preventDefault(),a()}),$("#search_field").keypress(function(e){13===e.keyCode&&(e.preventDefault(),a())}),$(".search-listing").on("click","tr.clickable",function(){window.location="?item="+$(this).attr("rel")})});var a=function(){var a=$("#search_field").val(),r=$(".header");console.log("searched string = "+a),$.ajax({type:"POST",url:e+"includes/ajax/get-news-search.php",cache:!1,dataType:"json",data:{q:a},success:function(e){"error"in e||(console.log(""+n),console.log("data : "+e),r.append(e.success))},error:c})},n=function(e){e=JSON.parse(e),$(".ajax-loader").fadeOut(200,function(){"error"in e?$(".search-listing").html(e.error).fadeIn(200):$(".search-listing").html(e.success).fadeIn(200)})},c=function(e,a,n){$(".ajax-loader").fadeOut(200,function(){$(".search-listing").html(n).fadeIn(200)})}}();