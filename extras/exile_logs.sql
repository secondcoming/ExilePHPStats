/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for exile
CREATE DATABASE IF NOT EXISTS `exile_logs` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `exile_logs`;

-- Dumping structure for table exile.trader_log
CREATE TABLE IF NOT EXISTS `trader_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_sold` varchar(50) DEFAULT NULL,
  `steamid64` varchar(50) DEFAULT NULL,
  `items_sold` varchar(100) DEFAULT NULL,
  `poptabs` varchar(50) DEFAULT NULL,
  `respect` varchar(50) DEFAULT NULL,
  `servername` varchar(50) DEFAULT NULL,
  KEY `Index 1` (`id`),
  KEY `Index 2` (`items_sold`),
  KEY `Index 3` (`steamid64`),
  KEY `Index 5` (`time_sold`),
  FULLTEXT KEY `servername` (`servername`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
