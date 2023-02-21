<?php
// Start the session
session_start();

// Prevents browser from caching user sumbisions
header("Cache-Control: no-cache, no-store, must-revalidate");

// Specifing that it will return json format
header("Content-Type: application/json; charset=UTF-8");

// Includes database 
require_once "../assets/php/database.php";

// Includes dependencies
include_once "../assets/php/libraries/PHPMailer/PHPMailerAutoload.php";
include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";
include_once "../assets/php/voter_class.php";

// Makes sure that there is a column and value variable
if (!isset($_GET["column"]) || !isset($_GET["value"]))
{
    // Returns nothing
    echo null;
}

else
{
    // Creates object to hold querys 
    $querys = [
        "email" => "SELECT email FROM users WHERE email = :value LIMIT 1;",
        "username" => "SELECT username FROM users WHERE username = :value LIMIT 1;",
    ];

    // Checks if a valid column or value was given
    if ($_GET["column"] != "email" && $_GET["column"] != "username")
    {
        // Returns NULL
        echo null;
    }

    // Gets the data from the database 
    $get_data = $db -> prepare($querys[$_GET["column"]]);

    $get_data -> execute([
        ":value" => $_GET["value"]],
    );

    // Returns json
    echo json_encode(["column" => $_GET["column"], "taken" => !empty($get_data -> fetch(PDO::FETCH_ASSOC))]);
}

?>