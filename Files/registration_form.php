<?php

/**
 * Generate HTML for registration form
 */
function registration_form(){
    ?>

    <div class="row page-intro">
        <div class="col-lg-12  ">
            <h1>Registration</h1>
        </div>
    </div>


    <!-- registration form -->
    <div class="container-fluid">
        <form method="post" class="register-form">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="name">Name</label>
                    <input name="name" class="form-control" type="text" id="name">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="email">Email</label>
                    <input name="email" class="form-control" type="email" id="email">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="password">Password</label>
                    <input name="password" class="form-control" type="password" id="password">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 col-lg-4">
                    <label for="password_again">Password Again</label>
                    <input name="password_again" class="form-control" type="password" id="password_again">
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <button class="btn btn-default regbutton">Register</button>
                </div>

            </div>
        </form>
    </div>
<?php
}