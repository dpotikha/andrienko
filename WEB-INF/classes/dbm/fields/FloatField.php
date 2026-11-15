<?php

require_once ("dbm/fields/NumberField.php");

class FloatField extends NumberField {
	function setValue($o) {
		if (is_object($o) && strtolower(get_class($o)) == 'httprequest') {
			$rValue = $this->float($o->getParameter($this->fieldDefShortname));
		} else {
			$rValue = $this->float($o);
		}
		$fieldDef = $this->getFieldDef();
		$max_value = $this->float($fieldDef->getParameterValue("max_value"));
		$min_value = $this->float($fieldDef->getParameterValue("min_value"));
		
		$verror = null;
		if (($rValue <= $max_value) && ($rValue >= $min_value)) {
			$this->value = $rValue;
		} else {
			$verror = $AdminTrnsl["FloadtValueNotInRange"] . " [" . $min_value . "," . $max_value . "].";
		}

		return $verror;
	}
	function float($val)
    {   
        return preg_replace(array('/,/i','/[^0-9\.\-]/i'),array('.',''),$val);
    }
	
}

?>