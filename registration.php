<?php
//require common functions
require "common.php";
include 'Files/registration_form.php';

// redirect if is logged
page_for_guest();

// if the registration form was sent
if (isset($_POST["email"])) {
	//connect to database
	$collection = select_collection("users");

	//Convert to PHP array
	$username = protect_input($_POST["name"]);
	$email = protect_input($_POST["email"]);
	$password = protect_input($_POST["password"]);
	$passwordagain = protect_input($_POST["password_again"]);
	$dataArray = [ 
	   "name" => trim($username),
	   "email" => strtolower(trim($email)),
	   "password" => trim($password),
	   "passwordagain" => trim($passwordagain),
	   ];

	// check if the inputs are filled in
	$error = false;
	if (empty($dataArray["name"])) {
		$error = true;
		set_error("Name Required");
	}
	   
	if (empty($dataArray["email"])) {
		$error = true;
        set_error("Email Required");
	}
	else {
	    // check if the email doesn't exists in DB
	    $document = $collection->findOne(['$and' => [['email' => $dataArray['email']],
                                                    ["role" => ['$ne' => "guest"]]]]);
		if ($document != null) {
			$error = true;
            set_error("Email Already in Our Database, Choose Another Email Address");
		}
	}
	 if (empty($dataArray["password"])) {
		$error = true;
		set_error("Password Required");
	}
	  
	 if (empty($dataArray["passwordagain"])) {
		$error = true;
         set_error("Password Again Required");
	}
	if (($dataArray["passwordagain"] != $dataArray["password"])) {
		$error = true;
        set_error("Password Does Not Match");
	}

	// if the input is correct
	if (!$error) {
	    // hash the password
		$dataArray['password'] = password_hash($dataArray['password'], 1);
		unset($dataArray ['passwordagain']); // delete password again
		$dataArray ['role'] = "customer"; // add role

		// Add new users to the database
		$returnVal = $collection->insertOne($dataArray);

        set_success("YOU are registered");

	
	}

}

// Generate HTML
generate_header("Registration", $in_cart_common);

// show form
registration_form();


generate_footer();

