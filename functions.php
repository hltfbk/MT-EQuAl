<?php
/*
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
*/

# get user annotation statistics
function getUserStats() {
	$hash = array();
	$query = "select id,username,name,tasks,count(*),activated from user left join annotation on annotation.user_id=user.id group by id order by id;";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_row($result)) {
			$counter = $row[4];
			if ($counter == 1) {
				$counter = 0;
			}
			$hash[$row[0]] = array($row[1],$row[2],$row[3],$counter,$row[5]);
		}
	}
	return $hash;
}

function getUserInfo($userid) {
	$query ="SELECT * FROM user WHERE id='$userid'";
	$result = safe_query($query);
	if (mysql_num_rows($result) == 1) {
		return mysql_fetch_array($result);
	}
	return array();
}

function getTaskInfo($taskid) {
	$query ="SELECT * FROM task WHERE id='$taskid'";
	$result = safe_query($query);
	if (mysql_num_rows($result) == 1) {
		return mysql_fetch_array($result);
	}
	return array();
}


#remove user info, and all down annotations 
function removeUser($userid) {
	$query ="DELETE FROM annotation WHERE user_id=$userid";
	if (safe_query($query) == 1) {
		$query ="DELETE FROM comment WHERE user_id=$userid";
		if (safe_query($query) == 1) {
			$query ="DELETE FROM done WHERE user_id=$userid";
			if (safe_query($query) == 1) {
				$query ="DELETE FROM user where id=$userid";
				if (safe_query($query) == 1) {
					return 1;
				}
			}
		}	
	}
	return 0;
}
	
#remove task info, sentences belongs to it and all user annotations 
function removeTask($taskid) {
	$query ="DELETE annotation FROM annotation left join sentence ON annotation.sentence_num=sentence.num WHERE task_id=$taskid";
	if (safe_query($query) == 1) {
		$query ="DELETE comment FROM comment left join sentence ON comment.sentence_num=sentence.num WHERE task_id=$taskid";
		if (safe_query($query) == 1) {
			$query ="DELETE done FROM done left join sentence ON done.sentence_num=sentence.num WHERE task_id=$taskid";
			if (safe_query($query) == 1) {
				$query ="DELETE FROM sentence WHERE task_id=$taskid";
				if (safe_query($query) == 1) {
					$query ="DELETE FROM task where id=$taskid";
					if (safe_query($query) == 1) {
						return 1;
					}
				}
			}
		}	
	}
	return 0;
}
	
function getAnnotationTaskStats() {
	$query = "select task_id,count(*) from annotation left join sentence on annotation.sentence_num=sentence.num group by task_id";
	
	$result = safe_query($query);	
	$hash = array();
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_row($result)) {
			$hash[$row[0]] = $row[1];
		}
	}
	return $hash;
}

#check if there are some annotation for a particular task
function isAnnotatedTask ($taskid) {
	$query="select distinct sentence_num,user_id from annotation left join sentence on annotation.sentence_num=sentence.num WHERE task_id=$taskid";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		return 1;
	}
	return 0;
}

function getDoneTaskStats() {
	$query = "select task_id,count(*) from done left join sentence on done.sentence_num=sentence.num group by task_id";
	
	$result = safe_query($query);	
	$hash = array();
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_row($result)) {
			$hash[$row[0]] = $row[1];
		}
	}
	return $hash;
}

function getDoneUserStats() {
	$query = "select user_id,count(*) from done group by user_id";
	
	$result = safe_query($query);	
	$hash = array();
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_row($result)) {
			$hash[$row[0]] = $row[1];
		}
	}
	return $hash;
}



#get last N sentence annotated by the users
# foreach sentence returns the sentence ID (as key of the hash), and the task ID and the last modified time (as array)
function getUserLastAnnotations($user_id, $limit = 2) {
	$query = "SELECT DISTINCT sentence_num,task_id,annotation.lasttime from annotation LEFT JOIN sentence ON annotation.sentence_num=sentence.num WHERE user_id=$user_id and task_id!='' group by sentence_num order by annotation.lasttime desc limit $limit;";
	
	$result = safe_query($query);	
	$hash = array();
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_row($result)) {
			$hash[$row[0]] = array($row[1], $row[2]);
		}
	}
	return $hash;
}

# get sentence mapping between the source ID and the internal MySQL one
function getSourceSentenceIdMapping($task_id) {
	$query = "SELECT id,num FROM sentence WHERE task_id=$task_id AND type='source'";
	$result = safe_query($query);	
	$hash = array();
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_row($result)) {
			$hash[$row[0]] = $row[1];
		}
	}
	return $hash;
}


# get info about a source sentence: text, reference, ..
function getSentence($num) {
	$query = "SELECT type,lang,text FROM sentence WHERE num='$num' OR (linkto='$num' AND type='reference');";
	$result = safe_query($query);	
	$hash = array();
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_array($result)) {
			$hash[$row["type"]] = array($row["lang"],$row["text"]);
		}
	}
	return $hash;
}

#get annotaion task: quality
function getQuality($sentence_num,$output_id,$user_id) {
	$query = "SELECT eval FROM annotation WHERE sentence_num='$sentence_num' AND output_id='$output_id' AND user_id=$user_id;";
	$result = safe_query($query);	
	#print mysql_num_rows($result)  . " -- ".$query;
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		return $row["eval"];
	}
	return -1;
}

#get annotaion task: errors
function getErrors($sentence_num,$output_id,$user_id) {
	$query = "SELECT eval,evalids,evaltext FROM annotation WHERE sentence_num=$sentence_num AND output_id=$output_id AND user_id=$user_id;";
	$result = safe_query($query);	
	//print mysql_num_rows($result)  . " -- ".$query;
	$hash_error = array();
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			$hash_error[$row["eval"]] = array($row["evalids"],$row["evaltext"]);
		}
	}
	return $hash_error;
}

#duplicate annotations: copy the annotations from taskid and fromuserid to the current userid (=curruserid)
function copyAnnotations ($curruserid, $taskid, $fromuserid) {
	$query="insert into annotation select sentence_num, output_id, $curruserid, eval, evalids, evaltext, now() from annotation left join sentence on annotation.sentence_num=sentence.num where user_id=$fromuserid and task_id=$taskid";
	mysql_query($query) or print ("ERROR! Records copying failed. (" . mysql_error() .")");	
	return mysql_affected_rows();
}

function addUserTask ($userid, $taskid) {
	$query = "SELECT tasks FROM user WHERE id=$userid";
	$result = safe_query($query);
	if (mysql_num_rows($result) == 1) {
		$row = mysql_fetch_row($result);		
		$tasks = $row[0];
		$taskname = getTaskName($taskid);
		if (strlen($taskname) > 0 && !strstr(" $tasks ", " $taskname ")) {
			$query = "UPDATE user SET tasks='$tasks $taskname' WHERE id=$userid";
			safe_query($query);
		}
	}
}

