<?php
$get_visitor = $db -> prepare("
    SELECT * FROM candidate_visitors WHERE date_visited > :date;
"); 

$get_visitor -> execute([
    ":date" => date("Y-m-d H:i:s", time() - 60 * 5),
]);

if (empty($get_visitor -> fetchAll(PDO::FETCH_ASSOC)))
{
    $add_visitor = $db -> prepare("
        INSERT INTO candidate_visitors (user_id, candidate_username) values (:user_id, :username);
    ");

    $add_visitor -> execute([
        ":user_id" => $_SESSION["user_id"],
        ":username" => $_GET["user"] ?? $user -> getAccountInfo("username"),
    ]);
}
?>