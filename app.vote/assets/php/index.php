<?php

/***** redirects to index page from any other location *****/

//just in case, added various dependencies and whatnot
session_start();

// Prevents browser from caching user submissions
header("Cache-Control: no-cache, no-store, must-revalidate");

require_once "database.php";
include_once "libraries/PHPMailer/PHPMailerAutoload.php";
include_once "redirect.php";
include_once "token_class.php";
include_once "account_class.php";
include_once "voter_class.php";

redirect("https://app.vote.skule.app/pages/index.php");