<?php
// a filmography entry has no content of its own: send visitors to the page it points to, else to the filmography
$linked = isset($CurrentNode->fields["link"]) ? $CurrentNode->fields["link"]->getLinkedNode() : null;
header("Location: " . ($linked ? $linked->getHref() : $CurrentNode->getParent()->getHref()), true, 301);
exit;
