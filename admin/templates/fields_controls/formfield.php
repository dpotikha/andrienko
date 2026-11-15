<table border=0 cellpadding=0 cellspacing=0 border=0 class=nobacktab>
<tr><td><select name="<?=$CM_CONTROL_NAME?>">
<option value="0"><?=$AdminTrnsl["FormFieldSelectForm"]?>
<?php  $i=0;foreach ($CM_PARAMS["all_forms"] as $form) {?>
<option value="<?=$form->id?>"<?php if($CM_PARAMS["form_id"] == $form->id){?> SELECTED<?php }?>><?=$form->name?><?php $i++;}?>
</select></td>
</tr></table>