function countSentenceAnnotations($sentence_num,$user_id = null) {
	$query = "SELECT distinct output_id FROM annotation WHERE sentence_num='$sentence_num' AND eval >= 0";
	if ($user_id != null) {
		$query .= " AND user_id=$user_id;";
	}
	#print $query;
	$result = safe_query($query);	
	return mysql_num_rows($result);
}
		
function getAnnotatedTasks ($user_id) { 
	$hash = array();
	$query="select task_id,count(*) from annotation,sentence where annotation.sentence_num=sentence.num and user_id=$user_id group by task_id order by task_id";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_row($result)) {
			$hash[$row[0]] = $row[1];
		}
	}
	$userinfo = getUserInfo($user_id);
	if ($userinfo["status"] != "admin") {
		foreach (split(" ",$userinfo["tasks"]) as $task) {
			if (trim($task) != "") {
				$tid = getTaskID(trim($task));
				if (!array_key_exists($tid, $hash)) {
					$hash[getTaskID(trim($task))] = 0;
				}
			} 
		}
	}
	return $hash;
}

#get uniq pair task,user for annotation	except the user user_id
function getTaskAndUsers ($user_id) { 
	$hash = array();
	$query="select distinct task_id,user_id from annotation,sentence where annotation.sentence_num=sentence.num and user_id != $user_id order by task_id, user_id";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			if (array_key_exists($row["task_id"], $hash)) {
				$hash[$row["task_id"]] .= " " .$row["user_id"];
			} else {
				$hash[$row["task_id"]] = $row["user_id"];
			}
		}
	}
	return $hash ;
}

#get all annotation divided by task/user key
# task/user id | count(*)
#     1-22     |   2505
function getTaskAndUserAnnotation () { 
	$hash = array();
	$query="select concat(task_id,'-',user_id) as tu, count(*) as count from annotation, sentence where annotation.sentence_num=sentence.num group by tu order by tu";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			$hash[$row["tu"]] = $row["count"];
		}
	}
	return $hash ;
}

#save annotation task: quality
function saveQuality($source_id,$output_id,$user_id,$eval,$action="") {
	$query="";
	if ($action == "remove") {
		$evalclause="";
		if ($eval > -1) {
			$evalclause="AND eval=$eval";
		}
		$query = "DELETE FROM annotation WHERE sentence_num='$source_id' AND output_id='$output_id' AND user_id=$user_id $evalclause";		
	} else if (getQuality($source_id,$output_id,$user_id) < 0) {	
		$query = "INSERT INTO annotation (sentence_num,output_id,user_id,eval,lasttime) VALUES ('$source_id','$output_id',$user_id,$eval,now())";
		
	} else {
		$query = "UPDATE annotation SET eval='$eval',lasttime=now() WHERE sentence_num='$source_id' AND output_id='$output_id' AND user_id=$user_id;";
	}
	
	//safe_query("UPDATE done SET completed='N',lasttime=now() WHERE sentence_num='$source_id' AND user_id=$user_id;");
	return safe_query($query);		
}

#save annotation task: error
function removeError($id,$targetid,$user_id,$eval,$item) {
	$query = "SELECT evalids,evaltext FROM annotation WHERE sentence_num=$id AND output_id='$targetid' AND user_id=$user_id AND eval=$eval";
	$result = safe_query($query);	
	//print mysql_num_rows($result)  . " -- ".$query;
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_row($result);
		
		$ids = split(",",$row[0]);
		$texts = split("__BR__",$row[1]);
		for ($i=0; $i<count($ids); $i++) {
			if ($ids[$i] == $item || empty($ids[$i])) {
				unset($ids[$i]);
				unset($texts[$i]);				
			}	
		}
		#saveLog("#".join("__BR__",array_filter($texts)) ."<br>!! ".join(",",array_filter($ids)));
		$ids = array_filter($ids);
		$texts = array_filter($texts);
		
		if (count($ids) == count($texts)) {
			$query = "UPDATE annotation SET evalids='".join(",",$ids)."',evaltext=\"".addslashes(join("__BR__",$texts))."\",lasttime=now() WHERE sentence_num=$id AND output_id='$targetid' AND user_id=$user_id AND eval=$eval;";
			safe_query($query);
			
			safe_query("UPDATE done SET completed='N',lasttime=now() WHERE sentence_num='$id' AND user_id=$user_id");
	
		} else {
			return 0;
		}
	}
	return 1;
}

//reset all annotations of an error category type 
function resetErrors($id,$targetid,$user_id,$check) {
	$query = "DELETE FROM annotation WHERE sentence_num=$id AND output_id='$targetid' AND user_id=".$user_id." AND eval=$check";
	safe_query($query);
	
	safe_query("UPDATE done SET completed='N',lasttime=now() WHERE sentence_num=$id AND user_id=$user_id");
}

#save annotation task: error
function saveErrors($source_id,$output_id,$user_id,$eval,$evalids="",$evaltext="") {
	$query="";
	if ($eval == -1) {
		$query = "DELETE FROM annotation WHERE sentence_num='$source_id' AND output_id='$output_id' AND user_id=$user_id";
		safe_query($query);
	} else {
		$hash = getErrors($source_id,$output_id,$user_id);
		if (!isset($hash[$eval])) {
			$evaltext = str_replace("\"","&quot;",stripslashes(trim($evaltext)));	
			$query = "INSERT INTO annotation (sentence_num,output_id,user_id,eval,evalids,evaltext,lasttime) VALUES ('$source_id','$output_id',$user_id,$eval,'$evalids',\"$evaltext\",now())";
		} else {	
			$evaltext = str_replace("\"","&quot;",stripslashes(trim($hash[$eval][1]."__BR__$evaltext")));	
			$query = "UPDATE annotation SET evalids='".$hash[$eval][0].",$evalids',evaltext=\"$evaltext\",lasttime=now() WHERE sentence_num='$source_id' AND output_id='$output_id' AND user_id=$user_id AND eval=$eval;";
		}
		safe_query($query);
		
		$query = "DELETE FROM annotation WHERE sentence_num='$source_id' AND output_id='$output_id' AND user_id=$user_id AND eval<2";
		safe_query($query);	
	}
	
	safe_query("UPDATE done SET completed='N',lasttime=now() WHERE sentence_num='$source_id' AND user_id=$user_id");
	return 1;
}

