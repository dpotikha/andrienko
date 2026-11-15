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
	usetemplate("mlangs/index",true);
}
function save_lang() {
	$locale=CLocale::get($_POST['lang_id']);

	foreach($_POST['lang'] as $section_shortname=>$v) {
		$locale[$section_shortname]['locale'] = array();
		foreach($v['shortname'] as $k1=>$v1) {
			$shortname=$v1;
			$value=$v['name'][$k1];
			if(trim($shortname)!='')
				$locale[$section_shortname]['locale'][$shortname]=$value;
		}
	}
	CLocale::save($_POST['lang_id'],$locale);
	echo 'success';
}
function synchronize(&$request) {
	$locale=array();
	global $SETUP_LANGUAGES;
	foreach($SETUP_LANGUAGES as $lang)
	$locale[$lang]=CLocale::get($lang);

	foreach($locale as $k=>$v) {
		foreach($locale as $k1=>$v1) {
			if($k1!=$k) {
				foreach($v1 as $section_shortname=>$section_data){
					if(!isset($v[$section_shortname])){
						$locale[$k][$section_shortname]=$v1[$section_shortname];
					}
					foreach($locale[$k1][$section_shortname]['locale'] as $k2=>$v2){
						if(!isset($locale[$k][$section_shortname]['locale'][$k2]))
						{
							$locale[$k][$section_shortname]['locale'][$k2]=$v2;
						}
					}
				}
			}
		}
		CLocale::save($k,$locale[$k]);
	}
}
function recursiveArrayMerge($array1, $array2) {
		reset ($array2);
		while(key($array2)!==null) {
			if (is_array(current($array2)) && isset($array1[key($array2)])) {
				$array1[key($array2)] = $this->recursiveArrayMerge($array1[key($array2)],current($array2));
			} else {
				$array1[key($array2)] = current($array2);
			}
			next($array2);
		}
		return $array1;
	}
function get_content(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$locale=CLocale::get($_GET['lang']);
	$request->setAttribute('locale',$locale);
	$request->setAttribute('lang',$_GET['lang']);
	$request->setAttribute('is_admin',$currentLoggedUser->group->id==1);

	usetemplate("mlangs/list",true);
}

function edit_section(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	if(!($currentLoggedUser->group->id==1))return;

	$locale=CLocale::get($_GET['lang']);
	$request->setAttribute('locale',$locale);
	$request->setAttribute('lang',$_GET['lang']);
	$request->setAttribute('section',$_GET['section']);
	$request->setAttribute('is_admin',$currentLoggedUser->group->id==1);
	$request->setAttribute('section_info',$locale[$_GET['section']]);
	usetemplate("mlangs/edit_section",true);

}
function delete_section() {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	if(!($currentLoggedUser->group->id==1))return;

	$locale=CLocale::get($_POST['lang']);
	unset($locale[$_POST['section_shortname']]);
	$t=-1;
	foreach($locale as $k=>$v) {
		$locale[$k]['sort_index'] = ++$t;
	}
	CLocale::save($_POST['lang'],$locale);
	echo 'success';
}
function save_sortable() {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	if(!($currentLoggedUser->group->id==1))return;

	$sort_items=explode(',',$_POST['sort_items']);
	save_lang_sortable($_POST['lang'], $sort_items);

	echo 'success';

}
function save_lang_sortable($lang,$sort_items) {

	$locale=CLocale::get($lang);

	$nlocale=array();
	foreach($sort_items as $k=>$v) {
		$nlocale[$v]=$locale[$v];
		$nlocale[$v]['sort_index']=$k;
	}
	CLocale::save($lang,$nlocale);

}
function add_section(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	if(!($currentLoggedUser->group->id==1))return;

	$request->setAttribute('lang',$_GET['lang']);
	usetemplate("mlangs/add_section",true);

}
function save_section(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	if(!($currentLoggedUser->group->id==1))return;

	$locale=CLocale::get($_POST['lang']);
	if(isset($locale[$_POST['section']]))
		$locale[$_POST['section']]['title']=$_POST['section_name'];

	CLocale::save($_POST['lang'],$locale);
	echo 'success';
}
class CLocale {
	public static function save($lang,$locale) {
		foreach($locale as $k=>$v){
			ksort($locale[$k]['locale']);
		}
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/s/langs/'.$lang.'.php', '<?php return '.var_export($locale, true).';?>');
	}
	public static function get($lang) {
		return require($_SERVER['DOCUMENT_ROOT'].'/s/langs/'.$lang.'.php');
	}
	public static function parse_old_lang($lang,$request) {
		
		include($_SERVER['DOCUMENT_ROOT'].'/WEB-INF/lang_'.$lang.'.php');
		$nLocale=array();
		foreach($LOCALE as $k=>$v){
			$exp=explode('.',$k);
			$section_shortname=array_shift($exp);
			if(!isset($nLocale[$section_shortname])){
				$nLocale[$section_shortname]=array('sort_index'=>count($nLocale),'title'=>$section_shortname,'locale'=>array());
			}
			if(!is_array($v)){
				$nLocale[$section_shortname]['locale'][implode('.',$exp)]=$v;
			}else{
				$nLocale[$section_shortname]['locale'][implode('.',$exp)]='[#'.implode('#],[#',$v).'#]';
			}
		}
		CLocale::save($lang,$nLocale);
	}
}

function create_section(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	if(!($currentLoggedUser->group->id==1))return;
	$locale=CLocale::get($_POST['lang']);
	$index=count($locale);
	$locale[$_POST['section_shortname']]=array('sort_index'=>$index,'title'=>$_POST['section_name'],'locale'=>array());
	CLocale::save($_POST['lang'],$locale);
	echo 'success';
}
function write_ini_file($path, $assoc_array) {

	foreach($assoc_array as $key => $item) {
		if(is_array($item)) {
			$content .= "\r\n[{$key}]\r\n";
			foreach ($item as $key2 => $item2) {
				if(is_numeric($item2) || is_bool($item2))
					$content .= "{$key2} = {$item2}\r\n";
				else {
					$content .= "{$key2} = \"{$item2}\"\r\n";
				}
			}
		} else {
			if(is_numeric($item) || is_bool($item))
				$content .= "{$key} = {$item}\r\n";
			else {
				$content .= "{$key} = \"{$item}\r\"\n";
			}
		}
	}

	if(!$handle = fopen($path, 'w')) {
		return false;
	}

	if(!fwrite($handle, $content)) {
		return false;
	}

	fclose($handle);
	return true;

}




function parse_ini_file_quotes_safe($f) {
	return parse_ini_file($f,true);
	$r=$null;
	$sec=$null;
	$f=@file($f);
	for ($i=0;$i<@count($f);$i++) {
		$newsec=0;
		$w=@trim($f[$i]);
		$w=str_replace('\r\n','ччч',$w);
		if ($w) {
			if ((!$r) or ($sec)) {
				if ((@substr($w,0,1)=="[") && (@substr($w,-1,1))=="]") {$sec=@substr($w,1,@strlen($w)-2);$newsec=1;$r[$sec]=array();}
			}
			if (!$newsec) {
				$w=@explode("=",$w);$k=@trim($w[0]);unset($w[0]); $v=@trim(@implode("=",$w));
				if ((@substr($v,0,1)=="\"") && (@substr($v,-1,1)=="\"")) {$v=@substr($v,1,@strlen($v)-2);}
				if ($sec) {$r[$sec][$k]=$v;} else {$r[$k]=$v;}
			}
		}
	}
	return $r;
}
?>