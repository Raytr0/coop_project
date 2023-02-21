<?php 
session_start();

// Prevents browser from caching the page
header("Cache-Control: no-cache, no-store, must-revalidate");

include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";
require_once "../assets/php/database.php";
// require_once "../assets/php/pdo.inc.php"; dumb stuff once again

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

// Checks if the users account is an admin 
else if ($user -> getAccountInfo("type_id") != 3)
{
	redirect("election-list.php");
}


//for displaying in the header
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
		$positions[$i]['id'] = $row['position_id'];
		$positions[$i]['name'] = $row['position_name'];
		$i++;
	}
}

catch (PDOException $e)
{
	$_SESSION['error'] = 'PDO: '.$e->getMessage();
	redirect("/pages/election_list.php");
}
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<!-------------------------we don't need number of candidates or number of votes; we can take those out-->
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>New Election</title>
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="widths=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="description" content="Skule Vote rocks.">
	<meta name="keywords" content="school votes, student elections">
	<meta name="author" content="Digitera">
	<link rel="apple-touch-icon" href="../app-assets/images/ico/apple-icon-120.png">
	<link rel="shortcut icon" type="image/x-icon" href="../app-assets/images/ico/favicon.ico">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
	<link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/pickers/pickadate/pickadate.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/forms/select/select2.min.css">
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
    <link rel="stylesheet" type="text/css" href="../assets/css/table.css">
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
                            <h2 class="content-header-title float-left mb-0">New Election</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="election-list.php">Election List</a>
                                    </li>
                                    <li class="breadcrumb-item active">New Election
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Create a new election</h4>
					</div>
					<div class="card-content">
						<div class="card-body">
							<form action="/assets/php/submit_new_election.inc.php" onsubmit="return validate();" method="post" id="form">
								<div style="display: flex; justify-content: space-around;">
									<div style="display: inline-block; width: 40%">
										<label for="basicInput">Election Name</label>
										<input type="text" name="name" class="form-control" placeholder="Election Name" id="name" required>
										<label for="basicInput">Start Date</label>
										<input type='text' class="form-control format-picker" name="start-date" id="startdate" required>
										<label for="basicInput">Start Time</label>
										<input type='text' class="form-control pickatime" name="start-time" id="starttime" required>
										<label for="basicInput">End Date</label>
										<input type='text' class="form-control format-picker" name="end-date" id="enddate" required>
										<label for="basicInput">End Time</label>
										<input type='text' class="form-control pickatime" name="end-time" id="endtime" required>
										<br>
										<button type="submit" name="new-election-submit" class="btn btn-primary mr-1 mb-1" style="width: 150">Submit</button>
									</div>
									<div style="display: inline-block; width: 40%">
										<div style="display: flex; justify-content: space-between;">
											<label for="basicInput">Positions</label>
											<a href="edit-positions.php">Edit positions</a>
										</div>
										<select class="select2 form-control" multiple="multiple" name="positions[]" id="pos">
											<?php
												for ($j = 0; $j < $i; $j++){
													echo '<option value='.$positions[$j]['id'].'>'.$positions[$j]['name'].'</option>';
												}
											?>
										</select>
									</div>
								</div>
							</form>
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
    <script src="../app-assets/vendors/js/pickers/pickadate/picker.js"></script>
    <script src="../app-assets/vendors/js/pickers/pickadate/picker.date.js"></script>
    <script src="../app-assets/vendors/js/pickers/pickadate/picker.time.js"></script>
    <script src="../app-assets/vendors/js/pickers/pickadate/legacy.js"></script>
    <script src="../app-assets/vendors/js/forms/select/select2.full.min.js"></script>
	<!-- END: Page Vendor JS-->

	<!-- BEGIN: Theme JS-->
	<script src="../app-assets/js/core/app-menu.js"></script>
	<script src="../app-assets/js/core/app.js"></script>
	<script src="../app-assets/js/scripts/components.js"></script>
	<!-- END: Theme JS-->

	<!-- BEGIN: Page JS-->
    <script src="../app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js"></script>
    <script src="../app-assets/js/scripts/forms/select/form-select2.js"></script>
	<!-- END: Page JS-->
	
	<script>
		function validate(){
			if (document.getElementById('name').value && document.getElementById('startdate').value &&
				document.getElementById('starttime').value && document.getElementById('enddate').value &&
				document.getElementById('endtime').value
				&& document.getElementById('pos').selectedOptions.length){
				
				var today = new Date();
				var hours = today.getHours();
				if (today.getHours() >= 12){
					var ampm = ' PM';
					if (today.getHours() > 12) hours = today.getHours() - 12;
				} else{
					var ampm = ' AM';
				}
				var now = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate()+' '+hours + ":" + today.getMinutes();
				now = Date.parse(now);
				
				var startdate = document.getElementById('startdate').value;
				var firstcomma = startdate.indexOf(',');
				var secondcomma = startdate.lastIndexOf(',');
				var month = startdate.substr(0, firstcomma);
				//alert(startdate);
				month = ('January___February__March_____April_____May_______June______July______August____September_October___November__December__'.indexOf(month) / 10 + 1);
				var day = startdate.substr(firstcomma + 2, secondcomma - (firstcomma + 2));
				var year = startdate.substr(secondcomma + 2);
				startdate = year+'-'+month+'-'+day+' '+ document.getElementById('starttime').value;
				startdate = Date.parse(startdate);
				
				var enddate = document.getElementById('enddate').value;
				var firstcomma = enddate.indexOf(',');
				var secondcomma = enddate.lastIndexOf(',');
				var month = enddate.substr(0, firstcomma);
				//alert(enddate);
				month = ('January___February__March_____April_____May_______June______July______August____September_October___November__December__'.indexOf(month) / 10 + 1);
				var day = enddate.substr(firstcomma + 2, secondcomma - (firstcomma + 2));
				var year = enddate.substr(secondcomma + 2);
				enddate = year+'-'+month+'-'+day+' '+ document.getElementById('endtime').value;
				enddate = Date.parse(enddate);
				//alert(now+'\n'+startdate+'\n'+enddate);
				
				if (startdate < now){
					alert("The start date cannot be in the past.");
					return false;
				}
				else if (enddate < now){
					alert("The end date cannot be in the past.");
					return false;
				}
				else if (enddate < startdate){
					alert("Sorry, elections can't end before they start.");
					return false;
				}
				
			}else {
				alert('Please fill in all the boxes.');
				return false;
			}
		}
	</script>
</body>
<!-- END: Body-->

</html>