CREATE TABLE `adr_scan_hosts` (
  `IP` int(11) unsigned default NULL,
  `TRACKING_METHOD` varchar(20) default NULL,
  `DNS` varchar(80) NOT NULL,
  `NETBIOS` varchar(90) NOT NULL,
  `OS` varchar(255) default NULL,
  `REPORT_TEMPLATE_ID` int(4) NOT NULL,
  `ACCOUNT` varchar(20) default NULL,
  KEY `IP` (`IP`),
  KEY `ACCOUNT` (`ACCOUNT`),
  KEY `REPORT_TEMPLATE_ID` (`REPORT_TEMPLATE_ID`),
  KEY `DNS` (`DNS`),
  KEY `NETBIOS` (`NETBIOS`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
