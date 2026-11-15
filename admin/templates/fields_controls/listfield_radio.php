<table border=0 cellpadding=0 cellspacing=0 border=0 class=nobacktab>
<tr align="center">
<?php  foreach ($CM_PARAMS["available_values"] as $avail_value) {?>
	<td style="padding:0 5px 0 5px"><?=$avail_value?></td>
<?php }?>
</tr><?php if ($CM_PARAMS["haveOther"]){?><td align=center><?=$CM_PARAMS["other_name"]?></td><?php }?>
<tr align="center">
<?php  $i=0;foreach ($CM_PARAMS["available_values"] as $avail_value) {?>
	<td><input type="radio" name="<?=$CM_CONTROL_NAME?>" value="<?=$i?>"<?php if(sizeof($CM_PARAMS["values"])!=0 && $CM_PARAMS["values"][0] == $i){?> checked<?php }?>></td>
<?php $i++;}?>
<?php if ($CM_PARAMS["haveOther"]){?><td><input type=text name=<?=$CM_CONTROL_NAME?> value="<?=$CM_PARAMS["other"]?>"></td><?php }?>
</tr></table>
