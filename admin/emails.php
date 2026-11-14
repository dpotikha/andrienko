<?php

require_once("prepend.php");
require_once("check_node.php");

define('NODES_PER_PAGE', 30);
define('NODELINKS_PER_PAGE', 20);

$action = (string)$request->getParameter("do");

if ($action == null || $action=="") {
	$action = "_default";
}

$action($request);

function _default(&$request) {

	header("Location: emails.php?do=main&id=".DELIVERY_NODE_ID);
	exit();
}

function main(&$request) {

	editnode($request);
}

function addnode_form(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$nodeClass = & NodeClass::findById($request->getParameter("class_id"));
	if ($nodeClass == NULL) {
		Header("Location: /admin/main.php");die();
	}
	$parentId = (int)$request->getParameter("parent_id");
	$parent = & Node::findById($parentId);
	if (($parent == null) && (Node::hasRoot())) {
		Header("Location: /admin/main.php");die();
	}

	if ($parent != null) {
		$gr = new NodeGroupRights($currentLoggedUser->group, $parent);
		$request->setAttribute("userHasWriteRights", $gr->hasWriteRight());
		$pList = &$parent->getParentList();
		$pList[] = &$parent;
		$request->setAttribute("parents", $pList);
		$request->setAttribute("parent", $parent);
		$request->setAttribute("parent_id", $parent->id);
	} else {
		$request->setAttribute("userHasWriteRights", true);
		$request->setAttribute("parents", array());
		$request->setAttribute("parent_id", -1);
	}
	$request->setAttribute("nodeClass", $nodeClass);
	$fieldDefs = &$nodeClass->getFieldDefs();

	$iparams = array();
	$fieldDefsKeys = array_keys($fieldDefs);
	foreach ($fieldDefsKeys as $k) {
		$fieldDef = &$fieldDefs[$k];

		$item = new PHPObject();

		$field = &$fieldDef->getFieldInstance();
		$field->setDefaultValue();
		$params = $field->getParamersForIncludeControlJSP();

		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = &$fieldDef;
		$item->params = $params;
		$iparams[] = &$item;
	}
	$fieldDefsArray = array($iparams);
	$request->setAttribute("fieldDefsArray", $fieldDefsArray);

	$nodePropsMap = new phpobject();
	$nodePropsMap->NodeDynamicTemplate = $nodeClass->default_template;
	$nodePropsArray = array($nodePropsMap);

	$formsCount = 1;

	$request->setAttribute("nodePropsArray", $nodePropsArray);
	$request->setAttribute("formsCount", $formsCount);

	usetemplate("emails/addnode");
}

