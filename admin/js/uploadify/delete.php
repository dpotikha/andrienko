<?php

function getmicrotime1() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
require_once(__DIR__ . "/upload_guard.php");
$node_id = (int) $request->getParameter("node_id");
$id = $request->getParameter("id");
if (isset($_SESSION['multiphoto'][$node_id][$id])) {
    @unlink($_SERVER['DOCUMENT_ROOT'].'/s/tmp/multiphoto/'.session_id().'/'. $node_id .'/thumb_'.$id);
    @unlink($_SERVER['DOCUMENT_ROOT'].'/s/tmp/multiphoto/'.session_id().'/'. $node_id .'/crop_'.$id);
    @unlink($_SERVER['DOCUMENT_ROOT'].'/s/tmp/multiphoto/'.session_id().'/'. $node_id .'/'.$id);
    unset($_SESSION['multiphoto'][$node_id][$id]);
}
?>