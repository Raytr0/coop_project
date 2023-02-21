<?php
// Starts Session
session_start();

// Prevents browser from caching user submissions
header("Cache-Control: no-cache, no-store, must-revalidate");

// Defines constant to know if a file is included
define("INCLUDED", true);

// Includes database 
require_once "../assets/php/database.php";

// Includes dependencies
include_once "../assets/php/libraries/PHPMailer/PHPMailerAutoload.php";
include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";

// Creates new user
$user = new Account();

// Checks if a user is currently logged in 
if ($user -> currentAccount() < 0)
{
    redirect("login.php", 307);
}

// Checks if the users account is verified 
else if (!$user -> getAccountInfo("verified"))
{
    redirect("confirm-email.php", 307);
}

// Gets users info
$user_info = $user -> getAccountInfo();
$profile_picture = $user -> getProfilePicture();
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
    <title>Index</title>
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
    <link rel="stylesheet" type="text/css" href="../app-assets/css/core/menu/menu-types/vertical-menu.css">
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

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- BEGIN: Header-->
    <?php include "templates/header.php";?>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <?php include "templates/navbar.php";?>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-body">
                <div class="calendar-container">
                    <div class="calendar-header">
                        <span class="left">
                            <svg id="left" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>  
                        </span>
                        <h1 id="header-date"></h1>
                        <span class="right">
                            <svg id="right" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </span>
                    </div>

                    <div class="calendar-body" id="calendar">
                        <div class="calendar-heading">Mon</div>
                        <div class="calendar-heading">Tue</div>
                        <div class="calendar-heading">Wed</div>
                        <div class="calendar-heading">Thu</div>
                        <div class="calendar-heading">Fri</div>
                        <div class="calendar-heading">Sat</div>
                        <div class="calendar-heading">Sun</div>

                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                        <div class="day"><span></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix blue-grey lighten-2 mb-0"><span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2019<a class="text-bold-800 grey darken-2" href="https://1.envato.market/pixinvent_portfolio" target="_blank">Pixinvent,</a>All rights Reserved</span><span class="float-md-right d-none d-md-block">Hand-crafted & Made with<i class="feather icon-heart pink"></i></span>
            <button class="btn btn-primary btn-icon scroll-top" type="button"><i class="feather icon-arrow-up"></i></button>
        </p>
    </footer>
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
    <script src="../assets/js/calendar.js"></script>
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>