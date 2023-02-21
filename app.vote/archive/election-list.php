<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
	<?php session_start(); ?>
    <title>Elections</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/file-uploaders/dropzone.min.css">
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
    <!--<link rel="stylesheet" type="text/css" href="../app-assets/css/plugins/file-uploaders/dropzone.css">-->
    <link rel="stylesheet" type="text/css" href="../app-assets/css/pages/data-list-view.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="../assets/css/table.css">
    <!-- END: Custom CSS-->
	
	<?php
		date_default_timezone_set('America/Toronto');
		$date = date('Y-m-d H:i:s', time());
		require "../assets/php/dbh.inc.php";
		
		$sql = "SELECT * FROM elections";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)){
			echo '<p>sql error</p>';
			exit();
		}
		else{
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			$i = 0;
			while ($row = mysqli_fetch_assoc($result)){
				if ($row['finishing_date'] > $date){
					$elections[$i]['school'] = $row['school_id'];
					$elections[$i]['name'] = $row['election_name'];
					$elections[$i]['votes'] = $row['total_votes'];
					$elections[$i]['candidates'] = $row['num_candidates'];
					$elections[$i]['start'] = $row['starting_date'];
					$elections[$i]['end'] = $row['finishing_date'];
					$i++;
				}
			}
		}
		
		//get school names
		for ($j = 0; $j < $i; $j++){
			$sql = "SELECT * FROM schools WHERE school_id=?";
			$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)){
				echo '<p>sql error</p>';
				exit();
			}
			else{
				mysqli_stmt_bind_param($stmt, "i", $elections[$j]['school']);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);

				while ($row = mysqli_fetch_assoc($result)){
					$elections[$j]['school'] = $row['school_name'];
					$j++;
				}
			}
		}
		
		//convert datetime values to legit stuff
		for ($j = 0; $j < $i; $j++){
			$d = date_create($elections[$j]['start']);
			$elections[$j]['start'] = date_format($d, 'M d, Y');
			$d = date_create($elections[$j]['end']);
			$elections[$j]['end'] = date_format($d, 'M d, Y');
			//echo $elections[$j]['start'].', '.$elections[$j]['end'].'<br>';
		}
	?>
	
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- BEGIN: Header-->
    <?php include"header.html";?>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <?php include"left-nav.html";?>
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
                            <h2 class="content-header-title float-left mb-0">Elections</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Elections
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!--<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
                        </div>
                    </div>
                </div>-->
            </div>
            <div class="content-body">
                <!-- Data list view starts -->
                <!--<section id="data-list-view" class="data-list-view-header">
				<!--
                    <div class="action-btns d-none">
                        <div class="btn-dropdown mr-1 mb-1">
                            <div class="btn-group dropdown actions-dropodown">
                                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Delete</a>
                                    <a class="dropdown-item" href="#">Archive</a>
                                    <a class="dropdown-item" href="#">Print</a>
                                    <a class="dropdown-item" href="#">Another Action</a>
                                </div>
                            </div>
                        </div>
                    </div>
-->
					<div class="sortfilter">
						<div>Sort
							<fieldset class="form-group">
								<select class="custom-select" id="customSelect">
									<option value="school" selected>School</option>
									<option value="name">Name</option>
									<option value="start">Start Date</option>
									<option value="end">End Date</option>
								</select>
							</fieldset>
						</div>
						
						<div>Filter
							<div class="form-group" style="width: 250px">
								<select class="select2 form-control">
									<option value="school">School</option>
									<option value="rectangle">Name</option>
									<option value="romboid">Start Date</option>
									<option value="trapeze">End Date</option>
								</select>
							</div>
						</div>
						
						<div>
							<fieldset class="form-group">
								<select class="custom-select" id="customSelect">
									<option value="all" selected>All elections</option>
									<option value="current">Current Elections</option>
									<option value="upcoming">Upcoming Elections</option>
								</select>
							</fieldset>
						</div>
					</div>

                    <!-- DataTable starts -->
                        <table class="table election-table">
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
								<?php
									for ($j = 0; $j < $i; $j++){
										echo '<tr>';
											echo '<td>'.$elections[$j]['school'].'</td>';
											echo '<td>'.$elections[$j]['name'].'</td>';
											echo '<td>'.$elections[$j]['candidates'].'</td>';
											echo '<td>'.$elections[$j]['start'].'</td>';
											echo '<td>'.$elections[$j]['end'].'</td>';
											echo '<td>'.$elections[$j]['votes'].'</td>';
										echo '</tr>';
									}
								?>
                            </tbody>
                        </table>
						<?php
							if ($i == 0) echo '<p>There are no current or upcoming elections to display. Check back later or change your filters to view more elections.</p>'
						?>
                    <!-- DataTable ends -->

                    <!-- add new sidebar starts --
                    <div class="add-new-data-sidebar">
                        <div class="overlay-bg"></div>
                        <div class="add-new-data">
                            <div class="div mt-2 px-2 d-flex new-data-title justify-content-between">
                                <div>
                                    <h4>ADD NEW DATA</h4>
                                </div>
                                <div class="hide-data-sidebar">
                                    <i class="feather icon-x"></i>
                                </div>
                            </div>
                            <div class="data-items pb-3">
                                <div class="data-fields px-2 mt-3">
                                    <div class="row">
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-name">Name</label>
                                            <input type="text" class="form-control" id="data-name">
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-category"> Category </label>
                                            <select class="form-control" id="data-category">
                                                <option>Audio</option>
                                                <option>Computers</option>
                                                <option>Fitness</option>
                                                <option>Appliance</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-status">Order Status</label>
                                            <select class="form-control" id="data-status">
                                                <option>Pending</option>
                                                <option>Canceled</option>
                                                <option>Delivered</option>
                                                <option>On Hold</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 data-field-col">
                                            <label for="data-price">Price</label>
                                            <input type="number" class="form-control" id="data-price">
                                        </div>
                                        <div class="col-sm-12 data-field-col data-list-upload">
                                            <form action="#" class="dropzone dropzone-area" id="dataListUpload">
                                                <div class="dz-message">Upload Image</div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="add-data-footer d-flex justify-content-around px-3 mt-2">
                                <div class="add-data-btn">
                                    <button class="btn btn-primary">Add Data</button>
                                </div>
                                <div class="cancel-data-btn">
                                    <button class="btn btn-outline-danger">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- add new sidebar ends -->
                </section>
                <!-- Data list view end -->

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
    <script src="../app-assets/vendors/js/extensions/dropzone.min.js"></script>-->
    <script src="../app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
    <script src="../app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="../app-assets/js/core/app-menu.js"></script>
    <script src="../app-assets/js/core/app.js"></script>
    <script src="../app-assets/js/scripts/components.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="../app-assets/js/scripts/ui/data-list-view.js"></script>
    <!-- END: Page JS-->
	
</body>
<!-- END: Body-->

</html>