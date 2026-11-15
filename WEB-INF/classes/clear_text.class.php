<?php

class clearText
{
	/**********************************************************************************************************************************************/
	
	private $text='';
	private $search=array();
	private $replacement=array();
	
	/**********************************************************************************************************************************************/
	
	public	function __construct($text)
	{
		$this->text=stripcslashes($text);	
	}
	
	/**********************************************************************************************************************************************/
	
	private function code2utf($num)
	{
		if ($num < 128) return chr($num);
		if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
		if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
		if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
		return '';
	}
	
	/**********************************************************************************************************************************************/
	
	private function convertHtmlCodes()
	{
		$this->search[]			=	'~&#0*(\d+);~e';
		$this->replacement[]	=	'$this->code2utf($1)';
		$this->text				=	htmlspecialchars_decode($this->text);
	}
	
	/**********************************************************************************************************************************************/
	
	private function removeNotHtmlObjects()
	{
		$this->search[]			=	'@<script[^>]*?>.*?</script>@si';
		$this->replacement[]	=	'';
		$this->search[]			=	'@<style[^>]*?>.*?</style>@siU';
		$this->replacement[]	=	'';
		$this->search[]			=	'@<![\s\S]*?--[ \t\n\r]*>@';
		$this->replacement[]	=	'';
		$this->search[]			=	'@<object[^>]*?>.*?</object>@si';
		$this->replacement[]	=	'';
	}
	
	/**********************************************************************************************************************************************/
	
	private function converExternalLinks()
	{
		$this->text	=	str_replace(array("\\r","\\n","\\n\\r"),array("\r","\n<BR>","\n\r"),$this->text);
        $in			=	array(
        	'/<a([^>]+)href="([a-z]+):\/\/(?!'.str_replace('.','\.',$_SERVER['HTTP_HOST']).')([^>]*)"([^>]*)>([^>]*)<\/a>/is'
        );
        $out		=	array(
		  	'<noindex><a $1 href="$2://$3" $4 rel="nofollow" target="_blank">$5</a></noindex> ',
        );
        return preg_replace($in,$out,$this->text);
	}
	
	/**********************************************************************************************************************************************/
	
	public function prepare($allowed_tags, $allowed_attributes='',$aAllowedProperties='')
	{
		//$this->search[]			=	'/<(.*?)>/ie';
		//$this->replacement[]	=	"'<' . preg_replace(array('/\s+/',\"/'/\",'/\s*\=\s*/i','/([a-z_-]+)=[^\'\"].*\s/iU','/\s[a-z_-]\s/iU','/(?<!$allowed_attributes)(\=\".*\")/iU','/(\s[a-z_-]+=\"\")/iU'), array(' ','\"','=','','','=\"\"',''), stripcslashes('$1')) . '>'";
		$allowed_attributes		=	str_replace(',','|',$allowed_attributes);
		$this->text 			= 	strip_tags($this->text, $allowed_tags);
		$this->text 			=	preg_replace($this->search, $this->replacement, $this->text);
		$this->text 			=	preg_replace('/<(.*?)>/ie', "'<' . preg_replace(array('/\s+/',\"/'/\",'/\s*\=\s*/i','/([a-z_-]+)=[^\'\"].*\s/iU','/\s[a-z_-]\s/iU','/(?<!$allowed_attributes)(\=\".*\")/iU','/(\s[a-z_-]+=\"\")/iU'), array(' ','\"','=','','','=\"\"',''), stripcslashes('$1')) . '>'", $this->text);
  		$this->text 			=	preg_replace('/([^;"]+)?(?<!'. $aAllowedProperties .'):(?!\/\/(.+?)\/)((.*?)[^;"]+)(;)?/is', '', $this->text);
		$this->text 			= 	strip_tags($this->text, $allowed_tags);
		$this->text 			= 	$this->converExternalLinks();
		$this->text 			= 	strip_tags($this->text, $allowed_tags);
		return $this->text;
	}
	
	/**********************************************************************************************************************************************/
	
	public function text()
	{
		return $this->text;		
	}
	
	/**********************************************************************************************************************************************/
}

?>