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

<head>
<style>
tr.row:hover {
background:#cf4;
}
td:hover {
 background: #ececec;
}
div.uploadpane {
	visibility: hidden;
	height:100%;
    width:100%;
    position:fixed;
    left:0;
    top:0;
    z-index:100 !important;
    background-color: rgba(64, 64, 64, 0.5);
}
div.uploadform {
	left: 30%; top: 30%; 
	position: absolute; background: #efefef; z-index: 10000;   
    padding: 20px;
    opacity:1.0; 
    filter:alpha(opacity=100); /* internet explorer */
    -khtml-opacity: 1;      /* khtml, old safari */
    -moz-opacity: 1;       /* mozilla, netscape */
    opacity: 1;   		/* fx, safari, opera */
    margin: auto auto auto auto;
    vertical-align: middle;
}
</style>
</head>

<?php
//add new sentence
$errmsg="";
if (!empty($mysession["status"]) && $mysession["status"] == "admin") {
$log = "";	
if (isset($taskid)) {
 if (isset($type)) {
  if (isset($action) && $action="remove") {
  	if (isAnnotatedTask($taskid) == 0) {
  		deleteSentences($taskid,$type);
  		//deleteFile($taskid,$type);
  		$errmsg="DONE! The $type sentences have been removed.";
  	} else {
  		print "<script>alert('Warning! This resource cannot be deleted because some annotations are joined to it.');</script>";
  	}
  } else {
  	$insert=0;
  	$ftmp = $_FILES['filecsv']['tmp_name'];
   if (!empty($ftmp)) {
	$oname = basename($_FILES['filecsv']['name']);
	if (file_exists($ftmp)) {
		$mappingsID2NUM = getSourceSentenceIdMapping($taskid);
		//print "TASK: $taskid, TYPE: $type, mappingsID2NUM: ". join(",",$mappingsID2NUM). "<br>";

		$handle = fopen($ftmp, "r");
		if ($handle) {
			$linenum = 0;
    		while (($line = fgets($handle)) !== false) {
    			$linenum++;
    			$query = "";
        		#print $line ."<br>";
    			$items = split("\t",$line);
    			if (count($items) < 3 || empty($items[1])) {
    				$errmsg = "WARNING! Parse error on file $oname: the language is missed.<br>\n[line: $linenum] $line\n";
    				break;
    			}
    			if ($type == "source") {
        			$query = "INSERT INTO sentence (id, type, lang, task_id, text, lasttime) VALUES ('".$items[0] ."','".$type."','" .$items[1]."',$taskid,'". str_replace("'","\\'",$items[2]) ."', now());";
        		} else {   			
        			if (!empty($mappingsID2NUM[$items[0]])) {
        				$query = "INSERT INTO sentence (id, type, lang, task_id, linkto, text, lasttime) VALUES ('".$items[0] ."','".$type."','" .$items[1]."',$taskid,'". $mappingsID2NUM[$items[0]] ."','" .str_replace("'","\\'",$items[2]) ."', now());";
        			} else {
        				$errmsg = "WARNING! The source of sentence ".$items[0]." is missed. Add the source sentence aligned to this output sentence.<br>";
        			}
        		}
				if (strlen($query) > 0) {
					$log .= $query ."<br>";
					$insert += safe_query($query);
				}
    		}
    		fclose($handle);
    		
    		## filter SENTENCE_LIMIT number of sentences
    		if (!isset($limit) && SENTENCE_LIMIT > 0) {
    			$limit = SENTENCE_LIMIT;
    		}
    		if ($type == "source" && $limit > 0 && $insert > $limit) {
    			$mappingsID2NUM = getSourceSentenceIdMapping($taskid);
    			while ($insert > $limit) {
    				$rand_key = array_rand($mappingsID2NUM);
    				$query = "DELETE FROM sentence WHERE num=".$mappingsID2NUM[$rand_key];
    				if ( safe_query($query) == 1) {
    					unset($mappingsID2NUM[$rand_key]);
    					$insert = $insert-1;
    				}
    			}
    		}
    		
    		//add info about used file
    		/*$query = "DELETE FROM file WHERE type='$type' AND task_id=$taskid";
    		safe_query($query);
			$query = "INSERT INTO file VALUES ('$oname','$type',$taskid)";
    		safe_query($query);
			*/
		} else {
    		// error opening the file.
    		$errmsg = "ERROR! Some problems occured opening the file $oname.";
		}
		if ($errmsg == "" || $insert == count($mappingsID2NUM)) {
    		$errmsg = "DONE! $insert sentences have been inserted";
    	}
	} else {
		$errmsg = "ERROR! Uploaded file hasn't been parsed correctly.";
	}
	} 
	#print "Uploading... taskid: $taskid, type: $type<br>\n"; #.$_FILES['filecsv']['tmp_name'].
  }
 } 
}
}

//show stored data
	$tasks = getTasks($mysession["username"]);
    
	print "<table border=1 cellspacing=0 cellpadding=2><tr bgcolor=#ccc><th>Task name</th>";
	foreach ($sentenceTypes as $stype) { 
		print "<th>$stype</th>";			
	}
	print "</tr>\n";	
	while (list ($tid,$tarr) = each($tasks)) {
		print "<tr class=row align=right><td><a href='admin.php?section=task&task=".$tarr[0]."'>".$tarr[0]."</a> </td>";
		$count_hash = countTaskSentences($tid);
		foreach ($sentenceTypes as $stype) { 
			print "<td>";
			if (isset($count_hash[$stype])) {
				print $count_hash[$stype];
				if ($mysession["status"] == "admin") { 
					print " <a href=\"javascript:delSentences($tid,'$stype');\"><img border=0 width=11 src='img/delete.png'></a> <a href=\"javascript:showUpload($tid,'$stype');\"><img src='img/add.png'></a>";
				}
			} else {
				print "<a href=\"javascript:showUpload($tid,'$stype');\"><img src='img/add.png'></a>";
			}
			print "</td>";
		}
		if (isset($taskid) && $tid == $taskid && !empty($errmsg)) {
			print "<td bgcolor=lightyellow>$errmsg</td>";
		}
		print "</tr>\n";
    }
	print "</table>";

//create upload form
if (!empty($mysession["status"]) && $mysession["status"] == "admin") {
 ?>

<div id=uploadpane class=uploadpane>
<div class=uploadform> 
<form action="admin.php?section=data" method="post" id=uploadform enctype="multipart/form-data">
<input type=hidden name=taskid value="" />
<input type=hidden name=type value="" />

 Import sentences from CSV file <font size=-1><i>(size max. 2Mb)</i></font>:<br>
 <input type="file" id="filecsv" name="filecsv"> </br><br>
 (you can catch just <INPUT TYPE=text NAME=limit value="<?php if (isset($limit)) { echo $limit;} ?>" size=5> sentences from the file)
 
  </br><br>
  Tokenize sentences: <select name="sysnum">
	<option value='no'>NO
	<option value='yes_space'>YES, using spaces only
	<option value='yes_simple'>YES using spaces and other	
	</select>
	</br></br>
  <input type="submit" name="Upload" value="Upload">	
  <input type="button" onclick="javascript:hideUpload();" value="Cancel">
</form>
</div>
</div>
<?php
}
?>
