<body>
<head>
    <title>MT-EQuAl: a Toolkit for Human Assessment of Machine Translation Output</title>
    <link href="css/mtequal.css" rel="styleSheet" type="text/css">
</head>
<?php
header("Content-type: text/html; charset=utf-8");
include("config.php");
include("functions.php");

#debug
if (DEBUG == "yes") {
	print"<div style='float: right; right: 20px; display: inline; position: absolute'>";
	if (isset($mysession)) {
		while (list ($key, $value) = each($mysession)) {
			print $key.": <b>" .$value."</b><br>\n";
		}
	}
	print "</div>";
}

# user permission
if (!empty($login) && !empty($password)) { 
	$query = "SELECT id,tasks,status FROM user WHERE username='$login' AND password='$password' AND activated='Y'";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$mysession = array ("username"=>$login, 
						"userid"=>$row["id"],
						"status"=>$row["status"],
						"tasks"=>$row["tasks"],
						"tasknow"=>"",
						"taskid"=>0,
						"tasksysnum"=>0,
						"tasktype"=>"",
						"taskranges"=>"",
						"sessionid" => session_id());
	} else {
		print "<div class='error'><font color=red><b>Warning!</b></font> The login or password is not valid.</div>";
	}
} else if (!empty($taskid)) {
	$taskinfo = getTaskInfo($taskid);
	$mysession["taskid"] = $taskid;
	$mysession["tasknow"] = $taskinfo["name"];
	$mysession["tasksysnum"] = countTaskSystem($taskid);
	$mysession["tasktype"] = $taskinfo["type"];
	$mysession["taskistr"] = $taskinfo["instructions"];
	$mysession["taskranges"] = rangesJson2Array($taskinfo["ranges"]);
	//print "taskranges: " .count($mysession["taskranges"]);
} else {
	if (!isset($mysession) || isset($logout)) {
	    #echo "<script> alert('user anonymous!'); </script>";
   		$mysession = array ("username"=>FALSE, 
							"status"=>"",
							"tasks"=>"",
							"tasknow"=>"",
							"taskid"=>0,
							"sessionid" => session_id());
	}
}
if (isset($_SESSION)) {
	$_SESSION["mysession"] = $mysession;
} else {
	session_register("mysession"); 
}			
include("menu.php");

if ($mysession["taskid"] > 0 && isset($mysession["userid"])) {
	$source_sentences = getSourceSentences($mysession["taskid"]);
	if (count($source_sentences) == 0) {
		print "<div class='error'><font color=red><b>Sorry!</b></font> No sentences have been found for this task.</div>";
		exit;
	}
	$done_sentences = getDoneSentences($mysession["taskid"], $mysession["userid"]);
	#$error_sentences = getErrorSentences($mysession["taskid"]);
	print "<div class='error'>&nbsp;&nbsp; <b>".count($done_sentences) . "</b>/<b>" . count($source_sentences) ."</b> <small>evaluated items</div><div class=index><br>";

	$i=1;
	while (list($k,$arr) = each($source_sentences)) {
		$done = "";
		$countAnnotations = countSentenceAnnotations($k,$mysession["userid"]);
		if ($countAnnotations > 0) {
			$done = "<div style='border: solid #FF6666 2px; left:50px; padding: 1px; display: inline'><i>".$countAnnotations."/".$mysession["tasksysnum"]."</i></div>";
			if (in_array($k, $done_sentences))
				$done ="<img src='img/done.png' width=16>";
			#if (in_array($k, $error_sentences))
			#	$done.="<img src='img/check_error.png' width=16>";
		}
		print "<a name='$k'><div class=row><div class=sentindex>$done <strong>$i.</strong> </div>";
		
		print showSentence($arr[0], "<a href='".$mysession["tasktype"].".php?id=$k&sentidx=$i&taskid=".$mysession["taskid"]."'>".$arr[1]."</a>","none",0); 
		print "</div><p>";
		$i++;
	}
} else {
	print "<div style='margin-left: 60px'><div class=index><center>";
	if (empty($mysession["status"])) {
            print "<h3>This is an end user interface for the evaluation of Machine Translation systems</h3>\n<p>Sign in, please!</p>";
	} else if (empty($mysession["tasknow"])) {
		if ($mysession["status"] == "admin" || $mysession["status"] == "advisor") {
			print "<br>Welcome " .$mysession["username"]."!";
		} else {
			print "<br><h4>Welcome! Choose a task to start your work.</h4>";
		}
	} 
	print "</center></div></div>";
}
?>
</div>
</div>
</body>