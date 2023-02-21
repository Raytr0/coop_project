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

$_SESSION['user_id'] = $user_info['user_id'];
$_SESSION['school_id'] = $user_info['school_id'];

if (!isset($_POST['election-id'])) 
{
	header("Location: vote-home.php");
}

try{
	$stm = $db->prepare("SELECT candidate_id, school_id FROM candidate_elections WHERE election_id=? AND position_id=? ");
	$stm->execute(array($_POST['election-id'], $_POST['position-id']));

	$num_candidates = 0;
	while ($row = $stm->fetch(PDO::FETCH_ASSOC)){
		//get voter id
		$stm2 = $db->prepare("SELECT voter_id FROM candidates WHERE candidate_id=? ");
		$stm2->execute(array($row['candidate_id']));
		
		if ($row2 = $stm2->fetch(PDO::FETCH_ASSOC)){
			//get name
			$stm3 = $db->prepare("SELECT first_name, last_name FROM voters WHERE voter_id=? ");
			$stm3->execute(array($row2['voter_id']));
			
			if ($row3 = $stm3->fetch(PDO::FETCH_ASSOC)){
				$c_name[$num_candidates] = $row3['first_name'].' '.$row3['last_name'];
				$c_id[$num_candidates] = $row['candidate_id'];
				$num_candidates++;
			} 
			else 
			{
				throw new Exception ('no name found');
			}
		} 
		else 
		{
			throw new Exception ('no voter found');
		}
	}
}
catch (PDOException $e){
	$_SESSION['error'] = 'PDO: '.$e->getMessage();
	header("Location: vote-home.php");
}
catch (Exception $e){
	echo 'an error has occured: '.$e->getMessage();
}
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">		
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
                            <h2 class="content-header-title float-left mb-0">Candidates</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="vote-home.php">Vote</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="vote-positions.php">Positions</a>
                                    </li>
                                    <li class="breadcrumb-item active">Candidates
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
			<form action="../assets/php/submit-vote.php" method="post" onsubmit="return confirmVote();">
				<?php if ($num_candidates == 0): ?>
					<p>Oh no! Looks like there aren\'t any candidates running for this position.</p>
				<?php else: 
						//randomize indices
						for ($j = 0; $j < $num_candidates; $j++)
							$arr[$j] = $j;
						shuffle($arr);
				?>
						
						<?php for ($j = 0; $j < $num_candidates; $j++): ?>
							<div class="radio-option">
								<input type="radio" class="voteOption" name="vote" value="<?=$c_id[$arr[$j]]?>">
								<label class="" for="customRadio1"><?=$c_name[$arr[$j]]?></label>
							</div>
						<?php endfor; ?>
						
						<br><br>
						<p><u>Important!</u></p>
						<ul>
							<li>You may only vote once.</li>
							<li>Once you have submitted, you cannot change your vote.</li>
						</ul>
						
						<input type="text" style="display: none" name="election-id" value='<?=$_POST['election-id']?>'></input>
						<input type="text" style="display: none" name="position-id" value='<?=$_POST['position-id']?>'></input>
						
						<button type="submit" name="vote-submit" class="btn btn-primary" style="width: 150">Submit</button>
				<?php endif; ?>
			</form>
					
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
		function confirmVote(){
			var formFilled = false;
			$('.voteOption').each(function(i, obj){
				if (obj.checked)
					formFilled = true;
			})
			
			if (!formFilled){
				alert ('Please select a candidate.');
				return false;
			}
			else return confirm('Submit this vote?');
		}
	</script>
</body>
<!-- END: Body-->

</html>