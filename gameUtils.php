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
    	echo "Source = ".$_SESSION['source']." destination = ".$_SESSION['destination'].$_SESSION['currentPage'];
    	echo "<div id='timer'> Time Elapsed = ".getTimeDiffInSec()."</div><br>";
    }

    function addLinks($from, $add, $where){
        $lastPos = 0;
        while (($lastPos = strpos($from, $where, $lastPos))!== false) {
            $from = substr_replace($from, $add, $lastPos+6,0);
            $lastPos = $lastPos + strlen($add);
        }
        return $from;
    }
    
    function loadCurrentPage($page){
        echo '<div id = "gamePage">';
        $url = "https://en.wikipedia.org".$page;
        $html = new simple_html_dom();
        $main = file_get_html($url);
        $outgoingLinks = array();
        foreach ($main->find('a') as $key) {
            if(strpos($key->href, "/wiki/")===0
                &&strpos($key->href, ":")===false
                &&$key->plaintext!=''
                &&$key->plaintext!='Read'
                &&$key->plaintext!='Main page'
                &&$key->plaintext!='Article'
                &&$key->plaintext!='Main Page'
                &&$key->plaintext!='free'
                &&$key->plaintext!='Full Article...'
                &&$key->plaintext!='Full article...'
                &&$key->plaintext!='Wikipedia'){
                array_push($outgoingLinks, $key->href);
            }
        }
        $_SESSION['outgoingLinks'] = $outgoingLinks;
        $main = addLinks($main, "authenticatePage.php?click=","href=\"/wiki/");
        $main = addLinks($main, "https://en.wikipedia.org","href=\"/");
        echo '<br><br>'.$main.'</div>';
    }


?>