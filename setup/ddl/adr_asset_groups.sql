CREATE TABLE `adr_asset_groups` (
  `IP` int(11) unsigned default NULL,
  `ASSET_GROUP_TITLE` varchar(255) default NULL,
  `ACCOUNT` varchar(20) default NULL,
  `REPORT_TEMPLATE_ID` int(4) NOT NULL,
  `DATE_ENTERED` DATETIME,
  KEY `IP` (`IP`),
  KEY `ACCOUNT` (`ACCOUNT`),
  KEY `DATE_ENTERED` (`DATE_ENTERED`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
