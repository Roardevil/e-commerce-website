

function SortProducts(data) {
    this.products = {};
    this.products.data = data;
    this.products.positions = [];

    // animations
    this.animation = {};
    this.animation.timer = null;
    this.animation.duration = 300;
    this.animation.fps = 50;
    this.animation.oneTimeoutDuration = this.animation.duration / (this.animation.duration/1000 * this.animation.fps);

    // filter
    this.products.filter = {};
    this.products.filterCount = {};


}

/**
 * Prepare for sorting
 */
SortProducts.prototype.init = function () {
    // store html for each product and add sortNumber
    for(var i = 0; i < this.products.data.length; i++) {
        var id = this.products.data[i]._id["$oid"];
        this.products.data[i].html = this.getProductHTML(id);

        this.products.data[i].showed = true; // set all to showed
    }

    // check filter and show/hide products
    this.fillFilter();
    this.setVisibility();
    this.showHideProducts(false);


}

/**
 * Get HTML for product item
 * @param id
 * @returns {string}
 */
SortProducts.prototype.getProductHTML = function (id) {
    return document.getElementById("product-"+id).outerHTML;
}

/**
 * Sort products
 * @param criterium
 * @param order
 */
SortProducts.prototype.sortProducts = function (criterium, order) {
    this.sortData(criterium, order);
    this.getPositions();
    this.moveProducts();

}

/**
 * Sort data of products
 * @param criterium
 * @param order
 */
SortProducts.prototype.sortData = function(criterium, order) {

    // selection sort
    for (var i = 0; i < this.products.data.length-1; i++) {
        var min = i;
        for (var k = i+1; k < this.products.data.length; k++) {
            if (this.products.data[k][criterium]*1 < this.products.data[min][criterium]*1) {
                min = k;
            }
        }
        var tmp = this.products.data[i];
        this.products.data[i] = this.products.data[min];
        this.products.data[min] = tmp;
    }

    // if reversed order
    if (order == 1) {
        this.products.data = this.products.data.reverse();
    }
}


/**
 * Generate new HTML for products list and display it
 */
SortProducts.prototype.displayProducts = function () {
    var html = "";
    for(var i = 0; i < this.products.data.length; i++) {
        html += this.products.data[i].html;
    }
    html += "<div style='clear: both'></div>"; // clearer

    document.getElementById("products-cover").innerHTML = html;
    this.showHideProducts(false); // show/hide products without animation
    document.getElementById("products-cover").style.height = "auto";
}

/**
 * Set visibility of products
 */
SortProducts.prototype.setVisibility = function () {

    for(var i = 0; i < this.products.data.length; i++) {
        var show = true;
        var product = this.products.data[i];

        // check if the item should be hidden
        if (this.products.filter.gender.indexOf(product.gender) > -1 && this.products.filter.gender.length  < this.products.filterCount.gender) {
            show = false;
        }
        if (this.products.filter.brand.indexOf(product.brand) > -1 && this.products.filter.brand.length < this.products.filterCount.brand) {
            show = false;
        }
        if (this.products.filter.price.indexOf(Math.ceil(product.price/100.0)*100) >= 0  && this.products.filter.price.length  < this.products.filterCount.price) {
            show = false;
        }

        this.products.data[i].showed = show;
    }
}

/**
 * Check filtering form and set filter
 * @returns {SortProducts.filter|*|filter|ga.selectors.filter|{TAG, CLASS, ATTR, CHILD, PSEUDO}|{}}
 */
SortProducts.prototype.fillFilter = function () {
    var filterEl = document.getElementById("sort-filter");
    var inputs = filterEl.getElementsByTagName("input");
    this.products.filterCount = {};

    // iterate checkboxes from the form and set filter data
    for (var i = 0; i < inputs.length; i++) {
        var filterName = inputs[i].getAttribute("data-filter");
        var filterValue = inputs[i].getAttribute("data-filter-value");

        // if price, convert value to number
        if (filterName == "price") {
            filterValue = filterValue*1;
        }

        // set number of values of one category
        this.products.filterCount[filterName] = this.products.filterCount[filterName] || 0;
        this.products.filterCount[filterName]++;


        this.products.filter[filterName] = this.products.filter[filterName] || [];
        if (!inputs[i].checked) { // add item
            if (this.products.filter[filterName].indexOf(filterValue)  == -1) {
                this.products.filter[filterName].push(filterValue);
            }
        } else { // remove item
            if (this.products.filter[filterName].indexOf(filterValue) > -1) {
                this.products.filter[filterName].splice(this.products.filter[filterName].indexOf(filterValue), 1);
            }
        }
    }
}


/**
 * Add class for showing/hiding
 * @param animation
 */
