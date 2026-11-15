if(typeof(configMCE)=='undefined')
{
	var root1 = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;
	var valid_elements = "@[id|class|style|align|title|dir<ltr?rtl|lang|xml::lang|onclick|ondblclick|"
+ "onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|"
+ "onkeydown|onkeyup],a[rel|rev|charset|hreflang|tabindex|accesskey|type|"
+ "name|href|target|title|class|onfocus|onblur],strong,b,em,i,strike,u,"
+ "#p,-ol[type|compact],-ul[type|compact],-li,br,img[longdesc|usemap|"
+ "src|border|alt=|title|hspace|vspace|width|height|align],-sub,-sup,"
+ "-blockquote,-table[border=0|cellspacing|cellpadding|width|frame|rules|"
+ "height|align|summary|bgcolor|background|bordercolor],-tr[rowspan|width|"
+ "height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot,"
+ "#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor"
+ "|scope],#th[colspan|rowspan|width|height|align|valign|scope],caption,-div,"
+ "-span,-code,-pre,address,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],-font[face"
+ "|size|color],dd,dl,dt,cite,abbr,acronym,del[datetime|cite],ins[datetime|cite],"
+ "object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width"
+ "|height|src|*],script[src|type],map[name],area[shape|coords|href|alt|target],bdo,"
+ "button,col[align|char|charoff|span|valign|width],colgroup[align|char|charoff|span|"
+ "valign|width],dfn,fieldset,form[action|accept|accept-charset|enctype|method],"
+ "input[accept|alt|checked|disabled|maxlength|name|readonly|size|src|type|value],"
+ "kbd,label[for],legend,noscript,optgroup[label|disabled],option[disabled|label|selected|value],"
+ "q[cite],samp,select[disabled|multiple|name|size],small,"
+ "textarea[cols|rows|disabled|name|readonly],tt,var,big";

	var configMCE={
		// General options
		mode : "textareas",
		theme : "advanced",
		skin : "hate",
		editor_selector : "mceAdvanced",

		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		theme_advanced_blockformats : "p,h1,h2,h3,h4,h5,h6",

		// Example content CSS (should be your site CSS)
		content_css : "/_css/editor.css?"+Math.random(),
		relative_urls : false,
		language:'ru',
		paste_auto_cleanup_on_paste : true,
		paste_use_dialog:false,
		paste_remove_styles_if_webkit: true,
		apply_source_formatting : true,
		valid_elements : valid_elements,
		paste_preprocess : function(pl, o) {
		// Content string containing the HTML from the clipboard
		 //alert(o.content);
		},
		paste_postprocess : function(pl, o) {
			// Content DOM node containing the DOM structure of the clipboard
			//alert(o.node.innerHTML);
			o.node.innerHTML=processCW([
				/<!--[\s\S]+?-->/gi,												// Word comments
				/<\/?(span)[^>]*>/gi,												// Span elements
				/<\/?(img|font|meta|link|style|div|v:\w+)[^>]*>/gi,					// Remove some tags including VML content
				/<\\?\?xml[^>]*>/gi,												// XML namespace declarations
				/<\/?o:[^>]*>/gi,													// MS namespaced elements <o:tag>
				/(id|style|class|name|lang|language|type|on\w+|v:\w+)=\"([^\"]*)\"/gi,				// on.., class, style and language attributes with quotes
				/(id|style|class|name|lang|language|type|on\w+|v:\w+)=(\w+)/gi,						// on.., class, style and language attributes without quotes (IE)
				[/<(\/?)s>/gi, '<$1strike>'],										// Convert <s> into <strike> for line-though
				/<script[^>]+>[\s\S]*?<\/script>/gi,								// All scripts elements for msoShowComment for example
				[/&nbsp;/g, '\u00a0']												// Replace nsbp entites to char since it's easier to handle
				],o.node.innerHTML);
				//alert(o.node.innerHTML);
		},


		theme_advanced_resize_horizontal : false,

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
	};
	function getSimpleConfig(configMCE, readonly)
	{
		configMCE.plugins = "paste,fullscreen,inlinepopups,advimage,advlink";
		configMCE.theme_advanced_buttons1= "mymenubutton,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,image,|,code,fullscreen,|,cleanup,removeformat";
		configMCE.theme_advanced_buttons2= "";
		configMCE.theme_advanced_buttons3= "";
                configMCE.convert_urls = false;
                configMCE.readonly = typeof readonly == 'undefined' ? 0 : readonly;
		return configMCE;
	}
	function getFullConfig(configMCE, readonly)
	{
		configMCE.plugins = "youtube,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template";
		configMCE.theme_advanced_buttons1 = "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,|,newdocument,preview,print,|,fullscreen,help";
		configMCE.theme_advanced_buttons2= "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,media,youtube";
		configMCE.theme_advanced_buttons3 = "tablecontrols,|,hr,visualaid,|,sub,sup,|,charmap,|,code,|,cleanup,removeformat,iframe[width|height|src|frameborder|allowfullscreen]";
                configMCE.convert_urls = false;
                configMCE.readonly = typeof readonly == 'undefined' ? 0 : readonly;
		return configMCE;
	}
	function setupMCE(id,is_simple,is_skip, readonly) {

		if(is_skip||confirm("Enable editor?")){
			document.getElementById('fr_'+id).style.display='none';
			document.getElementById('a_'+id).style.display='none';
			document.getElementById(id).style.display='';

			configMCE.editor_selector = "mceAdvanced"+id;
			if(is_simple){
				tinyMCE.init(getSimpleConfig(configMCE, readonly));
			}else{
				tinyMCE.init(getFullConfig(configMCE, readonly));
			}
		}
	}
	function processCW(items, content) {
		for (var k in items){
			var v=items[k];
			if (v.constructor == RegExp)
				content = content.replace(v, '');
			else{
				content = content.replace(v[0], v[1]);
			}
		}
		return content;
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
	function mce_iframe_onload(editor_id,is_simple,readonly)
	{
		if(document.getElementById("fr_"+editor_id)){
			document.getElementById("fr_"+editor_id).contentWindow.document.body.innerHTML=document.getElementById(editor_id).value;
		}
		if(typeof readonly == 'undefined' && document.getElementById("fr_"+editor_id)){
			document.getElementById("fr_"+editor_id).contentWindow.document.body.onclick = function() {
				setupMCE(editor_id,is_simple);
			}
		}
	}
}