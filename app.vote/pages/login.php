<?php
session_start();

// Prevents browser from caching the page
header("Cache-Control: no-cache, no-store, must-revalidate");

require_once "../assets/php/database.php";
include_once "../assets/php/libraries/PHPMailer/PHPMailerAutoload.php";
include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $user = new Account();

    $user -> logout();

    // Checks if remeber me was checked
    $remember_me = $_POST["remember_me"] == "on";

    if ($user -> login($_POST["username_email"], $_POST["password"], $remember_me))
    {
        // Redirects to index page
        redirect("/pages/index.php");
    }else {
        $_SESSION['error'] = "Login error:";
        $_SESSION['error_message'] = "Invlid username and/or password, please try again.";
    }
}
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Login Page - Vuexy - Bootstrap HTML admin template</title>
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
    <div class="form-wrapper">
        <div class="form-container small">
            
            <?php if(isset($_SESSION['error'])): ?>
            
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
    			<h4 class='alert-heading'><?php echo $_SESSION['error'] ?></h4>
    			<p class='mb-0'><?php echo$_SESSION['error_message'] ?></p>
    			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    				<span aria-hidden='true'><i class='feather icon-x-circle'></i></span>
    			</button>
    		</div>
            
            <?php endif; ?>
                
            <h1 class="header">Login</h1>
            <form action="/pages/login.php" method="POST" class="form">
                <div class="form-group">
                    <label for="username_email">Username Or Email</label>
                    <input type="text" id="username_email" class="form-control" placeholder="Username/Email" name="username_email" required>
                    <span class="text error" id="username-email_error"></span>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" class="form-control" placeholder="Password" name="password" required>
                    <span class="text error" id="password_error"></span>
                </div>

                <?php if (isset($error)): ?>
                    <span class='text error' id='login_error'><?=$error?></span>
                    <div class='last-element'></div>    
                <?php endif; ?>

                <div class="form-group row">
                    <div class="col-12">
                        <fieldset class="checkbox">
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                <input type="checkbox" name="remember_me">
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">Remember me.</span>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-left btn-inline mb-50 submit">Login</button>
                <div class="last-element"></div>
            </form>
            <div class="footer">
                <a class="link" href="/pages/forgot.php">Forgot your password?</a>
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