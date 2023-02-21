<?php 
session_start();

date_default_timezone_set('America/Toronto');
$date = date('Y-m-d H:i:s', time());

include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
require_once "../assets/php/database.php";
include_once "../assets/php/account_class.php";
require_once "../assets/php/pdo.inc.php";

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

try
{
    $user_id = $user -> getAccountInfo("user_id");
    $username = $user -> getAccountInfo("username");
    $profile_picture = $user -> getProfilePicture();
    
    //name grade, profile picture, values
    $stm = $pdo->prepare("SELECT * FROM voters WHERE user_id=?");
    $stm->bindValue(1, $user_id);
    $stm->execute();

    if ($row = $stm->fetch(PDO::FETCH_ASSOC))
    {
        $grade = $row['grade'];
        $firstname = $row['first_name'];
        $lastname = $row['last_name'];
        $values = $row['values_beliefs'];
    }
    
    //get bio, social media stuff, school, banner
    $stm = $pdo->prepare("SELECT * FROM candidates WHERE user_id=?");
    $stm->bindValue(1, $user_id);
    $stm->execute();

    if ($row = $stm->fetch(PDO::FETCH_ASSOC))
    {
        $bio = $row['candidate_bio'];
        $candidate_id = $row['candidate_id'];
        $c_facebook = $row['candidate_facebook'];
        $c_twitter = $row['candidate_twitter'];
        $c_instagram = $row['candidate_instagram'];
        
        $stm = $pdo->prepare("SELECT school_name FROM schools WHERE school_id=? LIMIT 1");
        $stm->bindValue(1, $user -> getAccountInfo("school_id"));
        $stm->execute();
        
        $school_name = $$stm -> fetch(PDO::FETCH_ASSOC)['school_name'];
    }

    $banner = "/assets/images/banner/123.jpg";
    
    //get position id and elections
    $stm = $pdo->prepare("SELECT * FROM candidate_elections WHERE candidate_id=?");
    $stm->bindValue(1, $candidate_id);
    $stm->execute();
    $position_id = [];
    $election_id = [];

    while ($row = $stm->fetch(PDO::FETCH_ASSOC))
    {
        $position_id[] = $row['position_id'];
        $election_id[] = $row['election_id'];
    }
    
    //filter elections to find current/upcoming elections (elections were found in the about card)
    $current_election_counter = 0;
    $current_election_names;
    $i = 0;
    
    foreach ($election_id as $value)
    {
        $stm = $pdo->prepare("SELECT * FROM elections WHERE election_id=?");
        $stm->bindValue(1, $value);
        $stm->execute();

        if ($row = $stm->fetch(PDO::FETCH_ASSOC)){
            //sort elections
            if ($row['finishing_date'] > $date){
                $current_elections_id[$current_election_counter] = $value;
                $current_election_names[$current_election_counter] = $row['election_name'];
                $current_election_counter++;
            }
        }
        $i++;
    }

    $current_positions_id = [];

    if (isset($current_elections_id))
    {
        $i = 0;

        //get position ids for current elections
        foreach ($current_elections_id as $value){
            $stm = $pdo->prepare("SELECT * FROM candidate_election WHERE candidate_id=? AND election_id=?");
            $stm->bindValue(1, $candidate_id);
            $stm->bindValue(2, $value);
            $stm->execute();
            
            $current_positions_id[] = $stm->fetch(PDO::FETCH_ASSOC)['position_id'];
        }
    }

    $i = 0;
    //get position names
    foreach ($current_positions_id as $value){
        $sql = "SELECT * FROM positions WHERE position_id=?";
        $stm = $pdo->prepare("SELECT * FROM positions WHERE position_id=?");
        $stm->bindValue(1, $value);
        $stm->execute();
        while ($row = $stm->fetch(PDO::FETCH_ASSOC)){
                $current_positions[$i] = $row['position_name'];
        }
    }
}

catch (PDOException $e)
{
    echo "PDO error: ".$e->getMessage();
    exit();
}

