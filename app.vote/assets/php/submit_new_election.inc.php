<?php
session_start();

require_once "database.php";
// require_once "pdo.inc.php"; dumb as nails
include_once "redirect.php";
include_once "token_class.php";
include_once "account_class.php";

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

// Checks if the current account is an admin
else if ($user -> getAccountInfo("type_id") != 3)
{
	redirect("/pages/election-list.php");
}

if (isset($_POST['new-election-submit']))
{
	//format dates
	date_default_timezone_set('America/Toronto');
	$date = date('Y-m-j g:i A', time());
	
	if (isset($_POST['start-date']) && isset($_POST['start-time']))
	{
		$first_comma = strpos($_POST['start-date'], ',');
		$second_comma = strpos($_POST['start-date'], ',', strpos($_POST['start-date'], ',') + 1);
		$month = substr($_POST['start-date'], 0, $first_comma);
		$day = substr($_POST['start-date'], $first_comma + 2, $second_comma - ($first_comma + 2));
		$year = substr($_POST['start-date'], $second_comma + 2);
		
		$startdatetime = $year.'-'.date("m", strtotime($month)).'-'.$day.' '.$_POST['start-time'];

	}

	else
	{
		echo '???';
	}
	
	if (isset($_POST['end-date']) && isset($_POST['end-time']))
	{

		$first_comma = strpos($_POST['end-date'], ',');
		$second_comma = strpos($_POST['end-date'], ',', strpos($_POST['end-date'], ',') + 1);
		$month = substr($_POST['end-date'], 0, $first_comma);
		$day = substr($_POST['end-date'], $first_comma + 2, $second_comma - ($first_comma + 2));
		$year = substr($_POST['end-date'], $second_comma + 2);
		
		$enddatetime = $year.'-'.date("m", strtotime($month)).'-'.$day.' '.$_POST['end-time'];
		//echo $date.'<br>'.$enddatetime;
	}

	else
	{
		echo '???';
	}
	
	try
	{
		$zero = 0;
		$selector = hash("sha256", bin2hex(random_bytes(32)) . $_POST['name']);
		$date_start = date('Y-m-d H:i:s',strtotime($startdatetime));
		$date_end = date('Y-m-d H:i:s',strtotime($enddatetime));
		$date_now = date('Y-m-d H:i:s',strtotime($date));
		$stm = $db->prepare("
			INSERT INTO elections (school_id, election_name, election_selector, total_votes, num_candidates,
			starting_date, finishing_date, date_created) VALUES (?,?,?,?,?,?,?,?);");
		$stm->bindValue(1, $user -> getAccountInfo("school_id"));
		$stm->bindValue(2, $_POST['name']);
		$stm->bindValue(3, $selector);
		$stm->bindValue(4, $zero);
		$stm->bindValue(5, $zero);
		$stm->bindValue(6, $date_start);
		$stm->bindValue(7, $date_end);
		$stm->bindValue(8, $date_now);
		$stm->execute();
		
		$election_id = $db -> lastInsertId();

		$insert_position = $db -> prepare("
			INSERT INTO position_elections 
			(position_id, election_selector, election_id) 
			VALUES 
			(:position, :election_selector, :election_id);
		");

		foreach ($_POST['positions'] as $pos)
		{
			$insert_position -> execute([
				":position" => $pos, 
				":election_selector" => $selector,
				":election_id" => $election_id,
			]);
		}
	}

	catch (PDOException $e)
	{
		echo $e;
		die();

		$_SESSION['error'] = 'PDO: ' . $e -> getMessage();
	}
	
	$_SESSION['changesMade'] = 'created a new election';
	redirect("/pages/election-list.php");
}