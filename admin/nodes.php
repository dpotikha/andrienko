<?php

function getmicrotime1() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

//define('ST',getmicrotime1());
require_once("prepend.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/admin/check_node.php");
/* if(isset($_POST['do'])&&$_POST['do']=='updatenode'){
  print_r($_POST);
  //echo ini_get('memory_limit');
  exit();
  } */


//dump($LOCALES);

define('NODES_PER_PAGE', 60);
define('NODELINKS_PER_PAGE', 20);

$action = (string) $request->getParameter("do");



if ($action == null || $action == "") {
    $ddd = 'jkhjk';
    $action = "_default";
}



$action($request);

function _default(&$request) {

    contenttree($request);
}

function changeSortOrder($request) {
    $orderBy = 'n.' . $request->getParameter('orderBy');
    $classNode = NodeClass::findById($request->getParameter('nodeClass'));
    if ($classNode->orderBy == $orderBy . ' ASC')
        $classNode->orderBy = $orderBy . ' DESC';
    else
        $classNode->orderBy = $orderBy . ' ASC';
    $classNode->store();
    Header("Location: /admin/nodes.php?do=main&id=" . $request->getParameter('returnToNode'));
    exit;
}

function deliveryNews($node) {

    $post = array(
        "lang" => 0,
        "name_en" => $node->name,
        "name_ru" => $node->tfields["name_ru"],
        "name_ua" => $node->tfields["name_ua"],
        "text_en" => $node->tfields["text_en"],
        "text_ru" => $node->tfields["text_ru"],
        "text_ua" => $node->tfields["text_ua"],
        "toall" => 0,
    );

    send_Emails($post);
}
function tags($request) {
    global $AdminTrnsl;

    global $session;
    global $MDB;
    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();

        return;
    }
    if (isset($_GET['autocomplete'])) {
        tinclude('blocks/startGZipJS');
        $_GET['query'] = preg_replace('/ +/', ' ', trim($_GET['query']));
        $_GET['query'] = CProtect::string($_GET['query']);
        $search_type = $_GET['search_type'];
        $category = $_GET['category'];

        if ($search_type == 0) {
            $MDB->Query('SELECT n.*, p.name as catname FROM dbm_nodes as n, dbm_nodes as p WHERE p.id=n.parent_id AND n.dynamic_template="tag_item" AND  (n.name LIKE "' . db_escape($_GET['query']) . '%") ORDER BY n.name ASC LIMIT 0,50');
        } else {
            $MDB->Query('SELECT n.*, p.name as catname FROM dbm_nodes as n, dbm_nodes as p WHERE p.id=n.parent_id AND n.dynamic_template="tag_item" AND  (n.name LIKE "%' . db_escape($_GET['query']) . '%") ORDER BY n.name ASC LIMIT 0,50');
        }

        $res = $MDB->getRecords();
        $s = array();
        $data = array();
        foreach ($res as $k => $v) {
            $s[] = trim($v['name']);
            $data[] = array($v['id'], trim($v['catname']));
        }
        if (!count($s)) {
            $s[] = $AdminTrnsl["Tags_NothingFound"];
            $data[] = array('x');
        }

        echo json_encode(array('query' => $_GET['query'], 'suggestions' => $s, 'data' => $data));
        exit();
    }
    if (isset($_POST['section'])) {
        if ($_POST['section'] == 'delete_tag') {
            $MDB->Query('DELETE FROM dbm_tags WHERE tag_id=' . (int)$_POST['id'] . '  AND node_id=' . $node_id);
        } elseif ($_POST['section'] == 'add_tag') {

            if ($_POST['tag_id'] == 0) {
                $v = trim($_POST['val']);
                if (strlen($v)) {
                    $MDB->Query('SELECT n.* FROM dbm_nodes as n WHERE n.parent_id='.(int)$_POST['category'].' AND n.name="' . db_escape($v) . '"');
                    $res = $MDB->NextResult();
                    if ($res) {
                        $tag_id = $res['id'];
                    } else {
                        $catNode=QNode::getInstance($_POST['category']);
                        if($catNode && $catNode->parent_id==31131){
                            $tagNode=QNode::createNode($catNode->getHref(), $v, '', 'tag_item');
                            $tag_id = $tagNode->id;
                        }
                    }
                    if($tag_id){
                         $MDB->Query('REPLACE INTO dbm_tags SET tag_id=' . $tag_id . ' , node_id=' . $node_id);
                    }
                }
            } elseif ($_POST['tag_id'] > 0) {
                $MDB->Query('REPLACE INTO dbm_tags SET tag_id=' . $_POST['tag_id'] . ' , node_id=' . $node_id);
            }
            echo 1;
        }
        exit();
    }
    if (isset($_GET['getlist'])) {


        $where_ads = array();


        $MDB->Query('SELECT count(n.id) as scount FROM dbm_nodes as n,dbm_nodes as p, dbm_tags as t WHERE p.id=n.parent_id AND t.tag_id=n.id AND n.dynamic_template="tag_item" AND t.node_id=' . $node_id);


        $count = array_shift($MDB->NextResult());
        $limit = $_POST['record_per_page'];
        $page = $_POST['page'];
        $totalPages = ceil($count / $limit);
        if ($page > $totalPages)
            $page = $totalPages;
        if ($page <= 0)
            $page = 1;
        $offset = ($page - 1) * $limit;
        $sortList = array(
            'n.id',
            'n.name',
            'p.name',
        );


        if (trim($_POST['sortList']) == '')
            $sortExp = array();
        else
            $sortExp = explode(',', $_POST['sortList']);
        $sortParams = array();
        for ($j = 0; $j < count($sortExp); $j = $j + 2) {
            $sortParams[] = $sortList[$sortExp[$j]] . ' ' . ($sortExp[$j + 1] == 1 ? 'ASC' : 'DESC');
        }
        if (empty($sortParams)) {
            $sortParams[] = 'n.name ASC';
        }

        $MDB->Query('SELECT 
                        n.id, n.name as tagname, p.name as catname
                    FROM dbm_nodes as n,dbm_nodes as p, dbm_tags as t WHERE p.id=n.parent_id AND t.tag_id=n.id AND n.dynamic_template="tag_item" AND t.node_id=' . $node_id . '
                    ORDER BY 
                        ' . implode(',', $sortParams) . ' 
                    LIMIT 
                        ' . $offset . ',' . $limit);

        $res = $MDB->getRecords();
        ob_start();
        foreach ($res as $k => $v) {
            ?>
            <tr id="row_<?= $v['id'] ?>">
                <td><?= $v['id'] ?></td>
                <td><?= $v['tagname'] ?></td>
                <td><?= $v['catname'] ?></td>
                <td>
                    &nbsp;<a href="javascript:;" class="row_delete" rel="<?= $v['id'] ?>" rev="<?= htmlspecialchars($v['tagname']) ?>"><img src="/admin/_img/ico/delete.jpg"></a>
                </td>
            </tr>
            <?php 
        }
        $content = ob_get_contents();
        ob_end_clean();
        $data = array(
            'page' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $count,
            'content' => $content,
            'errors' => '',
            'generateTime' => ''
        );

        tinclude('blocks/ajaxcontent', $data);
        exit();
    }

    //$action = $request->getParameter("action");
    //$request->setAttribute("children", $c);
    //$request->setAttribute("parents", $node->getParentList());
    //error_reporting(E_ALL);
    //ini_set('display_errors','On');
    $request->setAttribute("node", $node);
    /* if (isset($_GET['do1']) && $_GET['do1'] == 'update') {
      $MDB->Query("SELECT * FROM dbm_seo WHERE node_id = $node_id AND lang='" . $_GET['lang'] . "'");
      $seo = $MDB->NextResult();
      if ($seo) {
      $MDB->QUpdate('dbm_seo', array(
      'lang' => $_GET['lang'],
      'node_id' => $_GET['node_id'],
      'seo_title' => $_POST['seo_title'],
      'seo_keywords' => $_POST['seo_keywords'],
      'seo_description' => $_POST['seo_description'],
      'seo_google' => $_POST['seo_google'],
      'seo_google_childs' => $_POST['seo_google_childs']
      ), " node_id = $node_id AND lang='" . $_GET['lang'] . "'");
      } else {
      $MDB->QIns('dbm_seo', array(
      'lang' => $_GET['lang'],
      'node_id' => $_GET['node_id'],
      'seo_title' => $_POST['seo_title'],
      'seo_keywords' => $_POST['seo_keywords'],
      'seo_description' => $_POST['seo_description'],
      'seo_google' => $_POST['seo_google'],
      'seo_google_childs' => $_POST['seo_google_childs']
      ));
      }
      header('Location: /admin/nodes.php?do=seo&node_id=' . $node_id . ' &do1=success');
      exit();
      } */

    usetemplate("nodes/tags");
}
function afu($request) {

    if (count($_FILES)) {
        require_once ("imageclass.php");
        $DOC_ROOT = $_SERVER['DOCUMENT_ROOT'];
        if (isset($_POST["cropper_old_path"]) && strlen($_POST["cropper_old_path"])) {
            directoryClass::rmdir($DOC_ROOT . "/tmp/" . $_POST["cropper_old_path"] . "/");
        }

        $uniq_temp_folder = uniqNumber();
        $tmp_folder_path = "/s/tmp/" . $uniq_temp_folder . "/";
        @mkdir($DOC_ROOT . $tmp_folder_path . "start/", 0777, true);
        @mkdir($DOC_ROOT . $tmp_folder_path . "end/", 0777, true);
        @mkdir($DOC_ROOT . $tmp_folder_path . "finish/", 0777, true);

        $fname = $_FILES["file"]["name"];
        $fname = explode('.', $fname);
        $ext = array_pop($fname);
        $fname = implode('.', $fname);
        $filename = name2shortname($fname) . '.' . name2shortname($ext);
        @move_uploaded_file($_FILES["file"]["tmp_name"], $DOC_ROOT . $tmp_folder_path . "start/" . $filename);
        list($width, $height) = getimagesize($DOC_ROOT . $tmp_folder_path . "start/" . $filename);

        $new_width = 0;
        $new_height = 0;
        $max_width = 1920;
        if ($width > $max_width) {


            $new_width = $max_width;
            $new_height = $new_width * $height / $width;


            $thumb = PhpThumbFactory::create($DOC_ROOT . $tmp_folder_path . "start/" . $filename);
            $thumb->resize($new_width);
            $thumb->save($DOC_ROOT . $tmp_folder_path . "end/" . $filename);


            /* $img = new imageClass();
              $img->setSourceFile($DOC_ROOT.$tmp_folder_path."start/".$filename);
              $img->setTargetFile($DOC_ROOT.$tmp_folder_path."end/".$filename);
              $img->setJpegOutputQuality(100);
              $img->resizeToWidth($new_width); */
        } else {
            $new_width = $width;
            $new_height = $height;
            @copy($DOC_ROOT . $tmp_folder_path . "start/" . $filename, $DOC_ROOT . $tmp_folder_path . "end/" . $filename);
        }
        ?>[
        {
        folder: "<?= $uniq_temp_folder ?>",
        img_name: "<?= $filename ?>",
        width:<?= str_replace(',', '.', $new_width) ?>,
        height:<?= str_replace(',', '.', $new_height) ?>
        }
        ]<?php 
        exit();
    }
}

