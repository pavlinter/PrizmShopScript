<?php
include 'config.php';
$orders = getSuccessOrders();
?>
<div class="panel">
    <h2>Success orders</h2>
    <?php if ($orders) {?>
        <div class="order-list">
            <?php foreach ($orders as $order) {?>
                <?php
                $tr = getTransaction($order['hash'])
                ?>
                <div class="order-list-row">
                    <div><b>#<?= $order['id'] ?></b></div>
                    <?php if ($tr) {?>
                        <div>From: <b><?= $tr['address'] ?></b></div>
                    <?php }?>
                    <div>Price: <b><?= $order['price'] ?></b></div>
                    <div>Comment: <b><?= $order['hash'] ?></b></div>
                    <div>Date: <b><?= $order['created_at'] ?></b></div>
                </div>
            <?php }?>
        </div>
    <?php } else {?>
        ...
    <?php }?>
</div>


