var DM=DM||{};DM.QuickRequest=function(){var e=null;$(function(){$("#recaptcha").length&&t(),$("#contact-fields").on("click","button[name='submit']",function(e){e.preventDefault(),n()})});var t=function(){Recaptcha.create("6Lc_6rwSAAAAAI8rrOrnGBv8xv7L0ku5hWJ8DQM0","recaptcha",{theme:"blackglass",tabindex:11})},n=function(){e=$.Deferred();var t=$("#base_url").html(),n=$("#contact-msg"),r=$("input[name='name']").val(),l=$("input[name='email']").val(),o=$("textarea[name='comments']").val(),c=$("input[name='token']"),s=c.val();i(n),e.done(function(){$.ajax({type:"post",url:t+"includes/ajax/user/submit-contact.php",dataType:"json",data:{name:r,email:l,comments:o,nonce:s},success:function(e){"error"in e?(a(n,e.error,"error"),c.val(e.nonce)):$("#contact-fields").slideUp(100,function(){a(n,e.success,"success")})},error:function(e,t,i){a(n,t+"<br>"+i,"error")}})})},a=function(e,t,n){"undefined"==typeof n&&(n="error");var a="error"===n?'<div class="error">':'<div class="success">';a+=t,a+="</div>",e.html(a).slideDown(200)},i=function(t){t.is(":visible")?t.slideUp(200,function(){e.resolve()}):e.resolve()}}();var DM=DM||{};DM.LoadScript=function(){var e=function(e,t){var n=document.createElement("script");t&&n.addEventListener("load",t),n.async=!0,n.src=e,document.querySelector("body").appendChild(n)},t=function(e,t){var n=document.createElement("link");t&&n.addEventListener("load",t),n.href=e,n.rel="stylesheet",document.querySelector("head").appendChild(n)};return{loadScript:e,loadCSS:t}}();var DM=DM||{};DM.Nav=function(){var e=!1,t=[],n=[],a=null;$(function(){t=$(".page-section"),l(),a=$("input[name='initial_id']").val(),""!==a&&$("#"+a).animatescroll({scrollSpeed:0,onScrollStart:function(){e=!0},onScrollEnd:function(){e=!1,i(a)}}),$(".header a.link").click(function(t){t.preventDefault();var n=$(this).attr("href"),i=$(this).attr("data-element"),r=$(this).html();a=i,history.pushState({state:1},r,n),$(".nav .active").removeClass("active"),$(this).addClass("active"),$("#"+a).animatescroll({scrollSpeed:400,onScrollStart:function(){e=!0},onScrollEnd:function(){e=!1}})}),$(window).scroll(function(){if(!e){var n=$(this).scrollTop()+300,r=null;$.each(t,function(e,t){n>=t.offsetTop&&(r=$(t).attr("id"))}),i(r);var l=$(".nav a[data-element='"+r+"']");$(".nav .active").removeClass("active"),l.addClass("active");var o=l.attr("href"),c=l.html();a!==r&&history.pushState({state:1},c,o),a=r}})});var i=function(e){var t="";if("contact"===e){if(e in n)return;n[e]=!0,t=document.createElement("script"),t.setAttribute("src","https://maps.googleapis.com/maps/api/js?v=3&sensor=false&callback=DM.Nav.mapInitialize"),document.body.appendChild(t)}else if("gallery"===e){if(e in n)return;if(n[e]=!0,!$('input[name="gallery-existance"]').length)return;DM.LoadScript.loadScript("scripts/GalleryLoader-min.js",function(){DM.GalleryLoader.init()})}else if("news"===e){if(e in n)return;n[e]=!0,DM.LoadScript.loadScript("scripts/News-min.js")}else if("services"===e){if(e in n)return;n[e]=!0,DM.LoadScript.loadScript("scripts/Types-min.js",function(){DM.Types.init()})}},r=function(){var e=$(".google-map"),t=e.attr("data-lat"),n=e.attr("data-long"),a={};a.lat=t,a.lng=n;var i=new google.maps.LatLng(a.lat,a.lng),r={zoom:15,center:i,scrollwheel:!1,mapTypeId:"roadmap"},l=new google.maps.Map(e[0],r),o;o=new google.maps.Marker({position:new google.maps.LatLng(a.lat,a.lng),map:l})},l=function(){$("li.mobile").click(function(){$("li.main-link").is(":visible")?($(this).css("width",""),$("li.main-link").removeClass("visible")):($(this).css("width","100%"),$("li.main-link").addClass("visible"))})};return{mapInitialize:r}}();