function addnode(&$request) {

	global $session, $AdminTrnsl;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$nodeClass = & NodeClass::findById((int)$request->getParameter("class_id"));
	//print_r($nodeClass->fieldDefs["image"]->fieldParameters);
	
	if ($nodeClass == null) {
		Header("Location: /admin/main.php");die();
	}
	$parentId = (int)$request->getParameter("parent_id");
	$parent = &Node::findById($parentId);
	if (($parent == null) && (Node::hasRoot())) {
		Header("Location: /admin/main.php");die();
	}
/*
	$moder_id = (int)$request->getParameter("moder_id");
	if ($moder_id) {
		$ma = &ModeratedAction::findById($moder_id);
	}
*/
//var_dump($request);
	if ($parent != null) {
		$gr = new NodeGroupRights($currentLoggedUser->group, $parent);
		$request->setAttribute("userHasWriteRights", $gr->hasWriteRight());
		if (!$gr->hasWriteRight()) {
			die($AdminTrnsl["You_do_not_have_rights_to_view_this_node"]);
		}
		$pList = &$parent->getParentList();
		$pList[] = &$parent;
		$request->setAttribute("parents", $pList);
		$request->setAttribute("parent", $parent);
		$request->setAttribute("parent_id", $parent->id);
	} else {
		$request->setAttribute("userHasWriteRights", true);
		$request->setAttribute("parents", array());
		$request->setAttribute("parent_id", -1);
	}
	$request->setAttribute("nodeClass", $nodeClass);

	$fieldDefs = $nodeClass->getFieldDefs();

	$formsCount = (int)$request->getParameter("forms_count");

	$everythingWasSuccess = true;

	$fieldErrorsArray = array();
	$nodePropsArray = array();
	$fieldListArray = array();
	for ($formNumber = 0; $formNumber < $formsCount; $formNumber++) {
		$formNumberFormatted = sprintf("%02d", $formNumber);
		$fieldNamePrefix = "f" . $formNumberFormatted . "_";

		//it's a nice trick for multiple forms
		$request->parametersPrefix = $fieldNamePrefix;

		//let's validate fields
		$fieldErrors = array();
		$fieldList = array();

		$fieldDefsKeys = array_keys($fieldDefs);
		foreach ($fieldDefsKeys as $k) {
			$fieldDef = &$fieldDefs[$k];
			$field = $fieldDef->getFieldInstance();
			$verror = $field->setValue($request);
			if ($verror) {
				$fieldErrors[$fieldDef->shortname] = $verror;
			}
			$fieldList[$fieldDef->shortname] = $field;
		}

		//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		if ($fieldList["artikel"] != null) {
			$existingItem = findItemByArtikel($fieldList["artikel"]->value);
			if ($existingItem != null) {
				$fieldErrors["artikel"] = "ArtikelNr ist schon belegt";
			}
		}
		if ($nodeClass->shortname == "subscriber") {
			$existingSubscriber = findAbonentByEmail($fieldList["email"]->value);
			if ($existingSubscriber != null) {
				$fieldErrors["email"] = "E-Mail Adresse ist schon vergeben";
			}
		}
		//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

		//now validate node fields
		$NodeName = (string)$request->getParameter("NodeName");
		$NodeShortname = (string)$request->getParameter("NodeShortname");
		$NodeDynamicTemplate = (string)$request->getParameter("NodeDynamicTemplate");
		$NodeAdminURL = (string)$request->getParameter("NodeAdminURL");

		if (strlen($NodeName) == 0)
			$fieldErrors["NodeName"] = $AdminTrnsl["RequiredField"];

		if ($parent != null) {
			if(!Validator::validateShortname($NodeShortname)) {
				$fieldErrors["NodeShortname"] = $AdminTrnsl["NodeEditInvalidShortname"];
			}
		} else {
			$NodeShortname = "";
		}

		if (($parent != null) && $parent->hasChildren($NodeShortname)) {
			$fieldErrors["NodeShortname"] = $AdminTrnsl["NodeEditShortnameUsed"];
		}

		checkNodeNameByClass(&$NodeName,$nodeClass,$fieldList);
		$nodePropsMap = new phpobject();
		$nodePropsMap->NodeName = $NodeName;
		$nodePropsMap->NodeShortname = $NodeShortname;
		$nodePropsMap->NodeDynamicTemplate = $NodeDynamicTemplate;
		$nodePropsMap->NodeAdminURL = $NodeAdminURL;

		$fieldErrorsArray[$formNumber] = $fieldErrors;
		$nodePropsArray[$formNumber] = $nodePropsMap;
		$fieldListArray[$formNumber] = $fieldList;

		if (sizeof($fieldErrors) > 0) {
			$everythingWasSuccess = false;
		}

		//return back empty value
		$request->parametersPrefix = "";
	}
	//if validation not failed create node:
	if ($everythingWasSuccess == true) {
		//if ($currentLoggedUser->group->isModerator) {
			for ($formNumber = 0; $formNumber < $formsCount; $formNumber++) {
				$nodePropsMap = $nodePropsArray[$formNumber];
				$fieldList = $fieldListArray[$formNumber];

				$createdNode = & Node::createWithPreparedValues($parent, $nodePropsMap->NodeName, $nodePropsMap->NodeShortname,
					$nodePropsMap->NodeDynamicTemplate, $nodePropsMap->NodeAdminURL,
					$currentLoggedUser->id, $nodeClass->id, $fieldList);
				//if ($ma) {
				//	ModeratedAction::remove($ma->id);
				//}
			}
		/*} else {
			$moderatedCreateAction = new PHPObject();
			$moderatedCreateAction->parent_id = $parent->id;
			$moderatedCreateAction->NodeName = $NodeName;
			$moderatedCreateAction->NodeShortname = $NodeShortname;
			$moderatedCreateAction->NodeDynamicTemplate = $NodeDynamicTemplate;
			$moderatedCreateAction->NodeAdminURL = $NodeAdminURL;
			$moderatedCreateAction->user_id = $currentLoggedUser->id;
			$moderatedCreateAction->class_id = $nodeClass->id;
			$moderatedCreateAction->fieldList = $fieldList;

			$moderatedCreateAction->ParentPath = $parent->absolutePath;

			ModeratedAction::create(-1, $currentLoggedUser->id, "create", $moderatedCreateAction);
			$forwardUrl = "/admin/emails.php?do=main&id=" . $parent->id . "&msg=nodes_created_moderated";
		}*/
		$forwardUrl = "/admin/emails.php?do=main&id=";
		$forwardUrl .= $createdNode->parent_id;
		Header("Location: " . $forwardUrl . "&reload=1");
		return;
	}

	$fieldDefsArray = array();

	for ($formNumber = 0; $formNumber < $formsCount; $formNumber++) {
		$iparams = array();
		$fieldList = $fieldListArray[$formNumber];

		$fieldDefsKeys = array_keys($fieldDefs);
		foreach ($fieldDefsKeys as $k) {
			$fieldDef = &$fieldDefs[$k];
	
			$item = new PHPObject();
	
			$shortname = $fieldDef->shortname;
			$errMsg = $fieldErrors[$shortname];
	
			$field = &$fieldList[$shortname];
			$params = $field->getParamersForIncludeControlJSP();
	
			$item->jspname = $field->getJSPControlName();
			$item->fieldDef = &$fieldDef;
			$item->params = $params;
			$iparams[] = &$item;
		}
		$fieldDefsArray[$formNumber] = $iparams;
	}

	$request->setAttribute("fieldDefsArray", $fieldDefsArray);
	$request->setAttribute("validationErrorsArray", $fieldErrorsArray);
	$request->setAttribute("nodePropsArray", $nodePropsArray);
	$request->setAttribute("formsCount", $formsCount);

	//if ($ma) {
		//$request->setAttribute("moder_action", $ma);
	//}

	usetemplate("emails/addnode");
}

