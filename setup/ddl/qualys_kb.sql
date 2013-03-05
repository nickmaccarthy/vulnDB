CREATE TABLE `qualys_kb` (
  `QID` int(5) default NULL COMMENT 'Unique ID for Vulnerability.  Use in JOIN',
  `TYPE` varchar(50) default NULL COMMENT 'Type of vulnerability -- confirmed, potential, etc',
  `SEVERITY` int(2) default NULL,
  `TITLE` varchar(255) default NULL,
  `CATEGORY` varchar(255) default NULL,
  `LAST_UPDATE` datetime default NULL,
  `BUGTRAQ_ID` text,
  `PATCHABLE` int(1) default NULL,
  `CVE_ID` text,
  `DIAGNOSIS` text,
  `CONSEQUENCE` text,
  `SOLUTION` text,
  `COMPLIANCE_TYPE` varchar(50) default NULL,
  `COMPLIANCE_SECTION` varchar(50) default NULL,
  `COMPLIANCE_DESCRIPTION` tinytext,
  `CVSS_BASE` float default NULL,
  `CVSS_TEMPORAL` float default NULL,
  `CVSS_ACCESS_VECTOR` varchar(50) default NULL,
  `CVSS_ACCESS_COMPLEXITY` varchar(50) default NULL,
  `CVSS_AUTENTICATION` varchar(50) default NULL,
  `CVSS_CONFIDENTIALITY_IMPACT` varchar(50) default NULL,
  `CVSS_INTEGRITY_IMPACT` varchar(50) default NULL,
  `CVSS_AVAILABILITY_IMPACT` varchar(50) default NULL,
  `CVSS_EXPLOITABILITY` varchar(50) default NULL,
  `CVSS_REMEDIATION_LEVEL` varchar(50) default NULL,
  `CVSS_REPORT_CONFIDENCE` varchar(50) default NULL,
  `PCI_FLAG` int(1) default NULL,
  `DATE_ENTERED` datetime default NULL,
  KEY `TITLE` (`TITLE`),
  KEY `QID_2` (`QID`,`TYPE`,`SEVERITY`),
  KEY `PCI_FLAG` (`PCI_FLAG`),
  KEY `SEVERITY` (`SEVERITY`),
  KEY `TYPE` (`TYPE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Qualys Knowledge Base Table.  Contains Details for vulnerabi'
