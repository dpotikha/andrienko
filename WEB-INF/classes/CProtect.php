<?php

//Grigoriy Chorniy
class CProtect {

	/**********************************************************************************************************************************************/

	public static function int($val) {
		return (int)$val;
	}

	/**********************************************************************************************************************************************/

	public static function float($val) {
		return (float)$val;
	}

	/**********************************************************************************************************************************************/

	public static function real($val) {
		return (float)$val;
	}

	/**********************************************************************************************************************************************/

	public static function double($val) {
		return (double)$val;
	}

	/**********************************************************************************************************************************************/

	public static function bool($val) {
		return (bool)$val;
	}

	/**********************************************************************************************************************************************/

	public static function _array($val) {
		return ($val==NULL) ? array() : (array)$val;
	}

	/**********************************************************************************************************************************************/

	public static function _object($val) {
		return (object)$val;
	}

	/**********************************************************************************************************************************************/

	public static function string($val,$maxlength=255) {
		$CClearText=CClearText::getInstance();
		$CClearText->setMaxLength($maxlength);
		$CClearText->setCutText(true,true);
		$CClearText->setCutShortnameText(false);
		$CClearText->setAllowedTags('');
		$CClearText->setAllowedProperties('');
		$CClearText->setAllowedAttributes('');
		return $CClearText->prepare(trim($val));
	}

	/**********************************************************************************************************************************************/
	public static function cut_shortname($val,$maxlength=255) {
		$CClearText=CClearText::getInstance();
		$CClearText->setMaxLength($maxlength);
		$CClearText->setCutText(false,true);
		$CClearText->setCutShortnameText(true);
		$CClearText->setAllowedTags('');
		$CClearText->setAllowedProperties('');
		$CClearText->setAllowedAttributes('');
		return $CClearText->prepare(trim($val,'-_'));
	}

	/**********************************************************************************************************************************************/

	public static function short_html($val,$maxlength=512) {
		$CClearText=CClearText::getInstance();
		$CClearText->setMaxLength($maxlength);
		$CClearText->setCutText(true,true);
                $CClearText->setCutShortnameText(false);
		$CClearText->setAllowedTags('<p><b><a><i><em><li><ul><ol><u><strong><br><div><span>');
		$CClearText->setAllowedProperties('');
		$CClearText->setAllowedAttributes('');
		return $CClearText->prepare(trim($val));
	}

	/**********************************************************************************************************************************************/

	public static function tiny_html($val,$maxlength=512) {
		$CClearText=CClearText::getInstance();
		$CClearText->setMaxLength($maxlength);
		$CClearText->setCutText(true,true);
                $CClearText->setCutShortnameText(false);
		$CClearText->setAllowedTags('<p><b><a><i><em><li><ul><ol><u><strong><br><div><span>');
		$CClearText->setAllowedProperties('text-align');
		$CClearText->setAllowedAttributes('style');
		return $CClearText->prepare(trim($val));
	}

	/**********************************************************************************************************************************************/

	public static function html($val,$maxlength=65000) {
		$CClearText=CClearText::getInstance();
		$CClearText->setMaxLength($maxlength);
		$CClearText->setCutText(true,true);
                $CClearText->setCutShortnameText(false);
		$CClearText->setAllowedTags('<p><b><a><i><em><li><ul><ol><u><strong><br><div><span>');
		$CClearText->setAllowedProperties('');
		$CClearText->setAllowedAttributes('');
		return $CClearText->prepare(trim($val));
	}

	/**********************************************************************************************************************************************/
}

?>