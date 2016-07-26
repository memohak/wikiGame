<?php
	session_start();
	if(isset($_GET['click'])){
		$goto = $_GET['click'];
		if(array_search($goto, $_SESSION['outgoingLinks'])){
			$_SESSION['currentPage'] = $goto;
			$url = "http://localhost/wikiGame/game.php";
			header('Location: '.$url);
		}else{
			echo 'Something went wrong2';
		}
	}else{
		echo 'Something went wrong1';
	}
?>