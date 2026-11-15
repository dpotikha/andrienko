<?php

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/

class QFormText extends QFormElement {

    const TYPE                          =   'text';
    protected $css_default_class	=   'input_text';

    /*************************************************************************/

    public static function getInstance($field_name = '') {
        return new self(self::TYPE, $field_name);
    }

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

    public static function setField(QForm &$form, $field, $value = NULL, $required=false){
        self::setFields($form, array($field  =>  $value));
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
            return true;
        }elseif(!strlen($this->getPostValue())) {
            return false;
        }
        return true;
    }

    /*************************************************************************/

    public function create() {
        return '<input id="' . $this->getId() . '"  name="' . $this->getShortName() . '" type="text" ' . $this->getCSS() . ' value="' . $this->getHtmlValue() . '" ' . $this->getMaxLength() . ' ' . $this->getAttributes() . ' />';
    }

    /*************************************************************************/
    protected function _render() {
        tinclude('form_renders/fr_'.strtolower(self::TYPE), array('form_element'=>$this));
    }
    public function getInputBlock(){
        return $this->getForm()->d($this->field_name);
    }
     public function getErrorBlock(){
        return $this->getForm()->dE($this->field_name);
    }
}
?>