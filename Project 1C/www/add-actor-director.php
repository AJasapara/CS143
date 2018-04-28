<!DOCTYPE html>
<html>
<head>
	<title>Add Actor/Director Info</title>
</head>

<body>
	<h1>Add Actor/Director Info</h1>
	<form action="" method="GET">

		<input class="input-form" type="radio" name="person" value="Actor" id="actor">Actor 

		<input class="input-form" type="radio" name="person" value="Director" id="director">Director

		<br><br>
		<div class="label"><b>First name</b></div> 
		<input class="input-form text-field" type="text" name="first" maxlength="30">
		<br><br>

		<div class="label"><b>Last name</b></div> 
		<input class="input-form text-field" type="text" name="last" maxlength="30">

		<br><br>
		<input class="input-form" type="radio" name="sex" value="Male" id="male">Male 
		<input class="input-form" type="radio" name="sex" value="Female" id="female">Female 

		<br><br>
		<div class="label"><b>Date of birth</b></div> 
		<input class="input-form text-field" type="text" name="dob" maxlength="30">
		<br>
		i.e. 1997-04-04

		<br><br>
		<div class="label"><b>Date of death</b></div> 
		<input class="input-form text-field" type="text" name="dod" maxlength="30">
		<br>
		i.e. 1997-04-04, but leave blank if still alive.

		<br><br>

		<div class="button-container">
			<input class="submit-button" type="submit" value="Add!" name="submit-button">
		</div>
	</form>

	<?php
		if(isset($_GET['submit-button'])) {
			if(inputValid() == true) {
				addPerson();
			}
		}

		function inputValid() {
			$validPerson  = $validFirst  = $validLast = $validSex = $dobNotEmpty = $validDOB = $validDOD = false;
			
			if(isset($_GET['person'])) 
				$validPerson = true; // Actor or director was selected
			

			if(!empty($_GET['first'])) 
				$validFirst = true; // First name was filled out
			

			if(!empty($_GET['last'])) 
				$validLast = true; // last name was filled out
			

			if(isset($_GET['sex'])) 
				$validSex = true; // sex was chosen
			
			if(!empty($_GET['dob'])) {
				$dobNotEmpty = true; // DOB was filled out
				if(parseDate($_GET['dob']) == true) 
					$validDOB = true; // correct date format
			}

			if((!empty($_GET['dod']) &&  parseDate($_GET['dod']) == true) || empty($_GET['dod'])) 
				$validDOD = true; // correct date format or DOD not specified


			// Check if everything is valid
			if($validPerson  == true && $validFirst == true && $validLast == true && $validSex == true && $dobNotEmpty == true && $validDOB == true && $validDOD == true) {
				return true;
				// everything is valid
			}

			// not everything is valid :-( So print errors
			$errMsg = "<br>Error: <br>";
			
			if($validPerson == false) {
				$errMsg = $errMsg . "No job (actor/director) selected <br>";
			}

			if($validFirst == false) {
				$errMsg = $errMsg . "No first name entered<br>";
			}

			if($validLast == false) {
				$errMsg = $errMsg . "No last name entered<br>";
			}

			if($validSex == false) {
				$errMsg = $errMsg . "No sex chosen<br>";
			}

			if($dobNotEmpty == false) {
				$errMsg = $errMsg . "No DOB entered<br>";
			}

			if($dobNotEmpty == true && $validDOB == false) {
				$errMsg = $errMsg . "Wrong date format for DOB<br>";
			}

			if($validDOD == false) {
				$errMsg = $errMsg . "Wrong date format for DOD<br>";
			}

			// display error message
			echo $errMsg;

			return false;
		}

		function parseDate($date) {
			$part = explode("-", $date);
			// each part should represent the year, month, and day if the user correctly formatted the date. if not return false
			if(strlen($part[0]) != 4) 
				return false;
			if(strlen($part[1]) != 2  || (int) $part[1] < 1 || (int) $part[1] > 12) 
				return false;
			if(strlen($part[2]) != 2  || (int) $part[2] < 1 || (int) $part[2] > 24) 
				return false;

			return true;

		}

		function addPerson() {
			// Establishing connection to database
			$db = new mysqli('localhost','cs143','','CS143');
			if($db->connect_errno > 0) {
				die('Unable to connect to database [' . $db->connect_error . ']');
			}

			$maxIDQuery = "SELECT id FROM MaxPersonID";
			$maxIDRs = $db->query($maxIDQuery);
			while($row = $maxIDRs->fetch_assoc()) {
				foreach($row as $val)
					$maxID = $val;
			}


			$newID = intval($maxID) + 1;

			$first = $_GET['first'];
			$last = $_GET['last'];
			$job =  $_GET['person'];
			$dob = $_GET['dob'];
			$sex = $_GET['sex'];
			if(!empty($_GET['dod'])) 
				$dod = $_GET['dod'];
			
			else 
				 $dod = NULL;
			

			// create query
			if($job == 'Actor') {
				if($dod == NULL) 
					$addPersonQuery = "INSERT INTO Actor(id, last, first, sex, dob, dod) VALUES('$newID', '$last', '$first', '$sex', '$dob', NULL)";
				else 
					$addPersonQuery = "INSERT INTO Actor(id, last, first, sex, dob, dod) VALUES('$newID', '$last', '$first', '$sex', '$dob', '$dod')";
			}

			else { // Director
				if($dod == NULL) 
					$addPersonQuery = "INSERT INTO Director(id, last, first,  dob, dod) VALUES('$newID', '$last', '$first',  '$dob', NULL)";
				else 
					$addPersonQuery = "INSERT INTO Director(id, last, first, dob, dod) VALUES('$newID', '$last', '$first', '$dob', '$dod')";
			}

			$updateMaxIDQuery = "UPDATE MaxPersonID SET id=$newID WHERE id=$maxID";

			
			// Check if both queries were successful
			if(($db->query($addPersonQuery) === true) && ($db->query($updateMaxIDQuery) === true)) {
				// success
				$successMsg = "Woo! You added " . $first . " " . $last . ", " 
				. $job . ", to the database.";
				echo $successMsg;
			}


			else { // Insert failed :-()
				$err = $db->error;
				echo $err;
			}
		}
	?>


</body>

</html>