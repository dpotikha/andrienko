<?php
// Find ClassName::method() calls to methods that are not declared static (fatal in PHP 8).
// Reports, per method: whether it uses $this (then it cannot simply be made static) and the call sites.
// Usage: php find_static_calls.php files.txt [--fix]
//   --fix adds "static" to every reported method that does not use $this.
$fix = in_array('--fix', $argv);
$files = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$skip = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];
$methods = [];   // lc(class)::lc(method) => [file, class, method, static, usesThis, tokIndex]
$calls = [];     // list of [lc(class), lc(method), file:line]
$parents = [];   // lc(class) => lc(parent)
$dynamic = [];   // array($expr, 'method') callbacks whose class is only known at runtime: [lc(method), file:line, callerFn]
$tokensByFile = [];

foreach ($files as $f) {
    $toks = token_get_all(file_get_contents($f)); $n = count($toks); $tokensByFile[$f] = $toks;
    $sig = function ($i, $dir) use ($toks, $n, $skip) {
        for ($i += $dir; $i >= 0 && $i < $n; $i += $dir) {
            if (is_array($toks[$i]) && in_array($toks[$i][0], $skip)) continue;
            return $i;
        }
        return null;
    };
    $depth = 0; $class = null; $classDepth = -1; $fn = null; $fnDepth = -1;
    for ($i = 0; $i < $n; $i++) {
        $t = $toks[$i];
        if (is_array($t) && $t[0] === T_CLASS) {
            $j = $sig($i, 1);
            if ($j !== null && is_array($toks[$j]) && $toks[$j][0] === T_STRING) {
                $class = $toks[$j][1]; $classDepth = $depth;
                $k = $sig($j, 1);
                if ($k !== null && is_array($toks[$k]) && $toks[$k][0] === T_EXTENDS) {
                    $p = $sig($k, 1); $parents[strtolower($class)] = strtolower($toks[$p][1]);
                }
            }
            continue;
        }
        if ($t === '{' || (is_array($t) && in_array($t[0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES]))) { $depth++; continue; }
        if ($t === '}') {
            $depth--;
            if ($fn !== null && $depth === $fnDepth) $fn = null;
            if ($class !== null && $depth === $classDepth) $class = null;
            continue;
        }
        if (is_array($t) && $t[0] === T_FUNCTION && $class !== null && $fn === null && $depth === $classDepth + 1) {
            // "function& name": since PHP 8.1 "&" is a T_AMPERSAND_* token, not the string '&'
            $j = $sig($i, 1);
            if ($toks[$j] === '&' || (is_array($toks[$j]) && strpos(token_name($toks[$j][0]), 'T_AMPERSAND') === 0)) $j = $sig($j, 1);
            if (is_array($toks[$j]) && $toks[$j][0] === T_STRING) {
                $static = false;
                for ($k = $sig($i, -1); $k !== null && is_array($toks[$k]) && in_array($toks[$k][0], [T_STATIC, T_PUBLIC, T_PRIVATE, T_PROTECTED, T_ABSTRACT, T_FINAL]); $k = $sig($k, -1)) {
                    if ($toks[$k][0] === T_STATIC) $static = true;
                }
                $key = strtolower($class) . '::' . strtolower($toks[$j][1]);
                $methods[$key] = ['file' => $f, 'class' => $class, 'method' => $toks[$j][1], 'static' => $static, 'usesThis' => false, 'tok' => $i, 'line' => $t[2]];
                $fn = $key; $fnDepth = $depth;
            }
            continue;
        }
        if ($fn !== null && is_array($t) && $t[0] === T_VARIABLE && $t[1] === '$this') $methods[$fn]['usesThis'] = true;
        // callbacks: 'Class::method', array('Class', 'method') / ['Class', 'method'], array($expr, 'method')
        if (is_array($t) && $t[0] === T_CONSTANT_ENCAPSED_STRING) {
            $s = substr($t[1], 1, -1);
            if (preg_match('/^([A-Za-z_]\w*)::([A-Za-z_]\w*)$/', $s, $mm)) {
                $calls[] = [strtolower($mm[1]), strtolower($mm[2]), "$f:{$t[2]} (callback string)", $fn];
                continue;
            }
            if (!preg_match('/^[A-Za-z_]\w*$/', $s)) continue;
            $comma = $sig($i, -1);                                      // token before the method name
            $close = $sig($i, 1);
            if ($comma === null || $toks[$comma] !== ',' || $close === null || !in_array($toks[$close], [')', ']'], true)) continue;
            $q = $sig($comma, -1);
            $opener = $q === null ? null : $sig($q, -1);
            $isArrayOpen = function ($o) use ($toks, $sig) {
                if ($o === null) return false;
                if ($toks[$o] === '[') return true;
                if ($toks[$o] === '(') { $a = $sig($o, -1); return $a !== null && is_array($toks[$a]) && $toks[$a][0] === T_ARRAY; }
                return false;
            };
            if (is_array($toks[$q]) && $toks[$q][0] === T_CONSTANT_ENCAPSED_STRING && $isArrayOpen($opener)) {
                $calls[] = [strtolower(substr($toks[$q][1], 1, -1)), strtolower($s), "$f:{$t[2]} (array callback)", $fn];
                continue;
            }
            // array($expr, 'method') with a dynamic class: walk back to the opener over a simple expression
            $k = $q; $ok = false; $exprStart = null;
            for ($guard = 0; $k !== null && $guard < 12; $guard++) {
                $tk = $toks[$k];
                $simple = (is_array($tk) && in_array($tk[0], [T_VARIABLE, T_STRING, T_OBJECT_OPERATOR, T_CONSTANT_ENCAPSED_STRING, T_LNUMBER])) || $tk === ']';
                if (!$simple) break;
                if (is_array($tk) && $tk[0] === T_VARIABLE) {
                    $prev = $sig($k, -1);
                    if ($isArrayOpen($prev)) { $ok = true; $exprStart = $k; break; }
                }
                $k = $sig($k, -1);
                // step over an index: $a['x'] -> skip back to the matching '['
                if ($k !== null && $toks[$k] === '[') $k = $sig($k, -1);
            }
            if ($ok && !(is_array($toks[$q]) && $toks[$q][0] === T_VARIABLE && $toks[$q][1] === '$this' && $exprStart === $q)) {
                $dynamic[] = [strtolower($s), "$f:{$t[2]}", $fn];
            }
        }
        if (is_array($t) && $t[0] === T_DOUBLE_COLON) {
            $p = $sig($i, -1); $m = $sig($i, 1); $o = $m === null ? null : $sig($m, 1);
            if (is_array($toks[$p]) && $toks[$p][0] === T_STRING && is_array($toks[$m]) && $toks[$m][0] === T_STRING && $o !== null && $toks[$o] === '(') {
                $cls = strtolower($toks[$p][1]);
                if (in_array($cls, ['parent'])) continue;                     // parent:: from instance context is fine
                if (in_array($cls, ['self', 'static'])) $cls = $class ? strtolower($class) : $cls;
                $calls[] = [$cls, strtolower($toks[$m][1]), "$f:{$t[2]}", $fn];
            }
        }
    }
}

