<?php
session_start();

//none of this pre-head php was initially added. maybe this page isn't to be used rn?

// Prevents browser from caching user submissions
header("Cache-Control: no-cache, no-store, must-revalidate");

// Includes dependencies
include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";
require_once "../assets/php/database.php";

// Creates new user
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

// Checks if the current account is an admin
else if ($user -> getAccountInfo("type_id") != 3 || $_SERVER["REQUEST_METHOD"]  != "POST")
{
	redirect("/pages/election-list.php");
}

//for displaying in the header
$user_info = $user -> getAccountInfo();
$profile_picture = $user -> getProfilePicture();

$_SESSION['school_id'] = $user_info['school_id'];
?>


<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<!-------------------------we don't need number of candidates or number of votes; we can take those out-->
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
	
	<!--not sure what this is used for-->
	<?php //session stuff
		//include 'templates/test.php';
	?>
    <title>Add Candidates</title>
    
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
    <!-- END: Custom CSS-->
    
	<?php
		if ($user_info['type_id'] != 3){
			header('Location: election-list.php');
		}
		
		/*
		//get positions
		
		$sql = "SELECT position_id, position_name FROM positions WHERE school_id=?";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)){
			echo '<p>sql error</p>';
			exit();
		}
		else{
			mysqli_stmt_bind_param($stmt, "i", $_SESSION['school']);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			$i = 0;
			while ($row = mysqli_fetch_assoc($result)){
				$positions[$i]['id'] = $row['position_id'];
				$positions[$i]['name'] = $row['position_name'];
				$i++;
			}
		}
		*/
		
		try {
		    
		    //get the specific election we'll be adding candidates to from submitted electon selector (via pages/election-details.php)
			$stm = $db->prepare("SELECT * FROM elections WHERE election_selector = ?");
			$stm -> execute(array($_POST['election_selector']));

	        $election = $stm -> fetch(PDO::FETCH_ASSOC);
	        $_SESSION['election_id'] = $election['election_id']; 

//simple check	        
// echo "gottem {$election[election_id]}";
		    
		    //gets all the position ids associatd with the election
			$sql = $db->prepare("SELECT position_id FROM position_elections WHERE election_id = ? ");
			$sql -> execute(array($election['election_id']));
            
            //iterate through them and store them in $positions, and get their names from the positions table
			$i = 0;
			while ($row = $sql->fetch(PDO::FETCH_ASSOC)){
				$positions[$i]['id'] = $row['position_id'];
				$stm = $db->query("SELECT position_name FROM positions WHERE position_id=".$row['position_id']);
				$resultname = $stm->fetch();
				$positions[$i]['name'] = $resultname[0];
				
				//simple check
				//echo $i.'=> '.$positions[$i]['name'].' id =>'.$positions[$i]['id'].'<br>';
				$i++;
			}
			//get existing candidates
			$sql = $db->prepare("SELECT candidate_id, voter_id FROM candidates WHERE school_id=?");
			$sql->execute(array($election['school_id']));

			$x = 0;
			while ($row = $sql->fetch(PDO::FETCH_ASSOC)){
				$candidates[$x]['c_id'] = $row['candidate_id'];
				$candidates[$x]['v_id'] = $row['voter_id'];
				$stm = $db->prepare("SELECT first_name, last_name FROM voters WHERE voter_id=?");
				$stm-> execute(array($row['voter_id']));
				$resultname = $stm->fetch();
				$candidates[$x]['name'] = $resultname['first_name'] .' '. $resultname['last_name'];
				//echo $x . '=> '.$candidates[$x]['name'] . ' v_id =>'. $candidates[$x]['v_id'] . 'c_id =>' . $candidates[$x]['c_id'] . '<br>';
				$x++;
			}
		}
		catch (PDOException $e){
			$_SESSION['error'] = 'PDO: '.$e->getMessage();
			header("Location:../../pages/election_list.php");
		}
	?>
</head>
<!-- END: Head-->
<!-- BEGIN: Body-->



