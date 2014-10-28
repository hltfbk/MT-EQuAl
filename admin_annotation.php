<!--
MT-EQuAl: a Toolkit for Human Assessment of Machine Translation Output

Copyright 2014, Christian Girardi (cgirardi@fbk.eu)

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
-->

<?php
if ($mysession["status"] != "root" && $mysession["status"] != "admin" && $mysession["status"] != "advisor") {
	exit;
}
?>
<div style='margin: 10px;'>
<form action="javascript:void(0)" method="GET"> 
<div style='width: 100%; font-size: 14px'> Export 
<select id=taskselection name='taskid'><option value='' disabled='disabled'>
<?php
if (isset($mysession)) { 
	$tasks = getTasks($mysession["userid"]);
	
	$ttype = "";
	while (list ($tid,$tarr) = each($tasks)) {
		if ($tarr[1] != $ttype) {
			$ttype = $tarr[1];
			print "<option value='' disabled='disabled'>--- ".ucfirst($ttype) ." tasks --- \n";
		}
		print "<option value='$tid'";
		if (isset($id) && $id == $tid) {
			print " selected";
		} 
		print "> &nbsp;".$tarr[0]."\n";
	}	
}
?>
</select>
annotations:
<button onclick="javascript:exportTask('csv');">CSV</button>
<button onclick="javascript:exportTask('xml');">XML</button></a>
<!-- <button onclick="javascript:exportTask('iob2');">IOB2</button></a> -->
<span style="display:none;" class="spinner"><img width=25 src="img/spinner.gif" valign=bottom></span>
</div>
</form>
		
