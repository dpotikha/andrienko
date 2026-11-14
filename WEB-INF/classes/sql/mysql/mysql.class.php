<?php 
//Author: Grigoriy Chorniy
class QueryBuilder
{
	var $pattern = array();
	var $replacement = array();
	var $query="";
	function QueryBuilder($query)
	{
		$this->query=$query;
	}
	function setParameters($p=array())
	{
		foreach($p as $k=>$v){
			$this->pattern[$k]="/\?/";
			$this->replacement[$k]=mysql_escape_string($v);
		}
		$this->query=preg_replace($this->pattern, $this->replacement, $this->query,1);
		return $this->query;
	}
}
class ModernDataBase
{
    
    var $query="";
	var $connection_id;
    var $database_connect;
	var $database_name="";
	var $query_time_start;
	var $query_time_end;
	var $SQL;
	var $debug=false;
	function ModernDataBase($connection)
	{
		$this->connection_id=$connection->getConnectionId();
	}
	function getConnectionId()
	{
		return  $this->connection_id;
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function DefTables($SQL)
	{
		$this->SQL=$SQL;	
	}
////////////////////////////////////////////////////////////////////////////////////////////	

	function ShowError($sql)
	{
			if($this->debug&&!$this->QR())
			{
				echo "<br><font color='#ff0000'><b>QUERY ERROR # ".mysql_errno()." <br> ";	
				echo mysql_error()." <br> ";	
				echo $sql."</b></font><br>";
			}elseif($this->debug)
			{
				echo $sql." <b><font color='#00bb00'>".$this->GetQTime()." sec</font></b><br>";
			}
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function QIns($table_name,$insert_array)
	{
				$fields_names=array();	
				$fields_values=array();	
				foreach($insert_array as $k=>$v)
				{
					$fields_names[]=$k;	
					$fields_values[]="'".$this->PTDB($v)."'";	
				}
				$sql="INSERT INTO ".$table_name." ( ".implode(",",$fields_names).") VALUES (".implode(",",$fields_values).")";
				$this->Query($sql);
				$this->ShowError($sql);
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function QUpdate($table_name,$update_array,$where="")
	{
	
				$fields_update=array();	
				foreach($update_array as $k=>$v)
				{
					$fields_update[]=$k.'='."'".$this->PTDB($v)."'";	
				}
				$where_str=($where=="")?"":" WHERE ".$where;
				$sql="UPDATE  ".$table_name."  SET ".implode(",",$fields_update)."".$where_str;//Tut Bag
				$this->Query($sql);
				$this->ShowError($sql);
	
	
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function QDel($table_name,$where="")
	{
				$where_str=($where=="")?"":" WHERE ".$where;
				$sql="DELETE FROM ".$table_name.$where_str;
				$this->Query($sql);
				$this->ShowError($sql);
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function ConnectionResult()
	{
		if($this->connection_id&&$this->database_connect)return true; else return false;
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function CreateDB($database_name)
	{
		$sql="CREATE DATABASE ".$database_name;
		$this->Query($sql);
	}
/////////////////////////////////////////////////////////////////////////////////////////////
	function DeleteTable($table_name)
	{
		$this->Query("DROP TABLE IF EXISTS `".$table_name."`");
		return $this->QR();
	}
	
////////////////////////////////////////////////////////////////////////////////////////////	
	function CreateTable($table_name,$charset="utf8",$collate="utf8_unicode_ci")
	{
		$this->DeleteTable($table_name);
		$this->Query("CREATE TABLE `".$table_name."` (
		  	  `id` int(11) NOT NULL auto_increment,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `id` (`id`)
			) TYPE=MyISAM DEFAULT CHARSET=".$charset." COLLATE=".$collate."");
		return $this->QR();
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function AddPrimaryIndex($table,$fields=array(),$replace=false){
			if($replace){
				$this->Query("ALTER TABLE `".$table."` DROP PRIMARY KEY ,ADD PRIMARY KEY ( ".implode(",",$fields).")");	
			}else{
				$this->Query("ALTER TABLE `".$table."` ADD PRIMARY KEY ( ".implode(",",$fields).")");	
			}
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function AddFieldToTable($table_name,
							$field_name,
							$type = "VARCHAR",
							$length = "",
							$attributes = "",
							$is_null = "NOT NULL",
							$default = "",
							$after = ""
							)
	{
		$sql="ALTER TABLE `".$table_name."` ADD `".$field_name."` ".$type;
		if($length!="")$sql.="( ".$length." )";
		if($attributes!="")$sql.=" ".$attributes." ";
		$sql.=" ".$is_null." ";
		if($default!="")$sql.=" DEFAULT '".$default."' ";
		if($after!="")$sql.=" AFTER `".$after."` ";
		echo $sql;
		$this->Query($sql);	
		return $this->QR();
		
	}
////////////////////////////////////////////////////////////////////////////////////////////	
//ALTER TABLE `xxx` ADD `asdasd` VARCHAR( 234 ) NOT NULL FIRST ;
//ALTER TABLE `xxx` ADD `aaa` VARCHAR( 232 ) NOT NULL ;
//ALTER TABLE `xxx` ADD `sddsd` VARCHAR( 23 ) NOT NULL AFTER `id` ;
//ALTER TABLE `xxx` ADD `sdzfasd` INT( 23 ) UNSIGNED ZEROFILL DEFAULT '23';
////////////////////////////////////////////////////////////////////////////////////////////	
	function DropFieldFromTable($table_name,$field_name)
	{
		$sql="ALTER TABLE `".$table_name."` DROP `".$field_name."`";
		$this->Query($sql);	
		return $this->QR();
		
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function DropDB($database_name)
	{
		$sql="DROP DATABASE  ".$database_name;
		$this->Query($sql);
	}	
////////////////////////////////////////////////////////////////////////////////////////////	
	function DataSeek($i)
	{
		return @mysql_data_seek($this->query,$i);
	}

////////////////////////////////////////////////////////////////////////////////////////////	
	
	function GetConnectionEncoding()
	{
		$charset = mysql_client_encoding($this->connection_id);
		return $charset;
	}
////////////////////////////////////////////////////////////////////////////////////////////	

	function ChangeUser()
	{
		return @mysql_change_user($user, $password ,$this->database_name, $this->connection_id);
	}
////////////////////////////////////////////////////////////////////////////////////////////
	function parseQuery($query){
		$dbmQLBaseRegex ="/SELECT(\s+DISTINCT(.*)|\s+(.*))FROM\s+(.*)(\s+WHERE\s+(.*))*(\s+GROUP BY\s+(.*))*(\s+HAVING\s+(.*))*(\s+ORDER BY\s+(.*))*(\s+LIMIT\s+(.*),(.*))*$/iU";
		preg_match($dbmQLBaseRegex, $query, $matches);
		return $matches;		
	}	
////////////////////////////////////////////////////////////////////////////////////////////	
	function execute($query,$parameters=array())
	{
		if(count($parameters)){
			$pattern=array();
			$replacement=array();
			foreach($parameters as $k=>$v){
				$pattern[$k]="/\?/";
				$replacement[$k]=mysql_escape_string($v);
			}
			$query=preg_replace($pattern, $replacement, $query,1);
		}
		$query=preg_replace("/\{(.*)\}/iU", "dbm_nfv_$1", $query);
		$query=preg_replace("/\[(.*)\]/iU", "dbm_$1", $query);
//		echo $query;
		$this->query = mysql_query($query,$this->connection_id);
	}
	/////////////////////////////////////////////////////////////////////////////////////////
	function Query($query,$parameters=array())
	{
		if(count($parameters)){
			$pattern=array();
			$replacement=array();
			foreach($parameters as $k=>$v){
				$pattern[$k]="/\?/";
				$replacement[$k]=mysql_escape_string($v);
			}
			$query=preg_replace($pattern, $replacement, $query,1);
		}
//		echo $query;
		$this->query_time_start=$this->getmicrotime();
		$this->query = mysql_query($query,$this->connection_id);
		$this->query_time_end=$this->getmicrotime();
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function QR()
	{
		return $this->query;
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function QUR()
	{
		return mysql_affected_rows($this->connection_id);
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function NumRows()
	{
		return mysql_num_rows($this->query);
	}
////////////////////////////////////////////////////////////////////////////////////////////	
	function LastId()
	{
		return mysql_insert_id($this->connection_id);
	}
 //////////////////////////////////////////////Query//////////////////////////////////////////// 
	function NextResult($index=false)
	{
		return mysql_fetch_array($this->query,MYSQL_ASSOC);
	}
	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function getRecords(){
		$arr=array();
		while($res=$this->NextResult()){
			$arr[]=$res;
		}
		return $arr;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function my_addslashes($string)
{
    $string = mysql_escape_string($string);
	return $string;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function my_stripslashes($string)
{
    $string = stripslashes($string);
	return $string;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function PTDB($value)
{
		return $this->my_addslashes(trim($value));
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function GetQTime()
{
	return ($this->query_time_end-$this->query_time_start);
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getmicrotime() 
{ 
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
} 	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}

////////////////////////////////////////////////////////////////////////////////////////////	
$MDB=new ModernDataBase($connection);
$request->setAttribute("MDB",$MDB);
?>
