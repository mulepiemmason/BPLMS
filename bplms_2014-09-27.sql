# ************************************************************
# Sequel Pro SQL dump
# Version 4135
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.34)
# Database: bplms
# Generation Time: 2014-09-27 03:00:50 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table books
# ------------------------------------------------------------

DROP TABLE IF EXISTS `books`;

CREATE TABLE `books` (
  `bookid` char(12) NOT NULL DEFAULT '',
  `regdate` date NOT NULL,
  `title` char(128) NOT NULL DEFAULT '',
  `category` char(12) NOT NULL DEFAULT '',
  `author` char(64) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`bookid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table borrowed
# ------------------------------------------------------------

DROP TABLE IF EXISTS `borrowed`;

CREATE TABLE `borrowed` (
  `borrowid` char(32) NOT NULL DEFAULT '',
  `bookid` char(12) NOT NULL DEFAULT '',
  `date` date NOT NULL,
  `borrowerid` int(11) NOT NULL,
  `duedate` date NOT NULL,
  `returned` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`borrowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table daily
# ------------------------------------------------------------

DROP TABLE IF EXISTS `daily`;

CREATE TABLE `daily` (
  `username` char(32) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `address` char(32) NOT NULL DEFAULT '',
  `sex` char(6) NOT NULL DEFAULT '',
  `purpose` char(64) NOT NULL DEFAULT '',
  `checkout` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table iborrowed
# ------------------------------------------------------------

DROP TABLE IF EXISTS `iborrowed`;

CREATE TABLE `iborrowed` (
  `borrowid` char(32) NOT NULL DEFAULT '',
  `bookid` char(12) NOT NULL,
  `date` datetime NOT NULL,
  `username` char(32) NOT NULL DEFAULT '',
  `address` char(16) NOT NULL DEFAULT '',
  `sex` char(6) NOT NULL DEFAULT '',
  `returned` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`borrowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table members
# ------------------------------------------------------------

DROP TABLE IF EXISTS `members`;

CREATE TABLE `members` (
  `userid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `regdate` date NOT NULL,
  `firstname` char(16) NOT NULL DEFAULT '',
  `surname` char(16) NOT NULL DEFAULT '',
  `address` char(16) NOT NULL DEFAULT '',
  `sex` char(6) NOT NULL DEFAULT '',
  `age` int(2) NOT NULL,
  `expiry` date NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(32) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL DEFAULT '',
  `email` char(64) DEFAULT '',
  `firstname` char(32) NOT NULL DEFAULT '',
  `surname` char(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`userid`, `username`, `password`, `email`, `firstname`, `surname`)
VALUES
	(2,'musa','eb7f9542101c6a94f27404fafc3efd53','mujeremusa@yahoo.com','Mujere','Musa'),
	(3,'rose','1e1e419aa6bd78f877bfd58eccc8067f','rosealungho14@yahoo.com','Alungho','Rose');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
