<?php
// Fix 4: $s{expr} -> $s[expr] in code context only (tokenizer keeps strings/regexes out).
// Usage: php fix4_curly_offsets.php files.txt [--dry]   (prints file:line of each change)
$dry = in_array('--dry', $argv); $total = 0;
foreach (file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $f) {
    $toks = token_get_all(file_get_contents($f)); $n = count($toks); $hit = 0; $closers = [];
    $prev = function ($i) use ($toks) { for ($i--; $i >= 0; $i--) { if (is_array($toks[$i]) && in_array($toks[$i][0], [T_WHITESPACE, T_COMMENT])) continue; return $i; } return null; };
    $stack = [];  // for each open brace: true if it's an offset brace we converted
    for ($i = 0; $i < $n; $i++) {
        $t = $toks[$i];
        if ($t === '{') {
            $p = $prev($i); $pt = $p === null ? null : $toks[$p];
            $isOff = (is_array($pt) && $pt[0] === T_VARIABLE)
                  || $pt === ']'
                  || ($pt === '}' && in_array($p, $closers, true))
                  || (is_array($pt) && $pt[0] === T_STRING && ($pp = $prev($p)) !== null && is_array($toks[$pp]) && in_array($toks[$pp][0], [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR]));
            if ($isOff) {
                $toks[$i] = '[';
                for ($k = $p; $k >= 0 && !is_array($toks[$k]); $k--);   // nearest token with a line number
                echo "  $f:" . ($k >= 0 ? $toks[$k][2] : '?') . "\n";
                $hit++;
            }
            $stack[] = $isOff;
        } elseif (is_array($t) && in_array($t[0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES])) {
            $stack[] = false;
        } elseif ($t === '}') {
            if (array_pop($stack)) { $toks[$i] = ']'; $closers[] = $i; }
        }
    }
    if ($hit) { $total += $hit; if (!$dry) { $o = ''; foreach ($toks as $t) $o .= is_array($t) ? $t[1] : $t; file_put_contents($f, $o); } }
}
echo "total=$total\n";
