<?php

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QFormPassword extends QFormText {
    const TYPE                          =   'password';

    /*************************************************************************/

    public static function getInstance($field_name = '') {
        return new self(self::TYPE, $field_name);
    }

    /*************************************************************************/


    public function create() {
        return '<input id="' . $this->getId() . '" name="' . $this->getShortName() . '" type="password" ' . $this->getCSS() . ' value="' . $this->getHtmlValue() . '" ' . $this->getMaxLength() . ' ' . $this->getAttributes() . '/>';
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
        return self::setFields($form, array($field  =>  $value));
    }

}
?>