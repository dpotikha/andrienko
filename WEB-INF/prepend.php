<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set("display_errors", 'Off');
ini_set("log_errors", 'On');
// log to <account home>/tmp/php-errors.log (outside the web root): the server's php-fpm log is not readable from the site account
$__errLogDir = dirname($_SERVER["DOCUMENT_ROOT"]) . "/tmp";
if (is_dir($__errLogDir) && is_writable($__errLogDir)) {
    $__errLog = $__errLogDir . "/php-errors.log";
    if (@filesize($__errLog) > 10 * 1024 * 1024) @rename($__errLog, $__errLog . ".1"); // keep at most ~20 MB
    ini_set("error_log", $__errLog);
}
unset($__errLogDir, $__errLog);
		
		setlocale(LC_ALL, 'ru_RU.UTF8');
        setlocale(LC_CTYPE, array('ru_RU.UTF8'));
        ini_set('php_value default_charset', 'utf-8');
		ini_set('mbstring.internal_encoding','UTF-8');
	function saveCustomerFile($file,$folder,$node,$field){
		if(strlen($file)){
			$arr=explode('/',$file);
			@copy($_SERVER['DOCUMENT_ROOT'].$file,$_SERVER['DOCUMENT_ROOT'].$folder.$arr[count($arr)-1]);
			$node->fields[$field]->value =str_replace('/files/','/',$folder.$arr[count($arr)-1]);
		}
	}	
	function session_save(){
		global $MDB;
		
		if(isset($_SESSION)&&count($_SESSION)){
			$_SESSION['skip']=true;
			$MDB->Query("UPDATE dbm_sessions set session_data='".db_escape(serialize($_SESSION))."',date='".date('Y-m-d h:i:s')."' WHERE session_id='".session_id()."'");
			@session_destroy();
		}
	}
	function session_create($session_id){
		global $MDB;
		$MDB->Query("INSERT INTO dbm_sessions (session_id,session_data,date) VALUES ('".$session_id."','','".date('Y-m-d h:i:s')."')");
	}
	function session_clear(){
		global $MDB;
		$MDB->Query("DELETE  FROM  dbm_sessions WHERE UNIX_TIMESTAMP(date)<= '".(time()-24*3600)."'");
	}
	function session_remove($session_id){
		global $MDB;
		$MDB->Query("DELETE FROM dbm_sessions WHERE session_id='".$session_id."'");
		@session_destroy();
	}
	function session_load($session_id){
		global $MDB;
		$MDB->Query("SELECT * FROM dbm_sessions WHERE session_id='".$session_id."'");
		if(!$MDB->NumRows()){
			return false;
		}
		$sess_info=$MDB->NextResult();
		session_id($session_id);
		$_SESSION=unserialize($sess_info['session_data']);

		return true;		
	}
function makeDate($day,$month,$year){
	if(strlen($day)==1)$day='0'.$day;
	$month=getMonthsGenitive($month-1);
	return $day.' '.$month.' '.$year;
	
}
function switchLocale($lang,$path){
		if(!strlen($path)||$path=="/")
		return "/".$lang."/";
		return str_replace("/".SITE_LOCALE."/","/".$lang."/",$path);
}
///////////////////////////////////////////////////
function makeLink($node,$params=array(),$lang=""){
	$param_str="";
	foreach($params as $k=>$v){
		$param_str.="&".$k."=".$v;
	}
	$SITE_LOCALE=(strlen($lang))?$lang:SITE_LOCALE;
	if(MOD_REWRITE&&count($params))$param_str="?".substr($param_str,1);
	if(is_object($node)){
		if(MOD_REWRITE)
			return (USE_LANG)?("/".$SITE_LOCALE.$node->getHref().$param_str):($node->getHref().$param_str);
		else
			return (USE_LANG)?("/index.php?lang=".$SITE_LOCALE."&node_id=".$node->id.$param_str):("/index.php?node_id=".$node->id.$param_str);
	}else{
		if(MOD_REWRITE)
				return (USE_LANG)?("/".$SITE_LOCALE.$node):$node;
		else{
				$n=Node::findByPath($node);
				if(!$n)return  makeLink("/_404",$params,$lang);
				return (USE_LANG)?("/index.php?lang=".$SITE_LOCALE."&node_id=".$n->id.$param_str):("/index.php?node_id=".$n->id.$param_str);
			}
			
	}
}
/////////////////////////////////////////////////////////
function rucfirst($str) {

	 $e="UTF-8";

	 $fc = mb_strtoupper(mb_substr($str, 0, 1, $e), $e);

     return $fc.mb_substr($str, 1, mb_strlen($str, $e), $e);

}

