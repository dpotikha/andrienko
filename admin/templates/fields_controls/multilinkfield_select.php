<?php 
$node=Node::findById($request->getParameter("id"));
$topNode=QNode::getTopSection($node);
$rootNode = &Node::findByPath(str_replace('{LANG}',$topNode->shortname,$CM_PARAMS["root_node_path"]));
$availableValues = $rootNode->getRecursiveChildrenOfClasses(explode(",", $CM_PARAMS["allowed_classes"]));
?>
<select multiple="multiple" size="10" name="<?=$CM_CONTROL_NAME?>[]">
<?php  for ($i = 0; $i < sizeof($availableValues); $i++) { $node = &$availableValues[$i]; ?>
<?php  $checked = in_array($node->id, $CM_PARAMS["linked_node_ids"]); ?>
<option value="<?=$node->id?>" <?php if($checked){?> selected<?php }?>><?= $node->name ?></option>
<?php  } ?>
</select>