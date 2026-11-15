<?php


class QDelivery {



    public static function subscribe($name='', $email='', $category=1, $lang = 'ru') {
        global $MDB;
        $MDB->Query('SELECT email FROM dbm_mails_subscribers WHERE email="'.db_escape($email).'" LIMIT 0,1');
        if($MDB->NumRows()) {
            $MDB->Query('UPDATE dbm_mails_subscribers SET active=1 WHERE email="'.db_escape($email).'"');
        }else {
            $MDB->QIns('dbm_mails_subscribers',array('email'=>$email,'name'=>$name,'date'=>date('Y-m-d H:i:s'),'category_id'=>$category, 'lang_id' => $lang));
        }
    }
    public static function unsubscribe($code='') {
        global $MDB;
        $MDB->Query('SELECT email FROM dbm_mails_subscribers WHERE md5(email)="'.db_escape($code).'" LIMIT 0,1');
        if($MDB->NumRows()) {
            $MDB->Query('UPDATE dbm_mails_subscribers SET active=0 WHERE md5(email)="'.db_escape($code).'"');
        }else{
            //exit();
        }
    }
     public static function unsubscribeByEmail($email='') {
        global $MDB;
        $MDB->Query('SELECT email FROM dbm_mails_subscribers WHERE email="'.db_escape($email).'" LIMIT 0,1');
        if($MDB->NumRows()) {
            $MDB->Query('UPDATE dbm_mails_subscribers SET active=0 WHERE email="'.db_escape($email).'"');
        }else{
            //exit();
        }
    }

}
//////////////////////////////////////////////////////////////////////////////////////////////
?>