<?php //session stuff
		session_start();
		
		//user that is currently logged in
		$_SESSION['user-id'] = 124;
		
		//user whose page we are visiting (applicable to candidate profile)
		$user_id = 124;
		
		//get user type
		//if (!isset($_SESSION['user-type'])){
			require_once "../assets/php/dbh.inc.php";
			$sql = "SELECT type_id FROM users WHERE user_id=?";
			$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)){
				echo '<p>sql error</p>';
				exit();
			}
			else{
				mysqli_stmt_bind_param($stmt, "i", $_SESSION['user-id']);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);

				if ($row = mysqli_fetch_assoc($result)){
					$_SESSION['user-type'] = $row['type_id'];
				}
			}
		//}
			$sql = "SELECT type_id FROM users WHERE user_id=?";
			$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)){
				echo '<p>sql error</p>';
				exit();
			}
			else{
				mysqli_stmt_bind_param($stmt, "i", $user_id);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);

				if ($row = mysqli_fetch_assoc($result)){
					$user_type = $row['type_id'];
				}
			}
		
		//get school
		//if (!isset($_SESSION['school'])){
			require_once "../assets/php/dbh.inc.php";
			$sql = "SELECT school_id FROM users WHERE user_id=?";
			$stmt = mysqli_stmt_init($conn);
			if (!mysqli_stmt_prepare($stmt, $sql)){
				echo '<p>sql error</p>';
				exit();
			}
			else{
				mysqli_stmt_bind_param($stmt, "i", $_SESSION['user-id']);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);

				if ($row = mysqli_fetch_assoc($result)){
					$_SESSION['school'] = $row['school_id'];
				}
			}
		//}
		
		//get profile pic, name
		if ($_SESSION['user-type'] == 1 || $_SESSION['user-type'] == 2){
			//if (!isset($_SESSION['pp']) || !isset($_SESSION['user-name'])){
				require_once "../assets/php/dbh.inc.php";
				$sql = "SELECT profile_picture, first_name, last_name FROM voters WHERE user_id=?";
				$stmt = mysqli_stmt_init($conn);
				if (!mysqli_stmt_prepare($stmt, $sql)){
					echo '<p>sql error</p>';
					exit();
				}
				else{
					mysqli_stmt_bind_param($stmt, "i", $_SESSION['user-id']);
					mysqli_stmt_execute($stmt);
					$result = mysqli_stmt_get_result($stmt);

					if ($row = mysqli_fetch_assoc($result)){
						$_SESSION['pp'] = '../assets/images/profile/'.$_SESSION['user-id'].'.'.$row['profile_picture'];
						if (!file_exists($_SESSION['pp'])){
							$_SESSION['pp'] = '../assets/images/profile/default.png';
						}
						$_SESSION['user-name'] = $row['first_name'].' '.$row['last_name'];
					}
				}
			//}
		}
		else {
			//if (!isset($_SESSION['pp']) || !isset($_SESSION['user-name'])){
				require_once "../assets/php/dbh.inc.php";
				$sql = "SELECT username FROM users WHERE user_id=?";
				$stmt = mysqli_stmt_init($conn);
				if (!mysqli_stmt_prepare($stmt, $sql)){
					echo '<p>sql error</p>';
					exit();
				}
				else{
					mysqli_stmt_bind_param($stmt, "i", $_SESSION['user-id']);
					mysqli_stmt_execute($stmt);
					$result = mysqli_stmt_get_result($stmt);

					if ($row = mysqli_fetch_assoc($result)){
						$_SESSION['user-name'] = $row['username'];
					}
				}
				
				$_SESSION['pp'] = '../assets/images/profile/default.png';
			//}
		}
	?>