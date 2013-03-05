CREATE TABLE `scan_run` (
  `SCAN_ID` varchar(30) NOT NULL COMMENT 'Scan Reference ID',
  `TITLE` varchar(255) character set utf8 collate utf8_unicode_ci default NULL,
  `TYPE` varchar(50) NOT NULL COMMENT 'Scan Type:  On-Demand, Scheduled, etc',
  `SCAN_DATE` datetime default NULL,
  `SCAN_STATUS` varchar(20) default NULL COMMENT 'Status of scan.  Finished, Error, Canceled, etc',
  `SUB_STATE` varchar(20) default NULL COMMENT 'Sub State of the scan.  no_host_alive etc',
  `TARGETS` text COMMENT 'IPs targeted in the scan',
  `ASSET_GROUPS` text COMMENT 'Asset groups scanned',
  `OPTION_PROFILE` varchar(255) default NULL,
  `OPTION_PROFILE_DEFAULT_FLAG` int(1) default '0' COMMENT '1 if default option profile was used',
  `ACCOUNT` varchar(20) default NULL,
  `DATE_ENTERED` datetime default NULL,
  KEY `TITLE` (`TITLE`),
  KEY `SCAN_ID_2` (`SCAN_ID`,`SCAN_DATE`,`TITLE`,`ACCOUNT`),
  KEY `TYPE` (`TYPE`),
  KEY `ACCOUNT` (`ACCOUNT`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Details on scans that have ran'
