<?php

try
{
    // Connects to the database
    $db = new PDO("mysql:host=localhost;dbname=skule_voterapp", "skule_appvoter", "w-TWXWhcKbgpCv-J2xti");

    // Set the PDO error mode to exception
    $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
}
catch (PDOException $error)
{
   /* If there is an error an exception is thrown */
   echo 'Database connection failed.' . $error;
   die();
}
?>