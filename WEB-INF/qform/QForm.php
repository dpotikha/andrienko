<?php

/* * *****************************************************************************
 *
 *
 *
 *
 *
 * ***************************************************************************** */

class QForm {
    /*     * ********************************************************************** */

    /*    const FIELD_HIDDEN		= 'hidden';
      const FIELD_TEXT		= 'text';
      const FIELD_RADIOBOX		= 'radio';
      const FIELD_CHECKBOX		= 'checkbox';
      const FIELD_SELECTBOX		= 'select';
      const FIELD_TEXTAREA		= 'textarea';
      const FIELD_FILE                = 'file';
      const FIELD_PASSWORD		= 'password';

     */
    /*     * ********************************************************************** */

    private $ajax_submit_success_handler = false;
    private $error_messages = array();
    private $form_elements = array();
    private $form_id = '';
    private $multipart = false;
    private $action = '';
    private $form_attributes = array();
    private $css_form_class = '';
    public $css_error_class = 'error';
    private $css_valid_class = 'valid';
    private $css_ignore_class = 'ignore';
    private $dom_error_tag = 'em';
    private $is_submitted = false;
    private $enable_js_validation = true;
    private $ajax_submit = false;
    private $hidden_fields = array();
    private $hidden2_fields = array();
    private $errorContainer = NULL;
    private $highlight_handler = NULL;
    private $unhighlight_handler = NULL;
    private $default_values = NULL;
    protected static $instance = NULL;
    private $form_render_title = 'SubmitForm';
    private $form_render_submit_title = 'Submit';
    private $form_render_template = 'form';

    /*     * ********************************************************************** */

    public function getRenderTitle() {
        return $this->form_render_title;
    }

    public function setRenderTitlesForElements($elements) {
        foreach ($elements as $shortname => $title) {
            $this->getFieldByName($shortname)->setRenderTitle($title);
        }
    }

    public function setRenderTitle($form_render_title = '') {
        $this->form_render_title = $form_render_title;
    }

    public function getRenderSubmitTitle() {
        return $this->form_render_submit_title;
    }

    public function setRenderSubmitTitle($form_render_submit_title = '') {
        $this->form_render_submit_title = $form_render_submit_title;
    }

    public function setRenderTemplate($form_render_template = 'form') {
        $this->form_render_template = $form_render_template;
    }

    public function render() {
        tinclude('form_renders/fr_form_' . $this->form_render_template, array('form' => $this));
    }

    public function renderElements() {

        foreach ($this->form_elements as $k => $v) {
            if ($v->isRendered())
                $v->render();
            //dump($v,$k);
        }
    }

    public function __construct($id = '', $action = '') {
        $this->form_id = $id;
        if (strlen($action)) {
            $this->action = $action;
        }
        if (isset($_POST['form_' . $id . '_submitted']) && $_POST['form_' . $id . '_submitted'] == 1) {
            $this->is_submitted = true;
        }
        self::$instance[$id] = $this;
    }

    /*     * ********************************************************************** */

    public static function getInstance($id = '', $action = '') {
        if (!self::$instance[$id] instanceof self) {
            // dump($id,'+++');
            self::$instance[$id] = new self($id, $action);
        }
        return self::$instance[$id];
    }

    /*     * ********************************************************************** */

    private function _setAttribute($name, $value) {
        $this->form_attributes[$name] = $value;
    }

    /*     * ********************************************************************** */

    public function isSubmitted() {
        return $this->is_submitted;
    }

    /*     * ********************************************************************** */

    public function isAjaxSubmit() {
        return $this->ajax_submit;
    }

    /*     * ********************************************************************** */

    public function getErrors() {
        return $this->error_messages;
    }

    public function setAjaxSubmit($ajax_submit_success_handler = false) {
        $this->ajax_submit = true;
        $this->ajax_submit_success_handler = $ajax_submit_success_handler;
        return $this;
    }

    /*     * ********************************************************************** */

    protected function setMultipart() {
        $this->multipart = true;
        return $this;
    }

    /*     * ********************************************************************** */

    public function setCSS($form_class = '') {
        $this->css_form_class = $form_class;
        return $this;
    }

    /*     * ********************************************************************** */

    public function setErrorContainer($error_container = NULL) {
        $this->errorContainer = $error_container;
        return $this;
    }

    /*     * ********************************************************************** */

