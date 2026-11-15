<?php

require_once("prepend.php");

$action = (string)$request->getParameter("do"); 

if ($action == null || $action=="") {
	$action = "_default";
}

$action($request);

function checkPrivileges(&$request) {
	global $session, $AdminTrnsl;
	$currentLoggedUser = &$session->getAttribute(SESSION_LOGGED_USER_ATTR);
	if (!$currentLoggedUser->group->canManageClasses) {
		die($AdminTrnsl["You_Have_not_rights_to_manage_classes"]);
	}
}

function _default(&$request) {
	checkPrivileges($request);

	$sortby = $request->getParameter("sortby");
	$classes = array();
	if ($sortby == null) {
		$classes = &NodeClass::findAll("name");
	} else {
		$classes = &NodeClass::findAll($sortby);
	}
	$request->setAttribute("classes", $classes);

	usetemplate("classes/classes_list");
}

function create_form(&$request) {
	usetemplate("classes/create");
}

function validateClassFields($shortname, $name, $class_id) {
	$errors = array();

	if (strlen($shortname) == 0) {
		$errors["shortname"] = "sn_zerolen";
	} elseif (strlen($shortname) < 2) {
		$errors["shortname"] = "sn_short";
	} elseif(!preg_match("~^([A-Za-z_]{1})(.*)$~D", $shortname, $matches)) {
		$errors["shortname"] = "sn_badfl";
	} elseif(!preg_match("~^([A-Za-z_]{1})([A-Za-z0-9_]*)$~D", $shortname, $matches)) {
		$errors["shortname"] = "sn_bad";
	} else {
		$shClass = &NodeClass::findByShortname($shortname);
		if (($class_id == -1 && $shClass != null) || ($class_id != -1 && $shClass != null && $shClass != null && $shClass->id != $class_id)) {
			$errors["shortname"] = "sn_exists";
		}
	}

	if (strlen($name) == 0) {
		$errors["name"] = "n_required";
	}

	return $errors;
}

function getCloneShortname($s) {
    global $MDB;
    $MDB->Query('SELECT shortname FROM dbm_classes');
    $r = $MDB->getRecords();
    $shortnames = array();
    if ($r) foreach ($r as $k => $v) 
        $shortnames[] = $v['shortname'];
    if (!in_array($s . '_copy', $shortnames))
        return $s . '_copy';
    else {
        $i = 1;
        while (in_array($s . '_copy_' . $i, $shortnames))
            $i ++;
        return $s . '_copy_' . $i;
    }
}

function getCloneShortnameForField($s, $class_id) {
    global $MDB;
    $MDB->Query('SELECT shortname FROM dbm_class_fields WHERE class_id = ' . $class_id);
    $r = $MDB->getRecords();
    $shortnames = array();
    if ($r) foreach ($r as $k => $v)
        $shortnames[] = $v['shortname'];
    if (!in_array($s . '_copy', $shortnames))
        return $s . '_copy';
    else {
        $i = 1;
        while (in_array($s . '_copy_' . $i, $shortnames))
            $i ++;
        return $s . '_copy_' . $i;
    }
}

function cloneClass(&$request) {
        $class_id = (int)$request->getParameter("class_id");
	$donor = &NodeClass::findById($class_id);

        $nc = new NodeClass();

        $nc->name = $donor->name;
        $nc->setShortname(getCloneShortname($donor->shortname));
        $nc->default_template = $donor->default_template;
        $nc->showAtAdminTree = $donor->showAtAdminTree;
        $nc->nodeNameLabel = $donor->nodeNameLabel;
        $nc->orderBy = $donor->orderBy;
        $nc->flags = $donor->flags;

        $nc->create();
        
        $fieldDefs = $donor->getFieldDefs();
	if ($fieldDefs) foreach($fieldDefs as $fd) {
                $nc->addFieldDef($fd->shortname, $fd->name, $fd->fieldTypeId, $fd->required, $fd->shown, $fd->hide);
                $nc = &NodeClass::findById($nc->id);
                $newFd = $nc->getFieldDef($fd->shortname);
                if ($fd->fieldParameters) foreach ($fd->fieldParameters as $k => $v)
                    $newFd->setParameterValue($k, $v);
	}
        $request->parameters['class_id'] = $nc->id;
        edit($request);
}