function deletenode(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$node_id = (int)$request->getParameter("id");
	$node = & Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/main.php");die();
	}

	$parent_id = $node->parent_id;

	$forwardUrl = null;

	if ($parent_id != null) {
		$forwardUrl = "/admin/emails.php?do=main&id=" . $parent_id . "&reload=1&";
	} else {
		$forwardUrl = "/admin/main.php?";
	}

	if ($currentLoggedUser->group->isModerator) {
		Node::remove($node_id);
	} else {
		$moderatedCreateAction = new PHPObject();
		$moderatedCreateAction->NodeName = $node->name;
		$moderatedCreateAction->NodePath = $node->absolutePath;
		ModeratedAction::create($node_id, $currentLoggedUser->id, "delete", $moderatedCreateAction);
		$forwardUrl .= "msg=nodes_deleted_moderated";
	}

	Header("Location: ".$forwardUrl);
}

function deleteselectednode(&$request){
	//print_r($request->getParameter("delete_node"));
	$deletenodes=$request->getParameter("delete_node");
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	foreach($deletenodes as $k=>$node_id){
	//	$node_id = (int)$request->getParameter("id");
		$node = & Node::findById($node_id);
		if ($node == null) {
			Header("Location: /admin/main.php");die();
		}
	
		$parent_id = $node->parent_id;
	
		$forwardUrl = null;
	
		if ($parent_id != null) {
			$forwardUrl = "/admin/emails.php?do=main&id=" . $parent_id . "&reload=1&";
		} else {
			$forwardUrl = "/admin/main.php?";
		}
	
		if ($currentLoggedUser->group->isModerator) {
			Node::remove($node_id);
		} else {
			$moderatedCreateAction = new PHPObject();
			$moderatedCreateAction->NodeName = $node->name;
			$moderatedCreateAction->NodePath = $node->absolutePath;
			ModeratedAction::create($node_id, $currentLoggedUser->id, "delete", $moderatedCreateAction);
			$forwardUrl .= "msg=nodes_deleted_moderated";
		}
	}
	Header("Location: ".$forwardUrl);
}
function editnode_moder(&$request) {
	global $session, $AdminTrnsl;

	$mid = (int)$request->getParameter("mid");
	$ma = &ModeratedAction::findById($mid);

	$data = &$ma->data;

	$node_id = $ma->node_id;
	$node = &Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/main.php");die();
	}

	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$gr = new NodeGroupRights($currentLoggedUser->group, $node);
	if (!$gr->hasViewRight()) {
		die($AdminTrnsl["You_do_not_have_rights_to_view_this_node"]);
	}

	$all_deps = &$node->findAllDepends();
	$request->setAttribute("depends", $all_deps);

	$nodeClass = $node->getNodeClass();
	$request->setAttribute("nodeClass", $nodeClass);

	$request->setAttribute("node", $node);
	$fieldDefs = &$nodeClass->getFieldDefs();

	$iparams = array();

	$fieldDefsKeys = array_keys($fieldDefs);
	foreach ($fieldDefsKeys as $k) {
		$fieldDef = &$fieldDefs[$k];
		$fieldShortname = $fieldDef->shortname;
		$field = &$data->fieldList[$fieldShortname];
		$params = $field->getParamersForIncludeControlJSP();

		$item = new PHPObject();
		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = &$fieldDef;
		$item->params = $params;
		$iparams[] = &$item;
	}

	$request->setAttribute("fieldDefs", $iparams);
	$request->setAttribute("NodeName", $data->NodeName);
	subnodeslist($request, $node, $all_deps, $currentLoggedUser);

	$request->setAttribute("moder_action", $ma);

	usetemplate("emails/editnode");
}

