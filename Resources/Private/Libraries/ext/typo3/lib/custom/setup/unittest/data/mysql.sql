--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- $Id: mysql.sql 15921 2012-07-02 15:08:25Z nsendetzky $
--


-- Do not enable for setup as this hides errors
-- SET NAMES 'utf8';


--
-- TYPO3 table strutures
--

CREATE TABLE IF NOT EXISTS fe_users (
  `uid` int(11) unsigned NOT NULL auto_increment,
  `pid` int(11) unsigned DEFAULT '0' NOT NULL,
  `tstamp` int(11) unsigned DEFAULT '0' NOT NULL,
  `username` varchar(50) DEFAULT '' NOT NULL,
  `password` varchar(40) DEFAULT '' NOT NULL,
  `usergroup` tinytext,
  `disable` tinyint(4) unsigned DEFAULT '0' NOT NULL,
  `starttime` int(11) unsigned DEFAULT '0' NOT NULL,
  `endtime` int(11) unsigned DEFAULT '0' NOT NULL,
  `name` varchar(80) DEFAULT '' NOT NULL,
  `address` varchar(255) DEFAULT '' NOT NULL,
  `telephone` varchar(20) DEFAULT '' NOT NULL,
  `fax` varchar(20) DEFAULT '' NOT NULL,
  `email` varchar(80) DEFAULT '' NOT NULL,
  `crdate` int(11) unsigned DEFAULT '0' NOT NULL,
  `cruser_id` int(11) unsigned DEFAULT '0' NOT NULL,
  `lockToDomain` varchar(50) DEFAULT '' NOT NULL,
  `deleted` tinyint(3) unsigned DEFAULT '0' NOT NULL,
  `uc` blob,
  `title` varchar(40) DEFAULT '' NOT NULL,
  `zip` varchar(10) DEFAULT '' NOT NULL,
  `city` varchar(50) DEFAULT '' NOT NULL,
  `country` varchar(40) DEFAULT '' NOT NULL,
  `www` varchar(80) DEFAULT '' NOT NULL,
  `company` varchar(80) DEFAULT '' NOT NULL,
  `image` tinytext,
  `TSconfig` text,
  `fe_cruser_id` int(10) unsigned DEFAULT '0' NOT NULL,
  `lastlogin` int(10) unsigned DEFAULT '0' NOT NULL,
  `is_online` int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (`uid`),
  KEY parent (`pid`,`username`),
  KEY username (`username`),
  KEY is_online (`is_online`)
);