function removeField(&$request) {
        $class_id = (int)$request->getParameter("class_id");
        $field_id = (int)$request->getParameter("field_id");
	$nc = &NodeClass::findById($class_id);
        $fd = $nc->getFieldDef($field_id);
        $nc->delFieldDef($fd->shortname);
        $response['result'] = 1;
        die(json_encode($response));
}

function cloneField(&$request) {
        $class_id = (int)$request->getParameter("class_id");
        $field_id = (int)$request->getParameter("field_id");
	$nc = &NodeClass::findById($class_id);
        $fd = $nc->getFieldDef($field_id);
        $newShortName = getCloneShortnameForField($fd->shortname, $nc->id);
        $nc->addFieldDef($newShortName, $fd->name, $fd->fieldTypeId, $fd->required, $fd->shown, $fd->hide);
        $nc = &NodeClass::findById($nc->id);
        $newFd = $nc->getFieldDef($newShortName);
        if ($fd->fieldParameters) foreach ($fd->fieldParameters as $k => $v)
            $newFd->setParameterValue($k, $v);
        $response['result'] = $newFd->shortname;
        die(json_encode($response));
}

function create(&$request) {
	global $AdminTrnsl, $NODECLASS_FLAG_NAMES;
	$name = $request->getParameter("name");
	$shortname = $request->getParameter("shortname");
	$def_template = $request->getParameter("default_template");
	$show_at_atd = (int)$request->getParameter("show_at_adt");
	$nodename_label = $request->getParameter("nodename_label");
	$orderby = $request->getParameter("orderby");
	foreach ($NODECLASS_FLAG_NAMES as $flagName) {
		$flags[$flagName] = (int)$request->getParameter("flag_" . $flagName);
	}

	$errors = validateClassFields($shortname, $name, -1);
	$request->setAttribute("ERRORS", $errors);

	if (sizeof($errors) == 0) {
		$nc = new NodeClass();

		$nc->name = $name;
		$nc->setShortname(strtolower($shortname));
		$nc->default_template = $def_template;
		if ($show_at_atd != null) {
			$nc->showAtAdminTree = true;
		}
		$nc->nodeNameLabel = $nodename_label;
		$nc->orderBy = $orderby;
		foreach ($NODECLASS_FLAG_NAMES as $flagName) {
			$nc->setFlag($flagName, $flags[$flagName]);
		}

		$nc->create();

		header("Location: /admin/classes.php?MSG=Create_class_success");
	} else {
		usetemplate("classes/create");
	}
}

function edit(&$request) {
	$class_id = (int)$request->getParameter("class_id");
	$nc = &NodeClass::findById($class_id);

	$fieldDefs = $nc->getFieldDefs();

	$fieldDefsBeans = array();
	foreach($fieldDefs as $fd) {
		$fdBean = new PHPObject();
		$fdBean->id = $fd->id;

		$fdBean->shortname = $fd->shortname;
		$fdBean->name = $fd->name;
		$fdBean->field_type = $fd->fieldTypeId;
		$fdBean->required = $fd->required;
		$fdBean->shown = $fd->shown;
                $fdBean->hide = $fd->hide;
		$fdBean->isToDelete = false;

		$fieldDefsBeans[$fd->id] = clone $fdBean;
	}
	$request->setAttribute("class", $nc);
	$request->setAttribute("fieldDefs", $fieldDefsBeans);
	$request->setAttribute("fieldTypes", FieldType::getAllTypes());

	usetemplate("classes/edit");
}

