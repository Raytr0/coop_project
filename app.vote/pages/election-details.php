<?php
session_start();

include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";
require_once "../assets/php/database.php";
// require_once "../assets/php/pdo.inc.php";

$user = new Account();

// Checks if a user is currently logged in 
if ($user -> currentAccount() < 0)
{
    redirect("login.php");
}

// Checks if the users account is verified 
else if (!$user -> getAccountInfo("verified"))
{
    redirect("confirm-email.php");
}

if (!isset($_GET['election'])) 
{ 
	redirect("election-list.php");
}


//for displaying in the header
$user_info = $user -> getAccountInfo();
$profile_picture = $user -> getProfilePicture();

date_default_timezone_set('America/Toronto');
$date = date('Y-m-d H:i:s', time());


try{
	$stm = $db -> prepare("SELECT * FROM elections WHERE election_selector = ? ");
	$stm -> execute(array($_GET['election']));

	$election = $stm -> fetch(PDO::FETCH_ASSOC);
	
	
//Deletes everything related to this election 
//(positions and whatever "candidate_elections" are supposed to be) 
//in order to prevent foreign key checks stopping the deletion of this election
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($user -> getAccountInfo("type_id") == 3 && 
    isset($_POST["delete-election"])) {
        
        $stm = $db -> prepare("DELETE FROM candidate_elections WHERE election_id = ? ");
	    $stm -> execute(array($election['election_id']));
        
        $stm = $db -> prepare("DELETE FROM position_elections WHERE election_id = ? ");
	    $stm -> execute(array($election['election_id']));
	    
        $stm = $db -> prepare("DELETE FROM voter_elections WHERE election_id = ? ");
	    $stm -> execute(array($election['election_id']));
        
        $stm = $db -> prepare("DELETE FROM elections WHERE election_selector = ? ");
	    $stm -> execute(array($_GET['election']));
        redirect("election-list.php");
 	}    
}
	
	$stm = $db -> prepare("SELECT school_name FROM schools WHERE school_id=:school_id LIMIT 1");
	$stm -> execute([
		":school_id" => $election['school_id'],
	]);

	$school_name = $stm -> fetch(PDO::FETCH_ASSOC)["school_name"];
	
	
	//convert datetime values to legit stuff
	$d = date_create($election['starting_date']);
	$election['starting_date'] = date_format($d, 'F d, Y g:iA');
	$d = date_create($election['finishing_date']);
	$election['finishing_date'] = date_format($d, 'F d, Y g:iA');
	
	$stm = $db -> prepare("SELECT position_id FROM position_elections WHERE election_id=:election_id");
	$stm->execute([
		":election_id" => $election['election_id'],
	]);
	
	$positions = $stm -> fetchAll(PDO::FETCH_NUM);

	// Formats array
	foreach ($positions as $i => $position)
	{
		$positions[$i] = $position[0];
	}

	$position_name = [];
	$stm = $db -> prepare("SELECT position_name FROM positions WHERE position_id=:position_id");

	for ($j = 0; $j < count($positions); $j++)
	{
		$stm->execute([
			":position_id" => $positions[$j],
		]);

		$position_name[] = $stm -> fetch(PDO::FETCH_ASSOC)['position_name'];
	}


	$get_candidate_id = $db->prepare("
		SELECT candidate_id FROM candidate_elections WHERE election_id=? AND position_id=?;
	");

	$get_user_id = $db->prepare("
		SELECT user_id FROM candidates WHERE candidate_id=:candidate_id;
	");

	$get_user_name = $db->prepare("
		SELECT first_name, last_name FROM users WHERE user_id=:user_id;
	");

	$candidate_name = [];

	// Gets the name of each candidate for every position
	for ($j = 0; $j < count($positions); $j++)
	{
		$get_candidate_id -> bindValue(1, $election['election_id']);
		$get_candidate_id -> bindValue(2, $positions[$j]);
		$get_candidate_id -> execute();
		$candidate_ids = $get_candidate_id -> fetchAll(PDO::FETCH_NUM);

		// Formats array
		foreach ($candidate_ids as $i => $candidate_id)
		{
			$candidate_ids[$i] = $candidate_id[0];
		}

		foreach ($candidate_ids as $candidate_id)
		{
			$get_user_id -> execute([
				":candidate_id" => $candidate_id,
			]);

			$get_user_name -> execute([
				":user_id" => $get_user_id -> fetch(PDO::FETCH_ASSOC)["user_id"],
			]);
			
			$user_name = $get_user_name -> fetch(PDO::FETCH_ASSOC);

			if (empty($user_name))
			{
				echo 'no candidate with this id';
				die;
			}

			else
			{
				$candidate_name[$j][] = $user_name['first_name'].' '.$user_name['last_name'];
			}
		}

		if ($candidate_name[$j] == null || count($candidate_name[$j]) == 0) 
		{
			$candidate_name[$j][] = null;
		}
	}
}

catch (PDOException $e)
{
	$_SESSION['error'] = 'PDO: ' . $e->getMessage();
	echo 'PDO: ' . $e->getMessage() . "<br>";
	redirect("/pages/election-list.php");
}

catch (Exception $e)
{
	$_SESSION['error'] = 'error: ' . $e->getMessage();
	echo 'PDO: ' . $e->getMessage() . "<br>";
	redirect("/pages/election-list.php");
}

?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<!-------------------------we don't need number of candidates or number of votes; we can take those out-->
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Election Details</title>
	
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
    <link rel="stylesheet" type="text/css" href="../assets/css/table.css">
    <!-- END: Custom CSS-->
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- BEGIN: Header-->
    <?php include"templates/header.php";?>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <?php include"templates/navbar.php";?>
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
                            <h2 class="content-header-title float-left mb-0">Election Details</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="election-list.php">Election List</a>
                                    </li>
                                    <li class="breadcrumb-item active">Election Details
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
					<!--
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
                        </div>-->
                    </div>
                </div>
            </div>
            <div class="content-body">
				<div class="card">
					<div class="card-header" style="display: flex; justify-content: space-between;">
						<h4 class="card-title"><?php echo $election['election_name'];?></h4>
						<?php if ($user -> getAccountInfo("type_id") == 3 && $user -> getAccountInfo("school_id") == $election['school_id']): ?>
							<!-- <a href="javascript: delete_election();">Delete this election</a> -->
							<form action="add-candidates.php" method="post"> 
								<button type="submit" name="add_candidate" class="btn btn-outline-primary mb-1 btn-sm" style="color: #0f0; border-color: #0f0">Add a candidate to this election</button>
								<input name="election_selector" value="<?php echo $_GET['election'] ?>" hidden></input>
							</form>
							
							<form action="" method="post" 
							onsubmit="return delete_election();"  > 
								<button type="submit" name="delete-election" class="btn btn-outline-primary mr-1 mb-1 btn-sm" style="color: #F00; border-color: #f00">Delete this election</button>
								<input name="election_id" value="<?php echo $_GET['election']?>" hidden></input>
							</form>
						<?php endif; ?>
					</div>
					<div class="card-content">
						<div class="card-body" style="display: flex">
							<div style="flex: 1">
								<?php if (time() > strtotime($election['finishing_date'])): ?> 
									<p style="color: red">This election is in the past.
									</p>
								<?php endif; ?>
								<div class="mt-1"><h6 class="mb-0">Number of candidates:</h6></div>
								<p><?=$election['num_candidates']?></p>
								<div class="mt-1"><h6 class="mb-0">School:</h6></div>
								<p><?=$school_name?></p>
								<div class="mt-1"><h6 class="mb-0">Number of votes:</h6></div>
								<p><?=$election['total_votes']?></p>
								<div class="mt-1"><h6 class="mb-0">Start Date:</h6></div>
								<p><?=$election['starting_date']?></p>
								<div class="mt-1"><h6 class="mb-0">End Date:</h6></div>
								<p><?=$election['finishing_date']?></p>
							</div>
							<div style="flex: 1">
								<div class="mt-1"><h6 class="mb-0">Positions in this election:</h6></div><br>
								<?php for ($j = 0; $j < count($positions); $j++): ?>
									<p><b><?=$position_name[$j]?></b></p>

									<?php foreach ($candidate_name[$j] as $x): ?>

											<?php if (isset($x)): ?>
												<p><?=$x?></p>
											<?php endif; ?>

									<?php endforeach; ?>

								<?php endfor; ?>
							</div>
						</div>
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
		function delete_election(){
			if (!confirm('Are you sure you want to permanently delete this election, and everything associated with it?'))
				return false;
		}
	</script>
</body>
<!-- END: Body-->

</html>