CREATE TABLE `adr_scan_vulns` (
  `IP` int(11) unsigned default NULL,
  `QID` int(2) default NULL,
  `PORT` int(2) default NULL,
  `PROTOCOL` varchar(15) default NULL,
  `TYPE` varchar(20) default NULL,
  `SSL_ENABLED` varchar(10) default NULL,
  `RESULT` text,
  `FIRST_FOUND` datetime default NULL,
  `LAST_FOUND` datetime default NULL,
  `TIMES_FOUND` int(2) default NULL,
  `VULN_STATUS` varchar(15) default NULL,
  `CVSS_FINAL` float default NULL,
  `TICKET_NUMBER` int(3) default NULL,
  `TICKET_STATE` varchar(10) default NULL,
  `REPORT_TEMPLATE_ID` int(4) NOT NULL,
  `ACCOUNT` varchar(20) default NULL,
  KEY `IP` (`IP`),
  KEY `QID` (`QID`),
  KEY `ACCOUNT` (`ACCOUNT`),
  KEY `TYPE` (`TYPE`),
  KEY `REPORT_TEMPLATE_ID` (`REPORT_TEMPLATE_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
