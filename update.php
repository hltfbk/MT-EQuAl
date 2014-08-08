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

	resetErrors($id,$targetid,$userid,$check);
} else if (isset($completed)) {
	print saveDone($id,$userid,$completed);
} else if (isset($alignids)) {
	saveAligment($id,$targetid,$userid,$check,$alignids);
} else if (isset($tokenids)) {
	if (isset($action) && $action == "remove") {
		removeError($id,$targetid,$userid,$check,$tokenids);
	} else {
		saveErrors($id,$targetid,$userid,$check,$tokenids,$words);
	}
} else if (isset($comment)) {
	saveComment($id,$userid,$comment);
} else if (isset($check)) {
	print saveQuality($id,$targetid,$userid,$check,$action);
}

?>