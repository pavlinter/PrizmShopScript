<?php
include 'config.php';
include 'libs/phpqrcode/qrlib.php'; //http://phpqrcode.sourceforge.net/

$item = post('item');

if ($item == 1){
    $price = '1.00';
} elseif ($item == 2){
    $price = '2.00';
} elseif ($item == 3){
    $price = '3.00';
} else {
    exit();
}


$data = [
    'user_id' => null, //if exist user id
    'hash' => generateUniqHash(),
    'price' => (float)$price, //pzm
    'currency_rate' => 1, //currency rate (for example 1 dollar)
    'type' => (int)$item, //type of item or service
    'status' => ORDER_STATUS_NEW, // order status (ORDER_STATUS_ERROR / ORDER_STATUS_NEW / ORDER_STATUS_SUCCESS)
    'data' => serialize(['id' => 111]),
];
$sql = "INSERT INTO pzm_order (user_id, hash, price, currency_rate, `type`, status, `data`, created_at) VALUES (?,?,?,?,?,?,?,NOW())";
$db->prepare($sql)->execute([$data['user_id'], $data['hash'], $data['price'], $data['currency_rate'], $data['type'], $data['status'], $data['data']]);


ob_start();
\QRcode::png(PRIZM_ADDRESS. ":" . PRIZM_PUBLIC_KEY . ":" . $data['price'] . ":" . $data['hash'], null, QR_ECLEVEL_L, 6, $margin = 1);
$imageString = base64_encode( ob_get_contents() );
ob_end_clean();
$src = 'data:image/png;base64,'.$imageString;
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PrizmServlet</title>
</head>

<body>
<div>
    <a href="index.php">Back</a>
</div>
<div>
    <img src="<?= $src ?>" alt="Scan me!">
</div>
<table>
    <tr>
        <td>Prizm Address:</td>
        <td><b><?= PRIZM_ADDRESS ?></b></td>
    </tr>
    <tr>
        <td>Public key:</td>
        <td><b><?= PRIZM_PUBLIC_KEY ?></b></td>
    </tr>
    <tr>
        <td>Price:</td>
        <td><b><?= $data['price'] ?> pzm</b></td>
    </tr>
    <tr>
        <td>Comment:</td>
        <td><b><?= $data['hash'] ?></b></td>
    </tr>
</table>

</body>
</html>