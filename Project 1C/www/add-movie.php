<!DOCTYPE html>
<html>
<head>
	<title>Add Movie Info</title>
</head>

<body>
	<h1>Add Movie Info</h1>
	<form action="" method="GET">

		<div class="label"><b>Title</b></div> 
		<input class="input-form text-field" type="text" name="title" maxlength="30">
		<br><br>

		<div class="label"><b>Company</b></div> 
		<input class="input-form text-field" type="text" name="company" maxlength="30">

		<br><br>
		<div class="label"><b>Year</b></div> 
		<input class="input-form text-field" type="text" name="year" maxlength="30">
		
		<br><br>
		<div class="label"><b>Rating</b></div> 
		<input class="input-form" type="radio" name="rating" value="G">G 
		<input class="input-form" type="radio" name="rating" value="PG">PG 
		<input class="input-form" type="radio" name="rating" value="PG-13">PG-13
		<input class="input-form" type="radio" name="rating" value="R">R
		<input class="input-form" type="radio" name="rating" value="NC-17">NC-17

		<br><br>
		<div class="label"><b>Genre</b></div> 
		<input class="input-form" type="checkbox" name="genre[]" value="Action">Action
		<input class="input-form" type="checkbox" name="genre[]" value="Adult">Adult
		<input class="input-form" type="checkbox" name="genre[]" value="Adventure">Adventure
		<input class="input-form" type="checkbox" name="genre[]" value="Animation">Animation
		<input class="input-form" type="checkbox" name="genre[]" value="Comedy">Comedy
		<input class="input-form" type="checkbox" name="genre[]" value="Crime">Crime
		<input class="input-form" type="checkbox" name="genre[]" value="Documentary">Documentary
		<input class="input-form" type="checkbox" name="genre[]" value="Drama">Drama
		<input class="input-form" type="checkbox" name="genre[]" value="Family">Family
		<input class="input-form" type="checkbox" name="genre[]" value="Fantasy">Fantasy
		<input class="input-form" type="checkbox" name="genre[]" value="Horror">Horror
		<input class="input-form" type="checkbox" name="genre[]" value="Musical">Musical
		<input class="input-form" type="checkbox" name="genre[]" value="Mystery">Mystery
		<input class="input-form" type="checkbox" name="genre[]" value="Romance">Romance
		<input class="input-form" type="checkbox" name="genre[]" value="Sci-Fi">Sci-Fi
		<input class="input-form" type="checkbox" name="genre[]" value="Short">Short
		<input class="input-form" type="checkbox" name="genre[]" value="Thriller">Thriller
		<input class="input-form" type="checkbox" name="genre[]" value="War">War
		<input class="input-form" type="checkbox" name="genre[]" value="Western">Western

		<div class="button-container">
			<input class="submit-button" type="submit" value="Add!" name="submit-button">
		</div>
	</form>

	<?php
		if(isset($_GET['submit-button'])) {
			if(inputValid() == true) 
				addMovie();
		}

		function inputValid() {
			// Title, company, year, rating cannot be null. Genre can be null
			$validTitle  = $validCompany  = $yearNotEmpty = $validYear = $validRating = false;
			
			if(!empty($_GET['title'])) 
				$validTitle = true; // Title was filled out
			
			if(!empty($_GET['company'])) 
				$validCompany = true; // Company name was filled out

			if(!empty($_GET['year']))  {
				$yearNotEmpty = true;
				if(checkYear($_GET['year']) == true)
					$validYear = true; // Correct year format
			}
			
			if(isset($_GET['rating'])) 
				$validRating = true; // Rating was chosen
	
			// Check if everything is valid
			if($validTitle  == true && $validCompany  == true && $validYear == true && $validRating == true) {
				return true;
				// everything is valid
			}

			// not everything is valid :-( So print errors
			$errMsg = "<br>Error: <br>";
			
			if($validTitle == false) {
				$errMsg = $errMsg . "No title entered<br>";
			}

			if($validCompany == false) {
				$errMsg = $errMsg . "No company entered<br>";
			}

			if($yearNotEmpty == false) {
				$errMsg = $errMsg . "No year entered<br>";
			}

			if($validYear == false && $yearNotEmpty == true) {
				$errMsg = $errMsg . "Wrong year format<br>";
			}

			if($validRating == false) {
				$errMsg = $errMsg . "No rating chosen<br>";
			}

			// display error message
			echo $errMsg;

			return false;
		}

		function checkYear($year) {
			if(strlen($year) != 4 || (int) $year < 1000)
				return false;
			return true;
		}

		function addMovie() {
			// Establishing connection to database
			$db = new mysqli('localhost','cs143','','CS143');
			if($db->connect_errno > 0) {
				die('Unable to connect to database [' . $db->connect_error . ']');
			}

			$maxIDQuery = "SELECT id FROM MaxMovieID";
			$maxIDRs = $db->query($maxIDQuery);
			while($row = $maxIDRs->fetch_assoc()) {
				foreach($row as $val)
					$maxID = $val;
			}

			$newID = intval($maxID) + 1;

			$title = $_GET['title'];
			$comp = $_GET['company'];
			$year =  (string) $_GET['year'];
			$rating = $_GET['rating'];
			$genre = array();

			foreach($_GET['genre'] as $chosen) {
				array_push($genre, $chosen);
			}

			// create query
			$addMovieQuery = "INSERT INTO Movie(id, title, year, rating, company) VALUES('$newID','$title','$year','$rating','$comp')";
			$updateMaxIDQuery = "UPDATE MaxMovieID SET id=$newID WHERE id=$maxID";

			
			// Check if both queries were successful
			if(($db->query($addMovieQuery) === true) && ($db->query($updateMaxIDQuery) === true)) {
				// success
				$successMsg = "<br>Added movie, " . $title . ", successfully<br>";
				echo $successMsg;
			}

			else { // Insert failed :-()
				$err = $db->error;
				echo $err;
			}

			// Add genres to the movie
			foreach($genre as $movieGenre) {
				$addMovieGenreQuery = "INSERT INTO MovieGenre VALUES('$newID','$movieGenre')";
				if($db->query($addMovieGenreQuery) === true) {
					// success
					echo $movieGenre . "<br>";
				}
				else { // Genre insert failed
					$genreErr = $db->error;
					echo $genreErr;
				}
			}
			
		}
	?>


</body>

</html>