<?php
//require
require "common.php";
include_once "Files/user.php";
include_once "Files/products.php";
include_once "Files/cart.php";
include_once "Files/cart.php";
include_once "Files/products_detail.php";

$title = "Update Address";


$user_data = get_user_address(get_user_id());

// save order
if (isset($_POST["email"])) {
    $form_data = check_cart_form($_POST);

    // if data are correct, save order
    if ($form_data) {
        save_user_details($form_data, get_user_id());
        set_success("Your details were changed.");
        $user_data = get_user_address(get_user_id());
    }
}



// generate HTML
generate_header($title, $in_cart_common);
generate_page_title($title);


generate_address_form($user_data, "update");

generate_recomendation(get_recommended_data(get_user_id()));
generate_footer();

