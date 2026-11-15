<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/admin/prepend.php");
$q = mb_strtolower($_GET["q"]);
if (!$q) return;
$MDB->Query("SELECT id, name FROM dbm_nodes WHERE class_id = 212 AND name LIKE '" . db_escape($q) . "%'");
$c = $MDB->getRecords();
if ($c)
    foreach ($c as $k => $v) {
        echo $v['name'] . "|" . $v['id'] . "\n";
}
?>