function update(&$request) {
	global $AdminTrnsl, $NODECLASS_FLAG_NAMES;
	$class_id = (int)$request->getParameter("class_id");
	//without '&' !!!
	$nc = NodeClass::findById($class_id);

	if ($nc == null) {
		die("invalid class_id");
	}

	$name = $request->getParameter("name");
	$shortname = $request->getParameter("shortname");
	$def_template = $request->getParameter("default_template");
	$show_at_atd = (int)$request->getParameter("show_at_adt");
	$nodename_label = $request->getParameter("nodename_label");
	$orderby = $request->getParameter("orderby");
	$flags = array();
	foreach ($NODECLASS_FLAG_NAMES as $flagName) {
		$flags[$flagName] = (int)$request->getParameter("flag_" . $flagName);
	}

	$nc->name = $name;
	$nc->setShortname(strtolower($shortname));
	$nc->default_template = $def_template;
	$nc->showAtAdminTree = ($show_at_atd == 1);
	$nc->nodeNameLabel = $nodename_label;
	$nc->orderBy = $orderby;

	foreach ($NODECLASS_FLAG_NAMES as $flagName) {
		$nc->setFlag($flagName, $flags[$flagName]);
	}

	$fieldDefs = $nc->getFieldDefs();

	$FERRORS = array();

	$fieldDefsBeans = array();
	$thereAreFieldErrors = false;
	foreach($fieldDefs as $fd) {
		$fdBean = new PHPObject();
		$fdBean->id = $fd->id;

		$fdBean->shortname = $request->getParameter("df_shortnames_" . $fd->id);
		$fdBean->name = $request->getParameter("df_names_" . $fd->id);
		$fdBean->field_type = $request->getParameter("df_types_" . $fd->id);
		$fdBean->required = ($request->getParameter("df_required_" . $fd->id) == 1);
		$fdBean->shown = ($request->getParameter("df_shown_" . $fd->id) == 1);
                $fdBean->hide = ($request->getParameter("df_hide_" . $fd->id) == 1);
		$fdBean->isToDelete = ($request->getParameter("df_delete_" . $fd->id) == 1);
                $fdBean->order = $request->getParameter("df_orders_" . $fd->id);

		$fieldDefsBeans[$fd->id] = $fdBean;

		if ($fdBean->isToDelete) {
			continue;
		}

		$errors = array();
		if(!Validator::validateShortname($fdBean->shortname)) {
			$errors["shortname"] = "fbad";
		} elseif (strlen($fdBean->shortname) == 0) {
			$errors["shortname"] = "fsmall";
		} else {
			$efd = $fieldDefs[$fdBean->shortname];
			if ($efd != null && $efd->id != $fd->id) {
				$errors["shortname"] = "fdup";
			}
		}

		if (strlen($fdBean->name) == 0) {
			$errors["name"] = "fn_required";
		}

		$FERRORS[$fd->id] = $errors;
		if (sizeof($errors) > 0) {
			$thereAreFieldErrors = true;
		}
	}

	$errors = validateClassFields($shortname, $name, $class_id);
	if (!$thereAreFieldErrors && (sizeof($errors) == 0)) {
		$nc->store();
		foreach($fieldDefsBeans as $fdBean) {
			if ($fdBean->isToDelete) {
				$nc->delFieldDef($fdBean->shortname);
			} else {
				$nc->updateFieldDefWithOrder($fdBean->id, $fdBean->shortname, $fdBean->name, $fdBean->field_type, $fdBean->required, $fdBean->shown, $fdBean->order, $fdBean->hide);
			}
		}
                header("Location: /admin/classes.php?do=edit&class_id=" . $class_id . "&MSG=Class_update_success");
		return;
	} else {
		_dump($error);
		_dump($FERRORS);
	}

	$request->setAttribute("ERRORS", $errors);
	$request->setAttribute("FERRORS", $FERRORS);
	$request->setAttribute("class", $nc);
	$request->setAttribute("fieldDefs", $fieldDefsBeans);

	usetemplate("classes/edit");
}