<?php
## UPDATE ANNOTATIONS
if (isset($userid) && ($mysession["status"] == "root" || $mysession["status"] == "admin")) {
	if (isset($taskid) && isset($action) && $action="remove") {
		print "<script>alertify.log(\"Removing user annotations... (task: $taskid, user: $userid)\");</script>"; 
  		deleteAnnotations($taskid,$userid);
  	} else if (isset($copy) ) {
  		$taskanduser = split(",",$copy);
		if (count($taskanduser) == 2 && $taskanduser[0]>0 && $taskanduser[1] > 0) {
			$numcopy = copyAnnotations($userid, $taskanduser[0], $taskanduser[1]);
			if ($numcopy > 0) {
				print "<script>alert('The copy of $numcopy annotations have been completed.');</script>";
				#add the new task in the user's tasks
				addUserTask($userid,$taskanduser[0]);
			} else {
				print "<script>alert('Sorry, no annotation has been copied.');</script>";
			}
		} else {
			print "<script>alert('Error! The duplication of the annotations failed.');</script>";
		}
  	}
 }
 
	reset($tasks);
		
	$hash_users = getUserStats($mysession["userid"],$mysession["status"]);
	if (count($hash_users) > 0) {
	  $donecounter = getDoneUserStats();
	  $error_export ="";
		
	  print "<table cellspacing=0 cellpadding=2 border=1 valign=top><tr valign=top bgcolor=#ccc><th>User</th><th>Annotated tasks</th><th>DB Check</th><th>N.ann</th><th>N. evaluated<br>items</th><th>Last annotations</th></tr>\n";
	  while (list ($user_id, $useritems) = each($hash_users)) {
		$record = "<tr><td valign=top nowrap><a name='user$user_id'>".$useritems[0].":<br><small><b>".$useritems[1]."</b></small></td>";
		
		#annotated tasks
		$record .= "<td valign=top nowrap>";
		$ltasks = getAnnotatedTasks($user_id);
		$countTasks = 0;
		$countAnnotations = 0;
		if (count($ltasks) > 0) {
			$record .= "<table cellspacing=0 cellpadding=0>\n";
			while (list ($tid, $acount) = each($ltasks)) {
				if (array_key_exists($tid, $tasks)) {
					$countTasks++;
					$countAnnotations += $acount;
					$record .= "<tr><td><li type=square></td><td><small>&nbsp;".getTaskName($tid)."</small></td>\n<td>: <span style='cursor: nw-resize' title=\"there are $acount sentence/error type  annotations\">".$acount."</span>";
					if ($mysession["status"] == "root" || $mysession["status"] == "admin") {
						$record .= "<a href=\"javascript:delAnnotations($tid,$user_id,'".$useritems[0]."');\"><img title=\"delete ".$useritems[0]."'s annotations from this task\" border=0 width=12 src='img/remove.png'></a>";
					}
					$record .= "</td></tr>\n";
				}
			}
			$record .= "</table>";
		
			if ($countAnnotations > 0 && $countTasks > 0) {
				$record .= "<hr><small>Export: <a class=\"ziplink\" href=\"export.php?format=csv&userid=$user_id\"><button onclick=\"javascript:showSpinner();\">CSV</button></a>
<a class=\"ziplink\" href=\"export.php?format=xml&userid=$user_id\"><button onclick=\"javascript:showSpinner();\">XML</button></a></small>";
				$record .= "<hr>";
	  		}
	  		
	  		$taskAndUser = getTaskAndUsers($user_id);
	  		$duplicateTaskAndUser="";
			 
     		$prev_task = -1;
     		while (list ($taskid, $userids) = each($taskAndUser)) {
     			$lusers = explode(" ",$userids);
    	 		foreach ($lusers as $uid) {
     				if (array_key_exists($uid, $hash_users) && array_key_exists($taskid, $tasks)) {
						if ($prev_task != $taskid) {
     						if ($prev_task > -1) {
     							$duplicateTaskAndUser .= "<option value='' disabled='disabled'>───────────────";
   	  						} 
		     				$prev_task = $taskid;
    	 				}	
     					$duplicateTaskAndUser .= "<option value='$taskid,$uid'>".$tasks[$taskid][0]." (".$tasks[$taskid][1].") done by ".$hash_users[$uid][0]."\n";
					}
				}
			}
			if ($duplicateTaskAndUser != "" && ($mysession["status"] == "root" || $mysession["status"] == "admin")) {
				$record .= "<small>Duplicate annotation from task:</small></br><select name=taskuser onchange=\"javascript:duplicateAnnotation(this, $user_id);\"><option value=''>\n".$duplicateTaskAndUser."</select>\n";
			} 
	  		$record .= "</td>";
	  	
	  	}
	  	
			
		#annotation/done consistency checking
		#controllo che se ci sono dei done ci siano le annotazioni (almeno una, per cominciare)
		$checkdone = "";
		if ($countAnnotations > 0) {
			$hash_errors = getDBInconsistency($user_id, array_keys($ltasks));
			if (count($hash_errors) >0) {
				$errid_count=0;
				while (list ($sentid, $infoarr) = each($hash_errors)) {
					if (array_key_exists($infoarr[0], $tasks)) {
						if (strpos($sentid, "DONE!", 0) === 0) {
							$checkdone = "<img border=0 src='img/database_error.png' title='WARNING! Some annotations are not confirmed'> <small>In the task <b>".getTaskName($infoarr[0])."</b> ".$infoarr[1]. "</small><br>". $checkdone; 
						} else {
							$errid_count++;
							if ($errid_count == 1) {
								$checkdone .= "<img border=0 src='img/database_error.png' title='WARNING! Some annotations were not saved correctly.'> <small>Check these incomplete annotations or errors: </small><br>";
								#$checkdone .= " <small>".count($hash_errors) ." severe errors: </small>";
							} else {
								$checkdone .= ", ";
							}
							$checkdone .= "<a href='".$tasks[$infoarr[0]][1].".php?id=$sentid&userid=$user_id&taskid=".$infoarr[0]."'>$sentid</a>";
							if ($infoarr[1] != "") {
								$checkdone .= " <small>(".$infoarr[1].")</small>";
							}
							#$error_export .= "$user_id $sentid\n";
							if ($errid_count > 30) {
								$checkdone .= "<small> ... and more</small>";
								break;
							}
						}			
					}
				}
			} 
		}
		if (empty($checkdone)) {
			$record .="<td bgcolor=#B2FF99><img src='img/done.png' width=14 title='DB consistency checking passed!'></td>";
		} else {	
			$record .= "<td valign=top bgcolor=lightyellow>$checkdone</td>\n";
		}
		#number of annotation
		$record .= "<td valign=top>$countAnnotations</td>";
	  	
	  	#Done!
	  	$record .= "<td valign=top align=right>";
		if (isset($donecounter[$user_id]) && $donecounter[$user_id] > 0) {
			$record .= $donecounter[$user_id];
		} else {
			$record .= "0";
		}		
		$record .= "</td>";
		
		#last annotations
		$record .= "<td valign=top align=right nowrap>";
		$hash_lastannotations = getUserLastAnnotations($user_id,20);
		while (list ($sentenceID, $array_items) = each($hash_lastannotations)) {
			if (array_key_exists($array_items[0], $tasks)) {
				$record .= "<a href='".$tasks[$array_items[0]][1].".php?id=$sentenceID&userid=$user_id&taskid=".$array_items[0]."'>$sentenceID</a>, <small>task: ".$array_items[0]." - <i>".nicetime($array_items[1])."</small></i><br>";
			}
		}
		$record .= "&nbsp;</td>";
		
		
		$record .= "</tr>\n";
		print $record;
	  }
	  print "</table>";
	  print "<PRE><font color=white>$error_export</font></PRE>"; 
	} else {
		print "WARNING! There aren't any annotations for the available tasks.";
	}
?>
</div>
