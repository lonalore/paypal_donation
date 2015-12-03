CREATE TABLE `paypal_donation` (
`id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary ID of row item.',
UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The PayPal donation table.';

CREATE TABLE `paypal_donation_ipn` (
`id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary ID of row item.',
`txn_id` varchar(25) NOT NULL DEFAULT '' COMMENT 'Transaction ID.',
`item_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Item name as passed by plugin.',
`item_number` varchar(255) NOT NULL DEFAULT '' COMMENT 'Pass-through variable for plugin to track purchases.',
`mc_gross` float NOT NULL DEFAULT '0' COMMENT 'Full amount of the customer's payment, before transaction fee is subtracted.',
`mc_fee` float NOT NULL DEFAULT '0' COMMENT 'Transaction fee associated with the payment.',
`mc_currency` varchar(25) NOT NULL DEFAULT '' COMMENT 'For payment IPN notifications, this is the currency of the payment.',
`payment_date` int(11) NOT NULL DEFAULT '0' COMMENT 'Timestamp of the payment.',
`payment_status` varchar(25) NOT NULL DEFAULT '' COMMENT  'Status, which determines whether the transaction is complete.',
`cancelled` int(11) NOT NULL DEFAULT '0' COMMENT '',
`serialized` mediumtext NOT NULL COMMENT 'Full post data of IPN as a serialized string.',
UNIQUE KEY `ipn_id` (`id`),
KEY `txn_id` (`txn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The PayPal IPN payments table.';
