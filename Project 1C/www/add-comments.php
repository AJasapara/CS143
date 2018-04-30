<!DOCTYPE html>
<html>
<head>
	<title>Add Review</title>
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
	<h1>Add Review</h1>
	<form action="" method="GET">


		<div class="label"><b>Your name</b></div> 
		<input class="input-form text-field" type="text" name="name" maxlength="30">

		<br><br>
		<div class="label"><b>Rating</b></div> 
		Enter a number from 1 to 5.<br> 
		<input class="input-form text-field" type="text" name="rating" maxlength="30">

		<br><br>
		<div class="label"><b>Review</b></div> 
		<textarea type="text" name="review" cols="60" rows="8"></textarea>

		<input type="hidden" name="movieID" value="<?php echo htmlspecialchars($_GET['movie-list']);?>">


		<div class="button-container">
			<input class="submit-button" type="submit" value="Add!" name="submit-button">
		</div>
	</form>


		<?php
		if(isset($_GET['submit-button'])) {
			if(inputValid() == true) 
				addReview();
		}

		function inputValid() {
			// Name, rating cannot be null. Review can be empty. 
			$validName = $validRating = $ratingNotEmpty = $validMovie = false;
			
			if(!empty($_GET['name'])) 
				$validName = true; // Title was filled out

			if(!empty($_GET['rating'])) {
				$ratingNotEmpty = true;
				if((int) $_GET['rating'] > 0 && (int) $_GET['rating'] < 6)
					$validRating = true;
			}
			
			
			// Check if everything is valid
			if($validName  == true  && $validRating  == true) //  && $validMovie  == true) 
				return true; // everything is valid
			

			// not everything is valid :-( So print errors
			$errMsg = "<br>Error: <br>";
			
			if($validName == false) 
				$errMsg = $errMsg . "No name entered<br>";

			if($ratingNotEmpty == false) 
				$errMsg = $errMsg . "No rating entered<br>";

			if($ratingNotEmpty == true && $validRating == false) 
				$errMsg = $errMsg . "Rating needs to be a number from 1 to 5<br>";

			// if($validMovie == false) 
			// 	$errMsg = $errMsg . "No movie selected<br>";
			
			// display error message
			echo $errMsg;

			return false;
		}

	

		function addReview() {
			// Establishing connection to database
			$db = new mysqli('localhost','cs143','','CS143');
			if($db->connect_errno > 0) {
				die('Unable to connect to database [' . $db->connect_error . ']');
			}

			$name = $_GET['name'];
			$time = date('Y-m-d G:i:s');
			$mid = $_GET['movieID'];
			$review =  $_GET['review'];
			$rating = $_GET['rating'];

			// create query
			$addRevQuery = "INSERT INTO Review(name, time, mid, rating, comment) VALUES('$name','$time','$mid','$rating','$review')";

			
			// Check if both queries were successful
			if($db->query($addRevQuery) === true ) {
				// success
				$successMsg = "<br>Added review successfully!<br>";
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