-- viktor-andrienko.com: DB changes for the gady.com.ua CMS build (branch cms-upgrade).
-- Additive only: no existing row or column is changed except dbm_nodes.shortname (widened 32 -> 255).
-- Source of the definitions: gady.com.ua live DB (2014 dump) and the gady php8 code.
-- Run once, after a backup: mariadb viktorandrie < 001_core_schema.sql

-- node lookup by path (Node::findByPath) uses md5_path
ALTER TABLE dbm_nodes
  MODIFY shortname varchar(255) NOT NULL DEFAULT '',
  ADD COLUMN md5_path varchar(255) DEFAULT NULL,
  ADD UNIQUE KEY md5_path (md5_path),
  ADD KEY name (name);
UPDATE dbm_nodes SET md5_path = md5(absolute_path);

-- FieldDef: hidden fields in the admin form
ALTER TABLE dbm_class_fields ADD COLUMN hide tinyint(4) DEFAULT 0;

-- UserGroup: settings page right; the admin group (id 1) gets it
ALTER TABLE dbm_user_groups ADD COLUMN canManageSettings tinyint(3) DEFAULT 0;
UPDATE dbm_user_groups SET canManageSettings = 1 WHERE id = 1;

-- admin activity log (written on node create/update/delete)
CREATE TABLE IF NOT EXISTS dbm_log (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT 0,
  node_id int(11) DEFAULT 0,
  node_name varchar(255) DEFAULT NULL,
  parent_id int(11) DEFAULT 0,
  parent_name varchar(255) DEFAULT NULL,
  activity int(11) DEFAULT 0,
  ip varchar(45) DEFAULT NULL,
  server text,
  date datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- per-node SEO title / keywords / description
CREATE TABLE IF NOT EXISTS dbm_seo (
  id int(10) NOT NULL AUTO_INCREMENT,
  lang varchar(2) DEFAULT 'ru',
  node_id int(11) DEFAULT 0,
  seo_title varchar(256) DEFAULT NULL,
  seo_keywords text,
  seo_description text,
  seo_google tinyint(4) DEFAULT 1,
  seo_google_childs tinyint(4) DEFAULT 1,
  UNIQUE KEY id (id),
  UNIQUE KEY lang_node_id (lang, node_id),
  KEY lang (lang),
  KEY node_id (node_id),
  KEY seo_google (seo_google),
  KEY seo_google_childs (seo_google_childs)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- site constants editable in the admin (prepend.php defines one constant per row)
CREATE TABLE IF NOT EXISTS dbm_settings (
  id int(11) NOT NULL AUTO_INCREMENT,
  shortname varchar(100) NOT NULL,
  value varchar(200) NOT NULL,
  rights int(11) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY shortname (shortname)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- full-text search index
CREATE TABLE IF NOT EXISTS dbm_text_index (
  node_id int(11) NOT NULL DEFAULT 0,
  value_str text,
  FULLTEXT KEY value_str (value_str)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
