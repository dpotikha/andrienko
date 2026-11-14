# Prompts for the follow-up sessions (viktor-andrienko.com)

Run them in this order: 1 needs the live code and DB fetched; 3 builds on the PHP 8.4 code (the bot writes through the CMS `Node` API).

---

## 1. PHP 8.4 migration

```
viktor-andrienko.com runs on the same DBM CMS as gady.com.ua, which you already moved to PHP 8.4. Repeat that migration here.

Read first, in this order:
- D:\__Dima\html\Gady\site 3.0\site\CLAUDE.md (environment, server rules, cutover approach)
- D:\__Dima\html\Gady\site 3.0\site\tools\php8-migration\README.md and AGENT_REPORT_2026-11-14.md
- git log of the gady repo, branch php8: every "php8:", "security:" and "admin:" commit is a fix that likely applies here too
- docs\ in this repo

Server: same Mirohost eVPS as gady (vs1632.mirohost.net). SSH key ~/.ssh/andrienko_deploy, account <ACCOUNT>. Server CLI php is 5.6, use php8.4. One SSH connection at a time, with pauses. Local PHP 8.4 CLI: see the php-cli-location memory of the gady project.

Steps:
1. Check that main matches the live code (fetched in the first session). If not, stop and tell me.
2. Copy tools/php8-migration from the gady repo into this repo, branch php8, run fixes 1-6 as in the README, one commit per step (show me each commit for approval first).
3. Port what gady needed beyond syntax: mysqli DB layer, PHPMailer, JSON, uploadify replacement, error logging in prepend.php, security removals (phpmyadmin/, dumper.php, backup/ dumps, kill-switch GET hooks). Diff this site's WEB-INF and admin against gady's main branch first: copy gady's fixed files only where the originals are identical; port by hand where they differ.
4. Check table charsets and sql_mode of DB viktorandrie.
5. Staging: ask me for the staging subdomain; set its PHP to 8.4 per site in the panel (never the global switch); copy files and the DB server-side into a separate staging DB.
6. Test the public pages and the admin; write CLAUDE.md for this repo as you go.

Before starting, ask me only what you can't find out: the staging subdomain, and anything else that blocks you.
```

---

## 2. Design analysis

```
viktor-andrienko.com (DBM CMS, PHP 8.4 on branch php8) needs a completely new design. This session is analysis only: no code changes.

Inputs: the live site https://viktor-andrienko.com, this repo (dynamic_templates/, main.css, second.css, _img/), the DB node tree (classes and fields in dbm_* tables, via SSH, account <ACCOUNT>, key ~/.ssh/andrienko_deploy, php8.4 / mysql on the server).

Deliver:
1. Site map: every page type, its template in dynamic_templates/, the node class and fields behind it, how many nodes of each, last update date.
2. Content inventory: sections, media (photos, video, audio, press), languages, what is outdated or empty.
3. Technical constraints of the templates for a redesign (what is markup in templates vs content in the DB, inline styles in WYSIWYG content that a new design must cope with).
4. Current problems: mobile, accessibility, SEO (titles, meta, OG, sitemap, https), speed (image sizes).
5. A short list of questions for me (target audience, what must stay, references I like).

Ask me before guessing about the site's history or purpose.
```

---

## 3. Telegram bot

```
Add a Telegram bot to viktor-andrienko.com so the site owner can update content from Telegram, the same way as the gady.com.ua bot.

Reuse the gady bot: D:\__Dima\html\Gady\site 3.0\site\bot\ and the "Bot spec" section of that repo's CLAUDE.md (Claude proposes, PHP validates, writes only on Confirm, Node::createWithDefaultValues0 / Node::store / Node::remove, change log bot_log, /undo, secrets file outside the web folders, X-Telegram-Bot-Api-Secret-Token check).

Prerequisite: the PHP 8.4 code (branch php8) is deployed. Server: account <ACCOUNT>, key ~/.ssh/andrienko_deploy, php8.4.

Steps:
1. Ask me which content types the bot should manage (news, performances, gallery, ...) and who may use it (Telegram user IDs).
2. Map those node classes and fields from the DB; convert any cp1251 tables the bot writes to utf8 first (with backups).
3. Make the bot code generic where gady's is tour-specific, so both sites can share it; propose how before changing gady's copy.
4. Bot subdomain, Let's Encrypt, secrets template, self-test, set_webhook: same as gady.
```
