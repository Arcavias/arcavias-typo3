--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- $Id: mysql.sql 15921 2012-07-02 15:08:25Z nsendetzky $
--


-- Do not enable for setup as this hides errors
-- SET NAMES 'utf8';


--
-- TYPO3 table strutures
--

CREATE TABLE IF NOT EXISTS `fe_users` (
	`uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`pid` int(11) unsigned NOT NULL DEFAULT '0',
	`tstamp` int(11) unsigned NOT NULL DEFAULT '0',
	`username` varchar(50) DEFAULT NULL,
	`password` varchar(40) DEFAULT NULL,
	`usergroup` tinytext,
	`disable` tinyint(4) unsigned NOT NULL DEFAULT '0',
	`starttime` int(11) unsigned NOT NULL DEFAULT '0',
	`endtime` int(11) unsigned NOT NULL DEFAULT '0',
	`name` varchar(100) DEFAULT '',
	`first_name` varchar(50) DEFAULT NULL,
	`middle_name` varchar(50) DEFAULT NULL,
	`last_name` varchar(50) DEFAULT NULL,
	`address` varchar(255) DEFAULT NULL,
	`telephone` varchar(20) DEFAULT NULL,
	`fax` varchar(20) DEFAULT NULL,
	`email` varchar(80) DEFAULT NULL,
	`crdate` int(11) unsigned NOT NULL DEFAULT '0',
	`cruser_id` int(11) unsigned NOT NULL DEFAULT '0',
	`lockToDomain` varchar(50) DEFAULT NULL,
	`deleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
	`uc` blob,
	`title` varchar(40) DEFAULT NULL,
	`zip` varchar(20) DEFAULT '',
	`city` varchar(50) DEFAULT NULL,
	`country` varchar(60) DEFAULT '',
	`www` varchar(80) DEFAULT NULL,
	`company` varchar(80) DEFAULT NULL,
	`image` tinytext,
	`TSconfig` text,
	`fe_cruser_id` int(10) unsigned NOT NULL DEFAULT '0',
	`lastlogin` int(10) unsigned NOT NULL DEFAULT '0',
	`is_online` int(10) unsigned NOT NULL DEFAULT '0',
	`felogin_redirectPid` tinytext,
	`felogin_forgotHash` varchar(80) DEFAULT NULL,
	`tx_extbase_type` varchar(255) DEFAULT NULL,
	`static_info_country` char(3) NOT NULL DEFAULT '',
	`zone` varchar(45) NOT NULL DEFAULT '',
	`language` char(2) NOT NULL DEFAULT '',
	`gender` int(11) unsigned NOT NULL DEFAULT '99',
	`cnum` varchar(50) NOT NULL DEFAULT '',
	`status` int(11) unsigned NOT NULL DEFAULT '0',
	`comments` text NOT NULL,
	`by_invitation` tinyint(4) unsigned NOT NULL DEFAULT '0',
	`module_sys_dmail_html` tinyint(3) unsigned NOT NULL DEFAULT '0',
	`terms_acknowledged` tinyint(4) unsigned NOT NULL DEFAULT '0',
	`token` varchar(32) NOT NULL DEFAULT '',
	`tx_srfeuserregister_password` blob NOT NULL,
	`date_of_birth` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`uid`),
	KEY `parent` (`pid`,`username`),
	KEY `username` (`username`),
	KEY `is_online` (`is_online`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `static_countries` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `cn_iso_2` char(2) DEFAULT '',
  `cn_iso_3` char(3) DEFAULT '',
  `cn_iso_nr` int(11) unsigned DEFAULT '0',
  `cn_parent_tr_iso_nr` int(11) unsigned DEFAULT '0',
  `cn_official_name_local` varchar(128) DEFAULT '',
  `cn_official_name_en` varchar(128) DEFAULT '',
  `cn_capital` varchar(45) DEFAULT '',
  `cn_tldomain` char(2) DEFAULT '',
  `cn_currency_iso_3` char(3) DEFAULT '',
  `cn_currency_iso_nr` int(10) unsigned DEFAULT '0',
  `cn_phone` int(10) unsigned DEFAULT '0',
  `cn_eu_member` tinyint(3) unsigned DEFAULT '0',
  `cn_address_format` tinyint(3) unsigned DEFAULT '0',
  `cn_zone_flag` tinyint(4) DEFAULT '0',
  `cn_short_local` varchar(70) DEFAULT '',
  `cn_short_en` varchar(50) DEFAULT '',
  `cn_uno_member` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=latin1;


START TRANSACTION;


SET @siteid = ( SELECT `id` FROM `mshop_locale_site` WHERE `code` = 'unittest' );

--
-- Typo3 tables
--
DELETE FROM `fe_users` WHERE `lockToDomain` = 'www.unittest.metaways.de';


--
-- Typo3 frontend users
--
INSERT INTO `fe_users` ( `lockToDomain`, `name`, `username`, `gender`, `company`, `title`, `first_name`, `last_name`, `address`, `zip`, `city`, `zone`, `language`, `static_info_country`, `telephone`, `email`, `fax`, `www`, `date_of_birth`, `disable`, `password`, `tstamp`, `crdate`)
	VALUES ( 'www.unittest.metaways.de', 'Max Mustermann', 'unitCustomer1@metaways.de', 0, 'Metaways GmbH', 'Dr.', 'Max', 'Mustermann', 'Musterstraße 1a', '20001', 'Musterstadt', 'Hamburg', 'de', 'DEU', '01234567890', 'unitCustomer1@metaways.de', '01234567890', 'www.metaways.de', 157762800, '0', '5f4dcc3b5aa765d61d8327deb882cf99', 1294916626, 1294916616);
SET @fe_userid1 = ( SELECT LAST_INSERT_ID() );
INSERT INTO `fe_users` ( `lockToDomain`, `name`, `username`, `gender`, `company`, `title`, `first_name`, `last_name`, `address`, `zip`, `city`, `zone`, `language`, `static_info_country`, `telephone`, `email`, `fax`, `www`, `date_of_birth`, `disable`, `password`, `tstamp`, `crdate`)
	VALUES ( 'www.unittest.metaways.de', 'Erika Mustermann', 'unitCustomer2@metaways.de', 1, 'Metaways GmbH', 'Prof. Dr.', 'Erika', 'Mustermann', 'Heidestraße 17', '45632', 'Köln', '', 'de', 'DEU', '09876543210', 'unitCustomer2@metaways.de', '09876543210', 'www.metaways.de', 315529200, '1', '5f4dcc3b5aa765d61d8327deb882cf99', 1295916627, 1294916617);
SET @fe_userid2 = ( SELECT LAST_INSERT_ID() );
INSERT INTO `fe_users` ( `lockToDomain`, `name`, `username`, `gender`, `company`, `title`, `first_name`, `last_name`, `address`, `zip`, `city`, `zone`, `language`, `static_info_country`, `telephone`, `email`, `fax`, `www`, `date_of_birth`, `disable`, `password`, `tstamp`, `crdate`)
	VALUES ( 'www.unittest.metaways.de', 'Franz-Xaver Gabler', 'unitCustomer3@metaways.de', 0, 'Metaways GmbH', '', 'Franz-Xaver', 'Gabler', 'Phantasiestraße 2', '23643', 'Berlin', 'Berlin', 'de', 'DEU', '01234509876', 'unitCustomer3@metaways.de', '055544333212', 'www.metaways.de', 473382000, '0', '5f4dcc3b5aa765d61d8327deb882cf99', 1295916628, 1294916618);
SET @fe_userid3 = ( SELECT LAST_INSERT_ID() );

--
-- Typo3 countries
--
INSERT INTO `static_countries` (`pid`, `deleted`, `cn_iso_2`, `cn_iso_3`, `cn_iso_nr`, `cn_parent_tr_iso_nr`, `cn_official_name_local`, `cn_official_name_en`, `cn_capital`, `cn_tldomain`, `cn_currency_iso_3`, `cn_currency_iso_nr`, `cn_phone`, `cn_eu_member`, `cn_address_format`, `cn_zone_flag`, `cn_short_local`, `cn_short_en`, `cn_uno_member`)
SELECT 0, 0, 'DE', 'DEU', 276, 155, 'Bundesrepublik Deutschland', 'Federal Republic of Germany', 'Berlin', 'de', 'EUR', 978, 49, 1, 1, 0, 'Deutschland', 'Germany', 1 FROM DUAL WHERE NOT EXISTS ( SELECT `cn_iso_2` FROM `static_countries` WHERE `cn_iso_2` = 'DE' );


COMMIT;
