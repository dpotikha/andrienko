<?php
// single video pages are gone: the file is listed on its project page
header("Location: " . $CurrentNode->getParent()->getHref(), true, 301);
exit;