#save comment
function saveComment($sentence_num,$user_id,$comment) {
	$query = "DELETE FROM comment WHERE sentence_num=$sentence_num AND user_id=$user_id;";
	safe_query($query);
	$comment = str_replace("\"","&quot;",stripslashes(trim($comment)));
	#if (!empty(trim($comment))) {
		$query = "INSERT INTO comment VALUES ($sentence_num, $user_id, \"$comment\")";
		#saveLog($query);
		safe_query($query);	
	#}	
	safe_query("UPDATE done SET completed='N',lasttime=now() WHERE sentence_num='$source_id' AND user_id=$user_id");
	
}

#get comment
function getComment($sentence_num,$user_id) {
	$query = "SELECT comment FROM comment WHERE sentence_num=$sentence_num AND user_id=$user_id;";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_row($result);
		return $row[0];
	}
	return "";
}

function getComments($taskid, $user_id) {
	$hash = array();
	$query = "SELECT sentence_num,id,type,comment FROM comment LEFT JOIN sentence ON sentence_num=sentence.num WHERE task_id=$taskid AND comment.user_id=$user_id";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			$hash[$row["sentence_num"]] = $row["id"]."\t".$row["type"]."\t".$row["comment"];
		}
	}
	return $hash;
}

# check if the user click for a sentence
function isDone($sentence_num,$user_id) {
	$query = "SELECT completed FROM done WHERE sentence_num=$sentence_num AND user_id=$user_id;";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_row($result);
		if ($row[0] == "Y") {
			return 1;
		} else {
			return 0;
		}
	}
	return -1;
}

#save "Y" when a user click the "DONE" button in the sentence page
function saveDone($sentence_num,$user_id,$completed) {
	$query="";
	if (isDone($sentence_num,$user_id) < 0) {
		$query = "INSERT INTO done (sentence_num,user_id,completed,lasttime) VALUES ($sentence_num,$user_id,'Y',now())";
	} else {
		$query = "UPDATE done SET completed='Y',lasttime=now() WHERE sentence_num=$sentence_num AND user_id=$user_id";
	}
	return safe_query($query);
}

# get info about the target sentences
function getSystemSentences($id,$taskid) {
	$query = "SELECT num,lang,text FROM sentence WHERE linkto='$id' AND task_id=$taskid AND type != 'reference' order by type";
	$result = safe_query($query);
	$hash = array();
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			$hash[$row["num"]] = array($row["lang"],$row["text"]);
		}
	}
	
	$taskinfo = getTaskInfo($taskid);
	if ($taskinfo['randout'] == "Y") {
		#randomize and return
		return shuffle_assoc($hash,$id);
	}
	return $hash;

}


# get hash with uniq info about the available sentences for a task: sentence_num, text
function getSourceSentences($taskid) {
	$query = "SELECT num,lang,text FROM sentence WHERE task_id='$taskid' AND linkto is null order by num;";
	$result = safe_query($query);	
	$hash = array();
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_row($result)) {
			$hash[$row[0]] = array($row[1],$row[2]);
        }
	}
	return $hash;
}

# get an array with done sentece by a user
function getDoneSentences($taskid,$userid) {
	$query = "SELECT sentence_num FROM done LEFT JOIN sentence on done.sentence_num=sentence.num WHERE done.user_id=$userid AND sentence.task_id=$taskid AND completed ='Y'";
	$result = safe_query($query);	
	$arr = array();
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_row($result)) {
			array_push($arr, $row[0]);
		}
	}
	return $arr;
}


# get an array with done sentece by a user
function getErrorSentences($taskid) {
	$query = "SELECT linkto FROM sentence WHERE text LIKE '% %' AND task_id=$taskid";
	$result = safe_query($query);	
	$arr = array();
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_row($result)) {
			array_push($arr, $row[0]);
		}
	}
	return $arr;
}

#get the counter about sentence types of a task
function countTaskSentences ($taskid) {
	$query="SELECT type,count(*) as num FROM sentence WHERE task_id=$taskid group by type order by type;";
	$result = safe_query($query);	
	$hash = array();
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_array($result)) {
			$hash[$row["type"]] = $row["num"];
		}
	}
	return $hash;
}

#get the counter about sentence types of a task
function countTaskSystem ($taskid) {
	$query="SELECT distinct type FROM sentence WHERE task_id=$taskid AND type != 'source' AND type != 'reference';";
	$result = safe_query($query);	
	return mysql_num_rows($result);
}

#get the list of sentence types
function getSentenceType () {
	$query="SELECT distinct type FROM sentence ORDER BY type";
	$result = safe_query($query);	
	$arr = array();
	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_row($result)) {
			array_push($arr, $row[0]);
		}
	}
	return $arr;
}

# get task ID by the task name
function getTaskID($taskname) {
	$query = "SELECT id FROM task WHERE name='$taskname';";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_row($result);
		return $row[0];
	}
	return 0;
}

# get task type by a task name
function getTaskType($taskid) {
	$query = "SELECT type FROM task WHERE id=$taskid;";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_row($result);
		return $row[0];
	}
	return "";
}

# get task name by a task id
function getTaskName($taskid) {
	$query = "SELECT name FROM task WHERE id=$taskid;";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_row($result);
		return $row[0];
	}
	return "";
}

# get an array with tasks name
function getTasks($user) {
	$hash = array();
	if ($user != null && strlen($user) > 0) {
		$query = "SELECT tasks,status FROM user WHERE username='$user' OR id='$user'";
		$result = safe_query($query);
		if (mysql_num_rows($result) == 1) {
			$row = mysql_fetch_row($result);
			if ($row[1] == "admin") { 
				$hash = getTasks(null);
			} else {
				$tasks = split(" ",$row[0]);
				foreach ($tasks as $taskname) {
					$taskid = getTaskID($taskname);
					$hash[$taskid] = array($taskname, getTaskType($taskid));
				}
			}
		}
	} else {
		$query = "SELECT id,name,type FROM task ORDER BY type,id;";
		$result = safe_query($query);	
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_row($result)) {
				#print $row[0] ."= array(".$row[1].", ".$row[2].")<br>";
				$hash[$row[0]] = array($row[1], $row[2]);
			}
		}
	}
	return $hash;
}

# get system ids and labels
function getSystems() {
	$query = "SELECT DISTINCT type FROM sentence";
	$result = safe_query($query);	
	$hash = array();
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_row($result)) {
			array_push($hash, $row[0]);
		}
	}
	return $hash;
}

#delete the annotations from a task and user
function deleteAnnotations ($taskid,$userid) {	
	$query="DELETE annotation from annotation LEFT JOIN sentence on annotation.sentence_num=sentence.num where user_id=$userid and task_id=$taskid";
	mysql_query($query) or print ("ERROR! Annotations deleting failed. (" . mysql_error() .")");	
	$deleted = mysql_affected_rows();
	
	#delete done records
	$query="DELETE done from done LEFT JOIN sentence on done.sentence_num=sentence.num where user_id=$userid and task_id=$taskid";
	safe_query($query);
	return $deleted;
}

