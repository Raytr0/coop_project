<?php
// Start the session
session_start();

// Prevents browser from caching user sumbisions
header("Cache-Control: no-cache, no-store, must-revalidate");

// Specifing that it will return json format
header("Content-Type: application/json; charset=UTF-8");

// Defines constant to know if a file is included
define("INCLUDE", true);

// Includes database 
require_once "../assets/php/database.php";

// Includes dependencies
include_once "../assets/php/libraries/PHPMailer/PHPMailerAutoload.php";
include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";
include_once "../assets/php/voter_class.php";

// Creates new user
$user = new Account();

// Checks if a user is currently logged in 
if ($user -> currentAccount() < 0)
{
    echo null;
}

// Checks if the users account is verified 
else if (!$user -> getAccountInfo("verified"))
{
    echo null;
}

// Checks if the months and year variables are not set
else if (!isset($_GET["start"]) && !isset($_GET["end"]))
{
    echo null;
}

else
{
    // Gets elections
    $get_elections = $db -> prepare("
        SELECT * FROM elections WHERE :first_date <= starting_date AND starting_date <= :last_date AND school_id=:school_id; 
    ");

    $get_elections -> execute([
        ":first_date" => $_GET["start"],
        ":last_date" => $_GET["end"],
        ":school_id" => $user -> getAccountInfo("school_id"),
    ]);



    // Returns json elections
    echo json_encode($get_elections -> fetchAll(PDO::FETCH_ASSOC));
}
?>