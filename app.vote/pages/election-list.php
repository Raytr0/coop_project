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

date_default_timezone_set('America/Toronto');
$date = date('Y-m-d H:i:s', time());

try
{
	$stm = $db -> prepare("
		SELECT * FROM elections WHERE school_id=:school_id;
	");

	$stm -> execute([
		":school_id" => $user -> getAccountInfo("school_id"),
	]);

	$elections = $stm -> fetchAll();

	$get_school_name = $db -> prepare("
		SELECT school_name FROM schools WHERE school_id=:school_id;
	");

	$get_school_name -> execute([
		":school_id" => $user -> getAccountInfo("school_id"),
	]);

	$school_name = $get_school_name -> fetch(PDO::FETCH_ASSOC)["school_name"];
}

catch (PDOException $e)
{
	$_SESSION['error'] = 'PDO: '.$e->getMessage();
	redirect("/pages/index.php");
}

//convert datetime values to legit stuff
for ($j = 0; $j < count($elections); $j++)
{
	$d = date_create($elections[$j]['starting_date']);
	$elections[$j]['starting_date'] = date_format($d, 'M d, Y');
	$d = date_create($elections[$j]['finishing_date']);
	$elections[$j]['finishing_date'] = date_format($d, 'M d, Y');
}


?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<!------------------------- we don't need number of candidates or number of votes; we can take those out-->
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Election List</title>
	
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
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/tables/datatable/datatables.min.css">
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
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/table.css">
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
                            <h2 class="content-header-title float-left mb-0">Election List</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Election List
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
						<?php if ($user -> getAccountInfo("type_id") == 3): ?>
							<div class="dropdown">
							<button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="new-election.php">Create new election</a>
								<a class="dropdown-item" href="delete-election.php">Delete an election</a>
							</div>
							</div>
						<?php endif;?>
                    </div>
                </div>
            </div>
			<?php if (isset($_SESSION['changesMade'])): ?>
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<h4 class="alert-heading">Success</h4>
						<p class="mb-0">You have successfully <?=$_SESSION['changesMade']?>.</p>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true"><i class="feather icon-x-circle"></i></span>
						</button>
					</div>

					<?php unset($_SESSION['changesMade']);?>
			
			<?php endif;?>

			<?php if (isset($_SESSION['error'])): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<h4 class="alert-heading">Oh no...</h4>
						<p class="mb-0">An error has occured.</p>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true"><i class="feather icon-x-circle"></i></span>
						</button>
					</div>
					<?php unset($_SESSION['error']);?>

			<?php endif;?>
			
            <div class="content-body">
                <!-- Zero configuration table -->
                <section id="basic-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
								<div class="card-header" style="display: flex; justify-content: space-between;">
                                    <h4 class="card-title">Election List </h4> <p style="margin: 0px;"> </p>
									<?php if ($_SESSION['user-type'] == 3) echo '<a href="new-election.php">Create a new election</a>';?>
                                </div>
                                
                                <div class="card-content" id="currentElections">
                                    <div class="card-body card-dashboard">
                                        <p style="display: inline-block">Only current and upcoming elections are shown. &nbsp;&nbsp; 
                                            <a href="javascript: show_all();" class="toggle-past-elections" >View all elections</a>
										</p>
                                        <div class="table-responsive">
                                            <table class="table zero-configuration current-elections">
                                                <thead>
                                                    <tr>
														<th>School</th>
														<th>Name</th>
														<th>Candidates</th>
														<th>Start</th>
														<th>End</th>
														<th>Votes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
		<?php for ($j = 0; $j < count($elections); $j++): ?>
			<?php if (time() < strtotime($elections[$j]['finishing_date'])): ?>
			
									<tr id=' <?php echo $elections[$j]['election_selector'] ?>' class="table-row">
															<td class="school"><?=$school_name?></td>
															<td class="name"><?=$elections[$j]['election_name']?></td>
															<td class="candidates"><?=$elections[$j]['num_candidates']?></td>
															<td class="start"><?=$elections[$j]['starting_date']?></td>
															<td class="end"><?=$elections[$j]['finishing_date']?></td>
															<td class="votes"><?=$elections[$j]['num_votes'] ?? 0?></td> 
									</tr>
														
		    <?php endif; ?>
		<?php endfor; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-content" id="allElections" style="display: none">
                                    <div class="card-body card-dashboard">
										<a href="javascript: show_current();" class="toggle-past-elections" >View current elections</a>
                                        <div class="table-responsive">
                                            <table class="table zero-configuration current-elections">
                                                <thead>
                                                    <tr>
														<th>School</th>
														<th>Name</th>
														<th>Candidates</th>
														<th>Start</th>
														<th>End</th>
														<th>Votes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
			<?php for ($j = 0; $j < count($elections); $j++): ?>
														<tr id='<?=$elections[$j]['election_selector']?>' class="table-row">
															<td class="school"><?=$school_name?></td>
															<td class="name"><?=$elections[$j]['election_name']?></td>
															<td class="candidates"><?=$elections[$j]['num_candidates']?></td>
															<td class="start"><?=$elections[$j]['starting_date']?></td>
															<td class="end"><?=$elections[$j]['finishing_date']?></td>
															<td class="votes"><?=$elections[$j]['num_votes'] ?? 0?></td>
														</tr>
													<?php endfor; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                </section>
                <!--/ Zero configuration table -->
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
    <script src="../app-assets/vendors/js/tables/datatable/pdfmake.min.js"></script>
    <script src="../app-assets/vendors/js/tables/datatable/vfs_fonts.js"></script>
    <script src="../app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
    <script src="../app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
    <script src="../app-assets/vendors/js/tables/datatable/buttons.html5.min.js"></script>
    <script src="../app-assets/vendors/js/tables/datatable/buttons.print.min.js"></script>
    <script src="../app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js"></script>
    <script src="../app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="../app-assets/js/core/app-menu.js"></script>
    <script src="../app-assets/js/core/app.js"></script>
    <script src="../app-assets/js/scripts/components.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="../app-assets/js/scripts/datatables/datatable.js"></script>
    <!-- END: Page JS-->
	
	<script>
		/**************************** no clue why this doesn't work but it doesn't
		function election_click(var id){
			/*
			// Create a form
			var mapForm = document.createElement("form");
			mapForm.target = "_blank";    
			mapForm.method = "POST";
			mapForm.action = "election-details.php";

			// Create an input
			var mapInput = document.createElement("input");
			mapInput.type = "text";
			mapInput.name = "election_id";
			mapInput.value = id;

			// Add the input to the form
			mapForm.appendChild(mapInput);

			// Add the form to dom
			document.body.appendChild(mapForm);

			// Just submit
			mapForm.submit();
			
		}*/
		
		$('.table-row').click(function() {
//adds a space for some reason, .replace removes only the first space so I took some regular expressions from stack ovrflow, just in case
			var link = 'election-details.php?election=' + $(this).attr('id');
			var link = link.replace(/\s/g,'');
// 			document.write(link.replace(" ",''));
			window.open(link, "_self");
		});
		
		
		function show_all(){
			
			document.getElementById("currentElections").style = "display: none";
			document.getElementById("allElections").style = "display: block";
		}
		
		function show_current(){
			
			document.getElementById("currentElections").style = "display: block";
			document.getElementById("allElections").style = "display: none";
		}
		
	</script>

</body>
<!-- END: Body-->

</html>