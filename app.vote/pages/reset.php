<?php
session_start();

// Prevents browser from caching the page
header("Cache-Control: no-cache, no-store, must-revalidate");

require_once "../assets/php/database.php";
include_once "../assets/php/libraries/PHPMailer/PHPMailerAutoload.php";
include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";
include_once "../assets/php/voter_class.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Checks if there was a password reset token
    if (isset($_GET["token"]))
    {
        $user = new Account();

        // Checks if it successfully changed the password
        if ($user -> updatePassword($_GET["token"], $_POST["password"], $_POST["password_confirm"]))
        {
            $success = "Successfully changed password.";
        }

        else
        {
            $error = $user -> getAccountInfo("errors");
            
            // Checks what type of error occured
            if (isset($error["token"]))
            {
                $error = $error["token"];
            }
            else
            {
                $error = $error["password"];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Reset Password</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/pages/authentication.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/form.css">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern 1-column  navbar-floating footer-static bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    <!-- BEGIN: Content-->
    <!-- <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="row flexbox-container">
                    <div class="col-xl-7 col-10 d-flex justify-content-center">
                        <div class="card bg-authentication rounded-0 mb-0 w-100">
                            <div class="row m-0">
                                <div class="col-lg-6 d-lg-block d-none text-center align-self-center p-0">
                                    <img src="/app-assets/images/pages/reset-password.png" alt="branding logo">
                                </div>
                                <div class="col-lg-6 col-12 p-0">
                                    <div class="card rounded-0 mb-0 px-2">
                                        <div class="card-header pb-1">
                                            <div class="card-title">
                                                <h4 class="mb-0">Reset Password</h4>
                                            </div>
                                        </div>
                                        <p class="px-2">Please enter your new password.</p>
                                        <div class="card-content">
                                            <div class="card-body pt-1">
                                                <form>
                                                    <fieldset class="form-label-group">
                                                        <input type="text" class="form-control" id="user-email" placeholder="Email" required>
                                                        <label for="user-email">Email</label>
                                                    </fieldset>

                                                    <fieldset class="form-label-group">
                                                        <input type="password" class="form-control" id="user-password" placeholder="Password" required>
                                                        <label for="user-password">Password</label>
                                                    </fieldset>

                                                    <fieldset class="form-label-group">
                                                        <input type="password" class="form-control" id="user-confirm-password" placeholder="Confirm Password" required>
                                                        <label for="user-confirm-password">Confirm Password</label>
                                                    </fieldset>
                                                    <div class="row pt-2">
                                                        <div class="col-12 col-md-6 mb-1">
                                                            <a href="auth-login.html" class="btn btn-outline-primary btn-block px-0">Go Back to Login</a>
                                                        </div>
                                                        <div class="col-12 col-md-6 mb-1">
                                                            <button type="submit" class="btn btn-primary btn-block px-0">Reset</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div> -->
    <div class="form-wrapper">
        <div class="form-container small">
            
            <h1 class="header">Reset Your Password</h1>

            <?php if (!isset($success)): ?>
                <form action='/pages/reset.php?token=<?=$_GET["token"]?>' method='POST' class='form'>
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" class="form-control" placeholder="Password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="password-confirm">Confirm Password</label>
                        <input type="password" id="password-confirm" class="form-control" placeholder="Confirm Password" name="password_confirm" required>   
                        <?php if (!isset($error)): ?>
                            <div class="text">
                                Password must contain 8 characters, 1 digit, and 1 capital character
                            </div>
                        <?php else: ?>
                            <span class="text error" id="password_error">
                                <?=$error?>
                            </span>
                        <?php endif;?>
                    </div>
                    <button type="submit" class="btn btn-primary float-left btn-inline mb-50 submit">Reset</button>
                    <div class='last-element'></div>
                </form>
            <?php else: ?>
                <div class='text center' id='password_success'><?=$success?></div>
                <div class='last-element'></div>
            <?php endif; ?>

            <div class="footer">
                <a class="link" href="/pages/login.php">Already have an account? Sign in</a>
                <a class="link" href="/pages/student.php">Not registered? Get started today.</a>
            </div>
        </div>
    </div>

    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <script src="/app-assets/js/scripts/components.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>