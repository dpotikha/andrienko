<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/WEB-INF/prepend.php');

$node_id = urlencode($_GET["node_id"]);
$page = (int)urlencode($_GET["page"]);
if($page==0)$page=1;



$node_path = $request->getParameter("rn");
$root = Node::findRoot();
$allowed_classes_str = $request->getParameter("cs");
if ($allowed_classes_str != "*") {
	$allowed_classes_shortnames = explode(",", $allowed_classes_str);
	$allowed_classes = array();
	foreach($allowed_classes_shortnames as $classShortname) {
		$nc = NodeClass::findByShortname($classShortname);
		$allowed_classes[] = $nc->id;
	}
}
$depth = $request->getParameter("d");

$current = Node::findByPath($node_path);
$newarr=array();
	if ($allowed_classes_str != "*") {
		$hasAllowedChildren = $current->hasChildrenOfClasses($allowed_classes, $depth);
		$cnc = $current->getNodeClass();
		$isOfOneOfAllowedClasses = in_array($cnc->id, $allowed_classes);
	} else {
		$hasAllowedChildren = ($current->getChildrenCount() > 0);
		$isOfOneOfAllowedClasses = true;
	}
	$newarr= getNodeXML($current, $depth-1, $page);

$arr=array("ResultSet"=>array("Result"=>$newarr));
echo json_encode($arr, JSON_INVALID_UTF8_SUBSTITUTE);



function getNodeXML($node, $depth, $page) {
	$newarr=array();	
	global $allowed_classes,$node_path,$allowed_classes_str;
	if(count($allowed_classes)==1){
		if($depth>1)
		$node_class=NodeClass::findById($node->nodeClassId);
		else
		$node_class=NodeClass::findById($allowed_classes[0]);
		$sortBy=(strlen($node_class->orderBy))?$node_class->orderBy:"n.subtree_order ASC";
	}else{
                //sdump($allowed_classes);
                if($node->id==1010){
                    $sortBy="IF(n.class_id=370,1,0) DESC, IF(n.class_id=391,n.date,n.subtree_order) DESC";
                }else{
                    $sortBy="n.subtree_order ASC";
                }
		
        }
	//sdump($sortBy);	
	if($page>0){
		$rpp = 30;
		$childrenCount = $node->getChildrenCount();
		
		$pagesCount = floor($childrenCount / $rpp) + ($childrenCount % $rpp != 0 ? 1 : 0);
		$offset = ($page-1)*$rpp;

		$children = $node->getChildren($sortBy, $offset, $rpp);
	}else
	
		$children = $node->getChildren($sortBy);
		
	$k=0;
	if($page>0&&$page>1){
		$newarr[$k]["id"]=0;
		$newarr[$k]["title"]="<a href='javascript:ReloadNode(".$node->id.",".($page-1).",null,1)'><img src='/admin/yui/arrow-up.gif' border=0></a>";
		$newarr[$k]["ch"]=0;
		$k++;
	}
	foreach ($children as $child) {
		$title = $child->name;
                if($child->id==4913)continue;

		if ($allowed_classes_str != "*") {
			$hasAllowedChildren = $child->hasChildrenOfClasses($allowed_classes, $depth);
			$cnc = $child->getNodeClass();
			$isOfOneOfAllowedClasses = in_array($cnc->id, $allowed_classes);
		} else {
			$hasAllowedChildren = ($child->getChildrenCount() > 0);
			$isOfOneOfAllowedClasses = true;
		}
		if ($hasAllowedChildren || $isOfOneOfAllowedClasses) {
			$newarr[$k]["id"]=$child->id;
			$newarr[$k]["depth"]=$depth-1;
			$newarr[$k]["title"]=str_replace(array('"', "'"), '&quot;',$child->name);
			$newarr[$k]["ch"]=($hasAllowedChildren)?1:0;
                        $newarr[$k]["i"]=($isOfOneOfAllowedClasses)?1:0;
			$newarr[$k]["tb"]=0;//0-none;1-bottom;2-top
			$newarr[$k]["page"]=1;//0-none;1-bottom;2-top
			$newarr[$k]["absolutePath"]=$child->absolutePath;//0-none;1-bottom;2-top
			$k++;

			if ($isOfOneOfAllowedClasses) {
				$strBuffer .= " Href=\"javascript:selectNode('" . $child->absolutePath . "')\" Target=\"linkfieldwindow\"";
			}
		}
	}
	if($page>0&&$page<$pagesCount){
		$newarr[$k]["id"]=0;
		$newarr[$k]["title"]="<a href='javascript:ReloadNode(".$node->id.",".($page+1).",null,2)'><img src='/admin/yui/arrow-down.gif' border=0></a>";
		$newarr[$k]["ch"]=0;
	}
	return $newarr;
}

?>