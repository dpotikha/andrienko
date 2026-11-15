<?php

?>
<html>
	<head>
		<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
		<meta http-equiv="pragma" content="no-cache">
		<title>Admin Module</title>
		<link rel=stylesheet href="/admin/css.css" type="text/css" />
		<link rel=stylesheet href="/admin/jquery/css/smoothness/jquery-ui-1.7.2.custom.css" type="text/css" />
		<script type="text/javascript" src="/admin/jquery/js/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="/admin/jquery/js/jquery-ui-1.7.2.custom.min.js"></script>
		<script type="text/javascript" src="/admin/jquery/js/jquery.validate.js"></script>
		<script type="text/javascript" src="/admin/jquery/js/jquery.form.js"></script>

	</head>
	<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

		<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src="/admin/_img/s.gif" width="1" height="1"></td></table>
		<table width=100% border=0 cellpadding=4 cellspacing=0>
			<tr class=top41>
				<td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["Menu_Languages"]?></b></td>
			</tr>
		</table>



		<div id="tabs">
			<ul>
				<?php  global $SETUP_LANGUAGES,$SETUP_LANGUAGES_TITLE;foreach($SETUP_LANGUAGES as $k=>$lang){?>
				<li><a href="/admin/mlangs.php?do=get_content&lang=<?=$lang?>"><?=$SETUP_LANGUAGES_TITLE[$k]?></a></li>
				<?php  }?>
			</ul>

		</div>

		<div id="dialog"></div>
		<script type="text/javascript">
			function synchronize()
			{
				if(confirm('<?=$AdminTrnsl["Confirm_SyncVersions"]?>')){
					$.post('/admin/mlangs.php',{'do':'synchronize'},function(data){
						$("#tabs").tabs('load',TAB_INDEX);
					});
				}
			}
			function saveLang(lang)
			{

				var save_options1 = {
					success			:   successResponse1,
					beforeSubmit 	: 		beforeSubmit1
				};
				function beforeSubmit1(formData, jqForm, options) {
				}
				function successResponse1(responseText, statusText)  {
					if(responseText=='success')
					{
						$("#tabs").tabs('load',TAB_INDEX);
						$('#dialog').dialog('destroy');
					}else{
						alert(responseText);
					}
				}
				$("#frm_langs_"+lang).ajaxSubmit(save_options1);
			}
			function deleteLangItem(el)
			{
				if(confirm('<?=$AdminTrnsl["Confirm_DeleteTranslation"]?>')){
					$(el).parent().parent().remove();
				}
			}
			function addLangItem(lang, table_id,section_id)
			{
				$('#'+table_id).append('<tr>\n\
<?php  if($is_admin) {?><td><input type="checkbox"></td><?php  }?><td><input type="text" name="lang['+section_id+'][shortname][]" style="width:100%"></td>\n\
<td><input type="text" style="width:100%" name="lang['+section_id+'][name][]"></td>\n\
<td>&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="deleteLangItem(this); return false"><img src="/admin/_img/ico/delete.jpg"  /></td>\n\
</tr>');;
	}
	function saveSortable(lang){
		var result = $('#sortable_'+lang).sortable('toArray');
		$.post('/admin/mlangs.php',{'do':'save_sortable','lang':lang,'sort_items':result.join(',')},function(data){
			if(data=='success')
				$("#tabs").tabs('load',TAB_INDEX);
			else
				alert(data);
		});
	}
	function deleteSection(lang,section_shortname)
	{
		if(confirm('<?=$AdminTrnsl["Confirm_DeleteSection"]?>"'+section_shortname+'"?')){
			$.post('/admin/mlangs.php',{'do':'delete_section','lang':lang,'section_shortname':section_shortname},function(data){

				$("#tabs").tabs('load',TAB_INDEX);
			});

		}
	}
	var TAB_INDEX=0;
	$(function() {

		$('#tabs').tabs({
			select: function(event, ui) {
				TAB_INDEX=ui.index;
				return true;
			}
		});

	});
	function showCreateDialogSection(lang, title)
	{
		$('#dialog').html('<div class="shadow_right"><div class="backg_white" id="content">Please wait. Loading...</div></div>');
		$('#dialog').dialog({
			bgiframe: true,
			resizable:false,
			close:function(){
				$(this).dialog('destroy');
			},
			open:function(){
				$('.ui-dialog-buttonpane',$(this).parent()).append('<div class="corner_bottom_right"><div class="corner_bottom_left"></div></div>');
				$.post('/admin/mlangs.php?do=add_section&lang='+lang,{},function(data){
					$('#content',$('#dialog')).html(data);
				});

			},
			height:180,
			width:550,
			modal: true,
			closeText:'',
			overlay: {
				backgroundColor: '#000',
				opacity: 0.5
			},
			title: title,
			buttons: {
				'<?=$AdminTrnsl["Generic_Create"]?>': function() {

					var edit_options = {
						success			:   successResponse,
						beforeSubmit 	: 		beforeSubmit
					};
					function beforeSubmit(formData, jqForm, options) {
					}
					function successResponse(responseText, statusText)  {
						if(responseText=='success')
						{
							$("#tabs").tabs('load',TAB_INDEX);
							$('#dialog').dialog('destroy');
						}else{
							alert(responseText);
						}
					}

					if($("#frm_"+lang).valid())
					{
						$("#frm_"+lang).ajaxSubmit(edit_options);
					}


				},
				'<?=$AdminTrnsl["Generic_Cancel"]?>': function() {
					$(this).dialog('destroy');
				}
			}
		});

	}
	function showEditDialogSection(lang, section, title){
		$('#dialog').html('<div class="shadow_right"><div class="backg_white" id="content">Please wait. Loading...</div></div>');
		$('#dialog').dialog({
			bgiframe: true,
			resizable:false,
			close:function(){
				$(this).dialog('destroy');
			},
			open:function(){
				$('.ui-dialog-buttonpane',$(this).parent()).append('<div class="corner_bottom_right"><div class="corner_bottom_left"></div></div>');
				$.post('/admin/mlangs.php?do=edit_section&section='+section+'&lang='+lang,{},function(data){
					$('#content',$('#dialog')).html(data);
				});

			},
			height:180,
			width:550,
			modal: true,
			closeText:'',
			overlay: {
				backgroundColor: '#000',
				opacity: 0.5
			},
			title: title,
			buttons: {
				'<?=$AdminTrnsl["Save"]?>': function() {

					var edit_options = {
						success			:   successResponse,
						beforeSubmit 	: 		beforeSubmit
					};
					function beforeSubmit(formData, jqForm, options) {
					}
					function successResponse(responseText, statusText)  {
						if(responseText=='success')
						{
							$("#tabs").tabs('load',TAB_INDEX);
							$('#dialog').dialog('destroy');
						}else{
							alert(responseText);
						}
					}

					if($("#frm_"+lang+"_"+section).valid())
					{
						$("#frm_"+lang+"_"+section).ajaxSubmit(edit_options);
					}


				},
				'<?=$AdminTrnsl["Generic_Cancel"]?>': function() {
					$(this).dialog('destroy');
				}
			}
		});
	}
	function checkLocalesForDelete(lang,shortname,el)
	{
		var table = $('#table_'+lang+'_'+shortname);
		$(':checkbox',table).each(function(i){
			$(this).attr({'checked':el.checked});
		});
	}
	function deleteLocales(lang,shortname)
	{
		var table = $('#table_'+lang+'_'+shortname);
		var del=new Array();
		$(':checkbox',table).each(function(i){
				if(i>0){
					if($(this).attr('checked') || $(this).attr('checked')=='true')
					del[del.length]=$(this).val()
				}
		});
		if(del.length==0){
			alert('<?=$AdminTrnsl["Msg_SelectTranslationsToDelete"]?>');
			return;
		}
		if(confirm('<?=$AdminTrnsl["Confirm_DeleteSelectedTranslations"]?>')){
			$(':checkbox',table).each(function(i){
				if(i>0){
					if($(this).attr('checked') || $(this).attr('checked')=='true')
					$(this).parent().parent().remove();
				}else{
					$(this).attr({'checked':false});
				}
			});
		}
	}
		</script>
	</body>
</html>