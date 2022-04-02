<?php

function select_collection($collection) {
    return (new MongoDB\Client)->Ecomerce->$collection;
}

/**
 * Create a new instance of MongoDB object ID
 * @param $id string
 * @return \MongoDB\BSON\ObjectID
 */
function get_object_id($id) {
    return new MongoDB\BSON\ObjectID($id);
}

/**
 * Search the object and returns firt corresponding key if successful, false if not
 * @param $search bool|int|string
 * @param $mongo object mongoDB
 * @return bool|int|string
 */
function mongo_search($search, $mongo) {
    foreach ($mongo as $key => $value) {
        if ($search == $value) {
            return $key;
        }
        if (is_object($value)) {
            if (($k = mongo_search($search, $value)) !== false) {
                return $k;
            }
        }
    }

    return false;
}


function mongo_to_array($mongo_obj) {
    if ($mongo_obj instanceof MongoDB\Driver\Cursor) {
        $array = [];
        foreach ($mongo_obj as $item) {
            $array[] = $item;
        }
    } else {
        $array = (array) $mongo_obj;
    }


    foreach ($array as $key => $value) {
        if ($value instanceof MongoDB\Model\BSONArray || $value instanceof MongoDB\Model\BSONDocument) {
            $array[$key] = mongo_to_array($value);
        }
    }
    return $array;
}