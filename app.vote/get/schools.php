<?php
// Includes database 
require_once "../assets/php/database.php";

// Prevents browser from caching user sumbisions
header("Cache-Control: no-cache, no-store, must-revalidate");

// Specifing that it will return json format
header("Content-Type: application/json; charset=UTF-8");

// Makes sure that there is a school_board variable in the url
if (!isset($_GET["school_board"]))
{
    // Returns null
    echo null;
}

else
{
    // Gets all schools in the school board
    $get_schools = $db -> prepare("
        SELECT school_name, school_id FROM schools WHERE school_board_id=:school_board_id
    ");

    $get_schools -> execute([
        ":school_board_id" => urldecode($_GET["school_board"]),
    ]);

    $schools = $get_schools -> fetchAll(PDO::FETCH_ASSOC);

    // Returns json schools
    echo json_encode($schools);
}
?>