function contenttree($request) {

    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

    $root = Node::findRoot();

    $nodeClass = $root->getNodeClass();



    $shownFieldDefs = array();
    $fieldDefs = $nodeClass->getFieldDefs();

    $fieldDefsKeys = array_keys($fieldDefs);

    foreach ($fieldDefsKeys as $k) {

        $fd = $fieldDefs[$k];

        if ($fd->shown) {

            $shownFieldDefs[] = $fd;
        }
    }

    $rootMap = new PHPObject();

    $rootMap->node = $root;

    $ndGR = new NodeGroupRights($currentLoggedUser->group, $root);

    $rootMap->hasWriteRight = $ndGR->hasWriteRight();

    $rootMap->hasViewRight = $ndGR->hasViewRight();

    $rootMap->hasDeleteRight = $ndGR->hasDeleteRight();


    $item = new PHPObject();
    
    $item->nodes = array($rootMap);

    $item->shownFields = $shownFieldDefs;

    $item->fieldCount = sizeof($fieldDefs);

    $item->nodeClass = $nodeClass;



    $request->setAttribute("item", $item);

    usetemplate("nodes/contenttree");
}

function main($request) {

    editnode($request);
}

/*

  function addnode_moder_form(&$request) {

  global $session;

  $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



  $mid = (int)$request->getParameter("mid");

  $ma = &ModeratedAction::findById($mid);



  $data = &$ma->data;



  $nodeClass = &NodeClass::findById($data->class_id);

  $parentId = $data->parent_id;

  $parent = &Node::findById($parentId);



  if ($parent != null) {

  $gr = &new NodeGroupRights($currentLoggedUser->group, $parent);

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



  $item = &new PHPObject();



  $shortname = $fieldDef->shortname;



  $field = &$data->fieldList[$shortname];

  $params = null;

  $params = $field->getParamersForIncludeControlJSP();



  $item->jspname = $field->getJSPControlName();

  $item->fieldDef = &$fieldDef;

  $item->params = $params;

  $iparams[] = &$item;

  }



  $request->setAttribute("fieldDefs", $iparams);

  $request->setAttribute("validationErrors", $fieldErrors);



  $nodePropsMap = new phpobject();

  $nodePropsMap->NodeName = $data->NodeName;

  $nodePropsMap->NodeShortname = $data->NodeShortname;

  $nodePropsMap->NodeDynamicTemplate = $data->NodeDynamicTemplate;

  $nodePropsMap->NodeAdminURL = $data->NodeAdminURL;

  $request->setAttribute("nodeProps", $nodePropsMap);



  $request->setAttribute("moder_action", $ma);



  usetemplate("nodes/addnode");

  }

 */

function generateTempLogin() {
    global $MDB;
    $search = false;
    while (!$search) {
        $MDB->Query('SELECT * FROM dbm_sec_logins ORDER BY rand() LIMIT 0,1');
        $res = $MDB->NextResult();
        $MDB->Query('SELECT * FROM dbm_nodes WHERE name="' . $res['login'] . '" AND parent_id=9392  LIMIT 0,1');
        $count = $MDB->NumRows();
        if (!$count)
            $search = true;
    }
    $MDB->Query('SELECT * FROM dbm_sec_passwords ORDER BY rand() LIMIT 0,2');
    $res1 = $MDB->NextResult();
    $res2 = $MDB->NextResult();
    return array('login' => $res['login'], 'password' => $res1['pass'] . $res2['pass']);
}

