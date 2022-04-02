<?php


// common functions
require "common.php";

// data were sent
if (!empty($_POST["product-id"])) {
    $product_id = protect_input($_POST["product-id"]);
    $product_object_id = get_object_id($product_id);
    $product_quantity = intval($_POST["product-quantity"]);

    // check product in db
    $collection = (new MongoDB\Client)->Ecomerce->products;

    $product_db = $collection->findOne(["_id" => $product_object_id]);

    $error = false;
    $error_messages = [];

    if (!$product_db) {
        // product is not in db
        $error = true;
        $error_messages[] = "This product is not in DB";
    } elseif ($product_quantity > $product_db["quantity"]) {
        // User tries to order more than is available
        $error = true;
        $error_messages[] = "The maximum is $product_db[quantity] items.";
    } 
    else {
        // add to the basket
        include "Files/cart.php";
        $cart = get_cart(get_user_id());

        // user already ordered this product => increase quantity
        $exists_in_db = false;
        if ($cart) {
            foreach ($cart as $key => $item) {
                if ($item["product_id"] == $product_id) {
                    $cart[$key]["quantity"] += $product_quantity;
                    $exists_in_db = true;
                    break;
                }
            }
        }
        if (!$exists_in_db) { // add new item
            $cart[] = [
                "product_id" => $product_object_id,
                "product_name" => $product_db["name"],
                "quantity" => $product_quantity
            ];
        }

        // insert cart to db
        update_cart_in_db($cart, get_user_id());

        // decrease quantity of available products
        decrease_product_quantity($product_id, $product_quantity);

        // number of items in cart
        $in_cart = 0;
        foreach ($cart as $c) {
            $in_cart += $c["quantity"];
        }

        // track user
        track_user(get_user_id(), $product_object_id);

    }

    // return results
    if (isset($_GET["ajax"])) {
        $result["result"] = !$error;
        if ($error) {
            $result["error"] = $error_messages;
        } else {
            $result["inCart"] = $in_cart;
        }

        echo json_encode($result);
    } else {
        if ($error) {
            foreach ($error_messages as $er) {
                set_error($er);
            }
        } else {
            set_success("Items was added to the cart");
        }

        header("Location: ".$_SERVER["HTTP_REFERER"]);
        exit();
    }

}