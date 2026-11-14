#!/bin/bash
# Lint every file in a list with short tags OFF, the way PHP 8 runs it. Prints failures and deprecations.
# Usage (from the site root): PHP=/path/to/php bash tools/php8-migration/lint.sh files.txt
PHP="${PHP:-php}"
fail=0; n=0
while IFS= read -r f; do
  [ -z "$f" ] && continue
  n=$((n+1))
  out=$("$PHP" -n -d short_open_tag=0 -d error_reporting=-1 -d display_errors=1 -l "$f" 2>&1)
  echo "$out" | grep -q '^No syntax errors' || fail=$((fail+1))
  echo "$out" | grep -v -E '^No syntax errors|^Errors parsing|^$'
done < "$1"
echo "files=$n failed=$fail"
[ "$fail" -eq 0 ]
