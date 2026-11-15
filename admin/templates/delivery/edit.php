<?php

    $MDB->Query('SELECT * FROM dbm_mails_nodes WHERE id='.$_GET['id']);
    $res=$MDB->NextResult();
    $setup_categories = explode(',',$res['categories']);
    $MDB->Query('SELECT * FROM dbm_mails_categories ORDER BY id ASC');
    $categories = $MDB->getRecords();

?><form action="/admin/delivery.php" id="createForm" method="post" onsubmit="checkForm(this)">
    <input type="hidden" name="do" value="edit_mail"/>
    <input type="hidden" name="id" value="<?=$res['id']?>"/>
    <input type="hidden" name="resend" id="resend" value="0"/>
    <table cellpadding="0" cellspacing="5" border="0" width="100%">
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_subject"]?>:</strong></td>
            <td width="100%"><input type="text" name="subject" id="subject" value="<?=htmlspecialchars(stripslashes($res['subject']))?>" style="width: 100%" /></td>
        </tr>
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_body"]?>:</strong></td>
            <td><textarea id="mail_body" name="body" class="mceAdvancedmail_body" style="width: 100%; height: 400px"><?=(stripslashes($res['body']))?></textarea><iframe id="fr_mail_body"></iframe><a id="a_mail_body"></a></td>
        </tr>
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_categories"]?>:</strong></td>
            <td>

                <table border=0 cellpadding=0 cellspacing=0 border="0" class=nobacktab>
                    <?php  foreach($categories as $k=>$v){?>
                    <tr>
                        <td><input type="checkbox" name="categories[]" value="<?=$v['id']?>" <?php  if(in_array($v['id'],$setup_categories)){?>checked<?php  }?> ></td>
                        <td><?=$v['name']?></td>
                        </tr>
                        <?php  }?>
                </table>


            </td>
        </tr>
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_delivery_date"]?>:</strong></td>
            <td>

                <table border=0 cellpadding=0 cellspacing=0 border="0" class=nobacktab><tr>
                        <td><input type="text" size="20" value="<?=$res['date_send']?>" id="DELdate" name="DELdate"></td>
                        <?php /*<td><img src="/admin/js/calendar/img.gif" id="f_DELdate" style="cursor: pointer; border: 1px solid red;" title="Date selector"
                                 onmouseover="this.style.background='red';" onmouseout="this.style.background=''" /></td>*/?>
                </table>


            </td>
        </tr>
    </table>
</form>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "DELdate",     // id of the input field
        ifFormat       :    "%Y-%m-%d %H:%M:%S",      // format of the input field
		showsTime      :    true,
		timeFormat     :    "24",
        button         :    "f_DELdate",  // trigger for the calendar (button ID)
        align          :    "Tl",           // alignment (defaults to "Bl")
        singleClick    :    true
    });




</script>
<script type="text/javascript">
	$().ready(function() {
                //$('#mail_body')
                setupMCE('mail_body',false,true,false,'http://<?=$_SERVER['HTTP_HOST']?>/');
		/*$('textarea.tinymce').tinymce({
			// Location of TinyMCE script
			script_url : '/admin/tiny_mce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

			// Theme options
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			content_css : "css/content.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",

			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}
		});*/
	});
</script>
