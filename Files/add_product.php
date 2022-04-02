<?php
function products_form(){
?>

    <div class="row page-intro">
        <div class="col-lg-12  ">
            <h2>New Product</h2>
        </div>
    </div>


    <div class="container-fluid">
        <form class="register-form" method="POST" enctype="multipart/form-data">

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="name">Name of Product</label>
                    <input name="name" class="form-control" type="text" id="name" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" rows="10" id="description" required></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="picture">Upload Picture of Product</label>
                    <input type="file" name="imageToUpload" id="image" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="brand">Brand Name</label>
                    <input name="brand" class="form-control" type="text" id="brand" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="category">Category Name</label>
                    <input name="category" class="form-control" type="text" id="category" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="gender">Gender</label>
                    <input name="gender" class="form-control" type="text" id="gender" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="price">Price</label>
                    <input name="price" class="form-control" type="number" id="price" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="quantity">Quantity</label>
                    <input name="quantity" class="form-control" type="number" id="quantity" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="size">Size</label>
                    <input name="size" class="form-control" type="number" id="size" required>
                </div>
            </div>


            <hr>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <button type="submit" name="submit" class="btn btn-default regbutton">Add Product</button>

                </div>
            </div>
        </form>
    </div>

<?php
}