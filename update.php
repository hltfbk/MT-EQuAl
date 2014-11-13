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

include("config.php");
include("functions.php");
header("Content-type: text/html; charset=utf-8");

#saveLog("ID: " .$id.", TARGETID: " .$targetid.", USERID: ".$userid. " CHECK: ".$check ." tokenids: ".$tokenids ." words:".$words." ACTION: $action, COMMENT: $comment, COMPLETED: $completed");

if (isset($action) && $action == "reset") {
	#saveLog("USERID: ".$userid ."\n");
?>
		<script type="text/javascript" src="js/errors.js"></script>
<?php	
	resetErrors($id,$targetid,$userid,$check);
	$tasktype = getTaskType($taskid);
	$errors = getErrors($id,$targetid,$userid);
	if ($tasktype == "docann") {
		$sentence_hash = getSentence($targetid, $taskid);
		if (count($sentence_hash) > 0) {
			print showSentence ($id, trim(preg_replace("/\n/"," __BR__ ",trim($sentence_hash["source"][1]))), "output", $sentence_hash["source"][2], $targetid, $errors, $mysession["taskranges"]);
		}
    } else {			
		$hash_target = getSystemSentences($id,$taskid);
		if (isset($hash_target{$targetid})) {
			$sentence_item = $hash_target{$targetid};
			print showSentence ($sentence_item[0], $sentence_item[1], "output", $sentence_item[2], $targetid, $errors, $mysession["taskranges"]);
		}
	}
	
} else if (isset($completed)) {
	print saveDone($id,$userid,$completed);
} else if (isset($alignids)) {
	saveAligment($id,$targetid,$userid,$check,$alignids);
} else if (isset($tokenids)) {
?>
		<script type="text/javascript" src="js/errors.js"></script>
<?php
	if (isset($action) && $action == "remove") {
		removeError($id,$targetid,$userid,$check,$tokenids);
	} else {
		saveErrors($id,$targetid,$userid,$check,$tokenids,$words);
	}
	$checked=0;
	$hash_target = getSystemSentences($id,$taskid);
	if (count($hash_target) > 0) {
		while (list ($sentence_id, $sentence_item) = each($hash_target)) {
			$errors = getErrors($id,$sentence_id,$userid);
			if (count($errors) > 0) {
				$checked++;
			}
			if ($targetid == $sentence_id) {
				print showSentence ($sentence_item[0], $sentence_item[1], "output", $sentence_item[2], $targetid, $errors, $mysession["taskranges"]);
			}
		}
	} else {
		$hash_target = getSentence($id, $taskid);
		while (list ($type, $sentence_item) = each($hash_target)) {
			$errors = getErrors($id,$id,$userid);
			if (count($errors) > 0) {
				$checked++;
			}
			print showSentence ($id, trim(preg_replace("/\n/"," __BR__ ",$sentence_item[1])), "output", $sentence_item[2], $id, $errors, $mysession["taskranges"]);
		}
	} 
	if ($checked != count($hash_target)) {
	 	print "<script>notDoneYet();</script>";
	} else {
		print "<script>activateDone(0);</script>";
	}
 
} else if (isset($comment)) {
	saveComment($id,$userid,$comment);
} else if (isset($check)) {
	print saveQuality($id,$targetid,$userid,$check,$action);
}

?>