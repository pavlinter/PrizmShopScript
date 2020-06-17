<?php

/**
 * @return string
 */
function generateUniqHash()
{
    global $db;

    while (true){
        $hash = md5(md5(time() . rand(0, 10000)) . RANDOM_SALT);
        $stmt = $db->prepare('SELECT * FROM pzm_order WHERE hash=?');
        $stmt->bindParam(1, $hash, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) {
            break;
        }
    }

    return $hash;
}

/**
 * @return int
 */
function getLastPzmHistory()
{
    global $db;

    $stmt = $db->prepare('SELECT * FROM pzm_history ORDER BY id DESC LIMIT 1');
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return $row['tr_id'];
    }
    return 0;
}

/**
 * @return int
 */
function getSuccessOrders()
{
    global $db;

    $stmt = $db->prepare('SELECT * FROM pzm_order WHERE status=' . ORDER_STATUS_SUCCESS);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}

function getTransaction($hash)
{
    global $db;

    $stmt = $db->prepare('SELECT * FROM pzm_history WHERE comment=? LIMIT 1');
    $stmt->bindParam(1, $hash, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return [];
    }
    return $row;
}

/**
 * @param $json
 * @param bool $asArray
 * @return mixed|null
 */
function jsonDecode($json, $asArray = true)
{
    if ($json === null || $json === '') {
        return null;
    }
    $decode = json_decode((string) $json, $asArray);
    return $decode;
}

/**
 * @param $message
 */
function log_error($message) {
    logfile($message, 'error.log');
}

/**
 * @param $message
 * @param string $file
 */
function logfile($message, $file = 'logfile.log') {
    error_log($message."\n", 3, $file);
}

/**
 * @param $key
 * @param null $default
 * @return null
 */
function get($key, $default = null)
{
    if (isset($_GET[$key])) {
        return $_GET[$key];
    }
    return $default;
}

/**
 * @param $key
 * @param null $default
 * @return null
 */
function post($key, $default = null)
{
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }
    return $default;
}

/**
 * @param bool $key
 * @return array|mixed|null
 */
function items_list($key = false)
{
    $list = [
        '1' => [
            'name' => 'Pen',
            'price' => 1,
        ],
        '2' => [
            'name' => 'Pencil',
            'price' => 2,
        ],
        '3' => [
            'name' => 'Notebook',
            'price' => 3,
        ],
    ];

    if ($key === false) {
        return $list;
    }

    if (isset($list[$key])) {
        return $list[$key];
    }

    return null;
}


