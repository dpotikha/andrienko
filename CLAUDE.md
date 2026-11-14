# viktor-andrienko.com: PHP 8 migration

## Context
- An actor's personal site on DBM CMS (2008/2009 build, older than gady.com.ua's). The public site is single-language; the admin UI is Russian (`LOCALE=ru`).
- Same CMS and same server as gady.com.ua, which is already on PHP 8.4. **Reuse the gady work first**:
  - repo `D:\__Dima\html\Gady\site 3.0\site`, branch `php8`, `CLAUDE.md` there (server rules, cutover approach)
  - migration scripts in `tools/php8-migration/` (copied here)
- **Plan:** PHP 5.6 → 8.4 (branch `php8`), then a redesign, then a Telegram bot (prompts in `docs/session-prompts.md`).
- **Live:** PHP **5.6** (mod_php). It was down with HTTP 500 until 2026-11-14 because `session_is_registered` was removed in 5.4 (fixed in `7983258`, `WEB-INF/classes/base/HTTP.php`). Nothing goes to live without the owner's go.
- **Live snapshot and DB dump of 2026-11-14:** `D:\__Dima\html\andrienko\__site\live-2026-11-14\`. `main` = that live code, minus uploads and secrets.

## Working rules (mandatory)
- Every commit needs the owner's explicit approval: stage it, show the message, wait. No attribution lines in commit messages.
- Report findings and the planned action before changing anything; ask instead of guessing.
- Work on branch **`php8`**. Use one commit per logical category.
- Never put secrets in committed files. `_settings.php` (DB credentials) is gitignored and lives only on the server.
- Line endings: the working copy is CRLF (`core.autocrlf=true`), the repo LF. Edit with the PHP scripts or the Edit tool, not Git Bash `sed -i`.
- Answers: short and technical.

## Environment
- **Server:** Mirohost eVPS `vs1632.mirohost.net`, shared with gady.com.ua.
  - SSH: `ssh -i ~/.ssh/andrienko_deploy -o IdentitiesOnly=yes viktorandrienko@vs1632.mirohost.net`
  - home `/var/www/viktorandrie`, site folder `viktor-andrienko.com`, backups `~/deploy-backups`
  - **One SSH connection at a time, with pauses.** Rapid or parallel connections get the IP blocked for a while.
  - CLI `php` is 5.6; use `php8.4`. DB dumps: `mariadb-dump` (no `mysqldump`).
- **PHP version is set per site** in the Mirohost panel ("Сервер → Налаштування веб-сервера сайтів"). **Never use the global switch**: other sites on this server still run 5.6. Subdomains inherit the parent site's PHP.
- The php-fpm error log can't be read from the site account. `WEB-INF/prepend.php` logs PHP errors to `<home>/tmp/php-errors.log` when that folder exists and is writable. Create it on staging.
- **DB `viktorandrie`:** MariaDB 12.3.3, 27 tables, all MyISAM, all `utf8mb3_unicode_ci` (no cp1251 tables, unlike gady). The connection uses `SET NAMES utf8`, so emoji become `?`.
  - MyISAM means `DBUtils::rollback()` does nothing: a failed save leaves orphan rows.
- **HTTPS:** Let's Encrypt wildcard, works.
- **Local PHP 8.4 CLI:** `C:\Users\Seesens-Grafik\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe`. No local DB.
- **`_settings.php` constants** (server only): `LOCALE=ru`, `DB_CHARSET=utf8`, `MOD_REWRITE=true`, `USE_LANG=false`, DB URL `mysql://localhost:…`. A staging copy needs its own `_settings.php` pointing at the staging DB.

## Code map
- `index.php`: front controller (`.htaccess` routes everything through it); the node's `dynamicTemplate` picks `dynamic_templates/<name>.php`.
- `WEB-INF/prepend.php`: bootstrap (classes, DB, caches, session); `_site_settings.php` reads the settings node.
- `WEB-INF/classes/sql/mysql/`: DB layer (mysqli on `php8`); global `db_escape()` is in `Connection.php`.
- `WEB-INF/classes/dbm/`: nodes, classes, fields (`QL.php` = dbmQL → SQL).
- `admin/`: CMS admin. **Its templates are `.html` files containing PHP** (`admin/templates/**`, loaded by `usetemplate()`). The `.properties` language packs are PHP too. Scripts that only scan `*.php` miss both.
- Contact form: `dynamic_templates/contacts.php`. It uses reCAPTCHA (secret hardcoded there) and the CodeIgniter mail class `CI_Email` (`WEB-INF/classes/base/Email.class.php`, `mail()` transport). No PHPMailer on this site.
- Caches: `cache/` (serialized users, groups, node tree); delete the files to rebuild them.
- Uploads (gitignored, server only): `files/` (228 MB), `imglib/` (142 MB), `imglib_thumbnails/`, `content/`.
- `old/`: static HTML of the pre-CMS site. `_imgtpmclr.php`: deletes `imglib/_newimage/tmp` crop files older than a day.

