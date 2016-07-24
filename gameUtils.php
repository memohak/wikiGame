<?php

	ini_set('display_errors', 1);
	
	header( 'Content-type: text/html; charset=utf-8' );
	header('Connection: close');
    header('Content-length: '.ob_get_length());
	
	include_once('simple_html_dom.php');
    
    $connect = mysqli_connect('localhost','root','braze') or die('error1');
    mysqli_select_db($connect, "wikiGame");
    
    function getGame(){
    	$query = "SELECT * FROM `gameSeeds` ORDER BY rand() LIMIT 1";
    	$run_sql = mysqli_query($GLOBALS['connect'],$query);
        $res = mysqli_fetch_assoc($run_sql); 
        $game = array('id' => $res['gameId'], 'source' => $res['source'], 'destination' => $res['destination'] );
        return $game;
    }

    function getTimeDiffInSec(){
    	return (round(microtime(true) * 1000)-$_SESSION['startTime'])/1000;
    }

    function printGame(){
    	echo "Source = ".$_SESSION['source']." destination = ".$_SESSION['destination'];
    	echo "<div name='timer'> Time Elapsed = ".getTimeDiffInSec()."</div><br>";
    }
    



?>