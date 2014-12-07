-- Generation Time: Oct 19, 2013 at 04:34 PM
-- Server version: 5.0.44-log
-- PHP Version: 5.2.13-pl1-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `access_list`
--

CREATE TABLE IF NOT EXISTS `access_list` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) character set utf8 default NULL,
  `start` varbinary(255) NOT NULL,
  `end` varbinary(255) NOT NULL,
  `level` smallint(3) unsigned NOT NULL default '5',
  `type` varchar(64) character set utf8 default NULL,
  `user` int(11) NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `start` (`start`),
  KEY `end` (`end`),
  KEY `level` (`level`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE IF NOT EXISTS `album` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) character set utf8 default NULL,
  `prefix` varchar(32) character set utf8 default NULL,
  `mbid` varchar(36) collate utf8_unicode_ci default NULL,
  `year` int(4) unsigned NOT NULL default '1984',
  `disk` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `year` (`year`),
  KEY `disk` (`disk`),
  FULLTEXT KEY `name_2` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `artist`
--

CREATE TABLE IF NOT EXISTS `artist` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) character set utf8 default NULL,
  `prefix` varchar(32) character set utf8 default NULL,
  `mbid` varchar(36) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  FULLTEXT KEY `name_2` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `catalog`
--

CREATE TABLE IF NOT EXISTS `catalog` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(128) character set utf8 default NULL,
  `path` varchar(255) character set utf8 default NULL,
  `catalog_type` enum('local','remote') character set utf8 default NULL,
  `last_update` int(11) unsigned NOT NULL default '0',
  `last_clean` int(11) unsigned default NULL,
  `last_add` int(11) unsigned NOT NULL default '0',
  `enabled` tinyint(1) unsigned NOT NULL default '1',
  `rename_pattern` varchar(255) character set utf8 default NULL,
  `sort_pattern` varchar(255) character set utf8 default NULL,
  `gather_types` varchar(255) character set utf8 default NULL,
  PRIMARY KEY  (`id`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `democratic`
--

CREATE TABLE IF NOT EXISTS `democratic` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) character set utf8 default NULL,
  `cooldown` tinyint(4) unsigned default NULL,
  `level` tinyint(4) unsigned NOT NULL default '25',
  `user` int(11) NOT NULL,
  `primary` tinyint(1) unsigned NOT NULL default '0',
  `base_playlist` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `level` (`level`),
  KEY `primary_2` (`primary`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dynamic_playlist`
--

CREATE TABLE IF NOT EXISTS `dynamic_playlist` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) character set utf8 default NULL,
  `user` int(11) NOT NULL,
  `date` int(11) unsigned NOT NULL,
  `type` varchar(128) character set utf8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dynamic_playlist_data`
--

CREATE TABLE IF NOT EXISTS `dynamic_playlist_data` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `dynamic_id` int(11) unsigned NOT NULL,
  `field` varchar(255) character set utf8 default NULL,
  `internal_operator` varchar(64) character set utf8 default NULL,
  `external_operator` varchar(64) character set utf8 default NULL,
  `value` varchar(255) character set utf8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `flagged`
--

CREATE TABLE IF NOT EXISTS `flagged` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `object_id` int(11) unsigned NOT NULL default '0',
  `object_type` enum('artist','album','song') character set utf8 default NULL,
  `user` int(11) NOT NULL,
  `flag` enum('delete','retag','reencode','other') character set utf8 default NULL,
  `date` int(11) unsigned NOT NULL default '0',
  `approved` tinyint(1) unsigned NOT NULL default '0',
  `comment` varchar(255) character set utf8 default NULL,
  PRIMARY KEY  (`id`),
  KEY `date` (`date`,`approved`),
  KEY `object_id` (`object_id`),
  KEY `object_type` (`object_type`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `image` mediumblob NOT NULL,
  `mime` varchar(64) collate utf8_unicode_ci NOT NULL,
  `size` varchar(64) collate utf8_unicode_ci NOT NULL,
  `object_type` varchar(64) collate utf8_unicode_ci NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `object_type` (`object_type`),
  KEY `object_id` (`object_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ip_history`
--

CREATE TABLE IF NOT EXISTS `ip_history` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user` int(11) NOT NULL,
  `ip` varbinary(255) default NULL,
  `date` int(11) unsigned NOT NULL default '0',
  `agent` varchar(255) character set utf8 default NULL,
  PRIMARY KEY  (`id`),
  KEY `username` (`user`),
  KEY `date` (`date`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `live_stream`
--

CREATE TABLE IF NOT EXISTS `live_stream` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(128) character set utf8 default NULL,
  `site_url` varchar(255) character set utf8 default NULL,
  `url` varchar(4096) collate utf8_unicode_ci default NULL,
  `genre` int(11) unsigned NOT NULL default '0',
  `catalog` int(11) unsigned NOT NULL default '0',
  `frequency` varchar(32) character set utf8 default NULL,
  `call_sign` varchar(32) character set utf8 default NULL,
  PRIMARY KEY  (`id`),
  KEY `catalog` (`catalog`),
  KEY `genre` (`genre`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `now_playing`
--

CREATE TABLE IF NOT EXISTS `now_playing` (
  `id` varchar(64) character set utf8 NOT NULL default '',
  `object_id` int(11) unsigned NOT NULL,
  `object_type` varchar(255) character set utf8 default NULL,
  `user` int(11) NOT NULL,
  `expire` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `expire` (`expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `object_count`
--

CREATE TABLE IF NOT EXISTS `object_count` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `object_type` enum('album','artist','song','playlist','genre','catalog','live_stream','video') character set utf8 default NULL,
  `object_id` int(11) unsigned NOT NULL default '0',
  `date` int(11) unsigned NOT NULL default '0',
  `user` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `object_type` (`object_type`),
  KEY `object_id` (`object_id`),
  KEY `userid` (`user`),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE IF NOT EXISTS `playlist` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(128) character set utf8 default NULL,
  `user` int(11) NOT NULL,
  `type` enum('private','public') character set utf8 default NULL,
  `date` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_data`
--

CREATE TABLE IF NOT EXISTS `playlist_data` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `playlist` int(11) unsigned NOT NULL default '0',
  `object_id` int(11) unsigned default NULL,
  `object_type` varchar(32) character set utf8 default NULL,
  `track` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `playlist` (`playlist`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `preference`
--

CREATE TABLE IF NOT EXISTS `preference` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(128) character set utf8 default NULL,
  `value` varchar(255) character set utf8 default NULL,
  `description` varchar(255) character set utf8 default NULL,
  `level` int(11) unsigned NOT NULL default '100',
  `type` varchar(128) character set utf8 default NULL,
  `catagory` varchar(128) character set utf8 default NULL,
  PRIMARY KEY  (`id`),
  KEY `catagory` (`catagory`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE IF NOT EXISTS `rating` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user` int(11) NOT NULL,
  `object_type` enum('artist','album','song','steam','video') character set utf8 default NULL,
  `object_id` int(11) unsigned NOT NULL default '0',
  `rating` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_rating` (`user`,`object_type`,`object_id`),
  KEY `object_id` (`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `search`
--

CREATE TABLE IF NOT EXISTS `search` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user` int(11) NOT NULL,
  `type` enum('private','public') character set utf8 default NULL,
  `rules` mediumtext collate utf8_unicode_ci NOT NULL,
  `name` varchar(255) character set utf8 default NULL,
  `logic_operator` varchar(3) character set utf8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `id` varchar(64) character set utf8 NOT NULL default '',
  `username` varchar(16) character set utf8 default NULL,
  `expire` int(11) unsigned NOT NULL default '0',
  `value` longtext collate utf8_unicode_ci NOT NULL,
  `ip` varbinary(255) default NULL,
  `type` enum('mysql','ldap','http','api','xml-rpc') character set utf8 default NULL,
  `agent` varchar(255) character set utf8 default NULL,
  PRIMARY KEY  (`id`),
  KEY `expire` (`expire`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session_stream`
--

CREATE TABLE IF NOT EXISTS `session_stream` (
  `id` varchar(64) character set utf8 NOT NULL default '',
  `user` int(11) unsigned NOT NULL,
  `agent` varchar(255) character set utf8 default NULL,
  `expire` int(11) unsigned NOT NULL,
  `ip` varbinary(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `song`
--

CREATE TABLE IF NOT EXISTS `song` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `file` varchar(4096) collate utf8_unicode_ci default NULL,
  `catalog` int(11) unsigned NOT NULL default '0',
  `album` int(11) unsigned NOT NULL default '0',
  `year` mediumint(4) unsigned NOT NULL default '0',
  `artist` int(11) unsigned NOT NULL default '0',
  `title` varchar(255) character set utf8 default NULL,
  `bitrate` mediumint(8) unsigned NOT NULL default '0',
  `rate` mediumint(8) unsigned NOT NULL default '0',
  `mode` enum('abr','vbr','cbr') character set utf8 default NULL,
  `size` int(11) unsigned NOT NULL default '0',
  `time` smallint(5) unsigned NOT NULL default '0',
  `track` smallint(5) unsigned default NULL,
  `mbid` varchar(36) collate utf8_unicode_ci default NULL,
  `played` tinyint(1) unsigned NOT NULL default '0',
  `enabled` tinyint(1) unsigned NOT NULL default '1',
  `update_time` int(11) unsigned default '0',
  `addition_time` int(11) unsigned default '0',
  PRIMARY KEY  (`id`),
  KEY `album` (`album`),
  KEY `artist` (`artist`),
  KEY `file` (`file`(333)),
  KEY `update_time` (`update_time`),
  KEY `addition_time` (`addition_time`),
  KEY `catalog` (`catalog`),
  KEY `played` (`played`),
  KEY `enabled` (`enabled`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `song_data`
--

CREATE TABLE IF NOT EXISTS `song_data` (
  `song_id` int(11) unsigned NOT NULL,
  `comment` text collate utf8_unicode_ci,
  `lyrics` text collate utf8_unicode_ci,
  `label` varchar(128) character set utf8 default NULL,
  `catalog_number` varchar(128) character set utf8 default NULL,
  `language` varchar(128) character set utf8 default NULL,
  UNIQUE KEY `song_id` (`song_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) character set utf8 default NULL,
  UNIQUE KEY `name` (`name`),
  KEY `map_id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag_map`
--

CREATE TABLE IF NOT EXISTS `tag_map` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `tag_id` int(11) unsigned NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `object_type` varchar(16) character set utf8 default NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `object_id` (`object_id`),
  KEY `object_type` (`object_type`),
  KEY `user_id` (`user`),
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_browse`
--

CREATE TABLE IF NOT EXISTS `tmp_browse` (
  `id` int(13) NOT NULL auto_increment,
  `sid` varchar(128) character set utf8 NOT NULL default '',
  `data` longtext collate utf8_unicode_ci NOT NULL,
  `object_data` longtext collate utf8_unicode_ci,
  PRIMARY KEY  (`sid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_playlist`
--

CREATE TABLE IF NOT EXISTS `tmp_playlist` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `session` varchar(32) character set utf8 default NULL,
  `type` varchar(32) character set utf8 default NULL,
  `object_type` varchar(32) character set utf8 default NULL,
  PRIMARY KEY  (`id`),
  KEY `session` (`session`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_playlist_data`
--

CREATE TABLE IF NOT EXISTS `tmp_playlist_data` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `tmp_playlist` int(11) unsigned NOT NULL,
  `object_type` varchar(32) character set utf8 default NULL,
  `object_id` int(11) unsigned NOT NULL,
  `track` int(11) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `tmp_playlist` (`tmp_playlist`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `update_info`
--

CREATE TABLE IF NOT EXISTS `update_info` (
  `key` varchar(128) character set utf8 default NULL,
  `value` varchar(255) character set utf8 default NULL,
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(128) character set utf8 default NULL,
  `fullname` varchar(128) character set utf8 default NULL,
  `email` varchar(128) character set utf8 default NULL,
  `password` varchar(64) character set utf8 default NULL,
  `access` tinyint(4) unsigned NOT NULL,
  `disabled` tinyint(1) unsigned NOT NULL default '0',
  `last_seen` int(11) unsigned NOT NULL default '0',
  `create_date` int(11) unsigned default NULL,
  `validation` varchar(128) character set utf8 default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_catalog`
--

CREATE TABLE IF NOT EXISTS `user_catalog` (
  `user` int(11) unsigned NOT NULL,
  `catalog` int(11) unsigned NOT NULL,
  `level` smallint(4) unsigned NOT NULL default '5',
  KEY `user` (`user`),
  KEY `catalog` (`catalog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_preference`
--

CREATE TABLE IF NOT EXISTS `user_preference` (
  `user` int(11) NOT NULL,
  `preference` int(11) unsigned NOT NULL default '0',
  `value` varchar(255) character set utf8 default NULL,
  KEY `user` (`user`),
  KEY `preference` (`preference`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_shout`
--

CREATE TABLE IF NOT EXISTS `user_shout` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user` int(11) NOT NULL,
  `text` text collate utf8_unicode_ci NOT NULL,
  `date` int(11) unsigned NOT NULL,
  `sticky` tinyint(1) unsigned NOT NULL default '0',
  `object_id` int(11) unsigned NOT NULL,
  `object_type` varchar(32) character set utf8 default NULL,
  PRIMARY KEY  (`id`),
  KEY `sticky` (`sticky`),
  KEY `date` (`date`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_vote`
--

CREATE TABLE IF NOT EXISTS `user_vote` (
  `user` int(11) unsigned NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `date` int(11) unsigned NOT NULL,
  KEY `user` (`user`),
  KEY `object_id` (`object_id`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video`
--

CREATE TABLE IF NOT EXISTS `video` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `file` varchar(4096) collate utf8_unicode_ci default NULL,
  `catalog` int(11) unsigned NOT NULL,
  `title` varchar(255) character set utf8 default NULL,
  `video_codec` varchar(255) character set utf8 default NULL,
  `audio_codec` varchar(255) character set utf8 default NULL,
  `resolution_x` mediumint(8) unsigned NOT NULL,
  `resolution_y` mediumint(8) unsigned NOT NULL,
  `time` int(11) unsigned NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `mime` varchar(255) character set utf8 default NULL,
  `addition_time` int(11) unsigned NOT NULL,
  `update_time` int(11) unsigned default NULL,
  `enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `file` (`file`(333)),
  KEY `enabled` (`enabled`),
  KEY `title` (`title`),
  KEY `addition_time` (`addition_time`),
  KEY `update_time` (`update_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
