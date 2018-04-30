<!DOCTYPE html> 
<html>
<head>
	<title>Project 1C</title>
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

	<h1>Welcome to Arpit and Brittany's query system!</h1>
	<p>This is a website made for CS 143 Project 1C.</p>
</body>

</html>