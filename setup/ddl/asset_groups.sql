CREATE TABLE `asset_groups` (
  `ACCOUNT` varchar(20) default NULL,
  `ASSET_ID` mediumint(3) NOT NULL,
  `TITLE` varchar(255) default NULL,
  `IP_START` int(11) unsigned NOT NULL default '0',
  `IP_END` int(11) unsigned NOT NULL default '0',
  `APPLIANCE_NAME` varchar(100) default NULL,
  `SCANNER_SN` varchar(100) default NULL,
  `COMMENTS` tinytext,
  `BIZ_IMPACT_RANK` int(3) default NULL,
  `BIZ_IMPACT_TITLE` varchar(100) default NULL,
  `LOCATION` varchar(100) default NULL,
  `CVSS_ENVIRO_CDP` varchar(100) default NULL,
  `CVSS_ENVIRO_TD` varchar(100) default NULL,
  `CVSS_ENVIRO_CR` varchar(100) default NULL,
  `CVSS_ENVIRO_IR` varchar(100) default NULL,
  `CVSS_ENVIRO_AR` varchar(100) default NULL,
  `LAST_UPDATE` datetime default NULL,
  `USER_LOGIN` varchar(50) default NULL,
  `FIRST_NAME` varchar(100) default NULL,
  `LAST_NAME` varchar(100) default NULL,
  `ROLE` varchar(50) default NULL,
  `DATE_ENTERED` datetime default NULL,
  KEY `DATE_ENTERED` (`DATE_ENTERED`),
  KEY `IP_START` (`IP_START`,`IP_END`),
  KEY `TITLE` (`TITLE`),
  KEY `ACCOUNT_AID` (`ACCOUNT`,`ASSET_ID`),
  KEY `ASSET_ID` (`ASSET_ID`),
  KEY `ACCOUNT` (`ACCOUNT`),
  KEY `CID_TITLE_IS_IE` (`ACCOUNT`,`TITLE`,`IP_START`,`IP_END`),
  KEY `CID_AID_IS_IE` (`ACCOUNT`,`ASSET_ID`,`IP_START`,`IP_END`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Asset Groups for account.'
