<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/WEB-INF/prepend.php');

$node_id = urlencode($_GET["node_id"]);
$page = (int)urlencode($_GET["page"]);
if($page==0)$page=1;
$r=Node::findById($node_id);

if($page>0){
	$countChilds=$r->getChildrenCountForAdminTree1($_SESSION["CurrentAdminUser"]);
	$rpp=30;
	$pagesCount = floor($countChilds / $rpp) + ($countChilds % $rpp != 0 ? 1 : 0);
	$offset = ($page-1)*$rpp;
	$childs=$r->getChildrenForAdminTree1($_SESSION["CurrentAdminUser"], $offset, $rpp,'ForAdminTree');
}else{
	$childs=$r->getChildrenForAdminTree1($_SESSION["CurrentAdminUser"],-1,-1,'ForAdminTree');
}
$newarr=array();
$i=0;
if(count($childs)>0){
	$aname="#top".$r->id;
	if($page>1){
		$newarr[$i]["id"]=0;
		$newarr[$i]["title"]="<a name='".$aname."'></a><a href='javascript:ReloadNode(".$r->id.",".($page-1).",null,1)'><img src='/admin/yui/arrow-up.gif' border=0></a>";
		$newarr[$i]["ch"]=0;
		$i++;
	}
	foreach($childs as $k=>$v){
		$newarr[$i]["id"]=$v->id;
		$newarr[$i]["title"]=$v->name;//mb_convert_encoding($v->name, 'UTF-8', 'cp-1251');
		$ch1=$v->getChildrenCountForAdminTree1($_SESSION["CurrentAdminUser"]);
		$newarr[$i]["ch"]=($ch1>0)?1:0;
		$newarr[$i]["tb"]=0;//0-none;1-bottom;2-top
		$newarr[$i]["page"]=1;//0-none;1-bottom;2-top
		$newarr[$i]["adminURL"]=(strlen($v->adminUrl))?$v->adminUrl:"";
		$newarr[$i]["absolutePath"]=$v->getHref();
		$i++;
	}
	if($page<$pagesCount){
		$newarr[$i]["id"]=0;
		$newarr[$i]["title"]="<a name='".$aname."'></a><a href='javascript:ReloadNode(".$r->id.",".($page+1).",null,2)'><img src='/admin/yui/arrow-down.gif' border=0></a>";
		$newarr[$i]["ch"]=0;
	}
}

$arr=array("ResultSet"=>array("Result"=>$newarr));
//print_r($arr);
echo json_encode($arr, JSON_INVALID_UTF8_SUBSTITUTE);

?>