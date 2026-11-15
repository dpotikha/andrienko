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
