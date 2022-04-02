<?php


/** Generate HTML for address form
 * @param $data
 */
function generate_address_form($data, $page = "cart") {
    $data = protect_output($data); // XSS protection
    $button_label = ($page == "cart")?"Checkout":"Update";
    echo <<<END
<!-- address -->
        <section id="do_action">
            <h3>Your details</h3>
            <!-- Address -->
            <div class="container-fluid">
                <form method="post" class="register-form">
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-lg-4">
                            <label for="name">Name</label>
                            <input name="name" class="form-control" type="text" id="name" value="$data[name]" required>
                        </div>
                    </div>
END;

    // if is logged, email input is hidden
        echo '    
                <div class="row">
                    <div class="col-md-4 col-sm-4 col-lg-4">
                        <label for="email" '.(is_logged()?"style='display:none;'":"").'>Email</label>
                        <input name="email" class="form-control" id="email" value="'.$data["email"].'" required type="'.(is_logged()?"hidden":"email").'">
                    </div>
                </div>';


     echo <<<END
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-lg-4">
                            <label for="tel">Tel. number</label>
                            <input name="tel" class="form-control" type="tel" id="tel" value="$data[tel]" required>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-lg-4">
                            <label for="address1">Address <small>line 1</small></label>
                            <input name="address1" class="form-control" type="text" id="address1"  value="{$data["address"]["line_1"]}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-lg-4">
                            <label for="address2">Address <small>line 2</small></label>
                            <input name="address2" class="form-control" type="text" id="address2"  value="{$data["address"]["line_2"]}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-lg-4">
                            <label for="town">Town / City</label>
                            <input name="town" class="form-control" type="text" id="town" value="{$data["address"]["town"]}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-lg-4">
                            <label for="postcode">Postcode</label>
                            <input name="postcode" class="form-control" type="text" id="postcode"  value="{$data["address"]["postcode"]}" required>
                        </div>
                    </div>


                    <hr>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                            <button class="btn btn-default regbutton">$button_label</button>
                        </div>

                    </div>
                </form>
            </div>
        </section><!-- end - address -->

END;
}

/**
 * Generate HTML for cart table
 * @param $data
 */
function generate_cart_table($data) {

    //if it is not empty
    // get data for the cart table
    $total_price = 0;
    $total_quantity = 0;
    $cart_data = [];
    $collection = select_collection("products");
    foreach ($data as $item) {
        $product_info = $collection->findOne(["_id"=>get_object_id($item["product_id"])]);
        if ($product_info && $item["quantity"] > 0) {
            $row["product_id"] = $item["product_id"];
            $row["photo"] = $product_info["photos"][0];
            $row["quantity"] = $item["quantity"];
            $row["product_name"] = $product_info["name"];
            $row["price"] = $product_info["price"];
            $row["price_total"] = $row["price"] * $row["quantity"];

            $total_price += $row["price_total"];
            $total_quantity += $row["quantity"];

            $cart_data[] = $row;
        }
    }

    // sanitize output
    $cart_data = protect_output($cart_data);


    $cart_header = <<<END
<!-- cart items -->
        <section id="cart_items">
            <div class="container">

                <div class="table-responsive cart_info">
                    <table class="table table-condensed">
                        <thead>
                        <tr class="cart_menu">
                            <td class="image">Item</td>
                            <td class="description">Quantity</td>
                            <td class="price">Price</td>

                        </tr>
                        </thead>
                        <tbody>
                        
END;
    function cart_footer($price, $quantity) {
        return <<<END
                       
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>Total: </td><td data-quantity="$quantity" id="total-quantity">$quantity</td><td data-price="$price" id="total-price">£$price</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section> <!--/#cart_items-->
END;
    }

    function cart_row($d) {
        return <<<END
                        <tr id="row-{$d["product_id"]}">
                            <td class="cart_product">
                                <a href=""><img src="images/product-images/{$d["photo"]}" width="140" height="80" alt="{$d["product_name"]}"></a>
                            </td>
                            <td class="cart_description">
                                <h4><a href="product.php?id={$d["product_id"]}">{$d["product_name"]}</a></h4>
                                <p>
                                <input type="number" value="$d[quantity]" class="form-control" min="0" data-product-id="{$d["product_id"]}" data-product="true" data-product-value="$d[quantity]" data-product-price="$d[price]">
                                 <i class="loader" id="loader-{$d["product_id"]}" style=""></i>
                                 </p>
                            </td>                           
                            <td class="cart_price">
                                <p>£$d[price]</p>
                                <p><b id="price-{$d["product_id"]}" data-price="$d[price_total]">£$d[price_total]</b></p>
                            </td>

                        </tr>

END;
    }


 // display HTML
    echo $cart_header;
    foreach ($cart_data as $item) {
        echo cart_row($item);

    }
    echo cart_footer($total_price, $total_quantity);

}