function delete(&$request) {
	global $AdminTrnsl;
	//$class_id = (int)$request->getParameter("class_id");
        $classes = explode(',', $request->getParameter("class_id"));
        if ($classes) foreach ($classes as $class_id) {
            $nc = &NodeClass::findById((int)$class_id);
            $nc->remove();
        }

	$request->setAttribute("MSG", $AdminTrnsl["Class_delete_success"]);
	header("Location: /admin/classes.php?MSG=Class_delete_success");
}

function get_fdstring_html(&$request) {
    global $AdminTrnsl;
    $class_id = (int)$request->getParameter("class_id");
    $shortname = $request->getParameter("nfshortname");
    $class = NodeClass::findById($class_id);

    $fd = $class->getFieldDef($shortname);
    $fieldTypes = FieldType::getAllTypes();
    ?>
    <tr id=back3 class="fdRow<?= $fd->id ?>">
        <td width=5% nowrap><?= $fd->id ?><input type="hidden" value="<?= $class->getFieldDefsCount() ?>" id="df_orders_<?= $fd->id ?>" name="df_orders_<?= $fd->id ?>" /></td>
        <td nowrap><img border="0" onclick="remove(<?= $fd->id ?>)" src="/admin/_img/ico/delete.jpg" tooltip="<?=$AdminTrnsl["Delete"]?>"></td>
        <td width=4% nowrap class="dragHandle">&nbsp;DRAG</td>
        <td width=5% nowrap>&nbsp;<input type=button value="" style="width:21px" class=button41 onClick="moveDown(<?= $fd->id ?>)"><input type=button value="" style="width:21px" class=button42 onClick="moveUp(<?= $fd->id ?>)">&nbsp;</td>
        <td width=20%><input type="text" name="df_shortnames_<?= $fd->id ?>" value="<?= prepareStringForHtml($fd->shortname)?>" style="width:100%">
        <?php /*if ($FERRORS[$fd->id]['shortname'] != null) {?><div class=errmsg><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$FERRORS[$fd->id]['shortname']]?></div><?}*/?></td>
        <td width=25%><input type=text name="df_names_<?=$fd->id?>" style="width:100%" value="<?= prepareStringForHTML($fd->name) ?>">
        <?php /*if ($FERRORS[$fd->id]['name'] != null) {?><div class=errmsg><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$FERRORS[$fd->id]['name']]?></div><?}*/?></td>
        <td width=20%><select name="df_types_<?=$fd->id?>" style="width:100%"><?php forEach ($fieldTypes as $ft) {?><option value="<?=$ft->id?>"<?php if ($ft->id == $fd->fieldTypeId) {?> SELECTED<?php }?>><?=$ft->name?><?php }?></select></td>
        <td width=10% align=center><input type=checkbox name="df_required_<?=$fd->id?>" value=1<?php if ($fd->required) {?> CHECKED<?php }?>></td>
        <td width=10% align=center><input type=checkbox name="df_shown_<?=$fd->id?>" value=1 <?php if ($fd->shown) {?> CHECKED<?php }?>></td>
        <td width=10% align=center><input type=checkbox name="df_hide_<?=$fd->id?>" value=1 <?php if ($fd->hide) {?> CHECKED<?php }?>></td>
        <td width=10% align=center nowrap>
            <input type=button value="<?=$AdminTrnsl["parameters"]?>" class="button1" onClick="params(<?= $fd->id ?>)">
            <input type=button value="<?=$AdminTrnsl["Clone"]?>" class="button1" onClick="clone(<?= $fd->id ?>);">
        </td>
        <td width=10% align=center nowrap>
        <input type=checkbox name="df_delete_<?=$fd->id?>" value=1 <?php if ($fd->todelete) {?> CHECKED<?php }?>></td>
    </tr>
    <?php 
    exit;
}

