-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 07, 2017 at 12:55 PM
-- Server version: 5.5.44-0+deb8u1
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


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
DROP PROCEDURE IF EXISTS `setParameter`$$
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
DROP FUNCTION IF EXISTS `getFakeId`$$
CREATE DEFINER=`simpleregister`@`localhost` FUNCTION `getFakeId`() RETURNS int(11)
    NO SQL
begin
    return if(@fakeId, @fakeId:=@fakeId+1, @fakeId:=1);
end$$

DROP FUNCTION IF EXISTS `getParameter`$$
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
-- Table structure for table `T_ANSWER`
--

DROP TABLE IF EXISTS `T_ANSWER`;
CREATE TABLE IF NOT EXISTS `T_ANSWER` (
  `ANSWER_ID` int(11) NOT NULL,
  `NAME` varchar(3000) NOT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_LOG`
--

DROP TABLE IF EXISTS `T_LOG`;
CREATE TABLE IF NOT EXISTS `T_LOG` (
  `ID_LOG` int(11) NOT NULL,
  `ID_SERVICE` int(11) NOT NULL,
  `NAME` varchar(3000) DEFAULT NULL,
  `SURNAME` varchar(3000) DEFAULT NULL,
  `PATRONYMIC` varchar(3000) DEFAULT NULL,
  `MSISDN` varchar(1000) DEFAULT NULL,
  `EMAIL` varchar(1000) DEFAULT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `QUESTION_ID` int(11) DEFAULT NULL,
  `ANSWER_ID` int(11) DEFAULT NULL,
  `ANSWER_ORDER_NUM` int(11) DEFAULT NULL,
  `SESSION_ID` varchar(1000) DEFAULT NULL,
  `NET` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_MEMBER`
--

DROP TABLE IF EXISTS `T_MEMBER`;
CREATE TABLE IF NOT EXISTS `T_MEMBER` (
  `ID_MEMBER` int(11) NOT NULL,
  `LOGIN` varchar(100) NOT NULL,
  `PASSWD` varchar(100) NOT NULL,
  `ID_STATUS` int(11) NOT NULL,
  `ID_SERVICE` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_MEMBER_ROLE`
--

DROP TABLE IF EXISTS `T_MEMBER_ROLE`;
CREATE TABLE IF NOT EXISTS `T_MEMBER_ROLE` (
  `ID_MEMBER` int(11) NOT NULL,
  `ID_ROLE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_MEMBER_SERVICE`
--

DROP TABLE IF EXISTS `T_MEMBER_SERVICE`;
CREATE TABLE IF NOT EXISTS `T_MEMBER_SERVICE` (
  `ID_MEMBER` int(11) NOT NULL,
  `ID_SERVICE` int(11) NOT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_MEMBER_SHOW_FIELD`
--

DROP TABLE IF EXISTS `T_MEMBER_SHOW_FIELD`;
CREATE TABLE IF NOT EXISTS `T_MEMBER_SHOW_FIELD` (
  `ID_SHOW_FIELD` int(11) NOT NULL,
  `ID_MEMBER` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_MODULE`
--

DROP TABLE IF EXISTS `T_MODULE`;
CREATE TABLE IF NOT EXISTS `T_MODULE` (
  `ID_MODULE` int(11) NOT NULL,
  `NAME` varchar(1000) NOT NULL,
  `MODULE` varchar(1000) NOT NULL,
  `PATH` varchar(1000) NOT NULL,
  `ORDER_BY_MODULE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_MODULE_ROLE`
--

DROP TABLE IF EXISTS `T_MODULE_ROLE`;
CREATE TABLE IF NOT EXISTS `T_MODULE_ROLE` (
  `ID_MODULE_ROLE` int(11) NOT NULL,
  `ID_MODULE` int(11) NOT NULL,
  `ID_ROLE` int(11) NOT NULL,
  `ID_ACCESS_TYPE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_QUESTION`
--

DROP TABLE IF EXISTS `T_QUESTION`;
CREATE TABLE IF NOT EXISTS `T_QUESTION` (
  `QUESTION_ID` int(11) NOT NULL,
  `NAME` varchar(3000) NOT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_QUESTION_ANSWER`
--

DROP TABLE IF EXISTS `T_QUESTION_ANSWER`;
CREATE TABLE IF NOT EXISTS `T_QUESTION_ANSWER` (
  `QUESTION_ID` int(11) NOT NULL,
  `ANSWER_ID` int(11) NOT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ANSWER_ORDER_NUM` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_REPORT`
--

DROP TABLE IF EXISTS `T_REPORT`;
CREATE TABLE IF NOT EXISTS `T_REPORT` (
  `ID_REPORT` int(11) NOT NULL,
  `ID_SERVICE_REPORT` int(11) DEFAULT NULL,
  `ID_SERVICE` int(11) DEFAULT NULL,
  `ID_PERIOD` int(11) DEFAULT NULL,
  `FILE` varchar(300) DEFAULT NULL,
  `DT_SENDED` timestamp NULL DEFAULT NULL,
  `DT_FROM` timestamp NULL DEFAULT NULL,
  `DT_TO` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_REPORT_PERIOD`
--

DROP TABLE IF EXISTS `T_REPORT_PERIOD`;
CREATE TABLE IF NOT EXISTS `T_REPORT_PERIOD` (
  `ID_PERIOD` int(11) NOT NULL,
  `NAME` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_ROLE`
--

DROP TABLE IF EXISTS `T_ROLE`;
CREATE TABLE IF NOT EXISTS `T_ROLE` (
  `ID_ROLE` int(11) NOT NULL,
  `DESCRIPTION` varchar(100) NOT NULL,
  `IS_DISPLAY` int(11) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `ORDER_BY_ROLES` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_SERVICE`
--

DROP TABLE IF EXISTS `T_SERVICE`;
CREATE TABLE IF NOT EXISTS `T_SERVICE` (
  `ID_SERVICE` int(11) NOT NULL,
  `NAME` varchar(3000) NOT NULL,
  `NOTE` varchar(4000) NOT NULL,
  `IS_ACTIVE` int(11) NOT NULL DEFAULT '1',
  `IS_AUTO_EMAIL_NOTIFY` int(11) NOT NULL,
  `EMAIL_SUBJECT_DEF` varchar(1000) DEFAULT NULL,
  `EMAIL_FROM_DEF` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_SERVICE_EMAIL`
--

DROP TABLE IF EXISTS `T_SERVICE_EMAIL`;
CREATE TABLE IF NOT EXISTS `T_SERVICE_EMAIL` (
  `ID_SERVICE_EMAIL` int(11) NOT NULL,
  `ID_SERVICE` int(11) NOT NULL,
  `EMAIL` varchar(200) NOT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_SERVICE_REPORT_FIELD`
--

DROP TABLE IF EXISTS `T_SERVICE_REPORT_FIELD`;
CREATE TABLE IF NOT EXISTS `T_SERVICE_REPORT_FIELD` (
  `ID_SERVICE_REPORT` int(11) NOT NULL,
  `ID_SHOW_FIELD` int(11) NOT NULL,
  `ORDER_NUM` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_SERVICE_REPORT_PERIOD`
--

DROP TABLE IF EXISTS `T_SERVICE_REPORT_PERIOD`;
CREATE TABLE IF NOT EXISTS `T_SERVICE_REPORT_PERIOD` (
  `ID_SERVICE_REPORT` int(11) NOT NULL,
  `ID_SERVICE` int(11) NOT NULL,
  `ID_PERIOD` int(11) NOT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EMAIL_SUBJECT` varchar(1000) DEFAULT NULL,
  `EMAIL_FROM` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `T_SHOW_FIELD`
--

DROP TABLE IF EXISTS `T_SHOW_FIELD`;
CREATE TABLE IF NOT EXISTS `T_SHOW_FIELD` (
  `ID_SHOW_FIELD` int(11) NOT NULL,
  `NAME` varchar(1000) NOT NULL,
  `NAME_RUS` varchar(300) DEFAULT NULL,
  `DT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `V_LOG`
--
DROP VIEW IF EXISTS `V_LOG`;
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
,`QUESTION_ID` int(11)
,`ANSWER_ID` int(11)
,`ANSWER_ORDER_NUM` int(11)
,`QUESTION` varchar(3000)
,`ANSWER` varchar(3000)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `V_MEMBER_ROLE`
--
DROP VIEW IF EXISTS `V_MEMBER_ROLE`;
CREATE TABLE IF NOT EXISTS `V_MEMBER_ROLE` (
`id_member` int(11)
,`roles_level` int(11)
,`roles_list` text
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `V_REPORT_EVENT`
--
DROP VIEW IF EXISTS `V_REPORT_EVENT`;
CREATE TABLE IF NOT EXISTS `V_REPORT_EVENT` (
`ID_SERVICE_REPORT` int(11)
,`EMAIL_SUBJECT` varchar(1000)
,`EMAIL_FROM` varchar(1000)
,`NAME_SERVICE` varchar(3000)
,`ID_SERVICE` int(11)
,`ID_PERIOD` int(11)
,`NAME_PERIOD` varchar(100)
,`LAST_DT` varchar(29)
,`NEXT_DT` varchar(29)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `V_REPORT_EVENT_PREPARE`
--
DROP VIEW IF EXISTS `V_REPORT_EVENT_PREPARE`;
CREATE TABLE IF NOT EXISTS `V_REPORT_EVENT_PREPARE` (
`ID_SERVICE_REPORT` int(11)
,`EMAIL_SUBJECT` varchar(1000)
,`EMAIL_FROM` varchar(1000)
,`NAME_SERVICE` varchar(3000)
,`ID_SERVICE` int(11)
,`ID_PERIOD` int(11)
,`NAME_PERIOD` varchar(100)
,`LAST_DT` varchar(29)
,`NEXT_DT` varchar(29)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `V_SERVICE`
--
DROP VIEW IF EXISTS `V_SERVICE`;
CREATE TABLE IF NOT EXISTS `V_SERVICE` (
`id_service` int(11)
,`name` varchar(3000)
);

-- --------------------------------------------------------

--
-- Structure for view `V_LOG`
--
DROP TABLE IF EXISTS `V_LOG`;

CREATE ALGORITHM=UNDEFINED DEFINER=`simpleregister`@`localhost` SQL SECURITY DEFINER VIEW `V_LOG` AS select `T_LOG`.`ID_LOG` AS `ID_LOG`,`T_LOG`.`ID_SERVICE` AS `ID_SERVICE`,`T_LOG`.`NAME` AS `NAME`,`T_LOG`.`SURNAME` AS `SURNAME`,`T_LOG`.`PATRONYMIC` AS `PATRONYMIC`,`T_LOG`.`MSISDN` AS `MSISDN`,`T_LOG`.`EMAIL` AS `EMAIL`,`T_LOG`.`DT` AS `DT`,`tserv`.`NAME` AS `SERVICE`,`T_LOG`.`QUESTION_ID` AS `QUESTION_ID`,`T_LOG`.`ANSWER_ID` AS `ANSWER_ID`,`T_LOG`.`ANSWER_ORDER_NUM` AS `ANSWER_ORDER_NUM`,`tq`.`NAME` AS `QUESTION`,`ta`.`NAME` AS `ANSWER` from ((((`T_LOG` left join `T_SERVICE` `tserv` on((`T_LOG`.`ID_SERVICE` = `tserv`.`ID_SERVICE`))) left join `T_QUESTION_ANSWER` `tqa` on(((`tqa`.`QUESTION_ID` = `T_LOG`.`QUESTION_ID`) and ((`tqa`.`ANSWER_ID` = `T_LOG`.`ANSWER_ID`) or (`tqa`.`ANSWER_ORDER_NUM` = `T_LOG`.`ANSWER_ORDER_NUM`))))) left join `T_QUESTION` `tq` on((`tqa`.`QUESTION_ID` = `tq`.`QUESTION_ID`))) left join `T_ANSWER` `ta` on((`tqa`.`ANSWER_ID` = `ta`.`ANSWER_ID`))) where ((`T_LOG`.`ID_SERVICE` regexp `getParameter`('id_services')) or isnull(`getParameter`('id_services'))) order by `T_LOG`.`ID_LOG` desc;

-- --------------------------------------------------------

--
-- Structure for view `V_MEMBER_ROLE`
--
DROP TABLE IF EXISTS `V_MEMBER_ROLE`;

CREATE ALGORITHM=UNDEFINED DEFINER=`simpleregister`@`localhost` SQL SECURITY DEFINER VIEW `V_MEMBER_ROLE` AS select `mbrl`.`ID_MEMBER` AS `id_member`,min(`rl`.`ORDER_BY_ROLES`) AS `roles_level`,group_concat(`rl`.`DESCRIPTION` separator ', ') AS `roles_list` from (`T_MEMBER_ROLE` `mbrl` join `T_ROLE` `rl` on((`rl`.`ID_ROLE` = `mbrl`.`ID_ROLE`))) group by `mbrl`.`ID_MEMBER`;

-- --------------------------------------------------------

--
-- Structure for view `V_REPORT_EVENT`
--
DROP TABLE IF EXISTS `V_REPORT_EVENT`;

CREATE ALGORITHM=UNDEFINED DEFINER=`simpleregister`@`localhost` SQL SECURITY DEFINER VIEW `V_REPORT_EVENT` AS select `V_REPORT_EVENT_PREPARE`.`ID_SERVICE_REPORT` AS `ID_SERVICE_REPORT`,`V_REPORT_EVENT_PREPARE`.`EMAIL_SUBJECT` AS `EMAIL_SUBJECT`,`V_REPORT_EVENT_PREPARE`.`EMAIL_FROM` AS `EMAIL_FROM`,`V_REPORT_EVENT_PREPARE`.`NAME_SERVICE` AS `NAME_SERVICE`,`V_REPORT_EVENT_PREPARE`.`ID_SERVICE` AS `ID_SERVICE`,`V_REPORT_EVENT_PREPARE`.`ID_PERIOD` AS `ID_PERIOD`,`V_REPORT_EVENT_PREPARE`.`NAME_PERIOD` AS `NAME_PERIOD`,`V_REPORT_EVENT_PREPARE`.`LAST_DT` AS `LAST_DT`,`V_REPORT_EVENT_PREPARE`.`NEXT_DT` AS `NEXT_DT` from (`V_REPORT_EVENT_PREPARE` left join `T_REPORT` on(((`T_REPORT`.`ID_SERVICE` = `V_REPORT_EVENT_PREPARE`.`ID_SERVICE`) and (`T_REPORT`.`ID_SERVICE_REPORT` = `V_REPORT_EVENT_PREPARE`.`ID_SERVICE_REPORT`) and (`T_REPORT`.`ID_PERIOD` = `V_REPORT_EVENT_PREPARE`.`ID_PERIOD`) and (`T_REPORT`.`DT_FROM` = `V_REPORT_EVENT_PREPARE`.`LAST_DT`) and (`T_REPORT`.`DT_TO` = `V_REPORT_EVENT_PREPARE`.`NEXT_DT`)))) where isnull(`T_REPORT`.`DT_SENDED`);

-- --------------------------------------------------------

--
-- Structure for view `V_REPORT_EVENT_PREPARE`
--
DROP TABLE IF EXISTS `V_REPORT_EVENT_PREPARE`;

CREATE ALGORITHM=UNDEFINED DEFINER=`simpleregister`@`localhost` SQL SECURITY DEFINER VIEW `V_REPORT_EVENT_PREPARE` AS select `T_SERVICE_REPORT_PERIOD`.`ID_SERVICE_REPORT` AS `ID_SERVICE_REPORT`,`T_SERVICE_REPORT_PERIOD`.`EMAIL_SUBJECT` AS `EMAIL_SUBJECT`,`T_SERVICE_REPORT_PERIOD`.`EMAIL_FROM` AS `EMAIL_FROM`,`T_SERVICE`.`NAME` AS `NAME_SERVICE`,`T_SERVICE`.`ID_SERVICE` AS `ID_SERVICE`,`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD` AS `ID_PERIOD`,`T_REPORT_PERIOD`.`NAME` AS `NAME_PERIOD`,(case when (`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD` = 1) then left(((now() - interval 1 week) - interval weekday((now() - interval 1 week)) day),10) when (`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD` = 2) then (left((last_day((now() - interval 1 week)) - interval 1 month),10) + interval 1 day) when (`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD` = 3) then ((last_day((now() - interval 1 week)) - interval (month((now() - interval 1 week)) % 3) month) + interval 1 day) when (`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD` = 4) then left((now() - interval 1 day),10) end) AS `LAST_DT`,(case when (`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD` = 1) then left((((now() - interval 1 week) - interval weekday((now() - interval 1 week)) day) + interval 7 day),10) when (`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD` = 2) then (last_day((now() - interval 1 week)) + interval 1 day) when (`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD` = 3) then (((last_day((now() - interval 1 week)) - interval (month((now() - interval 1 week)) % 3) month) + interval 1 day) + interval 3 month) when (`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD` = 4) then left(now(),10) end) AS `NEXT_DT` from ((`T_SERVICE` join `T_SERVICE_REPORT_PERIOD` on((`T_SERVICE_REPORT_PERIOD`.`ID_SERVICE` = `T_SERVICE`.`ID_SERVICE`))) join `T_REPORT_PERIOD` on((`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD` = `T_REPORT_PERIOD`.`ID_PERIOD`))) where (`T_SERVICE`.`IS_ACTIVE` = 1) group by `T_SERVICE`.`NAME`,`T_SERVICE`.`ID_SERVICE`,`T_SERVICE_REPORT_PERIOD`.`ID_SERVICE_REPORT`,`T_SERVICE_REPORT_PERIOD`.`EMAIL_SUBJECT`,`T_SERVICE_REPORT_PERIOD`.`EMAIL_FROM`,`T_SERVICE_REPORT_PERIOD`.`ID_PERIOD`;

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
-- Indexes for table `T_ANSWER`
--
ALTER TABLE `T_ANSWER`
  ADD PRIMARY KEY (`ANSWER_ID`),
  ADD KEY `DT` (`DT`);

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
  ADD KEY `EMAIL` (`EMAIL`(255)),
  ADD KEY `QUESTION_ID` (`QUESTION_ID`),
  ADD KEY `ANSWER_ID` (`ANSWER_ID`),
  ADD KEY `ANSWER_ORDER_NUM` (`ANSWER_ORDER_NUM`),
  ADD KEY `SESSION_ID` (`SESSION_ID`(255)),
  ADD KEY `NET` (`NET`);

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
-- Indexes for table `T_QUESTION`
--
ALTER TABLE `T_QUESTION`
  ADD PRIMARY KEY (`QUESTION_ID`),
  ADD KEY `DT` (`DT`);

--
-- Indexes for table `T_QUESTION_ANSWER`
--
ALTER TABLE `T_QUESTION_ANSWER`
  ADD UNIQUE KEY `UNIQUE_INDEX_1` (`QUESTION_ID`,`ANSWER_ID`,`ANSWER_ORDER_NUM`) USING BTREE,
  ADD KEY `DT` (`DT`);

--
-- Indexes for table `T_REPORT`
--
ALTER TABLE `T_REPORT`
  ADD PRIMARY KEY (`ID_REPORT`),
  ADD KEY `ID_SERVICE` (`ID_SERVICE`),
  ADD KEY `ID_PERIOD` (`ID_PERIOD`),
  ADD KEY `DT_SENDED` (`DT_SENDED`),
  ADD KEY `DT_FROM` (`DT_FROM`),
  ADD KEY `DT_TO` (`DT_TO`),
  ADD KEY `ID_SERVICE_REPORT` (`ID_SERVICE_REPORT`);

--
-- Indexes for table `T_REPORT_PERIOD`
--
ALTER TABLE `T_REPORT_PERIOD`
  ADD PRIMARY KEY (`ID_PERIOD`);

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
  ADD KEY `IS_ACTIVE` (`IS_ACTIVE`),
  ADD KEY `IS_AUTO_EMAIL_NOTIFY` (`IS_AUTO_EMAIL_NOTIFY`);

--
-- Indexes for table `T_SERVICE_EMAIL`
--
ALTER TABLE `T_SERVICE_EMAIL`
  ADD PRIMARY KEY (`ID_SERVICE_EMAIL`),
  ADD UNIQUE KEY `ID_SERVICE_2` (`ID_SERVICE`,`EMAIL`),
  ADD KEY `ID_SERVICE` (`ID_SERVICE`),
  ADD KEY `EMAIL` (`EMAIL`) USING BTREE,
  ADD KEY `DT` (`DT`);

--
-- Indexes for table `T_SERVICE_REPORT_FIELD`
--
ALTER TABLE `T_SERVICE_REPORT_FIELD`
  ADD UNIQUE KEY `ID_SERVICE_REPORT` (`ID_SERVICE_REPORT`,`ID_SHOW_FIELD`),
  ADD KEY `ORDER_NUM` (`ORDER_NUM`);

--
-- Indexes for table `T_SERVICE_REPORT_PERIOD`
--
ALTER TABLE `T_SERVICE_REPORT_PERIOD`
  ADD PRIMARY KEY (`ID_SERVICE_REPORT`),
  ADD UNIQUE KEY `ID_SERVICE` (`ID_SERVICE`,`ID_PERIOD`),
  ADD KEY `DT` (`DT`),
  ADD KEY `EMAIL_SUBJECT` (`EMAIL_SUBJECT`(255)),
  ADD KEY `EMAIL_FROM` (`EMAIL_FROM`(255));

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
-- AUTO_INCREMENT for table `T_ANSWER`
--
ALTER TABLE `T_ANSWER`
  MODIFY `ANSWER_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_LOG`
--
ALTER TABLE `T_LOG`
  MODIFY `ID_LOG` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_MEMBER`
--
ALTER TABLE `T_MEMBER`
  MODIFY `ID_MEMBER` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_MODULE`
--
ALTER TABLE `T_MODULE`
  MODIFY `ID_MODULE` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_MODULE_ROLE`
--
ALTER TABLE `T_MODULE_ROLE`
  MODIFY `ID_MODULE_ROLE` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_QUESTION`
--
ALTER TABLE `T_QUESTION`
  MODIFY `QUESTION_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_REPORT`
--
ALTER TABLE `T_REPORT`
  MODIFY `ID_REPORT` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_REPORT_PERIOD`
--
ALTER TABLE `T_REPORT_PERIOD`
  MODIFY `ID_PERIOD` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_ROLE`
--
ALTER TABLE `T_ROLE`
  MODIFY `ID_ROLE` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_SERVICE`
--
ALTER TABLE `T_SERVICE`
  MODIFY `ID_SERVICE` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_SERVICE_EMAIL`
--
ALTER TABLE `T_SERVICE_EMAIL`
  MODIFY `ID_SERVICE_EMAIL` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_SERVICE_REPORT_PERIOD`
--
ALTER TABLE `T_SERVICE_REPORT_PERIOD`
  MODIFY `ID_SERVICE_REPORT` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `T_SHOW_FIELD`
--
ALTER TABLE `T_SHOW_FIELD`
  MODIFY `ID_SHOW_FIELD` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;