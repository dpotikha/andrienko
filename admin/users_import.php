<?
ini_set ("memory_limit", "40M");
set_time_limit (10*60*3600);

require_once("prepend.php");

$action = (string)$request->getParameter("do");

if ($action == null || $action=="") {
	$action = "_default";
}

$action($request);
function _default(&$request) {
	import_form($request);
}

function import_form(&$request) {
	global $session;
	usetemplate("users_import/form");
}

function import_file(&$request) {
	global $session;
	$file = $request->getParameter("import_file");

	$tmpFilePath = $file->tmp_name;

	$tmpFile = file($tmpFilePath);

	if (!$tmpFile) {
		$request->setAttribute("msg", "Некорректный файл");
		usetemplate("users_import/form");
		die();
	}

	$xmlFile = implode("", $tmpFile);

	$xmlReader = new XmlReader();

	$rootEl = $xmlReader->parse($xmlFile);

	$usersEls = &$rootEl->content;

	for ($i=0; $i < sizeof($usersEls); $i++) {
		$userEl = &$usersEls[$i];

		$userObj = new phpobject();

		for ($j=0; $j < sizeof($userEl->content); $j++) {
			$dataEl = &$userEl->content[$j];
			switch ($dataEl->tagname) {
				case "dogovir" :
					$userObj->contract_num = (int)$dataEl->cdata;
					break;
				case "Decoder" :
					$userObj->decoder_num = (int)$dataEl->cdata;
					break;
				case "famali" :
					$userObj->surname = $dataEl->cdata;
					break;
				case "name" :
					$userObj->name = $dataEl->cdata;
					break;
				case "balans" :
					$userObj->balance = (float)$dataEl->cdata;
					break;
				case "plata" :
					$userObj->pay = (float)$dataEl->cdata;
					break;
				case "stan" :
					$userObj->status = $dataEl->cdata;
					break;
				case "Paket" :
					$userObj->package = $dataEl->cdata;
					break;
				case "datapidkl" :
					$userObj->start_date = strtotime($dataEl->cdata);
					break;
				default:
					die("unknown tag " . $dataEl->tagname);
			}
		}

		$nodeShortname = $userObj->contract_num;
		$password = $userObj->decoder_num . date("dmY", $userObj->start_date);

		$existingNode = Node::findByPath("/profile/" . $nodeShortname);

		if ($existingNode != null) {
			$userNode = &$existingNode;
		} else {
			$userNode = Node::createWithDefaultValues0("/profile", $userObj->name, $nodeShortname, "abonent_profile");
		}

		$userNode->name = $userObj->name;
		$userNode->fields["surname"]->setValue($userObj->surname);
		$userNode->fields["contract_num"]->setValue($userObj->contract_num);
		$userNode->fields["decoder_num"]->setValue($userObj->decoder_num);
		$userNode->fields["balance"]->setValue($userObj->balance);
		$userNode->fields["pay"]->setValue($userObj->pay);
		$statusBoolean = ($userObj->status == "Викл.") ? false : true;
		$userNode->fields["status"]->setValue($statusBoolean);
		$userNode->fields["package"]->setValue($userObj->package);
		$userNode->fields["start_date"]->setValue(new Date($userObj->start_date));
		$userNode->fields["password"]->setValue($password);

		$userNode->store();
	}

	$request->setAttribute("msg", "Импорт успешно завершён");
	usetemplate("users_import/form");
}

?>