CREATE TABLE `scan_results` (
  `SCAN_ID` varchar(30) NOT NULL COMMENT 'The unique ID for the SCAN, use in JOIN',
  `SCAN_DATE` datetime default NULL,
  `IP` int(11) unsigned NOT NULL default '0' COMMENT 'IP of the HOST, stored in LONG, use INET_NTOA() to convert to human readable IP',
  `DNS` varchar(100) NOT NULL default '',
  `NETBIOS` varchar(100) NOT NULL default '',
  `QID` mediumint(3) NOT NULL default '0' COMMENT 'QualysID for the Vulnerability -- Join to qualys_kb table for more details on the vuln',
  `RESULT` MEDIUMTEXT COMMENT 'What Qualys found',
  `PROTOCOL` varchar(10) default NULL,
  `PORT` int(2) default NULL,
  `SSL_ENABLED` varchar(10) default NULL,
  `FQDN` varchar(100) default NULL,
  `ACCOUNT` varchar(20) default NULL,
  `DATE_ENTERED` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `IP` (`IP`),
  KEY `DNS` (`DNS`),
  KEY `NETBIOS` (`NETBIOS`),
  KEY `ACCOUNT` (`ACCOUNT`),
  KEY `QID` (`QID`),
  KEY `SCAN_ID` (`SCAN_ID`),
  KEY `SCAN_DATE` (`SCAN_DATE`),
  KEY `CID_SCAN_DATE` (`ACCOUNT`,`SCAN_DATE`),
  KEY `CID_SID_QID` (`ACCOUNT`,`SCAN_ID`,`QID`),
  KEY `CID_IP` (`ACCOUNT`,`IP`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Results of a Scan'
