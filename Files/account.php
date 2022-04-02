<?php


/**
 * Generate HTML for default user's account page
 */
function generate_account_default() {
    echo <<<END
 <section id="cart_items">
            <div class="container">

                <div class="table-responsive cart_info">
                    <table class="table table-condensed">
                        <thead>
                        <tr class="cart_menu">
                            <td class="image">Account Settings</td>
                            <td class="description"></td>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="cart_product">
                            </td>
                            <td class="cart_description">
                                <p>Manage Your Address</p>
                                <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                                    <a href="update_address.php" class="btn btn-default regbutton">Change Your Address</a>

                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="cart_product">
                                <p>Orders</p>
                            </td>
                            <td class="cart_description">
                                <p>Order History</p>
                                <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                                    <a href="order_history.php" class="btn btn-default regbutton">Order History</a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section> <!--/#cart_items-->

END;
}