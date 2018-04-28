<!DOCTYPE html>
<html>
<head>
	<title>Movie Information</title>
</head>

<body>
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
			$val = $row['Role'];
			echo $val;
			echo "</td>";
			echo "</tr>";
		}

		echo "</table>";
		$rs->free();

	?>
</body>

</html>