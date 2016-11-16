var DM = DM || {};

DM.LoadScript = function(){
	var loadScript = function(path, callback) {
		var s = document.createElement('script');
		if (callback)
			s.addEventListener('load', callback);
		s.async = true;
		s.src = path;
		document.querySelector('body').appendChild(s);
	};

	var loadCSS = function(path, callback) {
		var s = document.createElement('link');
		if (callback)
			s.addEventListener('load', callback);
		s.href = path;
		s.rel  = "stylesheet";
		document.querySelector('head').appendChild(s);
	};

	return {
		loadScript: loadScript,
		loadCSS: loadCSS
	};
	
}();