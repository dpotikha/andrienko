<?php
// Fix 3: "=& new" / "= &new" -> "= new". Removes the "&" token between "=" and T_NEW.
// Occurrences inside comments are not touched. Usage: php fix3_ref_new.php files.txt [--dry]
$dry = in_array('--dry', $argv); $total = 0; $other = [];
foreach (file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $f) {
    $toks = token_get_all(file_get_contents($f)); $n = count($toks); $hit = 0;
    $isAmp = fn($t) => $t === '&' || (is_array($t) && in_array(token_name($t[0]), ['T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG', 'T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG']));
    $sig = function ($i, $dir) use ($toks, $n) { for ($i += $dir; $i >= 0 && $i < $n; $i += $dir) { if (is_array($toks[$i]) && $toks[$i][0] === T_WHITESPACE) continue; return $i; } return null; };
    for ($i = 0; $i < $n; $i++) {
        if (!$isAmp($toks[$i])) continue;
        $nx = $sig($i, 1); if ($nx === null || !is_array($toks[$nx]) || $toks[$nx][0] !== T_NEW) continue;
        $pv = $sig($i, -1);
        if ($toks[$pv] === '=') {
            // drop "&"; only "=&new" (no whitespace on either side) needs a space put back
            $toks[$i] = ($pv === $i - 1 && $nx === $i + 1) ? ' ' : '';
            $hit++;
        } else $other[] = "$f:" . (is_array($toks[$nx]) ? $toks[$nx][2] : '?');
    }
    if ($hit) { $total += $hit; echo "$hit\t$f\n"; if (!$dry) { $o = ''; foreach ($toks as $t) $o .= is_array($t) ? $t[1] : $t; file_put_contents($f, $o); } }
}
echo "total=$total\nother &new (not after =): " . implode(', ', $other) . "\n";
