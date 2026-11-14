<?php
// Prints "md5  file" of each file's token stream; open-tag text normalised so <? and <?php compare equal.
// Proves fix 1 changed nothing but the tags:
//   php -d short_open_tag=1 token_signature.php files.txt > before.txt   (before fix 1)
//   php -d short_open_tag=0 token_signature.php files.txt > after.txt    (after fix 1)
//   diff before.txt after.txt   -> must be empty
foreach (file($argv[1], FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $f) {
    $s = '';
    foreach (token_get_all(file_get_contents($f)) as $t) {
        if (is_array($t)) $s .= $t[0] . ':' . ($t[0] === T_OPEN_TAG || $t[0] === T_WHITESPACE ? '' : $t[1]) . "\x1f";
        else $s .= $t . "\x1f";
    }
    echo md5($s), "  $f\n";
}