function deleteSentences($taskid,$type) {	
    #delete all annotations
    $query = "DELETE FROM annotation where output_id IN (
    SELECT num FROM sentence WHERE sentence.task_id=$taskid AND type='$type');";
	#delete all sentences
	$query = "DELETE FROM sentence WHERE task_id=$taskid AND type='$type';";
	#$result = safe_query($query);
	return safe_query($query);
}

		
#controllo che i done abbiamo tutti lo stesso numero di output controllati: se ce ne sono in numero diverso probabilmente c'e` stato qualche erore dell'interfaccia: -1 non è stato trovato nessun done, 1 tutto OK!, 0 c'è qualche errore
#select annotation.sentence_num, count(*) as count from annotation LEFT JOIN done ON annotation.sentence_num=done.sentence_num WHERE completed="Y" AND done.user_id=1 group by annotation.sentence_num;
function getCheckAndDone($userid) {	
	$query = "select distinct count(*) as count from annotation LEFT JOIN done ON annotation.sentence_num=done.sentence_num AND completed='Y' AND annotation.user_id=done.user_id WHERE done.user_id=$userid group by annotation.sentence_num;";
	$result = safe_query($query);	
	if (mysql_num_rows($result) > 1) {
		return 0;
	} else {
		if (mysql_num_rows($result) == 0) {
			return -1;
		}
	}
	
	return 1;
}

function getDBInconsistency($userid, $tasks) {
	$hash_error= array();
	//check if all evaluated sentence has full annotated output
	foreach ($tasks as $taskname) {
		$taskid=getTaskID($taskname);
		$tasksyscount=countTaskSystem ($taskid);
		if ($tasksyscount > 0) {
			$query = "select sentence_num,count(*) as count from annotation left join sentence on sentence.num=annotation.sentence_num where task_id=$taskid AND user_id=$userid group by sentence_num having count < $tasksyscount;";
			$result = safe_query($query);
			if (mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_row($result)) {
					$hash_error[$row[0]] = array($taskid,"");
				}
			}
		}
	}
	
	$query = "select distinct sentence_num FROM annotation where user_id=$userid";
	$result_annotation = safe_query($query);
	$hash = array();
	while ($row = mysql_fetch_row($result_annotation)) {
		$hash[$row[0]] = 1;
	}
	$query = "select distinct sentence_num,task_id,done.lasttime FROM done LEFT JOIN sentence ON done.sentence_num=sentence.num WHERE user_id=$userid AND completed='Y'";
	$result_done = safe_query($query);	
	while ($row = mysql_fetch_row($result_done)) {
		if (!isset($hash[$row[0]])) {
			$hash_error[$row[0]] = array($row[1],$row[2]);
		} 
	}
	
	return 	$hash_error;
}

