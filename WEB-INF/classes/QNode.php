<?php

class QNode {

    protected static $instance = NULL;
    private $node = NULL;

    public static function getInstance($id = 0, $use_lang = false) {
        if (!isset(self::$instance[$id]) || !self::$instance[$id] instanceof self) {
            self::$instance[$id] = new self($id, $use_lang);
        }
        return self::$instance[$id]->get();
    }

    public function __construct($id = 0, $use_lang = false) {
        if (preg_match('/^[0-9]+$/i', $id)) {
            $node = Node::findById($id);
        } else {
            if ($use_lang) {
                $node = Node::findByPath('/' . SITE_LOCALE . $id);
            } else {
                $node = Node::findByPath($id);
            }
        }
        if ($node) {
            $this->node = &$node;
        }
    }

    public function get() {
        return $this->node;
    }

    public static function getTopSection($node = null) {
        if (is_null($node)) {
            global $CurrentNode;
            $node = $CurrentNode;
        }
        $path = $node->absolutePath;
        $k = strpos($path, '/', 1);
        if ($k === false)
            $path = substr($path, 0);
        else
            $path = substr($path, 0, strpos($path, "/", 2));
        $res = QNode::getInstance($path); //Node::findByPath($path);
        return $res;
    }

    public static function getTopSectionS($path) {

        $k = strpos($path, '/', 1);
        if ($k === false)
            $path = substr($path, 0);
        else
            $path = substr($path, 0, strpos($path, "/", 2));
        $res = QNode::getInstance($path); //Node::findByPath($path);
        return $res;
    }

    public static function getL2pSection($node = null) {
        if (is_null($node)) {
            global $CurrentNode;
            $node = $CurrentNode;
        }

        $items = explode("/", $node->absolutePath);
        $items = array_pad($items, 3, ""); // missing levels were null (= "") in PHP 5.6 too
        if (true) { // was sizeof($items > 2): a typo that PHP 5.6 always evaluated as true
            $res = QNode::getInstance('/' . $items[1] . '/' . $items[2]); //Node::findByPath('/'.$items[1].'/'.$items[2]);
            return $res;
        }
        return null;
    }

    public static function getL3pSection($node = null) {
        if (is_null($node)) {
            global $CurrentNode;
            $node = $CurrentNode;
        }

        $items = explode("/", $node->absolutePath);
        $items = array_pad($items, 4, ""); // missing levels were null (= "") in PHP 5.6 too
        if (true) { // was sizeof($items > 3): a typo that PHP 5.6 always evaluated as true
            $res = QNode::getInstance('/' . $items[1] . '/' . $items[2] . '/' . $items[3]); //Node::findByPath('/'.$items[1].'/'.$items[2].'/'.$items[3]);
            return $res;
        }
        return null;
    }

    public static function getL4pSection($node = null) {
        if (is_null($node)) {
            global $CurrentNode;
            $node = $CurrentNode;
        }
        $items = explode("/", $node->absolutePath);
        $items = array_pad($items, 5, ""); // missing levels were null (= "") in PHP 5.6 too
        if (true) { // was sizeof($items > 4): a typo that PHP 5.6 always evaluated as true
            $res = QNode::getInstance('/' . $items[1] . '/' . $items[2] . '/' . $items[3] . '/' . $items[4]); //$res = Node::findByPath('/'.$items[1].'/'.$items[2].'/'.$items[3].'/'.$items[4]);
            return $res;
        }
        return null;
    }

    public static function getL5pSection($node = null) {
        if (is_null($node)) {
            global $CurrentNode;
            $node = $CurrentNode;
        }
        $items = explode("/", $node->absolutePath);
        $items = array_pad($items, 6, ""); // missing levels were null (= "") in PHP 5.6 too
        if (true) { // was sizeof($items > 5): a typo that PHP 5.6 always evaluated as true
            $res = QNode::getInstance('/' . $items[1] . '/' . $items[2] . '/' . $items[3] . '/' . $items[4] . '/' . $items[5]); //$res = Node::findByPath('/'.$items[1].'/'.$items[2].'/'.$items[3].'/'.$items[4]);
            return $res;
        }
        return null;
    }

    public static function createNode($path = '', $name = '', $shortname = '', $class = '', $fields = array()) {
        if (is_object($path))
            $path = $path->getHref();
        $pNode = Node::createWithDefaultValues0($path, $name, $shortname, $class);
        foreach ($fields as $k => $v) {
            //dump($v,$k);
            //$pNode->fields[$k]->setValue($v);
            if (!isset($pNode->fields[$k])) continue; // field not in this node class: PHP 5 only warned, PHP 8 throws
            $pNode->fields[$k]->value = $v;
        }
        $pNode->store();
        return $pNode;
    }

    public static function updateNode($node, $fields = array(), $name = '', $shortname = '') {
        if (mb_strlen($name)) {
            $node->name = $name;
        }
        if (mb_strlen($shortname)) {
            $node->shortname = $shortname;
        }
        foreach ($fields as $k => $v) {
            //dump($v,$k);
            //$pNode->fields[$k]->setValue($v);
            if (!isset($node->fields[$k])) continue; // field not in this node class: PHP 5 only warned, PHP 8 throws
            $node->fields[$k]->value = $v;
        }
        $node->store();
        return true;
    }

    public static function getImgPath($node) {
        global $MDB;
        $path = ''; // was undefined before the first append (a warning in PHP 8)
        $MDB->Query('SELECT n.id, n.shortname FROM dbm_nodes n, dbm_nodes_submission ns WHERE n.id=ns.parent_id AND ns.child_id=' . $node->id . ' AND NOT(ISNULL(n.parent_id)) ORDER BY ns.level_diff desc');
        $res = $MDB->getRecords();
        foreach ($res as $k => $v) {
            $path.='/' . (strlen($v['shortname']) > 32 ? CProtect::cut_shortname($v['shortname'],32) : $v['shortname']);
        }
        $path.='/' . (strlen($node->shortname) > 32 ? CProtect::cut_shortname($node->shortname, 32) : $node->shortname);
        return $path;
    }

}

//////////////////////////////////////////////////////////////////////////////////////////////
?>