function getNewsDate($datetime){

	if(SITE_LOCALE=="en")return date("d F Y",$datetime);

	return date("d ",$datetime).getMonthsGenitive(date("m",$datetime)-1).date(" Y",$datetime);

}

function getPeriod($start,$end){

	return getNewsDate($start)." - ".getNewsDate($end);

//October 6th 2007 - December 30th 2007

}

function getLocale($node,$field)

{

	if($field=="name"&&!isset($node->tfields[$field."_".SITE_LOCALE]))return $node->name;
	if(in_array($field,array('full_text','text'))&&!strlen($node->tfields[$field."_".SITE_LOCALE])){
		$str='';
		switch(SITE_LOCALE){
			case 'en':
					if(strlen($node->tfields[$field."_ua"])&&strlen($node->tfields[$field."_ru"])){
						return 'This page is available only in <a href="/ru'.$node->getHref().'">Russian</a> and <a href="/ua'.$node->getHref().'">Ukrainian</a> version';
					}elseif(strlen($node->tfields[$field."_ru"])){
						return 'This page is available only in <a href="/ru'.$node->getHref().'">Russian</a> version';
					}elseif(strlen($node->tfields[$field."_ua"])){
						return 'This page is available only in <a href="/ua'.$node->getHref().'">Ukrainian</a> version';
					}
					break;
			case 'ru':
					if(strlen($node->tfields[$field."_ua"])&&strlen($node->tfields[$field."_en"])){
						return 'Эта страница доступна только в <a href="/en'.$node->getHref().'">английской</a> и <a href="/ua'.$node->getHref().'">украинской</a> версиях';
					}elseif(strlen($node->tfields[$field."_ua"])){
						return 'Эта страница доступна только в <a href="/ua'.$node->getHref().'">украинской</a> версии';
					}elseif(strlen($node->tfields[$field."_en"])){
						return 'Эта страница доступна только в <a href="/en'.$node->getHref().'">английской</a> версии';
					}
					break;
			case 'ua':
					if(strlen($node->tfields[$field."_en"])&&strlen($node->tfields[$field."_ru"])){
						return 'Ця сторінка доступна тільки в <a href="/ru'.$node->getHref().'">російській</a> та <a href="/en'.$node->getHref().'">англійській</a> версіях';
					}elseif(strlen($node->tfields[$field."_ru"])){
						return 'Ця сторінка доступна тільки в <a href="/ru'.$node->getHref().'">російській</a> версії';
					}elseif(strlen($node->tfields[$field."_en"])){
						return 'Ця сторінка доступна тільки в <a href="/en'.$node->getHref().'">англійській</a> версії';
					}
					break;
		
		}
	}
	return $node->tfields[$field."_".SITE_LOCALE];

}





function utf8_substr($str,$start,$end)

{

   preg_match_all("/./su", $str, $ar);

   if(!count($ar[0]))return substr($str,$start,$end);

   if(func_num_args() >= 3) {

       $end = func_get_arg(2);

       return join("",array_slice($ar[0],$start,$end));

   } else {

       return join("",array_slice($ar[0],$start));

   }

}

