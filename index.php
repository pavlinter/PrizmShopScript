<?php
include 'config.php';
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PrizmServlet</title>
</head>

<body>
    <form action="create_order.php" method="post">
        <div>
            <label for="item">Item/Service</label>
            <select name="item" id="item">
                <option value="1">Item 1 (1pzm)</option>
                <option value="2">Item 2 (2pzm)</option>
                <option value="3">Item 3 (3pzm)</option>
            </select>
        </div>
        <br><br><br>
        <div>
            <input type="submit" value="Оплатить">
        </div>
    </form>
</body>
</html>