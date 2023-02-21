<?php
session_start();

//require_once "../assets/php/pdo.inc.php"; this is DUMB
require_once "../assets/php/database.php";
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

// Gets users info
$user_info = $user -> getAccountInfo();
$profile_picture = $user -> getProfilePicture();

try
{
	//get username, password, email
	$stm = $db->prepare("SELECT * FROM users WHERE user_id=:user_id ");
	$stm->execute([
		"user_id" => $user_info["user_id"],
	]);

	if ($row = $stm->fetch(PDO::FETCH_ASSOC))
	{
// 		$username = $row['username'];
	    $old_pw = $row['password'];
// 		$email = $row['email'];
	}
	
	//name grade, profile picture, values
	$stm = $db->prepare("SELECT * FROM voters WHERE voter_id=:user_id ");
	$stm->execute([
		"user_id" => $user_info["user_id"],
	]);

	if ($row = $stm->fetch(PDO::FETCH_ASSOC))
	{
		$grade = $row['grade'];
	    
	    //literally all of this is found in $user_info...
// 		$profile_picture = '../assets/images/profile/'.$_SESSION['user-id'].'.'.$row['profile_picture'];
// 		if (!$profile_picture) $profile_picture = '../assets/images/profile/default.png';
// 		$firstname = $row['first_name'];
// 		$lastname = $row['last_name'];
	}
}

catch (PDOException $e)
{
	echo "PDO error: ".$e -> getMessage();
	exit();
}

