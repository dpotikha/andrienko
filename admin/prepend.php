<?php
require_once($_SERVER["DOCUMENT_ROOT"]. "/WEB-INF/prepend.php");
require_once($_SERVER["DOCUMENT_ROOT"]. "/WEB-INF/_imgtpmclr.php");
define('SESSION_LOGGED_USER_ATTR', 'CurrentAdminUser');


define('DELIVERY_NODE_ID', 7201);
$_settingsNode = Node::findByPath("/_site_settings");
define("ADMIN_EMAIL_FROM", $_settingsNode->tfields["admin_email_from"]);

function send_Emails($post){
/*	switch($post["lang"]){
		case 0:$langs=array("en","ua","ru");break;
		case 1:$langs=array("en","ru");break;
		case 2:$langs=array("en","ua");break;
		case 3:$langs=array("ru","ua");break;
		case 4:$langs=array("en");break;
		case 5:$langs=array("ru");break;
		case 6:$langs=array("ua");break;
		default:$langs=array("en","ua","ru");break;
	}
	
	$from=ADMIN_EMAIL_FROM;
	foreach($langs as $k=>$v){
		$subj=$post["name_".$v];
		$body=$post["text_".$v];
		if($post["toall"]==0){
			$snode=Node::findByPath("/subscribers");
			$subscribers=$snode->getChildren();
			foreach($subscribers as $k1=>$v1){
				if($v1->tfields["active"]["value"]==0||strtolower($v1->tfields["lang"]["name"])!=$v)continue;
				s_email($from,$v1->tfields["email"],$subj,$body);
			}
		}else{
			foreach($post["subscribers"] as $k1=>$v1){
				$s=Node::findById($v1);
				if($s->tfields["active"]["value"]==0||strtolower($s->tfields["lang"]["name"])!=$v)continue;
				s_email($from,$s->tfields["email"],$subj,$body);
			}
		}
	}*/
}
function s_email($from,$to,$subj,$body){
	 sendMail($from,$to,$subj,$body);
}

function htmlrparamOut($pname) {
	global $request;
	return prepareStringForXML($request->getParameter($pname));
}

function usetemplate($template) {
	global $request, $CurrentAdminUser, $AdminTrnsl;
	foreach($request->attributes as $key=>$value) {
		$$key = $value;
	}
	include ($_SERVER["DOCUMENT_ROOT"]. "/admin/templates/" . $template . ".html");
}

function localmsg($key) {
	return $GLOBALS["AdminTrnsl"][$key];
}

$loginPage = "loginform";
$loggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
$GLOBALS["CurrentAdminUser"] = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

if ($loggedUser == null) {
	$login = $request->getParameter("_login");
	$password = $request->getParameter("_password");
//	print_r($_POST);
//	print_r($request);
//	exit();
	if ($login != null) {
		$loggedUser = User::findByLogin($login);
		if ($loggedUser == null) {
			$request->setAttribute("error_message", "login is not valid");
			usetemplate($loginPage);
			die();
		} else if ($loggedUser->password != $password) {
			$request->setAttribute("error_message", "password does not match");
			usetemplate($loginPage);
			die();
		} else {
			
			//Node::buildFullTextIndex();
			
						$session->setAttribute(SESSION_LOGGED_USER_ATTR, $loggedUser);
			Header("Location: /admin");
			die();
		}
	} else {
		$request->setAttribute("error_message", null);
		usetemplate($loginPage);
		die();
	}
}

$fileExtensionIconMap = array(
	"doc"=>"icon_word.gif"
	,"rtf"=>"icon_word.gif"
	,"txt"=>"icon_word.gif"
	,"jpg"=>"icon_img.gif"
	,"gif"=>"icon_img.gif"
	,"bmp"=>"icon_img.gif"
	,"png"=>"icon_img.gif"
	,"avi"=>"icon_media.gif"
	,"mpg"=>"icon_media.gif"
	,"wav"=>"icon_media.gif"
	,"mp3"=>"icon_mp3.gif"
	,"pdf"=>"icon_pdf.gif"
	,"ppt"=>"icon_ppt.gif"
	,"xls"=>"icon_xls.gif"
	,"mov"=>"icon_mov.gif"
	,"zip"=>"icon_zip.gif"
);
function getIconForFile($fileName) {
	global $fileExtensionIconMap;
	$fileExtension = getFileExt($fileName);
	$icon = $fileExtensionIconMap[strtolower($fileExtension)];

	if ($icon == null) {
		$icon = "icons_rest.gif";
	}
	return $icon;
}

?>