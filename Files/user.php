<?php


/**
 * Update last visit record in DB for the current user
 */
function update_last_visit() {
    if (!is_logged()) {
        // if there is not an account for guest yet, it is created
        if (!select_collection("users")->findOne(["_id" => session_id()])) {
            select_collection("users")->insertOne(["_id" => session_id(), "role" => "guest"]);
        }
    }

    select_collection("users")->updateOne(
        ["_id" => get_user_id()],
        ['$set' => [
            "last_visit" => time()
        ]]
    );
}

/**
 * Delete carts from account which weren't wisited for 10 days
 */
function delete_old_carts() {
    $old_carts = select_collection("users")->find(["last_visit" => ['$lt' => (time() - (1000*60*60*24*10))]]);

    foreach ($old_carts as $old) {
        // add products back to offer
        foreach ($old["cart"] as $item) {
            select_collection("products")->updateOne(
                ["_id" => $item["_id"]],
                ['$inc' => ["quantity" => $item["quantity"], "ordered_quantity" => (0-$item["quantity"])]]
            );
        }

        // remove cart from the account
        select_collection("users")->updateOne(
            ["_id" => get_user_id()],
            ['$set' => [
                "cart" => []
            ]]
        );
    }
}

/**
 * Return user Address, false if doesn't exists
 * @param $id string|object
 * @return array|false
 */
function get_user_address($id) {
    $data = select_collection("users")->findOne(["_id" => $id]);
    if (!$data) {
        $return = false;
    } else {
        $return["name"] = (isset($data["name"]))?$data["name"]:"";
        $return["email"] = (isset($data["email"]))?$data["email"]:"";
        $return["tel"] = (isset($data["tel"]))?$data["tel"]:"";
        $return["address"]["line_1"] = (isset($data["address"]["line_1"]))?$data["address"]["line_1"]:"";
        $return["address"]["line_2"] = (isset($data["address"]["line_2"]))?$data["address"]["line_2"]:"";
        $return["address"]["town"] = (isset($data["address"]["town"]))?$data["address"]["town"]:"";
        $return["address"]["postcode"] = (isset($data["address"]["postcode"]))?$data["address"]["postcode"]:"";
    }

    return $return;
}

/**
 * Saves user data to DB
 * @param $data
 * @param $user_id
 */
function save_user_details($data, $user_id) {
    $query = [
        "name" => protect_input($data["name"]),
        "email" => protect_input($data["email"]),
        "tel" => protect_input($data["tel"]),
        "address" => [
            "line_1" => protect_input($data["address1"]),
            "line_2" => protect_input($data["address2"]),
            "town" => protect_input($data["town"]),
            "postcode" => protect_input($data["postcode"]),
        ]
    ];

    // delete cart from users collection
    select_collection("users")->updateOne(
        ["_id" => $user_id],
        ['$set' => $query]
    );
}

/** Store to DB information about product which the user visits
 * @param $user_id
 * @param $product_id
 */
function track_user($user_id, $product_id) {
    if (is_string($user_id)) {
        $user_id = protect_input($user_id);
    }

    // get product data
    $product_data = select_collection("products")->findOne(["_id" => $product_id]);

    // if product exists
    if ($product_data && isset($product_data["category"]) && count($product_data["category"]) > 0) {
        $user_data = select_collection("users")->findOne(["_id" => $user_id]);
        $user_data = mongo_to_array($user_data);
        $user_tracking = (isset($user_data["tracking"]))?$user_data["tracking"]:[];

        // add product categories to user tracking
        $category = $product_data["category"];
        $category[] = strtolower($product_data["brand"]);
        $category[] = strtolower($product_data["gender"]);
        $category[] = strtolower("size-".$product_data["size"]);

        // increase quantity
        foreach ($category as $key => $value) {
            $value = str_replace(".", "", $value); // delete dots
            if (!isset($user_tracking[$value])) {
                $user_tracking[$value] = 0;
            }
            $user_tracking[$value] += 1;
        }

        // update database
        select_collection("users")->updateOne(
            ["_id" => $user_id],
            ['$set' =>
                ["tracking" => $user_tracking]
            ]
        );
    }
}

/**
 * If there is not any staff user, the default one is added
 */
function set_default_staff_user() {
    // if there is not any staff user
    if (!select_collection("users")->findOne(["role" => "staff"])) {
        $user_data["email"] = "staff@t-shirtapparel.com";
        $user_data["password"] = password_hash("staff", 1);
        $user_data["role"] = "staff";

        // insert to DB
        select_collection("users")->insertOne($user_data);
    }
}


/**
 * Returns number of items in the cart
 * @param $user_id
 * @return int
 */
function count_items_in_cart($user_id) {
    $collection = select_collection("users");

    $cart_db = $collection->findOne(["_id" => $user_id]);

    if (!$cart_db || !isset($cart_db["cart"]) || count($cart_db["cart"]) == 0) {
        return 0;
    } else {
        $count = 0;
        foreach ($cart_db["cart"] as $cart) {
            $count += $cart["quantity"];
        }

        return $count;
    }
}