function add_field(&$request) {
        global $AdminTrnsl;
	$class_id = (int)$request->getParameter("class_id");
	$name = $request->getParameter("nfname");
	$shortname = $request->getParameter("nfshortname");
	$field_type = (int)$request->getParameter("nffieldtype");
	$rRequired = (int)$request->getParameter("nfrequired");

	$required = ($rRequired == 1);

	$rShown = (int)$request->getParameter("nfshown");
	$shown = ($rShown == 1);
        
        $rHide = (int)$request->getParameter("nfhide");
	$hide = ($rHide == 1);

	$nc = &NodeClass::findById($class_id);
	$errors = array();
	if (strlen($name) == 0) {
                $errors["name"] = $AdminTrnsl["CLASS_EDIT_ERROR_fn_required"];
	}

	if(!Validator::validateShortname($shortname)) {
                $errors["shortname"] = $AdminTrnsl["CLASS_EDIT_ERROR_fbad"];
	} elseif (strlen($shortname) == 0) {
		$errors["shortname"] = $AdminTrnsl["CLASS_EDIT_ERROR_fsmall"];
	} elseif ($nc->getFieldDef($shortname) != null) {
		$errors["shortname"] = $AdminTrnsl["CLASS_EDIT_ERROR_fdup"];
	}

	$request->setAttribute("AFERRORS", $errors);
        $response = array();
	if (sizeof($errors) == 0) {
                $nc->addFieldDef($shortname, $name, $field_type, $required, $shown, $hide);
                $response['result'] = 1;
		
	} else {
                $response['result'] = 0;
                $response['errors'] = $errors;
	}
        die(json_encode($response));
}

function field_params(&$request) {
	$class_id = (int)$request->getParameter("class_id");
	$nc = &NodeClass::findById($class_id);
	if ($nc == null) {
		_default($request);
		return;
	}

	$request->setAttribute("class", $nc);

	$field_id = (int)$request->getParameter("field_id");

	$fd = $nc->getFieldDef($field_id);
	$request->setAttribute("fieldDef", $fd);

	$fpValues = array();
	$fdFieldType = $fd->getFieldType();
	$field_params = $fdFieldType->getParameterList();
	foreach ($field_params as $fp) {
		$fpValues[$fp->shortname] = $fd->getParameterValue($fp->shortname);
	}
	$request->setAttribute("parameterValues", $fpValues);

	usetemplate("classes/field_params");
}

function update_field_params(&$request) {
	$class_id = (int)$request->getParameter("class_id");
	$nc = &NodeClass::findById($class_id);
	if ($nc == null) {
		_default($request);
		return;
	}

	$request->setAttribute("class", $nc);

	$field_id = (int)$request->getParameter("field_id");
	$fd = $nc->getFieldDef($field_id);

	$fdFieldType = $fd->getFieldType();
	$field_params = $fdFieldType->parameterList;

	foreach ($field_params as $fp) {
		$param_value = $request->getParameter("p_" . $fp->shortname . "_value");
		$fd->setParameterValue($fp->shortname, $param_value[0]);
	}

	//field_params($request);
	header("Location: /admin/classes.php?do=field_params&class_id=".$class_id."&field_id=".$field_id."&saved=ok");
}

function depends(&$request) {
	$class_id = (int)$request->getParameter("class_id");
	$nc = &NodeClass::findById($class_id);

	$depends = &$nc->getDependentClasses();
	$request->setAttribute("allDepends", $depends);
	$request->setAttribute("class", $nc);

	$classes = &NodeClass::findAll();
	$request->setAttribute("allClasses", $classes);

	usetemplate("classes/depends");
}

function update_depends(&$request) {
	$class_id = (int)$request->getParameter("class_id");

	$dep_classes = $request->getParameter("class_deps");
	$nc = &NodeClass::findById($class_id);
	$nc->removeDepend();

	if ($dep_classes !== null) {
		foreach ($dep_classes as $dep_class_id) {
			$dep_class = &NodeClass::findById($dep_class_id);
			$nc->addDepend($dep_class);
		}
	}

	depends($request);
}

?>