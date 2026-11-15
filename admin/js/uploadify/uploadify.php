<?php
function getmicrotime1() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

//define('ST',getmicrotime1());
require_once($_SERVER['DOCUMENT_ROOT']."/WEB-INF/prepend.php");
require_once(__DIR__ . "/upload_guard.php");
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
//die(1);
//sleep(5);
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	//$targetPath = $_SERVER['DOCUMENT_ROOT'] .'/files/tmp/' $_REQUEST['folder'] . '/';
        
        
        $targetPath = $_SERVER['DOCUMENT_ROOT'] .'/s/tmp/multiphoto/'.session_id(). '/'.(int)($_POST['node_id'] ?? 0).'/';
        if (!is_dir($targetPath)) mkdir($targetPath,0777,true);

	$targetFile =  str_replace('//','/',$targetPath) . upload_safe_name($_FILES['Filedata']['name']);
	// gallery photos only
	if (!preg_match('/\.(jpe?g|gif|png)$/i', $targetFile) || @getimagesize($tempFile) === false) {
		header('HTTP/1.1 400 Bad Request');
		exit('Only JPG, GIF, PNG images');
	}
	
	// $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
	// $fileTypes  = str_replace(';','|',$fileTypes);
	// $typesArray = split('\|',$fileTypes);
	// $fileParts  = pathinfo($_FILES['Filedata']['name']);
	
	// if (in_array($fileParts['extension'],$typesArray)) {
		// Uncomment the following line if you want to make the directory if it doesn't exist
                // 
		// mkdir(str_replace('//','/',$targetPath), 0755, true);
        move_uploaded_file($tempFile,$targetFile);
        
        $filename=$_FILES['Filedata']['name'];
        
        $md5 = md5_file($targetFile);
        $exp = explode('.', $filename);
        $ext = strtolower(array_pop($exp));
        
        $tmp_file = $md5 . '.' . $ext;
        
	$_SESSION['multiphoto'][$_POST['node_id']][$tmp_file] = name2shortname(implode('_', $exp)) . '.' . $ext;
        $params=explode(":",$_POST['params'] ?? '');
        $tparams=explode(":",$_POST['tparams'] ?? '');
        // (int): an empty width/height ("") is 0 in PHP 5 arithmetic but a TypeError in PHP 8 (resizePhoto multiplies them)
        resizePhoto($targetFile, $targetPath . 'thumb_' . $tmp_file, (int)($tparams[0] ?? 0), (int)($tparams[1] ?? 0), $tparams[2] ?? '');
        resizePhoto($targetFile, $targetPath . 'crop_' . $tmp_file, (int)($params[0] ?? 0), (int)($params[1] ?? 0), $params[2] ?? '');
        
        echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
	// } else {
	// 	echo 'Invalid file type.';
	// }
}
?>