#!/bin/bash
# Write the list of the site's own tracked PHP files (vendor/third-party code excluded) to stdout.
# Usage (from the site root): bash tools/php8-migration/list_files.sh > files.txt
# Review the exclusions for each site: they are third-party libraries that are replaced or deleted, not patched.
git ls-files '*.php' | grep -v -i -E \
  'phpmyadmin|tiny_?mce|admin/dumper/|WEB-INF/PEAR/|admin/pear_xls/|admin/xls/|admin/js/|gd/examples/|WEB-INF/classes/PHPMailer/|^tools/'