function addnode_form($request) {
    if ($_GET['class_id'] == 207) {
        $arr = generateTempLogin();
        $request->setAttribute('login', $arr['login']);
        $request->setAttribute('password', $arr['password']);
        $request->setAttribute('date_start', date('d.m.Y H:i:s'));
        $request->setAttribute('date_end', date('d.m.Y H:i:s', time() + 31 * 24 * 3600));
    }
    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    $nodeClass = NodeClass::findById($request->getParameter("class_id"));

    if ($nodeClass == null) {

        Header("Location: /admin/main.php");
        die();
    }

    $parentId = (int) $request->getParameter("parent_id");

    $parent = Node::findById($parentId);

    if (($parent == null) && (Node::hasRoot())) {

        Header("Location: /admin/main.php");
        die();
    }



    if ($parent != null) {

        $gr = new NodeGroupRights($currentLoggedUser->group, $parent);

        $request->setAttribute("userHasWriteRights", $gr->hasWriteRight());

        $pList = $parent->getParentList();

        $pList[] = $parent;

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



    $iparams = array();

    $fieldDefsKeys = array_keys($fieldDefs);

    foreach ($fieldDefsKeys as $k) {

        $fieldDef = $fieldDefs[$k];



        $item = new PHPObject();



        $field = $fieldDef->getFieldInstance();

        $field->setDefaultValue();

        $params = $field->getParamersForIncludeControlJSP();



        $item->jspname = $field->getJSPControlName();

        $item->fieldDef = $fieldDef;

        $item->params = $params;

        $iparams[] = $item;
    }

    $fieldDefsArray = array($iparams);

    $request->setAttribute("fieldDefsArray", $fieldDefsArray);



    $nodePropsMap = new phpobject();

    $nodePropsMap->NodeDynamicTemplate = $nodeClass->default_template;

    $nodePropsArray = array($nodePropsMap);



    $formsCount = 1;



    $request->setAttribute("nodePropsArray", $nodePropsArray);

    $request->setAttribute("formsCount", $formsCount);


    if ($_GET['class_id'] == 207) {
        usetemplate("nodes/addnode_tempuser");
    } else {
        usetemplate("nodes/addnode");
    }
}

function addnode($request) {

    if (isset($_POST['class_id']) && $_POST['class_id'] == 207) {
        $login = $_POST['f00_NodeName'];
        $password = $_POST['f00_password'];
        @mkdir($_SERVER['DOCUMENT_ROOT'] . '/files/secure_files/tempfiles/' . $login . '/', 0777);
        @chmod($_SERVER['DOCUMENT_ROOT'] . '/files/secure_files/tempfiles/' . $login . '/', 0777);
    }
    global $session, $AdminTrnsl;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    $nodeClass = NodeClass::findById((int) $request->getParameter("class_id"));

    //print_r($nodeClass->fieldDefs["image"]->fieldParameters);



    if ($nodeClass == null) {

        Header("Location: /admin/main.php");
        die();
    }

    $parentId = (int) $request->getParameter("parent_id");

    $parent = Node::findById($parentId);

    if (($parent == null) && (Node::hasRoot())) {

        Header("Location: /admin/main.php");
        die();
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

        $pList = $parent->getParentList();

        $pList[] = $parent;

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



    $formsCount = (int) $request->getParameter("forms_count");



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

            $fieldDef = $fieldDefs[$k];

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

        $NodeName = (string) $request->getParameter("NodeName");

        $NodeShortname = (string) $request->getParameter("NodeShortname");

        $NodeDynamicTemplate = (string) $request->getParameter("NodeDynamicTemplate");

        $NodeAdminURL = (string) $request->getParameter("NodeAdminURL");



        if (strlen($NodeName) == 0)
            $fieldErrors["NodeName"] = $AdminTrnsl["RequiredField"];



        if ($parent != null) {

            if (!Validator::validateShortname($NodeShortname)) {

                $fieldErrors["NodeShortname"] = $AdminTrnsl["NodeEditInvalidShortname"];
            }
        } else {

            $NodeShortname = "";
        }



        if (($parent != null) && $parent->hasChildren($NodeShortname)) {

            $fieldErrors["NodeShortname"] = $AdminTrnsl["NodeEditShortnameUsed"];
        }



        checkNodeNameByClass($NodeName, $nodeClass, $fieldList);

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



            $createdNode = Node::createWithPreparedValues($parent, $nodePropsMap->NodeName, $nodePropsMap->NodeShortname, $nodePropsMap->NodeDynamicTemplate, $nodePropsMap->NodeAdminURL, $currentLoggedUser->id, $nodeClass->id, $fieldList);

            //if ($ma) {
            //	ModeratedAction::remove($ma->id);
            //}
        }

        /* } else {

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

          $forwardUrl = "/admin/nodes.php?do=main&id=" . $parent->id . "&msg=nodes_created_moderated";

          } */

        $forwardUrl = "/admin/nodes.php?do=main&id=";

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

            $fieldDef = $fieldDefs[$k];



            $item = new PHPObject();



            $shortname = $fieldDef->shortname;

            $errMsg = $fieldErrors[$shortname];



            $field = $fieldList[$shortname];

            $params = $field->getParamersForIncludeControlJSP();



            $item->jspname = $field->getJSPControlName();

            $item->fieldDef = $fieldDef;

            $item->params = $params;

            $iparams[] = $item;
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



    usetemplate("nodes/addnode");
}

function deletenode($request) {

    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    $node_id = (int) $request->getParameter("id");

    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();
    }



    $parent_id = $node->parent_id;



    $forwardUrl = null;



    if ($parent_id != null) {

        $forwardUrl = "/admin/nodes.php?do=main&id=" . $parent_id . "&reload=1&";
    } else {

        $forwardUrl = "/admin/main.php?";
    }



    if ($currentLoggedUser->group->isModerator) {
        checkNodeOnRemove($node_id);
        Node::remove($node_id);
    } else {

        $moderatedCreateAction = new PHPObject();

        $moderatedCreateAction->NodeName = $node->name;

        $moderatedCreateAction->NodePath = $node->absolutePath;

        ModeratedAction::create($node_id, $currentLoggedUser->id, "delete", $moderatedCreateAction);

        $forwardUrl .= "msg=nodes_deleted_moderated";
    }



    Header("Location: " . $forwardUrl);
}

function deleteselectednode($request) {

//print_r($request->getParameter("delete_node"));

    $deletenodes = $request->getParameter("delete_node");

    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

    foreach ($deletenodes as $k => $node_id) {

        //	$node_id = (int)$request->getParameter("id");

        $node = Node::findById($node_id);

        if ($node == null) {

            Header("Location: /admin/main.php");
            die();
        }



        $parent_id = $node->parent_id;



        $forwardUrl = null;



        if ($parent_id != null) {

            $forwardUrl = "/admin/nodes.php?do=main&id=" . $parent_id . "&reload=1&";
        } else {

            $forwardUrl = "/admin/main.php?";
        }



        if ($currentLoggedUser->group->isModerator) {

            checkNodeOnRemove($node_id);
            Node::remove($node_id);
        } else {

            $moderatedCreateAction = new PHPObject();

            $moderatedCreateAction->NodeName = $node->name;

            $moderatedCreateAction->NodePath = $node->absolutePath;

            ModeratedAction::create($node_id, $currentLoggedUser->id, "delete", $moderatedCreateAction);

            $forwardUrl .= "msg=nodes_deleted_moderated";
        }
    }

    Header("Location: " . $forwardUrl);
}

function editnode_moder($request) {

    global $session, $AdminTrnsl;



    $mid = (int) $request->getParameter("mid");

    $ma = ModeratedAction::findById($mid);



    $data = $ma->data;



    $node_id = $ma->node_id;

    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();
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

        $fieldDef = $fieldDefs[$k];

        $fieldShortname = $fieldDef->shortname;

        $field = $data->fieldList[$fieldShortname];

        $params = $field->getParamersForIncludeControlJSP();



        $item = new PHPObject();

        $item->jspname = $field->getJSPControlName();

        $item->fieldDef = $fieldDef;

        $item->params = $params;

        $iparams[] = $item;
    }



    $request->setAttribute("fieldDefs", $iparams);

    $request->setAttribute("NodeName", $data->NodeName);

    subnodeslist($request, $node, $all_deps, $currentLoggedUser);



    $request->setAttribute("moder_action", $ma);



    usetemplate("nodes/editnode");
}

