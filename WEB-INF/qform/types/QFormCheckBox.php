<?php

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QFormCheckBox extends QFormElement {

    const TYPE               = 'checkbox';
    protected $checked_value = NULL;

    /*************************************************************************/

    public static function getInstance($field_name = '') {
        return new self(self::TYPE, $field_name);
    }
    
    /*************************************************************************/

    public function setChecked($checked_vals = NULL) {
        $this->checked_value = $checked_vals;
    }

    /*************************************************************************/

    public function getShortName() {
        if(is_array($this->field_default_value)) {
            return $this->getName() . '[]';
        }else {
            return $this->getName();
        }
    }

    /*************************************************************************/

    public function getDefaultValue($index = NULL) {
        if(is_array($this->field_default_value) && !(is_null($index) || $index===false))
            return $this->field_default_value[$index];
        else
            return $this->field_default_value;
    }

    /*************************************************************************/

    public function getPostValue() {
        $value = isset($_POST[$this->getName()]) ? ($_POST[$this->getName()]) : NULL;
        return $value;
    }

    /*************************************************************************/

    protected function _createErrorContainer($error_tag = '', $css_error_class  = '') {
        return '<' . $error_tag . ' class="' . $css_error_class . '" id="error_' . $this->getId() . '" htmlfor="' . $this->getShortName() . '" generated="true">' . $this->error_message . '</' . $error_tag . '>';
    }
    /*************************************************************************/

    public function create($index = NULL) {
        if($this->getForm()->isSubmitted())
            if(is_array($this->checked_value)) {
                $checked  = (in_array($this->getDefaultValue($index),$this->getPostValue())) ? 'checked' : '';
            }else {
                $checked  = ($this->field_default_value == $this->getPostValue()) ? 'checked' : '';
            }
        else {
            if(is_array($this->checked_value)) {
                $checked = (in_array($this->getDefaultValue($index),$this->checked_value)) ? 'checked' : '';
            }elseif(!is_null($this->checked_value)) {
                $checked  = ($this->field_default_value == $this->checked_value) ? 'checked' : '';
            }
        }
        return '<input id="' . $this->getId($index) . '" name="' . $this->getShortName() . '" type="checkbox" ' . $this->getCSS() . ' value="' . $this->getDefaultValue($index) . '" ' . $checked . ' ' . $this->getAttributes() . ' />';
    }

    /*************************************************************************/
 /*************************************************************************/

    public static function setFields($form, $fields){
        $f = array();
        foreach($fields as $k=>$v) {
            $f[$k] = self::getInstance($k);
            if(!is_null($v)) {
                $f[$k]->setValue($v);
            }
        }
        $form->setFields($f);
        return $form;
    }

    /*************************************************************************/

    public static function setField(QForm &$form, $field, $value = NULL, $default_value=NULL, $required=false){
        self::setFields($form, array($field  =>  $value));
        if(!is_null($default_value) && $default_value==$value){
            $form->setFieldsChecked(array($field=>$default_value));
        }
        if($required){
            $form->setFieldsRules(array($field=>'required'));
        }
        return $form;
    }
      /*************************************************************************/
    public function getSkipPHPValidate() {
        if($this->_getRequired()) {
            return true;
        }elseif($this->getPostValue()==NULL){
            return false;
        }
        
        return true;
    }
}
?>