<?php

//require
require "common.php";
include "Files/orders_detail.php";
include_once "Files/user.php";
include_once "Files/orders.php";

$title = "Order History";

page_for_staff();

// detail of a order
if (isset($_GET["id"])) {


    // get the order details
    $order = get_order_details(get_object_id($_GET["id"]));

    if (!$order) {
        // if the order doesn't exist
        set_error("This order doesn't exist.");
        header("Location: manage_orders.php");
        exit();
    } else { // order id exists

        // change order status
        if (isset($_POST["status"])) {
            change_order_status($_POST["status"], $order["id"]);
            set_success("The order status was changed");
            header("location: ".$_SERVER["REQUEST_URI"]);
            exit();
        }

        // change date of arriving
        if (!empty($_POST["date"])) {
            change_arriving_date($_POST["date"], $order["id"]);
            set_success("The order arriving date was changed");
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
generate_header($title);
generate_page_title($title);

if (isset($show_order_detail)) {
    generate_order_detail("staff", $order);
} else {
    generate_orders_list($orders);
}


generate_footer();
