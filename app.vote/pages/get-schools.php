<?php 
session_start();

require_once "../assets/php/database.php";

if ($_SERVER['REQUEST_METHOD'] != "GET"){
    header("Location: index.php");
} else {
    echo "<option disabled selected value='default'>Select a school</option>";
    
    $boardId = $_GET['sboard'];
    
    $stm = $db -> prepare("SELECT school_id FROM schools WHERE school_board_id = ?");
    $stm -> execute(array($boardId));
    
    $stm2 = $db -> prepare("SELECT school_name FROM schools WHERE school_board_id = ?");
    $stm2 -> execute(array($boardId));
    
    //parentheses are necessary, otherwise $school_id becomes equal to 1 in evey case
    while (($school_id = $stm->fetchColumn()) && 
    ($school_name = $stm2->fetchColumn())) {
        echo "<option value='"; 
        echo $school_id; 
        echo "'>";
            echo $school_name; 
        echo "</option>";
    }
}