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

$user = new Voter();

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

// Gets users info
$user_info = $user -> getAccountInfo();
$profile_picture = $user -> getProfilePicture();

// Gets voters info
$voter_info = $user -> getVoterInfo();

// Gets school info
$get_school = $db -> prepare("
    SELECT * FROM schools WHERE school_id=:school_id LIMIT 1;
");

$get_school -> execute([
    ":school_id" => $user_info["school_id"],
]);

$school_info = $get_school -> fetchAll()[0];

// Calculates age
$birth_date = new DateTime($voter_info["birth_date"]);
$current_date = DateTime::createFromFormat("Y-m-d", date("Y-m-d"));
$age = $current_date -> diff($birth_date) -> y;
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <!-- - var description  = ""-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Profile - Vuexy - Bootstrap HTML admin template</title>
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
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/pages/users.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/profile.css">
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
                            <h2 class="content-header-title float-left mb-0">Profile</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Pages</a>
                                    </li>
                                    <li class="breadcrumb-item active">Profile
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Edit Profile</a></div>
                        </div>
                    </div>
                </div> -->
            </div>
            <div class="content-body">
                <div class="user-profile">
                    <div class="profile-images-container">
                        <img class="profile-image" src="<?=htmlentities($profile_picture)?>" alt="User Profile Image">
                        <!-- <div class="sm-buttons">
                            <button id="social" type="button" class="btn btn-sm btn-icon btn-primary"><i class="feather icon-facebook"></i></button>
                            <button id="social" type="button" class="btn btn-sm btn-icon btn-primary"><i class="feather icon-twitter"></i></button>
                            <button id="social" type="button" class="btn btn-sm btn-icon btn-primary"><i class="feather icon-instagram"></i></button>
                        </div> -->
                    </div>
                    <div class="profile-bio-container">
                        <h1><?=htmlentities($user_info["first_name"] . " " . $user_info["last_name"])?></h1>
                        <div class="profile-info-wrapper">
                            <div class="profile-info-container">
                                <div><h6>Age:</h6> <?=htmlentities($age)?></div>
                                <div><h6>Gender:</h6> <?=htmlentities($voter_info["gender"])?></div>
                                <div><h6>School:</h6> <?=htmlentities($school_info["school_name"])?></div> 
                            </div>
                            <div class="profile-info-container">
                                <div><h6>Birthday:</h6> <?=htmlentities($birth_date -> format("M d, Y"))?></div>
                                <div><h6>Email:</h6> <?=htmlentities($user_info["email"])?></div>
                                <div><h6>Grade:</h6> <?=htmlentities($voter_info["grade"])?></div>
                            </div>                                                       
                        </div>
                    </div>
                </div>
                <section id="profile-info">
                    <div class="row">
                        <?php if ($voter_info["about_me"] != ""):?>
                        <div class="col-lg-6 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>About Me</h4>
                                </div>
                                <div class="card-body">
                                    <p>
                                        <?=htmlentities($voter_info["about_me"])?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endif;?>

                        <?php if ($voter_info["views_beliefs"] != ""):?>
                        <div class="col-lg-6 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Values and Beliefs</h4>
                                </div>
                                <div class="card-body">
                                    <p>
                                        <?=htmlentities($voter_info["views_beliefs"])?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                </section>
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
    <script src="/app-assets/js/scripts/pages/user-profile.js"></script>
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>