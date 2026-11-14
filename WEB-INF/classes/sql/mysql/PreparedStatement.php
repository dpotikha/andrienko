<?php

require_once ("sql/mysql/Connection.php");
require_once ("sql/mysql/ResultSet.php");

DEFINE("SQL_DATE_FORMAT", "Y-m-d H:i:s");
DEFINE("SQL_DATEONLY_FORMAT", "Y-m-d");

class PreparedStatement {
	var $connection = null;

	var $numValues = 0;
	var $parts = array();
	var $values = array();

	var $resultSet = null;
	var $updateCount = -1;

	function __construct($connection, $sql) {
		$this->connection = $connection;
		if ($sql != null) {
			$this->_construct($sql);
		}
	}

	function _construct($sql) {
		$currentPart = "";
		$strLen = strlen($sql);
		for ($j = 0; $j < $strLen; $j++) {
			$c = $sql{$j};
			if ($c == '?') {
				$this->parts[] = $currentPart;
				$currentPart = "";
			} else if ($c == "'") {
				$currentPart .= "'";
				for ($j++; $j < $strLen && $sql{$j} != "'"; $j++) {
					$currentPart .= $sql{$j};
				}

				$currentPart .= "'";
			} else {
				$currentPart .= $sql{$j};
			}
		}
		$this->parts[] = $currentPart;
		$this->numValues = sizeof($this->parts) - 1;
	}

	function createStatement($connection, $sql) {
		$this->connection = $connection;

		$this->parts[] = $sql;
		$this->numValues = 1;
	}

	function checkIndex($i) {
		if ($i < 1 || $i > $this->numValues) {
			die("PreparedStatement::checkIndex() invalid index - $i.");
		}
	}

	function setObject($i, $obj) {
		$this->checkIndex($i);
		if ($obj === null) {
			$this->setNull($i);
		} elseif (is_bool($obj)) {
			$this->setBoolean($i, $obj);
		} elseif (is_int($obj)) {
			$this->setInt($i, $obj);
		} elseif (is_float($obj)) {
			$this->setFloat($i, $obj);
		} elseif (is_string($obj)) {
			$this->setString($i, $obj);
		} elseif (is_object($obj) && tget_class($obj) == 'date') {
			$this->setDate($i, $obj);
		} else {
			$this->setString($i, (string)$obj);
		}
	}

	function setNull($i) {
		$this->checkIndex($i);
		$this->values[$i] = "null";
	}

	function setBoolean($i, $boolean) {
		$this->checkIndex($i);
		$boolean = (boolean)$boolean;
		$this->values[$i] = ($boolean ? "1" : "0");
	}

	function setInt($i, $int) {
		$this->checkIndex($i);
		$int = (int)$int;
		$this->values[$i] = "".$int;
	}

	function setFloat($i, $float) {
		$this->checkIndex($i);
		$float = (float)$float;
		$this->values[$i] = "".$float;
	}

	function setString($i, $s) {
	
		$this->checkIndex($i);
		$sql = "";
		if ($s === null) {
			$this->setNull($i);
		} else {
			$sql .= "'";
			$j = strlen($s);
			for ($k = 0; $k < $j; $k++) {
				$c = $s{$k};
				if ($c == "'")
					$sql .= "\\'";
				else if ($c == '"')
					$sql .= "\\\"";
				else if (ord($c) == 0)
					$sql .= "\\0";
				else if ($c == "\\") {
					$sql .= "\\\\";
				} else
					$sql .= $c;
			}

			$sql .= "'";
			$this->values[$i] = $sql;
		}
		 $___logFile = @fopen($_SERVER["DOCUMENT_ROOT"]."/log/string_".date("Y-m-d").".log", "a+");
			if($___logFile){
				fwrite($___logFile, date("Y-m-d h:i:s") . "\t\r\n" . $s . "\r\n\r\n");
				fwrite($___logFile, $sql . "\r\n\r\n\r\n");
				fclose($___logFile);
		}
	}

	function setDate($i, $date) {
		$this->checkIndex($i);
		if ($date === null) {
			$this->setNull($i);
		} else {
			if (!is_object($date) || tget_class($date) != 'date' ) {
				die("Invalid argument for setDate");
			}
			$this->values[$i] = "'".$date->format(SQL_DATE_FORMAT)."'";
		}
	}

