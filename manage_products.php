<?php
//require
require "common.php";


page_for_staff();

generate_header("Manage Products"); ?>

<!-- main content -->
<div class="container">

    <div class="row page-intro">
        <div class="col-lg-12  ">
            <h2>Products management</h2>
        </div>
    </div>



    <div class="row">

        <section id="cart_items">
            <div class="container">

                <div class="table-responsive cart_info">
                    <table class="table table-condensed">
                        <thead>
                        <tr class="cart_menu">
                            <td class="product">Name of Product:</td>
                            <td class="quantity">Current Quantity</td>
                            <td class="add">Update</td>
                            <td class="add">Delete</td>

                        </tr>
                        </thead>
                        <tbody>
						
	<?php

	//connect to database
	$products = (new MongoDB\Client)->Ecomerce->products->find();

	foreach ($products as $cust) {
?>
	 
                        <tr>
                            <td class="cart_product">
                                <strong><?php echo $cust['name']; ?></strong>
                            </td>
                            <td class="cart_description">
                                <?php echo $cust['quantity']; ?>
                            </td>
                            <td>

                                <a href="update_product.php?update=<?php echo $cust['_id'] ?>" class="btn btn-info">Update</a>
                            </td>
                            <td>
                            <form action="delete_product.php?delete_pro_id=<?php echo $cust['_id']; ?>" method="POST">
                                <input type="hidden" value="<?php echo $cust['_id']; ?>">
                                <input type="submit" class="btn btn-danger" value="Delete">
                            </form>							
                            </td>
                        </tr>
					<?php } ?>
	
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                <a href="add_product.php" class="btn btn-default">Add new product</a>

            </div>
        </section> <!--/#cart_items-->


    </div>


    <!-- footer -->
    <footer>
        <div class="footer-blurb"></div>

        <div class="small-print">
           
        </div>
    </footer>
</div> <!-- end - main content -->

<!-- JavaScript links -->
<script src="../js/jquery-1.11.3.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/ie10-viewport-bug-workaround.js"></script>

</body>
</html>