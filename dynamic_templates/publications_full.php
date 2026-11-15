<?php
tinclude("_head");
$meta = array(e($CurrentNode->tfields["date"]["formatted_date"]));
if (strlen(trim($CurrentNode->tfields["author"] ?? ''))) $meta[] = e($CurrentNode->tfields["author"]);
if (strlen(trim($CurrentNode->tfields["source"] ?? ''))) {
	$meta[] = strlen(trim($CurrentNode->tfields["source_url"] ?? ''))
		? '<a href="' . e(externalURL($CurrentNode->tfields["source_url"])) . '">' . e($CurrentNode->tfields["source"]) . '</a>'
		: e($CurrentNode->tfields["source"]);
}
$parent = $CurrentNode->getParent();
tinclude("_article", array(
	"imgField" => "image",
	"meta" => implode(' · ', $meta),
	"backHref" => $parent->getHref(),
	"backLabel" => $parent->name,
	"older" => null,
	"newer" => null,
));
tinclude("_foot");
