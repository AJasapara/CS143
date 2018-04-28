<!DOCTYPE html>
<html>
<head>
	<title>Add Review</title>
</head>

<body>
	<h1>Add Review</h1>
	<form action="" method="GET">

		<div class="label"><b>Your name</b></div> 
		<input class="input-form text-field" type="text" name="name" maxlength="30">
		<br><br>

		<div class="label"><b>Movie you're reviewing</b></div> 
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
		<div class="label"><b>Rating</b></div> 
		Enter a number from 1 to 5.<br> 
		<input class="input-form text-field" type="text" name="rating" maxlength="30">
		

		<br><br>
		<div class="label"><b>Review</b></div> 
		<textarea type="text" name="review" cols="60" rows="8"></textarea>


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

			if(isset($_GET['movie-list']))  
				$validMovie = true;
				
	
			// Check if everything is valid
			if($validName  == true  && $validRating  == true  && $validMovie  == true) 
				return true; // everything is valid
			

			// not everything is valid :-( So print errors
			$errMsg = "<br>Error: <br>";
			
			if($validName == false) 
				$errMsg = $errMsg . "No name entered<br>";

			if($ratingNotEmpty == false) 
				$errMsg = $errMsg . "No rating entered<br>";

			if($ratingNotEmpty == true && $validRating == false) 
				$errMsg = $errMsg . "Rating needs to be a number from 1 to 5<br>";

			if($validMovie == false) 
				$errMsg = $errMsg . "No movie selected<br>";
			
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
			$mid = $_GET['movie-list'];
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