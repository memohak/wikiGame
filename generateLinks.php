<?php
	ini_set('display_errors', 1);
	include_once('simple_html_dom.php');
	header( 'Content-type: text/html; charset=utf-8' );
	header('Connection: close');
    header('Content-length: '.ob_get_length());
	$megaTime1 = microtime(true);
	$connect = mysqli_connect('localhost','root','braze') or die('error1');
    mysqli_select_db($connect,'wikipedia') or die('error2');
	if(isset($_GET['name'])){
		$t1 = microtime(true);
    	$from = $_GET['name'];
    	echo 'Page parsed: '.$from.'<br>';
    	$url = "https://en.wikipedia.org".$from;
    	$outgoing=0;
    	$unique=0;
    	$html = new simple_html_dom();
    	$main = file_get_html($url);
    	$t2 = microtime(true);
    	$gettingPage = $t2-$t1;
    	echo $gettingPage.'<br>';
    	$parsingLink = 0;
    	$linkCount = 0;
    	
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
    			$linkCount+=1;
		    	$t1=microtime(true);
    			$title = $key->title;
    			$title = mysqli_real_escape_string($connect,$title);
    			$to = $key->href;
    			$to = mysqli_real_escape_string($connect,$to);
    			$from = $_GET['name'];
    			$from = mysqli_real_escape_string($connect,$from);
    			$check_link = "SELECT `sno` FROM `links` WHERE `from`='$from' and `to`='$to'";
    			$query_run = mysqli_query($connect ,$check_link) or die('error3');
    			if(mysqli_num_rows($query_run)==0){
    				
    				$outgoing+=1;
    				$link_query = "INSERT INTO `links`(`from`, `to`) VALUES ('$from','$to')";
    				$run_link_query = mysqli_query($connect,$link_query) or die(mysqli_error($connect));
    				$check_page = "SELECT `incoming`,`name` FROM `pages` WHERE `name` = '$to'";
	    			$query_run = mysqli_query($connect ,$check_page) or die('error3');
	    			if(mysqli_num_rows($query_run)==0){
	    				$unique+=1;
	    				$link_query = "INSERT INTO `pages`(`title`, `name`) VALUES ('$title','$to')";
	    				$run_link_query = mysqli_query($connect,$link_query) or die(mysqli_error($connect));
	    			}else{
	    				$page = mysqli_fetch_assoc($query_run) ;
	    				$inc = $page['incoming'];
	    				$pgname = $page['name'];
	    				$pgname = mysqli_real_escape_string($connect,$pgname);
	    				$inc = $inc+1;
	    				$link_query = "UPDATE `pages` SET `incoming`='$inc' WHERE `name` = '$pgname'";
	    				$run_link_query = mysqli_query($connect,$link_query) or die(mysqli_error($connect));
	    			}

    			}
    			$t2 = microtime(true);
		    	$t = $t2-$t1;
		    	
		    	$parsingLink+=$t;
    		}
    	}
    	$percentUnique = ($unique/$outgoing)*100;
    	$parsingLink/=$linkCount;
    	$stat_query = "INSERT INTO `crawlingStats`(`name`, `gettingPage_time`, `processing1Link`, `outgoing`) VALUES ('$from','$gettingPage','$parsingLink','$linkCount')";
    	$query_run = mysqli_query($connect,$stat_query) or die(mysqli_error($connect));
    	$query = "UPDATE `pages` SET `outgoing`='$outgoing', `processed` = 1,`unique` = '$percentUnique',`editable`='$isEditable1' WHERE `name` = '$from'";
    	$query_run = mysqli_query($connect,$query) or die(mysqli_error($connect));
    	mysqli_close($connect);
    	echo $outgoing;
    	exit("done with this");
	}
    $get_unprocessed = "SELECT 	`name` FROM `pages` WHERE `processed`='0' and `incoming`>140 ORDER BY rand() LIMIT 1 ";
    $run_query1 = mysqli_query($connect, $get_unprocessed);
    $count = 1;
    while($result = mysqli_fetch_assoc($run_query1)){
    	if($count==2){
    		break;
    	}
    	$t1 = microtime(true);
    	$from = $result['name'];
    	$url = "https://en.wikipedia.org".$result['name'];
    	echo 'page parsing: <a href="'.$url.'" target="_blank">  '.$url.'</a><br>';
    	$outgoing=0;
    	$unique=0;
    	$html = new simple_html_dom();
    	$main = file_get_html($url);
    	$t2 = microtime(true);
    	$gettingPage = $t2-$t1;
    	echo 'page fetch time: '.$gettingPage.'<br>';
        $isEditable=false;
        foreach ($main->find('div') as $key ) {
			# code...
			if($key->class=='mw-content-ltr'){
				$wordCount = str_word_count($key->plaintext);
				echo 'words: '.$wordCount.'<br>';
			}
		}
		//ob_flush();
        //flush();
    	foreach ($main->find('script') as $key) {
    		# code...
    		if(strpos($key, "\"wgIsProbablyEditable\":true")!==false){
    			$isEditable=true;
    		}
    		if(strpos($key, "wgArticleId")!==false){
    			$pos1 = strpos($key, "wgArticleId");
    			$article = substr($key, $pos1+13,10);
    			$articleId = explode(",", $article);
    			$article = $articleId[0];
    			echo $article.'<br>';
    		}
    	}
    	if($isEditable==true){
    		$isEditable=2;
    	}else{
    		$isEditable=1;
    	}
    	$parsingLink = 0;
    	$linkCount = 0;
    	$myArray = array();
    	$t1=microtime(true);
    	foreach ($main->find('a') as $key) {
    		if(strpos($key->href, "/wiki/")===0
    			&&strpos($key->href, ":")===false
    			&&strpos($key->href, "#")===false
    			&&$key->plaintext!=''
    			&&$key->plaintext!='Read'
    			&&$key->plaintext!='Main page'
    			&&$key->plaintext!='Article'
    			&&$key->plaintext!='Main Page'
    			&&$key->plaintext!='free'
    			&&$key->plaintext!='Full Article...'
    			&&$key->plaintext!='Full article...'
    			&&$key->plaintext!='Wikipedia'){
    			
    			$title = $key->title;
    			$title = mysqli_real_escape_string($connect,$title);
    			$to = $key->href;
    			$to = mysqli_real_escape_string($connect,$to);
    			$from = $result['name'];
    			$from = mysqli_real_escape_string($connect,$from);
    			$isFound=false;
                foreach ($myArray as $key) {
                    if($key==$to){
                        $isFound=true;
                    }
                }
                
                if(!$isFound){
                	$outgoing+=1;
                    $myArray[]=$to;
                    $page1_query = "INSERT INTO `pages`(`title`, `name`) VALUES('$title','$to') ON DUPLICATE KEY UPDATE incoming=incoming+1";
                    $query_run = mysqli_query($connect ,$page1_query) or die('error3');
		    		if(mysqli_affected_rows($connect)==1){
		    			$unique+=1;
		    		}
                }
    		}
    	}
    	
    	$link1_query = 'INSERT INTO `links`(`from`, `to`) VALUES ';
    	foreach ($myArray as $to) {
    		$to = mysqli_real_escape_string($connect,$to);
    		$link1_query .= " ('$from','$to') ,";
    	}
    	$link1_query = rtrim($link1_query, ",");
    	$run_link_query = mysqli_query($connect,$link1_query) or die(mysqli_error($connect));
    	$t2 = microtime(true);
    	$parsingLink=$t2-$t1;
    	$percentUnique = ($unique/$outgoing)*100;
    	$parsingLink/=$outgoing;
    	$megaTime2 = microtime(true);
    	$megaTime = $megaTime2-$megaTime1;
    	$stat_query = "INSERT INTO `crawlingStats`(`name`, `gettingPage_time`, `processing1Link`, `totalTime`,`outgoing`,`unique`) VALUES ('$from','$gettingPage','$parsingLink','$megaTime','$outgoing','$percentUnique')";
    	$query_run = mysqli_query($connect,$stat_query) or die(mysqli_error($connect));
    	$query = "UPDATE `pages` SET `outgoing`='$outgoing', `processed` = 1,`editable`='$isEditable', `word_count`='$wordCount',`articleID` = '$article' WHERE `name` = '$from'";
    	$query_run = mysqli_query($connect,$query) or die(mysqli_error($connect));
    	mysqli_close($connect);
    	$count=2;
    }
    echo 'Net time taken.:  '.$megaTime.'<br>';
    echo 'Average time per link : '.$parsingLink.'<br>';
    echo 'Total unique outgoing: '.$outgoing.'<br>';
    echo 'unique percent:  '.$percentUnique.'<br>';
?>

<html>
<body onload="foo()"></body>

<script type="text/javascript">
	function foo(){
		location.reload();
	}
</script>
</html>