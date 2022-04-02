<?php
session_start();

include "Files/database.php";
include "Files/user.php";
include 'Files/layout.php';

// Mongo DB autoloading
// mongoDB functions
require("libraries/mongodb/src/functions.php");
function autoloadFunction($class) {
    $namespace = explode("\\", $class);

    if ($namespace[0] == "MongoDB") {
        unset($namespace[0]);
        $path = implode("/", $namespace);
        require("libraries/mongodb/src/" . $path . ".php");
        return;
    }
}
// classes autoload
spl_autoload_register("autoloadFunction");



// update last visit record
update_last_visit();

// delete carts form not visited accounts
delete_old_carts();


// Ensure that at least one Staff user is in the DB
set_default_staff_user();


// number of items in cart
$in_cart_common = count_items_in_cart(get_user_id());



// ****************************************************
// *
// * functions for users and authentication
// *
// ****************************************************

/**
 * Returns id of logged user, if not logged, returns session id
 * @return bool
 */
function get_user_id() {
    if (isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    } else {
        return session_id();
    }
}

/**
 * Returns role of logged user, if not logged, returns "guest"
 * @return bool
 */
function get_user_role() {
    if (isset($_SESSION["user"]["role"])) {
        return $_SESSION["user"]["role"];
    } else {
        return "guest";
    }
}

/**
 * Returns true if the user is logged in
 * @return bool
 */
function is_logged() {
    return (isset($_SESSION["user"]["id"]));
}

/**
 * returns true if the user is logged in and it is a customer
 * @return bool
 */
function is_customer() {
    return (isset($_SESSION["user"]["id"]) && $_SESSION["user"]["role"] == "customer");
}


/**
 * returns true if the user is logged in and it is a staff
 * @return bool
 */
function is_staff() {
    return (isset($_SESSION["user"]["id"]) && $_SESSION["user"]["role"] == "staff");
}


/**
 * If the user is logged in, he is redirected and program is terminated
 * @return bool
 */
function page_for_guest() {
    if (isset($_SESSION["user"]["id"])) {
        header("Location: /");
        exit;
    }

    return true;
}

/**
 * If the user is not logged in customer, he is redirected and program is terminated
 * @return bool
 */
function page_for_customer() {
    if (!isset($_SESSION["user"]["id"]) || $_SESSION["user"]["role"] != "customer") {
        header("Location: /");
        exit;
    }

    return true;
}

/**
 * If the user is not logged staff, he is redirected and program is terminated
 * @return bool
 */
function page_for_staff() {
    if (!isset($_SESSION["user"]["id"]) || $_SESSION["user"]["role"] != "staff") {
        header("Location: /");
        exit;
    }

    return true;
}


function set_success($message) {
    $_SESSION["message"]["success"][] = $message;
}


function set_error($message) {
    $_SESSION["message"]["error"][] = $message;
}




function protect_output($data) {
    if(!is_array($data)) {
        return htmlspecialchars($data, ENT_QUOTES);
    } else {
        $protectString = function(&$string) {
            $string = htmlspecialchars($string, ENT_QUOTES);
        };
        array_walk_recursive($data, $protectString);
        return $data;
    }
}


function protect_input($data) {
    return (string)trim($data);
}



function set_script($path) {
    // add script only if there is not yet
    if (!isset($_SESSION["script"]) || false === array_search($path, $_SESSION["script"])) {
        $_SESSION["script"][] = $path;
    }
}