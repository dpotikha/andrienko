<?php
// Find "$x->prop = ..." where $x has not been assigned yet in that function (or file scope).
// PHP 5 silently turned null into a stdClass ("Creating default object from empty value");
// PHP 8 throws "Attempt to assign property on null". Heuristic: first use of the variable in the scope is a property write.
// Usage: php -n find_autovivified_objects.php files.txt
$files = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$skip = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];
$hits = 0;
foreach ($files as $f) {
    $t = token_get_all(file_get_contents($f)); $n = count($t);
    $next = function ($i) use ($t, $n, $skip) { for ($i++; $i < $n && is_array($t[$i]) && in_array($t[$i][0], $skip); $i++); return $i; };
    // scopes: stack of [depthAtOpen, seenVars]; file scope at the bottom
    $stack = [[-1, []]]; $depth = 0; $pendingFn = false; $params = [];
    for ($i = 0; $i < $n; $i++) {
        $x = $t[$i];
        if (is_array($x) && $x[0] === T_FUNCTION) { $pendingFn = true; $params = []; continue; }
        if ($pendingFn && is_array($x) && $x[0] === T_VARIABLE) { $params[$x[1]] = 1; continue; }
        if ($x === ';' && $pendingFn) { $pendingFn = false; continue; } // abstract/interface method
        if ($x === '{' || (is_array($x) && in_array($x[0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES]))) {
            $depth++;
            if ($pendingFn) { $stack[] = [$depth, $params + ['$this' => 1]]; $pendingFn = false; }
            continue;
        }
        if ($x === '}') { if (count($stack) > 1 && end($stack)[0] === $depth) array_pop($stack); $depth--; continue; }
        if (!is_array($x)) continue;
        $top = count($stack) - 1;
        if (in_array($x[0], [T_GLOBAL, T_STATIC])) { // global $a, $b; static $c
            for ($j = $i + 1; $j < $n && $t[$j] !== ';'; $j++) if (is_array($t[$j]) && $t[$j][0] === T_VARIABLE) $stack[$top][1][$t[$j][1]] = 1;
            continue;
        }
        if ($x[0] !== T_VARIABLE) continue;
        $v = $x[1];
        if (isset($stack[$top][1][$v]) || $v === '$GLOBALS' || $v[1] === '_') { $stack[$top][1][$v] = 1; continue; }
        $stack[$top][1][$v] = 1;
        $j = $next($i);
        if (!(is_array($t[$j]) && $t[$j][0] === T_OBJECT_OPERATOR)) continue;
        // walk "->prop" / "->prop[...]" chain, then expect an assignment operator
        $k = $next($j); $k = $next($k);
        while ($k < $n && $t[$k] === '[') { $d = 0; for (; $k < $n; $k++) { if ($t[$k] === '[') $d++; if ($t[$k] === ']' && --$d === 0) break; } $k = $next($k); }
        $isAssign = $t[$k] === '=' || (is_array($t[$k]) && in_array($t[$k][0], [T_CONCAT_EQUAL, T_PLUS_EQUAL, T_MINUS_EQUAL]));
        if ($isAssign) { $hits++; echo "$f:{$x[2]} $v (first use in scope is a property write)\n"; }
    }
}
echo "hits=$hits\n";
