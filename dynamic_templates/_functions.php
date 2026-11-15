<?php
// Site helpers that lived in the old CMS build's WEB-INF/prepend.php (viktor-andrienko.com only).
// The gady CMS core does not have them; index.php loads this file after the core.

function externalURL($url) {
	if (strpos($url, "http://") === 0 || strpos($url, "https://") === 0) {
		return $url;
	}
	return "http://" . $url;
}

function humanLikeUrl($url) {
	if (substr($url, -1) == "/") {
		$url = substr($url, 0, -1);
	}
	if (strpos($url, "http://") === 0) {
		return substr($url, 7);
	} elseif (strpos($url, "https://") === 0) {
		return substr($url, 8);
	}
	return null; // as before: a URL without a scheme gave null
}

// first-level section of a node: /work/cinema/x -> /work
function getTopSection($node) {
	$path = $node->absolutePath;
	$k = strpos($path, '/', 1);
	if ($k !== false) {
		$path = substr($path, 0, $k);
	}
	return Node::findByPath($path);
}

// ---- redesign ("Екран") helpers ----

function e($s) {
	return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// image field value with a file behind it
function hasImg($img) {
	return is_array($img) && !empty($img['url']);
}

// image inside a fixed-ratio frame; the picture keeps its own size (never upscaled)
function frameImg($img, $alt = '', $frameClass = 'frame') {
	if (!hasImg($img)) {
		return '<span class="' . $frameClass . '"></span>';
	}
	return '<span class="' . $frameClass . '"><img src="' . e($img['url']) . '" alt="' . e($alt) . '" loading="lazy"></span>';
}

// plain text of a CMS rich-text field (for one-line announcements)
function plainText($html) {
	$t = html_entity_decode(strip_tags((string)$html), ENT_QUOTES, 'UTF-8');
	return trim(preg_replace('/\s+/u', ' ', $t));
}

// links to the node's parents, root and the node itself excluded
function crumbs($node) {
	$parents = $node->getParentList();
	array_shift($parents); // root
	$out = array();
	foreach ($parents as $p) {
		if ($p->id == $node->id) continue;
		$out[] = '<a href="' . e($p->getHref()) . '">' . e($p->name) . '</a>';
	}
	return $out ? '<nav class="crumbs" aria-label="Шлях">' . implode(' / ', $out) . '</nav>' : '';
}

// the section a project belongs to (Радіо, Кіно, ...), for labels
function sectionName($node) {
	$parent = $node->getParent();
	return $parent ? $parent->name : '';
}

// a viewer link for one photo node (class photo); the id lets old photo URLs open it via #p<id>
function photoLink($p, $group) {
	$img = $p->tfields['photo'];
	if (!hasImg($img)) return '';
	$cap = plainText($p->tfields['name_'] ?? '');
	return '<a class="js-view" id="p' . (int)$p->id . '" data-group="' . e($group) . '" data-caption="' . e($cap) . '" href="' . e($img['url']) . '">'
		. '<span class="frame"><img src="' . e($img['url']) . '" alt="' . e($cap) . '" loading="lazy"></span></a>';
}

// one row of a news / publications list
function listRow($n, $imgField, $meta) {
	$img = $n->tfields[$imgField] ?? null;
	$ann = plainText($n->tfields['announce'] ?? '');
	$h = '<article class="row">';
	if (hasImg($img)) {
		$h .= '<a href="' . e($n->getHref()) . '" tabindex="-1" aria-hidden="true">' . frameImg($img) . '</a>';
	}
	$h .= '<div class="row-text"><div class="row-meta">' . $meta . '</div>'
		. '<h2 class="h3" style="margin:4px 0 0"><a href="' . e($n->getHref()) . '">' . e($n->name) . '</a></h2>';
	if ($ann !== '') {
		$h .= '<p>' . e($ann) . '</p>';
	}
	return $h . '</div></article>';
}
