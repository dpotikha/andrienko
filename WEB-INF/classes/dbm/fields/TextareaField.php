<?php

require_once ("dbm/fields/StringField.php");

class TextareaField extends StringField {
	function TextareaField () {}

	function setValue($o) {
		global $AdminTrnsl;
		if (is_object($o) && strtolower(get_class($o)) == 'httprequest') {
			$request = $o;
			$rValue = $request->getParameter($this->fieldDefShortname);
			$rValue = str_replace("http://" . $request->getServerName() . ":" . $request->getServerPort(), "", $rValue);
			$rValue = str_replace("http://" . $request->getServerName(), "", $rValue);
		} elseif(is_string($o)) {
			$rValue = $o;
		}

		$fieldDef = $this->getFieldDef();
		$max_length = (int)$fieldDef->getParameterValue("max_length");
		if ($max_length == 0) {
			$max_length = "10240";
		}

		$verror = null;
		do {
			if (strlen($rValue) == 0 && $fieldDef->required) {
				$verror = $AdminTrnsl["RequiredField"];
				break;
			}
			if (strlen($rValue) > $max_length) {
				$verror = $AdminTrnsl["Length_of_string_too_long"] . $max_length . "!!!";;
				break;
			}
		} while(false);

		$this->value = $rValue;
		return $verror;
	}

	function getValueForTemplate() {
		if ($this->value) {
			return nl2br($this->value);
		} else {
			return $this->value;
		}
	}

	function getHtmlVisualValue() {
		if ($this->value) {
			return nl2br($this->value);
		} else {
			return $this->value;
		}
	}
}

?>
