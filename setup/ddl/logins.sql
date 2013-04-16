CREATE TABLE `logins` (
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(70) DEFAULT NULL,
  `api_url` varchar(50) DEFAULT NULL,
  `account` varchar(50) DEFAULT NULL,
  KEY `username` (`username`),
  KEY `account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
