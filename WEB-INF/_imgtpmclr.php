<?php

$dirRelUrl = "/s/tmp";
$yesturday = time() - 24 * 3600;
directoryClass::rmdir_only_last_files($_SERVER['DOCUMENT_ROOT'].$dirRelUrl,$yesturday);

?>