<?php
class service_class {

/********************************************************************************************************************************************/

	var $MDB;

/********************************************************************************************************************************************/

	function __construct() {
		global $MDB;
		$this->MDB = $MDB;
	}

/********************************************************************************************************************************************/

	function getPhone($phone) {
		return preg_replace('/[^0-9]/iU','',$phone);
	}

/********************************************************************************************************************************************/

	function isValidPhone($phone) {
		$phone=$this->getPhone($phone);
		return strlen($phone)==12;
	}

/********************************************************************************************************************************************/

	function isValidEmail($email) {
		return preg_match('/^[0-9a-z_\.-]+@[0-9a-z_^\.-]+\.[a-z]{2,6}$/i',$email);
	}

/********************************************************************************************************************************************/

	function isValidLogin($login) {
		return preg_match('/^[0-9a-z_]{2,}$/i',$login);
	}

/********************************************************************************************************************************************/
	function isUniqueEmail($email) {
		if(!$this->isValidEmail($email))
			return false;
                $this->MDB->Query('SELECT n.id as ncount FROM dbm_nfv_user as m, dbm_nodes as n WHERE n.id=m.node_id AND m.email="' . $email . '" LIMIT 0,1');
		if($this->MDB->NumRows()>0)
			return false;
		return true;

	}
/********************************************************************************************************************************************/
	function isUniqueLogin($login) {
		if(!$this->isValidLogin($login))
			return false;
                $this->MDB->Query('SELECT n.id FROM dbm_nfv_user as m, dbm_nodes as n WHERE n.id=m.node_id AND n.name="' . $login . '" LIMIT 0,1');
		if($this->MDB->NumRows()>0)
			return false;
		return true;

	}
/********************************************************************************************************************************************/
	function isGoodPassword($password) {
            if (in_array($password, array('aaa111','1q2w3e4r','123qwe','1q2w3e','1qaz2wsx','q1w2e3','qwerty123','q1w2e3r4','qwe123456','ducati749','1q2w3e4r5t','geijr2006','qwe123','qwerty777','qwe123qwe123','80668020772her','axialax10','ann101289','rjycnfynby1234','Sa123456','qqq111','6o1o2o3o','q1w2e3r4t5','zaq123','12qwaszx','1234qwer','1q2w3e4r5t6y','zxc123','qwerty12','asusk50c')))
                return false;
            $password = db_escape($password);
            $this->MDB->Query("SELECT count(*) c FROM dbm_nfv_user WHERE password = '$password'");
            $res = $this->MDB->NextResult();
            if ($res['c'] > 5)
                return false;
            return true;
	}
/********************************************************************************************************************************************/


}


?>