<?php

function generate_products_sidebar($data) {
    echo <<<END
        <!-- side bar -->
        <div class="col-sm-3 left-sidebar-cover">
            <div class="left-sidebar">

                <!--Sorting-->

                <h3>Sort by</h3>
                <div class="radio">
                    <label>
                        <input type="radio" name="sort" onchange="sort_obj.sortProducts('price', 0)">
                        Price: Lowest first
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="sort" onchange="sort_obj.sortProducts('price', 1)">
                        Price: Highest first
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="sort" onchange="sort_obj.sortProducts('ordered_quantity', 1)">
                        Popularity
                    </label>
                </div>
                <hr>
                <div id="sort-filter">
END;

    // filtering by gender
    if (isset($data["gender"])) {
        ksort($data["gender"]);
        echo '<!--Men or Women-->

                <h3>Gender</h3>';
        echo "<div class='brands-name'>";
        foreach ($data["gender"] as $key => $item) {
            echo '<div class="checkbox">
                        <label>
                            <input type="checkbox" value="" data-filter="gender" data-filter-value="'.$key.'" onchange="sort_obj.filter()">
                            '.ucfirst($key).' <span> ('.$item.') </span>
                        </label>
                    </div>';
        }
    }
    echo "</div>";

    // filtering by brand
    if (isset($data["brand"])) {
        ksort($data["brand"]);
        echo '<!--Brand-->

                <h3>Brand Name</h3>';
        echo "<div class='brands-name'>";
        ksort($data["brand"]);
        foreach ($data["brand"] as $key => $item) {
            echo '<div class="checkbox">
                        <label>
                            <input type="checkbox" value="" data-filter="brand" data-filter-value="'.$key.'" onchange="sort_obj.filter()">
                            '.ucfirst($key).' <span> ('.$item.') </span>
                        </label>
                    </div>';
        }
    }
    echo "</div>";

    // filtering by price
    if (isset($data["price"])) {
        ksort($data["price"]);
        echo '<!--Price-->

                <h3>Price Range</h3>';
        echo "<div class='brands-name'>";
        foreach ($data["price"] as $key => $item) {
            echo '<div class="checkbox">
                        <label>
                            <input type="checkbox" value="" data-filter="price" data-filter-value="'.$key.'" onchange="sort_obj.filter()">
                            £'.($key-100).' - £'.$key.' <span> ('.$item.') </span>
                        </label>
                    </div>';
        }
    }
    echo "</div></div>";

    echo "</div></div>";

}

/**
 * Generate HTML for one item in products list
 * @param $data
 */
function generate_product_item($data, $sort = false) {
    $id = protect_output($data["_id"]);
    $name = protect_output($data["name"]);
    $brand = protect_output($data["brand"]);
    $photo = protect_output($data["photos"][0]);
    $price = protect_output($data["price"]);
    //set_script("cart.js");

    if ($sort) {
        $identificator = " id='product-$id'";
    } else {
        $identificator = "";
    }
    echo <<<END

        <div class="col-md-3  product-item" $identificator data-product-id="$id">
            <article class="article-intro">
                <a href="product.php?id=$id" class="product-item-cover">
                    <img class="img-responsive img-rounded" src="images/product-images/$photo" alt="$name">
                </a>
                <h3>
					<a href="product.php?id=$id">$brand</a>
                </h3>
                    <p>$name</p>
					<p>£$price</p>
					<form onsubmit="return add_into_cart(event, this);">
					    <input type="hidden" name="product-quantity" value="1">
					    <input type="hidden" name="product-id" value="$id">					
                        <input name="Add" value="Add to Cart" type="submit" class="btn btn-default">
                        <div class='label label-success'>Added into Cart</div>
                    </form>
            </article>
        </div>

END;

}




function generate_product_detail($data) {

    $data["gender"] = ucfirst($data["gender"]);
    $data["description"] = nl2br($data["description"]);
    $data["category"] = implode(", ", $data["category"]);
    $data = protect_output($data);


    echo <<<END
<div class="row">
        <div class="col-sm-9 padding-right">
            <div class="product-details"><!--product-details-->
                <div class="col-sm-5">
                    <div class="view-product">
                        <img src="images/product-images/{$data["photos"][0]}" alt="$data[name]">
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="product-information"><!--/product-information-->
                        <h2>$data[name]</h2>
                        <span>
									<span>£$data[price]</span>
									<label>Quantity:</label>
									<form onsubmit="return add_into_cart(event, this);">
                                        <input type="number" value="1" min="1" max="$data[quantity]" name="product-quantity">
                                        <input type="hidden" value="$data[id]" name="product-id">
                                        <button class="btn btn-default cart">
                                            <i class="fa fa-shopping-cart"></i>
                                            Add to cart
                                        </button>
                                        <div class='label label-success'>Added into Cart</div>
									</form>
								</span>
                        <p><b>Availability:</b> In Stock</p>
                        <p><b>Gender:</b> $data[gender]</p>
                        <p><b>Size:</b> $data[size]</p>
                        <p><b>Categories:</b> $data[category]</p>
                        <p><b>Description:</b> </p>
                        <p>$data[description]</p>

                    </div><!--/product-information-->
                </div>
            </div><!--/product-details-->

        </div>
    </div>



END;

}

/**
 * Generate HTML for recommendation section
 * @param $data
 */
function generate_recomendation($data) {
    echo "<section class=\"row\">
        <h3 class=\"text-center\">Recommended for you</h3>";
    foreach ($data as $item) {
        generate_product_item($item);
    }

    echo "</section>";
}

/** Generate JavaScript Scripts for sending data about products to the client
 * @param $data
 */
function generate_products_json($data) {
    for ($i = 0; $i < count($data); $i++) {
        if (!isset($data[$i]["ordered_quantity"])) {
            $data[$i]["ordered_quantity"] = 1;
        }
    }
    echo "<script>";
    echo "sort_data = ".json_encode($data, JSON_UNESCAPED_UNICODE).";";
    echo "var sort_obj = new SortProducts(sort_data);";
    echo "sort_obj.init();";
    echo "</script>";
}