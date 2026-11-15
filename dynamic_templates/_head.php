<?php
// page header for the "Екран" design; optional tinclude params: $pageTitle, $extraHead
$path = $CurrentNode->absolutePath;
$navItems = array(
	array('/about', 'Про нього'),
	array('/work', 'Діяльність'),
	array('/about/filmography', 'Фільмографія'),
	array('/news', 'Новини'),
	array('/friends', 'Друзі'),
	array('/contacts', 'Контакти'),
);
$activeNav = '';
foreach ($navItems as $it) {
	if (strpos($path . '/', $it[0] . '/') === 0 && strlen($it[0]) > strlen($activeNav)) {
		$activeNav = $it[0];
	}
}
$title = $CurrentNode->isRoot() ? 'Віктор Андрієнко' : (isset($pageTitle) ? $pageTitle : $CurrentNode->name) . ' — Віктор Андрієнко';
$noindex = strpos($_SERVER['HTTP_HOST'] ?? '', 'new.') === 0; // the test copy on new.viktor-andrienko.com must not be indexed
?><!DOCTYPE html>
<html lang="uk" data-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<?php if ($noindex) { ?><meta name="robots" content="noindex, nofollow">
<?php } ?>
<script>try{var t=localStorage.getItem('theme');if(t==='light'||t==='night'||t==='dark')document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;700&amp;family=IBM+Plex+Mono:wght@400;500&amp;family=Inter:wght@400;500&amp;display=swap">
<link rel="stylesheet" href="/assets/site.css?v=1">
<?= isset($extraHead) ? $extraHead : '' ?>
</head>
<body>
<header class="site-header">
	<div class="wrap">
		<a class="logo" href="/" aria-label="Віктор Андрієнко — на головну"><img src="/_img/logo.gif" width="370" height="100" alt="Підпис Віктора Андрієнка"></a>
		<nav class="nav" aria-label="Головне меню">
<?php foreach ($navItems as $it) { ?>
			<a href="<?= $it[0] ?>"<?= $it[0] == $activeNav ? ' aria-current="page"' : '' ?>><?= $it[1] ?></a>
<?php } ?>
		</nav>
	</div>
</header>
<main>
