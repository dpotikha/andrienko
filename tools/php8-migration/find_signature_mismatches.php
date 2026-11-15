<?php
// Find overriding methods whose signature PHP 8 rejects as incompatible with the parent (a fatal error at class load,
// which lint can't see when parent and child live in different files).
// Checked: child has more required params than the parent, fewer params in total, by-reference mismatch, static mismatch.
// Usage: php -n find_signature_mismatches.php files.txt
$files = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$classes = []; // lc name => [name, parent(lc), file, methods => lc => [name, line, static, params => [[byref, optional]]]]
foreach ($files as $f) {
    $t = token_get_all(file_get_contents($f)); $n = count($t);
    $depth = 0; $cls = null; $clsDepth = -1;
    for ($i = 0; $i < $n; $i++) {
        $tok = $t[$i];
        if ($tok === '{' || (is_array($tok) && in_array($tok[0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES]))) { $depth++; continue; }
        if ($tok === '}') { $depth--; if ($cls && $depth === $clsDepth) $cls = null; continue; }
        if (!is_array($tok)) continue;
        if ($tok[0] === T_CLASS) {
            $j = $i + 1; while (is_array($t[$j]) && $t[$j][0] === T_WHITESPACE) $j++;
            if (!is_array($t[$j]) || $t[$j][0] !== T_STRING) continue;
            $name = $t[$j][1]; $parent = null;
            for ($k = $j + 1; $k < $n && $t[$k] !== '{'; $k++)
                if (is_array($t[$k]) && $t[$k][0] === T_EXTENDS) {
                    $m = $k + 1; while (is_array($t[$m]) && $t[$m][0] === T_WHITESPACE) $m++;
                    $parent = strtolower($t[$m][1]);
                }
            $cls = strtolower($name); $clsDepth = $depth;
            $classes[$cls] = ['name' => $name, 'parent' => $parent, 'file' => $f, 'methods' => []];
            continue;
        }
        if ($tok[0] === T_FUNCTION && $cls && $depth === $clsDepth + 1) {
            $static = false;
            for ($b = $i - 1; $b >= 0 && (!is_array($t[$b]) ? false : in_array($t[$b][0], [T_WHITESPACE, T_STATIC, T_PUBLIC, T_PRIVATE, T_PROTECTED, T_FINAL, T_ABSTRACT, T_COMMENT, T_DOC_COMMENT])); $b--)
                if ($t[$b][0] === T_STATIC) $static = true;
            $j = $i + 1;
            while ($j < $n && !(is_array($t[$j]) && $t[$j][0] === T_STRING)) $j++;
            $mname = $t[$j][1]; $line = $t[$j][2];
            while ($t[$j] !== '(') $j++;
            $params = []; $pd = 0; $cur = null;
            for ($j++; $j < $n; $j++) {
                $x = $t[$j];
                if ($x === '(' || $x === '[') { $pd++; continue; }
                if ($x === ')' || $x === ']') { if ($pd === 0) break; $pd--; continue; }
                if ($pd > 0) continue;
                if ($x === ',') { if ($cur) $params[] = $cur; $cur = null; continue; }
                if ($cur === null) $cur = ['byref' => false, 'optional' => false, 'var' => false];
                if ($x === '&' || (is_array($x) && in_array($x[0], [T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG]))) $cur['byref'] = true;
                if ($x === '=') $cur['optional'] = true;
                if (is_array($x) && $x[0] === T_ELLIPSIS) $cur['optional'] = true;
                if (is_array($x) && $x[0] === T_VARIABLE) $cur['var'] = true;
            }
            if ($cur && $cur['var']) $params[] = $cur;
            $classes[$cls]['methods'][strtolower($mname)] = ['name' => $mname, 'line' => $line, 'static' => $static, 'params' => $params];
        }
    }
}
$out = 0;
foreach ($classes as $lc => $c) {
    foreach ($c['methods'] as $lm => $m) {
        if ($lm === '__construct' || $lm === strtolower($c['name'])) continue; // constructors are exempt
        for ($p = $c['parent'], $seen = []; $p && isset($classes[$p]) && !isset($seen[$p]); $seen[$p] = 1, $p = $classes[$p]['parent']) {
            if (!isset($classes[$p]['methods'][$lm])) continue;
            $pm = $classes[$p]['methods'][$lm];
            $req = fn($ps) => count(array_filter($ps, fn($x) => !$x['optional']));
            $why = [];
            if ($req($m['params']) > $req($pm['params'])) $why[] = "requires {$req($m['params'])} args, parent {$req($pm['params'])}";
            if (count($m['params']) < count($pm['params'])) $why[] = 'takes ' . count($m['params']) . ' params, parent ' . count($pm['params']);
            for ($k = 0; $k < min(count($m['params']), count($pm['params'])); $k++)
                if ($m['params'][$k]['byref'] !== $pm['params'][$k]['byref']) $why[] = "param " . ($k + 1) . " by-ref mismatch";
            if ($m['static'] !== $pm['static']) $why[] = 'static mismatch';
            if ($why) { $out++; echo "{$c['file']}:{$m['line']} {$c['name']}::{$m['name']} vs {$classes[$p]['name']} ({$classes[$p]['file']}:{$pm['line']}): " . implode('; ', $why) . "\n"; }
            break; // nearest ancestor that defines it
        }
    }
}
echo "mismatches=$out\n";
