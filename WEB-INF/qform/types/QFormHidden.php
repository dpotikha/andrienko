<?php

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QFormHidden extends QFormElement {

    const TYPE                          =   'hidden';
    /*************************************************************************/

    public static function getInstance($field_name = '') {
        $field=new self(self::TYPE, $field_name);
        $field->notRendered();
        return $field;
    }
    
    /*************************************************************************/

    public static function setFields($form, $fields){
        $form->setHiddenFields($fields);
        return $form;
    }

    /*************************************************************************/

    public static function setField(&$form, $field, $value = NULL){
        return self::setFields($form, array($field  =>  $value));
    }

    /*************************************************************************/
    public function create() {
        return '<input id="' . $this->getId() . '" name="' . $this->getShortName() . '" type="hidden"  value="' . $this->getHtmlValue() . '"' . ' ' . $this->getAttributes() . ' />';
    }

    /*************************************************************************/


}
?>