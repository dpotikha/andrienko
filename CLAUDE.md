# viktor-andrienko.com: PHP 8 migration

## Context
- An actor's personal site on DBM CMS (2008/2009 build, older than gady.com.ua's). The public site is single-language; the admin UI is Russian (`LOCALE=ru`).
- Same CMS and same server as gady.com.ua, which is already on PHP 8.4. **Reuse the gady work first**:
  - repo `D:\__Dima\html\Gady\site 3.0\site`, branch `php8`, `CLAUDE.md` there (server rules, cutover approach)
  - migration scripts in `tools/php8-migration/` (copied here)
- **Plan:**
  1. PHP 5.6 → 8.4 (branch `php8`, live)
  2. move to the gady.com.ua CMS build (branch `cms-upgrade`, see "CMS upgrade")
  3. redesign
  4. Telegram bot (prompts in `docs/session-prompts.md`)
- **Why gady's build:** comparison in the shared session notes, `cms-version-comparison.md`. c-n-l (`D:\__Dima\html\Cai & Lenard\site\c-n-l`) has a slightly newer build that is not PHP 8-ready; gady `php8` is the only one already running on 8.4.
- **Live:** `php8` on PHP **8.4** (php-fpm) since 2026-11-15; see "Live cutover". Before that PHP 5.6 (mod_php). It was down with HTTP 500 until 2026-11-14 because `session_is_registered` was removed in 5.4 (fixed in `7983258`, `WEB-INF/classes/base/HTTP.php`). Nothing goes to live without the owner's go.
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
- PHP error log on live: `/var/log/apache/viktor-andrienko.com/viktor-andrienko.com-error_log` (readable). The `~/tmp/php-errors.log` set in `WEB-INF/prepend.php` only works where the panel doesn't force `error_log`.
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

Found by the local test run (2026-11-15), each a fatal on PHP 8:
15. Incompatible overrides:
    - `Field::create()` vs `FileField::create($node_id)`: every page was a 500
    - the TinyMCE image driver stubs in `Transform.php`
    - new scanner `tools/php8-migration/find_signature_mismatches.php` (0 hits on gady `php8`)
16. `admin/nodes.php`:
    - `contenttree()` wrote properties on unset `$rootMap`/`$item` (PHP 5 created the object silently); new scanner `find_autovivified_objects.php`
    - `deleteselectednode()` and `addnode_form()` used `$request` before `global $request`. Multi-delete was fatal on PHP 5 too.
17. `_foot.php`: `OVERRIDE_NODE_PHOTO_ID` is defined only by the news, friends and publications templates; every other page hit an undefined constant.
18. TinyMCE image manager: `Files::*` and `Image_Transform::factory` called statically.
19. `prepend.php`: `$HTTP_SERVER_VARS` read removed (a warning on every request).

## Local test environment (no server needed)
- Portable MariaDB 11.4.4 (official winx64 zip, checksum verified) in the session scratchpad. Data dir `mdb-data`, port **3307**, bound to 127.0.0.1, `sql_mode=''`. Live dump of 2026-11-14 imported as `viktorandrie`.
- PHP 8.4 built-in server on `127.0.0.1:8084` with docroot = repo root, plus a router script that mimics `.htaccess`. Extensions loaded with `-d extension=mysqli/gd/mbstring/curl/openssl/fileinfo`; `short_open_tag=0`.
- Local `_settings.php` (gitignored) points to `mysql://127.0.0.1:3307/viktorandrie`, user root, no password.
- `files/`, `imglib/`, `imglib_thumbnails/`: junctions to copies of the live snapshot (gitignored).
- Admin tests use a local-only user `localtest` (id 900, group 1) inserted into the local DB copy. The real admin passwords are not used.
- Testing with curl from Git Bash: non-ASCII command-line arguments arrive as `?` (send values from UTF-8 files with `--data-urlencode name@file`); with `MSYS_NO_PATHCONV=1`, pass Windows paths (`cygpath -m`).

## Local test results (PHP 8.4, 2026-11-15)
- Public site: BFS crawl of every internal link, 2,110 pages, local vs live HTML (whitespace-normalised).
  - All status codes match live: 2,100 × 200, 10 × 302 to `/_404`.
  - 2,074 pages are identical.
  - 35 differ only by the `</tr>` fix in `project_full.php`.
  - The home page differs by `_foot.php`'s two random projects (`ORDER BY rand()`).
