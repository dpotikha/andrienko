var root1 = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null; 
if (root1)
{ 
 // if (root1.addEventListener) root1.addEventListener("load", tinyOnload, false); 
  //else if (root1.attachEvent) root1.attachEvent("onload", tinyOnload); 
}
function setupMCE(id,is_simple,is_skip) {

if(is_skip||confirm("Enable editor?")){	
	document.getElementById('fr_'+id).style.display='none';
	document.getElementById('a_'+id).style.display='none';
	document.getElementById(id).style.display='';
	if(is_simple){
		tinyMCE.init({
		mode : "textareas",
		editor_selector : "mceAdvanced"+id,
		theme : "advanced",
		content_css : "/editor.css",
		plugins : "paste,fullscreen,inlinepopups",
	
		theme_advanced_buttons1 : "mymenubutton,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,undo,redo,|,code,fullscreen,|,cleanup,removeformat",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		paste_use_dialog : false,
			paste_auto_cleanup_on_paste : true,
			paste_convert_headers_to_strong : false,
			paste_strip_class_attributes : "all",
			paste_remove_spans : false,
			language: "en",
			paste_auto_cleanup_on_paste : true,
			paste_remove_styles : true
			
		});
	}else
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		editor_selector : "mceAdvanced"+id,
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",


		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,|,newdocument,preview,print",
		theme_advanced_buttons2: "cut,copy,paste,pastetext,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,media,|,fullscreen,help",
		theme_advanced_buttons3 : "tablecontrols,|,hr,visualaid,|,sub,sup,|,charmap,|,code,|,cleanup,removeformat",

		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		theme_advanced_blockformats : "p,h1,h2,h3,h4,h5,h6",

		// Example content CSS (should be your site CSS)
		content_css : "/editor.css",
		relative_urls : false,
		paste_auto_cleanup_on_paste : true,
		paste_use_dialog : false,
		theme_advanced_resize_horizontal : false,
		apply_source_formatting : false,

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",
		file_browser_callback : "fileBrowserCallBack",
		
		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
}
}
function fileBrowserCallBack(field_name, url, type, win) {
	
		var connector = "/admin/tiny_mce/filemanager/browser.html?Connector=connectors/php/connector.php";
		var enableAutoTypeSelection = true;
		
		var cType;
		tinymcpuk_field = field_name;
		tinymcpuk = win;
		
		switch (type) {
			case "image":
				cType = "Image";
				break;
			case "flash":
				cType = "Flash";
				break;
			case "file":
				cType = "File";
				break;
		}
		
		if (enableAutoTypeSelection && cType) {
			connector += "&Type=" + cType;
		}
		
		window.open(connector, "tinymcpuk", "modal,width=600,height=400");
	}
function mce_iframe_onload(editor_id,is_simple)
		{
			if(document.getElementById("fr_"+editor_id))
			document.getElementById("fr_"+editor_id).contentWindow.document.body.innerHTML=document.getElementById(editor_id).value;
			if(document.getElementById("fr_"+editor_id))
			document.getElementById("fr_"+editor_id).contentWindow.document.body.onclick = function() {
				
					setupMCE(editor_id,is_simple);
			
			}
		}
if(!document.getElementById('UsePasteFrame'))
document.write('<iframe id="UsePasteFrame" src="/admin/tiny_mce/plugins/paste/blank1.html" style=" width: 1px; height: 1px;" dir="ltr" wrap="soft" frameborder="0" ></iframe>');