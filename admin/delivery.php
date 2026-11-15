<?php
require_once("prepend.php");
$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

$action = (string)$request->getParameter("do");

if ($action == null || $action=="") {
    $action = "_default";
}

checkPrivileges($request);

$action($request);
//CLocale::parse_old_lang('ru',$request);
//CLocale::parse_old_lang('en',$request);
//CLocale::parse_old_lang('by',$request);
function checkPrivileges(&$request) {
    global $session;
    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
    if (!$currentLoggedUser->group->canManageUsers) {
        //die(You_Have_not_rights_to_manage_users);
    }
}

function _default(&$request) {
    global $session;
    $currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

    $request->setAttribute('is_admin',$currentLoggedUser->group->id==1);
    usetemplate("delivery/index",true);
}
function categories(&$request) {
    global $session;
    usetemplate("delivery/categories",true);
}
function subscribers(&$request) {
    usetemplate("delivery/subscribers",true);
}
function subscribers2(&$request) {
    usetemplate("delivery/subscribers2",true);
}
function subscribe_list(&$request) {
    global $MDB;
    $MDB->Query("SELECT count(*) as ncount FROM dbm_mails_subscribers");
    $count = array_pop($MDB->NextResult());
    $limit=$_GET['rows'];
    $sidx=$_GET['sidx'];
    $sord=$_GET['sord'];
    $page=$_GET['page'];

    if( $count >0 ) {
        $total_pages = ceil($count/$limit);
    } else {
        $total_pages = 0;
    }
    if ($page > $total_pages) $page=$total_pages;
    $offset = $limit*($page-1);
    if ($offset<0) $offset = 0;
    $SQL = "SELECT s.*, c.name as category_name FROM dbm_mails_subscribers as s, dbm_mails_categories as c WHERE c.id=s.category_id ORDER BY s.$sidx $sord LIMIT $offset , $limit";
    $MDB->Query($SQL);
    $records=$MDB->getRecords();

    $responce=new PHPObject();
    $responce->page = $page;
    $responce->total = $total_pages;
    $responce->records = $count;

    //$i=0;
    foreach($records as $k => $row) {
        $responce->rows[$k]=array(
                'id'     =>  $row['id'],
                'name'   =>  $row['name'],
                'email'  =>  $row['email'],
                'category'  =>  $row['category_name'],
                'active' =>  $row['active'],
                'sdate'  =>  $row['date'],
                //'actions'  =>  $row['date'],
        );
    }
    echo json_encode($responce); // coment if php 5 break;

}
function create(&$request) {
    usetemplate("delivery/create",true);
}
function delivery(&$request) {
    usetemplate("delivery/delivery",true);
}
function operations(&$request) {
    global $MDB;
    if(isset($_POST['oper'])) {
        switch($_POST['oper']) {
            case 'del':
                $MDB->Query('DELETE FROM dbm_mails_subscribers WHERE id IN ('.$_POST['id'].')');
                echo 'true';
                break;
            case 'edit':
                $e=array();
                if(isset($_POST['email']))
                    $e['email']=$_POST['email'];
                if(count($e)) {
                    $MDB->QUpdate('dbm_mails_subscribers',$e,'id='.$_POST['id']);
                }
                echo 'true';
                break;
            case 'add':
                $e=array();
                if(isset($_POST['email']))
                    $e['email']=$_POST['email'];
                if(count($e)) {
                    $MDB->QIns('dbm_mails_subscribers',$e);
                }
                echo 'true';
                break;
        }
    }
}

function create_mail(&$request) {
    global $MDB;
    $date_send=$_POST['DELdate'];
    $subject=$_POST['subject'];
    $body=$_POST['body'];
    if(isset($_POST['categories'])) {
        $MDB->QIns('dbm_mails_nodes',array('is_send'=>0,'date'=>date('Y-m-d H:i:s'),'categories'=>implode(',',$_POST['categories']),'date_send'=>$date_send,'body'=>$body,'subject'=>$subject));
    }
    header('Location: /admin/delivery.php');
    exit();
}
function edit_mail(&$request) {
    global $MDB;
    $date_send=$_POST['DELdate'];
    $subject=$_POST['subject'];
    $body=$_POST['body'];
    $arr=array('date_send'=>$date_send,'body'=>$body,'subject'=>$subject);
    if($_POST['resend']==1) {
        if(isset($_POST['categories'])) {
            $MDB->QIns('dbm_mails_nodes',array('is_send'=>0,'date'=>date('Y-m-d H:i:s'),'categories'=>implode(',',$_POST['categories']),'date_send'=>$date_send,'body'=>$body,'subject'=>$subject));
        }
        //$arr['is_send']=0;
    }else {
        // $arr['is_send']=1;
        if(!isset($_POST['categories'])) {
            $arr['categories']='';
        }else {
            $arr['categories']=implode(',',$_POST['categories']);
        }
        $MDB->QUpdate('dbm_mails_nodes',$arr,'id='.$_POST['id']);
    }
    header('Location: /admin/delivery.php');
    exit();
}

