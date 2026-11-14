<?php

require_once ("dbm/fields/Field.php");

class Stats extends Field {
	function __construct () {}

	function getDefaultParamersForIncludeControlJSP() {
		// was TextareaField::getDefaultParamersForIncludeControlJSP(): cross-class $this call, an Error in PHP 8
		$params = array("strvalue" => $this->dbDefaultValue, "inputtype" => "text");
		if ($this->nodeId != -1) {
			$params["node_id"] = $this->nodeId;
			$params["field_shortname"] = $this->fieldDefShortname;
		}
		return $params;
	}

	function getParamersForIncludeControlJSP() {
		// was TextareaField::getParamersForIncludeControlJSP(): same logic inlined (see StringField)
		$itype = $this->getFieldDef()->getParameterValue("inputtype");
		$params = array("inputtype" => $itype ? $itype : "text", "strvalue" => $this->value);
		if ($this->nodeId != -1) {
			$params["node_id"] = $this->nodeId;
			$params["field_shortname"] = $this->fieldDefShortname;
		}
		return $params;
	}

	function getValueForTemplate() {
		return $this->value;
	}
}

?>