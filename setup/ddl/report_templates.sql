CREATE TABLE `report_templates` (
  `ID` int(3) default NULL,
  `TYPE` varchar(30) default NULL,
  `TEMPLATE_TYPE` varchar(30) default NULL,
  `TITLE` varchar(255) default NULL,
  `USER_LOGIN` varchar(20) default NULL,
  `USER_FIRSTNAME` varchar(50) default NULL,
  `USER_LASTNAME` varchar(50) default NULL,
  `LAST_UPDATE` datetime default NULL,
  `GLOBAL` int(1) default NULL,
  `ACCOUNT` varchar(30) default NULL,
  KEY `TEMPLATE_ID` (`ID`),
  KEY `ACCOUNT` (`ACCOUNT`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
