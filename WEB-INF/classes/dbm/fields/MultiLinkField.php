<?php

require_once ("dbm/fields/Field.php");

DEFINE("MULTILINKFIELD_IDS_DELIM", ",");

class MultiLinkField extends StringField {
	function setValue($o) {
		global $AdminTrnsl;
		if (is_object($o) && strtolower(get_class($o)) == 'httprequest') {
			$rValues = $o->getParameter($this->fieldDefShortname);
			if ($rValues == null) {
				$rValues = array();
			}
		} elseif(is_string($o)) {
			$this->value = $o;
			return null;
		}

		$fieldDef = $this->getFieldDef();
		$allowed_classes = $fieldDef->getParameterValue("allowed_classes");
		$root_node_path = $fieldDef->getParameterValue("root_node_path");

		$verror = null;
		do {
			if (sizeof($rValues) == 0 && $fieldDef->required) {
				$verror = $AdminTrnsl["RequiredField"];
				break;
			}

			$values = array();
//			print_r($rValues);
			foreach($rValues as $nodeId) {
				$nodeId = (int)$nodeId;
				$values []= $nodeId;
				$linkedNode = Node::findById($nodeId);


				$rootNode = Node::findByPath($root_node_path);
//				
//				print_r($linkedNode);
				$linkedNodeClass = NodeClass::findById($lindedNode->nodeClassId);
				$classname = $linkedNodeClass->shortname;

				
			}
		} while(false);
		//print_r($values);
		$this->value = implode(MULTILINKFIELD_IDS_DELIM, $values);
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

		$params["linked_node_ids"] = $this->getNodeIds();
		//$params["linked_nodes"] = $this->getLinkedNodes();

		return $params;
	}

	static function getDefaultValue($fd) {
		return "";
	}

	function getJSPControlName() {
		$fieldDef = $this->getFieldDef();
		$ctype = $fieldDef->getParameterValue("control_type");
		if ($ctype == null || $ctype=="") {
			$ctype = "checkboxes";
		}

		$class = strtolower(get_class($this));
		$cname = $class . "_" . $ctype;
		return $cname;
	}

	function getLinkedNodes() {
		$nodeIds = $this->getNodeIds();
		$nodes = array();

		foreach ($nodeIds as $nodeId) {
			$linkedNode = Node::findById($nodeId);

			if ($linkedNode != null) {
				$nodes []= $linkedNode;
			}
		}

		return $nodes;
	}

	function getNodeIds() {
		$nodeIds = explode(MULTILINKFIELD_IDS_DELIM, $this->value);

		return $nodeIds;
	}

	function getValueForTemplate() {
		$nodeIds = $this->getNodeIds();

		return $nodeIds;
	}

	function getHtmlVisualValue() {
		global $AdminTrnsl;
		
		$nodes = $this->getLinkedNodes();
		$htmlStrings = array();		

		for ($i = 0; $i < sizeof($nodes); $i++) {
			$node = $nodes[$i];
			$href = "/admin/nodes.php?do=main&id=" . $node->id;
			$displayedName = $node->name;
			$htmlString = "<nobr><a href=\"" . $href . "\">" . $displayedName . "</a></nobr>";
			$htmlStrings []= $htmlString;
		}

		$html = implode(", ", $htmlStrings);

		return $html;
	}
}

?>