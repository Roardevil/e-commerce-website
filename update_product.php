<?php
//require
require "common.php";

// you must ensure that this page will be available only for staff (Tony)
page_for_staff();

if (isset($_POST["update"])) {

    // connect to mongodb server
    $collection = (new MongoDB\Client)->Ecomerce->products;

    //Convert to PHP array
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $brand= filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_STRING);
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);

    //Check file data has been sent
    if (!empty($_FILES["imageToUpload"]["name"])) {
        if(!array_key_exists("imageToUpload", $_FILES)){
            echo 'Upload Image.';
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


///
    $findProduct = [
        "_id" => new MongoDB\BSON\ObjectID($id)
    ];

    $updateProduct = [
     '$set' => [ 
       "name" => $name,
       "description" => $description,
       "brand" => $brand,
       "price" => intval($price), // use must store it as int (Tony)
       "category" => explode(", ", protect_input($_POST["category"])), // you missed category (Tony)
       "gender" => protect_input($_POST["gender"]), // you missed gender (Tony)
       "size" => intval($_POST["size"]), // you missed size (Tony)
       'quantity' => intval($quantity) // you must store it as int (Tony)
       ]
    ];
    if (isset($uploadFileName)) {
        $updateProduct['$set']["photos"] = [$uploadFileName];
    }

    $check_update = $collection->updateOne($findProduct, $updateProduct);
    $_SESSION["message"]["success"][] = "Product Updated Successfully!";

}

$findProduct = [
        "_id" => new MongoDB\BSON\ObjectID($_GET["update"])
    ];

generate_header("Update Product");

include 'src/templates/update_product.php';
update_form($findProduct);
generate_footer();


