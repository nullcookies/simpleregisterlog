-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 03, 2017 at 05:17 PM
-- Server version: 5.5.44-0+deb8u1
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table structure for table `T_LOG`
--

CREATE TABLE IF NOT EXISTS `T_LOG` (
  `ID_LOG` int(11) NOT NULL,
  `ID_SERVICE` int(11) NOT NULL,
  `NAME` varchar(3000) DEFAULT NULL,
  `SURNAME` varchar(3000) DEFAULT NULL,
  `PATRONYMIC` varchar(3000) DEFAULT NULL,
  `MSISDN` varchar(1000) DEFAULT NULL,
  `EMAIL` varchar(1000) DEFAULT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `QUESTION_ID` int(11) NULL,
  `ANSWER_ID` int(11) NULL, 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `T_LOG`
--
ALTER TABLE `T_LOG`
  ADD PRIMARY KEY (`ID_LOG`),
  ADD KEY `ID_SERVICE` (`ID_SERVICE`),
  ADD KEY `DT` (`DT`),
  ADD KEY `NAME` (`NAME`(255)),
  ADD KEY `SURNAME` (`SURNAME`(255)),
  ADD KEY `PATRONYMIC` (`PATRONYMIC`(255)),
  ADD KEY `MSISDN` (`MSISDN`(255)),
  ADD KEY `EMAIL` (`EMAIL`(255));

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `T_LOG`
--
ALTER TABLE `T_LOG`
  MODIFY `ID_LOG` int(11) NOT NULL AUTO_INCREMENT;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simpleregisterlog`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`simpleregister`@`localhost` PROCEDURE `setParameter`(IN `name` VARCHAR(3000) CHARSET utf8, IN `val` VARCHAR(3000) CHARSET utf8)
    NO SQL
BEGIN 
	if (name = 'id_service') then
    	set @id_service = val;
    elseif (name = 'id_services') then
        set @id_services = val;
	end if;
END$$

--
-- Functions
--
CREATE DEFINER=`simpleregister`@`localhost` FUNCTION `getFakeId`() RETURNS int(11)
    NO SQL
begin
    return if(@fakeId, @fakeId:=@fakeId+1, @fakeId:=1);
end$$

CREATE DEFINER=`simpleregister`@`localhost` FUNCTION `getParameter`(`name` VARCHAR(3000)) RETURNS varchar(3000) CHARSET utf8
    NO SQL
BEGIN
	declare l_val varchar(100);

	if (name = 'id_service') then
    	set l_val = @id_service;
    elseif (name = 'id_services') then
    	set l_val = @id_services; 
	end if; 

	return l_val;
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `T_MEMBER`
--

CREATE TABLE IF NOT EXISTS `T_MEMBER` (
  `ID_MEMBER` int(11) NOT NULL,
  `LOGIN` varchar(100) NOT NULL,
  `PASSWD` varchar(100) NOT NULL,
  `ID_STATUS` int(11) NOT NULL,
  `ID_SERVICE` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `T_MEMBER`
--

INSERT INTO `T_MEMBER` (`ID_MEMBER`, `LOGIN`, `PASSWD`, `ID_STATUS`, `ID_SERVICE`) VALUES
(1, 'root', 'BE-IntegeR', 1, NULL),
(2, 'test', 'test', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `T_MEMBER_ROLE`
--

CREATE TABLE IF NOT EXISTS `T_MEMBER_ROLE` (
  `ID_MEMBER` int(11) NOT NULL,
  `ID_ROLE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `T_MEMBER_ROLE`
--

INSERT INTO `T_MEMBER_ROLE` (`ID_MEMBER`, `ID_ROLE`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `T_MEMBER_SERVICE`
--

CREATE TABLE IF NOT EXISTS `T_MEMBER_SERVICE` (
  `ID_MEMBER` int(11) NOT NULL,
  `ID_SERVICE` int(11) NOT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `T_MEMBER_SERVICE`
--

INSERT INTO `T_MEMBER_SERVICE` (`ID_MEMBER`, `ID_SERVICE`, `DT`) VALUES
(2, 1, '2017-04-24 15:01:59');

-- --------------------------------------------------------

--
-- Table structure for table `T_MEMBER_SHOW_FIELD`
--

CREATE TABLE IF NOT EXISTS `T_MEMBER_SHOW_FIELD` (
  `ID_SHOW_FIELD` int(11) NOT NULL,
  `ID_MEMBER` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `T_MEMBER_SHOW_FIELD`
--

INSERT INTO `T_MEMBER_SHOW_FIELD` (`ID_SHOW_FIELD`, `ID_MEMBER`) VALUES
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 16);

-- --------------------------------------------------------

--
-- Table structure for table `T_MODULE`
--

CREATE TABLE IF NOT EXISTS `T_MODULE` (
  `ID_MODULE` int(11) NOT NULL,
  `NAME` varchar(1000) NOT NULL,
  `MODULE` varchar(1000) NOT NULL,
  `PATH` varchar(1000) NOT NULL,
  `ORDER_BY_MODULE` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `T_MODULE`
--

INSERT INTO `T_MODULE` (`ID_MODULE`, `NAME`, `MODULE`, `PATH`, `ORDER_BY_MODULE`) VALUES
(1, 'Логи запросов', 'Log', '/stat/log', 1);

-- --------------------------------------------------------

--
-- Table structure for table `T_MODULE_ROLE`
--

CREATE TABLE IF NOT EXISTS `T_MODULE_ROLE` (
  `ID_MODULE_ROLE` int(11) NOT NULL,
  `ID_MODULE` int(11) NOT NULL,
  `ID_ROLE` int(11) NOT NULL,
  `ID_ACCESS_TYPE` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `T_MODULE_ROLE`
--

INSERT INTO `T_MODULE_ROLE` (`ID_MODULE_ROLE`, `ID_MODULE`, `ID_ROLE`, `ID_ACCESS_TYPE`) VALUES
(1, 1, 1, 7),
(2, 1, 2, 7);

-- --------------------------------------------------------

--
-- Table structure for table `T_ROLE`
--

CREATE TABLE IF NOT EXISTS `T_ROLE` (
  `ID_ROLE` int(11) NOT NULL,
  `DESCRIPTION` varchar(100) NOT NULL,
  `IS_DISPLAY` int(11) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `ORDER_BY_ROLES` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `T_ROLE`
--

INSERT INTO `T_ROLE` (`ID_ROLE`, `DESCRIPTION`, `IS_DISPLAY`, `NAME`, `ORDER_BY_ROLES`) VALUES
(1, 'Администратор', 0, 'root', 1),
(2, 'Пользователь', 1, 'user', 2);

-- --------------------------------------------------------

--
-- Table structure for table `T_SERVICE`
--

CREATE TABLE IF NOT EXISTS `T_SERVICE` (
  `ID_SERVICE` int(11) NOT NULL,
  `NAME` varchar(3000) NOT NULL,
  `NOTE` varchar(4000) NOT NULL,
  `IS_ACTIVE` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `T_SERVICE`
--

INSERT INTO `T_SERVICE` (`ID_SERVICE`, `NAME`, `NOTE`, `IS_ACTIVE`) VALUES
(1, 'Test', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `T_SHOW_FIELD`
--

CREATE TABLE IF NOT EXISTS `T_SHOW_FIELD` (
  `ID_SHOW_FIELD` int(11) NOT NULL,
  `NAME` varchar(1000) NOT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `T_SHOW_FIELD`
--

INSERT INTO `T_SHOW_FIELD` (`ID_SHOW_FIELD`, `NAME`, `DT`) VALUES
(1, 'name', '2017-03-14 08:28:18'),
(2, 'surname', '2017-03-14 08:28:27'),
(3, 'patronymic', '2017-03-14 08:28:45'),
(4, 'msisdn', '2017-03-14 08:28:56'),
(5, 'email', '2017-03-14 08:29:02');

-- --------------------------------------------------------

--
-- Stand-in structure for view `V_LOG`
--
CREATE TABLE IF NOT EXISTS `V_LOG` (
`ID_LOG` int(11)
,`ID_SERVICE` int(11)
,`NAME` varchar(3000)
,`SURNAME` varchar(3000)
,`PATRONYMIC` varchar(3000)
,`MSISDN` varchar(1000)
,`EMAIL` varchar(1000)
,`DT` timestamp
,`SERVICE` varchar(3000)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `V_MEMBER_ROLE`
--
CREATE TABLE IF NOT EXISTS `V_MEMBER_ROLE` (
`id_member` int(11)
,`roles_level` int(11)
,`roles_list` text
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `V_SERVICE`
--
CREATE TABLE IF NOT EXISTS `V_SERVICE` (
`id_service` int(11)
,`name` varchar(3000)
);

-- --------------------------------------------------------

--
-- Structure for view `V_LOG`
--
DROP TABLE IF EXISTS `V_LOG`;

CREATE or replace
ALGORITHM=UNDEFINED 
DEFINER=`simpleregister`@`localhost` 
SQL SECURITY DEFINER 
VIEW `V_LOG` AS 
select	`T_LOG`.`ID_LOG` AS `ID_LOG`,
		`T_LOG`.`ID_SERVICE` AS `ID_SERVICE`,
		`T_LOG`.`NAME` AS `NAME`,
		`T_LOG`.`SURNAME` AS `SURNAME`,
		`T_LOG`.`PATRONYMIC` AS `PATRONYMIC`,
		`T_LOG`.`MSISDN` AS `MSISDN`,
		`T_LOG`.`EMAIL` AS `EMAIL`,
		`T_LOG`.`DT` AS `DT`,
		`tserv`.`NAME` AS `SERVICE`,
		`T_LOG`.`QUESTION_ID` AS `QUESTION_ID`,
		`T_LOG`.`ANSWER_ID` AS `ANSWER_ID`
from (`T_LOG` 
	left join `T_SERVICE` `tserv` 
	on((`T_LOG`.`ID_SERVICE` = `tserv`.`ID_SERVICE`))
) 
where ((
`T_LOG`.`ID_SERVICE` regexp `getParameter`('id_services')) or 
isnull(`getParameter`('id_services'))
) 
order by `T_LOG`.`ID_LOG` desc;

-- --------------------------------------------------------

--
-- Structure for view `V_MEMBER_ROLE`
--
DROP TABLE IF EXISTS `V_MEMBER_ROLE`;

CREATE ALGORITHM=UNDEFINED DEFINER=`simpleregister`@`localhost` SQL SECURITY DEFINER VIEW `V_MEMBER_ROLE` AS select `mbrl`.`ID_MEMBER` AS `id_member`,min(`rl`.`ORDER_BY_ROLES`) AS `roles_level`,group_concat(`rl`.`DESCRIPTION` separator ', ') AS `roles_list` from (`T_MEMBER_ROLE` `mbrl` join `T_ROLE` `rl` on((`rl`.`ID_ROLE` = `mbrl`.`ID_ROLE`))) group by `mbrl`.`ID_MEMBER`;

-- --------------------------------------------------------

--
-- Structure for view `V_SERVICE`
--
DROP TABLE IF EXISTS `V_SERVICE`;

CREATE ALGORITHM=UNDEFINED DEFINER=`simpleregister`@`localhost` SQL SECURITY DEFINER VIEW `V_SERVICE` AS select `T_SERVICE`.`ID_SERVICE` AS `id_service`,`T_SERVICE`.`NAME` AS `name` from `T_SERVICE` where ((`T_SERVICE`.`ID_SERVICE` regexp `getParameter`('id_services')) or isnull(`getParameter`('id_services')));

--
-- Indexes for dumped tables
--

--
-- Indexes for table `T_MEMBER`
--
ALTER TABLE `T_MEMBER`
  ADD PRIMARY KEY (`ID_MEMBER`),
  ADD KEY `LOGIN` (`LOGIN`),
  ADD KEY `PASSWD` (`PASSWD`),
  ADD KEY `ID_STATUS` (`ID_STATUS`),
  ADD KEY `ID_SERVICE` (`ID_SERVICE`);

--
-- Indexes for table `T_MEMBER_ROLE`
--
ALTER TABLE `T_MEMBER_ROLE`
  ADD PRIMARY KEY (`ID_MEMBER`,`ID_ROLE`);

--
-- Indexes for table `T_MEMBER_SERVICE`
--
ALTER TABLE `T_MEMBER_SERVICE`
  ADD UNIQUE KEY `ID_MEMBER` (`ID_MEMBER`,`ID_SERVICE`);

--
-- Indexes for table `T_MEMBER_SHOW_FIELD`
--
ALTER TABLE `T_MEMBER_SHOW_FIELD`
  ADD UNIQUE KEY `ID_SHOW_FIELD` (`ID_SHOW_FIELD`,`ID_MEMBER`);

--
-- Indexes for table `T_MODULE`
--
ALTER TABLE `T_MODULE`
  ADD PRIMARY KEY (`ID_MODULE`),
  ADD KEY `ORDER_BY_MODULE` (`ORDER_BY_MODULE`);

--
-- Indexes for table `T_MODULE_ROLE`
--
ALTER TABLE `T_MODULE_ROLE`
  ADD PRIMARY KEY (`ID_MODULE_ROLE`),
  ADD UNIQUE KEY `ID_MODULE` (`ID_MODULE`,`ID_ROLE`,`ID_ACCESS_TYPE`);

--
-- Indexes for table `T_ROLE`
--
ALTER TABLE `T_ROLE`
  ADD PRIMARY KEY (`ID_ROLE`),
  ADD KEY `IS_DISPLAY` (`IS_DISPLAY`),
  ADD KEY `ORDER_BY_ROLES` (`ORDER_BY_ROLES`);

--
-- Indexes for table `T_SERVICE`
--
ALTER TABLE `T_SERVICE`
  ADD PRIMARY KEY (`ID_SERVICE`),
  ADD KEY `IS_ACTIVE` (`IS_ACTIVE`);

--
-- Indexes for table `T_SHOW_FIELD`
--
ALTER TABLE `T_SHOW_FIELD`
  ADD PRIMARY KEY (`ID_SHOW_FIELD`),
  ADD KEY `DT` (`DT`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `T_MEMBER`
--
ALTER TABLE `T_MEMBER`
  MODIFY `ID_MEMBER` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `T_MODULE`
--
ALTER TABLE `T_MODULE`
  MODIFY `ID_MODULE` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `T_MODULE_ROLE`
--
ALTER TABLE `T_MODULE_ROLE`
  MODIFY `ID_MODULE_ROLE` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `T_ROLE`
--
ALTER TABLE `T_ROLE`
  MODIFY `ID_ROLE` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `T_SERVICE`
--
ALTER TABLE `T_SERVICE`
  MODIFY `ID_SERVICE` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `T_SHOW_FIELD`
--
ALTER TABLE `T_SHOW_FIELD`
  MODIFY `ID_SHOW_FIELD` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
