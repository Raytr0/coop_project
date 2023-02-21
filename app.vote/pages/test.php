<?php
session_start();

// Prevents browser from caching user sumbisions
header("Cache-Control: no-cache, no-store, must-revalidate");

require_once "../assets/php/database.php";
include_once "../assets/php/libraries/PHPMailer/PHPMailerAutoload.php";
include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if ($_POST["password"] == $_POST["confirm_password"]) {
        echo $unhash = $_POST["password"] . "<br>";
        $options = [
            'cost' => 12,
        ];
        $pass = password_hash($_POST["password"], PASSWORD_DEFAULT) . "<br>";
        echo $pass;
        echo $_POST['password'];
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
    <title>Calendar</title>
    <link rel="apple-touch-icon" href="../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="../app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/calendars/extensions/daygrid.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/calendars/extensions/timegrid.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/pickers/pickadate/pickadate.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="../app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/plugins/calendars/fullcalendar.css">
    <!-- END: Page CSS-->
    
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/calendar.css">
    <!-- END: Custom CSS-->
    
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
    
<body class="vertical-layout vertical-menu-modern 1-column  navbar-floating footer-static bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    
    
    <!-- BEGIN: Content-->
    <div class="form-wrapper">
        <div class="form-container">
            <h1 class="header">Student Registration</h1>
            <form action="" method="POST" class="form">
                

                <div class="form-group">
                    <label>Password</label>
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <input type="password" id="password" class="form-control" placeholder="Password" name="password" required>
                        </div>
                        <div class="col-lg-6 col-12">
                            <input type="password" id="confirm-password" class="form-control" placeholder="Confirm Password" name="confirm_password" required>
                        </div>
                    </div>

                    <?php if (!isset($error["password"])): ?>
                    <div class="text">
                        Password must contain 8 characters, 1 digit, and 1 capital character
                    </div>

                    <?php else: ?>
                    <span class="error" id="password_error">
                        <?=$error["password"]?>
                    </span>

                    <?php endif; ?>

                </div>

                <button type="submit" class="btn btn-primary float-left btn-inline mb-50 submit">Register</button>
                <div class="last-element"></div>
            </form>
            <div class="footer">
                <a class="link" href="/pages/login.php">Already have an account? Sign in</a>
                <a class="link" href="/pages/register.php">Not a student?</a>
            </div>
        </div>
    </div>
    <!-- BEGIN: Footer-->

    <!-- END: Footer-->


    <!-- BEGIN: Vendor JS-->
    <script src="../app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="../app-assets/vendors/js/extensions/moment.min.js"></script>
    <script src="../app-assets/vendors/js/calendar/fullcalendar.min.js"></script>
    <script src="../app-assets/vendors/js/calendar/extensions/daygrid.min.js"></script>
    <script src="../app-assets/vendors/js/calendar/extensions/timegrid.min.js"></script>
    <script src="../app-assets/vendors/js/calendar/extensions/interactions.min.js"></script>
    <script src="../app-assets/vendors/js/pickers/pickadate/picker.js"></script>
    <script src="../app-assets/vendors/js/pickers/pickadate/picker.date.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="../app-assets/js/core/app-menu.js"></script>
    <script src="../app-assets/js/core/app.js"></script>
    <script src="../app-assets/js/scripts/components.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="../assets/js/scripts.js"></script>
    <!--<script src="../assets/js/calendar.js"></script>-->
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>