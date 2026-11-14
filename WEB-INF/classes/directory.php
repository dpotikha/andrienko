<?php
	class directoryClass{
		function __construct()
		{
		
		}	
		
		
	function chmod($dirname,$mod=0777)
	{
	  if (!file_exists($dirname)) return false;
	  if (is_file($dirname))
	  {
		@chmod($dirname, $mod);
	  }
	  $dir = dir($dirname);
	  while (false !== $entry = $dir->read())
	  {
		if ($entry == '.' || $entry == '..') continue;
		if (is_dir($dirname."/".$entry))
		{
		  @chmod($dirname."/".$entry, $mod);
		  directoryClass::chmod($dirname."/".$entry,$mod);
		}
		else
		{
		  @chmod($dirname."/".$entry, $mod);
		}
	  }
	  $dir->close();
	  @chmod($dirname, $mod);
	}	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	function rmdir_only_last_files($dirname,$time_limit)
	{
	  if (!file_exists($dirname)) return false;
	  if (is_file($dirname))
	  {
		$update_time = filemtime ($dirname);
	  	if ($update_time < $time_limit) {
			@chmod($dirname, 0777);
			return @unlink($dirname);
		}
	  }
	  $dir = dir($dirname);
	  while (false !== $entry = $dir->read())
	  {
		if ($entry == '.' || $entry == '..') continue;
		$update_time = filemtime ($dirname);
			if (is_dir($dirname."/".$entry))
			{
			  @chmod($dirname."/".$entry, 0777);
			  directoryClass::rmdir_only_last_files($dirname."/".$entry,$time_limit);
			}
			else
			{
				if($update_time<$time_limit){
					  @chmod($dirname."/".$entry, 0777);
					  @unlink($dirname."/".$entry);
				  }
			}
	  }
	  $dir->close();
	  $update_time = filemtime ($dirname."/".$entry);
	  if($update_time<$time_limit){
		  if (is_dir($dirname."/".$entry))
		  {
			  @chmod($dirname, 0777);
			  return @rmdir($dirname);
		  }
	  }
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function rmdir($dirname)
	{
	  if (!file_exists($dirname)) return false;
	  if (is_file($dirname))
	  {
		@chmod($dirname, 0777);
		return @unlink($dirname);
	  }
	  $dir = dir($dirname);
	  while (false !== $entry = $dir->read())
	  {
		if ($entry == '.' || $entry == '..') continue;
		if (is_dir($dirname."/".$entry))
		{
		  @chmod($dirname."/".$entry, 0777);
		  directoryClass::rmdir($dirname."/".$entry);
		}
		else
		{
		  @chmod($dirname."/".$entry, 0777);
		  @unlink($dirname."/".$entry);
		}
	  }
	  $dir->close();
	  @chmod($dirname, 0777);
	  return @rmdir($dirname);
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////
		function createDirIfNotExists($dir,$file_roots=array()) {
			if(!count($file_roots)){
				$file_roots=array($_SERVER['DOCUMENT_ROOT']);
			}
			$subdirs = explode('/', $dir);
			$tmp_dir = "";
			foreach($subdirs as $k=>$subdir) {
				$tmp_dir .= '/' . $subdir;
				foreach($file_roots as $k1=>$v1){
					if(!file_exists($v1 . $tmp_dir)){
						@mkdir($v1 . $tmp_dir, 0777);
					}
				}
			}
		}
	}
?>