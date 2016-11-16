var Uploader = function(){

	var _base_url          = '',
		BYTES_PER_CHUNK    = 1048576, // 1MB
		FIRST_CHUNK        = 2000,
		NUM_CHUNKS         = 0,
		SIZE               = 0,
		TOTAL_LOADED       = 0,
		start              = 0,
		end                = BYTES_PER_CHUNK,
		blob               = null,
		name               = "",
		chunk_index_global = 0,
		current_file_index = 0,
		xhr                = null,
		xhr_instances      = [],
		file_data          = {},
		has_text           = false,
		_absolute_url = $("#absolute_url").val();

	$(function(){
		console.log("Advantage Transportation uploader");
		_base_url = $("#absolute_url").html();
		has_text  = $("#gallery_image_text").val();
		// console.log("in uploader _base_url "+_absolute_url);
		// console.log("has_text= "+has_text);

		$("body").on("click", "#upload", function(e){
			e.stopPropagation();
			e.preventDefault();

			Uploader.prepRequest();
		});
	});

	// ===================================================================================
	//	ADD NEW FILE TO LIST
	// ===================================================================================
	var addFile = function(){
		console.log("in addFile");	
		$("#general-errors").hide().html();
		$("#files").empty();
		
		var inp = document.getElementById("file");
		
		for (var i = 0; i < inp.files.length; ++i) {
			
			var name = inp.files.item(i).name;
			var name_bar = '<div class="upload-elements" id="'+i+'">';
			name_bar +=		'<div class="upload-filename"><span class="field-title">Filename:</span> '+name+'</div>';
			name_bar +=		'<div class="upload-data">';
			if(has_text)
			{
				name_bar +=			'<div class="form-field">';
				name_bar +=				'<div class="field-title">Summary:</div>';
				name_bar +=				'<div class="field-value"><textarea name="summary" placeholder="Brief description of image"></textarea></div>';
				name_bar +=			'</div>';
			}
			name_bar +=			'<div class="progress-wrapper">';
			name_bar +=				'<div class="progress-display">';
			name_bar +=					'<div class="progress">0%</div>';
			name_bar +=				'</div>';
			name_bar +=				'<div class="upload-error"></div>';
			name_bar +=			'</div>';
			name_bar +=		'</div>';
			name_bar +=	'</div>';

			$("#files").append(name_bar);

			$("#upload").show();
			// prepRequest();
		}
	},

	// ===================================================================================
	//	#3
	// ===================================================================================
	prepRequest = function() {
		console.log("in prep request ");
		if(typeof document.getElementById('file').files[current_file_index] === "undefined")
		{
			$("#general-errors").html("Please choose a file to upload.").show();
			return;
		}

		// ===================================
		//	De-activate out form fields
		// ===================================
		$("#frm :input, #frm textarea").attr("disabled", true);

		var currentUploadElement = $(".upload-elements[id='"+current_file_index+"']");
		console.log("in prep request "+currentUploadElement);
		// Fade notice in
		var prep_notice = $('<div class="file-prep-notice"><div class="prep-loader"></div><div class="prep-text">Prepping file for upload.</div></div>');
		currentUploadElement.find(".progress-display").append(prep_notice);
		prep_notice.fadeIn(200, function(){

			// Grab necessary info for working with file
			fileSelected(current_file_index);
			file_data[current_file_index]                 = {};
			file_data[current_file_index]['gallery_id']   = $("#gallery_id").val();
			file_data[current_file_index]['dom_id']       = current_file_index;
			file_data[current_file_index]['blob']         = document.getElementById('file').files[current_file_index];
			file_data[current_file_index]['SIZE']         = file_data[current_file_index]['blob'].size;
			file_data[current_file_index]['name']         = file_data[current_file_index]['blob'].name;
			file_data[current_file_index]['NUM_CHUNKS']   = Math.ceil(file_data[current_file_index]['SIZE'] / (BYTES_PER_CHUNK - FIRST_CHUNK)) + 1;
			file_data[current_file_index]['CHUNK_INDEX']  = 0;
			file_data[current_file_index]['TOTAL_LOADED'] = 0;
			file_data[current_file_index]['start']        = 0;

			// console.log("Name: "+	file_data[current_file_index]['name']   );
			// console.log("First chunk: "+FIRST_CHUNK);
			// console.log("Bytes per chunk: "+BYTES_PER_CHUNK);
			// console.log("Size: "+SIZE);
			// console.log("Sending # of chunks: "+		file_data[current_file_index]['NUM_CHUNKS']);

			// Prep the data for upload
			var fd = new FormData();
			fd.append("current_index", file_data[current_file_index]['dom_id']);
			fd.append("name", file_data[current_file_index]['name']);
			if(has_text) fd.append("summary", currentUploadElement.find("[name='summary']").val());
			fd.append("gallery-permalink", $("#gallery_permalink").val());

			// Set up new XHR request
			xhr = new XMLHttpRequest();
			xhr.addEventListener("load", function(evt){
				var data = JSON.parse(evt.target.response);

				if("error" in data)
				{
					showError(currentUploadElement.find(".upload-error"), "There was an error attempting to upload the file: "+data['error']);
				}
				// Start sending chunks
				else
				{
					prep_notice.fadeOut(300, function(){
						$(this).remove();
						// console.log("Prepped index:", current_file_index);
						sendRequest(file_data[current_file_index]);
					});
				}
			}, false);
			xhr.addEventListener("error", function(evt){
				var data = JSON.parse(evt.target.response);

				if("error" in data)
					showError(currentUploadElement.find(".upload-error"), "There was an error attempting to upload the file: "+data['error']);
				else
					console.log(evt);
			}, false);
			xhr.addEventListener("abort", function(evt){
				var data = JSON.parse(evt.target.response);

				if("error" in data)
					showError(currentUploadElement.find(".upload-error"), "There was an error attempting to upload the file: "+data['error']);
				else
					console.log(evt);
			}, false);

			console.log("call xhr post method "+$("#absolute_url").html()+"includes/fileupload/scripts/ajax-uploader/gallery/prep-file.php");
			

xhr.open("POST", _absolute_url+"includes/fileupload/scripts/ajax-uploader/gallery/prep-file.php");
			//xhr.open("POST", _base_url+"includes/fileupload/scripts/ajax-uploader/gallery/prep-file.php");
			xhr.send(fd);
		});
	},

	sendRequest = function(currentFile){

		if(currentFile['start'] < currentFile['SIZE'])
		{
			if(!currentFile['CHUNK_INDEX'])
				end = currentFile['start'] + FIRST_CHUNK;
			else
				end = currentFile['start'] + BYTES_PER_CHUNK;
			if(end > currentFile['SIZE'])
				end = currentFile['SIZE'];
			var chunk = currentFile['blob'].slice(currentFile['start'], end);

			// console.log("=========================");
			// console.log("Start:" + currentFile['start']);
			// console.log("End: " + end);
			// console.log("Sending chunk #"+(chunk_index_global+1)+".  Size: "+(end - start));
			// console.log("=========================");
			uploadFile(chunk, currentFile);

			currentFile['start'] = end;
			currentFile['CHUNK_INDEX']++;
		}
	},

	fileSelected = function(index) {
		var file = document.getElementById('file').files[index];
		if (file)
		{
			var fileSize = 0;
			if (file.size > 1024 * 1024)
				fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
			else
				fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';

			// console.log('Name: ' + file.name);
			// console.log('Size Orig: ' + file.size);
			// console.log('Size: ' + fileSize);
			// console.log('Type: ' + file.type);
			// console.log(file);
		}
	},

	uploadFile = function(chunk, currentBlob) {

		// Prep the data for upload
		var fd = new FormData();
		
		fd.append("file", chunk);
		fd.append("index", currentBlob['CHUNK_INDEX']);
		fd.append("current_index", currentBlob['dom_id']);

		xhr = new XMLHttpRequest();
		var chunkLoaded = 0;
		var prevLoaded = 0;
		
		xhr.addEventListener("load", function(evt){
			uploadComplete(evt, currentBlob);
		}, false);
		xhr.addEventListener("error", function(evt){
			uploadFailed(evt, currentBlob);
		}, false);
		xhr.addEventListener("abort", function(evt){
			uploadCanceled(evt, currentBlob);
		}, false);

		xhr.open("POST", _absolute_url+"includes/fileupload/scripts/ajax-uploader/gallery/uploadfile.php");
		xhr.upload.onprogress = function(evt){
			if (evt.lengthComputable){

				var prevLoaded = chunkLoaded;

				if(!currentBlob['CHUNK_INDEX'])
					chunkLoaded = evt.loaded / evt.total * FIRST_CHUNK;
				else
					chunkLoaded = evt.loaded / evt.total * BYTES_PER_CHUNK;
				currentBlob['TOTAL_LOADED'] = currentBlob['TOTAL_LOADED'] - prevLoaded + chunkLoaded;

				var percentComplete = (currentBlob['TOTAL_LOADED'] * 100 / currentBlob['SIZE']).toFixed(2);
				if (percentComplete > 100) percentComplete = 100;

				var progressContainer = $("#"+currentBlob['dom_id']).find(".progress");
				progressContainer.html(percentComplete.toString() + '%');
				progressContainer.addClass('active-progress');
				progressContainer.css('width', progressContainer.html());

				// console.log("First: ", (evt.loaded / evt.total * FIRST_CHUNK));
				// console.log("PERCENTAGE: ", percentComplete);
			}
			else {
				$("#"+currentBlob['dom_id']).find(".progress").html('unable to compute: '+uploadFile);
			}
		};
		// xhr.onload = function(evt) {
		// 	console.log("LOADED: ");
		// 	console.log(evt.target.response);
		// };

		// Push it to the server!
		xhr.send(fd);
	},

	// ===================================================================================
	//	Upload of chunk is done.  Server has sent back a response
	// ===================================================================================
	uploadComplete = function(evt, currentBlob) {
		var data = JSON.parse(evt.target.response);

		var currentElement = $("#"+currentBlob['dom_id']);

		if("error" in data)
		{
			showError(currentElement.find(".upload-error"), "There was an error attempting to upload the file: "+data['error']);
		}
		else if(currentBlob['NUM_CHUNKS'] === currentBlob['CHUNK_INDEX'])
		{
			// Fade notice in
			var prep_notice = $('<div class="file-prep-notice"><div class="prep-loader"></div><div class="prep-text">Uploaded file.  Merging.</div></div>');
			currentElement.find(".progress-display").append(prep_notice);
			prep_notice.fadeIn(200, function(){
				mergeFile(currentBlob);
			});
		}
		else
			sendRequest(currentBlob);
	},

	uploadFailed = function(evt, currentBlob) {
		evt = JSON.data(evt);
		$("#"+currentBlob['dom_id']).find(".upload-error").html("There was an error attempting to upload the file: "+evt['error']).show();
	},
	uploadCanceled = function(evt, currentBlob) {
		xhr.abort();
		xhr = null;
	},

	// ===================================================================================
	//	MERGE FILE JUST UPLOADED
	// ===================================================================================
	mergeFile = function(currentBlob) {

		xhr = new XMLHttpRequest();

		var fd = new FormData();
		fd.append("current_index", currentBlob['dom_id']);
		fd.append("name", currentBlob['name']);
		fd.append("index", currentBlob['NUM_CHUNKS']);

		xhr.addEventListener("error", function(evt){
			var data = JSON.parse(evt.target.response);
			showError("There was an error attempting to upload the file: "+data['error']);
		});
		xhr.addEventListener("load", function(evt){
			var data = JSON.parse(evt.target.response);

			if("error" in data)
				showError($("#"+currentBlob['dom_id']).find(".upload-error"), "There was an error attempting to upload the file: "+data['error']);
			else
			{
				$("#"+currentBlob['dom_id']).find(".prep-text").html("Merged file.  Updating database.");
				updateDB(currentBlob);
			}
		});
		xhr.open("POST", _absolute_url+"includes/fileupload/scripts/ajax-uploader/gallery/merge.php", true);
		xhr.send(fd);
	},

	// ===================================================================================
	//	UPDATE DB and RSS
	// ===================================================================================
	updateDB = function(currentBlob){

		xhr = new XMLHttpRequest();

		var fd = new FormData();
		fd.append("current_index", currentBlob['dom_id']);
		fd.append("gallery_id", currentBlob['gallery_id']);

		xhr.addEventListener("error", function(evt){
			var data = JSON.parse(evt.target.response);
			showError("There was an error attempting to update the database: "+data['error']);
		});
		xhr.addEventListener("load", function(evt){
			var data = JSON.parse(evt.target.response);

			if("error" in data)
				showError($("#"+currentBlob['dom_id']).find(".upload-error"), "There was an error attempting to update the database: "+data['error']);
			else
			{
				$("#"+currentBlob['dom_id']).find(".prep-loader").css("opacity", "0");
				$("#"+currentBlob['dom_id']).find(".prep-text").html("Done!");

				current_file_index++;
				if(document.getElementById('file').files.length > current_file_index)
				{
					prepRequest();
				}
				else
				{
					// console.log("hey, just checkin to make sure frm submitted LOLZZZZ");
					$("#frm").submit();
				}
			}
		});
		xhr.open("POST", _absolute_url+"includes/fileupload/scripts/ajax-uploader/gallery/updateDB.php", true);
		xhr.send(fd);
	},

	showError = function(errorDiv, error){
		// $(".upload-error").html(error).show();
		// $(".progress").addClass("error-progress");
		// $("#upload").hide();

		errorDiv.html(error).show();
		errorDiv.siblings('.progress-display').find('.progress').addClass("error-progress");
	},

	ajax_error = function (XMLHttpRequest, textStatus, errorThrown)
	{
		$("#permalink_error").html(textStatus+" - "+errorThrown);
	};

	return {
		addFile: addFile,
		prepRequest: prepRequest
	};
}();