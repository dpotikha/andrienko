<?php
// single-photo pages are gone: the photo opens in the viewer on its parent page
$parent = $CurrentNode->getParent();
header("Location: " . $parent->getHref() . "#p" . (int)$CurrentNode->id, true, 301);
exit;