function str_transliterate_ru2en($str) {

	$ru_letters = array(

		"а"=>"a", "б"=>"b", "в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e", "ё"=>"yo", 

		"ж"=>"zh", "з"=>"z", "и"=>"i", "й"=>"y", "к"=>"k", "л"=>"l", "м"=>"m",

		"н"=>"n", "о"=>"o", "п"=>"p", "р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u",

		"ф"=>"f", "х"=>"kh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"sch", "ь"=>"",

		"ы"=>"y", "ъ"=>"y", "э"=>"e", "ю"=>"yu", "я"=>"ya",



		"А"=>"A", "Б"=>"B", "В"=>"V", "Г"=>"G", "Д"=>"D", "Е"=>"E", "Ё"=>"Yo", 

		"Ж"=>"Zh", "З"=>"Z", "И"=>"I", "Й"=>"Y", "К"=>"K", "Л"=>"L", "М"=>"M",

		"Н"=>"N", "О"=>"O", "П"=>"P", "Р"=>"R", "С"=>"S", "Т"=>"T", "У"=>"U",

		"Ф"=>"F", "Х"=>"Kh", "Ц"=>"Ts", "Ч"=>"Ch", "Ш"=>"Sh", "Щ"=>"Sch", "Ь"=>"",

		"Ы"=>"Y", "Ъ"=>"Y", "Э"=>"E", "Ю"=>"Yu", "Я"=>"Ya",

		);



	$array_keys = array_keys($ru_letters);

	$resStr = '';

	for($i=0; $i < strlen($str); $i++) {

		$l = utf8_substr($str,$i,1);

		if (in_array($l, $array_keys)) {

			$resStr .= $ru_letters[$l];

		} else {

			$resStr .= $l;

		}

	}



	return $resStr;

}

function remove_illegal_characters($shortname)

{

	$shortname = strtolower($shortname);

	$shortname = str_replace(" ", "_", $shortname);

	$shortname = str_replace("-", "_", $shortname);

	$new_shortname="";

	for($i=0; $i < strlen($shortname); $i++) {

		$l = substr($shortname,$i,1);

		if(preg_match("~[0-9a-z_\.]~",$l))

		$new_shortname.=$l;

	}

	return $new_shortname;

}

function name2shortname($name) {

	$name = trim($name);

	$shortname = str_transliterate_ru2en($name);

	return remove_illegal_characters($shortname);

}







//setlocale(LC_ALL, "ru_RU.CP1251");



ini_set('mbstring.internal_encoding','UTF-8');

//ini_set('mbstring.http_input','latin1');

//ini_set('mbstring.http_output','latin1');

function tget_class($class)

{

return strtolower(get_class($class));

}



//error_reporting(E_ERROR | E_WARNING | E_PARSE);

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED); // was E_ALL ^ E_NOTICE; deprecations from this 2008 code would flood the log

//ini_set("magic_quotes_gpc", "0");

//ini_set("register_globals", "0");

ini_set("memory_limit", "512M");

ini_set("max_execution_time", "3000");



if(strpos (PHP_OS, "WIN") !== false ) {

	DEFINE("SYSTEM_SEPARATOR", ";");

	DEFINE("DIR_SEPARATOR", "\\");

	DEFINE("FILE_SEPARATOR", "\\");

	DEFINE("NEWLINE", "\r\n");

} else {

	DEFINE("SYSTEM_SEPARATOR", ":");

	DEFINE("DIR_SEPARATOR", "/");

	DEFINE("FILE_SEPARATOR", "/");

	DEFINE("NEWLINE", "\n");

}



$DOC_ROOT = $GLOBALS["HTTP_SERVER_VARS"]["DOCUMENT_ROOT"];

$DOC_ROOT = $_SERVER["DOCUMENT_ROOT"];



ini_set("include_path", "$DOC_ROOT/WEB-INF/classes");

//echo ini_get("include_path");

