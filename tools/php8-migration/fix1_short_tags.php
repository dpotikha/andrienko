<?php
// Fix 1: short open tags "<?" -> "<?php", using the tokenizer (strings, comments and "<?xml" in strings are untouched).
// Must run with short tags ON so the tokenizer recognises "<?":
//   php -d short_open_tag=1 fix1_short_tags.php files.txt [--dry]
// Verify afterwards with token_signature.php (before: short_open_tag=1, after: short_open_tag=0, outputs must match).
if (!ini_get('short_open_tag')) { fwrite(STDERR, "Run with -d short_open_tag=1\n"); exit(1); }
$dry = in_array('--dry', $argv);
$total = 0; $changed = 0;
foreach (file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $f) {
    $out = ''; $n = 0;
    foreach (token_get_all(file_get_contents($f)) as $t) {
        $text = is_array($t) ? $t[1] : $t;
        if (is_array($t) && $t[0] === T_OPEN_TAG && strncasecmp($text, '<?php', 5) !== 0) {
            $rest = substr($text, 2);          // whitespace the tokenizer attached, or ''
            $text = '<?php' . ($rest === '' ? ' ' : $rest);
            $n++;
        }
        $out .= $text;
    }
    if ($n) {
        $total += $n; $changed++;
        echo "$n\t$f\n";
        if (!$dry) file_put_contents($f, $out);
    }
}
echo "occurrences=$total files=$changed\n";
