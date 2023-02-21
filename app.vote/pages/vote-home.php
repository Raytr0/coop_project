<?php
session_start();

include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";
require_once "../assets/php/database.php";
// require_once "../assets/php/pdo.inc.php"; dumb stuff over here folks!

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

//for displaying in the header
$user_info = $user -> getAccountInfo();
$profile_picture = $user -> getProfilePicture();


		//only voters and candidates can vote
		if (!($user_info['type_id'] == 1 || $user_info['type_id'] == 2))
			header("Location: candidate-profile.php");
	
		date_default_timezone_set('America/Toronto');
		$date = date('Y-m-d H:i:s', time());
		
		try{
			$stm = $db->prepare("SELECT * FROM elections WHERE school_id= ? ");
			$stm->execute(array($user_info['school_id']));

			//get current elections at the school
			$c_elec = 0;
			$u_elec = 0;
			while ($row = $stm->fetch(PDO::FETCH_ASSOC)){
				if ($row['starting_date'] <= $date && $row['finishing_date'] >= $date){
					$current_elections[$c_elec]['id'] = $row['election_id'];
					$current_elections[$c_elec]['name'] = $row['election_name'];
					$current_elections[$c_elec]['start-full-date'] = $row['starting_date'];
					$current_elections[$c_elec]['end-full-date'] = $row['finishing_date'];
					$c_elec++;
				}
				else if ($row['finishing_date'] >= $date){
					$upcoming_elections[$u_elec]['id'] = $row['election_id'];
					$upcoming_elections[$u_elec]['name'] = $row['election_name'];
					$upcoming_elections[$u_elec]['start-full-date'] = $row['starting_date'];
					$upcoming_elections[$u_elec]['end-full-date'] = $row['finishing_date'];
					$u_elec++;
				}
			}
		}
		catch (PDOException $e){
			$_SESSION['error'] = 'PDO: '.$e->getMessage();
			//header("Location: election-list.php");
			echo $_SESSION['school'];
		}
		
		//convert datetime values to legit stuff
		for ($j = 0; $j < $c_elec; $j++){
			$d = date_create($current_elections[$j]['start-full-date']);
			$current_elections[$j]['start'] = date_format($d, 'M d, Y');
			$d = date_create($current_elections[$j]['end-full-date']);
			$current_elections[$j]['end'] = date_format($d, 'M d, Y');
		}
		for ($j = 0; $j < $u_elec; $j++){
			$d = date_create($upcoming_elections[$j]['start-full-date']);
			$upcoming_elections[$j]['start'] = date_format($d, 'M d, Y');
			$d = date_create($upcoming_elections[$j]['end-full-date']);
			$upcoming_elections[$j]['end'] = date_format($d, 'M d, Y');
		}
	?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">	
    <title>Vote Home</title>
	
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
	<link rel="stylesheet" type="text/css" href="../assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="../assets/css/voting.css">
	<!-- END: Custom CSS-->
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- BEGIN: Header-->
    <?php include_once "templates/header.php";?>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <?php include_once "templates/navbar.php";?>
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
                            <h2 class="content-header-title float-left mb-0">Vote</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a>
                                    </li>
                                    <!--<li class="breadcrumb-item"><a href="#">Pages</a>
                                    </li>-->
                                    <li class="breadcrumb-item active">Vote
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
			<?php if (isset($_SESSION['error'])): ?>
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<h4 class="alert-heading">Error</h4>
					<p class="mb-0"><?=$_SESSION['error']?></p>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true"><i class="feather icon-x-circle"></i></span>
					</button>
				</div>
				<?php unset($_SESSION['error']); ?>
			<?php endif; ?>

			<?php if ($c_elec == 0 && $u_elec == 0): ?>
				<p>Oh no! Looks like there aren\'t any elections running right now.</p>
			<?php else: ?>
				<!-- current elections first -->
				<h2>Current Elections</h2>
				<div class="card-container">
				<?php for ($j = 0; $j < $c_elec; $j++): ?>
					<div class="card voting-card clickable-card" onclick="javascript: openElection(<?=$current_elections[$j]['id']?>)">
						<div class="card-body">
							<h1 class="election-title"><?=$current_elections[$j]['name']?></h1>
							<p class="card-text"><?=$current_elections[$j]['start'].' - '.$current_elections[$j]['end']?></p>
						</div>
					</div>
				<?php endfor; ?>
				</div><br><br>
				
				<!-- upcoming elections -->
				<h2>Upcoming Elections</h2>
				<div class="card-container">
				<?php for ($j = 0; $j < $u_elec; $j++): ?>
					<div class="card voting-card disabled-card">
						<div class="card-body">
							<h1 class="election-title"><?=$upcoming_elections[$j]['name']?></h1>
							<p class="card-text"><?=$upcoming_elections[$j]['start'].' - '.$upcoming_elections[$j]['end']?></p>
						</div>
					</div>
				<?php endfor; ?>
				</div>
			
			<? endif;?>
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
		function openElection(id){
			//post
			var mapForm = document.createElement("form");
			mapForm.target = "_self";    
			mapForm.method = "POST";
			mapForm.action = "vote-positions.php";
			
			var mapInput = document.createElement("input");
			mapInput.type = "text";
			mapInput.name = "election-id";
			mapInput.value = id;
			mapForm.appendChild(mapInput);

			// Add the form to dom
			document.body.appendChild(mapForm);

			// Just submit
			mapForm.submit();
		}
	</script>
</body>
<!-- END: Body-->

</html>