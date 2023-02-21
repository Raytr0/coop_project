<?php
session_start();
require_once "database.php";

if (isset($_POST['candidates-submit'])){
	
	//add new candidates
	$i = 1;
	//if (!isset($_POST['username1'])) echo "there's no username";
	while (isset($_POST['username'.$i])){
// 		echo '<br>' . $_POST['username'.$i].'<br>';
// 		echo $_POST['password'.$i].'<br>';
// 		echo $_POST['grade'.$i].'<br>';
// 		echo $_POST['firstname'.$i].'<br>';
// 		echo $_POST['lastname'.$i].'<br><br>';
		try{
			$one = 1;
			date_default_timezone_set('America/Toronto');
			$date = date('Y-m-j G:i:s', time());
			$hashed_pw = password_hash($_POST['password'.$i], PASSWORD_DEFAULT);
//     		echo $_POST['password'.$i].'<br>';
// 			echo $hashed_pw . '<br><br>';
	
// 			echo "u0 stuff <br><br>";
			
			$stm = $db->prepare("SELECT user_id FROM users WHERE username = ?");
			$stm -> execute(array($_POST['username'.$i]));
			
// 			echo "u0.1 stuff <br><br>";
            
			if ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
    	        //if it is refreshed or reloaded for whatever reason, continue and ignore the duplicate
			    if ($row[user_id] != 0){
			        continue;
			    }
			    
			    //if you need to delete anything
    // 			$stm = $db->prepare("SELECT * FROM candidates WHERE user_id = ?");
    // 			$stm -> execute(array($row[user_id]));
    // 			$can_id = $stm -> fetch();
    			
    // 			$stm = $db->prepare("DELETE FROM candidate_elections WHERE candidate_id = ?");
    // 			$stm -> execute(array($can_id[0]));
    			
    // 			$stm = $db->prepare("DELETE FROM candidates WHERE user_id = ?");
    // 			$stm -> execute(array($row[user_id]));
    			
			 //   echo "<br> u0.3 stuff <br>";
    	        
    // 			$stm = $db->prepare("DELETE FROM voters WHERE user_id = ?");
    // 			$stm -> execute(array($row[user_id]));
    			
			 //   echo "<br> u0.4 stuff <br>";
			    
    // 			$stm = $db->prepare("DELETE FROM users WHERE user_id = ?");
    // 			$stm -> execute(array($row[user_id]));
			    
			 //   echo "<br> u0.5 stuff <br>";
            }
			
// 			echo "<br> u1 stuff <br>";
			$stm = $db->prepare("INSERT INTO users (school_id, type_id, username, password, date_created, first_name, last_name) VALUES (?,?,?,?,?,?,?);");
			$stm->bindValue(1, $_SESSION['school_id']);
			$stm->bindValue(2, $one);
			$stm->bindValue(3, $_POST['username'.$i]);
			$stm->bindValue(4, $hashed_pw);
			$stm->bindValue(5, $date);
			$stm->bindValue(6, $_POST['firstname'.$i]);
			$stm->bindValue(7, $_POST['lastname'.$i]);
			$stm->execute();

// 			echo "<BR> u2 stuff <BR> ";
			$stm = $db->prepare('SELECT user_id FROM users WHERE password=?');
			$stm -> execute(array($hashed_pw));
			$user_id = $stm->fetch();
			
			
// 			echo "<BR> v1 stuff <BR> ";
			$stm = $db->prepare("INSERT INTO voters (user_id, school_id, first_name, last_name, grade) VALUES (?,?,?,?,?);");
			$stm->bindValue(1, $user_id[0]);
			$stm->bindValue(2, $_SESSION['school_id']);
			$stm->bindValue(3, $_POST['firstname'.$i]);
			$stm->bindValue(4, $_POST['lastname'.$i]);
			$stm->bindValue(5, $_POST['grade'.$i]);
			$stm->execute();
			
// 			echo "<BR>v2 stuff <BR> ";
			$stm = $db->prepare("SELECT voter_id FROM voters WHERE user_id=?");
			$stm -> execute(array($user_id[0]));
			$voter_id = $stm->fetch();
			
// 			echo "<BR> c1 stuff <BR> ";
			$stm = $db->prepare("INSERT INTO candidates (voter_id, user_id, school_id, grade) VALUES (?,?,?,?);");
			$stm->bindValue(1, $voter_id[0]);
			$stm->bindValue(2, $user_id[0]);
			$stm->bindValue(3, $_SESSION['school_id']);
			$stm->bindValue(4, $_POST['grade'.$i]);
			$stm->execute();
			
// 			echo "<BR> c2 stuff <BR>";
			$stm = $db->prepare("SELECT candidate_id FROM candidates WHERE user_id=?");
			$stm -> execute(array($user_id[0]));
			$candidate_id = $stm->fetch();
			
// 			echo "<BR> c3 stuff <BR>";
			$stm = $db->prepare("INSERT INTO candidate_elections (candidate_id, election_id, position_id, school_id) VALUES (?,?,?,?);");
			$stm->bindValue(1, $candidate_id[0]);
			$stm->bindValue(2, $_SESSION['election_id']);
			$stm->bindValue(3, $_POST['position'.$i]);
			$stm->bindValue(4, $_SESSION['school_id']);
			$stm->execute();
			
// 			echo "<BR> e1 stuff <BR> ";
			$stm = $db->prepare('SELECT election_selector FROM elections WHERE election_id = ?');
			$stm->execute(array($_SESSION['election_id']));
			$election = $stm->fetch();
			
// 			echo "<BR> ALL DONE :) <BR> ";
		}
		catch (PDOException $e){
			echo $e->getMessage();
		}
		
		$i++;
	}
	
	$i--;
	
	//add existing candidates
	$j = 1;
	while (isset($_POST['existing_candidate'.$j])){
		try{
			date_default_timezone_set('America/Toronto');
			$date = date('Y-m-j G:i:s', time());
			
			//delete any duplicates, before re-adding
			//easier than checking
			$stm = $db->prepare("DELETE FROM candidate_elections WHERE candidate_id = ?");
			$stm -> execute(array($_POST['existing_candidate'.$j]));
			
			
			$stm = $db->prepare("INSERT INTO candidate_elections (candidate_id, election_id, position_id, school_id) VALUES (?,?,?,?);");
			$stm->bindValue(1, $_POST['existing_candidate'.$j]);
			$stm->bindValue(2, $_SESSION['election_id']);
			$stm->bindValue(3, $_POST['position_existing_candidate'.$j]);
			$stm->bindValue(4, $_SESSION['school_id']);
			$stm->execute();
			
			$stm = $db->prepare('SELECT election_selector FROM elections WHERE election_id = ?');
			$stm->execute(array($_SESSION['election_id']));
			$election = $stm->fetch();
			
		}
		catch (PDOException $e){
			echo $e->getMessage();
		}
		
		$j++;
	}
	$j--;
	
	$i += $j;
	
	//update number of candidates
	$db->exec("UPDATE elections SET num_candidates=".$i." WHERE election_id=".$_SESSION['election_id']);
	
	header("Location: ../../pages/election-details.php?election=".$election[0]);
}