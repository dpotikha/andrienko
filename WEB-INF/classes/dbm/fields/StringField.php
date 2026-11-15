<?php

require_once ("dbm/fields/Field.php");

class StringField extends Field {
	var $dbColumnName = "value_str";
	function __construct () {}

	function setValue($o) {
		global $AdminTrnsl;
		if (is_object($o) && strtolower(get_class($o)) == 'httprequest') {
			$rValue = $o->getParameter($this->fieldDefShortname);
		} elseif(is_string($o)) {
			$rValue = $o;
		}

		$fieldDef = $this->getFieldDef();
		$max_length = (int)$fieldDef->getParameterValue("max_length");
		if ($max_length == 0) {
			$max_length = "255";
		}
		$min_length = (int)$fieldDef->getParameterValue("min_length");
		$regex = $fieldDef->getParameterValue("regex");
		$regex_error_msg = $fieldDef->getParameterValue("regex_error_msg");

		$verror = null;
		do {
			if (mb_strlen($rValue) == 0 && $fieldDef->required) {
				$verror = $AdminTrnsl["RequiredField"];
				break;
			}
			if (mb_strlen($rValue) > $max_length) {
				$verror = $AdminTrnsl["Length_of_string_too_long"] . $max_length;
				break;
			}
			if (mb_strlen($rValue) < $min_length) {
				$verror = $AdminTrnsl["Length_of_string_too_short"] . $max_length;
				break;
			}
			if ($regex && !preg_match('~' . str_replace('~', '\~', $regex) . '~D', $rValue)) {
				$verror = $regex_error_msg;
				break;
			}
		} while(false);

		$this->value = $rValue;
		return $verror;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();
		$itype = null;

		$fieldDef = $this->getFieldDef();

		$itype = $fieldDef->getParameterValue("inputtype");

		if (!$itype) {
			$itype = "text";
		}
		$params["inputtype"] =  $itype;

		$params["strvalue"] = $this->value;
		return $params;
	}

	function getDefaultParamersForIncludeControlJSP() {
		$params = array();

		$params["strvalue"] = $this->dbDefaultValue;
		$params["inputtype"] = "text";
		return $params;
	}

	function getValueForTemplate() {
		return $this->value;
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

	static function getDefaultValue($fd) {
		$value = "";
		return $value;
	}
}

?>