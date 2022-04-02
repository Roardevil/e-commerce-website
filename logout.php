<?php


require "common.php";

if (is_logged()) {
    // delete session
    unset($_SESSION["user"]);
    set_success("You are logged out.");
}

header("Location: index.php");
exit();