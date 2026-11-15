<?php

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/
class QFormFile extends QFormElement {

    const TYPE                          =   'file';
    /*************************************************************************/

    public static function getInstance($field_name = '') {
        return new self(self::TYPE, $field_name);
    }

    /*************************************************************************/
    
    public static function setFields(&$form, $fields) {
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

    public static function setField(&$form, $field, $value) {
        return self::setFields($form, array($field  =>  $value));
    }

    /*************************************************************************/

    public function getPostValue() {
        $value = (isset($_FILES[$this->getName()]) && $_FILES[$this->getName()]['size'] && $_FILES[$this->getName()]['tmp_name'])
                ? $_FILES[$this->getName()]
                : NULL;
        return $value;
    }

    /*************************************************************************/

    public function create() {
        return '<input id="' . $this->getId() . '" name="' . $this->getShortName() . '" type="file" ' . $this->getCSS()  . ' ' . $this->getAttributes() . '/>';
    }

    /*************************************************************************/


}
?>