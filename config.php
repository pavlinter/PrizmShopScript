<?php

/*
_config.php
define("DB_SERVERNAME", "localhost");
define("DB_NAME", "");
define("DB_USERNAME", "");
define("DB_PASSWORD", "");
define("RANDOM_SALT", ""); //547894tgfslho
define("PRIZM_ADDRESS", "PRIZM-AAAA-AAAA-AAAA-AAAAA");
define("PRIZM_PUBLIC_KEY", "1b2fea9222d0e34c.....");

define("NODA_URL", "http://100.100.100.100:2525");
define("SERVLET_URL", "http://100.100.100.100:1515");
*/

include '_config.php';

define("ORDER_STATUS_ERROR", 0);
define("ORDER_STATUS_NEW", 1);
define("ORDER_STATUS_SUCCESS", 2);

define("HISTORY_STATUS_PENDING", 0);
define("HISTORY_STATUS_USED", 1);

include 'connect.php';
include 'functions.php';