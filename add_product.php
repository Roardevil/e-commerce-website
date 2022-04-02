<?php
//require
require "common.php";

// if the form was send
if (isset($_POST["submit"])) {
	// connect to mongodb server
	$collection = (new MongoDB\Client)->Ecomerce->products;


	//Convert to PHP array
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
	$brand= filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
	$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
	$quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_STRING);

	if (!empty($_FILES["imageToUpload"]["name"])) {

        //Check file data has been sent
        if(!array_key_exists("imageToUpload", $_FILES)){
            echo 'File missing.';
            return;
        }
        if($_FILES["imageToUpload"]["name"] == "" || $_FILES["imageToUpload"]["name"] == null){
            echo 'File missing.';
            return;
        }
        $uploadFileName = $_FILES["imageToUpload"]["name"];

        /*  Check if image file is a actual image or fake image
            tmp_name is the temporary path to the uploaded file. */
        if(getimagesize($_FILES["imageToUpload"]["tmp_name"]) === false) {
            echo "File is not an image.";
            return;
        }

        // Check that the file is the correct type
        $imageFileType = pathinfo($uploadFileName, PATHINFO_EXTENSION);
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            return;
        }

        //Specify where file will be stored
        $target_file = 'images/product-images/' . $uploadFileName;

        /* Files are uploaded to a temporary location.
            Need to move file to the location that was set earlier in the script */
        if (move_uploaded_file($_FILES["imageToUpload"]["tmp_name"], $target_file)) {
        }
        else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // If an image doesn't exist, the default one is set
    if (!isset($uploadFileName)) {
	    $uploadFileName = "default.jpg";
    }

	$dataArray = [ 
	   "name" => $name,
	   "description" => $description,
	   "brand" => $brand,
	   "price" => intval($price),
	   'quantity' => intval($quantity),
	   'size' => intval($_POST["size"]), // You missed size (Tony)
        "category" => explode(", ", protect_input($_POST["category"])), // You missed category (Tony)
        "gender" => protect_input($_POST["gender"]), // You missed gender (Tony)
	   'photos' => [$uploadFileName]
	   ];


		// Add new users to the database
		$returnVal = $collection->insertOne($dataArray); 

		$_SESSION["message"]["success"][] = "Product Added Successfully!";

}

generate_header("New Product");

include 'Files/add_product.php';
products_form();
generate_footer();


