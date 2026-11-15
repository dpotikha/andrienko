<?php
function getmicrotime1() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

require_once($_SERVER['DOCUMENT_ROOT']."/WEB-INF/prepend.php");
require_once(__DIR__ . "/upload_guard.php");

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
        // the file library sends no node_id: files land in /s/tmp/files/<session>/, where filelib.php picks them up
        $nodeId = (int)($_POST['node_id'] ?? 0);
        $targetPath = $_SERVER['DOCUMENT_ROOT'] .'/s/tmp/files/'.session_id(). '/'.($nodeId ? $nodeId.'/' : '');
        if (!is_dir($targetPath)) mkdir($targetPath,0777,true);
	$targetFile =  str_replace('//','/',$targetPath) . upload_safe_name($_FILES['Filedata']['name']);
        move_uploaded_file($tempFile,$targetFile);
        echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
}
?>