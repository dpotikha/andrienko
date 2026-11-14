<?

$_settingsNode = &Node::findByPath("/_site_settings");
$request->setAttribute("SettingsNode", $_settingsNode);

define("ADMIN_EMAIL", $_settingsNode->tfields["admin_email"]);
define("ADMIN_EMAIL_FROM", $_settingsNode->tfields["admin_email_from"]);
//include("_translations_" . SITE_LOCALE . ".php");
define("LOGGED_ABONENT_SESSION_ATTR", "_LOGGED_ABONENT_");

function localmsg($key) {
	return $GLOBALS["LOCAL_MESSAGES"][$key];
}

function nodeName(&$node) {
	return (SITE_LOCALE == 'ru' ? $node->name : $node->tfields["name_" . SITE_LOCALE]);
}

function highLightKeywords($str, $keywords) {
	foreach ($keywords as $keyword) {
		$keyword = trim($keyword);
		if (strlen($keyword) == 0) continue;
		$upKeyword = rucfirst ($keyword);
		$str = str_replace(" ".$keyword, " <span>".$keyword."</span>", $str);
		$str = str_replace($keyword." ", "<span>".$keyword."</span> ", $str);
		$str = str_replace(" ".$upKeyword, " <span>".$upKeyword."</span>", $str);
		$str = str_replace($upKeyword." ", "<span>".$upKeyword."</span> ", $str);
	}

	return $str;
}


function _draw_paging_link($link) {
	if ($link->index != 0) {
		$str = '';
	}

 	switch($link->type) {
		case "prev":            
        	$str .= '<td><a href="' . $link->href . '">&lt;</a></td>';
			break;
		case "next":
        	$str .= '<td><a href="' . $link->href . '">&gt;</a></td>';
			break;
		case "link":
        	$str .= '<td><a href="' . $link->href . '">' . ($link->index + 1) . '</a></td>';
			break;
		default:
            $str .= "<td>" . ($link->index + 1) . "</td>";
			break;
	}

	return $str;
}

$GLOBALS["months_names"] = array(
	1 => "Январь",
	"Февраль",
	"Март",
	"Апрель",
	"Май",
	"Июнь",
	"Июль",
	"Август",
	"Сентябрь",
	"Октябрь",
	"Ноябрь",
	"Декабрь"
);
$GLOBALS["months_names_genitive"] = array(
	1 => "Января",
	"Февраля",
	"Марта",
	"Апреля",
	"Мая",
	"Июня",
	"Июля",
	"Августа",
	"Сентября",
	"Октября",
	"Ноября",
	"Декабря"
);

function getFormattedDate($date = ""){
	if (empty($date)) return "";
	$date = new Date($date);
	return $date->getMonthName().' '.$date->getYear();
}
?>