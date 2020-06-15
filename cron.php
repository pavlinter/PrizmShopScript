<?php
include 'config.php';
include 'PrizmApi.php';


$prizmApi = new PrizmApi(SERVLET_URL, NODA_URL);
$last_id = $prizmApi->getLastPzmHistory();


$transactions = $prizmApi->servlet_history($last_id);

if ($prizmApi->hasError()) {
    echo '<pre>';
    echo print_r($prizmApi->result);
    echo '</pre>';
    echo 'Error';
} else {

    foreach ($transactions as $transaction) {
        /*[
            'id' => $tr_id,
            'date' => $tr_date,
            'timestamp' => $tr_timestamp,
            'from' => $tr_address,
            'price' => $tr_price,
            'comment' => $tr_comment,
        ];*/


        $data = [
            'tr_id' => $transaction['id'],
            'address' => $transaction['from'],
            'price' => $transaction['price'],
            'comment' => $transaction['comment'],
            'tr_timestamp' => $transaction['timestamp'],
            'tr_date' => date("Y-m-d H:i:s", $transaction['timestamp']),
            'status' => HISTORY_STATUS_PENDING,
        ];
        $sql = "INSERT INTO pzm_history (tr_id, address, price, comment, tr_timestamp, tr_date, status, created_at) VALUES (?,?,?,?,?,?,?,NOW())";
        $added = $db->prepare($sql)->execute([$data['tr_id'], $data['address'], $data['price'], $data['comment'], $data['tr_timestamp'], $data['tr_date'], $data['status']]);

        if ($added){
            if ($transaction['comment'] && $transaction['price'] > 0) {

                $stmt = $db->prepare('SELECT * FROM pzm_order WHERE hash=?');
                $stmt->bindParam(1, $transaction['comment'], PDO::PARAM_STR);
                $stmt->execute();
                $pzmOrder = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($pzmOrder) {

                    if ($transaction['price'] == $pzmOrder['price']) {
                        //success

                        $sql = "UPDATE pzm_order SET status=? WHERE id=?";
                        $pdo->prepare($sql)->execute([ORDER_STATUS_SUCCESS, $pzmOrder['id']]);

                        $sql = "UPDATE pzm_history SET status=? WHERE id=?";
                        $pdo->prepare($sql)->execute([HISTORY_STATUS_USED, $pzmOrder['id']]);

                    } else if ($transaction['price'] > $pzmOrder['price']){
                        log_error('cron - Error: paid too much #' . $pzmOrder['id']);

                        $sql = "UPDATE pzm_order SET status=? WHERE id=?";
                        $pdo->prepare($sql)->execute([ORDER_STATUS_ERROR, $pzmOrder['id']]);

                    } else {
                        log_error('cron - Error: not paid enough #' . $pzmOrder['id']);

                        $sql = "UPDATE pzm_order SET status=? WHERE id=?";
                        $pdo->prepare($sql)->execute([ORDER_STATUS_ERROR, $pzmOrder['id']]);
                    }
                }
            }
        }
    }

}