<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- BEGIN: Header-->
    <?php include"templates/header.php";?>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <?php include"templates/left-nav.php";?>
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
                                    <li class="breadcrumb-item active">New Candidate?
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
						<h4 class="card-title">Add Candidates</h4>
					</div>
					<div class="card-content">
						<div class="card-body">
							<form action="../assets/php/submit_candidates.inc.php" onsubmit="return validate();" method="post" id="form">
								<h5>Existing Candidates</h5>
								<!--<select class="select2 form-control" multiple="multiple" name="positions[]" id="candidate_select">
									<?php
										for ($j = 0; $j < $x; $j++){
											echo '<option value='.$candidates[$j]['c_id'].'>'.$candidates[$j]['name'].'</option>';
										}
									?>
								</select>
								<br><br><br>-->
								<table style="width: 40%">
									<tbody id="container_existing" style="width: 100%">
										<tr>
											<th style="width: 2em">#</td>
											<th>Candidate</th>
											<th>Position</th>
										</tr>
									</tbody>
								</table>
								<br>
								<a href="javascript: addExisting();">Add existing candidate: </a>
								<select id="selectExisting">
									<?php
										for ($j = 0; $j < $x; $j++){
											echo '<option value='.$candidates[$j]['c_id'].'>'.$candidates[$j]['name'].'</option>';
										}
									?>
								</select>
								<br><br>
								
								<h5>New Candidates</h5>
								<table style="width: 90%">
									<tbody id="container_new" style="width: 100%">
										<tr>
											<th style="width: 2em">#</td>
											<th>Username</th>
											<th>Password</th>
											<th>Grade</th>
											<th>First Name</th>
											<th>Last Name</th>
											<th>Position</th>
										</tr>
									</tbody>
								</table>
								<br>
								<a href="javascript: addNew();">Add new candidate</a>
								<br><br>
								<button type="submit" name="candidates-submit" class="btn btn-primary" style="width: 150">Submit</button>
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
		function addExisting(){
			if ($('#selectExisting option:selected').attr('disabled')){
				alert('This candidate has already been selected.');
				return false;
			}
			if (confirm('Add existing candidate?')){
				var container = document.getElementById('container_existing');
				
				//nth child
				var n = container.childElementCount;
				
				//make a new row
				var d = document.createElement('tr');
				d.setAttribute('id', 'd'+n);
				
				//get candidate id and name for table
				var candidate_id = document.getElementById('selectExisting').value;
				var candidate_name = $('#selectExisting option:selected').text();
				
				//number
				y = document.createElement('td');
				x = document.createElement('p');
				x.setAttribute('style', 'width: 2em');
				x.innerHTML = n;
				y.appendChild(x);
				d.appendChild(y);
				
				//candidate username
				y = document.createElement('td');
				x = document.createElement('input');
				x.setAttribute('name', 'existing_candidate' + n);
				x.setAttribute('style', 'display: none');
				x.setAttribute('value', candidate_id);
				
				y.appendChild(x);
				/*
				var num = '<?php echo $x; ?>';
				var can = JSON.parse('<?php echo json_encode($candidates);?>');
				
				for (j = 0; j < num; j++){
					z = document.createElement('option');
					z.setAttribute('value', candidate_id);
					z.innerHTML = candidate_name;
					x.appendChild(z);
				}*/
				
				x = document.createElement('input');
				x.readOnly = true;
				x.setAttribute('value', candidate_name);
				
				y.appendChild(x);
				d.appendChild(y);
				
				//position
				y = document.createElement('td');
				x = document.createElement('select');
				x.setAttribute('name', 'position_existing_candidate' + n);
				var i = '<?php echo $i; ?>';
				var pos = JSON.parse('<?php echo json_encode($positions);?>');
				
				for (j = 0; j < i; j++){
					z = document.createElement('option');
					z.setAttribute('value', pos[j]['id']);
					z.innerHTML = pos[j]['name'];
					x.appendChild(z);
				}
				y.appendChild(x);
				d.appendChild(y);
				
				/*
				//remove link
				x = document.createElement('a');
				x.setAttribute('href', 'javascript: remove('+n+');');
				x.innerHTML = ('remove');
				d.appendChild(x);
				*/
				
				container.appendChild(d);
				
				$('#selectExisting option:selected').attr('disabled', 'diabled');
			}
		}
		function addNew(){
			if (confirm('Add new candidate?')){
				var container = document.getElementById('container_new');
				
				//nth child
				var n = container.childElementCount;
				
				//make a new row
				var d = document.createElement('tr');
				d.setAttribute('id', 'd'+n);
				
				//number
				y = document.createElement('td');
				x = document.createElement('p');
				x.setAttribute('style', 'width: 2em');
				x.innerHTML = n;
				y.appendChild(x);
				d.appendChild(y);
				
				//candidate username
				y = document.createElement('td');
				x = document.createElement('input');
				x.setAttribute('name', 'username' + n);
				x.setAttribute('value', makeid(10));
				x.readOnly = true;
				y.appendChild(x);
				d.appendChild(y);
				
				//password
				y = document.createElement('td');
				x = document.createElement('input');
				x.setAttribute('name', 'password' + n);
				x.setAttribute('value', makeid(16));
				x.readOnly = true;
				y.appendChild(x);
				d.appendChild(y);
				
				// //grade
				// y = document.createElement('td');
				// x = document.createElement('input');
				// x.setAttribute('name', 'grade' + n);
				// x.setAttribute('type', 'number');
				// x.setAttribute('min', '7');
				// x.setAttribute('max', '13');
				// y.appendChild(x);
				// d.appendChild(y);
				
				//grade, but as a select
				y = document.createElement('td');
				x = document.createElement('select');
				x.setAttribute('name', 'grade' + n);
				//grades 7-12
				for (let i = 7; i <= 12; i++) {
                    var opt = document.createElement('option');
                    opt.appendChild(document.createTextNode('Grade ' + i));
                    opt.value = i; 
                    x.appendChild(opt);
				}
                y.appendChild(x);
				d.appendChild(y);
                
				
				//first name
				y = document.createElement('td');
				x = document.createElement('input');
				x.setAttribute('name', 'firstname' + n);
				y.appendChild(x);
				d.appendChild(y);
				
				//last name
				y = document.createElement('td');
				x = document.createElement('input');
				x.setAttribute('name', 'lastname' + n);
				y.appendChild(x);
				d.appendChild(y);
				
				//position
				y = document.createElement('td');
				x = document.createElement('select');
				x.setAttribute('name', 'position' + n);
				var i = '<?php echo $i; ?>';
				var pos = JSON.parse('<?php echo json_encode($positions);?>');
				
				for (j = 0; j < i; j++){
					z = document.createElement('option');
					z.setAttribute('value', pos[0]['id']);
					z.innerHTML = pos[0]['name'];
					x.appendChild(z);
				}
				y.appendChild(x);
				d.appendChild(y);
				
				/*
				//remove link
				x = document.createElement('a');
				x.setAttribute('href', 'javascript: remove('+n+');');
				x.innerHTML = ('remove');
				d.appendChild(x);
				*/
				
				container.appendChild(d);
			}
		}
		/*
		function remove(n){
			var container = document.getElementById('container');
			container.removeChild(container.childNodes[n + 1]);
		}*/
		function makeid(length) {
			var result           = '';
			var characters       = 'ABCDEFGHJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789';
			var charactersLength = characters.length;
			for ( var i = 0; i < length; i++ ) {
				result += characters.charAt(Math.floor(Math.random() * charactersLength));
			}
			return result;
		}
		function validate(){
			//alert('hello1');
			var container = document.getElementById('container_new');
			
			try{
			var n = container.childElementCount;
				for (var i = 2; i <= n; i++){
						//alert("i is "+i);
					for (var j = 2; j < 7; j++){
						//alert("j is "+j);
						m = document.querySelector('#container_new>tr:nth-child('+ i +') td:nth-child('+ j +')').children;
						//console.dir(m);
				        //console.log("thing #" + j + " of " + i + " has " + m[0].value);
						if (m[0].value == "" || m[0].value == null ){
							alert('Please fill in all candidate details.');
							return false;
						}
						//else alert("this thing is filled");
					}
				}
			}
			catch (e){
				alert("OH NO: "+e.message + ", was found at el #" + j - 1 + " of row #" + i - 1);
				
			}
			
			if (confirm('Submit this form?')){
				$('.disabled_field').disabled = false;
				/*for (var i = 2; i <= n; i++){
					m = document.querySelector('#container_new>tr:nth-child('+i+') td:nth-child(1) input');
					m.disabled = false;
					m = document.querySelector('#container_new>tr:nth-child('+i+') td:nth-child(2) input');
					m.disabled = false;
				}*/
				return true;
			}
			else return false;
		}
	</script>
</body>
<!-- END: Body-->

</html>