function editnode($request) {

    global $session, $AdminTrnsl;

    $node_id = (int) $request->getParameter("id");



    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();
    }



    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
    //	print_r($currentLoggedUser);


    $gr = new NodeGroupRights($currentLoggedUser->group, $node);
    //	print_r($gr);

    if (!$gr->hasViewRight()) {

        die($AdminTrnsl["You_do_not_have_rights_to_view_this_node"]);
    }



    $all_deps = $node->findAllDepends();

    $request->setAttribute("depends", $all_deps);
    //print_r($all_deps);



    $nodeClass = $node->getNodeClass();

    $request->setAttribute("nodeClass", $nodeClass);



    $request->setAttribute("node", $node);

    $fieldDefs = $nodeClass->getFieldDefs();



    $iparams = array();



    $fieldDefsKeys = array_keys($fieldDefs);

    foreach ($fieldDefsKeys as $k) {

        $fieldDef = $fieldDefs[$k];

        $fieldShortname = $fieldDef->shortname;

        $field = $node->getField($fieldShortname);

        $params = $field->getParamersForIncludeControlJSP();



        $item = new PHPObject();

        $item->jspname = $field->getJSPControlName();

        $item->fieldDef = $fieldDef;

        $item->params = $params;

        $iparams[] = $item;
    }

    $request->setAttribute("fieldDefs", $iparams);

    $request->setAttribute("NodeName", $node->name);

    subnodeslist($request, $node, $all_deps, $currentLoggedUser);



    usetemplate("nodes/editnode");
}

function subnodeslist($request, $node, $all_deps, $currentLoggedUser) {

    $pageNumMap = $request->getParameter("pagenum");

    $childrenByClasses = array();

    $childrenCount = 0;



    $all_deps_keys = array_keys($all_deps);

    foreach ($all_deps_keys as $k) {

        $nodeClass = $all_deps[$k];



        $item = new PHPObject();



        $item->nodeClass = $nodeClass;

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

          } */



        if ($classChildsCount <= NODES_PER_PAGE) {

            $classChilds = $node->getChildrenOfClassForGroup($currentLoggedUser->group->id, $nodeClass, $orderBy);

            $item->nodespagesCount = 0;
        } else {

            $page = (int)($pageNumMap[$nodeClass->shortname] ?? 0); // "" or junk: 0 on PHP 5, TypeError in PHP 8 (and it reaches SQL as the offset)

            $pagesCount = floor($classChildsCount / NODES_PER_PAGE) + ($classChildsCount % NODES_PER_PAGE != 0 ? 1 : 0);

            $offset = $page * NODES_PER_PAGE;

            $classChilds = $node->getChildrenOfClassForGroup($currentLoggedUser->group->id, $nodeClass, $orderBy, $offset, NODES_PER_PAGE);



            $item->nodespagesCount = $pagesCount;

            $item->nodesOffset = $offset;

            $item->nodesPage = $page;
        }

        $classChildsP = array();

        $classChildsKeys = array_keys($classChilds);



        foreach ($classChildsKeys as $cck) {

            $nd = $classChilds[$cck];



            $ndMap = new PHPObject();

            $ndMap->node = $nd;

            $ndGR = new NodeGroupRights($currentLoggedUser->group, $nd);

            $ndMap->hasWriteRight = $ndGR->hasWriteRight();

            $ndMap->hasViewRight = $ndGR->hasViewRight();

            $ndMap->hasDeleteRight = $ndGR->hasDeleteRight();



            $classChildsP[] = $ndMap;
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

        $fieldDefs = $nodeClass->getFieldDefs();

        $fieldDefsKeys = array_keys($fieldDefs);

        foreach ($fieldDefsKeys as $fdk) {

            $fieldDef = $fieldDefs[$fdk];

            if ($fieldDef->shown) {

                $shownFieldDefs[] = $fieldDef;
            }
        }



        $item->shownFields = $shownFieldDefs;

        $item->fieldCount = sizeof($fieldDefs);

        $childrenByClasses[] = $item;
    }



    $request->setAttribute("childrenByClasses", $childrenByClasses);

    $request->setAttribute("childrenCount", $childrenCount);
}

function updatenode($request) {

    global $session, $AdminTrnsl;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

    $node_id = (int) $request->getParameter("id");

    $node = Node::findById($node_id);





    if ($node == null) {

        Header("Location: /admin/main.php");
        die();
    }



    $mid = (int) $request->getParameter("moder_id");

    if ($mid != 0) {

        $ma = ModeratedAction::findById($mid);
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

        $field = $node->fields[$fieldShortname];

        $fieldDef = $field->getFieldDef();

        $verror = $field->setValue($request);

        if ($verror != null) {

            $fieldErrors[$fieldDef->shortname] = $verror;
        }

        $fieldList[$fieldDef->shortname] = $field;
    }







    $NodeName = (string) $request->getParameter("NodeName");

    if (strlen($NodeName) == 0)
        $fieldErrors["NodeName"] = $AdminTrnsl["RequiredField"];

    //		print_r($fieldErrors);
    //		echo (int)count($fieldErrors);
    //if validation not failed update fields data

    if (sizeof($fieldErrors) == 0) {

        //print_r($node);

        $forwardUrl = "/admin/nodes.php?do=main&reload=yes&id=" . $node->id;

        if ($currentLoggedUser->group->isModerator) {

            $node->name = $NodeName;

            $node->store();

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

        checkNodeName($NodeName, $node);

        Header("Location: " . $forwardUrl);

        return;
    }



    $request->setAttribute("node", $node);



    $iparams = array();

    $fieldDefs = $nodeClass->getFieldDefs();

    $_keys = array_keys($fieldDefs);

    foreach ($_keys as $_k) {

        $fieldDef = $fieldDefs[$_k];

        $item = new phpobject();



        $fieldShortname = $fieldDef->shortname;

        $field = $fieldList[$fieldShortname];

        $params = $field->getParamersForIncludeControlJSP();



        $item->jspname = $field->getJSPControlName();

        $item->fieldDef = $fieldDef;

        $item->params = $params;

        $iparams[] = $item;
    }

    $request->setAttribute("fieldDefs", $iparams);

    $request->setAttribute("validationErrors", $fieldErrors);



    $request->setAttribute("NodeName", $NodeName);

    subnodeslist($request, $node, $all_deps, $currentLoggedUser);



    if ($ma) {

        $request->setAttribute("moder_action", $ma);
    }



    usetemplate("nodes/editnode");
}

function properties($request) {

    global $session;

    $node_id = (int) $request->getParameter("node_id");



    $node = Node::findById($node_id);

    if ($node == null) {

        die("Invalid request argument");
    }



    $nodeMap = new phpobject();

    $nodeMap->NodeShortname = $node->shortname;

    $nodeMap->NodeDynamicTemplate = $node->dynamicTemplate;

    $nodeMap->NodeAdminURL = $node->adminUrl;

    $nodeMap->NodeID = $node->id;

    $nodeMap->id = $node->id;

    $nodeMap->parent_id = $node->parent_id;



    $request->setAttribute("nodeMap", $nodeMap);

    $request->setAttribute("nodeIsRoot", $node->isRoot());

    $request->setAttribute("node", $node);



    usetemplate("nodes/properties");
}

function properties_update($request) {

    global $session;

    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);

    if ($node == null) {

        die("Invalid request argument");
    }



    $nodeClass = $node->getNodeClass();



    $parent = $node->getParent();



    //now validate node fields

    $fieldErrors = array();

    $NodeShortname = $request->getParameter("NodeShortname");

    $NodeDynamicTemplate = $request->getParameter("NodeDynamicTemplate");

    $NodeAdminURL = $request->getParameter("NodeAdminURL");



    if ($parent != null) {

        if ($NodeShortname == null) {

            $NodeShortname = "";
        }

        if (!Validator::validateShortname($NodeShortname)) {

            $fieldErrors["NodeShortname"] = "Bad shorname";
        }
    } else {

        $NodeShortname = "";
    }



    if ($parent != null) {

        if ($NodeShortname == null)
            $NodeShortname = "";

        if (strlen($NodeShortname) == 0)
            $fieldErrors["NodeShortname"] = "Required field";
    } else {

        $NodeShortname = "";
    }



    if (!($NodeShortname == $node->shortname) && ($parent != null) && $parent->hasChildren($NodeShortname)) {

        $fieldErrors["NodeShortname"] = "Duplicate shortname in this subtree";
    }



    if ($NodeDynamicTemplate == null)
        $NodeDynamicTemplate = "";

    if ($NodeAdminURL == null)
        $NodeAdminURL = "";



    if (sizeof($fieldErrors) == 0) {

        $node_id = $node->id;

        $node->setShortname($NodeShortname);

        $node->dynamicTemplate = $NodeDynamicTemplate;

        $node->adminUrl = $NodeAdminURL;

        $node->store();



        header("Location: /admin/nodes.php?do=properties&msg=Node_properties_update_success&reload=true&node_id=" . $node_id);
    }



    $nodeMap = new phpobject();

    $nodeMap->NodeShortname = $NodeShortname;

    $nodeMap->NodeDynamicTemplate = $NodeDynamicTemplate;

    $nodeMap->NodeAdminURL = $NodeAdminURL;

    $nodeMap->NodeID = $node->id;

    $nodeMap->id = $node->id;

    $nodeMap->parent_id = $node->parent_id;



    $request->setAttribute("nodeMap", $nodeMap);

    $request->setAttribute("node", $node);

    $request->setAttribute("nodeIsRoot", $node->isRoot());

    $request->setAttribute("validationErrors", $fieldErrors);



    usetemplate("nodes/properties");
}

