	function ajaxFileUpload(){
		this.file="";
		this.form="";
		this.framename="";
		this.index=0;
		this._do="";
		this.create=function(file,options){
			if(typeof(options)!="undefined"){
				if(options.onStart!=null&&typeof(options.onStart)=="function")
					this.onStart=options.onStart;
				if(options.onComplete!=null&&typeof(options.onComplete)=="function")
					this.onComplete=options.onComplete;
			}
			this.form=file.form;
			this.file=file;
			if(typeof(AFUs)=="undefined"){
				AFUs=new Array();
			}
			this.index=AFUs.length;
			AFUs[AFUs.length]=this;
			this.framename='frame_' + Math.floor(Math.random() * 100000);
			var div = document.createElement('DIV');
			div.innerHTML = '<iframe style="display:none" src="about:blank" id="'+this.framename+'" name="'+this.framename+'" onload="AFUs['+this.index+'].frameOnLoad()"></iframe>';
			document.body.appendChild(div);
			this._do=this.form["do"].value;
			this.form["do"].value="afu"
			this.form.setAttribute('target',this.framename);
			this.onStart();
			this.onSubmit();
		}
		this.onSubmit=function(){
			this.form.submit();
		}
		this.onComplete=function(){
			
		}
		this.frameOnLoad=function(){
			var iframe = document.getElementById(this.framename);
			if (iframe.contentDocument) {
				var iframe_document = iframe.contentDocument;
			} else if (iframe.contentWindow) {
				var iframe_document = iframe.contentWindow.document;
			} else {
				var iframe_document = window.frames[this.framename].document;
			}
			if (iframe_document.location.href == "about:blank") {
				return;
			}
			this.form.setAttribute('target',"_self");
			this.form["do"].value=this._do;
			this.onComplete(iframe_document.body.innerHTML);
		}
		this.onStart=function(){}
		this.complete=function(data){}
	}