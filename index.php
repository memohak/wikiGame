<html>
	
	<head>
		<title>Wiki Game</title>
	</head>
	<body>
		<a href="setGame.php">Start single player game</a>
	</body>

	<?php
		session_start();
		if(isset($_SESSION['source'])){
			echo $_SESSION['source'];
		}
		//test area
		echo $milliseconds = round(microtime(true) * 1000);
	?>

</html>