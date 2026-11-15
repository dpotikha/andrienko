<?php

require_once ("dbm/fields/Field.php");

define("DATETIME_FORMAT", "d.m.Y H:i:s");

class DateTimeField extends Field {
	var $dbColumnName = "value_timestamp";
	function __construct() {}

	static function getDefaultValue($fd) {
		$value = new Date();
		return $value;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();

		$date = $this->value;

		$params["day"] = $date->getDay();
		$params["month"] = $date->getMonth();

		$params["year"] = $date->getYear();
		$params["hour"] = $date->getHour();
		$params["minute"] =$date->getMinute();
		$params["second"] = $date->getSecond();

		$params["formatted_date"] = $date->format(DATE_FORMAT);
		$params["formatted_datetime"] = $date->format(DATETIME_FORMAT);

		return $params;
	}

	function setValue($o) {
//		print_r(get_class($o));
		if (is_object($o) && strtolower(get_class($o)) == 'httprequest') {
			$rValue = $o->getParameter($this->fieldDefShortname);
			if(preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/iU",$rValue,$arr)){
				$rValue=$arr[3]."-".$arr[2]."-".$arr[1]." ".$arr[4].":".$arr[5].":".$arr[6];
			}
		} elseif(is_string($o)) {
			$rValue = $o;
		}
//		p($rValue);
			$this->value = new Date($rValue);
	}

	function getValueForTemplate() {
		$v = $this->getParamersForIncludeControlJSP();
		return $v;
	}

	function getHtmlVisualValue() {
		return $this->value->format(DATETIME_FORMAT);
	}

	function getJSPControlName() {
		$class = strtolower(get_class($this));

		$fieldDef = $this->getFieldDef();
		$ctype = $fieldDef->getParameterValue("control_type");

		if ($ctype == null || $ctype=="") {
			$cname = $class;
			return $cname;
		}

		$cname = $class . "_" . $ctype;

		return $cname;
	}
}

?>