    public function setHighlightHandlers($highlight_handler = NULL, $unhighlight_handler = NULL) {
        $this->highlight_handler = $highlight_handler;
        $this->unhighlight_handler = $unhighlight_handler;
        return $this;
    }

    /*     * ********************************************************************** */

    public function setCSSErrorClass($error_class = 'error') {
        $this->css_error_class = $error_class;
        return $this;
    }

    /*     * ********************************************************************** */

    public function getCSSErrorClass() {
        return $this->css_error_class;
    }

    /*     * ********************************************************************** */

    public function setCSSValidClass($valid_class = 'valid') {
        $this->css_valid_class = $valid_class;
        return $this;
    }

    /*     * ********************************************************************** */

    public function setCSSIgnoreClass($ignore_class = 'ignore') {
        $this->css_ignore_class = $ignore_class;
        return $this;
    }

    /*     * ********************************************************************** */

    public function setDOMErrorTag($dom_error_tag = 'em') {
        $this->dom_error_tag = $dom_error_tag;
        return $this;
    }

    /*     * ********************************************************************** */

    public function getDOMErrorTag() {
        return $this->dom_error_tag;
    }

    /*     * ********************************************************************** */

    public function validate() {
        $errors = array();
        foreach ($this->form_elements as $k => &$v) {
            if (is_array($v)) {
                foreach ($v as $k1 => &$v1) {
                    $err = $v1->validate();
                    if (!empty($err))
                        $errors[] = $err;
                }
            }else {
                $err = $v->validate();
                if (!empty($err))
                    $errors[] = $err;
            }
        }
        $this->error_messages = &$errors;
        return count($errors) == 0;
    }

    /*     * ********************************************************************** */

    public function displayAjaxErrors() {
        header('Content-type: text/xml');
        echo '<errors>';
        foreach ($this->error_messages as $k => &$v) {
            echo '<e><c>' . $v['container'] . '</c><m><![CDATA[' . $v['message'] . ']]></m></e>';
        }
        echo '</errors>';
        exit();
    }

    /*     * ********************************************************************** */

    public function setError($element, $message) {
        $form_element = $this->getFieldByName($element);
        $form_element->setErrorMessage($message);
        $this->error_messages[] = array('container' => $form_element->getId(), 'message' => $form_element->getErrorMessage());
    }

    /*     * ********************************************************************** */

    public function start($action = '') {
        if (strlen($action)) {
            $this->action = $action;
        }
        $this->setHiddenFields(array('form_' . $this->getId() . '_submitted' => 1));
        $css_class = strlen($this->css_form_class) ? 'class="' . $this->css_form_class . '"' : '';
        return '<form id="' . $this->getId() . '" action="' . $this->action . '" method="post" ' . ((true || $this->ajax_submit) ? 'onsubmit="return false"' : '') . ($this->multipart ? 'enctype="multipart/form-data"' : '') . ' ' . $css_class . '>' . $this->getHiddenFieldsContainers();
    }

    /*     * ********************************************************************** */

    public function end() {
        return '</form>' . ($this->isEnabledJSValidation() ? $this->generateJSValidation() : '');
    }

    /*     * ********************************************************************** */

    public function setFields(&$fields = array()) {
        foreach ($fields as $k => &$v) {
            $v->setForm($this->getId());
            $this->form_elements[$v->getName()] = $v;
        }
    }

    /*     * ********************************************************************** */

    public function setEnableJSValidation() {
        $this->enable_js_validation = true;
        return $this;
    }

    /*     * ********************************************************************** */

    public function setDisableJSValidation() {
        $this->enable_js_validation = false;
        return $this;
    }

    /*     * ********************************************************************** */

    public function isEnabledJSValidation() {
        return $this->enable_js_validation;
    }

    /*     * ********************************************************************** */

    public function getFields() {
        return $this->form_elements;
    }

    /*     * ********************************************************************** */

    public function getFieldValue($form_element = NULL) {
        return $this->getFieldByName($form_element)->getPostValue();
    }

    /*     * ********************************************************************** */

    public function getPostValues($get_form_elements=array()) {
        $values = array();
        $count_get_form_elements = count($get_form_elements);
        foreach ($this->form_elements as $k => $v) {
            if (!isset($_POST[$k]) && !isset($_FILES[$k])) {
                continue;
            }
            if ($count_get_form_elements>0 && !in_array($k,$get_form_elements)) {
                continue;
            }
            if (is_array($v)) {
                $values[$k] = $this->getFieldValue($k);
            } else {
                $values[$k] = $this->getFieldValue($k);
            }
        }
        return $values;
    }

