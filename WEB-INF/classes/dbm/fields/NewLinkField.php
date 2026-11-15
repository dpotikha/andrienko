<?php

require_once ("dbm/fields/IntegerField.php");

class NewLinkField extends IntegerField {
	var $linkedNode = null;
	var $lazyLoading = false;
	var $alreadyTriedToLoad = false;

	function __construct() {}

	function init($fieldRS, $tableAlias, $shortname) {
		$fieldDef = $this->getFieldDef();
		$lazyLoading = $fieldDef->getParameterValue("lazy_loading");

		if ($lazyLoading == "1" || strtolower(get_class($fieldRS)) != "nodequeryresultset") {
			if ($tableAlias != "") {
				$fieldNamesPrefix = $tableAlias . "___";
			} else {
				$fieldNamesPrefix = "";
			}

			$fieldType = $this->getType();

			$falias = $fieldNamesPrefix . $shortname;
			$rsGetMethod = "get" . $fieldType->rsType;
			$dbValue = $fieldRS->$rsGetMethod($falias);

			$this->value = $dbValue;
			$this->lazyLoading = true;
		} else {

			$this->linkedNode = $fieldRS->getNode($tableAlias . "___" . $shortname);
			$this->value = (int)$this->linkedNode->id;
			$this->lazyLoading = false;
		}
	}

	function setValue($o) {
		global $AdminTrnsl;
		if (is_object($o) && strtolower(get_class($o)) == 'httprequest') {
			$rValue = $o->getParameter($this->fieldDefShortname);
			if(!is_numeric($rValue)) {
				$n=Node::findByPath($rValue);
				$rValue=$n->id;
			}
		} elseif(is_int($o)) {
			$rValue = $o;
		}
		$fieldDef = $this->getFieldDef();

		$allowed_classes = $fieldDef->getParameterValue("allowed_classes");
		$root_node_path = str_replace("{LANG}","ru",$fieldDef->getParameterValue("root_node_path"));

		$verror = null;
		$this->value = "";
		do {
			if ($rValue != 0) {
				$rootNode = Node::findByPath($root_node_path);
				//				echo $root_node_path;

				if ($rootNode == null) {
					$verror = $AdminTrnsl["BadRootNodePath"];
					break;
				}
				$linkedNode = Node::findById($rValue);
				if ($linkedNode != null && $this->nodeId != -1 && $linkedNode->id == $this->value) {
					$verror = $AdminTrnsl["CantCreateLinkToItself"];
					break;
				}
				if ($linkedNode == null) {
					$verror = $AdminTrnsl["SpecifiedPathIsNotValid"];
					break;
				}
				if (!$linkedNode->isSubNodeOf($rootNode)) {
					$verror = $AdminTrnsl["SpecifiedPathIsNotGranted"];
					break;
				}

				$linkedNodeClass = $linkedNode->getNodeClass();
				$classname = $linkedNodeClass->shortname;
				if ($allowed_classes != "*" && strpos("," . $allowed_classes . ",", "," . $classname . ",") === false) {
					$verror = $AdminTrnsl["SpecifiedPathIsNotValidClass"];
					break;
				}

				$this->value = $linkedNode->id;
				$this->linkedNode = $linkedNode;

			} else if($fieldDef->required) {
					$verror = $AdminTrnsl["RequiredField"];
					break;
				}
		} while(false);

		return $verror;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();

		$fieldDef = $this->getFieldDef();
		$fieldType = $fieldDef->getFieldType();
		$classParams = $fieldType->getParametersNames();

		foreach ($classParams as $fieldParameterShortname) {
			$params[$fieldParameterShortname] = $fieldDef->getParameterValue($fieldParameterShortname);
		}

		$linkedNode = $this->getLinkedNode();
		if ($linkedNode != null) {
			$params["linked_node_path"] =  $this->linkedNode->absolutePath;
			$params["linked_node_id"] =  $this->linkedNode->id;

			$href = "/admin/nodes.php?do=main&id=" . $this->linkedNode->id;
			$displayedName = $this->linkedNode->name;

			$params["linked_node_name"] = $displayedName;
			$params["linked_node_admin_href"] = $href;
		}

		return $params;
	}

	function getValueForTemplate() {
		return $this->linkedNode;
	}

	function getLinkedNode() {
		if ($this->lazyLoading && ($this->value != 0 && $this->alreadyTriedToLoad == false)) {
			$this->linkedNode =  Node::findById($this->value);
			$this->alreadyTriedToLoad == true;
		}
		return $this->linkedNode;
	}

	static function getDefaultValue($fd) {
		return 0;
	}

	function getJSPControlName() {
		$fieldDef = $this->getFieldDef();
		$ctype = $fieldDef->getParameterValue("control_type");
		if ($ctype == null || $ctype=="") {
			$ctype = "popup";
		}

		$class = strtolower(get_class($this));
		$cname = $class . "_" . $ctype;
		return $cname;
	}

	function getHtmlVisualValue() {
		global $AdminTrnsl;
		$this->getLinkedNode();
		if ($this->value) {
			$href = "/admin/nodes.php?do=main&id=" . $this->linkedNode->id;
			$displayedName = $this->linkedNode->name;

			return "<nobr><a href=\"" . $href . "\">" . $displayedName . "</a></nobr>";
		} else {
			return "&nbsp;";
		}
	}
}

?>