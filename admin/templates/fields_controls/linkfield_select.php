<?php 
$rootNode = &Node::findByPath($CM_PARAMS["root_node_path"]);
$availableValues = $rootNode->getRecursiveChildrenOfClasses(explode(",", $CM_PARAMS["allowed_classes"]));
?>
<select name="<?= $CM_CONTROL_NAME ?>">
<option value="">- - -
<?php  foreach ($availableValues as $avail_value) { ?>
<option value="<?=$avail_value->absolutePath?>"<?php  if ($CM_PARAMS["linked_node_path"] == $avail_value->absolutePath) { ?> SELECTED<?php  } ?>><?=$avail_value->name?>
<?php  } ?>
