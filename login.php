<?php
//require common function
require "common.php";

// redirect if is logged
page_for_guest();

// if the form was sent
if (isset($_POST["email"])) {
	//connect to database
	$collection = (new MongoDB\Client)->Ecomerce->users;

	//Convert to PHP array
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	$dataArray = [ 
	   "email" => strtolower(trim($email)),
	   "password" => trim($password),
	   ];

	// check the inputs
	$error = false;
	if (empty($dataArray["email"])) {
		$error = true;
		$_SESSION["message"]["error"][] = "Email Required";
	}
	
	 if (empty($dataArray["password"])) {
		$error = true;
		set_error("Password Required");
	}


	if (!$error) { // if the fields are filled
		$document = $collection->findOne(['$and' =>[['email' => $dataArray['email']], ["role" => ['$ne' => "guest"]]]]);
		if ($document == null) { // if the email doesn't exist
			set_error("Email Doesn't Exist");
		
		} else{
		    // if the email exists

            // check passsword
			if (password_verify ($dataArray["password"], $document ["password"]) ) {

			        // log in
					$_SESSION ["user"]["id"] = $document ["_id"];
					$_SESSION ["user"]["role"] = $document ["role"];

					set_success("You Are Logged In");
					if ($document ["role"] == "staff") {
						header("Location: manage_products.php");
						exit();
					}
					// redirecting to home page
					header("Location: index.php");
					exit();
			

			}
			else {
			    // the password is wrong
				set_error("Wrong Password");
			
			}
			
			
		}
	
	}

}


generate_header("Login", $in_cart_common);



include 'Files/login_form.php';
login_form();

generate_footer();

