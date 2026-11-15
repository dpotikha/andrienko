<?php

/* * *****************************************************************************
 *
 *
 *
 *
 *
 * ***************************************************************************** */

class QFormSelectBox extends QFormElement {
    const TYPE = 'selectbox';
    protected $null_title = NULL;
    protected $selected_value = NULL;
     protected $css_default_class	=   'input_select';

    /*     * ********************************************************************** */

    public static function getInstance($field_name = '') {
        return new self(self::TYPE, $field_name);
    }

    /*     * ********************************************************************** */

    public function setValues($values, $null_title=NULL) {
        $this->values = $values;
        $this->null_title = $null_title;
        return $this;
    }

    /*     * ********************************************************************** */
    
    

    /*     * ********************************************************************** */
    
 public static function setField(QForm &$form, $field, $values = array(), $title=NULL, $value=NULL, $required=false){
        $form->setSelectBoxFields(array($field=>array($values,$title)));
        if(!is_null($value)){
            $form->setFieldsSelected(array($field=>$value));
        }
        if($required){
            $form->setFieldsRules(array($field=>'required'));
        }
        return $this;
    }
    /*     * ********************************************************************** */

    public function setSelected($value) {
        $this->selected_value = $value;
        return $this;
    }

    /*     * ********************************************************************** */

    public function create() {
        if ($this->getForm()->isSubmitted()) {
            $val = $this->getPostValue();
        } else {
            $val = $this->selected_value;
        }
        $select = '<select id="' . $this->getId() . '" name="' . $this->getShortName() . '"  ' . $this->getCSS() . ' ' . $this->getAttributes() . ' >';
        if (!is_null($this->null_title))
            $select .= '<option value="">' . $this->null_title . '</option>';
        foreach ($this->values as $k => $v) {
            $selected = (!is_null($val) && $val == $k) ? 'selected' : '';
            $select .= '<option ' . $selected . ' value="' . $k . '">' . $v . '</option>';
        }
        $select .= '</select>';
        return $select;
    }

    /*     * ********************************************************************** */

    protected function _render() {
        tinclude('form_renders/fr_' . strtolower(self::TYPE), array('form_element' => $this));
    }

    public function getInputBlock() {
        return $this->getForm()->d($this->field_name);
    }

    public function getErrorBlock() {
        return $this->getForm()->dE($this->field_name);
    }

}

?>