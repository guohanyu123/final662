<?php 

	// find all group member 
	// if they have summaries, show them
	// only evaluate this month
	// can change evaluation for this month ( or can't?)
	
	session_start();
    include $_SERVER['DOCUMENT_ROOT'] . '/includes/db.inc.php';
	


	$target_dir =  "../../summary/uploadfile";

	try
	{
		$sql = 'SELECT * FROM user_info LEFT JOIN (SELECT * FROM summary WHERE datediff(last_day(curdate()),time)<31 AND datediff(last_day(curdate() ),time )>0)AS T1 ON 
				user_info.userid = T1.userid WHERE user_info.userid != :userid AND groupid =:groupid AND user_info.userid NOT IN (SELECT userid FROM evaluation WHERE userid2 = :userid)';
		$s = $pdo->prepare($sql);
		$s->bindValue(':userid',$_SESSION['userid']);	
		$s->bindValue(':groupid',$_SESSION['groupid']);	
		$s->execute();
	}
	catch (PDOException $e){
		echo $e;
		header("Location: /includes/error.html.php");
		exit(); 
	}

	$names = $groupthismonthsummaries = array();
	while($result = $s->fetch()){
		$names[] = array($result['username'],searchFile($target_dir, $result["title"] ));
	}





	include 'evaluation.html.php';



	function searchFile($dir, $keyword) {
	  $sFile = getFile($dir);
	  if (count($sFile) <= 0) {
	    return false;
	  }
	  // $sResult = array();
	  foreach ($sFile as $file) {
	    if(strstr($file, $keyword) !== false ){
	      	// $sResult[] = $file;
	    	$sResult = $file;
	    }
	  }
	  if (count($sResult) <= 0) {
	    return false;
	  } else {
	    return $sResult;
	  }

	}

	function getFile($dir){
	  $dp = opendir($dir);
	  $fileArr = array();
	  while (!false == $curFile = readdir($dp)) {
	    if ($curFile!="." && $curFile!=".." && $curFile!="") {
	      if (is_dir($curFile)) {
	        $fileArr = getFile($dir."/".$curFile);
	      } else {
	        $fileArr[] = $dir."/".$curFile;
	      }
	    }
	  }
	  return $fileArr;
	}


?>