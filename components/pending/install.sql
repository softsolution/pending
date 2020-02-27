DROP TABLE IF EXISTS `#__pending`;
CREATE TABLE IF NOT EXISTS `#__pending` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `pubdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enddate` date NOT NULL,
  `is_end` tinyint(1) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `content` longtext,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `meta_desc` text NOT NULL,
  `meta_keys` text NOT NULL,
  `showtitle` tinyint(1) NOT NULL DEFAULT '1',
  `showdate` tinyint(1) NOT NULL DEFAULT '1',
  `showlatest` tinyint(1) NOT NULL DEFAULT '1',
  `showpath` tinyint(1) NOT NULL DEFAULT '1',
  `comments` tinyint(1) NOT NULL DEFAULT '1',
  `canrate` tinyint(1) NOT NULL DEFAULT '1',
  `pagetitle` varchar(255) NOT NULL,
  `url` varchar(100) NOT NULL,
  `tpl` varchar(50) NOT NULL DEFAULT 'com_content_read.tpl',
  `tags` varchar(200) NOT NULL,
  `access` text NOT NULL,
  `createmenu` varchar(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;