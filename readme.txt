Easymode!

Edit 'includes/config.php' to add the servers you want to use

to use the vehicle.last_updated field to display on the page add the following column to the vehicle table:

ALTER TABLE `vehicle` ADD COLUMN `last_updated` datetime DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP AFTER `pin_code`
