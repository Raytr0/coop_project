<?php
session_start();
if (isset($_POST['save-election'])){
	//format dates
	date_default_timezone_set('America/Toronto');
	$date = date('Y-m-j g:i A', time());
	
	if (isset($_POST['start-date']) && isset($_POST['start-time'])){
		if (!strpos($_POST['start-date'], ' ')){
			$first_comma = strpos($_POST['start-date'], ',');
			$second_comma = strpos($_POST['start-date'], ',', strpos($_POST['start-date'], ',') + 1);
			$month = substr($_POST['start-date'], 0, $first_comma);
			$day = substr($_POST['start-date'], $first_comma + 1, $second_comma - ($first_comma + 1));
			$year = substr($_POST['start-date'], $second_comma + 1);
		}
		else{
			$first_comma = strpos($_POST['start-date'], ',');
			$second_comma = strpos($_POST['start-date'], ',', strpos($_POST['start-date'], ',') + 1);
			$month = substr($_POST['start-date'], 0, $first_comma);
			$day = substr($_POST['start-date'], $first_comma + 2, $second_comma - ($first_comma + 2));
			$year = substr($_POST['start-date'], $second_comma + 2);
		}
		
		$startdatetime = $year.'-'.date("m", strtotime($month)).'-'.$day.' '.$_POST['start-time'];
		echo 'start: '.$date.'<br>'.$startdatetime."<br>";
	}else{
		echo '???';
	}
	
	if (isset($_POST['end-date']) && isset($_POST['end-time'])){
		if (!strpos($_POST['end-date'], ' ')){
			$first_comma = strpos($_POST['end-date'], ',');
			$second_comma = strpos($_POST['end-date'], ',', strpos($_POST['end-date'], ',') + 1);
			$month = substr($_POST['end-date'], 0, $first_comma);
			$day = substr($_POST['end-date'], $first_comma + 1, $second_comma - ($first_comma + 1));
			$year = substr($_POST['end-date'], $second_comma + 1);

		}
		else {
			$first_comma = strpos($_POST['end-date'], ',');
			$second_comma = strpos($_POST['end-date'], ',', strpos($_POST['end-date'], ',') + 1);
			$month = substr($_POST['end-date'], 0, $first_comma);
			$day = substr($_POST['end-date'], $first_comma + 2, $second_comma - ($first_comma + 2));
			$year = substr($_POST['end-date'], $second_comma + 2);
		}
		
		$enddatetime = $year.'-'.date("m", strtotime($month)).'-'.$day.' '.$_POST['end-time'];
		echo 'end: '.$date.'<br>'.$enddatetime."<br>";
	}else{
		echo '???';
	}
	
	require_once "pdo.inc.php";
	
	try{
		//all the easy stuff
		$zero = 0;
		$date_start = date('Y-m-d G:i:s',strtotime($startdatetime));
		$date_end = date('Y-m-d G:i:s',strtotime($enddatetime));
		$date_now = date('Y-m-d G:i:s',strtotime($date));
		$stm = $pdo->prepare("UPDATE elections SET school_id=?, election_name=?, starting_date=?, finishing_date=?,date_created=?
								WHERE election_id=?");
		$stm->bindValue(1, $_SESSION['school']);
		$stm->bindValue(2, $_POST['name']);
		$stm->bindValue(3, $date_start);
		$stm->bindValue(4, $date_end);
		$stm->bindValue(5, $date_now);
		$stm->bindValue(6, $_POST['election-id']);
		$stm->execute();
		/*
		$stm = $pdo->prepare("DELETE FROM position_election WHERE election_id=?");
		$stm->bindValue(1, $_POST['election-id']);
		$stm->execute();
		
		foreach ($_POST['positions'] as $pos){
			$pdo->exec("INSERT INTO position_election(position_id, election_id) VALUES (".$pos.", ".$_POST['election-id'].");");
		}
		*/
		
		
		
		//candidates
		
		$stm = $pdo->query("SELECT num_candidates FROM elections WHERE election_id=".$_POST['election-id']);
		$result = $stm->fetch();
		$num_candidates = $result[0];
		echo 'initial number of candidates: '.$num_candidates.'<br>';
		
		for ($i = 0; isset($_POST['existing_candidate'.$i]); $i++){
			$nrows = $pdo->exec("DELETE FROM candidate_election WHERE candidate_id=".$_POST['existing_candidate'.$i]." AND election_id=".$_POST['election-id']);
			$num_candidates -= $nrows;
			echo 'subtract '.$nrows.'. number of candidates: '.$num_candidates.'<br>';
			if ($_POST['select_pos'.$i] != -1){
				$pdo->exec("INSERT INTO candidate_election (candidate_id, position_id, election_id, school_id) VALUES
										(".$_POST['existing_candidate'.$i].", ".$_POST['select_pos'.$i].", ".$_POST['election-id'].", ".$_SESSION['school'].")");
				$num_candidates++;
				echo 'add 1. number of candidates: '.$num_candidates.'<br>';
			}
			
		}
		$pdo->exec("UPDATE elections SET num_candidates=".$num_candidates." WHERE election_id=".$_POST['election-id']);
		echo 'number of candidates: '.$num_candidates.'<br>';
		
		
		//positions (updated after candidates)
		$stm = $pdo->prepare("SELECT position_id FROM position_election WHERE election_id=?");
		$stm->bindValue(1, $_POST['election-id']);
		$stm->execute();
		
		for ($i = 0; $row = $stm->fetch(PDO::FETCH_ASSOC); $i++){
			$old_pos[$i] = $row['position_id'];
		}
		
		//populate array to check if all submitted positions are in the database
		foreach ($_POST['positions'] as $newp){
			$checked[$newp] = false;
		}
		
		foreach ($old_pos as $oldp){
			$match = false;
			
			//look for a matching submitted position
			foreach ($_POST['positions'] as $newp){
				if ($oldp == $newp){
					$match = true;
					$checked[$newp] = true;
					break;
				}
			}
			
			//no such submitted position was found; delete this position from the election
			if (!$match){
				$pdo->exec("DELETE FROM position_election WHERE position_id=".$oldp);
				
				//$stm = $pdo->query("SELECT candidate_id FROM candidate_election WHERE position_id=".$oldp);
				//$row = $stm->fetch(PDO::FETCH_ASSOC);
				
				$pdo->exec("DELETE FROM candidate_election WHERE position_id=".$oldp);
				$pdo->exec("DELETE FROM voter_election WHERE position_id=".$oldp);
			}
		}
		
		//check for any new positions
		foreach ($checked as $key=>$c){
			//save new positions
			if (!$c){
				$pdo->exec("INSERT INTO position_election (position_id, election_id) VALUES (".$key.", ".$_POST['election-id'].")");
			}
		}
		/*
		for ($i = 0; isset($_POST['existing_candidate'.$i]); $i++){
			echo $_POST['existing_candidate'.$i].' '.$_POST['select_pos'.$i].'<br>';
		}*/
	}
	catch (PDOException $e){
		$_SESSION['error'] = 'PDO: '.$e->getMessage();
		header("Location:../../pages/election-list.php");
	}
	
	$_SESSION['saved'] = true;
	header("Location: ../../pages/election-details.php?election=".$_POST['election-id']);
}