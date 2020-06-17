<?php
include 'config.php';

$items = items_list();
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PrizmServlet</title>
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
</head>

<body>
    <div class="main">
        <div class="leftcol">
            <form action="create_order.php" method="post">
                <div>
                    <label for="item">Item/Service</label>
                    <select name="item" id="item">
                        <?php foreach ($items as $item_id => $item) {?>
                            <option value="<?= $item_id ?>"><?= $item['name'] ?> (<?= $item['price'] ?>pzm)</option>
                        <?php }?>
                    </select>
                </div>
                <br><br><br>
                <div>
                    <input type="submit" value="Create order">
                </div>
            </form>
        </div>
        <div class="rightcol">
            <div id="orders"></div>
        </div>
        <div class="clear"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="/assets/js/all.js"></script>
</body>
</html>