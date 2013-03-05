CREATE TABLE `adr_scan_asset_groups` (
  `IP` int(11) unsigned default NULL,
  `ASSET_GROUP_TITLE` varchar(255) default NULL,
  `ACCOUNT` varchar(20) default NULL,
  KEY `IP` (`IP`),
  KEY `ACCOUNT` (`ACCOUNT`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
