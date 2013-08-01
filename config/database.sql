-- 
-- Table `tl_site_export`
-- 

CREATE TABLE `tl_site_export` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `pages` blob NULL,
  `recursive` char(1) NOT NULL default '',
  `includeLayout` char(1) NOT NULL default '',
  `targetDir` varchar(255) NOT NULL default '',
  `layout` int(10) unsigned NOT NULL default '0',
  `rulesFrom` int(10) unsigned NOT NULL default '0',
  `toc` varchar(32) NOT NULL default '',
  `tocHeadline` varchar(255) NOT NULL default '',
  `exportEpub` char(1) NOT NULL default '',
  `ebookFilename` varchar(255) NOT NULL default 'book.epub',
  `ebookCover` varchar(255) NOT NULL default '',
  `ebookTitle` varchar(255) NOT NULL default '',
  `ebookDescription` varchar(255) NOT NULL default '',
  `ebookIdentifier` varchar(255) NOT NULL default '',
  `ebookSubject` varchar(255) NOT NULL default '',
  `ebookCreator` varchar(255) NOT NULL default '',
  `ebookPublisher` varchar(255) NOT NULL default '',
  `ebookDate` varchar(10) NOT NULL default '',
  `ebookLanguage` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_site_export_rules`
-- 

CREATE TABLE `tl_site_export_rules` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `pattern` mediumtext NULL,
  `replacement` mediumtext NULL,
  `isRegex` char(1) NOT NULL default '',
  `modIgnoreCase` char(1) NOT NULL default '',
  `modMultiLine` char(1) NOT NULL default '',
  `modDotAll` char(1) NOT NULL default '',
  `modUngreedy` char(1) NOT NULL default '',
  `modUTF8` char(1) NOT NULL default '',
  `isActive` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;