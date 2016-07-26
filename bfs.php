<?php 

	ini_set('display_errors', 1);
	header( 'Content-type: text/html; charset=utf-8' );
	header('Connection: close');
    header('Content-length: '.ob_get_length());
	include_once('simple_html_dom.php');
    $connect = mysqli_connect('localhost','root','braze') or die('error1');
    mysqli_select_db($connect,'wikipedia') or die('error2');
    
    function getArticleId($name){
        //$connect = mysqli_connect('localhost','root','braze') or die('error1');
        $sql = "SELECT `articleID` FROM `pages` WHERE `name` = '$name'";
        $run_sql = mysqli_query($GLOBALS['connect'],$sql);
        $res = mysqli_fetch_assoc($run_sql);
        $id = $res['articleID'];
        return $id;
    }

    function getArticleName($id){
        //$connect = mysqli_connect('localhost','root','braze') or die('error1');
        $sql = "SELECT `name` FROM `pages` WHERE `articleID` = '$id'";
        $run_sql = mysqli_query($GLOBALS['connect'],$sql);
        $res = mysqli_fetch_assoc($run_sql);
        $name = $res['name'];
        return $name;
    }

    $to = $_GET['to'];
    $from = $_GET['from'];
	
    $visited = array();
    for($i=0;$i<5100000;$i++){
        $visited[$i]=-1;
    }
    $q = new SplQueue();
    $sid = getArticleId($to);
    $visited[$sid] = 0;
    $q->enqueue($sid);
    while(!$q->isEmpty()){
        $pid = $q->dequeue();
        $name = getArticleName($pid);
        if($name==$from){
            echo 'link found';
            break;
        }
        $getOutgoing = "SELECT `to` FROM `links` WHERE `from` = '$name'";
        $run_getOutgoing = mysqli_query($connect,$getOutgoing);
        while($result = mysqli_fetch_assoc($run_getOutgoing)){
            $outgoingName = $result['to'];
            $outgoingId = getArticleId($outgoingName);
            if($visited[$outgoingId]==-1){
                $q->enqueue($outgoingId);
                $visited[$outgoingId]=0;
            }
        }

    }
    echo 'done';
    /*$get_articleId = "SELECT `` FROM `pages` WHERE `processed`=1 and `articleID`=-1";
    $run_get_articleID = mysqli_query($connect,$get_articleId);
    while ($result = mysqli_fetch_assoc($run_get_articleID)) {
    	
    }*/
	?>