function delete(&$request) {
    global $MDB;
    $MDB->Query('DELETE FROM dbm_mails_nodes WHERE id='.$_GET['id']);
    header('Location: /admin/delivery.php?selected_tab=0');
    exit();
}
function delete_category(&$request) {
    global $MDB;
    $MDB->Query('DELETE FROM dbm_mails_categories WHERE id='.$_GET['id']);
    $MDB->Query('DELETE FROM dbm_mails_subscribers WHERE category_id='.$_GET['id']);
    header('Location: /admin/delivery.php?selected_tab=2');
    exit();
}
function delete_subscriber(&$request) {
    global $MDB;
    $MDB->Query('DELETE FROM dbm_mails_subscribers WHERE id='.$_GET['id']);
    header('Location: /admin/delivery.php?selected_tab=1');
    exit();
}
function edit(&$request) {
    global $MDB;
    usetemplate("delivery/edit",true);
    exit();
}
function edit_category(&$request) {
    global $MDB;

    if(isset($_POST['name'])) {
        $MDB->QUpdate('dbm_mails_categories',array('name'=>$_POST['name']),'id='.$_POST['id']);
        header('Location: /admin/delivery.php?selected_tab=2');
        exit();
    }
    usetemplate("delivery/edit_category",true);
    exit();
}
function add_category(&$request) {
    global $MDB;

    if(isset($_POST['name'])) {
        $MDB->QIns('dbm_mails_categories',array('name'=>$_POST['name'],'date'=>date('Y-m-d H:i:s')));
        header('Location: /admin/delivery.php?selected_tab=2');
        exit();
    }
    usetemplate("delivery/add_category",true);
    exit();
}
function add_subscriber(&$request) {
    global $MDB;
    if(isset($_POST['name'],$_POST['email'],$_POST['category'])) {
        $MDB->QIns('dbm_mails_subscribers',array('name'=>$_POST['name'],'date'=>date('Y-m-d H:i:s'),'email'=>$_POST['email'],'active'=>1,'category_id'=>$_POST['category']));
        header('Location: /admin/delivery.php?selected_tab=1');
        exit();
    }
    usetemplate("delivery/add_subscriber",true);
    exit();
}
function edit_subscriber(&$request) {
    global $MDB;
    if(isset($_POST['name'],$_POST['email'],$_POST['category'],$_POST['date'],$_POST['id'])) {
    //dump($_POST);exit;
        $MDB->QUpdate('dbm_mails_subscribers',array('name'=>$_POST['name'],'date'=>$_POST['date'],'email'=>$_POST['email'],'active'=>isset($_POST['active']) ? 1 : 0,'category_id'=>$_POST['category']),'id = '.$_POST['id']);
        header('Location: /admin/delivery.php?selected_tab=1');
        exit();
    }
    usetemplate("delivery/edit_subscriber",true);
    exit();
}
function import_s(&$request) {
    global $MDB;
    if(isset($_FILES['file']) && $_FILES['file']['tmp_name']) {
        $file = file($_FILES['file']['tmp_name']);
        foreach($file as $k=>$v) {
            $r=explode(';',substr($v,0,-2));
            if(strlen($r[0]) && preg_match('/^[0-9a-z_\.-]+@[0-9a-z_^\.-]+\.[a-z]{2,6}$/i',$r[0])) {
                $MDB->QIns('dbm_mails_subscribers',array(
                        'email'=>$r[0],
                        'name'=>isset($r[1])?$r[1]:'',
                        'category_id'=>$_POST['category'],
                        'date'=>date('Y-m-d H:i:s'),
                ));
            }
        }

    }
    //dump($_POST);
    header('Location: /admin/delivery.php?selected_tab=1');
    exit();//print_r($_FILES);
}
function export(&$request) {
    global $MDB;
    //dump($_GET);exit;
    header('Content-type: application/txt');
    header('Content-Disposition: attachment; filename="subscribers.csv"');
    $search_str = db_escape($_GET['keyword']);
    $category = (int)$_GET['category'];
    if ($category) $category = "AND s.category_id = $category";
    else $category = '';
    $SQL = "SELECT s.*, c.name as category_name FROM dbm_mails_subscribers as s, dbm_mails_categories as c WHERE (s.email LIKE '%$search_str%' OR s.name LIKE '%$search_str%') AND c.id=s.category_id $category ORDER BY s.id";
    $MDB->Query($SQL);
    $records=$MDB->getRecords();
    foreach($records as $k => $v)
        echo (int)($k + 1) .';'. $v['name'] .';'. $v['email'] .';'. $v['date'] ."\n";
    exit;
}
?>