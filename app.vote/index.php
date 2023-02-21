<?php

/***** redirects to index page from any other location *****/

//just in case, added various dependencies and whatnot
session_start();

// Prevents browser from caching user submissions
header("Cache-Control: no-cache, no-store, must-revalidate");

require_once "assets/php/database.php";
include_once "assets/php/libraries/PHPMailer/PHPMailerAutoload.php";
include_once "assets/php/redirect.php";
include_once "assets/php/token_class.php";
include_once "assets/php/account_class.php";
include_once "assets/php/voter_class.php";

redirect("https://app.vote.skule.app/pages/index.php");