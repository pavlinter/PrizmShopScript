PRIZM SHOP SCRIPT (PRIZM Servlet)
================

Download library [phpqrcode](http://phpqrcode.sourceforge.net)
--------------------------------------------------------------
Put library to libs folder.<br/>
The path should be /libs/phpqrcode/qrlib.php<br/>

Import database
----------------
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

Noda settings (prizm.default.properties)
----------------------------------------
![Screen Shot](https://github.com/pavlinter/PrizmShopScript/blob/master/assets/images/prizm.default.properties.jpg?raw=true)

Servlet settings (PrizmAPIServlet.properties)
------------------------------------------------
![Screen Shot](https://github.com/pavlinter/PrizmShopScript/blob/master/assets/images/PrizmAPIServlet.properties.jpg?raw=true)


Run Noda (linux)
----------------
```bash
cd /root/prizm-dist/
sh run.sh
```

Run Servlet (linux)
----------------
```bash
cd /root/prizm-api/
sh run-servlet.sh
```

Set Cron Job
----------------
```bash
wget "https://domain.com/cron.php" 2>&1
```