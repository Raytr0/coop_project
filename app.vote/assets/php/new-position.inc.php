<?php
session_start();

require_once "database.php";
// require_once "pdo.inc.php"; dumb stuff
include_once "redirect.php";
include_once "token_class.php";
include_once "account_class.php";

$user = new Account();

// Checks if a user is currently logged in 
if ($user -> currentAccount() < 0)
{
    redirect("../../pages/login.php");
}

// Checks if the users account is verified 
else if (!$user -> getAccountInfo("verified"))
{
    redirect("../../pages/confirm-email.php");
}

// Checks if the current account is an admin
else if ($user -> getAccountInfo("type_id") != 3)
{
	redirect("../../pages/election-list.php");
}

if (isset($_POST['new-pos-submit']))
{	
	try
	{
		$insert_position = $db -> prepare("
			INSERT INTO positions (position_name, school_id) VALUES (:position_name, :school_id);
		");

		$insert_position -> execute([
			":position_name" => $_POST["name"],
			":school_id" => $user -> getAccountInfo("school_id"),
		]);
	}

	catch (PDOException $e)
	{
		$_SESSION['error'] = 'PDO: '.$e->getMessage();
		redirect("/pages/election-list.php");
	}
	
	$_SESSION['changesMade'] = 'created a new position';
	redirect("/pages/edit-positions.php");
}

else
{
	if ($_SERVER['HTTP_REFERER']) 
	{
		redirect($_SERVER['HTTP_REFERER']);
	}

	else 
	{
		redirect('/pages/edit-positions.php');
	}
}
?>