catch (Exception $e)
{
	echo $e->getMessage();
	exit();
}
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- - var description  = ""-->
    <title>Account Settings</title>
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="description" content="Skule Vote rocks.">
	<meta name="keywords" content="school votes, student elections">
	<meta name="author" content="Digitera">
	<link rel="apple-touch-icon" href="../app-assets/images/ico/apple-icon-120.png">
	<link rel="shortcut icon" type="image/x-icon" href="../app-assets/images/ico/favicon.ico">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">


	<!-- BEGIN: Vendor CSS-->
	<link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/vendors.min.css">
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
	<link rel="stylesheet" type="text/css" href="../app-assets/css/pages/users.css">
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
                            <h2 class="content-header-title float-left mb-0">Account Settings</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a>
                                    </li>
                                    <!--<li class="breadcrumb-item"><a href="#">Pages</a>
                                    </li>-->
                                    <li class="breadcrumb-item active">Account Settings
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
				<!-- Purple settings gear above profile background picture
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="#">Chat</a>
								<a class="dropdown-item" href="#">Email</a>
								<a class="dropdown-item" href="#">Calendar</a>
							</div>
                        </div>
                    </div>
                </div>-->
            </div>
            <div class="content-body">
                <div id="user-profile">
					<div class="profile-header mb-2">
						<div class="relative">
							<div class="cover-container">
								<img class="img-fluid bg-cover rounded-0 w-100 banner-picture" src="../app-assets/images/profile/user-uploads/cover.jpg" alt="User Profile Image">
							</div>
							<div class="profile-img-container d-flex align-items-center justify-content-between">
								<img src="<?=$profile_picture?>"
									class="rounded-circle img-border box-shadow-1 profile-picture" alt="Card image" id="profilepic">
							</div>
						</div>
					</div>
				</div>
			</div>
					<div class="content-body">
						<section id="profile-info">
							<div class="row">
								<div class="col-lg-12 col-12">
									<div class="card">
										<div class="card-header">
											<h4>Account Settings</h4>
											<!--<i class="feather icon-more-horizontal cursor-pointer"></i>-->
										</div>
										<div class="card-body">
												
												<button class="btn btn-primary mr-1 mb-1" onclick="back();">Back</button>
												<form action="../assets/php/submit_edited_account_settings.inc.php" method="post" style="inherit">
												<!--
													<div class="default-collapse collapse-bordered">
													<div class="card collapse-header">
														<div id="headingCollapse1" class="card-header" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
															<span class="lead collapse-title">
																General
															</span>
														</div>
														<div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse">
															<div class="card-content">
																
																<label for="basicInput">Profile Picture</label>
																<input type="text" name="profile-pic" class="form-control" id="basicInput" placeholder="Choose a new profile pic (it doesn't work)" disabled>
																<br>
																<label for="basicInput">Banner Picture</label>
																<input type="text" name="banner-pic" class="form-control" id="basicInput" placeholder="Choose a new banner pic (it doesn't work)" disabled>
																<br>
																<label for="basicInput">Username</label>
																<input type="text" name="username" class="form-control" id="basicInput" placeholder="Enter username" value=<?php //echo $username?>>
																<br>
																<label for="basicInput">Email</label>
																<input type="text" name="email" class="form-control" id="basicInput" placeholder="Enter email" value=<?php //echo $email?>>
																<br>
															</div>
														</div>
													</div>
													<div class="card collapse-header">
														<div id="headingCollapse2" class="card-header" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
															<span class="lead collapse-title">
																Personal Information
															</span>
														</div>
														<div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse">
															<div class="card-content">
																<div class="card-body">
																	<label for="basicInput">First Name</label>
																	<input type="text" name="firstname" class="form-control" id="basicInput" placeholder="Enter first name" value=<?php //echo $firstname?> disabled>
																	<br>
																	<label for="basicInput">Last Name</label>
																	<input type="text" name="lastname" class="form-control" id="basicInput" placeholder="Enter last name" value=<?php //echo $lastname?> disabled>
																	<br>
																	<label for="basicInput">Grade</label>
																	<input type="text" name="grade" class="form-control" id="basicInput" placeholder="Enter grade" value=<?//php echo $grade?> disabled>
																	<br>
																</div>
															</div>
														</div>
													</div>
													<div class="card collapse-header">
														<div id="headingCollapse3" class="card-header collapse-header" data-toggle="collapse" role="button" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
															<span class="lead collapse-title">
																Password
															</span>
														</div>
														<div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse" aria-expanded="false">
															<div class="card-content">
																<div class="card-body">
																	<label for="basicInput">New Password</label>
																	<input type="password" name="new-pw" class="form-control" id="basicInput" placeholder="Enter your new password">
																	<br>
																	<label for="basicInput">Confirm New Password</label>
																	<input type="password" name="confirm-new-pw" class="form-control" id="basicInput" placeholder="Retype your new password">
																	<br>
																	<label for="basicInput">Old Password</label>
																	<input type="password" name="old-pw" class="form-control" id="basicInput" placeholder="Enter your old password">
																	<br>
																</div>
															</div>
														</div>
													</div>
													-->
													<div style="display: flex; flex-direction: row; justify-content: space-around">
														<div>
															<div class="card-header">
																<h5 style="text-align: left">General</h5>
															</div>
															<!--
															<label for="basicInput">Profile Picture</label>
															<input type="text" name="profile-pic" class="form-control" id="basicInput" placeholder="Choose a new profile pic (it doesn't work)" disabled>
															<br>
															<label for="basicInput">Banner Picture</label>
															<input type="text" name="banner-pic" class="form-control" id="basicInput" placeholder="Choose a new banner pic (it doesn't work)" disabled>
															<br>-->
															<label for="basicInput">Username</label>
															<input type="text" name="username" class="form-control" id="basicInput" placeholder="Enter username" value="<?php echo $user_info[username]?>">
															<br>
															<label for="basicInput">Email</label>
															<input type="text" name="email" class="form-control" id="basicInput" placeholder="Enter email" value="<?php echo $user_info[email]?>">
															<br>
														
														</div>
														<div>
															<div class="card-header">
																<h5 style="text-align: left">Personal Information</h5>
															</div>
															<label for="basicInput">First Name</label>
															<input type="text" name="firstname" class="form-control" id="basicInput" placeholder="<?php echo $user_info[first_name]?>"
															>
															<br>
															<label for="basicInput">Last Name</label>
															<input type="text" name="lastname" class="form-control" id="basicInput" placeholder="<?php echo $user_info[last_name]?>" >
															<br>
				<div class="form-group">
                    <label for="grade">Grade</label>
                    <select name="grade" id="grade" class="form-control" required>
                        <option value="<?php echo $grade ?>"><?php echo $grade ?></option>
                        <?php for ($grade_tmp = 7; $grade_tmp <= 12; $grade_tmp++): ?>
                            <?php if ($grade_tmp != $grade): ?>
                                <option value="<?=$grade_tmp?>"><?=$grade_tmp?></option>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </select>

                    <span class="error" id="grade_error">
                        <?=$error["grade"] ?? ""?>
                    </span>
                </div>
														</div>
														<div>
															<div class="card-header">
																<h5 style="text-align: left">Password</h5>
															</div>
															<label for="basicInput">New Password</label>
															<input type="password" name="new-pw" class="form-control" id="basicInput" placeholder="Enter your new password">
															<br>
															<label for="basicInput">Confirm New Password</label>
															<input type="password" name="confirm-new-pw" class="form-control" id="basicInput" placeholder="Retype your new password">
															<br>
															<label for="basicInput">Old Password</label>
															<input type="password" name="old-pw" class="form-control" id="basicInput" placeholder="Enter your old password">
															<br>
														</div>
													</div>
													<br>
													<button type="submit" name="update-account-submit" class="btn btn-primary mr-1 mb-1" style="width: 100px">Save</button>
												</form>
											<div class="mt-1">
												<!--<button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-facebook"></i></button>
												<button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-twitter"></i></button>
												<button type="button" class="btn btn-sm btn-icon btn-primary p-25"><i class="feather icon-instagram"></i></button>-->
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
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
	<!-- END: Page Vendor JS-->

	<!-- BEGIN: Theme JS-->
	<script src="../app-assets/js/core/app-menu.js"></script>
	<script src="../app-assets/js/core/app.js"></script>
	<script src="../app-assets/js/scripts/components.js"></script>
	<!-- END: Theme JS-->

	<!-- BEGIN: Page JS-->
	<script src="../app-assets/js/scripts/pages/user-profile.js"></script>
	<!-- END: Page JS-->
	
	<script>
		function back()
		{
			if (document.referrer)
			{
				location.href=document.referrer;
			}
			else 
			{
				location.href='candidate-profile.php';
			}
		}
	</script>

</body>
<!-- END: Body-->

</html>