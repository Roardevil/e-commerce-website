<?php


// include common files
require "common.php";
include "Files/products_detail.php";
include "Files/products.php";

// there is not $_GET["id"] or is empty
if (empty($_GET["id"])) {
    header("Location: /");

    exit();
}


// Product details:
$product_details = get_product($_GET["id"]);

if (!$product_details) {
    // the product doesn't exist
    set_error("This product does not exist");
    //header("Location: products.php");
    exit();
}

// track user
track_user(get_user_id(), get_object_id($_GET["id"]));



// generate HTML
generate_header("Product", $in_cart_common);
generate_product_detail($product_details);

generate_recomendation(get_recommended_data(get_user_id()));

generate_footer();