function dependencies($request) {

    global $session;

    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();

        return;
    }

    $action = $request->getParameter("action");



    if ($action == "adddep") {

        $class_id = (int) $request->getParameter("class_id");

        $nc = NodeClass::findById($class_id);

        $node->addDependentClass($nc);

        header("Location: /admin/nodes.php?do=dependencies&node_id=" . $node->id);
    } else if ($action == "removedep") {

        $class_id = (int) $request->getParameter("class_id");

        $node->removeDepend(NodeClass::findById($class_id));

        header("Location: /admin/nodes.php?do=dependencies&node_id=" . $node->id);
    }



    $deps = $node->findDepends();

    $nodeClass = $node->getNodeClass();

    $classDeps = $nodeClass->getDependentClasses();

    $allClasses = NodeClass::findAll();

    $availClasses = array();

    foreach ($allClasses as $nodeClass)
        if (!in_array($nodeClass->id, array_keys($classDeps)) && !in_array($nodeClass->id, array_keys($deps))) {

            $availClasses[] = $nodeClass;
        }



    $request->setAttribute("availClasses", $availClasses);

    $request->setAttribute("nodeDeps", $deps);



    $request->setAttribute("node", $node);

    usetemplate("nodes/dependencies");
}
function permissions($request) {

    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();

        return;
    }



    $action = $request->getParameter("action");

    $recurs = $request->getParameter("recursive");
    if ($action != null) {

        $cg = UserGroup::findAll();

        if (sizeof($cg) != 0) {
            
            foreach ($cg as $ug) {

                if ($ug->id == $currentLoggedUser->group->id || $ug->id == 1)
                    continue;
                if(isset($_POST['recursive']) && $_POST['recursive'] == 1 && $_POST['run_group']!=0 && $ug->id!=$_POST['run_group']){
                    continue;
                }
                
                $ngr = new NodeGroupRights($ug, $node);


                if ($request->getParameter($ug->id . "_r") == null) {

                    $ngr->unsetViewRight();
                } else {

                    $ngr->setViewRight();
                }

                if ($request->getParameter($ug->id . "_d") == null) {

                    $ngr->unsetDeleteRight();
                } else {

                    $ngr->setDeleteRight();
                }

                if ($request->getParameter($ug->id . "_w") == null) {

                    $ngr->unsetWriteRight();
                } else {

                    $ngr->setWriteRight();
                }


                if ($request->getParameter($ug->id . "_r_c") == null) {

                    $ngr->unsetViewRightForChildren();
                } else {

                    $ngr->setViewRightForChildren();
                }

                if ($request->getParameter($ug->id . "_d_c") == null) {

                    $ngr->unsetDeleteRightForChildren();
                } else {

                    $ngr->setDeleteRightForChildren();
                }

                if ($request->getParameter($ug->id . "_w_c") == null) {

                    $ngr->unsetWriteRightForChildren();
                } else {

                    $ngr->setWriteRightForChildren();
                }

                //----------

                $ngr->store();
                //edump($_POST['recursive']);
//                                echo $ngr->getRightsForChildren();
//                                die();
                if (isset($_POST['recursive']) && $_POST['recursive'] == 1) {

                    $node->setRightsToChildren($ug, $ngr->getRightsForChildren());
                }
//				if ($recurs != null) {
//
//					$node->setRightsToChildren($ug, $ngr->getRights());
//
//				}
            }
        }

        header("Location: /admin/nodes.php?do=permissions&node_id=" . $node->id . "&msg=Node_permissions_update_successefull&reload=true");
    }



    $groups = UserGroup::findAll();

    $rights = array();

    foreach ($groups as $g) {

        $rights[] = new NodeGroupRights($g, $node);
    }

    $request->setAttribute("groups", $groups);

    $request->setAttribute("rights", $rights);

    $request->setAttribute("node", $node);

    usetemplate("nodes/permissions");
}
/*
function permissions($request) {

    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();

        return;
    }



    $action = $request->getParameter("action");

    $recurs = $request->getParameter("recursive");

    if ($action != null) {

        $cg = UserGroup::findAll();

        if (sizeof($cg) != 0) {

            foreach ($cg as $ug) {

                if ($ug->id == $currentLoggedUser->group->id || $ug->id == 1)
                    continue;

                $ngr = new NodeGroupRights($ug, $node);

                if ($request->getParameter($ug->id . "_r") == null) {

                    $ngr->unsetViewRight();
                } else {

                    $ngr->setViewRight();
                }

                if ($request->getParameter($ug->id . "_d") == null) {

                    $ngr->unsetDeleteRight();
                } else {

                    $ngr->setDeleteRight();
                }

                if ($request->getParameter($ug->id . "_w") == null) {

                    $ngr->unsetWriteRight();
                } else {

                    $ngr->setWriteRight();
                }

                //----------
                if ($request->getParameter($ug->id . "_r_c") == null) {

                    $ngr->unsetViewRightForChildren();
                } else {

                    $ngr->setViewRightForChildren();
                }

                if ($request->getParameter($ug->id . "_d_c") == null) {

                    $ngr->unsetDeleteRightForChildren();
                } else {

                    $ngr->setDeleteRightForChildren();
                }

                if ($request->getParameter($ug->id . "_w_c") == null) {

                    $ngr->unsetWriteRightForChildren();
                } else {

                    $ngr->setWriteRightForChildren();
                }
                //----------

                $ngr->store();
//                                echo $ngr->getRightsForChildren();
//                                die();
                $node->setRightsToChildren($ug, $ngr->getRightsForChildren());
//				if ($recurs != null) {
//
//					$node->setRightsToChildren($ug, $ngr->getRights());
//
//				}
            }
        }

        header("Location: /admin/nodes.php?do=permissions&node_id=" . $node->id . "&msg=Node_permissions_update_successefull&reload=true");
    }



    $groups = UserGroup::findAll();

    $rights = array();

    foreach ($groups as $g) {

        $rights[] = new NodeGroupRights($g, $node);
    }

    $request->setAttribute("groups", $groups);

    $request->setAttribute("rights", $rights);

    $request->setAttribute("node", $node);

    usetemplate("nodes/permissions");
}*/

function multi_children_sort($request) {

    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);



    preg_match_all("|([0-9]+)|i", $_POST["node_ids"], $matches);

    foreach ($matches[1] as $k => $v) {

        $params = array(
            array($k + 1, $v),
        );

        $sql = "UPDATE dbm_nodes SET subtree_order=? WHERE id=?";

        $batch = array($sql);

        DBUtils::execBatch($batch, $params);
    }

    $c = $node->getChildrenForGroup($currentLoggedUser->group->id);

    $request->setAttribute("children", $c);

    $request->setAttribute("parents", $node->getParentList());

    $request->setAttribute("node", $node);

    usetemplate("nodes/children_sort");