## Differences from gady (why files couldn't simply be copied)
- Of 254 files shared with gady `main` in `WEB-INF/` + `admin/`, 190 were byte-identical. Mechanical fixes 1–6 reproduced gady's `php8` version of those exactly; the 15 with later gady fixes were taken from gady `php8` as they are.
- Not on this site: `QNode`, `QURL`, `QMail`, PHPMailer, `CClearText`, uploadify, the kill-switch GET hooks, `WEB-INF/PEAR`, `prepend_bg.php`.
- Here only: `.html` admin templates, `CI_Email`, `admin/forms.php`, and the TinyMCE image manager (`admin/tiny_mce/imagemanager`, opened from the file browser).
- `admin/JSON.php` was the real PEAR Services_JSON (escaping correct), so `json_encode` is a drop-in replacement; there was no pre-escaping to remove.

## Done on `php8` (not yet run on a server)
Each item is a separate commit. Lint on PHP 8.4: `files=221 failed=0`.
1. `tools/php8-migration` copied from gady. `find_undefined_constants.php` now also reads `.html` and `.properties`.
2. Removed: `phpmyadmin/` (old version, public on live) and `dumper.php`.
3. Fixes 1–6:
   - short tags: 1215 in 143 files; token signatures identical before and after
   - 53 constructors; `=& new`; curly offsets
   - ereg → preg (dynamic pattern in `StringField`)
   - `mktime()`, `xml_set_object(&$this)`, magic quotes
4. Removed dead admin modules:
   - `admin/emails.php` (fatal parse error since PHP 5.4) and its templates
   - `admin/vote.php` + `WEB-INF/classes/vote` (no tables, no templates)
   - `admin/users_import.php` (no templates; called any function named in `?do=`)
   - `admin/stats.php` + its templates + `admin/pear_xls` (queries tables of another DBM project)
   - `WEB-INF/classes/base/XmlReader_.php` (never loaded; its class name clashes with PHP's built-in `XMLReader`)
5. `admin/req1.php` (move-node dialog tree): call-time pass-by-reference removed. Fatal since 5.4, so moving nodes has been broken on live.
6. Static methods (`find_static_calls.php --fix` until clean) and `getDefaultValue` static in all field types.
7. Undefined constants: `RequiredField`, unquoted `$AdminTrnsl[...]` keys, `cname`/`fSize`/`shortname` without `$`, a stray `s` in `nodes/_edit_header.html`, `_MODERATION_FEATURE_` defined false.
8. mysqli DB layer from gady:
   - this site's byte-wise SQL splitter and `(float)` handling kept
   - removed a debug block in `PreparedStatement::execute()` that appended every INSERT/UPDATE plus `$_POST` and `$request` to `/log/*.log` in the web root (inactive on live only because `/log/` is missing)
9. `prepend.php`:
   - `display_errors` off, errors logged to `~/tmp/php-errors.log`
   - `error_reporting` without notices and deprecations
   - `Date` mktime `(int)` casts
   - `_write_cache` guard
   - `db_escape`
10. `Services_JSON` → `json_encode` in `admin/req*.php`.
11. Runtime fixes from gady:
    - QL optional regex groups
    - `ADMIN_EMAIL_FROM ?? null`
    - `(int)` children-list page number
    - numeric image crop values (`NewImageField::crop`, ported by hand)
12. TinyMCE file manager connector and image manager:
    - admin-session guard (both were open to anyone on live)
    - PHP 8 fixes from gady for the connector
    - fixes 1–5 for the image manager
13. `.htaccess`:
    - 403 for `/cache` (`USERS_CACHE` = logins and plaintext passwords, public on live), `/backup`, `/tools`, `/dynamic_templates`, `/WEB-INF`
    - no scripts in upload folders
    - `Options -Indexes`
14. Contact form: `$errors` initialised (a valid submission hit `sizeof(null)`, a TypeError). `project_full.php`: `count($photos)` typo → `$photoes` (adds the closing `</tr>` after an odd last photo).

## Security issues on LIVE (owner to act; nothing changed there)
- `https://viktor-andrienko.com/cache/USERS_CACHE` is downloadable: admin logins with **plaintext** passwords. Deny `cache/` and change all admin passwords.
- `backup/*.sql.gz`: 7 Dumper DB dumps (2008–2009) are downloadable by guessable name.
- `/phpmyadmin/` answers 200; `dumper.php` exists.
- The TinyMCE file manager and image manager accept anonymous upload, rename and delete.
- Admin passwords are stored and compared in plaintext (`dbm_users.password`, `admin/prepend.php`). Not changed on `php8`.

## Next
1. Staging: subdomain to be named by the owner.
   - PHP 8.4 for that site in the panel
   - copy files server-side (without `backup/`, `phpmyadmin/`, `dumper.php`, `error_log`, `cache/*`)
   - `mariadb-dump viktorandrie` → a separate staging DB; staging `_settings.php`; create `~/tmp`
2. Test the public pages: compare the sitemap/crawl HTML with live. Then test the admin:
   - login, tree, create/edit/delete/move a node
   - image upload and crop, file library, image library
   - TinyMCE file browser
   - contact form mail
3. Expect runtime-only PHP 8 errors (`count()` on null, arithmetic on `""`); check `~/tmp/php-errors.log` after each round.
