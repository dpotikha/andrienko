<?php 
if($_GET['do']=='addnode_form'){
    $value = $CM_BASE_PARAMS['default_value'];
}else{
    $value = $CM_PARAMS['strvalue'];
}
?><input type="<?=$CM_PARAMS['inputtype']?>" style="width:100%" style="width:495px" value="<?=prepareStringForXML($value)?>" name="<?=$CM_CONTROL_NAME?>">