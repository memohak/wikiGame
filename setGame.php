<?php
	session_start();
	ini_set('display_errors', 1);
	include_once('gameUtils.php');
	$game = getGame();
	$_SESSION['id'] = $game['id'];
	$_SESSION['source'] = $game['source'];
	$_SESSION['destination'] = $game['destination'];
	$_SESSION['currentPage'] = $game['source'];
	$_SESSION['startTime'] = round(microtime(true) * 1000);

	$url = "http://localhost/wikiGame/game.php";
	header('Location: '.$url);
?>