    /*     * ********************************************************************** */

    public function getFieldByName($shortname) {
        if (isset($this->form_elements[$shortname]))
            return $this->form_elements[$shortname];

        return false;
    }

    /*     * ********************************************************************** */

    public function getFieldId($shortname) {
        if (isset($this->form_elements[$shortname]))
            return $this->form_elements[$shortname]->getId();

        return false;
    }

    /*     * ********************************************************************** */

    public function display($field_name = '', $field_index = false) {
        // echo '</textarea>';print_r($field_name);
        return $this->getFieldByName($field_name)->create($field_index);
    }

    /*     * ********************************************************************** */

    public function d($field_name = '', $field_index = false) {
        return preg_replace('/ +/', ' ', $this->display($field_name, $field_index));
    }

    /*     * ********************************************************************** */

    public function displayErrorContainer($field_name = '', $field_index = false) {
        return $this->getFieldByName($field_name)->createErrorContainer();
    }

    /*     * ********************************************************************** */

    public function dE($field_name = '', $field_index = false) {
        return $this->displayErrorContainer($field_name, $field_index);
    }

    /*     * ********************************************************************** */

    public function generateJSValidation() {
        $js = array();
        $rules = array();
        $messages = array();
        foreach ($this->form_elements as $k => &$v) {
            if (is_array($v)) {
                foreach ($v as $k1 => &$v1) {
                    $res = $v1->generateJSValidation();
                    if (count($res['rules']) && count($res['messages'])) {
                        $rules[$v1->getShortName()] = '"' . $v1->getShortName() . '"' . ':{' . implode(',', $res['rules']) . '}';
                        $messages[$v1->getShortName()] = '"' . $v1->getShortName() . '"' . ':{' . implode(',', $res['messages']) . '}';
                    }
                }
            } else {
                $res = $v->generateJSValidation();
                //dump($v->getShortName());
                $sh_name = str_replace('-{0}', '', $v->getShortNameForJSValidator());
                if (count($res['rules']) && count($res['messages'])) {
                    $rules[$v->getShortName()] = '"' . $sh_name . '"' . ':{' . implode(',', $res['rules']) . '}';
                    $messages[$v->getShortName()] = '"' . $sh_name . '"' . ':{' . implode(',', $res['messages']) . '}';
                }
            }
        }
        //,

        /*        highlight: function(element, errorClass) {
          setTimeout('chName("'+element.name+'")',100);
          //chName(element.name);
          },
          unhighlight: function(element, errorClass) {
          //setTimeout('uchName("'+element.name+'")',1);
         */
        $default_values = '';
        if (!is_null($this->default_values)) {
            foreach ($this->default_values as $field_shortname => $val) {
                $default_values.='$("#' . $this->getFieldId($field_shortname) . '").defaultValue("' . addslashes($val) . '");';
            }
        }
        //dump($default_values);
        $highlight_handler = (is_null($this->highlight_handler)) ? '' : 'highlight: function(element, errorClass) {setTimeout("' . $this->highlight_handler . '(\'"+element.name+"\',\'"+element.id+"\',\'' . $this->form_id . '\')",50);},';
        $unhighlight_handler = (is_null($this->unhighlight_handler)) ? '' : 'unhighlight: function(element, errorClass) {' . $this->unhighlight_handler . '(element.name,element.id,"' . $this->form_id . '");},';
        $error_container = (!is_null($this->errorContainer)) ? 'errorContainer:"' . $this->errorContainer . '",errorLabelContainer: "' . array_shift(explode(',', $this->errorContainer)) . '",' : '';
        $js_rules = 'rules:{' . implode(',', $rules) . '},messages:{' . implode(',', $messages) . '}';
        /*$ajax_submit = ($this->isAjaxSubmit()) ? (',submitHandler: function(form) {$("#' . $this->form_id . '_loader").show();var f=$(form);var options = {dataType:  "xml",success:   processXml' . $this->form_id . 'Response};f.ajaxSubmit(options);}') : ',submitHandler: function(form) { alert("+++");form.submit();}';**/
        $ajax_submit = ($this->isAjaxSubmit()) ? (',submitHandler: function(form) {var f=$(form);var options = {dataType:  "xml",beforeSubmit:  function(formData, jqForm, options){
            if(_submitted'.$this->getId().'Form)return false;
			var form_id=jqForm.get(0).id;
			for(var k in formData){
				var obj=formData[k];
				if(typeof(tinyMCE)!="undefined" && tinyMCE.get(form_id+"_"+obj.name)){
					formData[k].value=tinyMCE.get(form_id+"_"+obj.name).getContent();
					tinyMCE.execCommand("mceRemoveControl", false, form_id+"_"+obj.name);
				}
			}
             if(typeof(beforeSubmit'.$this->form_id.'Handler)=="function") { 
                 var r = beforeSubmit'.$this->form_id.'Handler(formData, jqForm, options); 
				 
                 if(!r)return false;    
             }
            f.trigger("before_submit");
            _submitted'.$this->getId().'Form=true;
            $("#'.$this->form_id.'_loader").show();
            $(".'.$this->form_id.'_loader").show();
        },success:  function(responseText, statusText, xhr, $form){
                processXml'.$this->form_id.'Response(responseText);
               _submitted'.$this->getId().'Form=false;
               $("#'.$this->form_id.'_loader").hide();
                   $(".'.$this->form_id.'_loader").hide();
            } 
            
        };f.ajaxSubmit(options);}') : ',submitHandler: function(form) {form.submit();}';
        
        $processXmlfunction = ($this->isAjaxSubmit()) ? ('function processXml' . $this->form_id . 'Response(responseXML){processXml(responseXML,"' . $this->form_id . '", ' . $this->getJavaScriptSuccessHandler() . ')}') : '';
        return '<script type="text/javascript">$().ready(function() {' . $default_values . '$("#' . $this->form_id . '").validate({' . $error_container . $highlight_handler . $unhighlight_handler . 'errorClass: "' . $this->css_error_class . '",validClass: "' . $this->css_valid_class . '",ignore: ".' . $this->css_ignore_class . '",errorElement: "' . $this->dom_error_tag . '",onfocusout: false,onkeyup: false,onclick: false,invalidHandler: function(form, validator) {$("#' . $this->form_id . '_loader").hide();$(".' . $this->form_id . '_loader").hide();},' . $js_rules . $ajax_submit . '});' . $processXmlfunction . '});var _submitted'.$this->getId().'Form=false;</script>';
    }

    /*     * ********************************************************************** */

    public function setDefaultValues($fields) {
        $this->default_values = $fields;
        return $this;
    }

    /*     * ********************************************************************** */

    public function setTextFields($fields) {
        $f = array();
        foreach ($fields as $k => $v) {
            $f[$k] = QFormText::getInstance($k);
            if (!is_null($v)) {
                $f[$k]->setValue($v);
            }
        }
        $this->setFields($f);
        return $this;
    }

    /*     * ********************************************************************** */

    public function getHiddenFieldsContainers() {
        $hidden_fields = '';
        foreach ($this->hidden_fields as $k => &$v) {
            $hidden_fields .= $v->create();
        }
        return $hidden_fields;
    }

    /*     * ********************************************************************** */

    public function setHiddenFields($fields) {
        $f = array();
        foreach ($fields as $k => $v) {
            $f[$k] = QFormHidden::getInstance($k);
            if (!is_null($v)) {
                $f[$k]->setValue($v);
            }
            $this->hidden_fields[] = $f[$k];
        }
        $this->setFields($f);
        return $this;
    }

    /*     * ********************************************************************** */

    public function setHidden2Fields($fields) {
        $f = array();
        foreach ($fields as $k => $v) {
            $f[$k] = QFormHidden::getInstance($k);
            if (!is_null($v)) {
                $f[$k]->setValue($v);
            }
            $this->hidden2_fields[] = $f[$k];
        }
        $this->setFields($f);
        return $this;
    }

    /*     * ********************************************************************** */

    public function setPasswordFields($fields) {
        $f = array();
        foreach ($fields as $k => $v) {
            $f[$k] = QFormPassword::getInstance($k);
            if (!is_null($v)) {
                $f[$k]->setValue($v);
            }
        }
        $this->setFields($f);
        return $this;
    }

    /*     * ********************************************************************** */

    public function setFileFields($fields) {
        $f = array();
        foreach ($fields as $k => $v) {
            $f[$k] = QFormFile::getInstance($k);
        }
        $this->setMultipart();
        $this->setFields($f);
        return $this;
    }

    /*     * ********************************************************************** */

    public function setFieldAttributes($field_shortname = '', $attributes = array()) {
        $field = $this->getFieldByName($field_shortname);
        if ($field) {
            foreach ($attributes as $k => $v) {
                $field->setAttribute($k, $v);
            }
        }
        return $this;
    }

    /*     * ********************************************************************** */

    public function setTextAreaFields($fields) {
        $f = array();
        foreach ($fields as $k => $v) {
            $f[$k] = QFormTextArea::getInstance($k);
            if (!is_null($v)) {
                $f[$k]->setValue($v);
            }
        }
        $this->setFields($f);
        return $this;
    }

    /*     * ********************************************************************** */

    public function setSelectBoxFields($fields) {
        $f = array();
        foreach ($fields as $k => $v) {
            $f[$k] = QFormSelectBox::getInstance($k);
            if (!is_null($v)) {
                $f[$k]->setValues($v[0], $v[1]);
            } else {
                $f[$k]->setValues($v[0], null);
            }
        }
        $this->setFields($f);
        return $this;
    }

    /*     * ********************************************************************** */

    public function setRadioBoxFields($fields) {
        $f = array();
        foreach ($fields as $k => $v)
            $f[] = QFormRadioBox::getInstance($k)->setValue($v);
        $this->setFields($f);
        return $this;
    }

    /*     * ********************************************************************** */

    public function setCheckBoxFields($fields) {
        $f = array();
        foreach ($fields as $k => $v)
            $f[] = QFormCheckBox::getInstance($k)->setValue($v);
        $this->setFields($f);
        return $this;
    }

    /*     * ********************************************************************** */

    public function setFieldsCSS($fields, $css = '') {
        if(!is_array($fields)){
            $fields=array($fields);
        }
        foreach ($fields as $k => $v) {
            $this->getFieldByName($v)->setCSS($css);
        }
        return $this;
    }

    /*     * ********************************************************************** */

    public function setFieldsChecked($fields) {
        foreach ($fields as $k => $v) {
            $this->getFieldByName($k)->setChecked($v);
        }
        return $this;
    }

    /*     * ********************************************************************** */

    public function setFieldsSelected($fields) {
        foreach ($fields as $k => $v) {
            $this->getFieldByName($k)->setSelected($v);
        }
        return $this;
    }

    /*     * ********************************************************************** */

    public function setFieldsRules($fields) {
        foreach ($fields as $k => $v) {
            if (is_array($this->form_elements[$k])) {
                $this->getFieldByName($k, 0)->setRules($v);
            } else {
                $this->getFieldByName($k)->setRules($v);
            }
        }
        return $this;
    }

    /*     * ********************************************************************** */

    function setDisabledFields($fields=array()) {
        foreach ($fields as $k => $v) {
            if (is_array($this->form_elements[$v])) {
                foreach ($this->form_elements[$v] as $k1 => $v1) {
                    $this->form_elements[$v][$k1]->setDisabled();
                }
            } else {
                $this->form_elements[$v]->setDisabled();
            }
        }
        return $this;
    }

    /*     * ********************************************************************** */

    public function getSubmitFormButton($text = 'Save') {
        return '<button class="qbutton"  type="submit">' . $text . '</button>';
    }

    /*     * ********************************************************************** */

    public function getCloseDialogButton($text = 'Cancel') {
        return '<button class="qbutton"  type="button"  onclick="' . $this->getCloseDialogScript() . '">' . $text . '</button>';
    }

    /*     * ********************************************************************** */

    public function getCloseDialogScript() {
        return '$(\'#' . $this->getId() . 'Dialog\').dialog(\'close\');';
    }

    /*     * ********************************************************************** */

    public function getId() {
        return $this->form_id;
    }

    /*     * ********************************************************************** */

    public function getJavaScriptSuccessHandler() {

        if ($this->ajax_submit_success_handler === false)
            return 'success' . $this->form_id . 'Handler';
        else
            return $this->ajax_submit_success_handler;
    }

    /*     * ********************************************************************** */

    public function checkAjaxSubmit() {
        if ($this->isSubmitted() && $this->isAjaxSubmit()) {
            $this->displayAjaxErrors();
        }
    }

}

?>