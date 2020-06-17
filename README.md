PRIZM SHOP SCRIPT (PRIZM Servlet)
================

1) Download library [phpqrcode](http://phpqrcode.sourceforge.net)<br/>
Put library to libs folder.<br/>
The path should be /libs/phpqrcode/qrlib.php<br/>

2) Import database
```sql
CREATE TABLE IF NOT EXISTS `pzm_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tr_id` varchar(255) NOT NULL,
  `address` varchar(26) NOT NULL,
  `price` decimal(16,2) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `tr_timestamp` int(11) NOT NULL,
  `tr_date` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
```

<br/>

```sql
CREATE TABLE IF NOT EXISTS `pzm_order` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `hash` char(32) NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `currency_rate` float NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT 'type of service/item',
  `status` tinyint(1) NOT NULL,
  `data` text,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
```

3) Set Cron Job
wget "https://domain.com/cron.php" 2>&1