function editnode(&$request) {
	global $session, $AdminTrnsl;
	$node_id = (int)$request->getParameter("id");
	$node = & Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/main.php");die();
	}

	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$gr = new NodeGroupRights($currentLoggedUser->group, $node);
	if (!$gr->hasViewRight()) {
		die($AdminTrnsl["You_do_not_have_rights_to_view_this_node"]);
	}
	$all_deps = $node->findAllDepends();
	$request->setAttribute("depends", $all_deps);

	$nodeClass = $node->getNodeClass();
	$request->setAttribute("nodeClass", $nodeClass);

	$request->setAttribute("node", $node);
	$fieldDefs = $nodeClass->getFieldDefs();

	$iparams = array();

	$fieldDefsKeys = array_keys($fieldDefs);
	foreach ($fieldDefsKeys as $k) {
		$fieldDef = &$fieldDefs[$k];
		$fieldShortname = $fieldDef->shortname;
		$field = $node->getField($fieldShortname);
		$params = $field->getParamersForIncludeControlJSP();

		$item = new PHPObject();
		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = &$fieldDef;
		$item->params = $params;
		$iparams[] = &$item;
	}
	$request->setAttribute("fieldDefs", $iparams);
	$request->setAttribute("NodeName", $node->name);
	subnodeslist($request, $node, $all_deps, $currentLoggedUser);

	usetemplate("emails/editnode");
}

function subnodeslist(&$request, &$node, &$all_deps, &$currentLoggedUser) {
	$pageNumMap = $request->getParameter("pagenum");
	$childrenByClasses = array();
	$childrenCount = 0;

	$all_deps_keys = array_keys($all_deps);
	foreach ($all_deps_keys as $k) {
		$nodeClass = &$all_deps[$k];

		$item = new PHPObject();

		$item->nodeClass = &$nodeClass;
		$classChildsCount = $node->getChildrenCountOfClassForGroup($currentLoggedUser->group->id, $nodeClass->id);
//		echo $classChildsCount;
		$orderBy = $nodeClass->orderBy;
		$orderByDesc = false;

		if (!$orderBy) {
			$orderBy = "n.subtree_order";
		}/* else {
			list($orderBy, $_descStr) = explode(" ", $orderBy);
			if (strtoupper(trim($_descStr)) == 'DESC') {
				$orderByDesc = true;
			}
		}*/

		if ($classChildsCount <= NODES_PER_PAGE) {
			$classChilds = $node->getChildrenOfClassForGroup($currentLoggedUser->group->id, $nodeClass, $orderBy);
			$item->nodespagesCount = 0;
		} else {
			$page = $pageNumMap[$nodeClass->shortname];
			$pagesCount = floor($classChildsCount / NODES_PER_PAGE) + ($classChildsCount % NODES_PER_PAGE != 0 ? 1 : 0);
			$offset = $page*NODES_PER_PAGE;
			$classChilds = $node->getChildrenOfClassForGroup($currentLoggedUser->group->id, $nodeClass, $orderBy, $offset, NODES_PER_PAGE);

			$item->nodespagesCount = $pagesCount;
			$item->nodesOffset = $offset;
			$item->nodesPage = $page;
		}
		$classChildsP = array();
		$classChildsKeys = array_keys($classChilds);

		foreach ($classChildsKeys as $cck) {
			$nd = &$classChilds[$cck];

			$ndMap = new PHPObject();
			$ndMap->node = &$nd;
			$ndGR = new NodeGroupRights($currentLoggedUser->group, $nd);
			$ndMap->hasWriteRight = $ndGR->hasWriteRight();
			$ndMap->hasViewRight = $ndGR->hasViewRight();
			$ndMap->hasDeleteRight = $ndGR->hasDeleteRight();

			$classChildsP[] = &$ndMap;
		}

		$item->nodes = $classChildsP;
		$item->nodesCount = $classChildsCount;
		$childrenCount += sizeof($classChildsP);
		if (sizeof($classChildsP) > 1) {
			$item->sort = false; //Temporary!!!
		} else {
			$item->sort = false;
		}

		$shownFieldDefs = array();
		$fieldDefs = &$nodeClass->getFieldDefs();
		$fieldDefsKeys = array_keys($fieldDefs);
		foreach ($fieldDefsKeys as $fdk) {
			$fieldDef = &$fieldDefs[$fdk];
			if ($fieldDef->shown) {
				$shownFieldDefs[] = &$fieldDef;
			}
		}

		$item->shownFields = $shownFieldDefs;
		$item->fieldCount = sizeof($fieldDefs);
		$childrenByClasses[] = &$item;
	}

	$request->setAttribute("childrenByClasses", $childrenByClasses);
	$request->setAttribute("childrenCount", $childrenCount);
}