DEFINE("FIELD_IMGLIB_DIR", $DOC_ROOT.DIR_SEPARATOR."imglib");

DEFINE("FIELD_SERVERFILES_DIR", $DOC_ROOT . DIR_SEPARATOR . "files");

DEFINE("FIELD_IMGLIB_THUMBNAILS_DIR", $DOC_ROOT.DIR_SEPARATOR."imglib_thumbnails");

DEFINE("FIELD_IMGLIB_ORIGINAL_DIR", $DOC_ROOT.DIR_SEPARATOR."imglib_original");

DEFINE("FILE_FIELD_DIR", $DOC_ROOT.DIR_SEPARATOR."_files");



function getmicrotime() {

	list($usec, $sec) = explode(" ",microtime());

	return ((float)$usec + (float)$sec);

}



function _log($str) {

	global $___logFile;

	//$___logFile = fopen($_SERVER["DOCUMENT_ROOT"]."/sql.log", "a+");

	//fwrite($___logFile, date("Y-m-d h:i:s") . "\t" . $str . "\r\n");

	//fclose($___logFile);

}

function _log2($str) {

	global $___logFile;

	//$___logFile = fopen($_SERVER["DOCUMENT_ROOT"]."/sql.log", "a+");

	//fwrite($___logFile, date("Y-m-d h:i:s") . "\t" . $str . "\r\n");

	//fclose($___logFile);

}



$____st = getmicrotime();

$____pt = getmicrotime();



_log("\r\n\r\nScript start - " . $_SERVER['REQUEST_URI']);



function _logTimeFromStart($key = "") {

	global $____st, $____pt;

	$ct = getmicrotime();

	_log("Time from : prev. pt. - " . sprintf("%01.5f", ($ct - $____pt)) .

	",  script start - " . sprintf("%01.5f", ($ct - $____st)) .

	(strlen($key) == 0 ? "" : ", " . $key));

	$____pt = getmicrotime();

}



function _dump ( $v ) {

    echo "<pre>";

    	var_dump($v);

    echo "</pre>";

}



function getClientIP() {

	if ($_SERVER['HTTP_CLIENT_IP']) {

		$ip = $_SERVER['HTTP_CLIENT_IP'];

	} elseif ($_SERVER['HTTP_X_FORWARDED_FOR']) {

		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

	} elseif ($_SERVER['HTTP_X_FORWARDED']) {

		$ip = $_SERVER['HTTP_X_FORWARDED'];

	} elseif ($_SERVER['HTTP_FORWARDED_FOR']) {

		$ip = $_SERVER['HTTP_FORWARDED_FOR'];

	} elseif ($_SERVER['HTTP_FORWARDED']) {

		$ip = $_SERVER['HTTP_FORWARDED'];

	} else {

		$ip = $_SERVER['REMOTE_ADDR'];

	}



	return $ip;

}



function import( $package_name ) {

	_logTimeFromStart("import start");

	$packs = explode(".", $package_name);

	$cn = array_pop($packs);

	$dir = implode("/", $packs);

	if($cn == "*") {

		$include_path = ini_get("include_path");

		$incs = explode(SYSTEM_SEPARATOR, $include_path);

		$find = false;

		foreach($incs as $inc) {

			if(@is_dir("$inc/$dir")) {

				$dir = "$inc/$dir";

				$find = true;

				break;

			}

		}

		if(!$find){

			die("FATAL ERROR in import(): can't find path '$dir' in any include dirs('$include_path')");

		}

		$files = array();

		$d = dir($dir);

		while (false !== ($entry = $d->read())) {

		    if(substr($entry, -3) == "php") {

		    	$files[] = $dir."/".$entry;

		    }

		}

		$d->close();

	} else {

		$files = array($dir."/".$cn.".php");

	}



	foreach($files as $file) {

		$d = trim("$file");

		if(!@is_dir($d)) {

			_logTimeFromStart("start require $d");

			include_once($d);

			_logTimeFromStart("end require $d");

		}

	}

	_logTimeFromStart("import end");

}



