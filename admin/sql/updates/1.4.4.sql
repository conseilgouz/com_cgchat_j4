ALTER TABLE `#__cgchat_session` ADD `ip` varchar(100) DEFAULT NULL AFTER `key`;
ALTER TABLE `#__cgchat_session` ADD `country` varchar(10) DEFAULT NULL AFTER `ip`;
