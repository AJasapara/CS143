<!DOCTYPE html>
<html>
<head>
	<title>Database Query</title>
</head>

<body>
	<h1>Database Query</h1>

	Type an SQL query in the following box.

	<p>
		<form action="query.php" method="GET">
			<textarea type="text" name="query" cols="60" rows="8"></textarea>
			<input type="submit" name="submit" value="Submit"/>
		</form>
	</p>

	<?php
		function showTable() {
			if(isset($_GET['query'])) {
				echo "<h2>Results from MySQL</h2>";

				// Establishing connection to database
				$db = new mysqli('localhost','cs143','','CS143');
				if($db->connect_errno > 0) {
					die('Unable to connect to database [' . $db->connect_error . ']');
				}

				$dbQ = (string)$_GET['query']; // Get user input from text box

				if(!($rs = $db->query($dbQ))) {
					$errmsg = $db->error;
					print "Query failed: $errmsg <br />";
					exit(1);
				}
				$columnInfo = mysqli_fetch_fields($rs);


				echo "<table border='1' cellspacing='1' cellpadding='2'>";
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
			}

		}

		if(isset($_GET['submit'])) {// Is there a query? 
			showTable();
		} 
	?>

	


</body>


</html>