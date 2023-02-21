<?php session_start(); 

require_once "database.php";
include_once "redirect.php";



if (isset($_POST['update-account-submit'])){
	try{
		
		$stm = $db->prepare("UPDATE users SET username = ? WHERE user_id = ? ");

// 		$stm->bindValue(1, $_POST['username']);
// 		$stm->bindValue(2, $_SESSION['user-id']); mother of god it is user_id, not user-id
        
		$stm->execute(array($_POST['username'] ,$_SESSION['user_id']));
		
		$stm = $db->prepare("UPDATE users SET email=? WHERE user_id=?");
		$stm->bindValue(1, $_POST['email']);
		$stm->bindValue(2, $_SESSION['user_id']);
		$stm->execute();
		
		$stm = $db->prepare("UPDATE voters  SET grade = ? WHERE user_id = ? ");
		$stm->execute(array($_POST['grade'] ,$_SESSION['user_id']));
		
		if ($_POST['new-pw']){
			$hashed_pw = password_hash($_POST['new-pw'], PASSWORD_DEFAULT);
			$stm = $db->prepare("UPDATE users SET password=? WHERE user_id=?");
			$stm->bindValue(1, $hashed_pw);
			$stm->bindValue(2, $_SESSION['user_id']);
			$stm->execute();
		}
		
		//not sure why the heck this is here, but it is
		//$db->exec("DELETE FROM elections WHERE election_id=".$_POST['election-id']);
		//$db->exec("DELETE FROM position_election WHERE election_id=".$_POST['election-id']);
	}
	catch (PDOException $e){
		$_SESSION['error'] = 'PDO: '.$e->getMessage();
		redirect("../../pages/profile.php");
	}
	
	//sucessful update; return to candidate page
	$_SESSION['changesMade'] = "account";
	redirect("../../pages/profile.php");
} else 
redirect("../../pages/profile.php");

exit();
?>