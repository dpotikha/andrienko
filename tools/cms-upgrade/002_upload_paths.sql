-- viktor-andrienko.com: upload folders move to the gady.com.ua layout (branch cms-upgrade).
--   imglib -> s/i, imglib_thumbnails -> s/ithumb, files -> s/files
-- Image and file fields store paths relative to those folders, so they need no change.
-- Only hand-inserted <img src="/imglib/..."> in rich-text fields do: 25 references in 7 rows (live dump of 2026-11-14).
-- The old URLs also get 301 redirects in .htaccess.
UPDATE dbm_nfv_news_item SET text = REPLACE(text, '/imglib/', '/s/i/') WHERE text LIKE '%/imglib/%';
UPDATE dbm_nfv_pub       SET text = REPLACE(text, '/imglib/', '/s/i/') WHERE text LIKE '%/imglib/%';