function removeWWW($url) {

	if (strpos($url, "www.") === 0) {

		return substr($url, strlen("www."));

	}

	if (strpos($url, "www2.") === 0) {

		return substr($url, strlen("www2."));

	}

	return $url;

}



function _stripslashes($str) {

	$str = str_replace("\\\\", "\\", $str);

	$str = str_replace("\\\"", "\"", $str);

	$str = str_replace("\\'", "'", $str);

	return $str;

}



function quoteString($str, $maxlen) {

		$str = str_replace("\n", " ", $str);

		$str = str_replace("\r", " ", $str);

		$str = str_replace("\t", " ", $str);



		$_words = explode(" ", $str);

		$res = "";

		$c = 0;

		for($i=0; $i< sizeof($_words); $i++) {

			$w = $_words[$i];

			if (strlen($w) == 0) {

				continue;

			}

			if (strlen($res) + strlen($w) > $maxlen) {

				break;

			}

			if ($c != 0) {

				$res .= " ";

			}

			$res .= $w;

			$c++;

		}

		return $res;

}



/**

 *  convert special chars to XML style

 */

function prepareStringForXML($str) {

	$str = (string)$str;

    $out = "";

	for($i=0; $i < strlen($str); $i++) {

		$c = $str[$i];

		switch($c) {

			case '<':

				$out .= "&lt;";

				break;

			case '>':

				$out .= "&gt;";

				break;

			case '&':

				$out .= "&amp;";

				break;

			case '"':

				$out .= "&#034;";

				break;

            case '\'':

				$out .= "&#039;";

				break;

			default:

				$out .= $c;

		}

	}

	return $out;

}



/**

 *  convert special chars to HTML style

 */

function prepareStringForHTML($str) {

    $out = "";

	for($i=0; $i < strlen($str); $i++) {

		$c = $str[$i];

		switch($c) {

			case '<':

				$out .= "&lt;";

				break;

			case '>':

				$out .= "&gt;";

				break;

			case '&':

				$out .= "&amp;";

				break;

			case '"':

				$out .= "&quot;";

				break;

            case '\'':

				$out .= "&#039;";

				break;

			default:

				$out .= $c;

		}

	}

	return $out;

}



function encodeURL($uri) {

	$cb = "";

	for ($i = 0; $i < strlen($uri); $i++) {

		$ch = $uri[$i];

		switch ($ch) {

			case '?' :

			case '&' :

			case '=' :

			case ' ' :

			case '\'':

			case '"' :

			case '%' :

			case '<' :

			case '>' :

			case '+' :

			case '^' :

			case '~' :

			case '|' :

				$cb .= '%';

				$cb.= encodeHex(ord($ch) >> 4);

				$cb .= encodeHex(ord($ch));

				break;



			default :

				$cb .= $ch;

				break;

		}

	}



	return $cb;

}



function externalURL($url)  {

	if (strpos($url, "http://") === 0 || strpos($url, "https://") === 0) {

		return $url;

	} else {

		return "http://".$url;

	}

}



function externalURLNoHTTP($url)  {

	if (strpos($url, "http://") === 0) {

		return substr($url, strlen("http://"));

	} else if (strpos($url, "https://") === 0) {

		return substr($url, strlen("https://"));

	} else {

		return $url;

	}

}



function strReplaceButNotInQuotes($patt, $replacement, $s, $QUOTE_CHARACTER = "'") {

	$charbuffer = "";

	for ($j = 0; $j < strlen($s); $j++) {

		$c = $s[$j];

		if ($c == $QUOTE_CHARACTER) {

			$charbuffer .= $QUOTE_CHARACTER;

			for ($j++; $j < strlen($s) && $s[$j] != "'"; $j++)

				$charbuffer .= $s[$j];

			$charbuffer .= $QUOTE_CHARACTER;

		} else {

			$k = $j;

			for (; $k < strlen($s) && $k - $j < strlen($patt) && $s[$k] == $patt[$k - $j]; $k++) {}

			if (($k - $j) == strlen($patt)) {

				$j = $k - 1;

				$charbuffer .= $replacement;

			} else {

				$charbuffer .= $c;

			}

		}

	}

	return $charbuffer;

}