catch (Exception $e)
{
    echo $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <!-- - var description  = ""-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Profile</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/semi-dark-layout.css">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/pages/users.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/profile.css">
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
                            <h2 class="content-header-title float-left mb-0">Profile</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <!--<li class="breadcrumb-item"><a href="#">Pages</a>
                                    </li>-->
                                    <li class="breadcrumb-item active">Profile
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
				<!-- Purple settings gear above profile background picture
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="#">Chat</a>
								<a class="dropdown-item" href="#">Email</a>
								<a class="dropdown-item" href="#">Calendar</a>
							</div>
                        </div>
                    </div>
                </div>-->
            </div>
            <div class="content-body">
                <div id="user-profile">
                    <div class="">
                        <div class="profile-header mb-2">
                            <div class="relative">
                                <div class="cover-container" style="max-height: 70vh; overflow: hidden">
                                    <img class="img-fluid bg-cover rounded-0 w-100 banner-picture" src=<?=$banner?> alt="User Banner Image">
                                </div>
                                <div class="profile-img-container">
                                    <img src="<?=$profile_picture; ?>"
                                        class="rounded-circle img-border box-shadow-1 profile-picture" alt="Card image" id="profilepic">
                                </div>
                            </div>
                        </div>
                    </div>
					
					<!--profile updated alert-->
					<?php if (isset($_SESSION['changesMade']) && ($_SESSION['changesMade'] == 'account' || $_SESSION['changesMade'] == 'profile')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <h4 class="alert-heading">Success</h4>
                            <p class="mb-0">Your <?=$_SESSION['changesMade']?> has been updated successfully.</p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="feather icon-x-circle"></i></span>
                            </button>
                        </div>
                        <?php unset($_SESSION['changesMade'])?>
                    <?php endif; ?>

					<?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h4 class="alert-heading">Error</h4>
                            <p class="mb-0">Error: <?=$_SESSION['error']?></p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="feather icon-x-circle"></i></span>
                            </button>
                        </div>
                        <?php unset($_SESSION['error'])?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="content-body">
                <section id="profile-info">
                    <div>
                        <div class="card">
                            <div class="card-header">
                                <h4>About</h4><a href="javascript:cancel_edit_profile();" style="display: none" id="edit_cancel_link">cancel</a>
                                <!--<i class="feather icon-more-horizontal cursor-pointer"></i>-->
                            </div>
                            <div class="card-body" style="display: flex; flex-direction: column">
                                <div style="display: flex; flex-direction: row" id="about_display">
                                    <div style="flex: 1">
                                        <div class="mt-1"><h6 class="mb-0">Username:</h6></div>
                                        <p><?=$user -> getAccountInfo("username")?></p>
                                        <div class="mt-1"><h6 class="mb-0">Name:</h6></div>
                                        <p><?=$user -> getAccountInfo("first_name")." ".$user -> getAccountInfo("last_name")?></p>
                                        <div class="mt-1"><h6 class="mb-0">Grade:</h6></div>
                                        <p><?=$grade?></p>
                                        <br>
                                        <?php if ($c_facebook): ?>
                                            <a href="'
                                            <?="https://www.facebook.com/".$c_facebook?>
                                            '" target="_blank"><button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-facebook"></i></button></a>';
                                        <?php endif; ?>

                                        <? if ($c_twitter): ?>
                                            <a href="'
                                            <?="https://twitter.com/".$c_twitter?>
                                            '" target="_blank"><button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-twitter"></i></button></a>';
                                        <?php endif; ?>

                                        <?php if ($c_instagram): ?>
                                            <a href="'
                                            <?="https://www.instagram.com/".$c_instagram;?>
                                            '" target="_blank"><button type="button" class="btn btn-sm btn-icon btn-primary p-25"><i class="feather icon-instagram"></i></button></a>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="flex: 2">
                                        <?php if ($values): ?>
                                            <div class="mt-1"><h6 class="mb-0">Values and Beliefs:</h6></div>';
                                            <p><?=$values?></p>
                                        <?php endif; ?>

                                        <?php if ($bio): ?>
                                            <div class="mt-1"><h6 class="mb-0">Bio:</h6></div>';
                                            <p><?=$bio?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                    
                                <form action="../assets/php/submit_edited_profile.inc.php" method="POST" id="about_edit" style="display: none" enctype="multipart/form-data"> <!--class="edit_profile"-->
                                    <p>Edit your username in account settings.</p>
                                    <label for="basicInput">Profile Picture</label>
                                    <br>
                                    <input type="file" id="profile_picker" onchange="profile_pic_change()">
                                    <br><br>
                                    <label for="basicInput">Banner Picture</label>
                                    <br>
                                    <input type="file" id="banner_picker" onchange="banner_pic_change()">
                                    <br><br>
                                    <label for="basicInput">First Name</label>
                                    <input type="text" name="firstname" class="form-control" id="basicInput" placeholder="Enter first name" value=<?=$user -> getAccountInfo("first_name")?> disabled>
                                    <br>
                                    <label for="basicInput">Last Name</label>
                                    <input type="text" name="lastname" class="form-control" id="basicInput" placeholder="Enter last name" value=<?=$$user -> getAccountInfo("last_name")?> disabled>
                                    <br>
                                    <label for="basicInput">Grade</label>
                                    <input type="text" name="grade" class="form-control" id="basicInput" placeholder="Enter grade" value=<?=$grade?> disabled>
                                    <br>
                                    <label for="basicInput">Facebook Username</label>
                                    <input type="text" name="facebook" class="form-control" id="basicInput" placeholder="Enter Facebook username" value=<?=$c_facebook?>>
                                    <br>
                                    <label for="basicInput">Twitter Username</label>
                                    <input type="text" name="twitter" class="form-control" id="basicInput" placeholder="Enter Twitter username" value=<?=$c_twitter?>>
                                    <br>
                                    <label for="basicInput">Instagram Username</label>
                                    <input type="text" name="instagram" class="form-control" id="basicInput" placeholder="Enter Instagram username" value=<?=$c_instagram?>>
                                    <br>
                                    <label for="basicInput">Values and Beliefs</label>
                                    <textarea type="text" name="values" class="form-control" id="basicInput" placeholder="Enter values" style="height: 5em"><?=$values?></textarea>
                                    <br>
                                    <label for="basicInput">Bio</label>
                                    <textarea type="text" name="bio" class="form-control" id="basicInput" placeholder="Enter bio" style="height: 10em"><?=$bio?></textarea>
                                    <br>
                                    
                                    <div style="display: flex; align-items: center; justify-content: flex-start">
                                        <div>
                                            <button type="submit" name="update-profile-submit" class="btn btn-primary mr-1 mb-1" style="width: 100px">Save</button>
                                        </div>
                                        <a href="javascript:cancel_edit_profile();" style="display: none" id="edit_cancel_link2">cancel</a>
                                    </div>
                                </form>
                                <div class="mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4>Current Elections</h4>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0">School:</h6><?=$school_name?>
                                
                            <div class="mt-1"><h6 class="mb-0">Elections:</h6>
                            <?php if ($current_election_counter == 0) echo "This candidate is not participating in any upcoming elections."; ?>
                            
                            <?php for ($i = 0; $i < $current_election_counter; $i++): ?>
                                <?=$current_election_names[$i]?><br>Running for: 
                                <?=$current_positions[$i]?><br><br>
                                </div>
                                
                            <?php endfor; ?>
                        </div>
                    </div>
                </section>
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
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <script src="/app-assets/js/scripts/components.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/js/scripts/pages/user-profile.js"></script>
    <!-- END: Page JS-->

	<script>
		if (<?php echo $_SESSION['user-id']?> == <?php echo $user_id?>)
			document.getElementById("edit_buttons").style = "display: block";
        
		function profile_pic_change(){
			//alert("profile pic has changed");
			if (document.getElementById("profile_picker").files.length > 0){
				document.getElementById("profile_picker").setAttribute("name", "profile_pic");
			}
			else document.getElementById("profile_picker").setAttribute("name", "");
		}
		
		function banner_pic_change(){
			//alert("banner has changed");
			if (document.getElementById("banner_picker").files.length > 0){
				document.getElementById("banner_picker").setAttribute("name", "banner_pic");
				//alert("banner_pic name is set!!");
			}
			else document.getElementById("banner_picker").setAttribute("name", "");
		}
	
		function start_edit_profile(){
			if (<?php echo $_SESSION['user-id']?> == <?php echo $user_id?>){
				document.getElementById("edit_profile_button").style = "display: none";
				document.getElementById("account_settings_button").style = "display: none";
				document.getElementById("about_display").style = "display: none";
				document.getElementById("edit_cancel_link").style = "display: block";
				document.getElementById("about_edit").style = "display: block";
				document.getElementById("edit_cancel_link2").style = "display: inline";
			}
			else alert("You do not have permission to edit this profile.");
		}
		function cancel_edit_profile(){
				document.getElementById("edit_profile_button").style = "display: inline";
				document.getElementById("account_settings_button").style = "display: inline";
				document.getElementById("about_display").style = "display: flex";
				document.getElementById("edit_cancel_link").style = "display: none";
				document.getElementById("about_edit").style = "display: none";
				document.getElementById("edit_cancel_link2").style = "display: none";
		}
		function to_account_settings(){
			if (<?php echo $_SESSION['user-id']?> == <?php echo $user_id?>){
				location.href = "account-settings.php";
			}
			else alert("You do not have permission to edit this account.");
		}
	</script>

</body>
<!-- END: Body-->

</html>