function exportCSV ($userid) {
	$intDir="/tmp";
	if (!is_dir($intDir)) {
		mkdir($intDir, 0777);
	}
	$date = date('Ymd_his', time());
	#$intDir =$_SERVER['DOCUMENT_ROOT'] ."/mteval_".$date."/";
	$intDir .= "/mteval";
	
	$query_clause = " status='user'";
	if (isset($userid) && $userid != null) {
		$query_clause = " user.id='".$userid."'";
		$intDir .= "_".$userid;	
	}
	$intDir .= "_".$date."/";
	if (!is_dir($intDir)) {
		mkdir($intDir, 0777);
	}
	
	$tasks = getTasks($userid);
	while (list ($taskid,$arrinfo) = each($tasks)) {
		saveLog("TASK: ". $taskid . " ".$arrinfo[0]);
		$taskname=$arrinfo[0];
		$tasksyscount=countTaskSystem ($taskid);
		#if ($taskname != "FAO_Errors_EN-AR") {
		#	continue;
		#}
		
		$tasktype=$arrinfo[1];
		//count the number of annotators for the current task
		$query = "SELECT id FROM user WHERE ".$query_clause." AND tasks like '%".$taskname."%'";
		$result_annotators = safe_query($query);
		$annotators_count = mysql_num_rows($result_annotators);
			
		if ($annotators_count > 0) {
			//collect all valid ids for the current tash (get the interception of annotated output, all user must annotated them)
			$query = "SELECT sentence_num, count(*) AS n FROM annotation LEFT JOIN user ON annotation.user_id=user.id LEFT JOIN sentence ON annotation.output_id=sentence.num WHERE ".$query_clause." AND task_id=$taskid GROUP BY sentence_num ORDER BY sentence_num";
			$result_common_taskanns = safe_query($query);
			$hash_common_taskanns = array();
			while ($row = mysql_fetch_row($result_common_taskanns)) {
				// controllo che per il task errors siano stati inseriti X systemi per Y utenti
				$query = "SELECT DISTINCT output_id,user_id FROM annotation LEFT JOIN user ON annotation.user_id=user.id WHERE ".$query_clause." AND sentence_num=".$row[0];
				$result_check = safe_query($query);
				if (mysql_num_rows($result_check) != ($annotators_count * $tasksyscount)) {
					saveLog("ERROR! $taskname ".$row[0]." -- " . mysql_num_rows($result_check) ." != ".($annotators_count * $tasksyscount));
					continue;
				} 	
				$hash_common_taskanns[$row[0]] = 1;
			}
			
			//loop on users
			while ($row = mysql_fetch_row($result_annotators)) {
				$userid=$row[0];
				$filecsv = $intDir.$taskname."_ann".$userid.".csv";
				$fh=fopen($filecsv,"w"); 
				
				//save comments
				$comments = getComments($taskid,$userid);	
				if (count($comments) > 0) {
					$filecsv_comment = $intDir.$taskname."_comment".$userid.".csv";
					$fh_comment=fopen($filecsv_comment,"w"); 
					fwrite($fh_comment,"ID\ttype\tcomment\n");
					while (list ($sentnum,$comment) = each($comments)) {
						fwrite($fh_comment,	str_replace("&quot;","\"",$comment)."\n");
					}
					fclose($fh_comment); 				
				}
				
			
				#print "FILE : $fh $filecsv <br>";
				$count_ann=0;
				#print "<h3>USER: ".$row[0]."</h3> $filecsv";
				
				if (preg_match("/quality/i", $tasktype)) {
					fwrite($fh,"ID\tlanguage_pair\tsystem\tquality_score\ttarget_tok_num\ttarget_text\tsource_tok_num\tsource_text\n");
				} else if (preg_match("/errors/i", $tasktype)) {
					fwrite($fh,"ID\tlanguage_pair\tsystem\terror_type\terror_tok_IDs\ttarget_tok_num\ttokenized_target_text\tsource_tok_num\tsource_text\n");
				} else {
					fwrite($fh,"ID\tlanguage_pair\tsystem\tannotation\ttok_IDs\ttarget_tok_num\ttarget_text\tsource_tok_num\tsource_text\n");
				}	
				$query = "SELECT output_id,id,type,eval,evalids,sentence_num,lang,text FROM annotation LEFT JOIN sentence ON annotation.output_id=sentence.num WHERE user_id=".$userid." AND task_id=".$taskid." order by id;";
				$result_annotation = safe_query($query);
				saveLog($taskid . " " . $taskname . " " . mysql_num_rows($result_annotation) . " " . $query);
				$last_id = "";
				$src_text="";
				#$last_count = 0;
				while ($row_annotation = mysql_fetch_row($result_annotation)) {
					if ($last_id != $row_annotation[1]) {
						$last_id = $row_annotation[1];
							
						#get source data
						$query = "SELECT lang,text FROM sentence WHERE id='".$row_annotation[1]."' AND type='source'";
						$result_source = safe_query($query);
						$row_source = mysql_fetch_row($result_source);
						
						$src_text = preg_replace("/[\n|\r]/","",preg_replace("/\t+/"," ",$row_source[1]));
					}
						
					$text = preg_replace("/[\n|\r]/","",preg_replace("/\t+/"," ",$row_annotation[7]));
					if (isset($hash_common_taskanns[$row_annotation[5]])) { 
						if (preg_match("/quality/i", $tasktype)) {
							/*if ($last_id != $row_annotation[1]) {
								if ($last_id != "" && $last_count != $annotators_count) {
									saveLog("ERROR2: " . $last_id);
								}
								$last_count=1;
								$last_id = $row_annotation[1];
							} else {
								$last_count++;
							}*/
							fwrite($fh,$row_annotation[1] ."\t". $row_source[0]."_".$row_annotation[6] ."\t" . $row_annotation[2] ."\t". $row_annotation[3] ."\t".getTokensNum($row_annotation[6],$text)."\t$text\t".getTokensNum($row_source[0],$src_text)."\t".$src_text."\n");
						} else if (preg_match("/errors/i", $tasktype)) {
							$label = "";
							if ($row_annotation[3] == 0) {
								$label = "No_errors";
							} else if ($row_annotation[3] == 1) {
								$label = "Too_many_errors";
							} else if ($row_annotation[3] == 2) {
								$label = "Reordering";
							} else if ($row_annotation[3] == 3) {
								$label = "Lexicon";
							} else if ($row_annotation[3] == 4) {
								$label = "Missing";
							} else if ($row_annotation[3] == 5) {
								$label = "Morphology";
							} else if ($row_annotation[3] == 6) {
								$label = "Punctuation";
							} else if ($row_annotation[3] == 7) {
								$label = "Superfluous";
							} 
							$splitted_ids = split(",",preg_replace("/^,/","",$row_annotation[4]));
							$cleaned_ids = array();
							#$savelog=0;
							#if ($row_annotation[1] == "MI009_GSPPA-I_13_REP_en-11" && $taskid=4 && $row_annotation[2] == sys1 && $label == "Lexicon") { 
							#	$savelog =1;
							#}
							foreach ($splitted_ids as $item_ids) {
								$ids = split(" ", trim($item_ids));
								#if ($savelog == 1) {
								#	saveLog("=>>>>> "  . trim($item_ids));
								#}
								$spaceIDs = array();
								$tokenIDs = array();
								foreach ($ids as $id) {
									if ($id != "") {
										if (preg_match('/-/',$id)) { 
											if (!in_array($id, $spaceIDs)) {
												array_push($spaceIDs, $id);
											}
										} else {
											if (!in_array($id, $tokenIDs)) {
												array_push($tokenIDs, $id);
											}
										}
									}
								}
								
								if (count($tokenIDs) > 0) {
									array_push($cleaned_ids, join(" ",$tokenIDs));
								} else if (count($spaceIDs) == 1) {
									array_push($cleaned_ids, join(" ",$spaceIDs));
								}
							}
							$strids = trim(join(",", $cleaned_ids));
							#if ($savelog == 1) {
							#	saveLog($row_annotation[4] . ">>>> " . $strids);
							#}
							if ($row_annotation[3] > 1 && $strids == "") {
								continue;
							}
							fwrite($fh,$row_annotation[1] ."\t". $row_source[0]."_".$row_annotation[6] ."\t". $row_annotation[2] ."\t".$label ."\t". $strids ."\t". getTokensNum($row_annotation[6],$text)."\t".join(" ", getTokens(ereg_replace(".*_","", $row_annotation[6]), $text))."\t".getTokensNum($row_source[0],$src_text)."\t".$src_text."\n"); 	
						} else {
							fwrite($fh,$row_annotation[1] ."\t". $row_source[0]."_".$row_annotation[6] ."\t". $row_annotation[2] ."\t". $row_annotation[3] ."\t". $row_annotation[4] ."\t". getTokensNum($row_annotation[6],$text)."\t$text\t".getTokensNum($row_source[0],$src_text)."\t".$src_text."\n"); 	
						}
						$count_ann++;
						#print $row_annotation[0] ."\t". $row_annotation[1] ."\t". $row_annotation[2] ."\t". $row_annotation[3] ."\n";
					} else { 
						//saveLog("NO INTERCECTION: ". $row_annotation[1] ." ". $row[0] .": $query");
					}
				}
				fclose($fh); 
				saveLog("SAVED FILE $filecsv: $count_ann $annotators_count $taskname " . mysql_num_rows($result_annotation) . " " . count($hash_common_taskanns));
				if ($count_ann == 0) {
					unlink($filecsv);
				}
				#print "Saved $count_ann annotations."; 
			}
		}
	} //end tasks loop
	
			
	$filezip = "/tmp/mteval$date.zip";
	
	$zip = new ZipArchive();
	if($zip->open($filezip, ZIPARCHIVE::CREATE)!==TRUE){
		print "ERROR! Sorry ZIP creation failed.";
	}
	$files= scandir($intDir);
	//var_dump($files);
	//unset($files[0],$files[1]);
	foreach ($files as $file) {
		#print "ADD to zip: $file<br>";
		if ($file != "." && $file != "..") { 
			if (isset($userid) && $userid != null) {
				$zip->addFile($intDir.$file,"mteval_CSV_".$userid."-".$date."/".$file);
			} else {
  				$zip->addFile($intDir.$file,"mteval_CSV-".$date."/".$file);
  			}
  		}    
	}
	$zip->close();

	if (file_exists($filezip)) {
		#print $filezip . " (" . file_exists($filezip) .")";
		readfile($filezip);
		
		unlink($filezip);
		unlink($intDir);
		exit(0);
	}
}

