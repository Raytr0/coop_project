<?php
// Start the session
session_start();

// Prevents browser from caching user sumbisions
header("Cache-Control: no-cache, no-store, must-revalidate");

// Includes database 
require_once "../assets/php/database.php";

// Includes dependencies
include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";

// Creates account
$user = new Account();

// Logs out user
$user -> logout();

// Redirects to login
redirect("/pages/login.php");
?>