- Admin, all OK:
  - login; tree (`req.php` JSON); node list with paging
  - create a news item with image upload, crop and thumbnail; edit; delete; multi-delete; move node and back
  - properties, permissions, dependencies, sort, move dialog (`req1.php`)
  - classes and users pages; link-field popups
  - image library folder and upload (thumbnail made); file library folder and upload
  - TinyMCE connector upload; image manager (images, editor, thumbs); both return 403 without an admin session
  - UTF-8 (Cyrillic, quotes, backslash) is stored unchanged
- Contact form: validation and the reCAPTCHA call run. Sending mail was not tested (needs a valid captcha); `CI_Email` builds the message without errors on 8.4.
- Not ported: `admin/forms.php` needs `dbm/forms/FormDef.php`, which this site never had (500 on live too, not in the menu). Moderation is off (`_MODERATION_FEATURE_` false, no `dbm_moderation` table).

## Live cutover (2026-11-15, no staging, owner's decision)
- `php8` deployed to `viktor-andrienko.com` at 07:57; the owner switched the site to PHP 8.4 in the panel right after.
- Backups in `~/deploy-backups/2026-11-15/`:
  - `viktorandrie.sql.gz` (DB before cutover)
  - `_settings.php.orig`
  - `viktor-andrienko.com-php56/`: the old site, incl. `backup/`, `phpmyadmin/`, `error_log`
- Upload folders (`files`, `imglib`, `imglib_thumbnails`) were *moved* into the new site: hard links to apache-owned files are not allowed.
- Rollback:
  1. the owner sets PHP 5.6 in the panel;
  2. move the upload folders back into `viktor-andrienko.com-php56`;
  3. swap the folders back.
- Checks after cutover:
  - all 2,110 crawled pages have the same status codes as old live
  - 2,074 identical; 35 differ by the `</tr>` fix; the home page differs by its random projects
  - 403s and 404s as intended; TinyMCE endpoints return 403 without an admin session
  - error log: no fatals
- Footer photo fix (deployed right after cutover): `_foot.php` looked for `/_img/photo/photo_<shortname>jpg` (filesystem root, missing dot), so the 80 per-section photos never showed. Now:
  1. the section's own photo
  2. else `OVERRIDE_NODE_PHOTO_ID` (news, friends, publications)
  3. else `photo_.jpg`

## Live server facts (learned at cutover)
- **nginx in front of Apache serves static files itself**: `.htaccess` only covers requests that reach Apache (PHP, missing files, directories). Never keep anything secret as a static file in the web root.
  - This is why `_CACHE_DIR_` is now `<home>/cache` when that folder exists.
  - The admin `.html` templates and `WEB-INF/*.properties` are downloadable as source. There are no secrets in them.
- **PHP 8.4 runs as php-fpm, user `viktorandrie`**; 5.6 ran as `apache`. Old uploads are owned by `apache` with mode 644: new files work, but overwriting an old one in place fails.
- **PHP error log:** the panel forces `error_log`, so `prepend.php`'s `ini_set` is ignored. Read `/var/log/apache/viktor-andrienko.com/viktor-andrienko.com-error_log`; the site account can read it.
- **Warnings on 8.4** (no fatals; pages identical to 5.6): former notices now raised as warnings:
  - `_site_settings.php`: undefined `$str`
  - `contacts_form.php`: `$form` and `$errors` unset on GET
  - `publications_index.php` and `_404.php`: missing array keys
  - `project_full.php`: missing `thumburl`

## Security issues on LIVE
- Closed by the cutover:
  - `/cache/USERS_CACHE` (admin logins with plaintext passwords)
  - `backup/*.sql.gz` (7 Dumper dumps from 2008–2009)
  - `/phpmyadmin/` and `dumper.php`
  - anonymous upload, rename and delete in the TinyMCE file manager and image manager
- `USERS_CACHE` was public for years: **change all admin passwords.**
- Admin passwords are stored and compared in plaintext (`dbm_users.password`, `admin/prepend.php`); `admin/main.php` even puts the logged-in admin's password in a hidden form field. Not changed on `php8`.
- `admin/nodes.php` and the other admin controllers run any PHP function named in `?do=` with the request object as argument (admin session required). Not changed.

