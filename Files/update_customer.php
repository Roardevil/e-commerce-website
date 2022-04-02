<?php
    function update_form1(){
?>


    <div class="row page-intro">
        <div class="col-lg-12  ">
            <h2>Update Address</h2>
        </div>
    </div>

       <?php
        //connect to database

       // This query is listing all products instead of single product so get product by ID 
        $customers = (new MongoDB\Client)->Ecomerce->cart->findOne();

        // .limit(6) not working
        $cust = $customers;
?>

    <div class="container-fluid">
        <form class="register-form" method="POST" enctype="multipart/form-data">

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="firstName">Full Name</label>
                    <input name="firstName" class="form-control" type="text" id="firstName" value="<?php echo $cust['name']; ?>">
                </div>
            </div>


            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="address">Address Line 1</center></label>
                    <input name="address" class="form-control" type="text" id="address" value="<?php echo $cust['Address']; ?>">
                </div>
            </div>


            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="address2">Address
                        Line 2</label>
                    <input name="address2" class="form-control" type="text" id="address2" value="<?php echo $cust['AddressL']; ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="town">Town/City</label>
                    <input name="town" class="form-control" type="text" id="town" value="<?php echo $cust['Town']; ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="postcode">Postcode</label>
                    <input name="postcode" class="form-control" type="text" id="postcode" value="<?php echo $cust['Postcode']; ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="phone">Phone Number</label>
                    <input name="phone" class="form-control" type="tel" id="phone" value="<?php echo $cust['number']; ?>">
                </div>
            </div>

            <hr>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <input type="hidden" name="id" value="<?php echo $cust['_id']; ?>">
                    <button type="submit" name="update" class="btn btn-default regbutton">Update Product</button>

                </div>
            </div>
        </form>
    </div>

<?php
}