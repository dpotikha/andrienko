# PHP 5.6 → 8.4 migration tools for DBM CMS sites

Scripts used to move gady.com.ua (DBM CMS, ~2014) to PHP 8.4. They are meant to be reused on the other DBM sites.
CLI only: `tools/.htaccess` blocks web access. They do not read the database or any settings.

Validated: run on the original gady `main` code, fixes 1–5 reproduce the `php8` branch exactly
(except the one dynamic `ereg` pattern the script lists for manual fixing).

## Prerequisites
- A PHP 8.x CLI. No install needed: the Windows zip from windows.php.net works (`php.exe -n` = no php.ini).
- Run everything from the site root, on a git branch, and commit each step separately.
- **Line endings:** don't use Git Bash `sed -i` on CRLF files (it drops the CRs). The PHP scripts preserve bytes.

## Steps
```bash
bash tools/php8-migration/list_files.sh > files.txt      # review the exclusions first
PHP=php bash tools/php8-migration/lint.sh files.txt       # baseline
```

| # | Script | What it does |
|---|---|---|
| 1 | `php -d short_open_tag=1 fix1_short_tags.php files.txt` | `<?` → `<?php` via the tokenizer. Verify with `token_signature.php` (see its header): the token streams must be identical. |
| 2 | `php fix2_constructors.php files.txt` | PHP4 constructors → `__construct`. Then grep for explicit calls by the old name (gady had one: `parent::PEAR_Error(` in `admin/JSON.php`). |
| 3 | `php fix3_ref_new.php files.txt` | `=& new` → `= new` (comments untouched). |
| 4 | `php fix4_curly_offsets.php files.txt` | `$s{expr}` → `$s[expr]` (regex quantifiers in strings untouched). |
| 5 | `php fix5_ereg.php files.txt` | `ereg`/`eregi` → `preg_match` with `~` delimiters; `i` for eregi, `D` when the pattern ends in `$`. Fix the listed MANUAL items by hand; for a dynamic pattern use `'~' . str_replace('~', '\~', $regex) . '~D'`. |
| 6 | by hand | `grep -n -E 'mktime\(\s*\)\|\(real\)\|xml_set_object\|get_magic_quotes_gpc'`: `mktime()` → `time()`, `(real)` → `(float)`, drop `&` in `xml_set_object(..., &$this)`, `get_magic_quotes_gpc() == 1` → `false`. |

After step 6, `lint.sh` must report `failed=0` on PHP 8.4.

## Beyond syntax: what gady needed (see the `php8` branch commits)
- **DB layer → mysqli** (`WEB-INF/classes/sql/mysql/*`). Same class and method names. Key points:
  - `mysqli_report(MYSQLI_REPORT_OFF)` to keep the old "return false" behaviour.
  - `mysqli_set_charset` plus the existing `SET NAMES`.
  - `SET SESSION sql_mode=''` to match the live server (check `SELECT @@GLOBAL.sql_mode` first).
  - `is_resource($res)` → `$res instanceof mysqli_result`.
  - A type map so `ResultSet::getObject()` still gets the old `mysql_field_type()` names.
  - Global `db_escape()` replaces `mysql_escape_string` everywhere.
- **Check table charsets:** `SELECT table_name, table_collation FROM information_schema.tables WHERE table_schema='<db>'`. gady had 9 `cp1251` tables. They work through the utf8 connection, but characters outside cp1251 (emoji) become `?`.
- **PHPMailer 5.1 → 7.x:** the old bundled copy defaulted to UTF-8, the new one to ISO-8859-1. Set `CharSet = UTF-8`, or Cyrillic mail arrives garbled.
- **`admin/JSON.php` is a custom encoder, not PEAR Services_JSON:** it doesn't escape `"` or `\`, so callers pre-escape (`admin/req.php`). Remove that pre-escaping when switching to `json_encode`.
- **Dead or unsafe code to delete:** `WEB-INF/prepend_bg.php`, `admin/phpMyAdmin`, `admin/dumper` (its `backup/` folder may hold web-downloadable DB dumps), `WEB-INF/PEAR`, `admin/pear_xls`. `admin/xls` + `admin/import.php` were dead on gady but may be used on other DBM sites.
- **Admin upload:** uploadify is Flash-based and needs a replacement.
- **Runtime issues lint can't see:** `count()` on null, `max([])`, arrays in `$_GET`, `is_resource()` on GD images (objects in PHP 8).
