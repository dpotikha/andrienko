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

    function __construct ($res_id) {
        $this->result_id = $res_id;
        $this->rowCount = mysqli_num_rows($this->result_id);
        $this->columnCount = mysqli_num_fields($this->result_id);
        for ($i = 0; $i < $this->columnCount; $i++) {
        	$field = mysqli_fetch_field_direct($this->result_id, $i);
        	$this->columnTypeNames[$i] = ResultSet::typeName($field->type);
        	$this->columnNames[$i] = $field->name;
        	$this->columnIndexes[$this->columnNames[$i]] = $i;
        }
    }

    /**
     * Map a mysqli type code to the name the old mysql_field_type() returned.
     */
    static function typeName($type) {
        switch ($type) {
            case MYSQLI_TYPE_TINY: case MYSQLI_TYPE_SHORT: case MYSQLI_TYPE_LONG:
            case MYSQLI_TYPE_LONGLONG: case MYSQLI_TYPE_INT24:
                return "int";
            case MYSQLI_TYPE_FLOAT: case MYSQLI_TYPE_DOUBLE:
            case MYSQLI_TYPE_DECIMAL: case MYSQLI_TYPE_NEWDECIMAL:
                return "real";
            case MYSQLI_TYPE_STRING: case MYSQLI_TYPE_VAR_STRING:
                return "string";
            case MYSQLI_TYPE_TINY_BLOB: case MYSQLI_TYPE_MEDIUM_BLOB:
            case MYSQLI_TYPE_LONG_BLOB: case MYSQLI_TYPE_BLOB:
                return "blob";
            case MYSQLI_TYPE_DATE: case MYSQLI_TYPE_NEWDATE:
                return "date";
            case MYSQLI_TYPE_DATETIME:  return "datetime";
            case MYSQLI_TYPE_TIMESTAMP: return "timestamp";
            case MYSQLI_TYPE_TIME:      return "time";
            case MYSQLI_TYPE_YEAR:      return "year";
            case MYSQLI_TYPE_ENUM:      return "enum";
            case MYSQLI_TYPE_SET:       return "set";
            case MYSQLI_TYPE_GEOMETRY:  return "geometry";
            case MYSQLI_TYPE_NULL:      return "null";
            default:                    return "unknown";
        }
    }

	function close() {
		mysqli_free_result($this->result_id);
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
		mysqli_data_seek ($this->result_id, $this->currentRowIndex);
		$this->currentRow = mysqli_fetch_row($this->result_id);
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

	function __construct($rs) {
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