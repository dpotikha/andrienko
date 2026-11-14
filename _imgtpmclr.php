<?php 

require_once("WEB-INF/prepend.php");

function clearTmpDir($tmpDir, $time_limit) {
	$subdirs = list_dir_contents_only_dirs($tmpDir);
	foreach($subdirs as $dir) {
		$files = list_dir_contents_only_files($dir);
		foreach ($files as $file) {
			$update_time = filemtime ($file);

			if ($update_time < $time_limit) {
				//echo $file.", ".date("d.m.Y h:i",$update_time)."<br>";
				if (unlink ($file)) {
					echo "deleted $file<br>";
				}
			}
		}

		$update_time = filemtime ($dir);
		if ($update_time < $time_limit) {
			//echo $dir.",        ".date("d.m.Y h:i",$update_time)."<br>";
			if (rmdir ($dir)) {
				echo "deleted $dir<br>";
			}
		}
	}
}

///////////////////////////////////////////////////

$dirRelUrl = "_newimage/tmp";

$now = time();
$yesturday = $now - 24 * 3600;

clearTmpDir(FIELD_IMGLIB_DIR."/".$dirRelUrl, $yesturday);
clearTmpDir(FIELD_IMGLIB_THUMBNAILS_DIR."/".$dirRelUrl, $yesturday);

?>