<?php

function output_file($file,$name) 
 { 
 //do something on download abort/finish 
 //register_shutdown_function( 'function_name'  ); 
 if(!file_exists($file)) 
 die('file not exist!'); 
 $size = filesize($file); 
 $name = rawurldecode($name); 
  
 if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) 
 $UserBrowser = "Opera"; 
 elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) 
 $UserBrowser = "IE"; 
 else 
 $UserBrowser = ''; 
  
 /// important for download im most browser 
 $mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 
  'application/force-download' : 'application/octet-stream'; 
 @ob_end_clean(); /// decrease cpu usage extreme 
 header('Content-Type: ' . $mime_type); 
 header('Content-Disposition: attachment; filename="'.$name.'"'); 
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
 header('Accept-Ranges: bytes'); 
 header("Cache-control: private"); 
 header('Pragma: private'); 
  
 /////  multipart-download and resume-download 
 if(isset($_SERVER['HTTP_RANGE'])) 
 { 
 list($a, $range) = explode("=",$_SERVER['HTTP_RANGE']); 
 str_replace($range, "-", $range); 
 $size2 = $size-1; 
 $new_length = $size-$range; 
 header("HTTP/1.1 206 Partial Content"); 
 header("Content-Length: $new_length"); 
 header("Content-Range: bytes $range$size2/$size"); 
 } 
 else 
 { 
 $size2=$size-1; 
 header("Content-Length: ".$size); 
 } 
 $chunksize = 1*(1024*1024); 
 $bytes_send = 0; 
 if ($file = fopen($file, 'r')) 
 { 
 if(isset($_SERVER['HTTP_RANGE'])) 
 fseek($file, $range); 
 while(!feof($file) and (connection_status()==0)) 
 { 
 $buffer = fread($file, $chunksize); 
 print($buffer);//echo($buffer); // is also possible 
 flush(); 
 $bytes_send += strlen($buffer); 
 //sleep(1);//// decrease download speed 
 } 
 fclose($file); 
 } 
 else 
 die('error can not open file'); 
 if(isset($new_length)) 
 $size = $new_length; 
 die(); 
 } 
///////////////////////////////////////////////////////////////////////////
function clearTmpDir($tmpDir, $time_limit) {
	$subdirs = list_dir_contents_only_dirs($tmpDir);
	print_r($subdirs);
	foreach($subdirs as $dir) {
		$files = list_dir_contents_only_files($dir);
		foreach ($files as $file) {
			$update_time = filemtime ($file);
			echo $update_time."<br>";
			if ($update_time < $time_limit) {
				//echo $file.", ".date("d.m.Y h:i",$update_time)."<br>";
				if (@unlink ($file)) {
					//echo "deleted $file<br>";
				}
			}
		}

		$update_time = filemtime ($dir);
		if ($update_time < $time_limit) {
			//echo $dir.",        ".date("d.m.Y h:i",$update_time)."<br>";
			if (@rmdir ($dir)) {
				//echo "deleted $dir<br>";
			}
		}
	}
}


///////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
function uniqNumber()
{
 return md5(uniqid(rand(), true));
}
function hexbin($temp) {
	   $len = strlen($temp);
	   for ($i=0;$i<$len;$i+=2) $data.=chr(hexdec(substr($temp,$i,2)));
	   return $data;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////


function getHash(){
	$hash="";
	$alphabet = "abcdefghijklmnopqrstuvwxyz0123456789";
	for($i=0;$i<=20;$i++)
	{
	  $r=rand(0,35);
	  $hash.=substr($alphabet,$r,1);
	}
	return $hash;
}

function tformatDate($date){
			$arr=explode(".",$date);
			//<!--Пятница, 27 апреля
			return UppernameRusDayFromEngDay(date("l",mktime(0,0,0,$arr[1],$arr[0],$arr[2]))).", ".$arr[0]." ".getLowerMonthsGenitive($arr[1]-1);
		}
function getMonthsGenitive($id)
{
	$months_genitive=array();
	$months_genitive["ru"] = array(
	"января",
	"февраля",
	"марта",
	"апреля",
	"мая",
	"июня",
	"июля",
	"августа",
	"сентября",
	"октября",
	"ноября",
	"декабря"
);
	$months_genitive["ua"] = array(
	"січня",
	"лютого",
	"березня",
	"квітня",
	"травня",
	"червня",
	"липня",
	"серпня",
	"вересня",
	"жовтня",
	"листопада",
	"грудня"
);
$months_genitive["en"] = array(
	"january",
	"february",
	"march",
	"april",
	"may",
	"june",
	"july",
	"august",
	"september",
	"october",
	"november",
	"december"
);
	return $months_genitive[SITE_LOCALE][$id];
}
//////////////////////////////////////////////////////////////////////////////////////////////////
function getLowerMonthsGenitive($id)
{
	$months_genitive = array(
	"января",
	"февраля",
	"марта",
	"апреля",
	"мая",
	"июня",
	"июля",
	"августа",
	"сентября",
	"октября",
	"ноября",
	"декабря"
);
	return $months_genitive[$id];
}
//////////////////////////////////////////////////////////////////////////////////////////////////
function nameRusDayFromEngDay($eng_day)
{
	switch($eng_day){
		case "Sunday": return "воскресенье";
		case "Monday": return "понедельник";
		case "Tuesday": return "вторник";
		case "Wednesday": return "среда";
		case "Thursday": return "четверг";
		case "Friday": return "пятница";
		case "Saturday": return "суббота";
	}
	return "";
}
function UppernameRusDayFromEngDay($eng_day)
{
	switch($eng_day){
		case "Sunday": return "Воскресенье";
		case "Monday": return "Понедельник";
		case "Tuesday": return "Вторник";
		case "Wednesday": return "Среда";
		case "Thursday": return "Четверг";
		case "Friday": return "Пятница";
		case "Saturday": return "Суббота";
	}
	return "";
}
//////////////////////////////////////////////////////////////////////////////////////////////////
function GetNavigatorPath($CurrentNode,$max=-1,$is_include=1)
{
		$parent=$CurrentNode->getParentList();
		$parent=array_reverse($parent);
		for($i=0;$i<$max;$i++)
		{
			unset($parent[$i]);
		}
		foreach($parent as $k=>$v)
		{
			
			$navigatorPath='<a href="'.$v->absolutePath.'">'.$v->name.'</a>&nbsp;&nbsp;/&nbsp;&nbsp;'.$navigatorPath;
		}
		if($is_include==1)
		$navigatorPath.='<strong>'.$CurrentNode->name.'</strong>';
		return $navigatorPath;
}
//////////////////////////////////////////////////////////////////////////////////////////////////
function getLevelByPath($path)
{
	$level=count(explode("/",$path));
	return $level;
}
//////////////////////////////////////////////////////////////////////////////////////////////////

?>