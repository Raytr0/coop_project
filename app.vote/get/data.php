<?php
require_once "../assets/php/database.php";
include_once "../assets/php/libraries/PHPMailer/PHPMailerAutoload.php";
include_once "../assets/php/redirect.php";
include_once "../assets/php/token_class.php";
include_once "../assets/php/account_class.php";

// Prevents browser from caching user submissions
header("Cache-Control: no-cache, no-store, must-revalidate");

// Specifying that it will return json format
header("Content-Type: application/json; charset=UTF-8");

$user = new Account();

// Checks if a user is currently logged in 
if ($user -> currentAccount() < 0)
{
    echo json_encode([]);
}

// Checks if the users account is verified 
else if (!$user -> getAccountInfo("verified"))
{
    echo json_encode([]);
}

$user_type = $user -> getAccountInfo("type_id");

/**
 * 
 * 
 * @param array $arr
 * @return array
 */
function formatArray(&$arr)
{
    $formated_arr = [];

    foreach ($arr as $i)
    {
        $date = date("Y-m-d", strtotime($i["date"]));
        
        if ($formated_arr[$date] == null)
        {
            $formated_arr[$date] = 0;
        }

        $formated_arr[$date] += 1;
    }

    return $formated_arr;
}

/**
 * 
 * 
 * @param array $arr
 * @param string $get_school_name
 * @return array
 */
function formatArraySuper(&$arr, &$get_school_name)
{
    $formated_arr = [];

    foreach ($arr as $i)
    {
        $get_school_name -> execute([
            ":school_id" => $i["school_id"],
        ]);

        $school_name = $get_school_name -> fetch(PDO::FETCH_ASSOC)["school_name"];

        $date = date("Y-m-d", strtotime($i["date"]));
        
        if ($formated_arr[$school_name][$date] == null)
        {
            $formated_arr[$school_name][$date] = 0;
        }

        $formated_arr[$school_name][$date] += 1;
    }

    return $formated_arr;
}

// Candidate
if ($user_type == 1)
{
    $get_candidate_id = $db -> prepare("
        SELECT candidate_id FROM candidates WHERE user_id=:user_id LIMIT 1;
    ");
    
    $get_candidate_id -> execute([
        ":user_id" => $user -> getAccountInfo("user_id"),
    ]);

    $candidate_id = $get_candidate_id -> fetch(PDO::FETCH_ASSOC)["candidate_id"];

    $get_page_visits = $db -> prepare("
        SELECT date_visited as date FROM candidate_visitors WHERE candidate_username=:username;
    ");
    $get_page_visits -> execute([
        ":username" => $user -> getAccountInfo("username"),
    ]);

    $visits = formatArray($get_page_visits -> fetchAll(PDO::FETCH_ASSOC));

    $get_election_ids = $db -> prepare("
        SELECT election_id FROM candidate_elections WHERE candidate_id=:candidate_id;
    ");
    $get_election_ids -> execute([
        ":candidate_id" => $candidate_id,
    ]);

    $election_ids = $get_election_ids -> fetchAll(PDO::FETCH_ASSOC);

    $get_elections = $db -> prepare("
        SELECT election_name FROM elections WHERE election_id=:election_id;
    ");

    $elections = [];

    // Gets name of each election
    foreach ($election_ids as $election_id)
    {
        $get_elections -> execute([
            ":election_id" => $election_id["election_id"],
        ]);

        $elections[] = $get_elections -> fetch(PDO::FETCH_ASSOC)["election_name"];
    }

    echo json_encode([
        "type" => "candidate",
        "elections" => $elections,
        "num_visits" => [
            "title" => "Number of Page Visits",
            "data" => $visits,
        ]
    ]);
}

// Voter
else if ($user_type == 2)
{
    echo json_encode([]);
}

// Local admin
else if ($user_type == 3)
{
    $school_id = $user -> getAccountInfo("school_id");

    $get_elections = $db -> prepare("
        SELECT date_created as date FROM elections WHERE school_id=:school_id;
    ");
    $get_elections -> execute([
        ":school_id" => $school_id,
    ]);

    $elections = formatArray($get_elections -> fetchAll(PDO::FETCH_ASSOC));

    $get_users = $db -> prepare("
        SELECT date_created as date FROM users WHERE school_id=:school_id;
    ");
    $get_users -> execute([
        ":school_id" => $school_id,
    ]);

    $users = formatArray($get_users -> fetchAll(PDO::FETCH_ASSOC));

    $get_candidates = $db -> prepare("
        SELECT date_created as date FROM candidates WHERE school_id=:school_id;
    ");
    $get_candidates -> execute([
        ":school_id" => $school_id,
    ]);

    $candidates = formatArray($get_candidates -> fetchAll(PDO::FETCH_ASSOC));

    $get_votes = $db -> prepare("
        SELECT date_voted as date FROM voter_elections WHERE school_id=:school_id;
    ");
    $get_votes -> execute([
        ":school_id" => $school_id,
    ]);

    $votes = formatArray($get_votes -> fetchAll(PDO::FETCH_ASSOC));

    echo json_encode([
        "type" => "local",
        "num_elections" => [
            "title" => "Number of new elections",
            "data" => $elections,
        ],
        "num_users" => [
            "title" => "Number of new users",
            "data" => $users,
        ],
        "num_candidates" => [
            "title" => "Number of new candidates",
            "data" => $candidates,
        ],
        "num_votes" => [
            "title" => "Number of votes",
            "data" => $votes,
        ],
    ]);
}

// Super admin
else if ($user_type == 4)
{
    $get_school_name = $db -> prepare("
        SELECT school_name FROM schools WHERE school_id=:school_id LIMIT 1;
    ");

    $get_elections = $db -> prepare("
        SELECT date_created as date, school_id FROM elections;
    ");
    $get_elections -> execute();

    $elections = formatArraySuper($get_elections -> fetchAll(PDO::FETCH_ASSOC), $get_school_name);

    $get_users = $db -> prepare("
        SELECT date_created as date, school_id FROM users;
    ");
    $get_users -> execute();

    $users = formatArraySuper($get_users -> fetchAll(PDO::FETCH_ASSOC), $get_school_name);

    $get_candidates = $db -> prepare("
        SELECT date_created as date, school_id FROM elections
    ");
    $get_candidates -> execute();

    $candidates = formatArraySuper($get_candidates -> fetchAll(PDO::FETCH_ASSOC), $get_school_name);

    $get_votes = $db -> prepare("
        SELECT date_voted as date, school_id FROM voter_elections;
    ");
    $get_votes -> execute();

    $votes = formatArraySuper($get_votes -> fetchAll(PDO::FETCH_ASSOC), $get_school_name);

    echo json_encode([
        "type" => "super",
        "num_elections" => [
            "title" => "Number of new elections",
            "data" => $elections,
        ],
        "num_users" => [
            "title" => "Number of new users",
            "data" => $users,
        ],
        "num_candidates" => [
            "title" => "Number of new candidates",
            "data" => $candidates,
        ],
        "num_votes" => [
            "title" => "Number of votes",
            "data" => $votes,
        ],
    ]);
}
?>