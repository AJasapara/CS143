<!DOCTYPE html>
<html>
<head>
	<title>Add Director to Movie</title>
	<style>
		#nav {
			width: 100%;
			float: left;
			margin: 0 0 3em 0;
			padding: 0;
			list-style: none;
			background-color: #383838;
			border-bottom: 1px solid #ccc;
			border-top: 1px solid #ccc;
			text-align: center;
		}
		#nav ul {
			text-align: center;
		}
		#nav li {
			/*float: left;*/
			display: inline;
		
		}
		#nav a {
			display:inline-block;
			padding: 10px;
		}
		#nav li a {
			padding: 8px 15px;
			color: #ffffff;
		}

		#nav li a:hover {
			background-color: #5b5b5b;
			color: #89bcff;
		}
		body {
			font-family: "Arial";
			text-align: center;
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
		<li><a href="actorSearch.php">search</a></li>
	</ul>
	<h1>Add Director to Movie</h1>
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

		<div class="label"><b>Director name</b></div> 
		<?php
			$directorQuery = "SELECT id, first, last from Director";
			$directorRes = $db->query($directorQuery);

			// Show movies in dropdown
			echo '<select class"dropdown text-field" name="director-list"> 
			<option disabled selected value></option>';

			while($row = $directorRes->fetch_assoc()) {
				echo '<option value="' . $row[id] . '">' . $row[first] . " ".  $row[last] . '</option>';
			}

			echo '</select>';
		?>
		<br><br>

		<div class="button-container">
			<input class="submit-button" type="submit" value="Add!" name="submit-button">
		</div>
	</form>

	<?php
		if(isset($_GET['submit-button'])) {
			if(inputValid() == true) 
				addDirectorToMovie($db);
		}

		function inputValid() {
			// Name, rating cannot be null. Review can be empty. 
			$validDirector = $validMovie = false;
				
			if(isset($_GET['movie-list']))  
				$validMovie = true;

			if(isset($_GET['director-list']))  
				$validDirector = true;
	
			// Check if everything is valid
			if($validDirector  == true  && $validMovie  == true) 
				return true; // everything is valid
			
			// not everything is valid :-( So print errors
			$errMsg = "<br>Error: <br>";
			
			if($validDirector == false) 
				$errMsg = $errMsg . "No director chosen<br>";

			if($validMovie == false) 
				$errMsg = $errMsg . "No movie chosen<br>";
			
			// display error message
			echo $errMsg;

			return false;
		}

	

		function addDirectorToMovie($db) {
			$mid = $_GET['movie-list'];
			$did =  $_GET['director-list'];

			// create query
			$addDirectorToMovieQuery = "INSERT INTO MovieDirector(mid, did) VALUES('$mid','$did')";
			
			// Check if both queries were successful
			if($db->query($addDirectorToMovieQuery) === true ) {
				// success
				$successMsg = "<br>Added director to movie successfully!<br>";
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