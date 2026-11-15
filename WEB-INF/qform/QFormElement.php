<?php

/*******************************************************************************
 *
 *
 *
 *
 *
 *******************************************************************************/

abstract class QFormElement {

    /*************************************************************************/

    protected $error_message            = '';
    protected $field_type               = 'text';
    protected $validation_rules         = array();
    protected $filter_rules             = array();
    protected $values                   = array();
    protected $attributes               = array();

    protected $field_index              = false;
    protected $field_name               = '';
    protected $field_maxlength          = 512;
    protected $field_default_value      = '';
    protected $css_default_class        = '';
    protected $disabled                 = false;
    protected $form_id                  = NULL;
    protected $is_rendered              = true;
    protected $render_title              = 'Element';

    /*************************************************************************/

    abstract public function create();

    /*************************************************************************/

    public function __construct($field_type = 'text', $field_name = '') {
        $this->field_type = $field_type;
        $this->field_name = $field_name;
    }
    
    /*************************************************************************/

    protected function _getRequired() {
        if(in_array('required',$this->validation_rules)) {
            return true;
        }else {
            return false;
        }
    }
    public function isRequired(){
        if(isset($this->validation_rules['required'])) {
            return true;
        }else {
            return false;
        }
    }
    public function getRenderTitle(){
        return $this->render_title;
        
    }
    public function setRenderTitle($render_title){
        $this->render_title=$render_title;
        
    }
    /*************************************************************************/
    public function isRendered() {
        return $this->is_rendered;
    }

    /*************************************************************************/
    public function render() {
        if($this->isRendered()){
            $this->_render();//tinclude('form_renders/fr_'.self::TYPE, array('form_element'=>$this));
        }
    }
 /*************************************************************************/
    protected function _render() {
        
    }

    /*************************************************************************/
    public function notRendered() {
        $this->is_rendered = false;
    }

    /*************************************************************************/

    public function getSkipPHPValidate() {
        return !$this->_getRequired();
    }

    /*************************************************************************/

    public function setValues($values) {
        $this->values = $values;
        return $this;
    }

    /*************************************************************************/

    final public function setForm($form_id) {
        $this->form_id = $form_id;
        return $this;
    }

    /*************************************************************************/

    final public function getForm() {
        return QForm::getInstance($this->form_id);
    }

    /*************************************************************************/

    final public function setCSS($css_default_class = '') {
        $this->css_default_class = $css_default_class;
        return $this;
    }

    /*************************************************************************/

    public function setValue($field_value='') {
        $this->field_default_value	 = $field_value;
        return $this;
    }

    /*************************************************************************/

    public function setIndex($index = 0) {
        $this->field_index	 = $index;
        return $this;
    }

    /*************************************************************************/

    public function setDisabled() {
        $this->disabled	 = true;
        return $this;
    }

    /*************************************************************************/

    public function setMaxLength($max_length = 0) {
        $this->field_maxlength = $max_length;
        return $this;
    }

    /*************************************************************************/

    final public function setRules($rules) {
        $rules = explode('|' , $rules);
        foreach($rules as $k => $v) {
            if(!mb_strlen($v))continue;
            if(preg_match('/^([a-z0-9_]+)\[([a-z0-9_,\.:=&\?\/{}-]+)\]/i' , $v , $matches)) {
                
                $this->validation_rules[$matches[1]]	= $matches[2];
            }else {
                $this->validation_rules[$v]		= '';
            }
        }
        return $this;
    }

    /*************************************************************************/

    final public function setFilters($filters) {
        $filters = explode('|' , $filters);
        foreach($filters as $k => $v) {
            if(preg_match('/^([a-z0-9_]+)\[([a-z0-9_,\.:=\?\/{}-]+)\]/i',$v,$matches)) {
                $this->filter_rules[$matches[1]]    =   $matches[2];
            }else {
                $this->filter_rules[$k]             =   $v;
            }
        }
        return $this;
    }

    /*************************************************************************/

    final public function getCSS() {
        if(!empty($this->css_default_class))
                $class = $this->css_default_class;
        if(!empty($this->error_message))
                $class .= ' ' . $this->getForm()->css_error_class;
        if (strlen($class))
            return "class='$class'";
        return '';
    }

    /*************************************************************************/

