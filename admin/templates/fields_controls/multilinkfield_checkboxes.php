<?php 
$rootNode = Node::findByPath($CM_PARAMS["root_node_path"]);
$availableValues = $rootNode->getRecursiveChildrenOfClasses(explode(",", $CM_PARAMS["allowed_classes"]));
if (preg_match('/subregion/iU',$CM_CONTROL_NAME) || preg_match('/trademarks/iU',$CM_CONTROL_NAME)) {
    $arr = array();
    foreach ($availableValues as $k => $v) {
        $arr[$v->name] = $v;
    }
    ksort($arr);
    $availableValues = $arr;
    ?>

    <style>
        div.input_checkbox_float {float:left; min-width:300px; }
    </style>

    <?php 
    $i = 0;
    foreach ($availableValues as $k => $node) {

        $i++;
        ?>

        <?php  $checked = in_array($node->id, $CM_PARAMS["linked_node_ids"]); ?>
        <div class="input_checkbox_float" style="float:left; min-width:300px;">
            <input type="checkbox" id="<?= $CM_CONTROL_NAME ?>_<?= $node->id ?>" name="<?= $CM_CONTROL_NAME ?>[]" value="<?= $node->id ?>" <?php  if ($checked) { ?> checked <?php  } ?>>
            <label for="<?= $CM_CONTROL_NAME ?>_<?= $node->id ?>"><?= $node->name ?><?= $node->tfields["email"] ? " (" . $node->tfields["email"] . ")" : "" ?></label>
        </div>
    <?php  } ?>
<?php  } else { ?>
    <?php 
    for ($i = 0; $i < sizeof($availableValues); $i++) {
        $node = &$availableValues[$i];
        ?>
        <div class="input_checkbox_float" style="float:left; min-width:300px;">
            <?php  $checked = in_array($node->id, $CM_PARAMS["linked_node_ids"]); ?>
            <input type="checkbox" id="<?= $CM_CONTROL_NAME ?>_<?= $node->id ?>" name="<?= $CM_CONTROL_NAME ?>[]" value="<?= $node->id ?>" <?php  if ($checked) { ?> checked <?php  } ?>>
            <label for="<?= $CM_CONTROL_NAME ?>_<?= $node->id ?>"><?= $node->name ?><?= $node->tfields["email"] ? " (" . $node->tfields["email"] . ")" : "" ?></label>
        </div>
    <?php  } ?>


<?php  } ?>