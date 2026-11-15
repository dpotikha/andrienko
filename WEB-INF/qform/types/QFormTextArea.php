<?php

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QFormTextArea extends QFormText {

    const TYPE                          =   'textarea';
    protected $css_default_class	= 'input_textarea';
    protected $field_maxlength		= 65000;

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

     protected function setFiltered($value){
        if(!is_array($value)){
            return CProtect::tiny_html($value, 16000);
        }else{
            return $value;
        }
    }
    
    /*************************************************************************/

    public function create() {

        return '<textarea id="' . $this->getId() . '" name="' . $this->getShortName() . '" ' . $this->getCSS() . ' '  . ' ' . $this->getAttributes() . ' >' . $this->getHtmlValue() . '</textarea>';
    }

    /*************************************************************************/


}
?>