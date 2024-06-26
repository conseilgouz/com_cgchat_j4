CREATE TABLE IF NOT EXISTS `#__cgchat` (
`id` int(12) NOT NULL AUTO_INCREMENT,
`text` text NOT NULL,
`name` varchar(255) NOT NULL,
`userid` int(12) NOT NULL,
`row` int(1) NOT NULL,
`color` varchar(6) NOT NULL,
`img` text NOT NULL,
`url` text NOT NULL,
`time` int(12) NOT NULL,
`token` int(12) NOT NULL,
`session` varchar(200) NOT NULL,
`ip` varchar(100) DEFAULT NULL,
`country` varchar(10) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='messages';
CREATE TABLE IF NOT EXISTS `#__cgchat_private` (
`id` int(12) NOT NULL AUTO_INCREMENT,
`text` text NOT NULL,
`fid` int(11) NOT NULL,
`from` varchar(255) NOT NULL,
`tid` int(11) NOT NULL,
`to` varchar(255) NOT NULL,
`row` int(1) NOT NULL,
`color` varchar(6) NOT NULL,
`img` text NOT NULL,
`time` int(12) NOT NULL,
`session` varchar(32) NOT NULL,
`key` int(7) NOT NULL,
`token` int(12) NOT NULL,
`ip` varchar(100) DEFAULT NULL,
`country` varchar(10) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='private messages';
CREATE TABLE IF NOT EXISTS `#__cgchat_private_offline` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`fid` int(11) NOT NULL,
`tid` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`color` varchar(6) NOT NULL,
`row` int(1) NOT NULL,
`msg` text NOT NULL,
`img` text NOT NULL,
`time` int(12) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Offline private messages';
CREATE TABLE IF NOT EXISTS `#__cgchat_bans` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`state` tinyint DEFAULT 0, 
`name` varchar(255) DEFAULT '',
`session` varchar(32) NOT NULL,
`ip` varchar(100) DEFAULT NULL,
`time` int(12) NOT NULL,
`time_off` int(12) NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Bans';
CREATE TABLE IF NOT EXISTS `#__cgchat_session` (
`name` varchar(255) NOT NULL,
`userid` int(12) NOT NULL,
`row` int(1) NOT NULL,
`img` text NOT NULL,
`time` int(12) NOT NULL,
`session` varchar(32) NOT NULL,
`private` int(1) NOT NULL,
`hidden` int(1) NOT NULL,
`key` int(7) NOT NULL,
`ip` varchar(100) DEFAULT NULL,
`country` varchar(10) DEFAULT NULL,
UNIQUE KEY `session` (`session`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='chat sessions';
CREATE TABLE IF NOT EXISTS `#__cgchat_icons` (
`id` int(12) NOT NULL AUTO_INCREMENT,
`code` varchar(15) NOT NULL,
`img` varchar(31) NOT NULL,
`ordering` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Icons definition';
INSERT INTO `#__cgchat_icons` (`code`, `img`, `ordering`) VALUES
				(':_(', 'crying.png', 11), ('8)', 'glasses.png', 10), (':S', 'confused.png', 8), (':O', 'surprise.png', 7),
				(':|', 'plain.png', 6), (':D', 'grin.png', 5), (':P', 'razz.png', 4), (';)', 'wink.png', 3), (':(', 'sad.png', 2),
				(':)', 'smile.png', 1), (':-*', 'kiss.png', 12), ('(!)', 'important.png', 13), ('(?)', 'help.png', 14),
				(':-|', 'plain.png', 21), (':-)', 'smile.png', 15), (':-(', 'sad.png', 16), (';-)', 'wink.png', 17),
				(':-P', 'razz.png', 18), (':-D', 'grin.png', 20), (':-O', 'surprise.png', 19), ('O.O', 'eek.png', 9),
				('xD', 'grin.png', 22);
		