//print_r($matches[1]);
}

function children_sort($request) {

    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();

        return;
    }



    $action = $request->getParameter("action");

    if ($action != null) {

        $kid_action = $request->getParameter("kid_act");

        $kid_id = $request->getParameter("kid_id");



        $kid = Node::findById($kid_id);



        if ($kid_action == "up") {

            $kid->moveUpInSubtreeOrder();
        } else if ($kid_action == "down") {

            $kid->moveDownInSubtreeOrder();
        } else if ($kid_action == "top") {

            $kid->moveHomeInSubtreeOrder();
        } else if ($kid_action == "bottom") {

            $kid->moveEndInSubtreeOrder();
        }
    }



    $c = $node->getChildrenForGroup($currentLoggedUser->group->id);

    $request->setAttribute("children", $c);

    $request->setAttribute("parents", $node->getParentList());

    $request->setAttribute("node", $node);



    usetemplate("nodes/children_sort");
}

function move_node_xmltree($request) {

    global $session;



    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    Header("Content-Type: text/xml; charset=" . ADMIN_CHARSET);

    print("<?xml version='1.0' encoding='" . ADMIN_CHARSET . "'?>");

    print("<TreeNode>");



    $target_id = (int) $request->getParameter("node_id");

    $generate_from_id = (int) $request->getParameter("parent_id");



    $target = Node::findById($target_id);

    $current = null;

    if ($generate_from_id == -1) {

        $current = Node::findRoot();



        $hasAllowedChildren = $current->hasChildrenForMoveNodeTree($currentLoggedUser, $target);

        $cdeps = $current->findAllDepends();

        $isValid = $currentLoggedUser->canEditNode($current) && !$current->equals($target) && !$current->equals($target->getParent()) && $cdeps[$target->nodeClassId] != null;



        print("<TreeNode NodeId=\"tree");

        print($current->id);

        print("\" Title=\"");

        print(prepareStringForXML($current->name));

        print('"');

        if ($isValid) {

            print(" Href=\"javascript: selectNode(");

            print($current->id);

            print(")\"");
        }



        if ($hasAllowedChildren) {

            print(" expanded=\"true\"");

            print('>');



            print(move_node_xmltree_getNodeXML($current, $target, $currentLoggedUser));



            print("</TreeNode>");
        } else {

            print("/>");
        }
    } else {

        $current = Node::findById($generate_from_id);

        if ($current != null) {

            print(move_node_xmltree_getNodeXML($current, $target, $currentLoggedUser));
        }
    }



    print("</TreeNode>");
}

function move_node_xmltree_getNodeXML($node, $target, $currentLoggedUser) {

    $strBuffer = "";

    $children = $node->getChildrenForUser($currentLoggedUser);

    for ($i = 0; $i < sizeof($children); $i++) {

        $child = $children[$i];



        $title = $child->name;



        $hasAllowedChildren = $child->hasChildrenForMoveNodeTree($currentLoggedUser, $target);

        $tparent = $target->getParent();

        $cdeps = $child->findAllDepends();

        $isValid = $child->id != $target->id && $child->id != $tparent->id && $currentLoggedUser->canEditNode($child) && $cdeps[$target->nodeClassId] != null;



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

function move_node_treeconfig($request) {

    $node_id = (int) $request->getParameter("node_id");



    Header("Content-Type: text/xml; charset=" . ADMIN_CHARSET);

    echo "<?xml version='1.0' encoding='" . ADMIN_CHARSET . "'?>";

    echo "<deeptreeconfig>";

    echo "  <TopXMLSrc>/admin/nodes.php?do=move_node_xmltree&amp;node_id=" . $node_id . "&amp;parent_id=-1</TopXMLSrc>";

    echo "  <TreeLabel>Test tree</TreeLabel>";

    echo "  <StartPage>/</StartPage>";

    echo "  <ErrorPage>http://www.microsoft.com</ErrorPage>";

    echo "  <ContentTarget>fraContent</ContentTarget>";

    echo "  <TreeId>cmtree</TreeId>";

    echo "  <TreeRootPath>/admin/main.php</TreeRootPath>";

    echo "  <Locale>en-us</Locale>";

    echo "  <LocaleTextDirection>LTR</LocaleTextDirection>";

    echo "</deeptreeconfig>";
}

function moveselectednode($request) {
    $nodes = explode(',', $request->getParameter("node_ids"));

    //p($nodes,1);
    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);


    foreach ($nodes as $k => &$v) {
        $node_id = (int) $v;
        $target = Node::findById($node_id);
        $root = Node::findRoot();
        $hasAllowedChildren = $root->hasChildrenForMoveNodeTree($currentLoggedUser, $target);
        $rdeps = $root->findAllDepends();
        $isValid = $root->id != $target->id && !$root->equals($target->getParent()) && $currentLoggedUser->canEditNode($root) && $rdeps[$target->nodeClassId] != null;
        if (!$hasAllowedChildren && !$isValid) {
            $request->setAttribute("NoValidNodes", true);
        }
    }
    usetemplate("nodes/move_nodes");
}

function move_node_form($request) {

    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

    $node_id = (int) $request->getParameter("node_id");



    $target = Node::findById($node_id);

    $root = Node::findRoot();



    $hasAllowedChildren = $root->hasChildrenForMoveNodeTree($currentLoggedUser, $target);

    $rdeps = $root->findAllDepends();

    $isValid = $root->id != $target->id && !$root->equals($target->getParent()) && $currentLoggedUser->canEditNode($root) && $rdeps[$target->nodeClassId] != null;



    if (!$hasAllowedChildren && !$isValid) {

        $request->setAttribute("NoValidNodes", true);
    }

    usetemplate("nodes/move_node");
}

function move_nodes($request) {

    global $session;
    $nodes = explode(',', $request->getParameter("node_ids"));
    foreach ($nodes as $k => &$v) {
        $node_id = (int) $v;
        $node = Node::findById($node_id);
        $request->setAttribute("oldParent", $node->getParent());
        $node->moveToNewParent((int) $request->getParameter("np_id"));
    }
    usetemplate("nodes/move_nodes_ok");
}

function move_node($request) {


    global $session;

    $node = Node::findById((int) $request->getParameter("node_id"));

    $request->setAttribute("oldParent", $node->getParent());

    $node->moveToNewParent((int) $request->getParameter("np_id"));

    usetemplate("nodes/move_node_ok");
}

/*

  function importxmlform(&$request) {

  global $session;

  $node_id = (int)$request->getParameter("id");



  $node = Node::findById($node_id);

  if (node == null) {

  $request->setAttribute("error_msg", "Invalid request argument");

  }

  $request->setAttribute("node", node);



  usetemplate(request, response, "nodes/importxml");

  } catch (ServletException e) {

  throw e;

  } catch (Exception e) {

  e.printStackTrace(System.err);

  }

  }

  /*

  function importxml(&$request) {

  global $session;

  $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

  $node_id = (int)$request->getParameter("id");



  $node = Node::findById($node_id);

  if (node == null) {

  $request->setAttribute("error_msg", "Invalid request argument");

  }



  Validator validationErrors = new Validator();



  String xmlFilePath = $request->getParameter("xmlfile.file");

  FileReader fr = new FileReader(xmlFilePath);

  Node.importFromXML(fr, node, currentLoggedUser);

  } catch(Throwable e) {

  validationErrors.setErrorMsg("xmlfile", e.getMessage());

  }



  $request->setAttribute("validationErrors", validationErrors.getValidationErrors());

  $request->setAttribute("node", node);




  usetemplate(request, response, "nodes/importxml");

  } catch (ServletException e) {

  throw e;

  } catch (Exception e) {

  e.printStackTrace(System.err);

  }

  }

  /*

  function exportxmlform(&$request) {

  global $session;

  $node_id = (int)$request->getParameter("id");



  $node = Node::findById($node_id);

  if (node == null) {

  $request->setAttribute("error_msg", "Invalid request argument");

  }

  $request->setAttribute("node", node);



  usetemplate(request, response, "nodes/exportxml");

  } catch (ServletException e) {

  throw e;

  } catch (Exception e) {

  e.printStackTrace(System.err);

  }

  }

  /*

  function exportxml(&$request) {

  global $session;

  $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

  $node_id = (int)$request->getParameter("id");



  $node = Node::findById($node_id);

  if (node == null) {

  $request->setAttribute("error_msg", "Invalid request argument");

  }



  boolean exportNode = $request->getParameter("exportnode") != null;

  Reader xmlReader;

  if (exportNode == false) {

  xmlReader = node.exportChildrenToXML();

  } else {

  xmlReader = node.exportToXML();

  }

  response.addHeader("Content-disposition", "filename=" + node.shortname + ".xmld");

  response.addHeader("Pragma", "no-cache");

  response.addHeader("Expires", "0");

  response.setContentType("application/octetstream");

  int i;

  Writer out = response.getWriter();

  while ((i=xmlReader.read()) != -1) {

  out.write(i);

  }

  out.flush();

  } catch (Exception e) {

  e.printStackTrace(System.err);

  }

  }

 */

function children_sort_class($request) {

    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();

        return;
    }


    $c = $node->getChildrenOfClassesForGroup($currentLoggedUser->group->id, array($_GET['class']));



    $nodeClass = NodeClass::findByShortname($_GET['class']);
    $shownFieldDefs = array();
    $fieldDefs = $nodeClass->getFieldDefs();
    $fieldDefsKeys = array_keys($fieldDefs);
    foreach ($fieldDefsKeys as $fdk) {
        $fieldDef = $fieldDefs[$fdk];
        if ($fieldDef->shown) {
            $shownFieldDefs[] = $fieldDef;
        }
    }
    //print_r($shownFieldDefs);
    //exit();
    $node->shownFields = $shownFieldDefs;
    $node->nodeClass = $nodeClass;



    $request->setAttribute("children", $c);

    $request->setAttribute("parents", $node->getParentList());

    $request->setAttribute("node", $node);


    usetemplate("nodes/children_sort_class");
}

