<?php
	ini_set('display_errors', 1);
	include_once('simple_html_dom.php');
	header( 'Content-type: text/html; charset=utf-8' );
	header('Connection: close');
    header('Content-length: '.ob_get_length());
    $connect = mysqli_connect('localhost','root','braze') or die('error1');
    mysqli_select_db($connect,'wikipedia') or die('error2');
    $get_unprocessed = "SELECT 	`name` FROM `pages` WHERE `processed`='0'  ORDER BY rand() LIMIT 1 ";
    $run_query1 = mysqli_query($connect, $get_unprocessed);
    while($result = mysqli_fetch_assoc($run_query1)){
    	$pageFetchStart = microtime(true);
    	$name = $result['name'];
    	$url = "https://en.wikipedia.org".$name;
    	$html = new simple_html_dom();
    	$main = file_get_html($url);
    	echo $main;
    	$main = mysqli_real_escape_string($connect,$main);
    	$name = mysqli_real_escape_string($connect,$name);
    	$store_page = "INSERT INTO `wikiPage`(`name`, `data`) VALUES ('$name','$main')";
    	
    	$run_query = mysqli_query($connect,$store_page) or die(mysqli_error($connect));

    }