/*
Based on (with a few modifications):
-filedrag.js - HTML5 File Drag & Drop demonstration-
-Featured on SitePoint.com-
-Developed by Craig Buckler (@craigbuckler) of OptimalWorks.net-
*/
//(function() {
    //var counter = 0;
    var filetypes = ['image/jpeg','image/png']; //Add allowed filetypes here

	// getElementById
	function $id(id) {
		return document.getElementById(id);
	}

	// output information
	function Output(msg) {
		var m = $id("bilder");
		m.innerHTML = m.innerHTML + msg;
	}

    function Status(msg) {
        var m = $id("process");
        m.innerHTML = m.innerHTML + msg;
    }


	// file drag hover
	function FileDragHover(e) {
		e.stopPropagation();
		e.preventDefault();
		e.target.className = (e.type == "dragover" ? "hover" : "");
	}

    //Filetypes
    function types(type) {
        for(var i = 0; i < filetypes.length; i++){
            if(type == filetypes[i]){
                return true
            }
        }
        return false;
    }

	// file selection
	function FileSelectHandler(e) {

		// cancel event and hover styling
		FileDragHover(e);

		// fetch FileList object
		var files = e.target.files || e.dataTransfer.files;

		// process all File objects
		for (var i = 0, f; f = files[i]; i++) {
            if(processFile(f) == true){
                UploadFile(f, counter);
                counter += 1;
            }
        }
	}

    //Check file
    function processFile(file){
        var type = types(file.type);

        if (file.type.indexOf("image") == 0 && type == true && file.size <= $id("MAX_FILE_SIZE").value) {
            var fileReader = new FileReader();
            fileReader.onload = function(a){
                a.target.result;

                Output(
                    '<div class="img" style="background: url(' + "'" + a.target.result + "'" +') no-repeat center center; background-size: cover"><span>' + file.name + '</span></div>'
                );
            }

            fileReader.readAsDataURL(file);



            addCover(file.name);

            return true;
        }else if(type == false){
            Output('<div class="imgError">' + file.name + '<br>Ikke støttet filtype!</div>');
            return false;
        }else if(file.size > $id("MAX_FILE_SIZE").value){
            Output('<div class="imgError">' + file.name + '<br>Filen er for stor</div>');
            return false;
        }
        return false;
    }

	// upload JPEG files
	function UploadFile(file, nr) {

		var xhr = new XMLHttpRequest();
		//if (xhr.upload && types(file.type) == true && file.size <= $id("MAX_FILE_SIZE").value) {
            var path = "../upload/save.php?ajax=on&aid="+aid+"&nr="+nr+"&year="+year+"&name="+name+"&mail="+mail+"&type="+file.type;

			// start upload
			xhr.open("POST", path, true);
            xhr.setRequestHeader("X_file_type", file.type);
			xhr.send(file);

		/*}else{
            alert('ikke alle krav er oppfylt');
        }*/
	}


	// initialize
	function Init() {

		var fileselect = $id("sti"),
			filedrag = $id("filedrag"),
			submitbutton = $id("submitbutton");

		// file select
		fileselect.addEventListener("change", FileSelectHandler, false);

		// is XHR2 available?
		var xhr = new XMLHttpRequest();
		if (xhr.upload) {

			// file drop
			filedrag.addEventListener("dragover", FileDragHover, false);
			filedrag.addEventListener("dragleave", FileDragHover, false);
			filedrag.addEventListener("drop", FileSelectHandler, false);
			filedrag.style.display = "block";
            fileselect.style.visibility = "hidden";

			// remove submit button
			submitbutton.style.display = "none";
		}

	}

	// call initialization file
	/*if (window.File && window.FileList && window.FileReader) {
		Init();
	}*/


//})();