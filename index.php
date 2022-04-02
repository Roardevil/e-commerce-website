<?php
//require
require "common.php";
include "Files/home.php";
include "Files/products_detail.php";
include "Files/products.php";

// Get Data
$featured_data = get_featured_data(5);
$recommended_data = get_recommended_data(get_user_id());


// generate HTML

generate_header("Home", $in_cart_common);
generate_slider($featured_data);
generate_recomendation($recommended_data);
generate_footer();
