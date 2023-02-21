<?php session_start(); 

include "database.php";

// echo $_SESSION['user-id'] . '-';
echo $_SESSION['user_id'] . '_';
if (isset($_POST['update-profile-submit']))
{
	if (isset($_FILES["profile_pic"]))
	{
		try
		{
			$target_dir = "../images/profile/";
			$target_file = $target_dir . basename($_FILES['profile_pic']["name"]);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			$target_file = $target_dir . $_SESSION['user-id'] .'.'. $imageFileType;
			$check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
			$check = getimagesize($_FILES["profile_pic"]["name"]);
			if($check !== true) 
			{
				throw new Exception('File not image' . " " . $_FILES["profile_pic"]["tmp_name"] . " " . 
				    $_FILES['profile_pic']["name"] . " " .
				    $imageFileType . "   " .
				    $target_file . "   " .
				    $target_dir . " " .
				    //  . " " .
				    $target_dir . basename($_FILES['profile_pic']["name"]));
			}

			//file size
			if ($_FILES["profile_pic"]["size"] > 500000) {
				throw new Exception('File too big');
			}
			//file type
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) 
			{
				throw new Exception('Bad file type');
			}

			if (file_exists($target_file)) {}
			
			if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], '../'.$target_file)) {
				$stm = $db->prepare("UPDATE voters SET profile_picture=? WHERE user_id=?");
				$stm->bindValue(1, $imageFileType);
				$stm->bindValue(2, $_SESSION['user-id']);
				$stm->execute();
				
			}

			else 
			{
				throw new Exception("upload error: ".$_FILES["profile_pic"]["error"] ?? "NULL");
			}
		}

		catch (Exception $e)
		{
			$_SESSION['error'] = $e->getMessage();
			header("Location:../../pages/profile.php");
			exit();
		}

		catch (PDOException $e)
		{
			$_SESSION['error'] = $e->getMessage();
			header("Location:../../pages/profile.php");
			exit();
		}
	}

	if (isset($_FILES["banner_pic"]))
	{
		try
		{
			$target_dir = "images/banner/";
			$target_file = $target_dir . basename($_FILES['banner_pic']["name"]);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			$target_file = $target_dir.$_SESSION['user-id'].'.'.$imageFileType;
			$check = getimagesize($_FILES["banner_pic"]["tmp_name"]);

			if($check !== true) 
			{
				throw new Exception('File not image');
			}

			//file size
			if ($_FILES["banner_pic"]["size"] > 500000) 
			{
				throw new Exception('File too big');
			}

			//file type
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) 
			{
				throw new Exception('Bad file type');
			}

			//file exists?
			if (file_exists($target_file)) {}
			
			if (move_uploaded_file($_FILES["banner_pic"]["tmp_name"], '../'.$target_file)) 
			{

				$stm = $db->prepare("UPDATE voters SET profile_picture=? WHERE user_id=?");
				$stm->bindValue(1, $imageFileType);
				$stm->bindValue(2, $_SESSION['user-id']);
				$stm->execute();
			} 

			else 
			{
				throw new Exception("upload error: ".$_FILES["banner_pic"]["error"] ?? "NULL");
			}
		}

		catch (Exception $e)
		{
			$_SESSION['error'] = $e->getMessage();
			header("Location:../../pages/profile.php");
			exit();
		}

		catch (PDOException $e)
		{
			$_SESSION['error'] = $e->getMessage();
			header("Location:../../pages/profile.php");
			exit();
		}
	}

	try
	{
		$stm = $db->prepare("UPDATE candidates SET candidate_facebook=? WHERE user_id=?");
		$stm->bindValue(1, $_POST['facebook']);
		$stm->bindValue(2, $_SESSION['user-id']);
		$stm->execute();
		
		$stm = $db->prepare("UPDATE candidates SET candidate_twitter=? WHERE user_id=?");
		$stm->bindValue(1, $_POST['twitter']);
		$stm->bindValue(2, $_SESSION['user-id']);
		$stm->execute();
		
		$stm = $db->prepare("UPDATE candidates SET candidate_instagram=? WHERE user_id=?");
		$stm->bindValue(1, $_POST['instagram']);
		$stm->bindValue(2, $_SESSION['user-id']);
		$stm->execute();
		
		$stm = $db->prepare("UPDATE candidates SET candidate_bio=? WHERE user_id=?");
		$stm->bindValue(1, $_POST['bio']);
		$stm->bindValue(2, $_SESSION['user-id']);
		$stm->execute();
		
		$stm = $db->prepare("UPDATE voters SET values_beliefs=? WHERE user_id=?");
		$stm->bindValue(1, $_POST['values']);
		$stm->bindValue(2, $_SESSION['user-id']);
		$stm->execute();
	}

	catch (PDOException $e)
	{
		$_SESSION['error'] = "sql: ".$e->getMessage();
		header("Location:../../pages/profile.php");
		exit();
	}

	//sucessful update; return to candidate page
	$_SESSION['changesMade'] = "profile";
	header("Location:../../pages/profile.php");
}

exit();
?>