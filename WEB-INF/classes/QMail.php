<?php

/*******************************************************************************
 *
 *
 *
 *
 *
 *
 * ****************************************************************************/

class QMail {

    /**************************************************************************/

    protected static $instance = NULL;
    private $node = NULL;
    private $adminEmails = array();
    private $adminEmailFrom = '';
    private $adminEmailFromName = '';
    const HASH_STRING = ';eqoi83asd!#%&4sdf3m,nqwe';

    /**************************************************************************/

    public static function getInstance() {
        if(!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**************************************************************************/

    public function  __construct() {
        $settingsNode               =   QNode::getInstance('/_site_settings');
        $this->adminEmails          =   explode(',', $settingsNode->tfields['admin_email']);
        $this->adminEmailFrom       =   $settingsNode->tfields['admin_email_from'];
        $this->adminEmailFromName   =   $settingsNode->tfields['admin_email_from_name'];
    }

    /**************************************************************************/

    // PHPMailer 6+ defaults to ISO-8859-1; the old bundled 5.1 copy defaulted to UTF-8
    private static function createMailer() {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true); // throw exceptions on errors
        $mail->CharSet = \PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
        return $mail;
    }

    /**************************************************************************/

    public static function sendToAdmin($mail_shortname, $replacements) {
        $search_array   =   self::getInstance()->prepareMetaTags($replacements);
        $messageNode    =   QNode::getInstance('/_site_settings/mailtexts/'.$mail_shortname);
        $subject        =   preg_replace(array_keys($search_array),array_values($search_array),$messageNode->tfields['subject']);
        $body           =   preg_replace(array_keys($search_array),array_values($search_array),$messageNode->tfields['body']);
         //dump( $subject);
        /*$config             =   array();
        $config['charset']  = 'UTF-8';
        $config['mailtype'] = 'html';
        $config['wordwrap'] = FALSE;
        foreach(self::getInstance()->adminEmails as $email) {
            $emailObj=new CI_Email($config);
            $emailObj->from(self::getInstance()->adminEmailFrom, self::getInstance()->adminEmailFromName);
            $emailObj->to(trim($email));
            $emailObj->subject($subject);
            $emailObj->message($body);
            $emailObj->send();
        }*/
		foreach(self::getInstance()->adminEmails as $email) {
			$mail = self::createMailer();
			//$mail->IsSendmail(); // telling the class to use SendMail transport
			try {
			  $mail->AddReplyTo(self::getInstance()->adminEmailFrom, self::getInstance()->adminEmailFromName);
			  $mail->AddAddress(trim($email));
			  $mail->SetFrom(self::getInstance()->adminEmailFrom, self::getInstance()->adminEmailFromName);
			  $mail->Subject = $subject;
			  $mail->AltBody = strip_tags($body);
			  $mail->MsgHTML($body);
			  //$mail->AddAttachment('images/phpmailer.gif');      // attachment
			  //$mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
			  $mail->Send();
			  //echo "Message Sent OK</p>\n";
			} catch (Exception $e) {
			  error_log("QMail::sendToAdmin: " . $e->getMessage());
			}
		}
    }

    /**************************************************************************/

    public static function sendToUser($email, $mail_shortname, $replacements) {
        $search_array   =   self::getInstance()->prepareMetaTags($replacements);
        $messageNode    =   QNode::getInstance('/_site_settings/mailtexts/'.$mail_shortname);
        $subject        =   preg_replace(array_keys($search_array),array_values($search_array),$messageNode->tfields['subject']);
        $body           =   preg_replace(array_keys($search_array),array_values($search_array),$messageNode->tfields['body']);
       //dump($body);
       //dump($subject);
       //dump($email);
        /*$config             =   array();
        $config['charset']  = 'UTF-8';
        $config['mailtype'] = 'html';
        $config['wordwrap'] = FALSE;
        $emailObj=new CI_Email($config);
        $emailObj->from(self::getInstance()->adminEmailFrom, self::getInstance()->adminEmailFromName);
		//$emailObj->from(self::getInstance()->adminEmailFrom);
        $emailObj->to(trim($email));
        $emailObj->subject($subject);
        $emailObj->message($body);
        $emailObj->send();*/
		
		
		$mail = self::createMailer();
		//$mail->IsSendmail(); // telling the class to use SendMail transport
		try {
		  $mail->AddReplyTo(self::getInstance()->adminEmailFrom, self::getInstance()->adminEmailFromName);
		  $mail->AddAddress(trim($email));
		  $mail->SetFrom(self::getInstance()->adminEmailFrom, self::getInstance()->adminEmailFromName);
		  $mail->Subject = $subject;
		  $mail->AltBody = strip_tags($body);
		  $mail->MsgHTML($body);
		  //$mail->AddAttachment('images/phpmailer.gif');      // attachment
		  //$mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
		  $mail->Send();
		  //echo "Message Sent OK</p>\n";
		} catch (Exception $e) {
		  error_log("QMail::sendToUser: " . $e->getMessage());
		}

		
    }

    public static function deliverySend($from_name, $from_email, $to_name, $to_email, $subject, $body, $lang_id){
		/*$config             =   array();
        $config['charset']  = 'UTF-8';
        $config['mailtype'] = 'html';
        $config['wordwrap'] = FALSE;
        $emailObj=new CI_Email($config);
        $emailObj->from($from_email, $from_name);
		//$emailObj->from(self::getInstance()->adminEmailFrom);
        $emailObj->to($to_email);
        $emailObj->subject($subject);
        $emailObj->message($body);
        $emailObj->send();
		return;
	*/
                $mail = self::createMailer();
		//$mail->IsSendmail(); // telling the class to use SendMail transport
                $body=(str_replace(array('{#ucode#}','&amp;','{#lang_id#}'),array(md5($to_email),'&', $lang_id),$body));
                ///$body=htmlspecialchars($body);
                //edump($body,1);
		try {
		  $mail->AddReplyTo($from_email, $from_name);
		  $mail->AddAddress(trim($to_email));
		  $mail->SetFrom($from_email, $from_name);
		  $mail->Subject = stripslashes($subject);
		  $mail->AltBody = strip_tags(stripslashes($body));

		  $mail->MsgHTML(stripslashes($body));
		  $mail->Send();
                  //edump($body);
		} catch (Exception $e) {
		  error_log("QMail::deliverySend: " . $e->getMessage());
		}
    }

    /**************************************************************************/
    protected function prepareMetaTags($arr){
        $res_arr=array();
        foreach($arr as $k=>$v){
            $res_arr['/\{#'.mb_strtoupper($k).'#\}/i'] = $v;
        }
        return $res_arr;
    }
    /**************************************************************************/


}

    /**************************************************************************/

?>