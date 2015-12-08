CREATE TABLE `paypal_donation` (
`pd_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary ID of row item.',
`pd_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Title for donation menu item.',
`pd_description` text NOT NULL COMMENT 'Description for donation menu item.',
`pd_custom_amount` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Allows to use custom amount field on donation form.',
`pd_goal_amount` float NOT NULL DEFAULT '0' COMMENT 'Goal amount for donation menu item.',
`pd_goal_date` int(11) NOT NULL DEFAULT '0' COMMENT 'Goal date for donation menu item.',
`pd_currency` varchar(25) NOT NULL DEFAULT '' COMMENT 'Currency for amounts.',
`pd_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Status active or inactive.',
`pd_weight` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Weight for ordering.',
`pd_visibility` varchar(10) NOT NULL DEFAULT '' COMMENT 'Users who can view menu item.',
`pd_donate` varchar(10) NOT NULL DEFAULT '' COMMENT 'Users who can donate.',
`pd_language` varchar(25) NOT NULL DEFAULT '' COMMENT 'Language code to define PayPal language.',
PRIMARY KEY (`pd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `paypal_donation_amount` (
`pda_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary ID of row item.',
`pda_donation` int(11) NOT NULL DEFAULT '0' COMMENT 'Donation menu item ID from paypal_donation table.',
`pda_label` varchar(255) NOT NULL DEFAULT '' COMMENT 'Label for donation amount.',
`pda_value` float NOT NULL DEFAULT '0' COMMENT 'Value for donation amount.',
`pda_weight` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Weight for ordering.',
PRIMARY KEY (`pda_id`)/*,*/
/* FOREIGN KEY (`pda_donation`) REFERENCES `paypal_donation`(`pd_id`) */
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `paypal_donation_ipn` (
`pdi_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary ID of row item.',
`pdi_donation` int(11) NOT NULL DEFAULT '0' COMMENT 'Donation menu item ID.',
`pdi_user` int(11) NOT NULL DEFAULT '0' COMMENT 'User id from e107 users table.',
`pdi_txn_id` varchar(25) NOT NULL DEFAULT '' COMMENT 'Transaction ID.',
`pdi_mc_gross` float NOT NULL DEFAULT '0' COMMENT 'Full amount of the customers payment, before transaction fee is subtracted.',
`pdi_mc_fee` float NOT NULL DEFAULT '0' COMMENT 'Transaction fee associated with the payment.',
`pdi_mc_currency` varchar(25) NOT NULL DEFAULT '' COMMENT 'For payment IPN notifications, this is the currency of the payment.',
`pdi_payment_date` int(11) NOT NULL DEFAULT '0' COMMENT 'Timestamp of the payment.',
`pdi_cancelled` int(11) NOT NULL DEFAULT '0' COMMENT 'Admin can cancel payment manually.',
`pdi_serialized_ipn` mediumtext NOT NULL COMMENT 'Full post data of IPN as a serialized string.',
PRIMARY KEY (`pdi_id`)/*,*/
/* FOREIGN KEY (`pdi_donation`) REFERENCES `paypal_donation`(`pd_id`) */
/* FOREIGN KEY (`pdi_user`) REFERENCES `user`(`user_id`) */
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
