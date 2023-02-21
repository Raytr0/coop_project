<?php
session_start();
require_once "database.php";

if (!(isset($_POST['vote-submit']))) {
    header ('Location: ../../pages/candidate-profile.php'); 
}

$votes = 0;

date_default_timezone_set('America/Toronto');
$date = date('Y-m-d G:i:s', time());

$stm = $db->prepare("SELECT voter_id FROM voters WHERE user_id = ?");
$stm -> execute(array($_SESSION['user_id']));
$result = $stm->fetch();
$voter_id = $result[0];

//delete duplicate votes (same id, same election, same position)
//unlikely to happen but you can never be too cautious
$stm = $db->prepare("DELETE FROM voter_elections WHERE voter_id = ? AND election_id = ? AND position_id = ? ");
if($stm->execute(array(
    $voter_id, 
    $_POST['election-id'], 
    $_POST['position-id']
    ))){
    $votes--;
}

$stm = $db->prepare('INSERT INTO voter_elections (election_id, voter_id, candidate_id, position_id, school_id, date_voted) VALUES (?,?,?,?,?,?);');
$stm->bindValue(1, $_POST['election-id']);
$stm->bindValue(2, $voter_id);
$stm->bindValue(3, $_POST['vote']);
$stm->bindValue(4, $_POST['position-id']);
$stm->bindValue(5, $_SESSION['school_id']);
$stm->bindValue(6, $date);
$stm->execute();


//get number of votes, if they exist
//what kinda silly billy tried to get total votes from voters??
//let's just ignore the lack of a try catch as well
//$stm = $db->prepare("SELECT total_votes FROM voters WHERE election_id = ? ");
$stm = $db->prepare("SELECT total_votes FROM elections WHERE election_id = ? ");
$stm -> execute(array($_POST['election-id']));
$result = $stm->fetch(PDO::FETCH_ASSOC);

if ($result[total_votes] != null){
    $votes += $result[total_votes];
} 
$votes++;

//update num of votes
$stm = $db->prepare('UPDATE elections SET total_votes = ?  WHERE election_id = ? ');
$stm->execute(array($votes, $_POST[election-id]));

header("Location: ../../pages/vote-home.php");
