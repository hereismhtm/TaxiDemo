-- MySQL dump 10.13  Distrib 5.7.37, for Linux (x86_64)
--
-- Host: localhost    Database: mysql_taxidemo
-- ------------------------------------------------------
-- Server version	5.7.37-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `__logs`
--

DROP TABLE IF EXISTS `__logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `__logs` (
  `sn` int(8) NOT NULL AUTO_INCREMENT,
  `via` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `unit` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `args` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `post` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `echo` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cookie` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `agent` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `__logs`
--

LOCK TABLES `__logs` WRITE;
/*!40000 ALTER TABLE `__logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `__logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `books` (
  `Book_SN` int(8) NOT NULL AUTO_INCREMENT,
  `Class` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Booking_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Pick_Up` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Drop_Off` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Distance` float NOT NULL,
  `Note` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Passenger_ID` int(8) NOT NULL,
  `Driver_ID` int(8) NOT NULL DEFAULT '0',
  `Registration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Book_SN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `Coupon_SN` int(8) NOT NULL AUTO_INCREMENT,
  `Coupon_Code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `Coupon_Value` float NOT NULL,
  `Coupon_Amount` int(8) NOT NULL,
  `Used_Amount` int(8) NOT NULL,
  `Expire_Date` date NOT NULL,
  `Deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`Coupon_SN`),
  UNIQUE KEY `Coupon_Code` (`Coupon_Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `driversforms`
--

DROP TABLE IF EXISTS `driversforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `driversforms` (
  `Form_SN` int(8) NOT NULL AUTO_INCREMENT,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IP` varchar(39) COLLATE utf8_unicode_ci NOT NULL,
  `PHPSESSID` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `Agent` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `isAccepted` tinyint(1) NOT NULL DEFAULT '0',
  `Fullname` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `Gender` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `Phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Logo_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Nat_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `CarFront_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `CarRight_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `CarBack_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `CarLeft_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `CarInside_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Emr_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Lic_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Cert_img` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Form_SN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driversforms`
--

LOCK TABLES `driversforms` WRITE;
/*!40000 ALTER TABLE `driversforms` DISABLE KEYS */;
/*!40000 ALTER TABLE `driversforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pings`
--

DROP TABLE IF EXISTS `pings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pings` (
  `PSN` int(8) NOT NULL AUTO_INCREMENT,
  `Vehicles_Count` int(4) NOT NULL,
  `Trip_SN` int(8) NOT NULL,
  `Passenger_ID` int(8) NOT NULL,
  `Sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isAccepted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PSN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pings`
--

LOCK TABLES `pings` WRITE;
/*!40000 ALTER TABLE `pings` DISABLE KEYS */;
/*!40000 ALTER TABLE `pings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prices`
--

DROP TABLE IF EXISTS `prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prices` (
  `SN` int(8) NOT NULL AUTO_INCREMENT,
  `Class` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Period` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `Fixed` float NOT NULL DEFAULT '0',
  `Kilo` float NOT NULL DEFAULT '0',
  `Wait` float NOT NULL DEFAULT '0',
  `Tax` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`SN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prices`
--

LOCK TABLES `prices` WRITE;
/*!40000 ALTER TABLE `prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `Sub_SN` int(8) NOT NULL AUTO_INCREMENT,
  `User_ID` int(8) NOT NULL,
  `Days` int(4) NOT NULL,
  `Payed` float NOT NULL,
  `Payment_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Expire_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Sub_SN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_users`
--

DROP TABLE IF EXISTS `system_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_users` (
  `User_No` int(8) NOT NULL AUTO_INCREMENT,
  `Username` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(99) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#',
  `Permissions` int(11) NOT NULL,
  `Fullname` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `Join_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`User_No`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_users`
--

LOCK TABLES `system_users` WRITE;
/*!40000 ALTER TABLE `system_users` DISABLE KEYS */;
INSERT INTO `system_users` VALUES (1,'admin','#f589f361d60bbfa1facabbaf56627870-ce4adce0aeb1465fd6ea8f70135ae22c52e722bd9cffcb186929eb1b94da0c4e#',12288,'Administration Account','2017-11-27 07:46:41');
/*!40000 ALTER TABLE `system_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_vars`
--

DROP TABLE IF EXISTS `system_vars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_vars` (
  `RADAR_SCOPE` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_vars`
--

LOCK TABLES `system_vars` WRITE;
/*!40000 ALTER TABLE `system_vars` DISABLE KEYS */;
INSERT INTO `system_vars` VALUES (2);
/*!40000 ALTER TABLE `system_vars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trips`
--

DROP TABLE IF EXISTS `trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trips` (
  `Trip_SN` int(8) NOT NULL AUTO_INCREMENT,
  `Status` int(1) NOT NULL,
  `Class` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Pick_Up` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Drop_Off` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Pick_Up_Address` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Drop_Off_Address` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Distance` float NOT NULL,
  `Duration` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Cost` float NOT NULL,
  `After_Discount` float NOT NULL,
  `New_Balance` float NOT NULL,
  `Tax` float NOT NULL,
  `Note` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `End` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Evaluation` float NOT NULL DEFAULT '0',
  `Passenger_Note` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Driver_Note` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Vehicle_ID` int(8) NOT NULL DEFAULT '0',
  `Driver_ID` int(8) NOT NULL DEFAULT '0',
  `Passenger_ID` int(8) NOT NULL,
  PRIMARY KEY (`Trip_SN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trips`
--

LOCK TABLES `trips` WRITE;
/*!40000 ALTER TABLE `trips` DISABLE KEYS */;
/*!40000 ALTER TABLE `trips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `used_coupons`
--

DROP TABLE IF EXISTS `used_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `used_coupons` (
  `SN` int(8) NOT NULL AUTO_INCREMENT,
  `Coupon_SN` int(8) NOT NULL,
  `User_ID` int(8) NOT NULL,
  `Used_Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `used_coupons`
--

LOCK TABLES `used_coupons` WRITE;
/*!40000 ALTER TABLE `used_coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `used_coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `User_ID` int(8) NOT NULL AUTO_INCREMENT,
  `Frozen` tinyint(1) NOT NULL DEFAULT '0',
  `Active_Until` timestamp NULL DEFAULT NULL,
  `Form_SN` int(8) NOT NULL,
  `Type` int(1) NOT NULL DEFAULT '0',
  `Phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Balance` float NOT NULL DEFAULT '0',
  `Credit` float NOT NULL DEFAULT '0',
  `KeyPass` char(99) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#',
  `Pass` char(40) COLLATE utf8_unicode_ci NOT NULL,
  `Perm` int(8) NOT NULL,
  `Fullname` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `Gender` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `Email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Logo` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `Registration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `PFCM` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `CFCM` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `P_VC` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `C_VC` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`User_ID`),
  UNIQUE KEY `Phone` (`Phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicles` (
  `Vehicle_ID` int(8) NOT NULL AUTO_INCREMENT,
  `Driver_ID` int(8) NOT NULL,
  `isLinked` tinyint(1) NOT NULL,
  `Class` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Swap_Classes` int(2) NOT NULL DEFAULT '0',
  `Model` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Color` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Plate` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Registration` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `LAT` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `LNG` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`Vehicle_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-04-07  0:44:35
