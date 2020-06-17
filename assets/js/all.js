$(function(){
    function loadOrders(){
        $.ajax({
            url: "ajax.php",
            type: "POST",
            dataType: "html",
            data: {}
        }).done(function (d) {
            $("#orders").html(d);
        });
        setTimeout(loadOrders, 5000);
    }
    loadOrders();
});
