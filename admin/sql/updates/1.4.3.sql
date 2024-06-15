ALTER TABLE `#__cgchat_private` ADD `ip` varchar(100) DEFAULT NULL AFTER `token`;
ALTER TABLE `#__cgchat_private` ADD `country` varchar(10) DEFAULT NULL AFTER `ip`;
ALTER TABLE `#__cgchat_bans` ADD `state` tinyint DEFAULT 0 AFTER `id`;
ALTER TABLE `#__cgchat_bans` ADD `name` varchar(255) DEFAULT '' AFTER `state`;
ALTER TABLE `#__cgchat_bans` ADD `time_off` int(12) DEFAULT NULL AFTER `time``;