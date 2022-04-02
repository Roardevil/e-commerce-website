<?php


//require
require "common.php";
include "Files/orders_detail.php";
include_once "Files/user.php";
include_once "Files/orders.php";

$title = "Order History";

page_for_customer();

// detail of a order
if (isset($_GET["id"])) {


    // get the order details
    $order = get_order_details(get_object_id($_GET["id"]));

    if (!$order || $order["customer_details"]["user_id"] != get_user_id()) {
        // if the order doesn't exist or it is not current user's order
        set_error("This order doesn't exist.");
        header("Location: order_history.php");
        exit();
    } else {

        // change order status
        if (isset($_POST["status"]) && $_POST["status"] == "canceled") {
            change_order_status($_POST["status"], $order["id"]);
            set_success("The order was canceled");
            header("location: ".$_SERVER["REQUEST_URI"]);
            exit();
        }


        $show_order_detail = true;
        $title = "Order detail";
    }
} else {
    // list of orders
    $orders = get_orders(get_user_id());
}


// generate HTML
generate_header($title, $in_cart_common);
generate_page_title($title);

if (isset($show_order_detail)) {
    generate_order_detail("customer", $order);
} else {
    generate_orders_list($orders);
}


generate_footer();

