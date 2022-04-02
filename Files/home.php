<?php

/**
 * Generate HTML for home page slider
 * @param $data
 */
function generate_slider($data){
    $data = mongo_to_array($data);
    $data = protect_output($data); // XSS protection
?>

<div class="jumbotron feature">
    <div class="container">

        <div id="feature-carousel" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">

                <?php
                $slider_i = 0;
                foreach ($data as $d) {
                    echo "<li data-target=\"#feature-carousel\" data-slide-to=\"$slider_i\" ".($slider_i == 0?"class=\"active\"":"")."></li>\n";

                    $slider_i++;
                }

                ?>
            </ol>
            <div class="carousel-inner" role="listbox">

                <?php
                // Slides
                $slider_i = 0;
                foreach ($data as $d) {
                    echo "<div class=\"item ".($slider_i == 0?" active":"")." \">
                    <a href=\"product.php?id={$d["_id"]}\">
                        <img class=\"img-responsive\" src=\"images/product-images/{$d["photos"][0]}\" alt=\"{$d["name"]}\">
                    </a>
                    <div class=\"carousel-caption\">
                        <h3>{$d["brand"]}</h3>
                    </div>
                </div>\n";

                    $slider_i++;
                }

                ?>
            </div>

            <a class="left carousel-control" href="#feature-carousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#feature-carousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>

    </div>
</div>
<?php
}

/**
 * Generate HTML for featured products
 */
function generate_feature(){
?>
    <div class="container">
        <div class="row page-intro">
            <div class="col-lg-12">
                <h1>Featured Products</h1>
            </div>
        </div>

    <div class="row">
        <?php

        //connect to database
        $products = (new MongoDB\Client)->ecomerce->products->find();
       

        foreach ($products as $cust) { ?>
            <div class="col-md-4">
                <article class="article-intro">
                    <a href="product_details.html">
                        <img class="img-responsive img-rounded" src="'../images/product-images/<?php echo $cust['name'] ?>" alt="">
                    </a>
                    <h3>
                        <a href="product_details.html"><?php echo $cust['brand']; ?></a>
                    </h3>
                    <p><?php echo $cust['name']; ?></p>
                </article>
            </div>
        <?php } ?>
    </div>
<?php
}