function byteToHex($b) {

	$cb = "";

	$cb .= encodeHex(($b & 0xf0) >> 4);

	$cb .= encodeHex($b & 0xf);

	return $cb;

}



function encodeHex($ch) {

	$ch &= 0xf;

	if ($ch < 10)

		return strtoupper(chr($ch + 48));

	else

		return strtoupper(chr(($ch + 97) - 10));

}



function _array_remove_filter($var) {

	return ($var != null);

}

function array_remove($array, $key) {

	$array[$key] = null;

	$array = array_filter($array, "_array_remove_filter");

	return $array;

}



function getFileExt($filename) {

	$temp = explode(".", $filename);

	return $temp[sizeof($temp)-1];

}



function tinclude($t) {

	global $request, $session;

	$dynamic_template_path = $_SERVER['DOCUMENT_ROOT'] . "/dynamic_templates/" . $t . ".php";

	if (!file_exists($dynamic_template_path)) {

		die('Dynamic template file does not exists: ' . $t);

	}

	foreach ($request->attributes as $k=>$v) {

		$$k = $v;

	}

	include($dynamic_template_path);

}



define('META_MAX_WORDS', 20);



function _getMetaKeywords($str) {

	$str = strip_tags($str);

	$str = str_replace("\n", " ", $str);

	$str = str_replace("\r", " ", $str);



	$str = str_replace(".", " ", $str);

	$str = str_replace(",", " ", $str);

	$str = str_replace('"', " ", $str);



	$_keywords = explode(" ", $str);

	$keywords = array();

	for($i=0; $i< sizeof($_keywords) && $i<META_MAX_WORDS; $i++) {

		$k = $_keywords[$i];

		if (strlen($k) < 4) continue;

		$keywords []= $k;

	}

	return $keywords;

}



function prepareMetaDescription($str) {

	$keywords = _getMetaKeywords($str);

	return implode(" ", $keywords);

}



function prepareMetaKeywords($str) {

	$keywords = _getMetaKeywords($str);

	return implode(",", $keywords);

}



_logTimeFromStart("point1");

/**************************************************************************************************/



DEFINE("_CACHE_DIR_", $_SERVER["DOCUMENT_ROOT"] . "/cache");



function _ftok($path, $proj) {

	return strlen($path)+0xff3;

}



function _write_cache($varname) {

//*

	//if ($GLOBALS[$varname]) {

		$cacheFile = fopen(_CACHE_DIR_ . "/" . $varname, "w+");

		if ($cacheFile === false) {
			error_log("_write_cache: cannot write " . _CACHE_DIR_ . "/" . $varname);
			return;
		}

		fwrite($cacheFile,  serialize($GLOBALS[$varname]));

		fclose($cacheFile);

	//}

//*/

/*



	if ($GLOBALS[$varname]) {

		$cache_key = _ftok ($varname, "dbm");



		$serializedValue = serialize($GLOBALS[$varname]);



		$shm_id = @shmop_open($cache_key, "a", 0, 0);

		if ($shm_id) {

			shmop_delete($shm_id);

			shmop_close($shm_id);

		}

		$shm_id = shmop_open($cache_key, "c", 0644, strlen($serializedValue));

		if ($shm_id) {

			shmop_write($shm_id, $serializedValue, 0);

			shmop_close($shm_id);

		}

	}

//*/

}



