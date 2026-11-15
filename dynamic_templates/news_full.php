<?php
tinclude("_head");
$d = $CurrentNode->tfields["date"];
$sqlDate = sprintf("%04d-%02d-%02d", (int)$d["year"], (int)$d["month"], (int)$d["day"]);
$older = Node::findByQuery("SELECT n.* FROM {news_item} n WHERE n.date < '" . $sqlDate . "' ORDER BY n.date DESC LIMIT 1");
$newer = Node::findByQuery("SELECT n.* FROM {news_item} n WHERE n.date > '" . $sqlDate . "' ORDER BY n.date ASC LIMIT 1");
tinclude("_article", array(
	"imgField" => "photo",
	"meta" => e($d["formatted_date"]),
	"backHref" => "/news?year=" . (int)$d["year"],
	"backLabel" => "Усі новини " . (int)$d["year"],
	"older" => $older ? $older[0] : null,
	"newer" => $newer ? $newer[0] : null,
));
tinclude("_foot");