// resolve a method through the class hierarchy
$resolve = function ($cls, $m) use ($methods, $parents) {
    for ($c = $cls, $guard = 0; $c !== null && $guard < 20; $c = $parents[$c] ?? null, $guard++) {
        if (isset($methods["$c::$m"])) return "$c::$m";
    }
    return null;
};

$bad = [];
foreach ($calls as [$cls, $m, $where, $callerFn]) {
    $key = $resolve($cls, $m);
    if ($key === null || $methods[$key]['static']) continue;
    // Ancestor::x() or self::x() from a non-static method of the same class or a subclass runs on $this: legal
    if ($callerFn !== null && !$methods[$callerFn]['static']) {
        $target = explode('::', $key)[0];
        for ($c = explode('::', $callerFn)[0], $g = 0; $c !== null && $g < 20; $c = $parents[$c] ?? null, $g++) {
            if ($c === $target) continue 2;
        }
    }
    $bad[$key][] = $where;
}
// Making a method static is only safe if no ancestor or descendant defines the same method non-static
$conflict = function ($key) use ($methods, $parents) {
    [$cls, $m] = explode('::', $key);
    for ($c = $parents[$cls] ?? null, $g = 0; $c !== null && $g < 20; $c = $parents[$c] ?? null, $g++) {
        if (isset($methods["$c::$m"]) && !$methods["$c::$m"]['static']) return "ancestor $c";
    }
    foreach ($parents as $child => $_) {
        for ($c = $parents[$child] ?? null, $g = 0; $c !== null && $g < 20; $c = $parents[$c] ?? null, $g++) {
            if ($c === $cls && isset($methods["$child::$m"]) && !$methods["$child::$m"]['static']) return "subclass $child";
        }
    }
    return null;
};

ksort($bad);
$fixable = 0;
foreach ($bad as $key => $sites) {
    $md = $methods[$key];
    $why = $md['usesThis'] ? 'uses $this' : $conflict($key);
    if ($why !== null) $methods[$key]['usesThis'] = true;     // excluded from --fix
    $tag = $why !== null ? "MANUAL: $why" : 'fixable';
    if ($why === null) $fixable++;
    echo "{$md['class']}::{$md['method']}  [{$tag}]  defined {$md['file']}:{$md['line']}\n";
    foreach (array_unique($sites) as $s) echo "    called at $s\n";
}
echo "methods=" . count($bad) . " fixable=$fixable manual=" . (count($bad) - $fixable) . "\n";

// array($expr, 'method'): the class is a runtime value. If $expr holds a class NAME (string), a non-static
// target is fatal in PHP 8; if it holds an object, it is fine. Listed for manual review, never auto-fixed.
$seen = [];
foreach ($dynamic as [$m, $where, $callerFn]) {
    $nonStatic = [];
    foreach ($methods as $key => $md) {
        if (substr($key, strpos($key, '::') + 2) === $m && !$md['static']) $nonStatic[] = $md['class'];
    }
    if (!$nonStatic || isset($seen["$m@$where"])) continue;
    $seen["$m@$where"] = true;
    echo "REVIEW dynamic callback ->$m() at $where; non-static in: " . implode(', ', array_slice($nonStatic, 0, 8)) . (count($nonStatic) > 8 ? ' ...' : '') . "\n";
}

if ($fix) {
    $byFile = [];
    foreach ($bad as $key => $_) if (!$methods[$key]['usesThis']) $byFile[$methods[$key]['file']][] = $methods[$key]['tok'];
    foreach ($byFile as $f => $idxs) {
        $toks = $tokensByFile[$f];
        foreach ($idxs as $i) $toks[$i] = [T_FUNCTION, 'static function', 0];
        $o = ''; foreach ($toks as $t) $o .= is_array($t) ? $t[1] : $t;
        file_put_contents($f, $o);
        echo "fixed " . count($idxs) . " in $f\n";
    }
}
