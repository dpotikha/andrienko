<?php 
if($_GET['do']=='addnode_form'){
    $value = $CM_BASE_PARAMS['default_value'];
}else{
    $value = $CM_PARAMS['strvalue'];
}
?><input type="hidden" value="<?=prepareStringForXML($value)?>" name="<?=$CM_CONTROL_NAME?>">
<?= $value ?>