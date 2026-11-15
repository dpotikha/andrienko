<table border="0" cellspacing="5" cellpadding="0">
<?php  $i=0; foreach ($CM_PARAMS["available_values"] as $avail_value) {?>
<tr><td><?=$avail_value?></td>
<?php $checked = false; foreach($CM_PARAMS["values"] as $value) {if($value == $i) {$checked = true;}}?>
<td>
    <input disabled type="checkbox" <?php if($checked){?> checked<?php }?>>
    <input style="display:none;" type="checkbox" name="<?=$CM_CONTROL_NAME?>[]" value="<?=$i?>"<?php if($checked){?> checked<?php }?>>
</td></tr>
<?php  $i++;}?>
<?php if ($CM_PARAMS["haveOther"]){?>
<tr><td><?=$CM_PARAMS["other_name"]?></td>
<td><?=$CM_PARAMS["other"]?><input style="display:none;" type=text name=<?=$CM_CONTROL_NAME?> value="<?=$CM_PARAMS["other"]?>"></td></tr>
<?php }?>
</table>