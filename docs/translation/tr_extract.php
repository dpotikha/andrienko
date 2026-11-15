<?php
// Translation, day 1: extract texts from new.sqlite into job files.
// Short fields are de-duplicated by text; long HTML fields are cleaned of the old editor's markup first.
// Every job file: [{"id": "...", "text": "..."}]; refs.json maps each id to the DB places it fills.
$base = dirname(__DIR__);
$db = new PDO("sqlite:$base/new.sqlite");
@mkdir("$base/tr", 0777, true);
@mkdir("$base/tr/day1", 0777, true);

function cyr($s) { return preg_match('/\p{Cyrillic}/u', (string)$s); }

// keep structure, drop presentation: allowed tags and attributes only, no spans/fonts, no empty paragraphs
function cleanHtml($html) {
	$html = (string)$html;
	if (trim($html) === '') return '';
	$doc = new DOMDocument();
	libxml_use_internal_errors(true);
	$doc->loadHTML('<?xml encoding="utf-8"?><div id="__root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	libxml_clear_errors();
	$allowed = array('p' => array(), 'br' => array(), 'b' => array(), 'strong' => array(), 'i' => array(), 'em' => array(), 'u' => array(),
		'a' => array('href', 'target'), 'img' => array('src', 'alt', 'width', 'height'), 'ul' => array(), 'ol' => array(), 'li' => array(),
		'h2' => array(), 'h3' => array(), 'h4' => array(), 'blockquote' => array(), 'table' => array(), 'tr' => array(), 'td' => array('colspan', 'rowspan'), 'th' => array(),
		'tbody' => array(), 'thead' => array(), 'div' => array(), 'object' => array('width', 'height', 'data', 'type'), 'param' => array('name', 'value'),
		'embed' => array('src', 'type', 'width', 'height', 'allowfullscreen', 'allowscriptaccess'), 'iframe' => array('src', 'width', 'height', 'frameborder', 'allowfullscreen'));
	$root = $doc->getElementById('__root');
	$walk = function ($node) use (&$walk, $allowed) {
		for ($c = $node->firstChild; $c; $c = $next) {
			$next = $c->nextSibling;
			if ($c->nodeType == XML_COMMENT_NODE) { $node->removeChild($c); continue; }
			if ($c->nodeType != XML_ELEMENT_NODE) continue;
			$tag = strtolower($c->nodeName);
			if ($tag == 'h1' || $tag == 'h5' || $tag == 'h6') $tag = 'h3';
			if (!isset($allowed[$tag])) {
				// unwrap (span, font, o:p, ...): keep the children
				$walk($c);
				while ($c->firstChild) $node->insertBefore($c->firstChild, $c);
				$node->removeChild($c);
				continue;
			}
			foreach (iterator_to_array($c->attributes) as $a) {
				if (!in_array(strtolower($a->nodeName), $allowed[$tag])) $c->removeAttribute($a->nodeName);
			}
			$walk($c);
		}
	};
	$walk($root);
	$out = '';
	foreach ($root->childNodes as $c) $out .= $doc->saveHTML($c);
	$out = str_replace("\xC2\xA0", '&nbsp;', $out);
	// empty paragraphs/divs and stray whitespace
	$blank = '(?:\s|&nbsp;|<br\s*/?>)';
	do { $prev = $out; $out = preg_replace('~<(p|div|b|strong|i|em|u)>' . $blank . '*</\1>~iu', '', $out); } while ($out !== $prev);
	$out = preg_replace('~(?:&nbsp;\s*){2,}~u', ' ', $out);
	$out = preg_replace("~\n{2,}~", "\n", $out);
	return trim($out);
}

$refs = array();
function addJob(&$jobs, &$refs, $id, $text, $place) {
	if (!isset($jobs[$id])) $jobs[$id] = array('id' => $id, 'text' => $text);
	$refs[$id][] = $place;
}

// 1. titles (node names), de-duplicated
$names = array();
foreach ($db->query("select id, name from dbm_nodes") as $r) {
	if (!cyr($r['name'])) continue;
	addJob($names, $refs, 'name:' . md5($r['name']), $r['name'], array('dbm_nodes', 'id', $r['id'], 'name'));
}
// 2. photo captions, de-duplicated
$caps = array();
foreach ($db->query("select node_id, name_ from dbm_nfv_photo") as $r) {
	if (!cyr($r['name_'])) continue;
	addJob($caps, $refs, 'cap:' . md5($r['name_']), $r['name_'], array('dbm_nfv_photo', 'node_id', $r['node_id'], 'name_'));
}
// 3. short fields: announcements, article titles, filmography fields
$short = array();
$shortFields = array(
	'dbm_nfv_news_item' => array('announce'), 'dbm_nfv_pub' => array('announce', 'author', 'source'),
	'dbm_nfv_friend' => array('announce', 'article_name'), 'dbm_nfv_movie' => array('studio', 'position', 'roles', 'series'),
);
foreach ($shortFields as $t => $fields) {
	foreach ($db->query("select * from $t") as $r) {
		foreach ($fields as $f) {
			$v = (string)$r[$f];
			if (!cyr($v)) continue;
			$v = strpos($v, '<') !== false ? cleanHtml($v) : trim($v);
			if ($v === '') continue;
			addJob($short, $refs, 'short:' . md5($v), $v, array($t, 'node_id', $r['node_id'], $f, 'clean'));
		}
	}
}
// 4. review sample: the 10 latest news, one project, one friend, one publication (full texts)
$sample = array();
foreach ($db->query("select node_id, text from dbm_nfv_news_item order by date desc limit 10") as $r) {
	$sample[] = array('id' => 'long:dbm_nfv_news_item:' . $r['node_id'] . ':text', 'text' => cleanHtml($r['text']));
}
foreach (array(array('dbm_nfv_project', 1471, 'text'), array('dbm_nfv_friend', 2293, 'article_text')) as $x) {
	$v = $db->query("select {$x[2]} from {$x[0]} where node_id='{$x[1]}'")->fetchColumn();
	$sample[] = array('id' => "long:{$x[0]}:{$x[1]}:{$x[2]}", 'text' => cleanHtml($v));
}
$pub = $db->query("select node_id, text from dbm_nfv_pub where length(text) between 3000 and 9000 order by date desc limit 1")->fetch(PDO::FETCH_ASSOC);
$sample[] = array('id' => 'long:dbm_nfv_pub:' . $pub['node_id'] . ':text', 'text' => cleanHtml($pub['text']));
$sample = array_values(array_filter($sample, function ($j) { return $j['text'] !== ''; }));

// glossary input: titles of projects, films, friends, sections (the names most likely to repeat)
$gl = array();
foreach ($db->query("select n.name, c.name as cls from dbm_nodes n join dbm_classes c on c.id = n.class_id where c.name in ('Проект','Фильм','Друг - партнер','Раздел деятельности','Раздел','Топ-раздел')") as $r) {
	if (cyr($r['name'])) $gl[trim($r['name'])] = $r['cls'];
}

$w = function ($file, $data) use ($base) {
	file_put_contents("$base/tr/day1/$file", json_encode(array_values($data), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
};
// split titles in two halves for two agents
$nameList = array_values($names);
$half = intdiv(count($nameList), 2);
$w('names_a.json', array_slice($nameList, 0, $half));
$w('names_b.json', array_slice($nameList, $half));
$w('captions.json', $caps);
$w('short.json', $short);
$w('sample.json', $sample);
file_put_contents("$base/tr/day1/glossary_input.json", json_encode($gl, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
file_put_contents("$base/tr/day1/refs.json", json_encode($refs, JSON_UNESCAPED_UNICODE));

$len = function ($a) { $n = 0; foreach ($a as $j) $n += mb_strlen($j['text']); return $n; };
printf("names %d (%d chars), captions %d (%d), short %d (%d), sample %d (%d), glossary terms %d\n",
	count($names), $len($names), count($caps), $len($caps), count($short), $len($short), count($sample), $len($sample), count($gl));