function _read_cache($varname) {

//*

	$cacheFileStr = @file_get_contents (_CACHE_DIR_ . "/" . $varname);



	if ($cacheFileStr) {

		$GLOBALS[$varname] = unserialize($cacheFileStr);

	} else {

		$GLOBALS[$varname] = null;

	}

//*/

/*

		//$cache_key = ftok ($_SERVER["SERVER_NAME"] . "/" . "NID");

		$cache_key = _ftok ($varname, "dbm");

		$shm_id = @shmop_open($cache_key, "a", 0, 0);

		if ($shm_id) {

			$cacheFileStr = shmop_read($shm_id, 0, shmop_size($shm_id));

			shmop_close($shm_id);

		}

		//_dump($cacheFileStr);

//*/

}

require_once ("dbm/DBUtils.php");





function ___shutdown_function() {

	//global $___logFile;

//	return false;

	//fclose($___logFile);

DBUtils::rollback(true);



	_write_cache("_NODE_CLASS_CACHE_BY_ID");

	_write_cache("_NODE_CLASS_CACHE_BY_SHORTNAME");

	_write_cache("_NODES_CACHE_BY_ID");

	_write_cache("_NODES_CACHE_BY_PATH");

	_write_cache("USERS_CACHE");

	_write_cache("USER_GROUPS_CACHE");

}



register_shutdown_function("___shutdown_function");

/**************************************************************************************************/



function list_dir_contents ($path) {

	$files = array();

	if ($dir = @opendir($path)) {

		while (($file = readdir($dir)) !== false) {

			if ($file=='.' || $file=='..') {

				continue;

			}

			$file = $path.'/'.$file;

			$files[] = $file;

		}  

		closedir($dir);

	}

	return $files;

}



function list_dir_contents_only_dirs ($path) {

	$files = array();

	if ($dir = @opendir($path)) {

		while (($file = readdir($dir)) !== false) {

			if ($file=='.' || $file=='..') {

				continue;

			}

			$file = $path.'/'.$file;

			if (is_dir($file)) {

				$files[] = $file;

			}

		}  

		closedir($dir);

	}

	return $files;

}



function list_dir_contents_only_files ($path) {

	$files = array();

	if ($dir = @opendir($path)) {

		while (($file = readdir($dir)) !== false) {

			if ($file=='.' || $file=='..') {

				continue;

			}

			$file = $path.'/'.$file;

			if (!is_dir($file)) {

				$files[] = $file;

			}

		}  

		closedir($dir);

	}

	return $files;

}



//simple object

class PHPObject {

    function __construct() {

    }

}



class Date {

	var $time = 0;



	function __construct($o = null) {

		if (is_string($o)) {

			if (preg_match("~([0-9]{1,2})\\.([0-9]{1,2})\\.([0-9]{4})~", $o)) {

				list($d,$m,$y) = explode(".", $o);

				$this->time = mktime(0, 0, 0, (int)$m, (int)$d, (int)$y); // (int): PHP 5 read "2026 12:00" as 2026, PHP 8 throws

			} else {

				$this->time = strtotime($o);

			}

		} elseif (is_integer($o)) {

			$this->time = $o;

		} else {

			$this->time = time();

		}

	}



	function format($format = "Y-m-d") {

		return date($format, $this->time);

	}



	function getDay1() {

		return $this->format("j");

	}



	function getDay() {

		return $this->format("d");

	}



	function getMonth() {

		return $this->format("m");

	}



	function getYear() {

		return $this->format("Y");

	}



	function getHour() {

		return $this->format("H");

	}



	function getMinute() {

		return $this->format("i");

	}

	function getMonthName($prefix = "") {
		return $GLOBALS["months_names".$prefix][$this->format("n")];
	}

	function getSecond() {

		return $this->format("s");

	}

}



/**************************************************************************************************/



function getTopSection($node) {

	$path = $node->absolutePath;

	$k = strpos($path, '/', 1);

	if ($k===false)

		$path = substr($path, 0);

	else

		$path = substr($path, 0, strpos($path, "/", 2));

   	$res = Node::findByPath($path);

   	return $res;

}



