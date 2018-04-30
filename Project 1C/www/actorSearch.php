<!DOCTYPE html>
<html>
<head>
	<title>Search</title>
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
	<h1>Search</h1>
	<form action="" method="GET">

		<div class="label"><b>Name</b></div> 
		<input class="input-form text-field" type="text" name="name" maxlength="30">
		<br><br>

		<div class="button-container">
			<input class="submit-button" type="submit" value="Submit!" name="submit-button">
		</div>
	</form>

	<?php
		if(isset($_GET['submit-button']) && isset($_GET['name'])) {
			searchPerson();
			searchMovie();
		}

		function searchPerson() {
			// Establishing connection to database
			$db = new mysqli('localhost','cs143','','CS143');
			if($db->connect_errno > 0) {
				die('Unable to connect to database [' . $db->connect_error . ']');
			}

			echo "<h2>Matching Actors Are:</h2>";
			$name = $_GET['name'];
			$q = "SELECT id, CONCAT(Actor.first, ' ',Actor.last) AS 'Actor Name', dob AS 'Date of Birth' FROM Actor WHERE CONCAT(Actor.first, ' ',Actor.last)LIKE '%$name%'";

			
			if(!($rs = $db->query($q))) {
				$errmsg = $db->error;
				print "Query failed: $errmsg <br />";
				exit(1);
			}
			$columnInfo = mysqli_fetch_fields($rs);
			echo "<table border='1' cellspacing='1' cellpadding='2'>";
			echo "<tr>";

			// Print out first row of column names
			foreach($columnInfo as $attribute) {
				if($attribute->name !== "id"){
					echo "<th align='center'>";
					echo "$attribute->name";
					echo "</th>";
				}
			}

			echo "</tr>";

			//Print out query results
			while($row = $rs->fetch_assoc()) {
				echo "<tr>";
				echo "<td align='center'>";
				$id = $row['id'];
				$val = $row['Actor Name'];
				echo '<a href="actorInfo.php?id='.$id.'">'.$val.'</a>';
				echo "</td>";
				echo "<td align='center'>";
				$val = $row['Date of Birth'];
				echo '<a href="actorInfo.php?id='.$id.'">'.$val.'</a>';
				echo "</td>";
				echo "</tr>";
			}

			echo "</table>";
			$rs->free();

		}

		function searchMovie() {
			// Establishing connection to database
			$db = new mysqli('localhost','cs143','','CS143');
			if($db->connect_errno > 0) {
				die('Unable to connect to database [' . $db->connect_error . ']');
			}

			echo "<h2>Matching Movies Are:</h2>";
			$name = $_GET['name'];
			$q = "SELECT id, title AS 'Title', year AS 'Year' FROM Movie WHERE title LIKE '%$name%'";

			
			if(!($rs = $db->query($q))) {
				$errmsg = $db->error;
				print "Query failed: $errmsg <br />";
				exit(1);
			}
			$columnInfo = mysqli_fetch_fields($rs);
			echo "<table border='1' cellspacing='1' cellpadding='2'>";
			echo "<tr>";

			// Print out first row of column names
			foreach($columnInfo as $attribute) {
				if($attribute->name !== "id"){
					echo "<th align='center'>";
					echo "$attribute->name";
					echo "</th>";
				}
			}

			echo "</tr>";

			//Print out query results
			while($row = $rs->fetch_assoc()) {
				echo "<tr>";
				echo "<td align='center'>";
				$id = $row['id'];
				$val = $row['Title'];
				echo '<a href="movieInfo.php?id='.$id.'">'.$val.'</a>';
				echo "</td>";
				echo "<td align='center'>";
				$val = $row['Year'];
				echo '<a href="movieInfo.php?id='.$id.'">'.$val.'</a>';
				echo "</td>";
				echo "</tr>";
			}

			echo "</table>";
			$rs->free();

		}
	?>


</body>

</html>