## Deploy notes
- The live `_settings.php` now starts with `<?php` (it was `<?`). Deploy code with `git archive` of a commit, excluding `CLAUDE.md`, `docs/`, `tools/` and `.gitignore`; never upload CRLF working-copy files.

## CMS upgrade (branch `cms-upgrade`, local only, not deployed)
- `WEB-INF/` and `admin/` are gady `php8` as they are, except:
  - `WEB-INF/lang_en.php`, `WEB-INF/lang_ua.php`: this site's, needed by `index.php`
  - `admin/tiny_mce/imagemanager/`: this site's `php8` version. Gady's copy is unported and has no admin-session guard.
  - `WEB-INF/classes/dbm/Node.php` `findByPath()`: the URL path is now escaped. Gady concatenates it raw into SQL (an injection on every page; gady live still has it).
  - `WEB-INF/prepend.php`: caches in `<home>/cache` when it exists (gady's `s/cache/` is inside the web root, and nginx serves it)
  - short tags in 11 gady TinyMCE templates (`admin/templates/tiny_mce/`), converted with `fix1`
- Upload folders now use gady's layout. URLs and folders:
  - `/s/i` (was `imglib`)
  - `/s/ithumb` (was `imglib_thumbnails`)
  - `/s/files` (was `files`)
  - plus `/s/ithumb2`, `/s/ithumb3`, `/s/o`, `/s/tmp`
  - `.htaccess` 301-redirects the old URLs; `s/` is gitignored and must not run scripts.
- Site code adapted to the new core:
  - `dynamic_templates/_functions.php`: `getTopSection`, `externalURL`, `humanLikeUrl`, which lived in the old `prepend.php`
  - `_site_settings.php`: `getFormattedDate` renamed `siteFormattedDate` (clashed with the core's `my_func.php`); month names read directly (the old `Date::getMonthName` is gone)
  - `filmography.php`: `nl2br` on roles (the new `TextAreaField` no longer adds it)
  - contact form: PHPMailer from the core instead of `CI_Email`; visitor input HTML-escaped; Reply-To set to the visitor
- `_settings.php` needs the new constants; the template is `_settings.example.php`.
- DB (additive), `tools/cms-upgrade/`:
  - `001_core_schema.sql`:
    - `dbm_nodes.md5_path` (filled), `shortname` widened to 255
    - `dbm_class_fields.hide`, `dbm_user_groups.canManageSettings`
    - tables `dbm_log`, `dbm_seo`, `dbm_settings`, `dbm_text_index`
  - `002_upload_paths.sql`: 25 `/imglib/` references in 7 rich-text rows → `/s/i/`
- New image fields store paths like `/news/<id>_<name>.jpg`; old values (`/_newimage/...`) keep working.
- Local tests (PHP 8.4, DB `viktor_cms` = live dump of 2026-11-14 + migrations):
  - crawl of 2,110 pages: all status codes match `php8`
  - 2,109 pages are identical, with the old upload paths mapped to the new ones; the home page differs by its random projects
  - all 4,035 `/s/` image and file URLs on those pages checked
  - admin:
    - login; tree, lists, properties, permissions, sorting, classes, users, settings, log, image and file libraries
    - news with image upload, crop and thumbnail; edit (TinyMCE); move away and back; delete; multi-delete
    - TinyMCE file manager upload and image manager; anonymous access gets 403
  - lint and scanners clean
- **Deploy plan (not done):**
  1. backup files and DB
  2. run `001` and `002` on `viktorandrie`
  3. `mkdir s`; `mv imglib s/i`; `mv imglib_thumbnails s/ithumb`; `mv files s/files`; create `s/ithumb2`, `s/ithumb3`, `s/o`, `s/tmp`
  4. add the new constants to the server `_settings.php`
  5. swap the code folder like the PHP 8 cutover; empty `~/cache`
- Gady modules the admin menu now shows (newsletter, languages, log, settings) are unused here. Newsletter has no tables on this site.

## Next
1. Owner tests on live:
   - admin login, editing a node, image upload
   - the TinyMCE file browser
   - the contact form with a real captcha (mail via `mail()` → exim)
2. Clean up the 8.4 warnings listed above (log noise only).
3. Delete `~/deploy-backups/2026-11-15/viktor-andrienko.com-php56` after ~2 weeks without problems.
