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
		if($_SESSION['currentPage']===$_SESSION['destination']){
			echo 

			"
			<script type=\"text/javascript\">
				alert(\"Voila You Won\");
			</script>
			";
		}
		printGame();
		//loadCurrentPage("/wiki/Main_Page");
		loadCurrentPage($_SESSION['currentPage']);
	?>

	<script type="text/javascript">
		 $("#gamePage a").click(function () {
	        var addressValue = $(this).attr("href");
	        if(addressValue.indexOf("https://en.wikipedia.org/wiki/")==0){
	        	alert(addressValue);
	        }else{

	        }
	    });

		 function gameWon(){
		 	alert("Voila");
		 }
	</script>

</body>
</html>


