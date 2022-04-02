<?php
//require
require "common.php";
include "Files/products.php";
include "Files/products_detail.php";


//Connect to MongoDB
$collection = (new MongoDB\Client)->Ecomerce;

// if there is searching
if (isset($_GET["search"])) {
    //Extract the data that was sent to the server
    $name= filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);

//Create a PHP array with our search criteria
    $findCriteria = [
        '$and' => [
            ["name" => $name],
            ["quantity" => ['$gt' => 0]]
        ]

    ];
    $search_html = "<p>Search phrase: <strong>$name</strong></p>";
    $title = "Search Results";
} else {
    // if there is not searching
    $findCriteria = ["quantity" => ['$gt' => 0]];
    $search_html = "";
    $title = "Products";
}


//Find all of the customers that match  this criteria
$products = $collection->products->find($findCriteria);
$products = mongo_to_array($products);


generate_header($title, $in_cart_common);

echo '

    <div class="row page-intro">
        <div class="col-lg-12  ">
            <h2>'.$title.'</h2>
            '.$search_html.'
        </div>
    </div>';

echo "<div class=row>";
echo "<script src='js/product_sort.js'></script>";
$sidebar_data = get_sidebar_data($products);
generate_products_sidebar($sidebar_data);
echo "<div id='products-cover'>";



foreach ($products as $p) {
    generate_product_item($p, true);
}
echo "<div style='clear: both'></div></div>";
echo "</div>";

generate_products_json($products);

generate_footer();
