<?php 

require_once ("sql/mysql/ResultSet.php");
require_once ("sql/mysql/PreparedStatement.php");

/**
 * Connection with Database
 * @package sql
 */
class Connection {
	var $dblink = null;
	function getConnectionId(){
		return $this->dblink;
	}
	function __construct ($url, $properties=null) {
		$i = mb_strpos($url, "://");
		if ($i === false)
			die("mysql Connection()   Bad url.");
		$i += 3;
		$j = mb_strpos($url, '/', $i);
		if ($j === false)
			die("mysql Connection()   Bad url.");
		$port = 3306;
		$l = mb_strpos($url, ':', $i);
		$host = "";
		if ($l !== false && $l < $j) {
			$host =  mb_substr($url, $i, $l-$i);
			$port = (int)mb_substr($url,$l + 1, $j-$l-1);
		} else {
			$host = mb_substr($url, $i, $j-$i);
		}

		$paramsIndex = mb_strpos($url, '?', $j);
		$dbname = "";
		if ($paramsIndex !== false) {
			$dbname = mb_substr($url, $j + 1, $paramsIndex - $j - 1);
			$params = mb_substr($url, $paramsIndex+1);

	        $fromIndex = 0;
	        for (;$fromIndex < mb_strlen($params);) {
	            $indx = mb_strpos($params, '&', $fromIndex);
            	$eindx = mb_strpos($params, '=', $fromIndex);
	            if ($indx === false) {
	            	$properties[mb_substr($params, $fromIndex, $eindx - $fromIndex)] = mb_substr($params, $eindx+1);
	                break;
	            }
                $properties[mb_substr($params, $fromIndex, $eindx-$fromIndex)] = mb_substr($params, $eindx+1, $indx-$eindx-1);
	            $fromIndex = $indx+1;
	        }
		} else {
			$dbname = mb_substr($url, $j + 1);
		}

		$user = null;
		$password = null;
		if ($properties != null) {
			$user = $properties["user"];
			$password = $properties["password"];
		}

		// keep the legacy behaviour: failed queries return false instead of throwing (PHP 8.1+ default)
		mysqli_report(MYSQLI_REPORT_OFF);
		$this->dblink = @mysqli_connect($host, $user, $password, $dbname, $port);
		if (!$this->dblink) {
			error_log("mysql Connection(): " . mysqli_connect_error());
			die("Can't connect to database.");
		}
		mysqli_set_charset($this->dblink, defined('DB_CHARSET') ? DB_CHARSET : 'utf8');
		// same as the live server today (empty sql_mode), independent of the server default
		mysqli_query($this->dblink, "SET SESSION sql_mode = ''");
	}

	function prepareStatement($sql) {
		$pstmt = new PreparedStatement($this, $sql);
		return $pstmt;
	}

	function createStatement() {
		$pstmt = new PreparedStatement($this, null);
		return $pstmt;
	}

    function close() {
        mysqli_close($this->dblink);
    }
}

/**
 * Escape a string for use inside a quoted SQL literal (replacement for mysql_escape_string).
 */
function db_escape($s) {
	global $connection;
	return mysqli_real_escape_string($connection->dblink, (string)$s);
}

?>