#save XML files
function exportXML ($userid) {
	$intDir="/tmp";
	if (!is_dir($intDir)) {
		mkdir($intDir, 0777);
	}
	$date = date('Ymd_his', time());
	#$intDir =$_SERVER['DOCUMENT_ROOT'] ."/mteval_".$date."/";
	$intDir .= "/mteval";
	
	$query_clause = " status='user'";
	if (isset($userid) && $userid != null) {
		$query_clause = " user.id='".$userid."'";
		$intDir .= "_".$userid;	
	}
	$intDir .= "_".$date."/";
		
	if (!is_dir($intDir)) {
		mkdir($intDir, 0777);
	}
	
	$tasks = getTasks($userid);
	while (list ($taskid,$arrinfo) = each($tasks)) {
		saveLog("TASK: ". $taskid . " ".$arrinfo[0]);
		$taskname=$arrinfo[0];
		$tasksyscount=countTaskSystem ($taskid);
		#if ($taskname != "FAO_Errors_EN-AR") {
		#	continue;
		#}
		
		$tasktype=$arrinfo[1];
		//count the number of annotators for the current task
		$query = "SELECT id FROM user WHERE ".$query_clause." AND tasks like '%".$taskname."%'";
		$result_annotators = safe_query($query);
		$annotators_count = mysql_num_rows($result_annotators);
			
		if ($annotators_count > 0) {
			//collect all valid ids for the current tash (get the interception of annotated output, all user must annotated them)
			$query = "SELECT sentence_num, count(*) AS n FROM annotation LEFT JOIN user ON annotation.user_id=user.id LEFT JOIN sentence ON annotation.output_id=sentence.num WHERE ".$query_clause." AND task_id=$taskid GROUP BY sentence_num ORDER BY sentence_num";
			$result_common_taskanns = safe_query($query);
			$hash_common_taskanns = array();
			while ($row = mysql_fetch_row($result_common_taskanns)) {
				// controllo che per il task errors siano stati inseriti X systemi per Y utenti
				$query = "SELECT DISTINCT output_id,user_id FROM annotation LEFT JOIN user ON annotation.user_id=user.id WHERE ".$query_clause." AND sentence_num=".$row[0];
				$result_check = safe_query($query);
				if (mysql_num_rows($result_check) != ($annotators_count * $tasksyscount)) {
					saveLog("ERROR! $taskname ".$row[0]." -- " . mysql_num_rows($result_check) ." != ".($annotators_count * $tasksyscount));
					continue;
				} 	
				$hash_common_taskanns[$row[0]] = 1;
			}
			
			//loop on users
			while ($row = mysql_fetch_row($result_annotators)) {
				$userid=$row[0];
				$filecsv = $intDir.$taskname."_ann".$userid.".xml";
				$fh=fopen($filecsv,"w"); 
				fwrite($fh,"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<".$tasktype."_task>\n");
				#print "FILE : $fh $filecsv <br>";
				$count_ann=0;
				#print "<h3>USER: ".$row[0]."</h3> $filecsv";
				
				$query = "SELECT output_id,id,type,eval,evalids,sentence_num,lang,text FROM annotation LEFT JOIN sentence ON annotation.output_id=sentence.num WHERE user_id=".$userid." AND task_id=".$taskid." order by id,type,eval;";
				$result_annotation = safe_query($query);
				saveLog($taskid . " " . $taskname . " " . mysql_num_rows($result_annotation) . " " . $query);
				$last_id = "";
				$system_id = "";
				$error_id = "";
				$tokens=array();
				#$last_count = 0;
				
				//get comments
				$comments = getComments($taskid,$userid);	
							
				while ($row_annotation = mysql_fetch_row($result_annotation)) {
					if (isset($hash_common_taskanns[$row_annotation[5]])) { 
						if ($last_id != $row_annotation[1]) {
							if ($last_id != "") {
								fwrite($fh,"  </system>\n </eval_item>\n");
							}
							$last_id = $row_annotation[1];
							$system_id = "";
							$error_id = "";
							
							#get source data
							$query = "SELECT lang,text FROM sentence WHERE id='".$row_annotation[1]."' AND type='source'";
							$result_source = safe_query($query);
							$row_source = mysql_fetch_row($result_source);
							fwrite($fh," <eval_item ID='".$last_id."' language_pair='".$row_source[0]."_".$row_annotation[6]."'>\n");
					
							$src_text = preg_replace("/[\n|\r]/","",preg_replace("/\t+/"," ",$row_source[1]));
							if ($src_text != "") {
								fwrite($fh,"  <source_text source_tok_num='".getTokensNum($row_source[0],$src_text)."'>".xml_escape($src_text)."</source_text>\n");
							}	
						}	
						if ($system_id != $row_annotation[2]) {
							if ($system_id != "") {
								fwrite($fh,"  </system>\n");
							}
							$system_id = $row_annotation[2];
					
							$text = preg_replace("/[\n|\r]/","",preg_replace("/\t+/"," ",$row_annotation[7]));
							fwrite($fh,"  <system name='".$system_id."'");
							if (preg_match("/quality/i", $tasktype)) {
								fwrite($fh," quality_score='".$row_annotation[3]."'");
							}
							fwrite($fh,">\n");
							if (count($comments) > 0) {
								if (array_key_exists($row_annotation[0], $comments)) {
									fwrite($fh,"    <comment>".preg_replace("/.+\t/","",$comments{$row_annotation[0]}) ."</comment>\n");
								}									
							}
							fwrite($fh,"    <target_text target_tok_num='".getTokensNum($row_annotation[6],$text)."'>".xml_escape($text)."</target_text>\n");	
							$tokens = getTokens(ereg_replace(".*_","",$row_annotation[6]), $text);
							
							#add tokens
				$i=1;
				fwrite($fh,"      <tokens>\n");
				foreach ($tokens as $token) {
					fwrite($fh,"        <token id='$i'>".xml_escape($token)."</token>\n");
					$i++;
				}
				fwrite($fh,"      </tokens>\n");								
				
						}
						if (preg_match("/errors/i", $tasktype)) {
							$label = "";
							if ($row_annotation[3] == 0) {
								$label = "No_errors";
							} else if ($row_annotation[3] == 1) {
								$label = "Too_many_errors";
							} else if ($row_annotation[3] == 2) {
								$label = "Reordering";
							} else if ($row_annotation[3] == 3) {
								$label = "Lexicon";
							} else if ($row_annotation[3] == 4) {
								$label = "Missing";
							} else if ($row_annotation[3] == 5) {
								$label = "Morphology";
							} else if ($row_annotation[3] == 6) {
								$label = "Punctuation";
							} else if ($row_annotation[3] == 7) {
								$label = "Superfluous";
							} 
							
							
							if ($error_id != "") {
								fwrite($fh,"    <errors>\n");
								$error_id = $row_annotation[3];
							}
							$splitted_ids = split(",",preg_replace("/^,/","",$row_annotation[4]));
							$cleaned_ids = array();
							
							foreach ($splitted_ids as $item_ids) {
								$ids = split(" ", trim($item_ids));
								#if ($savelog == 1) {
								#	saveLog("=>>>>> "  . trim($item_ids));
								#}
								$spaceIDs = array();
								$tokenIDs = array();
								foreach ($ids as $id) {
									if ($id != "") {
										if (preg_match('/-/',$id)) { 
											if (!in_array($id, $spaceIDs)) {
												array_push($spaceIDs, $id);																						
											}
										} else {
											if (!in_array($id, $tokenIDs)) {
												array_push($tokenIDs, $id);
											}
										}
									}
								}
								
								if (count($tokenIDs) > 0) {
									array_push($cleaned_ids, join(" ",$tokenIDs));
								} else if (count($spaceIDs) == 1) {
									array_push($cleaned_ids, join(" ",$spaceIDs));
								}
							}
							$strids = trim(join(",", $cleaned_ids));
							
							#if ($savelog == 1) {
							#	saveLog($row_annotation[4] . ">>>> " . $strids);
							#}
							if ($row_annotation[3] > 1 && $strids == "") {
								continue;
							}
							fwrite($fh,"      <error error_type='".$label."'>\n");
							foreach ($cleaned_ids as $ids) {
								fwrite($fh,"        <error_span>\n");
								foreach (split(" ", $ids) as $id) {
									fwrite($fh,"          <token id='$id'");
									if (preg_match('/-/',$id)) {
										fwrite($fh,"/>\n");
									} else {
										fwrite($fh,">".xml_escape($tokens[($id-1)])."</token>\n");
									} 	
								}
								fwrite($fh,"        </error_span>\n");								
							}							
							fwrite($fh,"      </error>\n");
							
							#fwrite($fh,$row_annotation[1] ."\t". $row_source[0]."_".$row_annotation[6] ."\t". $row_annotation[2] ."\t".$label ."\t". $strids ."\t". getTokensNum($row_annotation[6],$text)."\t$text\t".getTokensNum($row_source[0],$src_text)."\t".$src_text."\n"); 	
						} else {
							#fwrite($fh,$row_annotation[1] ."\t". $row_source[0]."_".$row_annotation[6] ."\t". $row_annotation[2] ."\t". $row_annotation[3] ."\t". $row_annotation[4] ."\t". getTokensNum($row_annotation[6],$text)."\t$text\t".getTokensNum($row_source[0],$src_text)."\t".$src_text."\n"); 	
						}
						$count_ann++;
						#print $row_annotation[0] ."\t". $row_annotation[1] ."\t". $row_annotation[2] ."\t". $row_annotation[3] ."\n";
					} else { 
						//saveLog("NO INTERCECTION: ". $row_annotation[1] ." ". $row[0] .": $query");
					}
				}
							
				if (mysql_num_rows($result_annotation) > 0) {
					fwrite($fh,"  </system>\n</eval_item>\n");
				}
				fwrite($fh,"</".$tasktype."_task>\n");				
				fclose($fh); 
				
				saveLog("SAVED FILE $filecsv: $count_ann $annotators_count $taskname " . mysql_num_rows($result_annotation) . " " . count($hash_common_taskanns));
				if ($count_ann == 0) {
					unlink($filecsv);
				}
				#print "Saved $count_ann annotations."; 
			}
		}
	} //end tasks loop
	
			
	$filezip = "/tmp/mteval-XML_$date.zip";
	
	$zip = new ZipArchive();
	if($zip->open($filezip, ZIPARCHIVE::CREATE)!==TRUE){
		print "ERROR! Sorry ZIP creation failed.";
	}
	$files= scandir($intDir);
	//var_dump($files);
	//unset($files[0],$files[1]);
	foreach ($files as $file) {
		#print "ADD to zip: $file<br>";
		if ($file != "." && $file != "..") { 
			if (isset($userid) && $userid != null) {
				$zip->addFile($intDir.$file,"mteval_XML_".$userid."-".$date."/".$file);
			} else {
  				$zip->addFile($intDir.$file,"mteval_XML-".$date."/".$file);
  			}
  		}    
	}
	$zip->close();

	if (file_exists($filezip)) {
		#print $filezip . " (" . file_exists($filezip) .")";
		readfile($filezip);
		
		unlink($filezip);
		unlink($intDir);
		exit(0);
	}
	
}