	function execute($sql = null) {
		$startTime = getmicrotime();
		if ($sql != null) {
			$this->_construct($sql);
		}
		if (sizeof($this->values) != $this->numValues) {
			die("PreparedStatement:execute()  not all ? params was set.");
		}
		$sql = "";
		for ($i = 0; $i < $this->numValues; $i++) {
			$sql .= $this->parts[$i].$this->values[$i+1];
		}
		$sql .= $this->parts[$i];

		if(substr_count($sql,'INSERT INTO')){
			$___logFile = @fopen($_SERVER["DOCUMENT_ROOT"]."/log/sql_".date("Y-m-d_H").".log", "a+");
			if($___logFile){
				fwrite($___logFile, date("Y-m-d h:i:s") . "\t\r\n" . $sql . "\r\n\r\n\r\n");
				fclose($___logFile);
			}
			$___logFile = @fopen($_SERVER["DOCUMENT_ROOT"]."/log/sql_".date("Y-m-d").".log", "a+");
			if($___logFile){
				fwrite($___logFile, date("Y-m-d h:i:s") . "\t\r\n" . $sql . "\r\n\r\n\r\n");
				fclose($___logFile);
			}
			$___logFile = @fopen($_SERVER["DOCUMENT_ROOT"]."/log/post_".date("Y-m-d_H").".log", "a+");
			if($___logFile){
				ob_start();
				print_r($_POST);
				$__post=ob_get_contents();		
				ob_end_clean();
				fwrite($___logFile, date("Y-m-d h:i:s") . "\t\r\n" . $__post . "\r\n\r\n\r\n");
				fclose($___logFile);
			}
			$___logFile = @fopen($_SERVER["DOCUMENT_ROOT"]."/log/request_".date("Y-m-d_H").".log", "a+");
			if($___logFile){
				ob_start();
				global $request;
				print_r($request);
				$__request=ob_get_contents();		
				ob_end_clean();
				fwrite($___logFile, date("Y-m-d h:i:s") . "\t\r\n" . $__request . "\r\n\r\n\r\n");
				fclose($___logFile);
			}
		}
		if(substr_count($sql,'UPDATE')){
			$___logFile = @fopen($_SERVER["DOCUMENT_ROOT"]."/log/sql_update_".date("Y-m-d_H").".log", "a+");
			if($___logFile){
				fwrite($___logFile, date("Y-m-d h:i:s") . "\t\r\n" . $sql . "\r\n\r\n\r\n");
				fclose($___logFile);
			}
			$___logFile = @fopen($_SERVER["DOCUMENT_ROOT"]."/log/sql_update_".date("Y-m-d").".log", "a+");
			if($___logFile){
				fwrite($___logFile, date("Y-m-d h:i:s") . "\t\r\n" . $sql . "\r\n\r\n\r\n");
				fclose($___logFile);
			}
			$___logFile = @fopen($_SERVER["DOCUMENT_ROOT"]."/log/post_".date("Y-m-d_H").".log", "a+");
			if($___logFile){
				ob_start();
				print_r($_POST);
				$__post=ob_get_contents();		
				ob_end_clean();
				fwrite($___logFile, date("Y-m-d h:i:s") . "\t\r\n" . $__post . "\r\n\r\n\r\n");
				fclose($___logFile);
			}
			
			$___logFile = @fopen($_SERVER["DOCUMENT_ROOT"]."/log/request_".date("Y-m-d_H").".log", "a+");
			if($___logFile){
				ob_start();
				global $request;
				$NodeName = $request->getParameter("NodeName");
				echo 'rNodeName'."\t".$NodeName."\t\r\n";
				echo 'r1NodeName'."\t".((string)$NodeName)."\t\r\n";
				echo 'pNodeName'."\t".$_POST['NodeName']."\t\r\n";
				print_r($request);
				$__request=ob_get_contents();		
				ob_end_clean();
				fwrite($___logFile, date("Y-m-d h:i:s") . "\t\r\n" . $__request . "\r\n\r\n\r\n");
				fclose($___logFile);
			}
		}

		$res = mysql_query($sql, $this->connection->dblink) or die($sql."<br>".mysql_error());
		if ($res == false) {
			$this->resultSet = null;
			$updateCount = -1;
			return false;
		}

		if (is_resource($res)) {
			$this->resultSet = new ResultSet($res);
			$updateCount = -1;
			$result = false;
		} else {
			$this->updateCount = mysql_affected_rows ($this->connection->dblink);
			$this->resultSet = null;
			$result = true;
		}

		_log((getmicrotime() - $startTime) . "\t" . $sql);

		return $result;
	}

	function executeQuery($sql = null) {
		if ($sql != null) {
			$this->_construct($sql);
		}
		$this->execute();
		return $this->resultSet;
	}

	function executeUpdate($sql = null) {
		if ($sql != null) {
			$this->_construct($sql);
		}
		$this->execute();
		return $this->updateCount;
	}

	function close() {
		if ($this->resultSet != null) {
			$this->resultSet->close();
			$this->resultSet = null;
		}
	}
}
?>