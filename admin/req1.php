<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/WEB-INF/prepend.php');

//$node_id = urlencode($_GET["main_node"]);
//$movedNode=Node::findById($_GET["main_node"]);
$page = (int)urlencode($_GET["page"]);
if($page==0)$page=1;

$targetNode=Node::findById($_GET["target_node"]);
$fromNode=Node::findById($_GET["from_node"]);
$currentLoggedUser = $_SESSION["CurrentAdminUser"];

$newarr=array();
/*foreach($childs as $k=>$v){
		$newarr[$i]["id"]=$v->id;
		$newarr[$i]["title"]=$v->name;
		$ch1=$v->getChildrenCountForAdminTree($_SESSION["CurrentAdminUser"]);
		$newarr[$i]["ch"]=($ch1>0)?1:0;
		$newarr[$i]["tb"]=0;//0-none;1-bottom;2-top
		$newarr[$i]["page"]=1;//0-none;1-bottom;2-top
		$i++;
	}
*/
	$children = $fromNode->getChildrenForUser($currentLoggedUser);
	$k=0;
	for ($i = 0; $i < sizeof($children); $i++) {
		$child = $children[$i];
		$title = $child->name;

		$hasAllowedChildren = $child->hasChildrenForMoveNodeTree($currentLoggedUser, $targetNode);
		$tparent = $targetNode->getParent();
		$cdeps = $child->findAllDepends();
		$isValid = $child->id!=$target->id && $child->id != $tparent->id && $currentLoggedUser->canEditNode($child) && $cdeps[$targetNode->nodeClassId]!= null;

		if ($hasAllowedChildren || $isValid) {
			
			
			
			
		if($child->id!=$targetNode->id){	
			$newarr[$k]["id"]=$child->id;
			$newarr[$k]["title"]=$child->name;
			$newarr[$k]["ch"]=($hasAllowedChildren)?1:0;
			$newarr[$k]["tb"]=0;//0-none;1-bottom;2-top
			$newarr[$k]["page"]=1;//0-none;1-bottom;2-top
                        $newarr[$k]["isvalid"]=($isValid)?1:0;
			$k++;
		}
			
			
			$src = "";
			if ($hasAllowedChildren) {
				$src = " NodeXmlSrc=\"/admin/nodes.php?do=move_node_xmltree&amp;node_id=" . $target->id . "&amp;parent_id=" . $child->id . "\"";
			}

			$strBuffer .= "<TreeNode NodeId=\"tree" . $child->id . "\" Title=\"" . prepareStringForXML($title) . "\" " . $src;
			if ($isValid) {
				$strBuffer .= " Href=\"javascript:selectNode(" . $child->id . ")\"";
			}

			$strBuffer .= ">";
			$strBuffer .= "</TreeNode>";
		}
	}
	$arr=array("ResultSet"=>array("Result"=>$newarr));
echo json_encode($arr, JSON_INVALID_UTF8_SUBSTITUTE);

//echo $strBuffer;


exit();







	$node_id = (int)$request->getParameter("main_node");

	$target = &Node::findById($node_id);
	$root = &Node::findRoot();


function move_node_xmltree_getNodeXML(&$node, &$target, &$currentLoggedUser) {
	$strBuffer = "";
	$children = $node->getChildrenForUser($currentLoggedUser);
	for ($i = 0; $i < sizeof($children); $i++) {
		$child = $children[$i];

		$title = $child->name;

		$hasAllowedChildren = $child->hasChildrenForMoveNodeTree($currentLoggedUser, $target);
		$tparent = $target->getParent();
		$cdeps = $child->findAllDepends();
		$isValid = $child->id!=$target->id && $child->id != $tparent->id && $currentLoggedUser->canEditNode($child) && $cdeps[$target->nodeClassId]!= null;

		if ($hasAllowedChildren || $isValid) {
			$src = "";
			if ($hasAllowedChildren) {
				$src = " NodeXmlSrc=\"/admin/nodes.php?do=move_node_xmltree&amp;node_id=" . $target->id . "&amp;parent_id=" . $child->id . "\"";
			}

			$strBuffer .= "<TreeNode NodeId=\"tree" . $child->id . "\" Title=\"" . prepareStringForXML($title) . "\" " . $src;
			if ($isValid) {
				$strBuffer .= " Href=\"javascript:selectNode(" . $child->id . ")\"";
			}

			$strBuffer .= ">";
			$strBuffer .= "</TreeNode>";
		}
	}

	return $strBuffer;
}
	$nn=Node::findById(11968);
	$str= move_node_xmltree_getNodeXML($nn, $target, $currentLoggedUser);
	echo $str;
	//print_r($rdeps);
echo json_encode($arr, JSON_INVALID_UTF8_SUBSTITUTE);

?>