### PRESENTATION FUNCTION ###
function showSentence ($lang, $text, $type = "", $tokenize = "no", $idx = "") {
	global $languages;
	$spacebg = " class=token onmouseover=\"this.className='orangebg'\" onmouseout=\"this.className='whitebg'\"";
	$tokenbg = " class=token onmouseover=\"this.className='orangeborderb'\" onmouseout=\"this.className='whiteborderb'\"";

	if ($type=="" || $type=="source") {
		$html="<div class='cell source";
	} else {	
		$html="<div class='cell $type";
	}
	if (isset($languages[$lang][2]) && $languages[$lang][2] == "rtl") {
		$html.=" rtl";
	}
	if ($tokenize == "yes") {
		$tokens = getTokens($lang, $text);
		$id=1;
		$text = "";
		foreach ($tokens as $token) {
			$text .= "<div id=$idx.$id $tokenbg>$token</div>";
			$text .= "<div id=$idx.$id"."-".($id+1)." $spacebg>&nbsp;</div>";
			$id++;
		}
		
		/*preg_match_all('/./u', $text, $tokenized_text);
		$id=0;
		if ($lang == "zh") {
			$text = "";
			foreach ($tokenized_text[0] as $ch) {
				if (trim($ch) == "") {
					$text .= "<div id=$idx.$id"."-".($id+1)." $spacebg>&nbsp;</div>";
				} else {
					$id++;
					$text .="<div id=$idx.$id $tokenbg>$ch</div>";
				}
			}
		} else {
			$text="";
			$token="";
			$errors="";
			foreach ($tokenized_text[0] as $ch) {
				#print " ($ch)" . chr($ch);
				#other special character
				#ord($ch)=194 (No-break space) U+00A0 &#160; 
				#if (ord($ch) == 194) 
				#	$text .="<img src='img/check_error.png' width=16>";
			
				if ($ch == " " || ord($ch) == 194 || eregi("[!|\?|\"|'|-|/|$|,|:|;|\.|\(|\)|\[|\]|{|}]",$ch)) {
					if (strlen($token) > 0) {
						$id++;
						#print "<li>$token";
						$text .="<div id=$idx.$id $tokenbg>$token</div>";
						$token="";
					}
					if (trim($ch) == "") {
						$text .= "<div id=$idx.$id"."-".($id+1)." $spacebg>&nbsp;</div>";
					} else {
						$id++;
						$text .= "<div id=$idx.$id $tokenbg>$ch</div>";
					}
				} else {
					$token .="$ch";
				}
			}
			if (strlen($token) > 0) {
				$id++;
				$text .="<div id=$idx.$id $tokenbg>$token</div>\n";
			}
			
		}*/
	}
	if ($type == "source") {
		$text = "<b>$text</b>";
	} else if  ($type == "reference") {
		$text = "<font color=#888>$text</font>";
	} 
	if ($type=="output") {
		$html.="'><div style='border: solid #aaa 1px; padding: 4px; top: -10px'>$text</div></div>";
	} else {
		$html.="'>$text</div>";
	}
	return $html;
}