function multi_children_sort_class($request) {

    global $session;
    $sorts = explode(',', $_POST['sorts']);
    $node_ids = explode(',', $_POST['node_ids']);

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);
    foreach ($node_ids as $k => $v) {
        $params = array(
            array($sorts[$k], $v),
        );

        $sql = "UPDATE dbm_nodes SET subtree_order=? WHERE id=?";

        $batch = array($sql);

        DBUtils::execBatch($batch, $params);
    }
    header('Location: /admin/nodes.php?do=children_sort_class&node_id=' . $node->id . '&class=' . $_POST['class'] . '&reload=1');
    exit();
}

//sdump(ST-getmicrotime1());


function comments($request) {

    global $session;

    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();

        return;
    }

    //$action = $request->getParameter("action");
    //$request->setAttribute("children", $c);
    //$request->setAttribute("parents", $node->getParentList());

    $request->setAttribute("node", $node);
    usetemplate("nodes/comments");
}

function add_comment($request) {
    $node_id = (int) $request->getParameter("node_id");
    $parent_id = (int) $request->getParameter("parent_id");
    $name = $request->getParameter("name");
    $text = $request->getParameter("text");
    global $MDB;
//    $MDB->Query("SELECT id FROM dbm_nodes WHERE name = '". db_escape($name) . "' AND class_id = 212");
//    $res = $MDB->NextResult();
//    $user_id = $res ? $res['id'] : 0;
    if ($id = addComment($node_id, $parent_id, $name, $text, $user_id))
        die(getCommentPage($id, $node_id, ADMIN_COMMENTS_PER_PAGE));
    else
        die('false');
}

function remove_comments($request) {
    global $MDB;
    $comments = explode('|', $request->getParameter("comments"));
    foreach ($comments as $k => $v)
        $comments[$k] = (int) $v;
    if (sizeof($comments)) {
        $MDB->QDel("dbm_comments", 'id=' . implode(" OR id=", $comments));
        $node_id = (int) $request->getParameter("node_id");
        $node = QNode::getInstance($node_id);
        if ($node->nodeClassId == 213)
            $MDB->QUpdate("dbm_nfv_idea", array('comments_count' => getNumberOfComments($node_id), "last_comment_time" => date('Y-m-d H:i:s')), "node_id = $node_id");
        if ($node->nodeClassId == 218)
            $MDB->QUpdate("dbm_nfv_blog_item", array('counter' => getNumberOfComments($node_id), "date_last" => date('Y-m-d H:i:s')), "node_id = $node_id");
    }
    die('true');
}

function block_comments($request) {
    global $MDB;
    $comments = explode('|', $request->getParameter("comments"));
    foreach ($comments as $k => $v)
        $comments[$k] = (int) $v;
    if (sizeof($comments)) {
        $MDB->QUpdate("dbm_comments", array('active' => $request->getParameter("active")), 'id=' . implode(" OR id=", $comments));
    }
    die('true');
}

function save_comment($request) {
    global $MDB;
    $MDB->QUpdate("dbm_comments", array('text' => CProtect::short_html($request->getParameter("text"), 2000)), "id = " . (int) $request->getParameter("id"));
    die('true');
}

function seo($request) {

    global $session;
    global $MDB;
    global $SETUP_LANGUAGES;
    global $SETUP_LANGUAGES_TITLE;
    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

    $node_id = (int) $request->getParameter("node_id");

    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();

        return;
    }

    //$action = $request->getParameter("action");
    //$request->setAttribute("children", $c);
    //$request->setAttribute("parents", $node->getParentList());
    //error_reporting(E_ALL);
    //ini_set('display_errors','On');
    $request->setAttribute("node", $node);
    if (isset($_GET['do1']) && $_GET['do1'] == 'update') {
        $MDB->Query("SELECT * FROM dbm_seo WHERE node_id = $node_id AND lang='" . $_GET['lang'] . "'");
        $seo = $MDB->NextResult();
        if ($seo) {
            $MDB->QUpdate('dbm_seo', array(
                'lang' => $_GET['lang'],
                'node_id' => $_GET['node_id'],
                'seo_title' => $_POST['seo_title'],
                'seo_keywords' => $_POST['seo_keywords'],
                'seo_description' => $_POST['seo_description'],
                'seo_google' => $_POST['seo_google'],
                'seo_google_childs' => $_POST['seo_google_childs']
                    ), " node_id = $node_id AND lang='" . $_GET['lang'] . "'");
        } else {
            $MDB->QIns('dbm_seo', array(
                'lang' => $_GET['lang'],
                'node_id' => $_GET['node_id'],
                'seo_title' => $_POST['seo_title'],
                'seo_keywords' => $_POST['seo_keywords'],
                'seo_description' => $_POST['seo_description'],
                'seo_google' => $_POST['seo_google'],
                'seo_google_childs' => $_POST['seo_google_childs']
            ));
        }
        header('Location: /admin/nodes.php?do=seo&node_id=' . $node_id . ' &do1=success');
        exit();
    }
    if (isset($_GET['lang']))
        usetemplate("nodes/seo_lang");
    else
        usetemplate("nodes/seo");
}

function clone_node($request) {
    global $session;
    global $MDB;
    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
    $node_id = (int) $request->getParameter("id");
    $node = Node::findById($node_id);
    if ($node == null) {
        Header("Location: /admin/main.php");
        die();
    }
    $cloneSubNodes = $request->getParameter("clonesubnodes") == 'true' ? true : false;
    cloneNode($node_id, $node->getParent()->id, $request->getParameter("clonesubnodes") == 'true' ? true : false);
    header('Location: /admin/nodes.php?do=main&id=' . $node->getParent()->id . '&msg=Clone_success&reload=1');
    exit();
}

