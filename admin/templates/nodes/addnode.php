<?php 
$restrict_edit = $CurrentAdminUser->group->restrictNodeEdit;
?>
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
<link rel=stylesheet href="/admin/js/calendar/calendar-win2k-cold-2.css" type="text/css">
<script type="text/javascript" src="/admin/js/calendar/calendar.js"></script>
<script type="text/javascript" src="/admin/js/calendar/ru.js"></script>
<script type="text/javascript" src="/admin/js/calendar/calendar-setup.js"></script>
<script type="text/javascript" src="/admin/js/tooltip.js"></script>
<?php /*<script language="javascript" src="/admin/js/jquery-latest.js"></script>
<link rel="stylesheet" type="text/css" href="/admin/js/timepicker.css" />
<script language="javascript" src="/admin/js/jquery.timePicker.js"></script>
*/?>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<?php if (!$userHasWriteRights) { $disabledControlIfUserHasNoWriteRights = " disabled=true"; }?>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top4>
<td height=40 id=pad18 colspan=2 class=head><?php  $i=0;foreach ($parents as $parent) {$i++;?><a href="/admin/nodes.php?do=main&id=<?=$parent->id?>" id=black><?=$parent->name?></a> : <?php }?><b><?=$AdminTrnsl["Creating_node_of_class"]?>:</b> <?=$nodeClass->name?><?php if(!$restrict_edit){?>&nbsp;[<?=$nodeClass->shortname?>]<?php }?></b></td>
</tr>
</table>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top42><td><img src=/admin/_img/s.gif width=1 height=5></td></table>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<td id=pad18 class=h4>
<form action="/admin/nodes.php" method="post" name="node_form" enctype="multipart/form-data">
<input type="hidden" name="do" value="addnode">
<input type="hidden" name="class_id" value="<?=$nodeClass->id?>">
<input type="hidden" name="parent_id" value="<?=$parent_id?>">
<input type="hidden" name="forms_count" value="<?= $formsCount ?>">

<input type="hidden" name="moder_id" value="<?=$moder_action->id?>">

<script>
	var formsCount = <?= $formsCount ?>;

	function addOneForm() {
		var mf = document.getElementById("wholeForm");
		var baseForm = document.getElementById("additionalForm0");
		var newFormHTML = baseForm.innerHTML + "";
		var newFormFormatterNumber = (formsCount > 9) ? "" + formsCount : "0" + formsCount;
		newFormHTML = newFormHTML.replace(/f00\_/gi, 'f' + newFormFormatterNumber + '_');
		mf.innerHTML = mf.innerHTML + "<span id='additionalForm" + formsCount + "'><hr>" + newFormHTML + "</span>";
		formsCount++;
		document.forms["node_form"].elements["forms_count"].value = formsCount;
	}

	function removeLastForm() {
		if (formsCount > 1) {
			formsCount--;
			var lastForm = document.getElementById("additionalForm" + formsCount);
			lastForm.outerHTML = "";
			document.forms["node_form"].elements["forms_count"].value = formsCount;
		}
	}
