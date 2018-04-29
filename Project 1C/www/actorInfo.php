<!DOCTYPE html>
<html>
<head>
	<title>Actor Information</title>
</head>

<body>
	<h1>Actor Information Page</h1>
	<?php
		$id = $_GET['id'];
		$q = "SELECT CONCAT(Actor.first, ' ',Actor.last) AS 'Actor Name', sex AS 'Sex', dob AS 'Date of Birth', dod AS 'Date of Death' FROM Actor WHERE id = $id";
		echo "<h2>Actor information is:</h2>";
		showTable($q);

		$q = "SELECT mid, role AS 'Role', title AS 'Title' FROM MovieActor, Movie WHERE aid = $id AND mid = id";
		echo "<h2>Actor's Movies and Role:</h2>";
		showTable($q);

		function showTable($q){
			$db = new mysqli('localhost','cs143','','CS143');
			if($db->connect_errno > 0) {
				die('Unable to connect to database [' . $db->connect_error . ']');
			}
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
				if($attribute->name !== "mid"){
					echo "<th align='center'>";
					echo "$attribute->name";
					echo "</th>";
				}
			}
			echo "</tr>";
			while($row = $rs->fetch_assoc()) {
				echo "<tr>";
				foreach($row as $key=>$val) {
					if ($key == "mid"){
						$mid = $val;
						continue;
					}
					echo "<td align='center'>";
					if($key === "Date of Death" && is_null($val))
						echo "Still Alive";
					else if($key === "Title")
						echo '<a href="movieInfo.php?id='.$mid.'">'.$val.'</a>';
					else
						echo $val;
					echo "</td>";
				}
				echo "</tr>";
			}

			echo "</table>";
			$rs->free();
		}

	?>
</body>

</html>