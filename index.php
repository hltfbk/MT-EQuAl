<?php
@header("Content-type: text/html; charset=utf-8");
include("config.php");
include("functions.php");
?>
<head>
    <title>MT-EQuAl: a Toolkit for Human Assessment of Machine Translation Output</title>
    <link href="css/mtequal.css" rel="styleSheet" type="text/css">
    
<style>
div.error {background: #fff; z-index:1; position:fixed; top:43px; left:100px; border-left: solid 1px #000; border-bottom: solid 1px #666; padding: 3px; font-size: 16px; margin-bottom: 0px; border-radius: 0px 0px 0px 15px;}
div.sentindex {display: cell; margin-left: 16px; width: 60px; font-size: 13px; padding-top: 5px; text-align: right; vertical-align: top; padding-right: 4px; top: 10px}
div.source {border: solid #bbb 1px; font-weight: bold; display: inline-block; font-family:Arial, Helvetica, Sans-Serif; font-size:15px; padding: 4px; padding-bottom: 8px; width: 575px; border-radius: 4px 4px 4px 4px;}
div.reference {margin-left: 2px; display: inline-block; font-family:Arial, Helvetica, Sans-Serif; font-size:15px; color: #888; padding: 4px; margin-top: 8px; padding-bottom: 10px;  width: 575px;}
div.output {margin-left: 2px; display: inline-block; font-family:Arial, Helvetica, Sans-Serif; font-size:15px; margin-top: 8px; margin-right: 8px; width: auto; border: solid #aaa 1px; padding: 4px; top: -10px}

div.head {border-top: solid #000 1px;}
div.rtl {direction:rtl;}
div.label {display: table-cell; white-space: nowrap; padding-top: 5	px; padding-right: 3px; text-align: right; width: 100px; font-size: 14px}
div.labelref {display: table-cell; white-space: nowrap; padding-top: 5px; padding-right: 3px; text-align: right; width: 100px; font-size: 14px; padding-bottom: 9px; color: #888}
div.check {display: inline-block; margin-top: 13px; padding-right: 10px; width: 11px; text-align: center; vertical-align: top}
div.donebottom {float: right; background: #cde; right: 0px; bottom: -3px; display: inline-block; position: fixed; text-align: left; font-size: 14px; padding: 5px; margin-right: 40px; border: solid #999 1px; border-radius: 15px 15px 0px 0px; z-index: 1000}
div.bglgreen {background: lightgreen; }
div.log {bottom: 30px; right: 20px; display: block; position: fixed; font-size: 10px}
div.debug {float: right; right: 0px; bottom: 30px; background: lightyellow; padding: 3px; border: solid #999 1px; display: inline; position: fixed}

div.orangeborderb {display: inline-block; cursor:pointer; cursor: hand; border-top: solid orange 2px;}
div.whiteborderb {display: inline-block; cursor:pointer; border-top: solid white 2px;}
div.orangebg {display: inline-block; cursor: pointer; cursor: hand; background: orange;}
div.whitebg {display: inline-block; cursor:default; background: white;}

div.token {display: inline-block; margin-bottom: 0px; border-top: solid white 2px;}

td.black { background-color: black; }
td.white { background-color: #ccc; }
td.gray  { background-color: gray; }
td.highlight  { background-color: #000; }
td.blue {background-color: blue}
td.yellow {background-color: lightyellow}
td.red {background-color: red}

div.container {
	width: 100%;
	display: table ;
}


div.left {
    width:150px;
}

div.right {
    display: table-cell;
    float: left;
}

option.lineseparator { border-bottom: 2px solid #000; }
    
.nowhitespace { font-size: 0; }
.nowhitespace > span { font-size: 16px; }

</style>
</head>

<?php
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
if (!empty($login) && isset($password)) { 
  if ($login==ROOTNAME && $password==ROOTPWD) {
  	$mysession = array ("username"=>$login, 
						"userid"=>0,
						"status"=>"root",
						"tasknow"=>"",
						"taskid"=>0,
						"tasksysnum"=>0,
						"tasktype"=>"",
						"taskranges"=>"",
						"sessionid" => session_id());
  } else {
	$query = "SELECT id,status FROM user WHERE username='$login' AND password='$password' AND activated='Y'";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$mysession = array ("username"=>$login, 
						"userid"=>$row["id"],
						"status"=>$row["status"],
						"tasknow"=>"",
						"taskid"=>0,
						"tasksysnum"=>0,
						"tasktype"=>"",
						"taskranges"=>"",
						"sessionid" => session_id());
	} else {
		print "<div class='error'><font color=red><b>Warning!</b></font> The login or password is not valid.</div>";
	}
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

if ($mysession["taskid"] > 0 && isset($mysession["userid"]) && $mysession["status"] != "root") {
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
		if ($mysession["tasktype"] == "docann") {
			print "<div class='cell none'><a href='".$mysession["tasktype"].".php?id=$k&sentidx=$i&taskid=".$mysession["taskid"]."'>".$arr[2]."</a></div>";
		} else {
			print showSentence($arr[0], "<a href='".$mysession["tasktype"].".php?id=$k&sentidx=$i&taskid=".$mysession["taskid"]."'>".$arr[1]."</a>","none",0); 
		}
		print "</div></p>";
		$i++;
	}
} else {
	print "<div class=index><center>";
	if (empty($mysession["status"])) {
            print "<h3>This is an end user interface for the evaluation of Machine Translation systems</h3>\n<p>Sign in, please!</p>";
	} else if (empty($mysession["tasknow"])) {
		if ($mysession["status"] == "admin" || $mysession["status"] == "advisor") {
			print "<br>Welcome " .$mysession["username"]."!";
		} else {
			print "<br><h4>Welcome! Choose a task to start your work.</h4>";
		}
	} 
	print "</center></div>";
}
?>
<br><br>
</html>