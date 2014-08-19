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

<div style='margin: 10px;'>
<div style='padding: 10px; width: 100%; margin-right: 40px; font-size: 14px'>Export all annotations: <a class="ziplink" href="export.php?format=csv">
<button onclick="javascript:showSpinner();">CSV</button></a>
<a class="ziplink" href="export.php?format=xml"><button onclick="javascript:showSpinner();">XML</button></a>
<span style="display:none;" class="spinner"><img width=25 src="img/spinner.gif" valign=bottom></span>
</div>

<?php
## UPDATE ANNOTATIONS
if (isset($userid) && isset($taskid)) {
	if ($mysession["status"] == "admin" && isset($action) && $action="remove") {
		print "<script>alertify.log(\"Removing user annotations... (task: $taskid, user: $userid)\");</script>"; 
  		deleteAnnotations($taskid,$userid);
  	}
 }
 
	$hash_users = getUserStats();
	$donecounter = getDoneUserStats();
	$error_export ="";
		
		print "<table cellspacing=0 cellpadding=2 border=1 valign=top><tr bgcolor=#ccc><th>User</th><th>N.ann</th><th>N.pages</th><th>Last annotations</th><th>DB Check</th><th>Annotated tasks</small></th></tr>\n";
	while (list ($user_id, $useritems) = each($hash_users)) {
		$checkdone = "";
				
		#controllo che se ci sono dei done ci siano le annotazioni (almeno una, per cominciare)
		$hash_errors = getDBInconsistency($user_id,split(" ",$useritems[2]));
		if (count($hash_errors) >0) {
			$errid_count=0;
			
			while (list ($sentid, $infoarr) = each($hash_errors)) {
				if (strpos($sentid, "DONE!", 0) === 0) {
					$checkdone = "<img border=0 src='img/database_error.png' title='WARNING! Some done are missed!'> <small>Check <b>DONE! in task ".$infoarr[0]."</b>: ".$infoarr[1]. "</small><br>". $checkdone; 
				} else {
					$errid_count++;
					if ($errid_count == 1) {
						$checkdone .= "<br><img border=0 src='img/database_error.png' title='WARNING! Some annotations were not saved correctly.'> <small>Check these incompleted annotations or errors: </small><br>";
						#$checkdone .= " <small>".count($hash_errors) ." severe errors: </small>";
					}
				
					$checkdone .= "<a href='".$tasks[$infoarr[0]][1].".php?id=$sentid&userid=$user_id&taskid=".$infoarr[0]."'>$sentid</a>";
					if ($infoarr[1] != "") {
						$checkdone .= " <small>(".$infoarr[1].")</small>";
					}
					$checkdone .= ", ";
					#$error_export .= "$user_id $sentid\n";
				}
			}
		} else {
			$checkdone = "<img src='img/done.png' width=14 title='DB consistency checking passed!'> ";
		}
			
		print "<tr><td valign=top nowrap><a name='user$user_id'>#$user_id ".$useritems[0].":<br><small><b>".$useritems[1]."</b></small></td><td valign=top align=right>".$useritems[3]."</td>";
		print "<td valign=top align=right>";
		if (isset($donecounter[$user_id]) && $donecounter[$user_id] > 0) {
			print $donecounter[$user_id];
		} else {
			print "0";
		}		
		print "</td>";
		
		#last annotations
		print "<td valign=top align=right nowrap>";
		$hash_lastannotations = getUserLastAnnotations($user_id,20);
		while (list ($sentenceID, $array_items) = each($hash_lastannotations)) {
			if (array_key_exists($array_items[0], $tasks)) {
				print "<a href='".$tasks[$array_items[0]][1].".php?id=$sentenceID&userid=$user_id&taskid=".$array_items[0]."'>$sentenceID</a>, <small>task: ".$array_items[0]." - <i>".nicetime($array_items[1])."</small></i><br>";
			}
		}
		print "&nbsp;</td>";
		#annotation/done consistency checking
		print "<td valign=top>$checkdone</td>\n";
		#assigned tasks
		print "<td valign=top>";
		
		#annotated tasks
		$ltasks = getAnnotatedTasks($user_id);
		if (count($ltasks) > 0) {
		print "<table cellspacing=0 cellpadding=0>\n";
		while (list ($tid, $acount) = each($ltasks)) {
			print "<tr><td><li type=square></td><td align=right><small>$tid.</small></td><td><small>&nbsp;".getTaskName($tid)."</small></td>\n<td>";
			#if (array_key_exists($tid ."-".$user_id,$anntaskusercounter)) {
			#if ($acount > 0) {	
				print ": <span style='cursor: nw-resize' title=\"there are $acount sentence/error type  annotations\">$acount</span> <a href=\"javascript:delAnnotations($tid,$user_id,'".$useritems[0]."');\"><img title=\"delete ".$useritems[0]."'s annotations from this task\" border=0 width=12 src='img/remove.png'></a>";
				
			#}
			print "</td></tr>\n";
		}
		print "</table>";
		
		if ($useritems[3] > 0) {
			print "<hr><small>Export: <a class=\"ziplink\" href=\"export.php?format=csv&userid=$user_id\"><button onclick=\"javascript:showSpinner();\">CSV</button></a>
<a class=\"ziplink\" href=\"export.php?format=xml&userid=$user_id\"><button onclick=\"javascript:showSpinner();\">XML</button></a></small>";
		}
		print "<hr>";
	  }
	  
	  $taskAndUser = getTaskAndUsers($user_id);
	  print "<small>Duplicate annotation from task: <small> <select name=taskuser onchange=\"javascript:duplicateAnnotation(this, $user_id);\">";
     	$prev_task = -1;
     	while (list ($taskid, $userids) = each($taskAndUser)) {
     		$lusers = split(" ",$userids);
     		foreach ($lusers as $uid) {
     			if ($prev_task != $taskid) {
     				if ($prev_task > -1) {
     					print "<option value='' disabled='disabled'>───────────────";
     				} else {
     					print "<option value='' disabled='disabled'>";
     				}
     				$prev_task = $taskid;
     			}	
     			if (array_key_exists($taskid, $tasks)) {
					print "<option value='$taskid,$uid'>".$tasks[$taskid][0]." (id: $taskid) done by ".$hash_users[$uid][0]."\n";
				}
			}
		}
	  print "</select></td></tr>\n";
	}
	print "</table>";
	
	print "<PRE><font color=white>$error_export</font></PRE>"; 
?>
</div>