CREATE TABLE IF NOT EXISTS `tt_address` (
	-- Unique address id
	`uid` BIGINT NOT NULL AUTO_INCREMENT,
	-- Defines the storage page in TYPO3
	`pid` int(11) NOT NULL default '0',
	-- Defines the creation/modification time in TYPO3
	`tstamp` int(11) NOT NULL default '0',
	-- Defines the visibility in TYPO3
	`hidden` tinyint(4) NOT NULL default '0',
	-- Deleted flag in TYPO3
	`deleted` tinyint(3) default '0',
	-- Name ( firstname + middlename + lastname ) in TYPO3 (obsolete)
	`name` tinytext NOT NULL,
	-- Middle name property in TYPO3
	`middle_name` tinytext NOT NULL,
	-- Birthday of User (TYPO3)
	`birthday` int(11) NOT NULL default '0',
	-- Mobile phone in TYPO3
	`mobile` varchar(30) NOT NULL default '',
	-- Building property of addresses in TYPO3
	`building` varchar(20) NOT NULL default '',
	-- Room property of addresses in TYPO3
	`room` varchar(15) NOT NULL default '',
	-- Image property of addresses in TYPO3
	`image` tinyblob NOT NULL,
	-- Description property of addresses in TYPO3
	`description` text NOT NULL,
	-- Addressgroup property of addresses in TYPO3
	`addressgroup` int(11) NOT NULL default '0',
	-- site id, references mshop_global_site.id
	`tx_mshop_siteid` int(11) NOT NULL default '0',
	-- reference id for customer // refid
	`tx_mshop_fe_user_uid` text,
	-- company name
	`company` varchar(80) NOT NULL default '',
	-- customer/supplier categorization ( f = female, m = male ) // salutation
	`gender` varchar(1) NOT NULL default '',
	-- title of the customer/supplier
	`title` varchar(40) NOT NULL default '',
	-- first name of customer/supplier // firstname
	`first_name` tinytext NOT NULL,
	-- last name of customer/supplier // lastname
	`last_name` tinytext NOT NULL,
	-- Depending on country, e.g. house name // address1
	`address` tinytext NOT NULL,
	-- Depending on country, e.g. street // address2
	`tx_mshop_address2` text,
	-- Depending on country, e.g. county/suburb // address3
	`tx_mshop_address3` text,
	-- postal code of customer/supplier // postal
	`zip` varchar(20) NOT NULL default '',
	-- city name of customer/supplier
	`city` varchar(80) NOT NULL default '',
	-- state name of customer/supplier // state
	`region` varchar(100) NOT NULL default '',
	-- language id // langid
	`tx_mshop_langid` char(2) NOT NULL default '',
	-- Country id the customer/supplier is living in // countryid
	`country` varchar(100) NOT NULL default '',
	-- Telephone number of the customer/supplier // telephone
	`phone` varchar(30) NOT NULL default '',
	-- Email of the customer/supplier
	`email` varchar(80) NOT NULL default '',
	-- Telefax of the customer/supplier // telefax
	`fax` varchar(30) NOT NULL default '',
	-- Website of the customer/supplier // website
	`www` varchar(80) NOT NULL default '',
	-- Position  // pos
	`tx_mshop_pos` int(11) NOT NULL default '0',
	PRIMARY KEY  (`uid`),
	KEY `parent` (`pid`),
	KEY `pid` (`pid`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;




START TRANSACTION;


SET @siteid = ( SELECT `id` FROM `mshop_locale_site` WHERE `code` = 'unittest' );

--
-- Typo3 tables
--
DELETE FROM `fe_users` WHERE `lockToDomain` = 'www.unittest.metaways.de';
DELETE FROM `tt_address` WHERE `tx_mshop_siteid` = @siteid;


--
-- Typo3 frontend users
--
INSERT INTO `fe_users` ( `tstamp`, `username`, `password`, `crdate`, `disable`, `lockToDomain`, `email`) VALUES ( 1294916626, 'unitCustomer1@metaways.de', '5f4dcc3b5aa765d61d8327deb882cf99', 1294916626, 0, 'www.unittest.metaways.de', 'unitCustomer1@metaways.de');
SET @fe_userid1 = ( SELECT LAST_INSERT_ID() );
INSERT INTO `fe_users` ( `tstamp`, `username`, `password`, `crdate`, `disable`, `lockToDomain`, `email`) VALUES ( 1295917626, 'unitCustomer2@metaways.de', '5f4dcc3b5aa765d61d8327deb882cf99', 1295916626, 0, 'www.unittest.metaways.de', 'unitCustomer2@metaways.de');
SET @fe_userid2 = ( SELECT LAST_INSERT_ID() );
INSERT INTO `fe_users` ( `tstamp`, `username`, `password`, `crdate`, `disable`, `lockToDomain`, `email`) VALUES ( 1295918626, 'unitCustomer3@metaways.de', '5f4dcc3b5aa765d61d8327deb882cf99', 1295916626, 1, 'www.unittest.metaways.de', 'unitCustomer3@metaways.de');
SET @fe_userid3 = ( SELECT LAST_INSERT_ID() );



--
-- Typo3 mshop addresses for frontend users
--
INSERT INTO `tt_address`( `tx_mshop_siteid`, `tstamp`, `tx_mshop_fe_user_uid`, `company`, `gender`, `title`, `first_name`, `last_name`, `address`, `tx_mshop_address2`, `zip`, `city`, `region`, `tx_mshop_langid`, `country`, `phone`, `email`, `fax`, `www`, `tx_mshop_pos` ) VALUES (@siteid, 0, @fe_userid1, 'Metaways GmbH', 'm', 'Dr.', 'Max', 'Mustermann', 'Musterstraße', '1a', '20001', 'Musterstadt', 'HH', 'DE', 'DE', '01234567890', 'arcavias@metaways.de', '01234567890', 'www.metaways.de', '0');
INSERT INTO `tt_address`( `tx_mshop_siteid`, `tstamp`, `tx_mshop_fe_user_uid`, `company`, `gender`, `title`, `first_name`, `last_name`, `address`, `tx_mshop_address2`, `zip`, `city`, `region`, `tx_mshop_langid`, `country`, `phone`, `email`, `fax`, `www`, `tx_mshop_pos` ) VALUES (@siteid, 0, @fe_userid2, 'Metaways GmbH', 'f', 'Prof. Dr.', 'Erika', 'Mustermann', 'Heidestraße', '17', '45632', 'Köln', 'HH', 'DE', 'DE', '09876543210', 'arcavias@metaways.de', '09876543210', 'www.metaways.de', '1');
INSERT INTO `tt_address`( `tx_mshop_siteid`, `tstamp`, `tx_mshop_fe_user_uid`, `company`, `gender`, `title`, `first_name`, `last_name`, `address`, `tx_mshop_address2`, `zip`, `city`, `region`, `tx_mshop_langid`, `country`, `phone`, `email`, `fax`, `www`, `tx_mshop_pos` ) VALUES (@siteid, 0, @fe_userid2, 'Metaways GmbH', 'm', '', 'Franz-Xaver', 'Gabler', 'Phantasiestraße', '2', '23643', 'Berlin', 'Berlin', 'de', 'de', '01234509876', 'arcavias@metaways.de', '055544333212', 'www.metaways.de', '1');
INSERT INTO `tt_address`( `tx_mshop_siteid`, `tstamp`, `tx_mshop_fe_user_uid`, `company`, `gender`, `title`, `first_name`, `last_name`, `address`, `tx_mshop_address2`, `zip`, `city`, `region`, `tx_mshop_langid`, `country`, `phone`, `email`, `fax`, `www`, `tx_mshop_pos` ) VALUES (@siteid, 0, @fe_userid3, 'unitcompany', '', 'unittitle', 'unitfirstname', 'unitlastname', 'unitaddress1', 'unitaddress2', 'unitpostal', 'unitcity', 'unitstate', 'de', 'de', '055123456', 'arcavias@metaways.de', '055123456', 'www.metaways.de', '2');



COMMIT;
