<?php

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/

abstract class QFormValidationRule {

    /*************************************************************************/

    protected			$form			= NULL;
    protected			$form_element	= NULL;
    protected static	$instance		= NULL;

    /*************************************************************************/

    public function __construct($form, $form_element) {
        $this->form			= $form;
        $this->form_element = $form_element;
    }


    /*************************************************************************/

    public static function getInstance($form, $form_element, $className) {
        if(! self::$instance instanceof $className) {
            self::$instance = new $className($form, $form_element);
        }else {
            self::$instance->form		  = $form;
            self::$instance->form_element = $form_element;
        }
        return self::$instance;
    }


    /*************************************************************************/


    abstract function validate($value = '', $params = NULL);

    /*************************************************************************/

    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        return $this->makeJSRule($rule, 'true', QLang::getFormError($dom_element_id, $rule));
    }

    /*************************************************************************/

    protected function makeJSRule($rule, $ruleCond = 'true', $message = 'error') {
        $_rule		= '"' . $rule . '":'  . $ruleCond;
        $_message	= '"' . $rule . '":"' . addslashes($message) . '"';
        return array('rule' => &$_rule, 'message' => &$_message);
    }

    /*************************************************************************/

    protected function getForm() {
        return QForm::getInstance($this->form);
    }

    /*************************************************************************/

}

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/


class QRequiredRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        if(($value==NULL)){
            return false;
        }elseif(empty($value) && !($value==0)) {
            
            return false;
        }
        return true;
    }
    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        if($params=='false') {
            return $this->makeJSRule($rule, 'false', QLang::getFormError($dom_element_id, $rule));
        }else {
            return $this->makeJSRule($rule, 'true', QLang::getFormError($dom_element_id, $rule));
        }
    }
}


/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/


class QMaxLengthRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        return mb_strlen($value) <= (int)$params;
    }
    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        return $this->makeJSRule($rule, (int)$params, sprintf(QLang::getFormError($dom_element_id, $rule),$params));
    }
}

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/

class QMinLengthRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        return mb_strlen($value) >= (int)$params;
    }
    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        return $this->makeJSRule($rule, (int)$params, sprintf(QLang::getFormError($dom_element_id, $rule),$params));
    }
}

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/

class QValidEmailRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        return preg_match('/^[0-9a-z_\.-]+@[0-9a-z_^\.-]+\.[a-z]{2,6}$/i',$value);
    }
}

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QValidNicknameRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        return preg_match('/^[0-9a-z_-]+$/i',$value);
    }
}

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QValidShortNameRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        return preg_match('/^[0-9a-z_-]+$/i',$value);
    }
}

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QValidLatinRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        return preg_match('/^[a-z0-9!@%#$\^&*()\[\]+_=<>?.\/\\{}~"\'’`:;,| \r\n\t~¢£¤¥¦§¨©«¬®¯°±²³´·¸¹º»ǀǁǂ\‒\–\—\―‖‗‘’‚‛“”„‟†‡•…′″‴‹›₤€₵℅ℓ№℗™■□▪▫◊○◌●◦-]+$/i',$value);
    }
}
/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QValidPasswordRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        return preg_match('/^[a-z0-9_\$\@\*\%\^\?\(\)+=-]+$/i',$value);
    }
}

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/

class QEqualToRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        return $value == $this->getForm()->getFieldValue($params);
    }
    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        //dump($this->getForm()->getFields());
        return $this->makeJSRule($rule, '"#' . $this->getForm()->getFieldByName($params)->getId() . '"', QLang::getFormError($dom_element_id, $rule));
    }
}

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/

class QCheckAgeRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        return true;
        if($this->form_element->getSkipPHPValidate()) return true;
        $params     = explode(',', $params);
        $day        = $this->getForm()->getFieldValue($params[0]);
        $month      = $this->getForm()->getFieldValue($params[1]);
        $ageStart   = $params[2];
        $ageEnd     = $params[3];

        if(is_null($value) && is_null($day) && is_null($month))
            return true;

        $currentTime= mktime(0,0,0);
        $timeStart  = mktime(0,0,0,date('m',$currentTime),date('d',$currentTime),date('Y',$currentTime)-$ageStart);
        $timeEnd    = mktime(0,0,0,date('m',$currentTime),date('d',$currentTime),date('Y',$currentTime)-$ageEnd);

        $time       = mktime(0,0,0,$month,$day,$value);
        return  $value.sprintf('%02d',$month).sprintf('%02d',$day) == date('Ymd',$time) && $time<=$timeStart && $time>=$timeEnd;
    }
    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        $params = explode(',', $params);
        return $this->makeJSRule($rule, '"' . $this->getForm()->getFieldByName($params[0])->getId() . ',' . $this->getForm()->getFieldByName($params[1])->getId() . ',' . $params[2] . ',' . $params[3] . '"', QLang::getFormError($dom_element_id, $rule));
    }
}

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/

class QCheckDateRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        $params = explode(',', $params);
        $day    = $this->getForm()->getFieldValue($params[0]);
        $month  = $this->getForm()->getFieldValue($params[1]);
        if(is_null($value) && is_null($day) && is_null($month))
            return true;
        $time   = mktime(0,0,0,$month,$day,$value);
        return $value.sprintf('%02d',$month).sprintf('%02d',$day) == date('Ymd',$time);
    }
    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        $params = explode(',', $params);
        //  dump($this->form_element->getId());
        return $this->makeJSRule($rule, '"' . $this->getForm()->getFieldByName($params[0])->getId() . ',' . $this->getForm()->getFieldByName($params[1])->getId() . '"', QLang::getFormError($dom_element_id, $rule));
    }
}

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QAcceptRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        if(!isset($value['name'])) return true;
        return in_array(array_pop(explode('.',$value['name'])),explode(',',strtolower($params)));
    }
    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        return $this->makeJSRule($rule, '"' . $params . '"', QLang::getFormError($dom_element_id, $rule));
    }
}


/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QRemoteRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        //if($this->form_element->getSkipPHPValidate()) return true;
        //dump($params,$value);
        return true;
    }
    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        return $this->makeJSRule($rule, '"' . $params . '"', QLang::getFormError($dom_element_id, $rule));
    }
}
/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QSecretkeyRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        //if($this->form_element->getSkipPHPValidate()) return true;
        $result=($_SESSION['secretkey_'.$params]==$value)?true:false;
        $_SESSION['secretkey_'.$params]=rand(1000,99999);
        return $result;
    }
    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        return $this->makeJSRule($rule, '"' . $params . '"', QLang::getFormError($dom_element_id, $rule));
    }
}
/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QTwoDigitsAndTwoLettersRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        if (!preg_match('/.*[0-9].*[0-9].*/', $password))
                return false;
        if (!preg_match('/.*[a-zA-Z].*[a-zA-Z].*/', $password))
                return false;
        return true;
    }
}
/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/

class QNotEqualToRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        if($this->form_element->getSkipPHPValidate()) return true;
        return $value != $this->getForm()->getFieldValue($params);
    }
    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
        //dump($this->getForm()->getFields());
        return $this->makeJSRule($rule, '"#' . $this->getForm()->getFieldByName($params)->getId() . '"', QLang::getFormError($dom_element_id, $rule));
    }
}

class QdependsOnJoinTeamRule extends QFormValidationRule {
    public function validate($value = '', $params = NULL) {
        return true;
//        if($this->form_element->getSkipPHPValidate()) return true;
//        return $value != $this->getForm()->getFieldValue($params);
    }
//    public function getJavaScriptRule($rule, $dom_element_id, $params = NULL) {
//        //dump($this->getForm()->getFields());
//        return $this->makeJSRule($rule, '"#' . $this->getForm()->getFieldByName($params)->getId() . '"', QLang::getFormError($dom_element_id, $rule));
//    }
}

?>