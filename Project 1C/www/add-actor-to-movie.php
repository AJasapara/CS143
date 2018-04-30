<!DOCTYPE html>
<html>
<head>
	<title>Add Actor to Movie</title>
	<style>
		#nav {
			width: 100%;
			float: left;
			margin: 0 0 3em 0;
			padding: 0;
			list-style: none;
		}
		#nav li {
			float: left;
			display: block;
			padding: 8px 15px;

		}
	</style>
</head>

<body>
	<ul id="nav">
		<li><a href="index.php">home</a></li>
		<li><a href="add-actor-director.php">add actor/director</a></li>
		<li><a href="add-movie.php">add movie info</a></li>
		<li><a href="add-actor-to-movie.php">add movie/actor relation</a></li>
		<li><a href="add-director-to-movie.php">add movie/director relation</a></li>
		<li><a href="add-comments.php">add review</a></li>
		<li><a href="actorSearch.php">search</a></li>
	</ul>
	<h1>Add Actor to Movie</h1>
	<form action="" method="GET">

		<div class="label"><b>Movie title</b></div> 
		<?php
			$db = new mysqli('localhost','cs143','','CS143');
			if($db->connect_errno > 0) {
				die('Unable to connect to database [' . $db->connect_error . ']');
			}

			$movieQuery = "SELECT id, title, year from Movie";
			$movieRes = $db->query($movieQuery);

			// Show movies in dropdown
			echo '<select class"dropdown text-field" name="movie-list"> 
			<option disabled selected value></option>';

			while($row = $movieRes->fetch_assoc()) {
				echo '<option value="' . $row[id] . '">' . $row[title] . ' (' . $row[year] . ')</option>';
			}

			echo '</select>';

		?>

		<br><br>

		<div class="label"><b>Actor name</b></div> 
		<?php
			

			$actorQuery = "SELECT id, first, last from Actor";
			$actorRes = $db->query($actorQuery);

			// Show movies in dropdown
			echo '<select class"dropdown text-field" name="actor-list"> 
			<option disabled selected value></option>';

			while($row = $actorRes->fetch_assoc()) {
				echo '<option value="' . $row[id] . '">' . $row[first] . " ".  $row[last] . '</option>';
			}

			echo '</select>';
		?>


		<br><br>
		<div class="label"><b>Role</b></div> 
		<input class="input-form text-field" type="text" name="role" maxlength="30">
		
		<br><br>

		<div class="button-container">
			<input class="submit-button" type="submit" value="Add!" name="submit-button">
		</div>
	</form>

	<?php
		if(isset($_GET['submit-button'])) {
			if(inputValid() == true) {
				addActorToMovie($db);
			}
		}

		function inputValid() {
			$roleNotEmpty =  $validActor = $validMovie = false;

			if(!empty($_GET['role'])) 
				$roleNotEmpty = true;
				
			if(isset($_GET['movie-list']))  
				$validMovie = true;

			if(isset($_GET['actor-list']))  
				$validActor = true;
				
	
			// Check if everything is valid
			if($validActor  == true  && $roleNotEmpty  == true  && $validMovie  == true) 
				return true; // everything is valid
			

			// not everything is valid :-( So print errors
			$errMsg = "<br>Error: <br>";
			
			if($validActor == false) 
				$errMsg = $errMsg . "No actor chosen<br>";

			if($validMovie == false) 
				$errMsg = $errMsg . "No movie chosen<br>";

			if($roleNotEmpty == false) 
				$errMsg = $errMsg . "Role not entered<br>";

			
			// display error message
			echo $errMsg;

			return false;
		}

	

		function addActorToMovie($db) {
			$mid = $_GET['movie-list'];
			$aid =  $_GET['actor-list'];
			$role = $_GET['role'];

			// create query
			$addActorToMovieQuery = "INSERT INTO MovieActor(mid, aid, role) VALUES('$mid','$aid','$role')";
			
			// Check if both queries were successful
			if($db->query($addActorToMovieQuery) === true ) {
				// success
				$successMsg = "<br>Added actor to movie successfully!<br>";
				echo $successMsg;
			}

			else { // Insert failed :-()
				$err = $db->error;
				echo "FAIL";
				echo $err;
			}
			
		}
	?>

</body>

</html>