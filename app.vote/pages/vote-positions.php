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
if (!($user_info['type_id'] == 1 || $user_info['type_id'] == 2)) {
    redirect("candidate-profile.php");
}
if (!isset($_POST['election-id'])) { 
    redirect("vote-home.php");
}

try{
	$stm = $db->prepare("SELECT position_id FROM position_elections WHERE election_id=?");
	$stm->execute(array($_POST['election-id']));

	$num_pos = 0;
	while ($row = $stm->fetch(PDO::FETCH_ASSOC)){
		$stm2 = $db->prepare("SELECT position_name FROM positions WHERE position_id=".$row['position_id'].";");
		$stm2->execute();
		
		if ($row2 = $stm2->fetch(PDO::FETCH_ASSOC)){
			$positions[$num_pos]['name'] = $row2['position_name'];
			$positions[$num_pos]['id'] = $row['position_id'];
			$num_pos++;
		}
	}
}
catch (PDOException $e){/*
	$_SESSION['error'] = 'PDO: '.$e->getMessage();
	header("Location: vote-home.php");*/
}
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<?php //session stuff
		include 'templates/test.php';
	?>
		
    <title>Positions</title>
	
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
                            <h2 class="content-header-title float-left mb-0">Positions</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="vote-home.php">Vote</a>
                                    </li>
                                    <li class="breadcrumb-item active">Positions
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
				<?php if ($num_pos == 0): ?> 
					<p>Oh no! Looks like there aren\'t any positions in this election. Please contact your school administrator.</p>
				<?php else: ?>
					<div class="card-container">
					<?php for ($j = 0; $j < $num_pos; $j++): ?>
						<div class="card voting-card clickable-card" onclick="javascript: selectPosition(<?=$positions[$j]['id']?>)">
							<div class="card-body">
								<h1 class="election-title"><?=$positions[$j]['name']?></h1>
							</div>
						</div>
					<?php endfor; ?>
					</div>
				<?php endif;?>
					
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
		function selectPosition(id){
			//post
			var mapForm = document.createElement("form");
			mapForm.target = "_self";    
			mapForm.method = "POST";
			mapForm.action = "vote-candidates.php";
			
			var mapInput = document.createElement("input");
			mapInput.type = "text";
			mapInput.name = "position-id";
			mapInput.value = id;
			mapForm.appendChild(mapInput);
			
			var mapInput = document.createElement("input");
			mapInput.type = "text";
			mapInput.name = "election-id";
			mapInput.value = <?php echo $_POST['election-id'] ?>;
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