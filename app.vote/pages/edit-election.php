<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<?php
	if (!isset($_POST['edit-election'])) header("Location: election-list.php");
?>
<head>
	<?php //session stuff
		include 'templates/test.php';
	?>
    <title>Edit Election</title>
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
	<link rel="stylesheet" type="text/css" href="../app-assets/css/pages/users.css">
	<!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/table.css">
    <!-- END: Custom CSS-->

	<?php
		date_default_timezone_set('America/Toronto');
		$date = date('Y-m-d H:i:s', time());
		require_once "../assets/php/pdo.inc.php";
		
		try{
			$stm = $pdo->prepare("SELECT * FROM elections WHERE election_id=?");
			$stm->bindValue(1, $_POST['election-id']);
			$stm->execute();
			if ($row = $stm->fetch(PDO::FETCH_ASSOC)){
				$election['id'] = $row['election_id'];
				$election['school-id'] = $row['school_id'];
				$election['name'] = $row['election_name'];
				$election['votes'] = $row['total_votes'];
				$election['candidates'] = $row['num_candidates'];
				$election['start-full-date'] = $row['starting_date'];
				$election['end-full-date'] = $row['finishing_date'];
			}
			else throw new Exception('no election found');
			
			$stm = $pdo->prepare("SELECT school_name FROM schools WHERE school_id=?");
			$stm->bindValue(1, $election['school-id']);
			$stm->execute();
			if ($row = $stm->fetch(PDO::FETCH_ASSOC)){
				$election['school'] = $row['school_name'];
			}
			
			
			//convert datetime values to legit stuff
			$d = date_create($election['start-full-date']);
			$election['start-date'] = date_format($d, 'F,j,Y');
			$d = date_create($election['end-full-date']);
			$election['end-date'] = date_format($d, 'F,j,Y');
			
			$d = date_create($election['start-full-date']);
			$election['start-time'] = date_format($d, 'g:iA');
			$d = date_create($election['end-full-date']);
			$election['end-time'] = date_format($d, 'g:iA');
			
			//get all positions at this school --> $s is the variable
			$stm = $pdo->prepare("SELECT position_id FROM positions WHERE school_id=?");
			$stm->bindValue(1, $_SESSION['school']);
			$stm->execute();
			$s = 0;
			while ($row = $stm->fetch(PDO::FETCH_ASSOC)){
				$school_pos_id[$s] = $row['position_id'];
				$school_pos_selected[$s] = false;
				$s++;
			}
			
			//get all position names
			for ($j = 0; $j < $s; $j++){
				$stm = $pdo->prepare("SELECT position_name FROM positions WHERE position_id=?");
				$stm->bindValue(1, $school_pos_id[$j]);
				$stm->execute();
				while ($row = $stm->fetch(PDO::FETCH_ASSOC)){
					$school_pos_name[$j] = $row['position_name'];
				}
			}
			
			//get all positions in this election ($i)
			$stm = $pdo->prepare("SELECT position_id FROM position_election WHERE election_id=?");
			$stm->bindValue(1, $election['id']);
			$stm->execute();
			$i = 0;
			while ($row = $stm->fetch(PDO::FETCH_ASSOC)){
				$position[$i] = $row['position_id'];
				for ($c = 0; $position[$i] != $school_pos_id[$c]; $c++);
				$school_pos_selected[$c] = true;
				$i++;
			}
			
			//position names for positions in election
			for ($j = 0; $j < $i; $j++){
				$stm = $pdo->prepare("SELECT position_name FROM positions WHERE position_id=?");
				$stm->bindValue(1, $position[$j]);
				$stm->execute();
				while ($row = $stm->fetch(PDO::FETCH_ASSOC)){
					$position_name[$j] = $row['position_name'];
				}
			}
			
			//find all candidates for each election
			for ($j = 0; $j < $i; $j++){
				$stm = $pdo->prepare("SELECT candidate_id FROM candidate_election WHERE election_id=? AND position_id=?");
				$stm->bindValue(1, $election['id']);
				$stm->bindValue(2, $position[$j]);
				$stm->execute();
				$numcandidates = 0;
				while ($row = $stm->fetch(PDO::FETCH_ASSOC)){
					$stm2 = $pdo->prepare("SELECT voter_id FROM candidates WHERE candidate_id=?");
					$stm2->bindValue(1, $row['candidate_id']);
					$stm2->execute();
					if ($row2 = $stm2->fetch(PDO::FETCH_ASSOC)){
						$stm3 = $pdo->prepare("SELECT * FROM voters WHERE voter_id=?");
						$stm3->bindValue(1, $row2['voter_id']);
						$stm3->execute();
						if ($row3 = $stm3->fetch(PDO::FETCH_ASSOC)){
							$candidate[$j]['name'][$numcandidates] = $row3['first_name'].' '.$row3['last_name'];
							$candidate[$j]['id'][$numcandidates] = $row['candidate_id'];
						}
						else{
							echo 'no candidate with this id';
							die;
						}
					}
					$numcandidates++;
				}
				if ($numcandidates == 0) $candidate[$j]['name'][0] = null;
			}
			
			//all candidates in the school
			$sql = "SELECT candidate_id, voter_id FROM candidates WHERE school_id=".$_SESSION['school'];
			$sql_prepare = $pdo->prepare($sql);
			$resultid = $pdo->query($sql);

			$x = 0;
			while ($row = $resultid->fetch(PDO::FETCH_ASSOC)){
				$candidates[$x]['c_id'] = $row['candidate_id'];
				$candidates[$x]['v_id'] = $row['voter_id'];
				$stm = $pdo->query("SELECT first_name, last_name FROM voters WHERE voter_id=".$row['voter_id']);
				$resultname = $stm->fetch();
				$candidates[$x]['name'] = $resultname['first_name'].' '.$resultname['last_name'];
				$x++;
			}
		}
		catch (PDOException $e){
			$_SESSION['error'] = 'PDO: '.$e->getMessage();
			header("Location:../../pages/election_list.php");
		}
		catch (Exception $e){
			$_SESSION['error'] = 'error: '.$e->getMessage();
			header("Location:../../pages/election_list.php");
		}
		
		function findNextIndex($arr){
			for ($next = 0; $arr[$next]; $next++);
			return $next;
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
                            <h2 class="content-header-title float-left mb-0">Edit Election</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="election-list.php">Election List</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Election
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
					<form action="../assets/php/save-election.inc.php" method="post" onsubmit="return confirmation();">
						<div class="card-header" style="display: flex; justify-content: space-between;">
							<a href="election-details.php?election=<?php echo $_POST['election-id'] ?>">Cancel</a>
						</div>
						<div class="card-content">
							<div class="card-body" style="display: flex; justify-content: space-around;">
								<div style="display: inline-block; width: 40%">
									<?php
										if ($election['end-full-date'] < $date) echo '<p style="color: red">This election is in the past.</p>';
									?>
									<label for="basicInput">Election Name</label>
									<input style="width: 100%" type="text" name="name" class="form-control" id="basicInput" placeholder="Election Name" value="<?php echo $election['name'];?>" style="width: 500px">
									
									<label for="basicInput">Start Date</label>
									<input type='text' class="form-control format-picker" name="start-date" id="startdate" value="<?php echo $election['start-date'];?>"/>
									<label for="basicInput">Start Time</label>
									<input type='text' class="form-control pickatime" name="start-time" id="starttime" value="<?php echo $election['start-time'];?>"/>
									
									<label for="basicInput">End Date</label>
									<input type='text' class="form-control format-picker" name="end-date" id="enddate" value="<?php echo $election['end-date'];?>"/>
									<label for="basicInput">End Time</label>
									<input type='text' class="form-control pickatime" name="end-time" id="endtime" value="<?php echo $election['end-time'];?>"/>
									
									<br><br>
									<button type="submit" name="save-election" class="btn btn-primary" style="width: 150">Save</button>
								</div>
								<div style="display: inline-block; width: 40%">
									<div style="display: flex; justify-content: space-between;">
										<label for="basicInput">Positions</label>
										<a href="edit-positions.php">Edit positions</a>
									</div>
									<select class="select2 form-control" multiple="multiple" name="positions[]" id="pos">
										<?php
											for ($j = 0; $j < $s; $j++){
												echo '<option value='.$school_pos_id[$j];
												if ($school_pos_selected[$j]) echo ' selected';
												echo '>'.$school_pos_name[$j].'</option>';
											}
										?>
									</select>
									<p>Note: deleting a position will not delete a candidate account.</p>
									
									<table style="width: 100%">
										<thead>
											<tr>
												<th>Candidate</th>
												<th>Position</th>
											</tr>
										</thead>
										<tbody id="existing_candidate_tbody">
											<?php
												//this will keep track of the entries in this table
												for ($q = 0; $q < 50; $q++)
													$table_entries[$q] = false;
												for ($j = 0; $j < $i; $j++){
													foreach ($candidate[$j]['name'] as $key=>$name){
														if (isset($candidate[$j]['id'])){
															$next = findNextIndex($table_entries);
															echo '<tr>';
																echo '<td><input readOnly value="'.$name.'"></input></td>';
																echo '<input name="existing_candidate'.$next.'" value="'.$candidate[$j]['id'][$key].'" style="display: none"></input>';
																echo '<td>';
																	echo '<select name="select_pos'.$next.'">';
																		echo '<option value="-1">Remove from election</option>';
																		for ($k = 0; $k < $i; $k++){
																			echo '<option value='.$position[$k];
																			if ($k == $j) echo ' selected';
																			echo '>'.$position_name[$k].'</option>';
																		}
																	echo '</select>';
																echo '</td>';
															echo '</tr>';
															$table_entries[$next] = true;
															$candidate_taken[$candidate[$j]['id'][$key]] = true;
														}
													}
												}
											?>
										</tbody>
									</table>
									<br>
									<a href="javascript: addExistingCandidate();">Add an existing candidate: </a>
									<select id="selectExisting">
										<?php
											for ($j = 0; $j < $x; $j++){
												echo '<option value='.$candidates[$j]['c_id'];
												if (isset($candidate_taken[$candidates[$j]['c_id']]) && $candidate_taken[$candidates[$j]['c_id']]) echo ' disabled';
												echo '>'.$candidates[$j]['name'].'</option>';
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<input value="<?php echo $_POST['election-id'] ?>" style="display: none" name="election-id"></input>
					</form>
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
	<script src="../app-assets/js/scripts/pages/user-profile.js"></script>
    <script src="../app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js"></script>
    <script src="../app-assets/js/scripts/forms/select/form-select2.js"></script>
	<!-- END: Page JS-->
	
	
	<script>
		function confirmation(){
			return confirm("Save these changes?");
		}
		function addExistingCandidate(){
			if ($('#selectExisting option:selected').attr('disabled')){
				alert('This candidate has already been selected.');
				return false;
			}
			if (confirm('Add existing candidate?')){
				try{
					var table_entries = JSON.parse('<?php echo json_encode($table_entries); ?>');
					var container = document.getElementById('existing_candidate_tbody');
					
					var candidate_id = document.getElementById('selectExisting').value;
					var candidate_name = $('#selectExisting option:selected').text();
					
					var next;
					for (next = 0; table_entries[next]; next++);
					
					var tr = document.createElement('tr');
					
					td = document.createElement('td');
					x = document.createElement('input');
					x.readOnly = true;
					x.setAttribute('value', candidate_name);
					td.appendChild(x);
					tr.appendChild(td);
					
					thing = document.createElement('input');
					thing.setAttribute('name', 'existing_candidate'+next);
					thing.setAttribute('value', candidate_id);
					thing.setAttribute('style', 'display: none');
					tr.appendChild(thing);
					
					td = document.createElement('td');
					x = document.createElement('select');
					x.setAttribute('name', 'select_pos' + next);
					var i = '<?php echo $i; ?>';
					var pos_id = JSON.parse('<?php echo json_encode($position);?>');
					var pos_name = JSON.parse('<?php echo json_encode($position_name);?>');
					z = document.createElement('option');
					z.setAttribute('value', -1);
					z.innerHTML = "Remove from election";
					x.appendChild(z);
					for (j = 0; j < i; j++){
						z = document.createElement('option');
						z.setAttribute('value', pos_id[j]);
						z.innerHTML = pos_name[j];
						if (j == 0) z.selected = true;
						x.appendChild(z);
					}
					td.appendChild(x);
					tr.appendChild(td);
					
					container.appendChild(tr);
					$('#selectExisting option:selected').attr('disabled', 'diabled');
				}
				catch (e){
					
					alert(e.message);
				}
			}
		}
	</script>
</body>
<!-- END: Body-->

</html>