</script>
<?php  if ($nodeClass->checkFlag("USE_MULTIFORMS")) { ?>
<div id=pad5>
<input type="button" value="<?=$AdminTrnsl["Node_AddForm"]?>" onClick="addOneForm()" class=button1>
<input type="button" value="<?=$AdminTrnsl["Node_DeleteForm"]?>" onClick="removeLastForm()" class=button1>
</div>
<?php  } ?>
<span id="wholeForm">
<?php 
for ($formNumber = 0; $formNumber < $formsCount; $formNumber++) {
	$nodeProps = $nodePropsArray[$formNumber];
	$validationErrors = $validationErrorsArray[$formNumber];
	$fieldDefs = $fieldDefsArray[$formNumber];
	$formNumberFormatted = sprintf("%02d", $formNumber);
	$fieldNamePrefix = "f" . $formNumberFormatted . "_";
?>
<span id="additionalForm<?= $formNumber ?>">
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<?php  if ($parent != null && $nodeClass->checkFlag("USE_SHORTNAME")) { ?>
<tr>
<td><?=$AdminTrnsl["Shortname"]?>:&nbsp;</td>
<td width=80% id=back3><input type="text"  style="width: 100%" name="<?= $fieldNamePrefix ?>NodeShortname" value="<?=$nodeProps->NodeShortname?>"> <span class=errmsg><?=$validationErrors['NodeShortname']?></span></td>
</tr>
<?php  } ?>
<?php  if (!$restrict_edit && $nodeClass->checkFlag("USE_ADMINURL_AND_TEMPLATE")) { ?>
<tr>
<td><?=$AdminTrnsl["Dynamic_Template"]?>:&nbsp;</td>
<td width=80% id=back3><input type="text" style="width: 100%" name="<?= $fieldNamePrefix ?>NodeDynamicTemplate" value="<?=$nodeProps->NodeDynamicTemplate?>"> <span class=errmsg><?=$validationErrors['NodeDynamicTemplate']?></span></td>
</tr>
<tr>
<td><?=$AdminTrnsl["Admin_URL"]?>:&nbsp;</td>
<td width=80% id=back3><input type="text" style="width: 100%" name="<?= $fieldNamePrefix ?>NodeAdminURL" value="<?=$nodeProps->NodeAdminURL?>"> <span class=errmsg><?=$validationErrors['NodeAdminURL']?></span></td>
</tr>
<?php  } else { ?>
<input type="hidden" name="<?= $fieldNamePrefix ?>NodeDynamicTemplate" value="<?=$nodeProps->NodeDynamicTemplate?>">
<input type="hidden" name="<?= $fieldNamePrefix ?>NodeAdminURL" value="<?=$nodeProps->NodeAdminURL?>">
<?php  } ?>
</table>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>
<?php  if ($nodeClass->checkFlag("USE_NODENAME")) { ?>
<tr id=back2>
<td><b><?=($nodeClass->nodeNameLabel ? $nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"])?></b><span id=red>*</span>&nbsp;</td>
<td width=80% id=back3><input type="text" style="width: 100%" name="<?= $fieldNamePrefix ?>NodeName" value="<?=prepareStringForHtml($nodeProps->NodeName)?>"> <span class=errmsg><?=$validationErrors['NodeName']?></span></td>
</tr>
<?php  } else {?>
<input type="hidden" name="<?= $fieldNamePrefix ?>NodeName" value="<?= prepareStringForHtml($nodeClass->name) ?>">
<?php  } ?>
<?php  foreach ($fieldDefs as $item) { 
//	print_r($item->params);
?>
<?php $request->setAttribute("CM_BASE_PARAMS", $item->fieldDef->fieldParameters);?>
<?php $request->setAttribute("CM_PARAMS", $item->params);?>
<?php $request->setAttribute("CM_CONTROL_NAME", $fieldNamePrefix . $item->fieldDef->shortname);?>
<tr valign=top id=back2 <?= $CurrentAdminUser->group->id != 1 && $item->fieldDef->hide ? 'style="display:none;"' : '' ?>>
<td id=pad5><nobr><b><?=$item->fieldDef->name?></b> <?php if ($item->fieldDef->required) {?><sup><font color=red>*</font></sup><?php }?>&nbsp;</nobr><nobr><span class=small><?php if(!$restrict_edit){?>[<?=$item->fieldDef->shortname?>]<?php }?></td>
<td id=back3 width=80%>
<div class="errmsg"><?=$validationErrors[$item->fieldDef->shortname]?></div>
<?php  if (in_array($item->fieldDef->shortname, array('coordinates'))) { ?>
    <a href="javascript:;" onClick="window.open('/select_point.php?admin_coordinates=2&field_id=f00_<?=$item->fieldDef->shortname?>','cowindow','width=1300,height=1000,left=0,top=0,screenX=0,screenY=100')"><?=$AdminTrnsl["Select"]?></a>
<?php }?>
<?php usetemplate("fields_controls/".$item->jspname);?>
</td></tr>
<?php  } ?>
</table>
</span>
<?php  } ?>
</span>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>
<tr valign=top id=back2>
<td>&nbsp;</td>
<td id=pad10>
<input type=submit value="<?=($moder_action ? $AdminTrnsl["Create_node_from_moderation"] : $AdminTrnsl["CreateSubnode"] . ' ' . $nodeClass->name)?>" style="<?php /*width:150px*/?>" class=button5 <?=$disabledControlIfUserHasNoWriteRights?>>
</td></tr>
</table>

</td></tr>
</form>
</table>
</body>
</html>