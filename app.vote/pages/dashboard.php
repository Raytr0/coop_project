<?php
session_start();

// Prevents browser from caching user submissions
header("Cache-Control: no-cache, no-store, must-revalidate");

require_once "../assets/php/database.php";
include_once "../assets/php/libraries/PHPMailer/PHPMailerAutoload.php";
include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";

$user = new Account();

// Checks if a user is currently logged in 
if ($user -> currentAccount() < 0)
{
    redirect("/pages/login.php");
}

// Checks if the users account is verified 
else if (!$user -> getAccountInfo("verified"))
{
    redirect("/pages/confirm-email.php");
}

// Checks if the users account is verified 
else if ($user -> getAccountInfo("type_id") == 2)
{
    redirect("/pages/calendar.php");
}

$type_id = $user -> getAccountInfo("type_id");

//for displaying in the header
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
    <title>Dashboard</title>
    <link rel="apple-touch-icon" href="../../../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="../../../app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/calendars/extensions/daygrid.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/calendars/extensions/timegrid.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/pickers/pickadate/pickadate.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/plugins/calendars/fullcalendar.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/dashboard.css">
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
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Dashboard</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="calendar.php">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Dashboard
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="filters">
                    <?php if ($type_id == 4): ?>
                        <select class="filter" id="schools" onChange="
                        ((event) => {
                            let menu = event.target;
                            school_filter = menu.options[menu.selectedIndex].innerText;

                            let date_menu_filter = document.getElementById('dates');

                            filter(date_menu_filter.options[date_menu_filter.selectedIndex].innerText);
                        }) (event);
                        ">
                            <option value="">All</option> 
                        </select>
                    <?php endif; ?>
                    <select class="filter" id="dates"
                    onChange="
                        ((event) => {
                            let menu = event.target; 

                            filter(menu.options[menu.selectedIndex].innerText);
                        })(event);
                    ">
                        <option value="">Past Week</option>
                        <option value="">Past Month</option>
                        <option value="">Past Year</option>
                        <!-- <option value="">All</option> -->
                    </select>
                </div>
            </div>
            <div class="chart-container">
                <?php
                switch($type_id)
                {
                    case 1:
                        $num_charts = 2;
                        break;
                    
                    case 3:
                        $num_charts = 4;
                        break;
                    
                    case 4:
                        $num_charts = 4;
                        break;

                    default:
                        $num_charts = 2;
                        break;
                }
                ?>

                <?php for ($i = 0; $i < $num_charts; $i++): ?>
                <div class="chart-wrapper" id="chart<?=$i?>">
                    <canvas width="500" height="400"></canvas>   
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

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
    <script src="/assets/js/scripts.js"></script>
    <script src="/assets/js/node_modules/chart.js/dist/Chart.js"></script>
    <script src="/assets/js/dashboard.js"></script>
    <!-- END: Page JS-->

    </body>
    <!-- END: Body-->

</html>