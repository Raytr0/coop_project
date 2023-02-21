<?php 

// Starts session just in case
session_start();

//this page is seemingly rendundant, redirect to index.php in order not to have to change every reference to calendar.php
header("Location: index.php");

?>