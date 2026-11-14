<?php

require_once ("dbm/fields/DateTimeField.php");

define("DATE_FORMAT", "d.m.Y");

class DateField extends DateTimeField {
	function DateField() {}

	function getParamersForIncludeControlJSP() {
		$params = array();

		$date = $this->value;

		$params["day"] = $date->getDay1();
		$params["month"] = $date->getMonth();
		$params["year"] = $date->getYear();

		$params["formatted_date"] = $date->format(DATE_FORMAT);
		return $params;
	}

	function setValue($o) {
		if (is_object($o) && strtolower(get_class($o)) == 'httprequest') {
			$rValue = $o->getParameter($this->fieldDefShortname);
			$this->value = new Date($rValue);
		} elseif(is_string($o)) {
			$rValue = $o;
			//echo $o;
			$this->value = new Date($rValue);
		} elseif (is_object($o) && strtolower(get_class($o)) == 'date') {
			$this->value = $o;
		}
	}

	function getHtmlVisualValue() {
		return $this->value->format(DATE_FORMAT);
	}
}

?>