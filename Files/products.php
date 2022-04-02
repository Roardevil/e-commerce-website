<?php

function get_product($id) {
    // connect to mongodb server
    $data = select_collection("products")->findOne(["_id" => get_object_id($id)]);

    if (!$data) { // product doesn't exit
        return false;
    } else {
        $product_data = mongo_to_array($data);
        $product_data["id"] = $product_data["_id"];

        return $product_data;
    }
}


/**
 * Returns array of IDs of the most popular products
 * @param $limit
 * @return array|bool
 */
function get_most_popular($limit) {
    $products = select_collection("products")->find(
        ["quantity" => ['$gt' => 0]],
        [
            "limit"=>$limit,
            "sort" => ["ordered_quantity" => 1],
            "projection" => [
                "_id" => 1,
                "ordered_quantity" => 1
            ]
        ]);
    $popular_ids = [];
    foreach ($products as $p) {
        $popular_ids[] = $p["_id"];
    }

    return $popular_ids;
}


function get_recomendation($limit, $user_id) {
    $user_tracking = select_collection("users")->findOne(["_id" => $user_id]);
    if ($user_tracking && isset($user_tracking["tracking"]) && count($user_tracking["tracking"]) > 0) {
        // there are some tracking data
        $tracking = mongo_to_array($user_tracking["tracking"]);
        asort($tracking);
        $tracking = array_reverse($tracking);
        $tracking_id = array_keys($tracking);

        // products data
        $recommend = [];
        $products_db = select_collection("products")->find(["quantity" => ['$gt' => 0]]);
        if ($products_db) {
            $products = mongo_to_array($products_db);
            foreach ($products as $item) {
                // get all category
                $category = $item["category"];
                $category[] = $item["brand"];
                $category[] = $item["gender"];
                $category[] = "size-".$item["size"];

                $score = 0;
                // add score
                foreach ($category as $c) {
                    $c = (string)$c;
                    if (array_key_exists($c, $tracking)) {
                        $score += $tracking[$c];
                    }
                }

                if ($score) {
                    $recommend[(string)$item["_id"]] = $score;
                }
            }

        }

        // sort recommend
        asort($recommend);
        $recommend = array_slice(array_reverse($recommend), 0, $limit);
        $recommend = array_keys($recommend);
    } else {
        $recommend = false;
    }

    return $recommend;
}

/**
 * Return data of recommended items
 * @param $user_id
 * @return array
 */
function get_recommended_data($user_id) {
    $limit = 4;

    $recommend_ids = get_recomendation($limit, $user_id);
    if (!$recommend_ids) {$recommend_ids = [];}
    if (count($recommend_ids) < $limit) {
        $popular_ids = get_most_popular($limit - count($recommend_ids));
        $recommend_ids = array_merge($recommend_ids, $popular_ids);
    }

    $data = [];

    foreach ($recommend_ids as $recommend) {
        $data[] = get_product($recommend);
    }

    return $data;

}

/**
 * Return data of featured items
 * @param $limit
 * @return array
 */
function get_featured_data($limit) {

     $popular_ids = get_most_popular($limit);

    $data = [];

    foreach ($popular_ids as $popular) {
        $data[] = get_product($popular);
    }

    return $data;
}

/**
 * Generate data for sidebar on the product page
 * @param $data
 * @return array
 */
function get_sidebar_data($data) {
    $result = [];
    foreach ($data as  $d) {
        $result["gender"][$d["gender"]] = (isset($result["gender"][$d["gender"]]))?$result["gender"][$d["gender"]]+1:1;
        $result["size"][$d["size"]] = (isset($result["size"][$d["size"]]))?$result["size"][$d["size"]]+1:1;
        $result["brand"][$d["brand"]] = (isset($result["brand"][$d["brand"]]))?$result["brand"][$d["brand"]]+1:1;
        $price_range = ceil($d["price"]/100) * 100;
        $result["price"][$price_range] = (isset($result["price"][$price_range]))?$result["price"][$price_range]+1:1;
    }

    return $result;
}