    public function getMaxLength() {
        if($this->field_maxlength === false)
            return '';
        else
            return 'maxlength="' . $this->field_maxlength . '"';
    }

    /*************************************************************************/

    public function getName() {
        return $this->field_name;
    }

    /*************************************************************************/

    public function getShortName() {
        return $this->getName();
    }

    /*************************************************************************/

    public function getShortNameForJSValidator() {
        return $this->getShortName();
    }

    /*************************************************************************/

    public function getId($field_index = NULL) {
        if(is_null($field_index))
            return $this->form_id . '_' . $this->getName();
        return $this->form_id . '_' . $this->getName() . '_' . $field_index;
    }

    /*************************************************************************/

    public function getValue($field_index = NULL) {
        if($this->getForm()->isSubmitted() && is_null($field_index)) {
            return $this->getPostValue();
        }else {
            if(is_array($this->field_default_value) && !is_null($field_index)) {
                return $this->field_default_value[$field_index];
            }else {
                return $this->field_default_value;
            }
        }
    }

    /*************************************************************************/

    public function getHtmlValue() {
        return htmlspecialchars($this->getValue());
    }

    /*************************************************************************/

    public function getPostValue() {
        $value = isset($_POST[$this->getName()]) ? stripslashes($_POST[$this->getName()]) : NULL;
        return $this->setFiltered($value);
    }

    /*************************************************************************/
    
    protected function setFiltered($value){
        if(!is_array($value)){
            return CProtect::string($value);
        }else{
            return $value;
        }
    }

    /*************************************************************************/

    public function createErrorContainer() {
        $form				=	$this->getForm();
        return $this->_createErrorContainer($form->getDOMErrorTag(), $form->getCSSErrorClass());
    }

    /*************************************************************************/

    protected function _createErrorContainer($error_tag = '', $css_error_class  = '') {
        return '<' . $error_tag . ' class="' .$css_error_class . '" id="error_' . $this->getId() . '" for="' . $this->getId() . '" generated="true">' . $this->getErrorMessage() . '</' . $error_tag . '>';
    }
    
    /*************************************************************************/

    final public function validate() {
        $value = $this->getPostValue();
        $errors = array();
        foreach($this -> validation_rules as $rule => $rule_params) {
            $r = 'Q' . $rule . 'Rule';
            $ruleObj = call_user_func_array (array($r, 'getInstance'),array($this->form_id, &$this, $r));
            $err = $ruleObj -> validate($value, $rule_params);
            if($err === false) {
                $errors[] = $rule;
                break;
            }
        }
        if(empty($errors))
            return array();

        $this->setErrorMessage(QLang::getFormError($this->getId(),$errors[0]));
        return array('container' => $this->getId(), 'message' => $this->getErrorMessage());
    }

    /*************************************************************************/

    final public function setErrorMessage($error_message = '') {
        $this->error_message = $error_message;
        return $this;
    }

    /*************************************************************************/

    final public function getErrorMessage() {
        return $this->error_message;
    }

    /*************************************************************************/

    final public function setAttribute($attr_name = '', $attr_value = '') {
        $this->attributes[$attr_name] = $attr_value;
        return $this;
    }

    /*************************************************************************/

    protected final function getAttributes() {
        $attr = array();
        foreach($this->attributes as $k => $v){
            $attr[] = $k . '="' . htmlspecialchars($v) . '"';
        }
        return implode(' ', $attr);
    }

    /*************************************************************************/

    final public function generateJSValidation() {

        $rules    = array();
        $messages = array();
        foreach($this -> validation_rules as $rule => $rule_params) {
            $r		= 'Q' . $rule . 'Rule';
            $ruleObj	= call_user_func_array (array($r, 'getInstance'),array($this->form_id, &$this, $r));
            $jsRule	= $ruleObj -> getJavaScriptRule($rule, $this->getId(), $rule_params);
            $rules[]	= $jsRule['rule'];
            $messages[] = $jsRule['message'];
        }
        return array('rules' => $rules, 'messages' => $messages);
    }

    /*************************************************************************/
    public static function setFieldRules(&$form, $field, $rules){
        $form->setFieldsRules(array(
                $field  => $rules
        ));
        return $form;
    }
    /*************************************************************************/


}
?>