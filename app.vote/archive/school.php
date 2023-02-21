<?

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
    <title>School Registration</title>
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
            <div class="content-header row"></div>
            <div class="content-body">
                <section class="row flexbox-container">
                    <div class="col-xl-8 col-10 d-flex justify-content-center">
                        <div class="card bg-authentication rounded-0 mb-0">
                            <div class="row m-0">
                                <div class="col-lg-6 d-lg-block d-none text-center align-self-center pl-0 pr-3 py-0">
                                    <img src="/app-assets/images/pages/register.jpg" alt="branding logo">
                                </div>
                                <div class="col-lg-6 col-12 p-0">
                                    <div class="card rounded-0 mb-0 p-2">
                                        <div class="card-header pt-50 pb-1">
                                            <div class="card-title">
                                                <h4 class="mb-0">Student Registration</h4>
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body pt-0">
                                                <form action="index.html">
                                                    <div class="form-label-group">
                                                        <input type="text" id="inputName" class="form-control" placeholder="Name" required>
                                                        <label for="inputName">Name</label>
                                                    </div>
                                                    <div class="form-label-group">
                                                        <input type="email" id="inputEmail" class="form-control" placeholder="Email" required>
                                                        <label for="inputEmail">Email</label>
                                                    </div>
                                                    <div class="form-label-group">
                                                        <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
                                                        <label for="inputPassword">Password</label>
                                                    </div>
                                                    <div class="form-label-group">
                                                        <input type="password" id="inputConfPassword" class="form-control" placeholder="Confirm Password" required>
                                                        <label for="inputConfPassword">Confirm Password</label>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-12">
                                                            <fieldset class="checkbox">
                                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                                    <input type="checkbox" checked>
                                                                    <span class="vs-checkbox">
                                                                        <span class="vs-checkbox--check">
                                                                            <i class="vs-icon feather icon-check"></i>
                                                                        </span>
                                                                    </span>
                                                                    <span class=""> I accept the terms & conditions.</span>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                    <a href="auth-login.html" class="btn btn-outline-primary float-left btn-inline mb-50">Login</a>
                                                    <button type="submit" class="btn btn-primary float-right btn-inline mb-50">Register</a>
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
        <div class="form-container">
            <h1 class="header">School Registration</h1>
            <form action="/register/school.php" method="POST" class="form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" class="form-control" placeholder="Username" name="username" required>
                    <span class="error" id="username_error">
                    <?=$error["username"] ?? ""?>
                    </span>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" class="form-control" placeholder="Email" name="email" required>
                    <span class="error" id="email_error">
                    <?=$error["email"] ?? ""?>
                    </span>
                </div>

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

                <div class="form-group">
                    <label>Name</label>
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <input type="text" id="first-name" class="form-control" placeholder="First" name="first_name" required>
                        </div> 
                        <div class="col-lg-6 col-12">
                            <input type="text" id="last-name" class="form-control" placeholder="Last" name="last_name" required>
                        </div>
                    </div>

                    <span class="error" id="name_error"></span>           
                </div>

                <div class="form-group">
                    <label for="school-name">School Name</label>
                    <input type="text" id="school-name" class="form-control" placeholder="School Name" name="school_name" required>
                    <span class="error" id="school-name_error"></span>           
                </div>

                <div class="form-group">
                    <label>Country</label>
                    <select name="country" id="country" class="form-control" required>
                        <option disabled selected value="default">Select a Country</option>
                        <option value="canada">Canada</option>
                        <option value="united-states">United States</option>
                    </select>
                    <span class="error" id="country_error"></span> 
                </div>

                <div class="form-group">
                    <label>State</label>
                    <select name="state" id="state" class="form-control" required>
                        <option disabled selected value="default">Select a State</option>
                    </select>
                    <span class="error" id="state_error"></span> 
                </div>

                <div class="form-group">
                    <label>School Board</label>
                    <select name="school_board" id="school-board" class="form-control" required>
                        <option disabled selected value="">Select a school board</option>
                    </select>
                    <span class="error" id="school_board_error"></span> 
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" class="form-control" placeholder="City" name="city" required>
                    <span class="error" id="city_error"></span>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" class="form-control" placeholder="Address" name="address" required> 
                    <span class="error" id="address_error"></span>
                </div>

                <div class="form-group">
                    <label for="zip">Zip Code</label>
                    <input type="text" id="zip" class="form-control" placeholder="Zip" name="zip" required> 
                    <span class="error" id="zip_error"></span>
                </div>

                <div class="form-group">
                    <label for="school-website">School Website</label>
                    <input type="text" id="school-website" class="form-control" placeholder="School Website" name="school_website" required>
                    <span class="error" id="school-website_error"></span>           
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" required> 
                    <span class="error" id="phone_error"></span>
                </div>

                <div class="form-group">
                    <label for="fax">Fax Number</label>
                    <input type="text" id="fax" class="form-control" placeholder="Fax" name="fax" required> 
                    <span class="error" id="fax_error"></span>
                </div>

                <div class="form-group row">
                    <div class="col-12">
                        <fieldset class="checkbox">
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                <input type="checkbox" required>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class=""> I accept the terms & conditions.</span>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-left btn-inline mb-50 submit">Register</button>
                <div class="last-element"></div>
            </form>
            <div class="footer">
                <a class="link" href="/login.php">Already have an account? Sign in</a>
                <a class="link" href="/register.php">Not a student?</a>
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
    <script src="/assets/js/availability.js"></script>
    <script src="/assets/js/states.js">

    </script>
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>