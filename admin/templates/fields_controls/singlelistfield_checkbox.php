<table border=0 cellpadding=0 cellspacing=0 border=0 class=nobacktab>
<tr><td>1
<?php  $i=0; foreach ($CM_PARAMS["available_values"] as $avail_value) {?>
<?= $avail_value ?><input type="checkbox" name="<?=$CM_CONTROL_NAME?>[]" value="<?=$i?>"<?php if($checked){?> checked<?php }?>>
<?php  }?>
<?php  /*?><select name="<?= $CM_CONTROL_NAME ?>">
<option value="-1"><?= $CM_PARAMS["any_el_name"] ?></option>
<? $i=0; foreach ($CM_PARAMS["available_values"] as $avail_value) { ?>
<option value="<?= $i ?>"<? if($CM_PARAMS["value"] == $i) { ?> SELECTED<? } ?>><?= $avail_value ?></option>
<? $i++; }?>

</select>
<? */?>
</td>
</tr></table>
<?php  /*?>
<table border="0" cellspacing="5" cellpadding="0">
<? $i=0; foreach ($CM_PARAMS["available_values"] as $avail_value) {?>
<tr><td><?=$avail_value?></td>
<?$checked = false; foreach($CM_PARAMS["values"] as $value) {if($value == $i) {$checked = true;}}?>
<td><input type="checkbox" name="<?=$CM_CONTROL_NAME?>[]" value="<?=$i?>"<?if($checked){?> checked<?}?>></td></tr>
<? $i++;}?>
<?if ($CM_PARAMS["haveOther"]){?>
<tr><td><?=$CM_PARAMS["other_name"]?></td>
<td><input type=text name=<?=$CM_CONTROL_NAME?> value="<?=$CM_PARAMS["other"]?>"></td></tr>
<?}?>
</table><? */?>