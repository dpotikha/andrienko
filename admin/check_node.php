<?php

function checkNodeOnRemove($node_id) {
    global $MDB;
    $MDB->QDel("dbm_comments", "node_id = $node_id");
    $MDB->QDel("dbm_seo", "node_id = $node_id");
    $node = QNode::getInstance($node_id);
    $parent = QNode::getInstance($node->parent_id);

    $parent = QNode::getInstance($node->parent_id);
    $MDB->QIns('dbm_log', array(
        'user_id' => $_SESSION['CurrentAdminUser']->id,
        'activity' => 4,
        'node_id' => $node->id,
        'node_name' => $node->name,
        'parent_id' => $parent->id,
        'parent_name' => $parent->name,
        'ip' => QURL::getClientIP(),
        'server' => jsonServerVars(),
        'date' => date('Y-m-d H:i:s')
    ));
}

function checkNodeName(&$NodeName, $node) {
    global $MDB;
    $parent = QNode::getInstance($node->parent_id);
    if ($node->dynamicTemplate == 'question_faq') {
        //edump($_POST);
        if ($_POST['theme'] > 0 && $_POST['faquser'] > 0 && strlen($_POST['answer'])) {
            //edump($node->fields['faquser']->value);
            $themeNode=QNode::getInstance($_POST['theme']);
            $newNode=QNode::createNode($themeNode->getHref(), $node->name, '', 'question', array(
                'username'=>$_POST['username'],
                'answer'=>$_POST['answer'],
                'faquser'=>$_POST['faquser']
            ));

            $MDB->QIns('dbm_log', array(
                'user_id' => $_SESSION['CurrentAdminUser']->id,
                'activity' => 3,
                'node_id' => $newNode->id,
                'node_name' => $newNode->name,
                'parent_id' => $themeNode->id,
                'parent_name' => $themeNode->name,
                'ip' => QURL::getClientIP(),
                'server' => jsonServerVars(),
                'date' => date('Y-m-d H:i:s')
            ));
            $MDB->QIns('dbm_log', array(
                'user_id' => $_SESSION['CurrentAdminUser']->id,
                'activity' => 4,
                'node_id' => $node->id,
                'node_name' => $node->name,
                'parent_id' => $parent->id,
                'parent_name' => $parent->name,
                'ip' => QURL::getClientIP(),
                'server' => jsonServerVars(),
                'date' => date('Y-m-d H:i:s')
            ));
            Node::remove($node->id);
            QUrl::redirect('/admin/nodes.php?do=main&id='.$newNode->id);
            
        }
    }
    $MDB->QIns('dbm_log', array(
        'user_id' => $_SESSION['CurrentAdminUser']->id,
        'activity' => 2,
        'node_id' => $node->id,
        'node_name' => $node->name,
        'parent_id' => $parent->id,
        'parent_name' => $parent->name,
        'ip' => QURL::getClientIP(),
        'server' => jsonServerVars(),
        'date' => date('Y-m-d H:i:s')
    ));
}

function checkNodeNameByClass(&$NodeName, $nodeClass, $fieldList) {
    
}

?>