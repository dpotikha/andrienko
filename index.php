<?php 
//die("<p align=center>We are now upgrading software.<br>Sorry for inconvenience.<br>Visit us tomorrow with new great features and refreshed design!<br><br>Sorry - Wartungsarbeiten!<br>In etwa einer halben Stunde sind wir wieder online...");
function getmicrotime1() 
{ 
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
} 
define('ST',getmicrotime1()) ;
Header("ContentType: text/html; charset=utf-8");
require_once($_SERVER['DOCUMENT_ROOT'].'/WEB-INF/prepend.php');
$uri = $_SERVER['REQUEST_URI'];

$path = $uri;
//$path=$_GET["path"];
if(!MOD_REWRITE){
		
	//$node_id=strlen($request->getParameter("node_id"))?$request->getParameter("node_id"):1;
	if(strlen($path)){
		$p=substr($path,3);
		
		$n=Node::findByPath($p);
		$node_id=$n->id;
		$_GET["lang"]=substr($path,1,2);
		//echo $_GET["lang"];
	}else{
		$node_id=1;
	}
}
$p = strpos($path, "?");
if ($p !== false) {
	$path = substr($path, 0, $p);
}

if ($path=='') {
	$path = '/';
} else if (strlen($path) != 1 && $path[strlen($path)-1] == '/') {
	$path = substr($path, 0, strlen($path) - 1);
}
//echo $path;
$CurrentNode = (MOD_REWRITE)?Node::findByPath($path):Node::findById($node_id);
if ($CurrentNode && $CurrentNode->isRoot()) {
//	header("Location: /en");
//	die();
}
	/*if($CurrentNode && $CurrentNode->isRoot()){
		$_SESSION["LANG"]="ru";
	}else{
		$_SESSION["LANG"]=substr($path,1,2);
	}
	if($_SESSION["LANG"]!="ru"&&$_SESSION["LANG"]!="ua")
	$_SESSION["LANG"]="ru";
	$LANG=$_SESSION["LANG"];
	$request->setAttribute("LANG",$LANG);
	if(file_exists($_SERVER['DOCUMENT_ROOT']."/WEB-INF/lang_".$LANG.".php"))
	require_once($_SERVER['DOCUMENT_ROOT']."/WEB-INF/lang_".$LANG.".php");
	$request->setAttribute("lang",$lang);*/
//if(!$CurrentNode)
//$CurrentNode = Node::findByPath(str_replace("/ua/","/en/",$path));

$MAX_NAVIGATOR_DEPTH=-1;
$NAVIGATOR_INCLUDE_LINK=1;
$request->setAttribute("MAX_NAVIGATOR_DEPTH",$MAX_NAVIGATOR_DEPTH);
$request->setAttribute("NAVIGATOR_INCLUDE_LINK",$NAVIGATOR_INCLUDE_LINK);
//print_r($CurrentNode);

	$lang=(isset($_GET["lang"])&&strlen($_GET["lang"]))?$_GET["lang"]:(strlen($session->getAttribute("SITE_LOCALE"))?$session->getAttribute("SITE_LOCALE"):"ua");
	if ($lang != "ua" && $lang != "en" && $lang != "ru") {
		$lang = "ua";
	}
$session->setAttribute("SITE_LOCALE", $lang);

define("SITE_LOCALE", $lang);
	
if ($CurrentNode == null) {
	//header("Location: /");
	//header("HTTP/1.0 404 Not Found");
//	$CurrentNode = &Node::findByPath("/en/_404");
	
	header("Location: ".makeLink("/_404"));
	exit();
	//header("Status: 404 Not Found");
	//die("+++");
}

	
	//echo $lang;

//echo LOCALE;
require_once('WEB-INF/lang_'.$lang.'.php');

$request->setAttribute("CurrentNode", $CurrentNode);

require_once('_site_settings.php');
//echo $CurrentNode->dynamicTemplate;
if(!isset($_SESSION["skip"])&&isset($_GET["skip"])){
	$_SESSION["skip"]=true;
}
//if(isset($_SESSION["skip"])){
if(true){
	tinclude($CurrentNode->dynamicTemplate);
}else{
	if(SITE_LOCALE=="en")include("index-en.html");
	else
	include("index.html");
}
_logTimeFromStart("script end");

?>