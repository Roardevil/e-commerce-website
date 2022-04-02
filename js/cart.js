/**
 * Created by Tonda on 17.03.2017.
 */

/**
 * Ajax function for adding product into cart
 * @param event
 * @param form_element
 * @returns {boolean}
 */
function add_into_cart(event, form_element) {
    event.preventDefault();

    // get data
    var product_id = form_element["product-id"].value;
    var product_quantity = form_element["product-quantity"].value;


    var request = new XMLHttpRequest();
    request.open("POST", "add_to_cart.php?ajax=true", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("product-id=" + product_id + "&product-quantity=" + product_quantity);

    request.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var response;
            try {
                response = JSON.parse(this.responseText);
            } catch (e) {
                alert("error");
            }

            if (response.result) {
                update_items_in_cart(response.inCart);
                show_label(form_element);

                // set quantity in input to one
                form_element["product-quantity"].value = 1;
            } else {
                alert(response.error);
            }
        }
    };

    return false;
}

/**
 * Show label that item was added to the cart
 * @param form_element
 */
function show_label(form_element) {
    var label = form_element.getElementsByClassName("label")[0];
    label.className += " show-label";
    setTimeout(function () { // hide label after 3 seconds
        label.className = "label label-success";
    }, 3000);
}

/**
 * Update number of items in cart in the navigation
 * @param num
 */
function update_items_in_cart(num) {
    document.getElementById("nav-cart-items").innerHTML = "("+num+")";

}

/**
 * Manage Cart actions
 */
function manage_cart() {

    // set events
    var inputs = document.getElementsByTagName("input");
    var timeouts = {};
    for (var i = 0; i < inputs.length; i++) {
        (function(i) {
            if (inputs[i].hasAttribute("data-product")) {
                inputs[i].onchange = function () {manage_change_quantity(inputs[i]);};
                inputs[i].onkeyup = function () {manage_change_quantity(inputs[i]);};
            }
        })(i);

    }


    /**
     * Manage changing quantity of products
     * @param inp
     */
    function manage_change_quantity(inp) {
        var product_id = inp.getAttribute("data-product-id");
        if (typeof timeouts[product_id] != "undefined") {
            clearTimeout(timeouts[product_id]);
        }
        timeouts[product_id] = setTimeout(function () {change_quantity(inp)}, 300); // wait 300 milisecond
    }

    /**
     * Ajax function for changing quantity of ordered products
     * @param inp
     */
    function change_quantity(inp) {
        // get data
        var product_id = inp.getAttribute("data-product-id");
        var new_quantity = inp.value*1;
        var old_quantity = inp.getAttribute("data-product-value")*1;
        var quantity = new_quantity - old_quantity;


        // deactive input
        document.getElementById("loader-"+(product_id)).style.visibility = "visible";
        inp.disabled = true;

        // if the product quantity was changed to 0, set opacity
        if (new_quantity == 0) {
            document.getElementById("row-"+product_id).style.opacity = 0.3;
        } else {
            document.getElementById("row-"+product_id).style.opacity = 1;
        }



        var request = new XMLHttpRequest();
        request.open("POST", "add_to_cart.php?ajax=true", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send("product-id=" + product_id + "&product-quantity=" + quantity);

        request.onreadystatechange = function () {
       
            if (this.readyState == 4 && this.status == 200) {
                var response;
                try {
                    response = JSON.parse(this.responseText);
                } catch (e) {
                    alert("error");
                }

                if (response.sucess) { // if success
                    update_items_in_cart(response.inCart);
                    update_prices(inp)
                } else {
                    alert(response.error);
                    inp.value = old_quantity;
                }

                // reactive input
                document.getElementById("loader-"+(product_id)).style.visibility = "hidden";
                inp.disabled = false;

            }
        };
    }


    /**
     * Recount prices in the cart table
     * @param inp
     * @param new_quantity
     */
    function update_prices(inp, new_quantity) {
        var product_id = inp.getAttribute("data-product-id");
        var new_quantity = inp.value*1;
        var old_quantity = inp.getAttribute("data-product-value")*1;
        var quantity = new_quantity - old_quantity;

        var old_price = document.getElementById("price-"+product_id).getAttribute("data-price")*1;
        var new_price = new_quantity * (inp.getAttribute("data-product-price")*1);

        var price_diff = new_price - old_price;

        // update total item price
        document.getElementById("price-"+product_id).setAttribute("data-price", ""+new_price);
        document.getElementById("price-"+product_id).innerHTML = "£"+new_price;

        // update total price
        var total_price = document.getElementById("total-price").getAttribute("data-price")*1 + price_diff;
        document.getElementById("total-price").setAttribute("data-price", ""+total_price);
        document.getElementById("total-price").innerHTML = "£"+total_price;

        // update total number of items
        var total_quantity = document.getElementById("total-quantity").getAttribute("data-quantity")*1 + (new_quantity - old_quantity);
        document.getElementById("total-quantity").setAttribute("data-quantity", ""+total_quantity);
        document.getElementById("total-quantity").innerHTML = total_quantity;

        inp.setAttribute("data-product-value", new_quantity);
    }
}

manage_cart();