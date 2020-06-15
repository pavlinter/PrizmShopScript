<?php
try {
    $db = new PDO("mysql:host=" . DB_SERVERNAME . ";dbname=" . DB_NAME . "", DB_USERNAME, DB_PASSWORD);
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}