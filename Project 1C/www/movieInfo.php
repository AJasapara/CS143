<!DOCTYPE html>
<html>
<head>
	<title>Movie Information</title>
	<style>
	    table {
	    	border-collapse:collapse; 
	    	table-layout:fixed; 
	    	width:600px;
	    }
	    table td {word-wrap:break-word;}
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
		th, td {
			padding: 5px;
		}
		body {
			font-family: "Arial";
			text-align: center;
			align-content: center;
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
	<h1>Movie Information Page</h1>
	<?php
		$id = $_GET['id'];
		$q = "SELECT title, company, rating, CONCAT(Director.first, ' ',Director.last) AS 'Director Name', genre FROM Movie, MovieDirector, Director, MovieGenre 
			WHERE Movie.id = $id AND MovieDirector.mid = $id AND MovieDirector.did = Director.id AND MovieGenre.mid = $id";
		echo "<h2>Movie information is:</h2>";
		$db = new mysqli('localhost','cs143','','CS143');
		if($db->connect_errno > 0) {
			die('Unable to connect to database [' . $db->connect_error . ']');
		}
		if(!($rs = $db->query($q))) {
			$errmsg = $db->error;
			print "Query failed: $errmsg <br />";
			exit(1);
		}
		$row = $rs->fetch_assoc();
		echo 'Title: '.$row['title'].'<br/>';
		echo 'Producer: '.$row['company'].'<br/>';
		echo 'MPAA Rating: '.$row['rating'].'<br/>';
		echo 'Director: '.$row['Director Name'].'<br/>';
		echo 'Genre: '.$row['genre'].'<br/>';
		$rs->free();

		echo "<h2>Actors in this Movie:</h2>";

		$q = "SELECT id, CONCAT(Actor.first, ' ',Actor.last) AS 'Actor Name', role as 'Role' FROM MovieActor, Actor WHERE mid = $id AND aid = Actor.id";

		if(!($rs = $db->query($q))) {
			$errmsg = $db->error;
			print "Query failed: $errmsg <br />";
			exit(1);
		}
		$columnInfo = mysqli_fetch_fields($rs);
		echo "<table align='center' border='1' cellspacing='1' cellpadding='2'>";
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
			$aid = $row['id'];
			$val = $row['Actor Name'];
			echo '<a href="actorInfo.php?id='.$aid.'">'.$val.'</a>';
			echo "</td>";
			echo "<td align='center'>";
			$val = $row['Role'];
			echo $val;
			echo "</td>";
			echo "</tr>";
		}

		echo "</table></br>";
		$rs->free();

		echo'<h2>User Reviews</h2>';
		$q = "SELECT AVG(rating) as avg, COUNT(rating) as count FROM Review WHERE mid = $id";
		if(!($rs = $db->query($q))) {
			$errmsg = $db->error;
			print "Query failed: $errmsg <br />";
			exit(1);
		}
		$row = $rs->fetch_assoc();
		echo'The average score of this movie is '.$row['avg'].' based on '.$row['count'].' user reviews.</br>';
		$rs->free();
		echo'</br>';

		$q = "SELECT name, time, rating, comment FROM Review WHERE mid = $id";
		if(!($rs = $db->query($q))) {
			$errmsg = $db->error;
			print "Query failed: $errmsg <br />";
			exit(1);
		}
		$columnInfo = mysqli_fetch_fields($rs);
		echo "<table align='center' border='1' cellspacing='1' cellpadding='2'>";
		echo "<tr>";
		// Print out first row of column names
		foreach($columnInfo as $attribute) {
			echo "<th align='center'>";
			echo $attribute->name;
			echo "</th>";
		}
		echo "</tr>";
		//Print out query results
		while($row = $rs->fetch_assoc()) {
			echo "<tr>";
			foreach($row as $val) {
				echo "<td align='center'>";
				if(is_null($val))
					echo "N/A";
				else
					echo $val;
				echo "</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
		$rs->free();
		

		echo '<h3><a href="add-comments.php?movieID='.$id.'">Add Comment!</a></h3>';

	?>
</body>

</html>