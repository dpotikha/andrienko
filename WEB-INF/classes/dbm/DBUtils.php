<?php

$_TRANSACTION_STATUS = array();

class DBUtils {
	static function getConnection() {
		global $connection;
		return $connection;
	}
	static function getNextSequnceID($seqName) {
		global $connection;
	
		$stmt = $connection->prepareStatement("SELECT value FROM __sequences WHERE name='$seqName'");
	    $rs = $stmt->executeQuery();
	    if ($rs->next()) {
	    	$value = $rs->getInt(1);
	    } else {
	    	$value = 0;
	    }
	    $value++;
	    $stmt->close();
	    $stmt = $connection->prepareStatement("REPLACE INTO __sequences SET value=$value, name='$seqName'");
	    $stmt->executeUpdate();
	
	    return $value;
	}
	
	static function getCurrentSequnceID($seqName) {
		global $connection;
	
		$stmt = $connection->prepareStatement("SELECT value FROM __sequences WHERE name='$seqName'");
	    $rs = $stmt->executeQuery();
	    $rs->next();
	    $value = $rs->getInt(0);
	
	    return $value;
	}
	
	static function execSelect($sql, $params = array()) {
		global $connection;
	
		$pstmt = $connection->prepareStatement($sql);
		
		foreach ($params as $i=>$p) {
			$pstmt->setObject($i + 1, $p);
		}
//		print_r($pstmt);
//		echo "<br><br>";
		$rs = $pstmt->executeQuery();
	
		$crs = $rs->getCachedResultSet();
	
		$pstmt->close();
		return $crs;
	}
	
	static function execCountSelect($sql, $params = array()) {
		global $connection;
	
		$pstmt = $connection->prepareStatement($sql);
		foreach ($params as $i=>$p) {
			$pstmt->setObject($i + 1, $p);
		}
		$rs = $pstmt->executeQuery();
		$rs->next();
		$c = $rs->getInt(1);
		$pstmt->close();
	
		return $c;
	}
	
	static function execUpdate($sql, $params = array()) {
		global $connection;
	
		$pstmt = $connection->prepareStatement($sql);
		foreach ($params as $i=>$p) {
			$pstmt->setObject($i + 1, $p);
		}
		$updatedCount = $pstmt->executeUpdate();
	
		$pstmt->close();
		return $updatedCount;
	}
	
	static function startTransaction() {
		global $connection, $_TRANSACTION_STATUS;
		if (sizeof($_TRANSACTION_STATUS) == 0) {
			DBUtils::execUpdate("BEGIN");
			array_push($_TRANSACTION_STATUS, "COMMIT");
		} else {
			array_push($_TRANSACTION_STATUS, "");
		}
	}

	static function commit() {
		global $connection, $_TRANSACTION_STATUS;

		if (sizeof($_TRANSACTION_STATUS) == 1) {
			DBUtils::execUpdate($_TRANSACTION_STATUS[0]);
		}
		array_pop($_TRANSACTION_STATUS);
	}

	static function rollback($force = false) {
		global $connection, $_TRANSACTION_STATUS;

		if ($force) {
			if (sizeof($_TRANSACTION_STATUS) > 0) {
				DBUtils::execUpdate("ROLLBACK");
				$_TRANSACTION_STATUS = array();
			}
		} else {
			if (sizeof($_TRANSACTION_STATUS) == 1) {
				DBUtils::execUpdate("ROLLBACK");
			} else {
				$_TRANSACTION_STATUS[0] = "ROLLBACK";
			}
			array_pop($_TRANSACTION_STATUS);
		}
	}

	static function execBatch($sqls, $params = array()) {
		global $connection;
	
		foreach ($sqls as $i=>$sql) {
			$pstmt = $connection->prepareStatement($sql);
			foreach ($params[$i] as $j=>$param) {
				$pstmt->setObject($j + 1, $param);
			}
			$updatedCount = $pstmt->executeUpdate();
			$pstmt->close();
		}
	}
	
	static function createRowSet($sqlCommand) {
		global $connection;
		$pstmt = $connection->prepareStatement($sql);
		$rset = $pstms->executeQuery();
	
		$crs = $rset->getCachedResultSet();
	
		$pstmt->close();
		return $crs;
	}
}
?>