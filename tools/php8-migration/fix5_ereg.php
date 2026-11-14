<?php
// Fix 5: ereg("pat", ...) -> preg_match("~pat~", ...), eregi -> "~pat~i".
// Adds the D flag when the pattern ends with "$", so "$" keeps POSIX meaning (no match before a trailing "\n").
// Only literal patterns are converted. Dynamic patterns, patterns containing "~", and
// ereg_replace/eregi_replace/split/spliti/sql_regcase are listed for manual fixing.
// Usage: php fix5_ereg.php files.txt [--dry]
$dry = in_array('--dry', $argv); $total = 0; $manual = [];
$skip = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];
foreach (file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $f) {
    $toks = token_get_all(file_get_contents($f)); $n = count($toks); $hit = 0;
    $sig = function ($i, $dir) use ($toks, $n, $skip) {
        for ($i += $dir; $i >= 0 && $i < $n; $i += $dir) {
            if (is_array($toks[$i]) && in_array($toks[$i][0], $skip)) continue;
            return $i;
        }
        return null;
    };
    for ($i = 0; $i < $n; $i++) {
        $t = $toks[$i];
        if (!is_array($t) || $t[0] !== T_STRING) continue;
        $name = strtolower($t[1]);
        $pv = $sig($i, -1);
        if ($pv !== null && is_array($toks[$pv]) && in_array($toks[$pv][0], [T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION])) continue;
        $op = $sig($i, 1);
        if ($op === null || $toks[$op] !== '(') continue;
        if (in_array($name, ['ereg_replace', 'eregi_replace', 'split', 'spliti', 'sql_regcase'])) { $manual[] = "$f:{$t[2]} $name()"; continue; }
        if ($name !== 'ereg' && $name !== 'eregi') continue;
        $a = $sig($op, 1);
        $after = $a === null ? null : $sig($a, 1);
        if (!is_array($toks[$a]) || $toks[$a][0] !== T_CONSTANT_ENCAPSED_STRING || $toks[$after] !== ',') {
            $manual[] = "$f:{$t[2]} $name() with a non-literal pattern"; continue;
        }
        $q = $toks[$a][1][0]; $body = substr($toks[$a][1], 1, -1);
        if (strpos($body, '~') !== false) { $manual[] = "$f:{$t[2]} $name() pattern contains ~"; continue; }
        $flags = ($name === 'eregi' ? 'i' : '') . (substr($body, -1) === '$' && substr($body, -2) !== '\\$' ? 'D' : '');
        $toks[$i][1] = 'preg_match';
        $toks[$a][1] = $q . '~' . $body . '~' . $flags . $q;
        echo "  $f:{$t[2]}\n"; $hit++;
    }
    if ($hit) { $total += $hit; if (!$dry) { $o = ''; foreach ($toks as $t) $o .= is_array($t) ? $t[1] : $t; file_put_contents($f, $o); } }
}
echo "converted=$total\nMANUAL (" . count($manual) . "):\n  " . implode("\n  ", $manual) . "\n";
