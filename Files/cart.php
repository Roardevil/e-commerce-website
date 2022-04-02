<?php



function get_cart($user_id) {
    $cart_db = select_collection("users")->findOne(["_id" => $user_id]);

    // cart is empty
    if (!$cart_db || !isset($cart_db["cart"]) || count($cart_db["cart"]) == 0) {
        return false;
    } else {
        $cart = [];
        foreach ($cart_db["cart"] as $item) {
            if ($item["quantity"] > 0) {
                $cart[] = $item;
            }
        }
        return $cart;
    }
}

/**
 * Update cart in DB
 * @param $cart
 * @param $user_id
 */
function update_cart_in_db($cart, $user_id) {
    $collection = select_collection("users");

    $update = $collection->updateOne(
        [
            "_id" => $user_id
        ],
        [
            '$set' => ["cart" => $cart]
        ]
    );
}


function decrease_product_quantity($product_id, $quantity) {
    return select_collection("products")->updateOne(
        ["_id" => get_object_id($product_id)],
        ['$inc' => ["quantity" => (0-$quantity), "ordered_quantity" => $quantity]]);
}

/**
 * Check form in the cart page
 * @param $data
 * @return bool
 */
function check_cart_form($data) {
    $error = false;

    // check inputs
    if (empty($data["name"])) {
        set_error("Fill in your name.");
        $error = true;
    }
    if (empty($data["email"])) {
        set_error("Fill in your email.");
        $error = true;
    }
    if (empty($data["tel"])) {
        set_error("Fill in your telephone.");
        $error = true;
    }
    if (empty($data["address1"])) {
        set_error("Fill in your Address line 1.");
        $error = true;
    }
    if (empty($data["town"])) {
        set_error("Fill in your town.");
        $error = true;
    }
    if (empty($data["postcode"])) {
        set_error("Fill in your postcode.");
        $error = true;
    }

    if ($error) {
        return false;
    } else {
        return $data;
    }

}



