<?php 
if($_GET['do']=='addnode_form'){
    $value = $CM_BASE_PARAMS['default_value'] ? true : false;
}else{
    $value = $CM_PARAMS['value'];
}
$cname = $CM_CONTROL_NAME;?>
<?=$AdminTrnsl["Yes"]?> <input type="radio" name="<?=$cname?>" value="1"<?php if ($value) {?> CHECKED<?php }?>>&nbsp;&nbsp;&nbsp;<?=$AdminTrnsl["No"]?> <input type="radio" name="<?=$cname?>" value="0"<?php if (!$value) {?> CHECKED<?php }?>>