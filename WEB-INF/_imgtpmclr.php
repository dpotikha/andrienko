<?php

$dirRelUrl = "_newimage/tmp";
$yesturday = time() - 24 * 3600;
directoryClass::rmdir_only_last_files(FIELD_IMGLIB_DIR."/".$dirRelUrl,$yesturday);
directoryClass::rmdir_only_last_files(FIELD_IMGLIB_THUMBNAILS_DIR."/".$dirRelUrl,$yesturday);

?>