function getTokens  ($lang, $text) {
	preg_match_all('/./u', trim($text), $tokenized_text);
	$tokens=array();
	if ($lang == "zh") {
		foreach ($tokenized_text[0] as $ch) {
			if (trim($ch) != "") {
				array_push($tokens, $ch);
			}
		}
	} else {
		$token="";
		foreach ($tokenized_text[0] as $ch) {
			#print " ($ch)" . chr($ch);
			#other special character
			#ord($ch)=194 (No-break space) U+00A0 &#160; 
			#if (ord($ch) == 194) 
			#	$token .="<img src='img/check_error.png' width=16>";
			if ($ch == " " || ord($ch) == 194 || eregi("[!|\?|\"|'|-|/|$|,|:|;|\.|\(|\)|\[|\]|{|}]",$ch)) {
				if (strlen($token) > 0) {
					array_push($tokens, $token);
					$token="";
				}
				if (trim($ch) != "") {
					array_push($tokens, $ch);
				}
			} else {
				$token .="$ch";
			}
		}
		if (strlen($token) > 0) {
			array_push($tokens, $token);					
		}
	}
	return $tokens;
}	

function getTokensNum ($lang, $text) {
	return count(getTokens($lang, $text));
}	
	

#$date = "2009-03-04 17:45";
#$result = nicetime($date); // 2 days ago
function niceTime($date) {
	if(empty($date)) {
        return "No date provided";
    }
    
    $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths = array("60","60","24","7","4.35","12","10");
    
    $now = time();
    $unix_date = strtotime($date);
    
       // check validity of date
    if(empty($unix_date)) {    
        return "Bad date";
    }

    // is it future date or past date
    if($now > $unix_date) {    
        $difference = $now - $unix_date;
        $tense = "ago";
        
    } else {
        $difference = $unix_date - $now;
        $tense = "from now";
    }
    
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }
    
    $difference = round($difference);
    
    if($difference != 1) {
        $periods[$j].= "s";
    }
    
    return "$difference $periods[$j] {$tense}";
}

/*
 * return an array whose elements are shuffled in random order.
 */
function shuffle_assoc($list,$id) { 
  if (!is_array($list)) return $list; 

  $keys = array_keys($list); 
  //shuffle($keys); 
  //each user will be a difference random output ordered
  seoShuffle($keys,$id);
  $random = array(); 
  foreach ($keys as $key) 
    $random[$key] = $list[$key]; 

  return $random; 
} 

#the same randomized result each time that list is generated.
function seoShuffle(&$items,$seed) { 
    mt_srand($seed); 
    for ($i = count($items) - 1; $i > 0; $i--){ 
        $j = @mt_rand(0, $i); 
        $tmp = $items[$i]; 
        $items[$i] = $items[$j]; 
        $items[$j] = $tmp; 
    } 
} 

/*
 * Logging
 */
#saveLog("PASSO! $eval $source_id,$target_id,$user_id,$action\n");
function saveLog($line) {
	if (SAVELOG == 1) {
		$time = date( "d/m/Y H:m:s", time() );

		$myFile = "/tmp/mteval.log";
		$fh = fopen($myFile, 'a') or die("can't open file");
		fwrite($fh, "$time\t$line\n");
		fclose($fh);
	}
}

#generic function for Mysql query
function safe_query ($query = "") {
    global $mysession;
    if (empty($query)) {
		return FALSE;
    }
	$errorno=0;
    if (QUERY_LOG == "yes" && substr(strtolower(trim($query)),0, 6) != "select") {
		$querylog = addslashes($query);
    	$querylog = "INSERT INTO log (user_id, query, error, lasttime) VALUES (".$mysession['userid'].",\"$querylog\",\"$errorno\",now());";
		mysql_query($querylog) or die ("Error! " . mysql_error());
    }    
    	
    $result = mysql_query($query) or $errorno= mysql_errno();
    if ($errorno != 0) {
		if (QUERY_DEBUG == "no") {
		    print ("<BR>Query failed: please contact the webmaster " . SYSADMIN . ".");
		} else {
			$error = mysql_error();	
	    	print ("</td></tr></table></td></tr></table><BR>Query failed:" 
					      . "<li> errorno=" . $errorno
					      . "<li> error=" . $error
					      . "<b><li> query=" . $query
					      . "<p><a href=\"javascript:history.go(-1)\"> Back</a>");
			return 0;
		}	
    } 
    return $result;
}

#generic function for Mysql query
function safe_query_OLD ($query = "") {
    global $mysession, $LAST_INSERT_ID;
    if (empty($query)) {
		return FALSE;
    }
    
    #$query = str_replace("\"", "\\\"",$query);
    print $query . "<br>";
    $errorno=0;

    $result = mysql_query($query) or $errorno= mysql_errno();
    if (eregi("^INSERT", $query)) {
        $LAST_INSERT_ID = mysql_insert_id();
    }
    #saveLog($query);
    if (QUERY_LOG == "yes" && substr(strtolower(trim($query)),0, 6) != "select") {
		$query = addslashes($query);
    	$query = "INSERT INTO log (user_id, query, error, lasttime) VALUES (".$mysession['userid'].",\"$query\",\"$errorno\", now());";
		mysql_query($query) or die ("Error! " . mysql_error());
	}
    if ($errorno != 0) {
		if (QUERY_DEBUG == "no") {
		    print ("<BR>Query failed: please contact the webmaster " . SYSADMIN . ".");
		} else {
			$error = mysql_error();	
	    	print ("</td></tr></table></td></tr></table><BR>Query failed:" 
					      . "<li> errorno=" . $errorno
					      . "<li> error=" . $error
					      . "<b><li> query=" . $query
					      . "<p><a href=\"javascript:history.go(-1)\"> Back</a>");
			exit;
		}	
    }
    return $result;
}


function xml_escape($s) {
	$s = str_replace("&quot;","\"",$s);
    $s = html_entity_decode($s, ENT_QUOTES, 'UTF-8');
    $s = htmlspecialchars($s, ENT_QUOTES, 'UTF-8', false);
    return $s;
}
?>