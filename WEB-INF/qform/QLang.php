<?php

class QLang{
	public static function get($lang_shortname=''){
		global $LOCALE;
		if(isset($LOCALE[$lang_shortname]))
			return $LOCALE[$lang_shortname];
		return '';
	}
	public static function getFormError($element='', $rule=''){
		global $LOCALE;
		$arr=explode('_',$element);
		array_shift($arr);
		$element=implode('_',$arr);
                if(preg_match('/(.+)-([0-9]+)$/iU',$element,$matches) && isset($LOCALE['errors.' . $matches[1] . '-{0}.' . $rule])){
                    return str_replace('{0}',$matches[2],$LOCALE['errors.' . $matches[1] . '-{0}.' . $rule]);
                }elseif(isset($LOCALE['errors.' . $element . '.' . $rule]))
			return $LOCALE['errors.' . $element . '.' . $rule];
		elseif(isset($LOCALE['errors.' . $rule]))
			return $LOCALE['errors.' . $rule];
		return 'Unspecified error when filling fields';
	}
}
?>
