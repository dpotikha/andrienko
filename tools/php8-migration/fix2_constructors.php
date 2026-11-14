<?php
// Fix 2: rename PHP4-style constructors (method named like its class) to __construct.
// Only when the class has no __construct already. Usage: php fix2_constructors.php files.txt [--dry]
// Afterwards grep for explicit calls by the old name, e.g. parent::OldName( or $this->OldName(, and fix them by hand.
$dry = in_array('--dry', $argv);
$renamed = []; $skipped = [];
foreach (file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $f) {
    $toks = token_get_all(file_get_contents($f));
    $n = count($toks);
    $next = function ($i) use ($toks, $n) {   // index of next significant token
        for ($i++; $i < $n; $i++) {
            if (is_array($toks[$i]) && in_array($toks[$i][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) continue;
            return $i;
        }
        return null;
    };
    $depth = 0; $classes = []; $pending = null; $stack = [];
    for ($i = 0; $i < $n; $i++) {
        $t = $toks[$i];
        if (is_array($t) && $t[0] === T_CLASS) {
            $j = $next($i);
            if ($j !== null && is_array($toks[$j]) && $toks[$j][0] === T_STRING) {
                $pending = ['name' => $toks[$j][1], 'line' => $t[2], 'ctor' => null, 'has' => false];
            }
            continue;
        }
        if ($t === '{' || (is_array($t) && in_array($t[0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES]))) {
            $depth++;
            if ($pending && $t === '{') { $pending['depth'] = $depth; $stack[] = $pending; $pending = null; }
            continue;
        }
        if ($t === '}') {
            if ($stack && end($stack)['depth'] === $depth) $classes[] = array_pop($stack);
            $depth--;
            continue;
        }
        if (is_array($t) && $t[0] === T_FUNCTION && $stack && end($stack)['depth'] === $depth) {
            $j = $next($i);
            // "function& name": since PHP 8.1 "&" is a T_AMPERSAND_* token, not the string '&'
            if ($j !== null && ($toks[$j] === '&' || (is_array($toks[$j]) && strpos(token_name($toks[$j][0]), 'T_AMPERSAND') === 0))) $j = $next($j);
            if ($j !== null && is_array($toks[$j]) && $toks[$j][0] === T_STRING) {
                $k = count($stack) - 1;
                if (strcasecmp($toks[$j][1], '__construct') === 0) $stack[$k]['has'] = true;
                elseif (strcasecmp($toks[$j][1], $stack[$k]['name']) === 0) $stack[$k]['ctor'] = $j;
            }
        }
    }
    $changed = false;
    foreach ($classes as $c) {
        if ($c['ctor'] === null) continue;
        if ($c['has']) { $skipped[] = "$f: {$c['name']}"; continue; }
        $toks[$c['ctor']][1] = '__construct';
        $renamed[] = "$f: {$c['name']}";
        $changed = true;
    }
    if ($changed && !$dry) {
        $out = '';
        foreach ($toks as $t) $out .= is_array($t) ? $t[1] : $t;
        file_put_contents($f, $out);
    }
}
echo "RENAMED (" . count($renamed) . "):\n  " . implode("\n  ", $renamed) . "\n";
echo "SKIPPED, already has __construct (" . count($skipped) . "):\n  " . implode("\n  ", $skipped) . "\n";
