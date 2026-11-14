<?php
/*
 * Lists identifiers used as constants that are never defined (define()/const/class const)
 * and are not PHP built-ins. PHP 5/7 read an undefined constant as its own name (a string);
 * PHP 8 throws "Undefined constant", so every hit is a potential fatal error.
 * Usage: php -n find_undefined_constants.php <root> [exclude-dir ...]
 */
$root = rtrim($argv[1] ?? '.', '/\\');
$exclude = array_slice($argv, 2);

$files = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    if (strtolower($f->getExtension()) !== 'php') continue;
    $rel = str_replace('\\', '/', substr($f->getPathname(), strlen($root) + 1));
    foreach ($exclude as $ex) if (strpos($rel, rtrim($ex, '/') . '/') === 0) continue 2;
    $files[$rel] = file_get_contents($f->getPathname());
}

$defined = array_change_key_case(get_defined_constants(), CASE_UPPER);
$uses = [];
$names = [];
$skipPrev = [T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_NEW, T_FUNCTION, T_CLASS, T_INTERFACE, T_TRAIT,
    T_EXTENDS, T_IMPLEMENTS, T_INSTANCEOF, T_CONST, T_USE, T_NAMESPACE, T_GOTO, T_AS, T_INSTEADOF];
if (defined('T_NULLSAFE_OBJECT_OPERATOR')) $skipPrev[] = T_NULLSAFE_OBJECT_OPERATOR;
if (defined('T_ENUM')) $skipPrev[] = T_ENUM;
$ignore = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];
$keywords = ['true', 'false', 'null', 'self', 'parent', 'static', 'array', 'callable', 'int', 'float',
    'string', 'bool', 'void', 'mixed', 'object', 'iterable'];

foreach ($files as $rel => $src) {
    $t = token_get_all($src);
    $n = count($t);
    for ($i = 0; $i < $n; $i++) {
        if (!is_array($t[$i])) continue;

        // define('NAME', ...)
        if ($t[$i][0] === T_STRING && strcasecmp($t[$i][1], 'define') === 0) {
            for ($j = $i + 1; $j < $n && ((is_array($t[$j]) && $t[$j][0] === T_WHITESPACE) || $t[$j] === '('); $j++);
            if ($j < $n && is_array($t[$j]) && $t[$j][0] === T_CONSTANT_ENCAPSED_STRING)
                $defined[strtoupper(trim($t[$j][1], '\'"'))] = 1;
            continue;
        }
        // const NAME = ... (global or class constant)
        if ($t[$i][0] === T_CONST) {
            for ($j = $i + 1; $j < $n && $t[$j] !== ';'; $j++)
                if (is_array($t[$j]) && $t[$j][0] === T_STRING) $defined[strtoupper($t[$j][1])] = 1;
            continue;
        }
        if ($t[$i][0] !== T_STRING) continue;

        $name = $t[$i][1];
        for ($p = $i - 1; $p >= 0 && is_array($t[$p]) && in_array($t[$p][0], $ignore); $p--);
        for ($q = $i + 1; $q < $n && is_array($t[$q]) && in_array($t[$q][0], $ignore); $q++);
        $prev = $p >= 0 ? $t[$p] : null;
        $next = $q < $n ? $t[$q] : null;

        if (is_array($prev) && in_array($prev[0], $skipPrev)) continue;                 // ->x, ::x, new X, function x ...
        if ($next === '(' || (is_array($next) && in_array($next[0], [T_DOUBLE_COLON, T_VARIABLE, T_ELLIPSIS]))) continue; // call, X::, type hint
        if ($next === '&' && $q + 1 < $n && is_array($t[$q + 1]) && $t[$q + 1][0] === T_VARIABLE) continue; // Type &$x
        if ($prev === ':' && in_array($next, ['{', ';', '='], true)) continue;           // return type
        if ($next === ':' && in_array($prev, [';', '{', '}'], true)) continue;           // goto label
        if (in_array(strtolower($name), $keywords)) continue;

        $key = strtoupper($name);
        $uses[$key][] = "$rel:{$t[$i][2]}";
        $names[$key] = $name;
    }
}

$out = [];
foreach ($uses as $key => $where) {
    if (isset($defined[$key])) continue;
    $out[] = sprintf("%-38s %3d  %s", $names[$key], count($where),
        implode(', ', array_slice($where, 0, 4)) . (count($where) > 4 ? ' ...' : ''));
}
sort($out);
echo implode("\n", $out), "\n";
