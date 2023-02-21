<?php
session_start();

// Prevents browser from caching the page
header("Cache-Control: no-cache, no-store, must-revalidate");

include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";
require_once "../assets/php/database.php";
// require_once "../assets/php/pdo.inc.php"; dumb stuff again

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

// Checks if the current account is an admin
else if ($user -> getAccountInfo("type_id") != 3)
{
	redirect("/pages/election-list.php");
}

// Gets users info
$user_info = $user -> getAccountInfo();
$profile_picture = $user -> getProfilePicture();

try
{
	$stm = $db->prepare("SELECT position_id, position_name FROM positions WHERE school_id=?");
	$stm->bindValue(1, $user -> getAccountInfo("school_id"));
	$stm->execute();

    $i = 0;
    while ($row = $stm->fetch(PDO::FETCH_ASSOC))
    {
        $position_id[$i] = $row['position_id'];
        $position_name[$i] = $row['position_name'];
        $i++;
    }
}

catch (PDOException $e)
{
    $_SESSION['error'] = 'PDO: ' . $e -> getMessage();
    redirect("/pages/election_list.php");
}
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<!-------------------------we don't need number of candidates or number of votes; we can take those out-->
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Edit Positions</title>
	
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
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
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
                            <h2 class="content-header-title float-left mb-0">Edit Positions</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="election-list.php">Election List</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="new-election.php">New Election</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Positions
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
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
			
            <?php endif; ?>

			<?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Oh no...</h4>
                    <p class="mb-0">An error has occured.</p>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true"><i class="feather icon-x-circle"></i></span>
                    </button>
                </div>
                
                <?php unset($_SESSION['error']);?>

            <?php endif; ?>

			
            <div class="content-body">
                <!-- Zero configuration table -->
                <section id="basic-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-default">
								<div class="card-header" style="display: flex; justify-content: space-between;">
                                    <h4 class="card-title">Edit Positions</h4>
									<?php //if ($_SESSION['user-type'] == 3) echo '<a href="new-election.php">Create a new election</a>';?>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
										<p style="display: inline-block">Note: deleting a position will remove it from any elections.</p>
										<!--<a href="javascript: show_all();" class="toggle-past-elections">View all elections</a>-->
										<br>
										<button class="btn btn-primary mr-1 mb-1" onclick="openNewPos();">New Position</button>
                                        <div class="table-responsive">
                                            <table class="table zero-configuration current-elections">
                                                <thead>
                                                    <tr>
														<th>Position</th>
														<th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php for ($j = 0; $j < $i; $j++): ?>
															<tr class="table-row">
															    <!-- <tr id='.$elections[$j]['id'].' class="table-row"> -->
																<td class="name"><?=$position_name[$j]?></td>
																<td class="delete"><a onclick="deletePosition( <?=$position_id[$j]?>);" id='<?=$position_id[$j]?>'>Delete</a></td>
															</tr>
													<?php endfor; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div class="card card-new-pos" style="display: none">
								<div class="card-header" style="display: flex; justify-content: space-between;">
                                    <h4 class="card-title">Create a new position</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
										<a href="javascript: closeNewPos();">Cancel</a>
										<!--form for new pos-->
										<form action="/assets/php/new-position.inc.php" method="post" onsubmit="return validate();">
											<label for="basicInput">Position Name</label>
											<input type="text" name="name" class="form-control" placeholder="Position Name" id="positionName"><br>
											<button type="submit" name="new-pos-submit" class="btn btn-primary mr-1 mb-1" style="width: 150">Submit</button>
										</form>
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
		function deletePosition(id){
			if (confirm('Delete this position?')){
				var mapForm = document.createElement("form");
				mapForm.target = "_self";    
				mapForm.method = "POST";
				mapForm.action = "../assets/php/delete-position.inc.php";
				
				var mapInput = document.createElement("input");
				mapInput.type = "text";
				mapInput.name = "pos-id";
				mapInput.value = id;
				mapForm.appendChild(mapInput);

				// Add the form to dom
				document.body.appendChild(mapForm);

				// Just submit
				mapForm.submit();
			}
		}
		
		function openNewPos(){
			$('.card-new-pos').css("display","block");
			$('.card-default').css("display","none");
		}
		
		function closeNewPos(){
			$('.card-new-pos').css("display","none");
			$('.card-default').css("display","block");
		}
		
		function validate(){
			if (!document.getElementById('positionName').value){
				alert('Please enter a name for your new position.');
				return false;
			}
			
		}
	</script>

</body>
<!-- END: Body-->

</html>