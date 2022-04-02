<?php
//require
require "common.php";
include "Files/cart.php";
include "Files/orders_detail.php";
include "Files/products_detail.php";
include_once "Files/user.php";
include_once "Files/cart_detail.php";
include_once "Files/orders.php";
include_once "Files/products.php";

$title = "Cart";

// get data
$cart_data = get_cart(get_user_id());
$user_data = get_user_address(get_user_id());

// save order
if (isset($_POST["email"])) {
    $form_data = check_cart_form($_POST);

    // if data are correct, save order
    if ($form_data && $cart_data) {
        save_user_details($form_data, get_user_id());
        $order_saved = save_order($form_data, $cart_data, get_user_id());
        $order_id = $order_saved->getInsertedId();
        $new_order_data = get_order_details($order_id);
        $title = "Order Summary";
        $in_cart_common = 0;
    }
}
// if the cart is empty
elseif (!$cart_data) {
    set_error("Your cart is empty.");
}



// generate HTML
generate_header($title, $in_cart_common);
generate_page_title($title);

if (isset($order_saved)) { // show order summary
    generate_order_detail(get_user_role(), $new_order_data);
    echo "saved";
} else {

    if($cart_data) {
        // if the cart is not empty
        generate_cart_table($cart_data);
    }

    generate_address_form($user_data);

}
generate_recomendation(get_recommended_data(get_user_id()));
generate_footer();

