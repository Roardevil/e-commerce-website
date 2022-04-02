<?php

function generate_order_detail($role, $data) {
    $data = protect_output($data); // XSS protection
    
    // status style
    $status_style = ($data["status"] == "processing"?" style='font-weight: bold; color: black'":"").
                            ($data["status"] == "canceled"?" style='color: red'":"").
                            ($data["status"] == "dispatched"?" style='color: green'":"");
    
    echo "
<!-- customer details -->
        <section>
            <h4>Overview</h4>
            <p><b>Date of order:</b> ".date("d/m/Y", $data["date"])."</p>
            <p><b>Status: </b><span $status_style>".ucfirst($data["status"])."</span>";
    if ($data["status"] == "processing" && $role == "customer") {
        echo "
            <form method='post'>
                <input type='hidden' value='canceled' name='status'>
                <input type='submit' name='change_status' value='Cancel Order' class='btn btn-danger'>
            </form>            
            <p><b>Arriving: </b>".date("d/m/Y", $data["arriving"])."</p>
            ";
    }

    if ($role == "staff") {
        // buttons for changing status

        if ($data["status"] != "canceled") {
            echo "
            <form method='post'  class='form-inline'>
                <input type='hidden' value='canceled' name='status'>
                <input type='submit' name='change_status' value='Cancel Order' class='btn btn-danger'>
            </form>
            ";
        }

        if ($data["status"] != "processing") {
            echo "
            <form method='post' class='form-inline'>
                <input type='hidden' value='processing' name='status'>
                <input type='submit' name='change_status' value='Change to Processing' class='btn btn-primary'>
            </form>
            ";
        }

        if ($data["status"] != "dispatched") {
            echo "
            <form method='post' class='form-inline'>
                <input type='hidden' value='dispatched' name='status'>
                <input type='submit' name='change_status' value='Was Dispatched' class='btn btn-info'>
            </form>
            ";
        }

        // form for changing arriving date
        if ($data["status"] == "processing") {
            echo "<p><br><b>Arriving: </b>
            <form method='post'  class='form-inline'>
                <input type='text' value='".date("d/m/Y", $data["arriving"])."' name='date' class='form-control'>
                <input type='submit' name='change_date' value='Change Date' class='btn'>
            </form></p>
            ";
        }


    }

    // Address
    echo "
            <hr>

            <h4>".($role == "customer"?"Your Details":"Customer Details")."</h4>
            <p>{$data["customer_details"]["name"]}</p>
            <p>{$data["customer_details"]["address"]["line_1"]}</p>
            <p>{$data["customer_details"]["address"]["line_2"]}</p>
            <p>{$data["customer_details"]["address"]["town"]}</p>
            <p>{$data["customer_details"]["address"]["postcode"]}</p>
            <p><b>Phone: </b>{$data["customer_details"]["tel"]}</p>
            <p><b>Email: </b>{$data["customer_details"]["email"]}</p>
        </section>
        <!-- items -->
        <section>
            <hr>
            <h4>Products</h4>
            <div class='container'>

                <div class='table-responsive cart_info'>
                    <table class='table table-condensed'>
                        <thead>
                        <tr class='cart_menu'>
                            <td class='image'>Item</td>
                            <td class='description'>Quantity</td>
                            <td class='price'>Price</td>

                        </tr>
                        </thead>
                        <tbody>";

    // Products
    foreach ($data["items"] as $item) {
        echo "
                        <tr>
                            <td class='cart_product'>
                                <a href='product.php?id={$item["product_id"]}'><img src='images\product-images/{$item["photo"]}' width='140' height='80' alt=''></a>
                            </td>
                            <td class='cart_description'>
                                <h4><a href='product.php?id={$item["product_id"]}'>{$item["product_name"]}</a></h4>
                                <p><strong>Quantity: </strong> {$item["quantity"]}</p>
                            </td>
                            <td class='cart_price'>
                                <p>£{$item["price"]}</p>
                                <p><b>£{$item["total_price"]}</b></p>
                            </td>

                        </tr>";
    }


    // Summary
    echo "
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>Total:</td>
                            <td>{$data["total_quantity"]}</td>
                            <td>£{$data["total_price"]}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section> <!--/#cart_items-->";

}

/** Generate HTML for list of orders
 * @param $data
 */
function generate_orders_list($data) {
    $data = protect_output($data);

    // no orders
    if (!$data) {
        echo "<h3>No Orders</h3>";
        return;
    }

    // table header
    echo <<<END
        <section id="cart_items">
            <div class="container">

                <div class="table-responsive cart_info">
                    <table class="table table-condensed">
                        <thead>
                        <tr class="cart_menu">
                            <td class="date">Date</td>
                            <td class="user">Name</td>
                            <td class="description">Price</td>
                            <td class="user">Detail</td>
                            <td class="user">Status</td>

                        </tr>
                        </thead>
                        <tbody>
END;

    // Items
    foreach ($data as $item) {
        echo "
                        <tr".($item["status"] == "processing"?" style='font-weight: bold; color: black'":"").
                            ($item["status"] == "canceled"?" style='color: red'":"").
                            ($item["status"] == "dispatched"?" style='color: green'":"").">
                            <td>".date("d/m/Y", $item["date"])."</td>
                            <td>$item[name]</td>
                            <td>£$item[price]</td>
                            <td><a href='?id=$item[order_id]' class='btn btn-info'>Detail</a></td>
                            <td>$item[status]</td>
                        </tr>
        ";
    }

    // table footer
    echo "
                        </tbody>
                    </table>
                </div>
            </div>
        </section> <!--/#cart_items-->";

}