SortProducts.prototype.showHideProducts = function(animation) {

    var animate = (animation)?"-animate":""; // animation

    for (var i = 0; i < this.products.data.length; i++) {
        var id = this.products.data[i]._id["$oid"]; // product id

        // if should be showed
        if (this.products.data[i].showed) {
            if (document.getElementById("product-"+id).className.search("showed") == -1) { // set showing class only if it was hidden
                document.getElementById("product-"+id).className = "col-md-3  product-item product-showed"+animate;

            }
        } else { // hide
            if (document.getElementById("product-"+id).className.search("hidden") == -1) {
                document.getElementById("product-" + id).className = "col-md-3  product-item product-hidden" + animate;
            }
        }
    }
}


/**
 * Filter products
 */
SortProducts.prototype.filter = function () {
    this.fillFilter();
    this.setVisibility();
    this.showHideProducts(true);
}

/**
 * Set new positions and moveStep of products elements
 */
SortProducts.prototype.getPositions = function () {
    this.products.positions = []; // clear positions

    // get showed product elements
    var elementsAll = document.getElementsByClassName("product-item");
    var elements = [];
    for (var i = 0; i < elementsAll.length; i++) {
        if (elementsAll[i].className.search("hidden") == -1) {
            elements.push(elementsAll[i]);
        }
    }


    var j = 0;
    var frames = (this.animation.duration/1000 * this.animation.fps); // frames of one animation

    for (var i = 0; i < this.products.data.length; i++) {
        if (this.products.data[i].showed) {

            // get current position
            var currentPosition = {};
            currentPosition.left = document.getElementById("product-"+this.products.data[i]._id["$oid"]).offsetLeft;
            currentPosition.top = document.getElementById("product-"+this.products.data[i]._id["$oid"]).offsetTop;

            // set new position - get position of HTML element which is in the same order as the product
            this.products.data[i].newPosition = {};
            this.products.data[i].newPosition.top = elements[j].offsetTop;
            this.products.data[i].newPosition.left = elements[j].offsetLeft;

            // set moving step
            this.products.data[i].moveStep = {};
            this.products.data[i].moveStep.top = (elements[j].offsetTop - currentPosition.top) / frames;
            this.products.data[i].moveStep.left = (elements[j].offsetLeft - currentPosition.left) / frames;

            j++;
        }
    }
}


/**
 * Animate products for sorting
 */
SortProducts.prototype.moveProducts = function () {
    document.getElementById("products-cover").style.height = window.getComputedStyle(document.getElementById("products-cover")).height; // set actual size of cover element

    // set current positions of every elements
    for (var i = 0; i < this.products.data.length; i++) {
        if (this.products.data[i].showed) {
            var id = this.products.data[i]._id["$oid"];

            this.products.data[i].currentPosition = {};
            this.products.data[i].currentPosition.left = document.getElementById("product-"+id).offsetLeft+"px";
            this.products.data[i].currentPosition.top = document.getElementById("product-"+id).offsetTop+"px";
        }
    }

    // set current position, but in absolute context
    for (var i = 0; i < this.products.data.length; i++) {
        if (this.products.data[i].showed) {
            var id = this.products.data[i]._id["$oid"];
            // set width and height
            document.getElementById("product-"+id).style.width = window.getComputedStyle(document.getElementById("product-"+id)).width;
            document.getElementById("product-"+id).style.height = window.getComputedStyle(document.getElementById("product-"+id)).height;

            // set position
            document.getElementById("product-"+id).style.top = this.products.data[i].currentPosition.top
            document.getElementById("product-"+id).style.left = this.products.data[i].currentPosition.left;
            document.getElementById("product-"+id).style.position = "absolute";
        }
    }

    clearTimeout(this.animation.timer);

    // animate moving
    var frames = (this.animation.duration/1000 * this.animation.fps);
    var frame = 1;
    var self = this;

    /**
     * Animation of product elements
     */
    function animateProduct() {
        for (var i = 0; i < self.products.data.length; i++) {
            if (self.products.data[i].showed) {
                var id = self.products.data[i]._id["$oid"];

                // get current position
                var left = document.getElementById("product-"+id).style.left.replace("px", "")*1;
                var top = document.getElementById("product-"+id).style.top.replace("px", "")*1;

                // calculate new position
                left += self.products.data[i].moveStep.left;
                top += self.products.data[i].moveStep.top;

                // set new position
                document.getElementById("product-"+id).style.top = top+"px";
                document.getElementById("product-"+id).style.left = left+"px";
            }
        }

        self.showHideProducts(false); // hide products without animation


        if (frame < frames) { // set timer for next animation frame
            self.animation.timer = setTimeout(function () {
                animateProduct();
            }, self.animation.oneTimeoutDuration);
        } else {
            // in the end replace DOM by elements in the right order
            self.displayProducts();
        }

        frame++; // counting frames


    }

    animateProduct(); // start animation

}