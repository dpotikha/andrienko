<?php

require_once ("dbm/fields/IntegerField.php");

class BoolField extends IntegerField {
	function setValue($o) {
		global $AdminTrnsl;
		if (is_object($o) && strtolower(get_class($o)) == 'httprequest') {
			$rValue = (int)$o->getParameter($this->fieldDefShortname);
		} else if (is_bool($o)) {
			$rValue = (bool)$o ? 1 : 0;
		} else {
			$rValue = (int)$o;
		}

		$verror = null;
		if (($rValue == 0) || ($rValue == 1)) {
			$this->value = $rValue;
		} else {
			$verror = $AdminTrnsl["BadBoolValue"];
		}

		return $verror;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();
		$params["value"] = ($this->value == 1);

		return $params;
	}

	function getHtmlVisualValue() {
		global $AdminTrnsl;
		return ($this->value) == 1 ? $AdminTrnsl["Yes"] : $AdminTrnsl["No"];
	}

	function getValueForTemplate() {
		return ($this->value == 1);
	}
}

?>