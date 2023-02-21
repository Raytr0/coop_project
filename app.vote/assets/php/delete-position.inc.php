<?php
session_start();

require_once "database.php";
// require_once "pdo.inc.php"; big stinky
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
else if ($user -> getAccountInfo("type_id") != 3)
{
	redirect("../../pages/election-list.php");
}

if (isset($_POST['pos-id']))
{
	try
	{
		$delete_position = $db -> prepare("
			DELETE FROM positions WHERE position_id=:position_id;
		");
		$delete_position -> execute([
			":position_id" => $_POST['pos-id'],
		]);

		$delete_position = $db -> prepare("
			DELETE FROM position_election WHERE position_id=:position_id;
		");
		$delete_position -> execute([
			":position_id" => $_POST['pos-id'],
		]);

		$delete_position = $db -> prepare("
			DELETE FROM candidate_election WHERE position_id=:position_id;
		");
		$delete_position -> execute([
			":position_id" => $_POST['pos-id'],
		]);

	}

	catch (PDOException $e)
	{
		$_SESSION['error'] = 'PDO: '.$e->getMessage();
		redirect("../../pages/election-list.php");
	}
	
	$_SESSION['changesMade'] = 'deleted a position';
	redirect("../../pages/edit-positions.php");
}

else
{
	if ($_SERVER['HTTP_REFERER']) 
	{
		redirect($_SERVER['HTTP_REFERER']);
	}

	else 
	{
		redirect('../../pages/edit-positions.php');
	}
}