function cloneNode($node_id, $parent_id, $cloneSubNodes, $is_child = false) {
    global $MDB;
    $node = QNode::getInstance($node_id);
    $class = NodeClass::findById($node->nodeClassId);
    $clone = QNode::createNode(QNode::getInstance($parent_id)->absolutePath, $node->name . ($is_child ? '' : '_copy'), $is_child ? $node->shortname : DBUtils::getNextSequnceID("nodes"), $class->shortname);
    $clone->fields = $node->fields;
    $clone->dynamicTemplate = $node->dynamicTemplate;
    $MDB->Query("SELECT * FROM dbm_class_fields");
    $r = $MDB->getRecords();
    foreach ($r as $k => $v)
        $class_fields[$v['id']] = $v['type_id'];
    if ($clone->fields)
        foreach ($clone->fields as $k => $v) {
            if (in_array($class_fields[$v->fieldDefId], array(9, 15))) //file, server file
                $clone->fields[$k]->value = '';
            if (in_array($class_fields[$v->fieldDefId], array(14, 24)) && $clone->fields[$k]->value) { //new image
                //edump($class->fieldDefs['photo'],'',10);
                $cloneImageDir =  $clone->getHref();
                $fileName = explode('/', $clone->fields[$k]->value);
                $fileName = array_pop($fileName);
                $imgFolders = array('i', 'o', 'ithumb', 'ithumb2', 'ithumb3');
                foreach ($imgFolders as $imgFolder) {
                    if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/s/$imgFolder" . $clone->fields[$k]->value)) {
                        FileWorks::mkdir_recursive($_SERVER["DOCUMENT_ROOT"] . "/s/$imgFolder$cloneImageDir");
                        copy($_SERVER["DOCUMENT_ROOT"] . "/s/$imgFolder" . $clone->fields[$k]->value, $_SERVER["DOCUMENT_ROOT"] . "/s/$imgFolder$cloneImageDir/$fileName");
                    }
                }
                $clone->fields[$k]->value = "$cloneImageDir/$fileName";
            }
        }
    $clone->store();
    if ($cloneSubNodes) {
        $kids = $node->getChildrenIds();
        if ($kids)
            foreach ($kids as $k => $v)
                cloneNode($v, $clone->id, $cloneSubNodes, true);
    }
}

function multiphoto($request) {

    global $session;
    $node_id = (int) $request->getParameter("node_id");
    if (isset($_SESSION['multiphoto'][$node_id])) {
        //unset($_SESSION['multiphoto'][$node_id]);
    }

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/s/tmp/multiphoto/' . session_id() . '/' . $node_id . '/')) {
        //FileWorks::rmdir_recursive($_SERVER['DOCUMENT_ROOT'] .'/files/tmp/multiphoto/'.session_id().'/'.$node_id.'/', true);
    }
    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);



    $node = Node::findById($node_id);

    if ($node == null) {

        Header("Location: /admin/main.php");
        die();

        return;
    }
    $class = NodeClass::findById($node->nodeClassId);
    $request->setAttribute("class", $class);
    $request->setAttribute("node", $node);
    $request->setAttribute("node_id", $node_id);
    //dump($class->fieldDefs['multiphoto'],'',9);
    usetemplate("nodes/multiphoto");
}

function multiphoto_save($request) {

    global $session;
    $node_id = (int) $request->getParameter("node_id");
    $node = Node::findById($node_id);

    $class = NodeClass::findById($node->nodeClassId);
    //sdump($class->fieldDefs['photo']->fieldParameters['save_original'],'',9);
    //edump($request);
    $use_watermak = (int) $request->getParameter("use_watermak");

    $config = $class->fieldDefs['multiphoto']->fieldParameters['default_value'];

    $exp = explode(":", $config);
    $fclass = NodeClass::findById($exp[0]);
    $field = $fclass->getFieldDef((int) trim($exp[1]));
    $watermark_positions = array(
        'cb', 'cc', 'lb', 'lt', 'rb', 'rt'
    );
    $fieldDef=$field;
    // edump($fieldDef);
    if (isset($_SESSION['multiphoto'][$node_id])) {
        foreach ($_SESSION['multiphoto'][$node_id] as $k => $v) {

            $new_node = QNode::createNode($node->getHref(), 'photo', '', $fclass->shortname, array('use_watermark_' . $field->shortname => '1'));
                    
            if ($fieldDef->fieldParameters["parent_save"] == 'false') {
                $prefix = '';
                $imgPath = QNode::getImgPath($new_node);
            } else {
                $prefix = $new_node->id . '_';
                $imgPath = QNode::getImgPath($new_node->getParent());
            }


            FileWorks::mkdir_recursive(FIELD_IMGLIB_THUMBNAILS_DIR .  $imgPath . '/');
            FileWorks::mkdir_recursive(FIELD_IMGLIB_DIR . $imgPath . '/');
            
            if ($fieldDef->fieldParameters["save_original"]) {
                FileWorks::mkdir_recursive(FIELD_IMGLIB_ORIGINAL_DIR . $imgPath . '/');
                @copy($_SERVER['DOCUMENT_ROOT'] . '/s/tmp/multiphoto/' . session_id() . '/' . $node_id . '/'. $v, FIELD_IMGLIB_ORIGINAL_DIR .  $imgPath . '/' . $prefix . $v);
                //edump($_SERVER['DOCUMENT_ROOT'] . '/s/tmp/multiphoto/' . session_id() . '/' . $node_id . '/'. $v, FIELD_IMGLIB_ORIGINAL_DIR .  $imgPath . '/' . $prefix . $v);
            }

            @copy($_SERVER['DOCUMENT_ROOT'] . '/s/tmp/multiphoto/' . session_id() . '/' . $node_id . '/thumb_' . $k, FIELD_IMGLIB_THUMBNAILS_DIR .  $imgPath . '/' . $prefix . $v);
            @copy($_SERVER['DOCUMENT_ROOT'] . '/s/tmp/multiphoto/' . session_id() . '/' . $node_id . '/crop_' . $k, FIELD_IMGLIB_DIR  . $imgPath . '/' . $prefix . $v);
            if ($use_watermak == 1 && $field->fieldTypeId == 24) {
                $size = getimagesize(FIELD_IMGLIB_DIR . $imgPath . '/' . $prefix . $v);

                $thumb = PhpThumbFactory::create(FIELD_IMGLIB_DIR .  $imgPath . '/' . $prefix . $v);
                $thumb->resize($size[0]);
                foreach ($watermark_positions as $k1 => $v1) {
                    if (strlen($field->fieldParameters[$v1]) && file_exists($_SERVER['DOCUMENT_ROOT'] . $field->fieldParameters[$v1])) {
                        $mark_image = $_SERVER['DOCUMENT_ROOT'] . $field->fieldParameters[$v1];
                        $position = $v1;
                        $padding = (int) $field->fieldParameters[$v1 . '_padding'];

                        $thumb->createWatermark($mark_image, $position, $padding);
                    }
                }
                $thumb->save(preg_replace('/\/+/', '/', FIELD_IMGLIB_DIR .  $imgPath . '/' . $prefix . $v));
            }
            QNode::updateNode($new_node, array($field->shortname => preg_replace('/\/+/', '/',  $imgPath . '/' . $prefix . $v)));
        }
        unset($_SESSION['multiphoto'][$node_id]);
    }

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/s/tmp/multiphoto/' . session_id() . '/' . $node_id . '/')) {
        FileWorks::rmdir_recursive($_SERVER['DOCUMENT_ROOT'] . '/s/tmp/multiphoto/' . session_id() . '/' . $node_id . '/', true);
    }
    header('Location: /admin/nodes.php?do=main&id=' . $node_id);
}
?>