<?php

/* * *****************************************************************************
 *
 *
 *
 *
 *
 * ***************************************************************************** */

class QFormRadioBox extends QFormElement {
    const TYPE = 'radiobox';

    /*     * ********************************************************************** */

    protected $checked_value = NULL;

    /*     * ********************************************************************** */

    public static function getInstance($field_name = '') {
        return new self(self::TYPE, $field_name);
    }

    /*     * ********************************************************************** */

    public function setChecked($checked_value) {
        $this->checked_value = $checked_value;
        return $this;
    }

    /*     * ********************************************************************** */

    public static function setFields(&$form, $fields) {
        $f = array();
        foreach ($fields as $k => $v) {
            $f[$k] = self::getInstance($k);
            if (!is_null($v)) {
                $f[$k]->setValue($v);
            }
        }
        $form->setFields($f);
        return $form;
    }

    /*     * ********************************************************************** */

    public static function setField(QForm &$form, $field, $values, $default_value=NULL, $required=false) {
        
        self::setFields($form, array($field => $values));
        if(!is_null($default_value) && in_array($default_value,$values)){
            $form->setFieldsChecked(array($field=>$default_value));
        }
        if($required){
            $form->setFieldsRules(array($field=>'required'));
        }
        return $form;
        
    }

    /*     * ********************************************************************** */

    public static function setCheckedValue($form, $shortname, $checked_value) {
        return $form->getFieldByName($shortname)->setChecked($checked_value);
    }

    /*     * ********************************************************************** */

    public function getShortName() {
        return $this->getName();
    }

    /*     * ********************************************************************** */

    protected function _createErrorContainer($error_tag = '', $css_error_class = '') {
        return '<' . $error_tag . ' class="' . $css_error_class . '" id="error_' . $this->getId() . '" htmlfor="' . $this->getName() . '" generated="true">' . $this->error_message . '</' . $error_tag . '>';
    }

    /*     * ********************************************************************** */

    public function create($index = 0) {
        if ($this->getForm()->isSubmitted()) {
            $checked = ($this->getValue($index) == $this->getPostValue()) ? 'checked' : '';
        } else {
            $checked = (!is_null($this->checked_value) && $this->checked_value == $index) ? 'checked' : '';
        }
        $disabled = ($this->disabled) ? 'disabled' : '';
        return '<input id="' . $this->getId($index) . '" name="' . $this->getShortName() . '" type="radio" ' . $this->getCSS() . ' value="' . $index . '" ' . $checked . ' ' . $disabled . ' ' . $this->getAttributes() . ' />';
    }

    /*     * ********************************************************************** */

    protected function _render() {
        tinclude('form_renders/fr_' . strtolower(self::TYPE), array('form_element' => $this));
    }

    public function getInputBlock() {
        $values = $this->getValue();
        $t = array();
        foreach($values as $k=>$v){
            $t[]= $this->getForm()->d($this->field_name, $k).' '.$v;
        }
        return $t;
    }

    public function getErrorBlock() {
        return $this->getForm()->dE($this->field_name);
    }

}

?>