<?php
session_start();

require_once "database.php";
// require_once "pdo.inc.php";
include_once "redirect.php";
include_once "token_class.php";
include_once "account_class.php";

$user = new Account();

// Checks if a user is currently logged in 
if ($user -> currentAccount() < 0)
{
    redirect("../../pages/login.php", 307);
}

// Checks if the users account is verified 
else if (!$user -> getAccountInfo("verified"))
{
    redirect("../../pages/confirm-email.php", 307);
}

// Checks if the current account is an admin
else if ($user -> getAccountInfo("type_id") < 3)
{
	redirect("../../pages/election-list.php");
}

if (isset($_POST['election-id']))
{
	try {
		$delete_position_election = $db -> prepare("
			DELETE FROM position_elections WHERE election_id=:election_id;
		");
		$delete_position_election -> execute([
			":election_id" => $_POST['election-id'],
		]);
		
		$delete_candidate_election = $db -> prepare("
			DELETE FROM candidate_elections WHERE election_id=:election_id;
		");
		$delete_candidate_election -> execute([
			":election_id" => $_POST['election-id'],
		]);
		
		$delete_voter_election = $db -> prepare("
			DELETE FROM voter_elections WHERE election_id=:election_id;
		");
		$delete_voter_election -> execute([
			":election_id" => $_POST['election-id'],
		]);

		$delete_election = $db -> prepare("
			DELETE FROM elections WHERE election_id=:election_id;
		");

		$delete_election -> execute([
			":election_id" => $_POST['election-id'],
		]);
	    redirect("../../pages/delete-election.php");
	}

	catch (PDOException $e)
	{
		echo "ERROR<br>";
		echo $_SESSION['error'] = 'PDO: '.$e->getMessage();
		die();
	}
	
	$_SESSION['changesMade'] = 'deleted an election';
	redirect("../../pages/election-list.php");
}

else
{
	if ($_SERVER['HTTP_REFERER']) 
	{
		redirect($_SERVER['HTTP_REFERER']);
	}

	else 
	{
		redirect('../../pages/election-list.php');
	}
}