function updatenode(&$request) {
	global $session, $AdminTrnsl;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$node_id = (int)$request->getParameter("id");
	$node = Node::findById($node_id);


	if ($node == null) {
		Header("Location: /admin/main.php");die();
	}

	$mid = (int)$request->getParameter("moder_id");
	if ($mid != 0) {
		$ma = &ModeratedAction::findById($mid);
	}

	$nodeClass = $node->getNodeClass();
	$request->setAttribute("nodeClass", $nodeClass);

	$cuGroupRights = new NodeGroupRights($currentLoggedUser->group, $node);
	if (!$cuGroupRights->hasWriteRight()) {
		die($AdminTrnsl["You_do_not_have_rights_to_update_data_of_this_node"]);
	}

	$all_deps = $node->findAllDepends();
	$request->setAttribute("depends", $all_deps);

	//let's validate fields
	$fieldErrors = array();
	$fieldList = array();

	$_keys = array_keys($node->fields);
	foreach ($_keys as $fieldShortname) {
		$field = &$node->fields[$fieldShortname];
		$fieldDef = $field->getFieldDef();
		$verror = $field->setValue($request);
		if ($verror != null) {
			$fieldErrors[$fieldDef->shortname] = $verror;
		}
		$fieldList[$fieldDef->shortname] = &$field;
	}



	$NodeName = (string)$request->getParameter("NodeName");
	if (strlen($NodeName) == 0)
		$fieldErrors["NodeName"] = $AdminTrnsl["RequiredField"];
//		print_r($fieldErrors);
//		echo (int)count($fieldErrors);
	//if validation not failed update fields data
	
	if (sizeof($fieldErrors) == 0) {
		//print_r($node);
		$forwardUrl = "/admin/emails.php?do=main&reload=yes&id=" . $node->id;
		if ($currentLoggedUser->group->isModerator) {
			$node->name = $NodeName;
			if($_POST["create"]==1){
				$node->store();
			}
			if($_POST["send"]==1){
				send_Emails($_POST);
			}
			$forwardUrl .= "&msg=Node_update_success";
			if ($ma) {
				ModeratedAction::remove($ma->id);
			}
		} else {
			$moderatedCreateAction = new PHPObject();
			$moderatedCreateAction->NodeName = $NodeName;
			$moderatedCreateAction->NodePath = $node->absolutePath;
			$moderatedCreateAction->fieldList = $fieldList;
			ModeratedAction::create($node->id, $currentLoggedUser->id, "update", $moderatedCreateAction);
			$forwardUrl .= "&msg=nodes_updated_moderated";
		}
		checkNodeName(&$NodeName,$node);
		Header("Location: ".$forwardUrl);
		return;
	}
	
	$request->setAttribute("node", $node);

	$iparams = array();
	$fieldDefs = $nodeClass->getFieldDefs();
	$_keys = array_keys($fieldDefs);
	foreach ($_keys as $_k) {
		$fieldDef = &$fieldDefs[$_k];
		$item = new phpobject();

		$fieldShortname = $fieldDef->shortname;
		$field = &$fieldList[$fieldShortname];
		$params = $field->getParamersForIncludeControlJSP();

		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = &$fieldDef;
		$item->params = $params;
		$iparams[] = &$item;
	}
	$request->setAttribute("fieldDefs", $iparams);
	$request->setAttribute("validationErrors", $fieldErrors);

	$request->setAttribute("NodeName", $NodeName);
	subnodeslist($request, $node, $all_deps, $currentLoggedUser);

	if ($ma) {
		$request->setAttribute("moder_action", $ma);
	}

	usetemplate("emails/editnode");
}






?>