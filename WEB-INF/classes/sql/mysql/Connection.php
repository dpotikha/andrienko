<?

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
	function Connection ($url, $properties=null) {
		$i = strpos($url, "://");
		if ($i === false)
			die("mysql Connection()   Bad url.");
		$i += 3;
		$j = strpos($url, '/', $i);
		if ($j === false)
			die("mysql Connection()   Bad url.");
		$port = 3306;
		$l = strpos($url, ':', $i);
		$host = "";
		if ($l !== false && $l < $j) {
			$host =  substr($url, $i, $l-$i);
			$port = (int)substr($url,$l + 1, $j-$l-1);
		} else {
			$host = substr($url, $i, $j-$i);
		}

		$paramsIndex = strpos($url, '?', $j);
		$dbname = "";
		if ($paramsIndex !== false) {
			$dbname = substr($url, $j + 1, $paramsIndex - $j - 1);
			$params = substr($url, $paramsIndex+1);

	        $fromIndex = 0;
	        for (;$fromIndex < strlen($params);) {
	            $indx = strpos($params, '&', $fromIndex);
            	$eindx = strpos($params, '=', $fromIndex);
	            if ($indx === false) {
	            	$properties[substr($params, $fromIndex, $eindx - $fromIndex)] = substr($params, $eindx+1);
	                break;
	            }
                $properties[substr($params, $fromIndex, $eindx-$fromIndex)] = substr($params, $eindx+1, $indx-$eindx-1);
	            $fromIndex = $indx+1;
	        }
		} else {
			$dbname = substr($url, $j + 1);
		}

		$user = null;
		$password = null;
		if ($properties != null) {
			$user = $properties["user"];
			$password = $properties["password"];
		}

		$this->dblink = mysql_connect("$host:$port", $user, $password) or die(mysql_error()."Can't connect to mysql server.");
		mysql_select_db($dbname, $this->dblink) or die("Can't select db.");
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
        mysql_close($this->dblink);
    }
}

?>