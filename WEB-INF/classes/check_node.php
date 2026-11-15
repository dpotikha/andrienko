<?php

function checkNodeAfterCreate(&$nodeClass, $node_id) {
	global $QLOCALES,$SETUP_LANGUAGES, $MDB;
	if(USE_LANG && empty($QLOCALES)) {
		foreach($SETUP_LANGUAGES as $lang) {
			$QLOCALES[$lang]=QLANG($lang);
		}
	}
        $node = QNode::getInstance($node_id);
    $parent=QNode::getInstance($node->parent_id);
    $MDB->QIns('dbm_log', array(
        'user_id' => $_SESSION['CurrentAdminUser']->id,
        'activity' => 3,
        'node_id' => $node_id,
        'node_name'=>$node->name,
        'parent_id'=>$parent->id,
        'parent_name'=>$parent->name,
        'ip' => QURL::getClientIP(),
        'server' => jsonServerVars(),
        'date' => date('Y-m-d H:i:s')
    ));
	
}

?>