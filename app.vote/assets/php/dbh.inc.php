<?php

$servername = "localhost";
$dBUsername = "skule_appvoter";
$dBPassword = "w-TWXWhcKbgpCv-J2xti";
$dBName = "skule_voterapp";

$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);
if (!$conn){
	echo "<p>Connection failed </p>".mysqli_connect_error();
	die("Connection failed:".mysqli_connect_error());
}