function getTopSectionS($path) {

	$k = strpos($path, '/', 1);

	if ($k===false)

		$path = substr($path, 0);

	else

		$path = substr($path, 0, strpos($path, "/", 2));

   	$res = Node::findByPath($path);

   	return $res;

}



function getL2pSection($node) {

	$items = explode("/", $node->absolutePath);

	if (sizeof($items > 2)) {

		$res = Node::findByPath('/'.$items[1].'/'.$items[2]);

		return $res;

	}



   	return null;

}



function getL3pSection($node) {

	$items = explode("/", $node->absolutePath);

	if (sizeof($items > 3)) {

		$res = Node::findByPath('/'.$items[1].'/'.$items[2].'/'.$items[3]);

		return $res;

	}



   	return null;

}



function getL4pSection($node) {

	$items = explode("/", $node->absolutePath);

	if (sizeof($items > 3)) {

		$res = Node::findByPath('/'.$items[1].'/'.$items[2].'/'.$items[3].'/'.$items[4]);

		return $res;

	}



   	return null;

}



function useEmailTemplate($template, $patterns, $values) {

	$text = $template;

	for ($i=0; $i<sizeof($patterns); $i++) {

		$text = str_replace($patterns[$i], $values[$i], $text);

	}

	return $text;

}



function formatCurrency($number) {

	return sprintf("%01.2f", $number);

}

/**************************************************************************************************/

									_logTimeFromStart("point2");

require_once ("dbm/User.php");

									_logTimeFromStart("point2.1");

require_once ("dbm/UserGroup.php");

									_logTimeFromStart("point2.2");

require_once ("dbm/NodeClass.php");

									_logTimeFromStart("point2.3");

require_once ("base/HTTP.php");

require_once ("base/Mail.php");
require_once ("base/Email.class.php");
									_logTimeFromStart("point2.5");

require_once ("base/Validator.php");

									_logTimeFromStart("point2.6");

require_once ("base/HTMLWriter.php");

									_logTimeFromStart("point2.7");

require_once ("sql/mysql/Connection.php");

									_logTimeFromStart("point2.8");

									_logTimeFromStart("point2.9");

require_once($DOC_ROOT . "/_settings.php");

									_logTimeFromStart("point2.10");

require_once ("dbm/messages_".LOCALE.".properties");

									_logTimeFromStart("point2.11");

require_once ("dbm/ModeratedAction.php");

									_logTimeFromStart("point3");

require_once ("my_func.php");



$connection = new Connection($connect_params["url"], $connect_params["params"]);

require_once ("sql/mysql/mysql.class.php");



//////////////////////////////////////////////////////////////////////////////////////////////////



//if ($_SERVER["SERVER_NAME"] == "shina") {
//set character_set7client = utf8

	DBUtils::execUpdate("set character_set_client = utf8");
	DBUtils::execUpdate("SET CHARACTER SET '".DB_CHARSET."'");

	DBUtils::execUpdate("SET NAMES '".DB_CHARSET."'");

//}



$_USE_NODES_CACHE_ = true;



NodeClass::buildNodeClassCache();

UserGroup::buildUserGroupCache();

User::buildUsersCache();

Node::readCache();

function humanLikeUrl($url) {
	if (substr($url, strlen($url)-1, strlen($url)) == "/") {
		$url = substr($url, 0, strlen($url)-1);
	}
	if (strpos($url, "http://") === 0) {
		return substr($url,7);
	} elseif (strpos($url, "https://") === 0) {
		return substr($url,8);
	}
}

_logTimeFromStart("point4");


/*if(isset($_GET["PHPSESSID"])){
	if(!session_load($_GET["PHPSESSID"]))
		session_create($_GET["PHPSESSID"]);
}elseif(!session_load(session_id())){
		session_create(session_id());
}
register_shutdown_function('session_save');
session_clear();*/
?>