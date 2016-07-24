<!DOCTYPE html>
<html>
<head>
	<title>Game</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head>
<body>
	<?php
		session_start();
		include_once('gameUtils.php');
		printGame();
		$url = "https://en.wikipedia.org".$_SESSION['currentPage'];
		echo "<iframe id=\"iframe\" src=\"".$url."\" width=\"1270dp\" height=\"600dp\"></iframe>";
	
	?>
	<script type="text/javascript">
		$('#iframe').load(function() {
		  alert("the iframe has been loaded");
		});
	</script>


</body>
</html>


