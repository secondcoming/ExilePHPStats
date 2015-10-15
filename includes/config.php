<?php
// array of servers ('hostname or ip address|database name|database username|database password|server name|mysql port')
$ServerList = array('127.0.0.1|exile_altis|dbuser|dbpass|altis|3306','127.0.0.1|exile_chernarus|dbuser|dbpass|chernarus|3306'); 


$UseAccountLog = TRUE;

/*

Add the following to your database to use the  Account Log feature, it records every time a player changes their name

CREATE TABLE IF NOT EXISTS `account_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `old_name` varchar(64) NOT NULL,
  `money` double NOT NULL DEFAULT '0',
  `score` double NOT NULL DEFAULT '0',
  `connected` datetime DEFAULT NULL,
  KEY `Index 1` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 
-- Dumping structure for trigger exile.update_name
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `update_name` AFTER UPDATE ON `account` FOR EACH ROW BEGIN
    IF (NEW.name != OLD.name) THEN
        INSERT INTO account_log
            (`uid` , `name` , `old_name`, `money` , `score` , `connected` )
        VALUES
            (NEW.uid, NEW.name, OLD.name, NEW.money, NEW.score, NOW());
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


*/
?>

