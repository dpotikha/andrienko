<?php
// Translation: validate agent output against the job files and build SQL UPDATEs for the new. DB.
// usage: php tr_apply.php <day dir> <out.sql> job1 job2 ...   (job = name of a job file without .json; output is out_<job>.json)
$base = dirname(__DIR__);
[$_, $day, $sqlFile] = $argv;
$jobs = array_slice($argv, 3);
$dir = "$base/tr/$day";
$refs = json_decode(file_get_contents("$dir/refs.json"), true);

function tags($h) {
	preg_match_all('~<(/?)([a-zA-Z0-9]+)([^>]*)>~', $h, $m, PREG_SET_ORDER);
	$out = array();
	foreach ($m as $t) {
		preg_match_all('~\b(href|src)\s*=\s*"([^"]*)"~i', $t[3], $a, PREG_SET_ORDER);
		$attrs = array();
		foreach ($a as $x) $attrs[] = strtolower($x[1]) . '=' . $x[2];
		$out[] = $t[1] . strtolower($t[2]) . ($attrs ? '[' . implode(',', $attrs) . ']' : '');
	}
	return $out;
}
function sq($s) { return "'" . str_replace(array("\\", "'"), array("\\\\", "\\'"), $s) . "'"; }

$sql = "SET NAMES utf8;\n";
$problems = array(); $ok = 0; $updates = 0;
foreach ($jobs as $job) {
	$in = json_decode(file_get_contents("$dir/$job.json"), true);
	$outFile = "$dir/out_$job.json";
	if (!is_file($outFile)) { $problems[] = "$job: output file missing"; continue; }
	$raw = file_get_contents($outFile);
	$raw = preg_replace('~^\s*```(?:json)?\s*|\s*```\s*$~', '', $raw);
	$out = json_decode($raw, true);
	if (!is_array($out)) { $problems[] = "$job: output is not valid JSON (" . json_last_error_msg() . ")"; continue; }
	$byId = array();
	foreach ($out as $o) if (isset($o['id'], $o['uk'])) $byId[$o['id']] = $o['uk'];
	foreach ($in as $j) {
		$id = $j['id'];
		if (!isset($byId[$id])) { $problems[] = "$job: missing $id"; continue; }
		$uk = trim($byId[$id]);
		if ($uk === '') { $problems[] = "$job: empty $id"; continue; }
		if (tags($j['text']) !== tags($uk)) { $problems[] = "$job: HTML tags/links differ in $id"; continue; }
		if (preg_match('/[ыэъёЫЭЪЁ]/u', strip_tags($uk))) { $problems[] = "$job: Russian letters left in $id: " . mb_substr(strip_tags($uk), 0, 80); continue; }
		$ok++;
		if (strpos($id, 'long:') === 0) {
			[, $t, $nid, $f] = explode(':', $id);
			$sql .= "UPDATE `$t` SET `$f`=" . sq($uk) . " WHERE node_id=" . (int)$nid . ";\n"; $updates++;
		} else {
			foreach ($refs[$id] ?? array() as $r) {
				[$t, $key, $nid, $f] = $r;
				$sql .= "UPDATE `$t` SET `$f`=" . sq($uk) . " WHERE `$key`=" . (int)$nid . ";\n"; $updates++;
			}
		}
	}
}
file_put_contents("$base/$sqlFile", $sql);
echo "valid: $ok, SQL updates: $updates, problems: " . count($problems) . "\n" . implode("\n", array_slice($problems, 0, 60)) . "\n";
