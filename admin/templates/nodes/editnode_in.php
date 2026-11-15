<script type="text/javascript">
    function showPoint(fieldName) {
        form = document.forms['node_form'];
        window.open('/admin/show_point.php?c=' + form.elements[fieldName].value ,'cowindow','width=1300,height=1000,left=0,top=0,screenX=0,screenY=100')
    }
</script>
<?php  if ($nodeClass->checkFlag("USE_NODENAME") || sizeof($fieldDefs) != 0) { ?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<form action="/admin/nodes.php" method="POST" name="node_form" onsubmit="return <?=(!$restrict_edit || $ndMap->hasWriteRight)?"true":"false"?>" enctype="multipart/form-data">
<input type="hidden" name="do" value="updatenode">
<input type="hidden" name="id" value="<?=$node->id?>">
<input type="hidden" name="moder_id" value="<?=$moder_action->id?>">

<?php  if(true) { ?>
<tr>
<td><nobr><b><?=$AdminTrnsl["Node_path"]?></b></nobr>&nbsp;</td>
<td width=80% id=back3 height=28><?=$node->absolutePath?></td>
</tr>
<?php  } ?>

<?php  if ($nodeClass->checkFlag("USE_NODENAME")) { ?>
<tr>
<td><nobr><b><?=($nodeClass->nodeNameLabel ? $nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"])?></b><span id=red>*</span></nobr></td>
<td width=80% id=back3>
<?php  if (!$restrict_edit || !$nodeClass->checkFlag("NODENAME_READONLY")) { ?>
	<input type="text" style="width:100%"  name="NodeName" value="<?=prepareStringForHtml($NodeName)?>"> <span class=errmsg><?=$validationErrors['NodeName']?></span>
<?php  } else { ?>
	<?=prepareStringForHtml($NodeName)?>
	<input type="hidden" name="<?= $fieldNamePrefix ?>NodeName" value="<?= prepareStringForHtml($NodeName) ?>">
<?php  } ?>
</td>
</tr>
<?php  } else {?>
<input type="hidden" name="<?= $fieldNamePrefix ?>NodeName" value="<?= prepareStringForHtml($NodeName) ?>">
<?php  } ?>

<?php 
foreach ($fieldDefs as $item) {
	$request->setAttribute("CM_BASE_PARAMS", $item->fieldDef->fieldParameters);
	$request->setAttribute("CM_PARAMS", $item->params);
	$request->setAttribute("CM_CONTROL_NAME", $item->fieldDef->shortname);
?>
<tr valign=top id=back2 <?= $CurrentAdminUser->group->id != 1 && $item->fieldDef->hide ? 'style="display:none;"' : '' ?>>
<td id=pad5><nobr><b><?=$item->fieldDef->name?></b> <?php if ($item->fieldDef->required) {?><span id=red>*</span><?php }?>&nbsp;</nobr><nobr><span class=small><?php if(!$restrict_edit){?>[<?=$item->fieldDef->shortname?>]<?php }?></span></nobr></td>
<td id=back3 width=80%>
<div class="errmsg"><?=$validationErrors[$item->fieldDef->shortname]?></div>
<?php  if (in_array($item->fieldDef->shortname, array('coordinates', 'map_center'))) { ?>

    <a href="javascript:;" onClick="window.open('/select_point.php?admin_coordinates=2&c=<?=$item->params['strvalue']?>&field_id=<?=$item->fieldDef->shortname?><?=$map_center?>&node_id=<?=$node->id?>','cowindow','width=1300,height=1000,left=0,top=0,screenX=0,screenY=100')"><?=$AdminTrnsl["Select"]?></a>
    <!--<a href="javascript:;" onClick="showPoint('<?=$item->fieldDef->shortname?>')">Показать</a>-->
<?php }?>
<?php  usetemplate("fields_controls/".$item->jspname) ?>
</td>
</tr>
<?php  } ?>

<tr id=back2 class=date>
<td align=right id=pad5><nobr><?=$AdminTrnsl["Node_Time_Created"]?></nobr></td>
<td id=back3 width=80%><?=$node->timeCreated->format(DATETIME_FORMAT)?></td>
</tr>
<tr id=back2 class=date>
<td align=right id=pad5><nobr><?=$AdminTrnsl["Node_Time_Updated"]?></nobr></td>
<td id=back3 width=80%><?=$node->timeUpdated->format(DATETIME_FORMAT)?></td>
</tr>
<?php  if (!$restrict_edit || $ndMap->hasWriteRight){?>
<tr id=back2>
<td>&nbsp;</td>
<td id=pad10>
<input type=submit class=button5 value="<?=($moder_action ? $AdminTrnsl["Submit_changes_from_moderation"] : $AdminTrnsl["Submit_changes"])?>" <?=$disabledControlIfUserHasNoWriteRights?>></td>
</tr>
<?php  }?>
</form>
</table>
<?php  } ?>