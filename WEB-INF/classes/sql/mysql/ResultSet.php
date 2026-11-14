<?php

require_once ("sql/mysql/Connection.php");
require_once ("sql/mysql/PreparedStatement.php");

/**
 * Database query result Set
 * @package sql
 */
class ResultSet {
    var $result_id = null;
    var $rowCount = -1;

    var $columnIndexes = array();
    var $columnNames = array();
    var $columnTypeNames = array();
    var $columnCount = -1;

    var $currentRowIndex = -1;
    var $currentRow = array();

    function ResultSet ($res_id) {
        $this->result_id = $res_id;
        $this->rowCount = mysql_num_rows($this->result_id);
        $this->columnCount = mysql_num_fields($this->result_id);
        for ($i = 0; $i < $this->columnCount; $i++) {
        	$this->columnTypeNames[$i] = mysql_field_type($this->result_id, $i);
        	$this->columnNames[$i] = mysql_field_name($this->result_id, $i);
        	$this->columnIndexes[$this->columnNames[$i]] = $i;
        }
    }

	function close() {
		mysql_free_result($this->result_id);
	}

	function absolute($i) {
		$i = (int)$i;
		if ($i < 0 || $i >= $this->rowCount) {
			die("ResultSet::absolute()   Invalid index - $i");
		}
		$this->currentRowIndex = $i;
	}
	function beforeFirst() {
		$this->currentRowIndex = -1;
	}
	function afterLast() {
		$this->currentRowIndex = $this->rowCount;
	}
	function first() {
		$this->beforeFirst();
		return $this->next();
	}
	function last() {
		$this->afterLast();
		return $this->previous();
	}
	function next() {
		$this->currentRowIndex++;
		if ($this->currentRowIndex < $this->rowCount) {
			$this->fetchRow();
			return true;
		} else {
			return false;
		}
	}
	function previous() {
		$this->currentRowIndex--;
		if ($this->currentRowIndex >= 0) {
			$this->fetchRow();
			return true;
		} else {
			return false;
		}
	}

	function fetchRow() {
		mysql_data_seek ($this->result_id, $this->currentRowIndex);
		$this->currentRow = mysql_fetch_row($this->result_id);
	}

	function checkColumnIndex(&$i) {
		if (!is_int($i)) {
			$i = $this->columnIndexes[(string)$i];
		} else {
			$i--;
		}
		if ($i < 0 || $i >= $this->columnCount) {
			die("ResultSet::checkColumnIndex() invalid index '$i'.");
		}
	}

	function getObject($i) {
		$this->checkColumnIndex($i);
		switch ($this->columnTypeNames[$i]) {
			case "int" :
				return $this->getInt($i+1);
			case "real" :
				return $this->getFloat($i+1);
			case "string" :
			case "blob" :
				return $this->getString($i+1);
			case "datetime" :
			case "date" :
				return $this->getDate($i+1);
			default:
				//_dump($i);
				//echo "!!!";
				//_dump($this->columnTypeNames[$i]);
				//echo "!!!";
				return $this->currentRow[$i];
		}
	}

	function getInt($i) {
		$this->checkColumnIndex($i);
		return (int)$this->currentRow[$i];
	}

	function getFloat($i) {
		$this->checkColumnIndex($i);
		return (float)$this->currentRow[$i];
	}

	function getBoolean($i) {
		$this->checkColumnIndex($i);
		return ((int)$this->currentRow[$i] == 1);
	}

	function getString($i) {
		$this->checkColumnIndex($i);
		return (string)$this->currentRow[$i];
	}

	function getDate($i) {
		$this->checkColumnIndex($i);
		$date = new Date((string)$this->currentRow[$i]);
		return $date;
	}

	function getCachedResultSet() {
		$crs = new CachedResultSet($this);
		return $crs;
	}
}

class CachedResultSet extends ResultSet {
    var $rows = array();

    var $currentRow = array();

	function CachedResultSet($rs) {
		$this->rowCount = $rs->rowCount;
	    $this->columnIndexes = $rs->columnIndexes;
	    $this->columnNames = $rs->columnNames;
	    $this->columnTypeNames = $rs->columnTypeNames;
	    $this->columnCount = $rs->columnCount;
	    $this->currentRowIndex = -1;

	    $this->rows = array();

		$rs->beforeFirst();
		while ($rs->next()) {
			$this->rows[$rs->currentRowIndex] = $rs->currentRow;
		}
	}

	function fetchRow() {
		$this->currentRow = $this->rows[$this->currentRowIndex];
	}

	function close() { }
}

?>