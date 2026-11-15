<?php


?><style type="text/css">
	#sortable_<?=$lang?> { list-style-type: none; margin: 0; padding: 0; width: 60%; }
	#sortable_<?=$lang?> li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1em; height: 18px; }
	#sortable_<?=$lang?> li span { position: absolute; margin-left: -1.3em; }
</style>
<script type="text/javascript">
	$(function() {
		$("#sortable_<?=$lang?>").sortable();
		$("#sortable_<?=$lang?>").disableSelection();
	});
</script>
<script type="text/javascript">
	$(function() {
		$("#accordion_<?=$lang?>").accordion({
			icons: {
				header: "ui-icon-circle-arrow-e",
				headerSelected: "ui-icon-circle-arrow-s"
			},
			autoHeight: false

		});
	});
</script>

<?php  if($is_admin) {?>
<button type="button" class="ui-state-default ui-corner-all" onclick="showCreateDialogSection('<?=$lang?>', '<?=$AdminTrnsl["Mlang_CreateSection"]?>')"><?=$AdminTrnsl["Mlang_AddSection"]?></button>
<input type="checkbox" value="1" onclick="if(this.checked){$('#sortable_div_<?=$lang?>').show();$('#accordion_div_<?=$lang?>').hide();}else{$('#sortable_div_<?=$lang?>').hide();$('#accordion_div_<?=$lang?>').show();}"> <?=$AdminTrnsl["Mlang_SortMode"]?>
<input type="checkbox" value="1" onclick="synchronize()"> <?=$AdminTrnsl["Mlang_SyncVersions"]?>
<br /><br />
<div id="sortable_div_<?=$lang?>" style="display:none">
	<ul id="sortable_<?=$lang?>">
			<?php  foreach($locale as $k=>$v) {
				
				?>
		<li id="<?=$k?>" class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><?=$v['title'].'['.$k.']' ?></li>
			<?php  }?>
	</ul>
	<br/><br/>
	<button type="button" class="ui-state-default ui-corner-all" onclick="saveSortable('<?=$lang?>')"><?=$AdminTrnsl["Mlang_SaveSort"]?></button>
</div>
<?php  }?>
<div id="accordion_div_<?=$lang?>">
	<form action="/admin/mlangs.php" method="post" id="frm_langs_<?=$lang?>" onsubmit="return false">
		<input type="hidden" name="do" value="save_lang" />
		<input type="hidden" name="lang_id" value="<?=$lang?>" />
		<div id="accordion_<?=$lang?>">
			<?php  foreach($locale as $k=>$v) {
				//$locale_info=explode('::',$k);
			?>

			<h3><a href="#"><?php  if($is_admin) {?>&nbsp;<img src="/admin/_img/ico/edit.jpg" onclick="showEditDialogSection('<?=$lang?>', '<?=$k?>','<?=$AdminTrnsl["Mlang_EditSectionTitle"]?><?=addslashes($v['title'])?> '); return false" />&nbsp;&nbsp;&nbsp;<img src="/admin/_img/ico/delete.jpg" onclick="deleteSection('<?=$lang?>','<?=$k?>'); return false" /><?php  }?> &nbsp;&nbsp; <b><?=trim($v['title'])?></b> [<?=$k?>]</a></h3>
			<div>
				<table cellpadding="5" cellspacing="0" border="1" width="100%" id="table_<?=$lang?>_<?=$k?>">
					<tr>
						<?php  if($is_admin) {?><td style="width:40px"><input type="checkbox"  onclick="checkLocalesForDelete('<?=$lang?>','<?=$k?>',this);"><a href="javascript:;" onclick="deleteLocales('<?=$lang?>','<?=$k?>'); return false"><img src="/admin/_img/ico/delete.jpg"  /></a></td><?php  }?>
						<td style="width:15%"><b><?=$AdminTrnsl["Shortname_Label"]?></b></td>
						<td><b><?=$AdminTrnsl["Mlang_Translation"]?></b></td>
						<?php  if($is_admin) {?><td style="width:10%"><b><?=$AdminTrnsl["Action"]?></b></td><?php  }?>
					</tr>
						<?php  if(is_array($v['locale']))foreach($v['locale'] as $k1=>$v1) {?>
					<tr>
						<?php  if($is_admin) {?><td><input type="checkbox"></td><?php  }?>
						<td><input type="text" name="lang[<?=$k?>][shortname][]" <?php  if(!$is_admin) {?>readonly<?php  }?> value="<?=$k1?>" style="width:100%" /></td>
                                                <td><input type="text" name="lang[<?=$k?>][name][]" value="<?=htmlspecialchars(stripslashes($v1))?>"  style="width:100%"/></td>
						<?php  if($is_admin) {?><td>&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="deleteLangItem(this); return false"><img src="/admin/_img/ico/delete.jpg"  /></a></td><?php  }?>
					</tr>
							<?php  }?>
				</table>
					<?php  if($is_admin) {?>
				<br/><br/>
				<button type="button" class="ui-state-default ui-corner-all" onclick="addLangItem('<?=$lang?>','table_<?=$lang?>_<?=$k?>','<?=$k?>')"><?=$AdminTrnsl["Mlang_AddTranslation"]?></button>
				<br/><br/>
					<?php  }?>
			</div>
	
			<?php  }?>

		</div>
	</form>
	<br/><br/>
	<button type="button" class="ui-state-default ui-corner-all" onclick="saveLang('<?=$lang?>')"><?=$AdminTrnsl["Mlang_SaveLangFile"]?></button>
	<br/><br/>
</div>
