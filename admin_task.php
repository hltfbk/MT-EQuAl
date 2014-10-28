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
<script type="text/javascript" src="js/jscolor/jscolor.js"></script>

	<meta charset="utf-8">
	<!-- markItUp! skin -->
	<link rel="stylesheet" type="text/css" href="js/markitup/skins/markitup/style.css">
	<!--  markItUp! toolbar skin -->
	<link rel="stylesheet" type="text/css" href="js/markitup/sets/default/style.css">
	<!-- markItUp! -->
	<script type="text/javascript" src="js/markitup/jquery.markitup.js"></script>
	<!-- markItUp! toolbar settings -->
	<script type="text/javascript" src="js/markitup/sets/default/set.js"></script>

<style>
li.row {
	padding-top: 1px;
	border-bottom: 1px solid #fff;
}

li.row:hover {
	padding-top: 1px;
	border-bottom: 1px solid #090;
	background:#BAE3E0;
}
li.selected {
	background:#E0A4AA;
	padding-right:4px;
	border-bottom: 1px solid #5c0120;
	font-size:14px;
	color: #600;
}
</style>

<SCRIPT language="javascript">
var DEFAULT_ERRORS_RANGES="[{\"val\": \"0\",\"label\": \"No errors\",\"color\": \"C8E8B8\"},{\"val\": \"1\",\"label\": \"Too many errors\",\"color\": \"F0D3A8\"}]";
var DEFAULT_WORDALIGN_RANGES="[{\"val\": \"0\",\"label\": \"Possible alignment\",\"color\": \"BABABA\"},{\"val\": \"1\",\"label\": \"Sure alignment\",\"color\": \"000000\"}]";
var DEFAULT_DOCANN_RANGES="[{\"val\": \"0\",\"label\": \"No annotation\",\"color\": \"C8E8B8\"},{\"val\": \"1\",\"label\": \"Too many annotations\",\"color\": \"F0D3A8\"}]";
var DEFAULT_RANGES="[{\"val\": \"\",\"label\": \"\",\"color\": \"FFFFFF\"}]";

function addRange(taskid, type) {
	jsonRanges = getRanges(false);
   	$.ajax({
		url: 'ranges.php',
		type: 'GET',
		data: {taskid: taskid, type: type, ranges: jsonRanges, action: "add"},
		async: false,
		cache:false,
		crossDomain: true,
		success: function(response) {
			$("#divranges").html(response);
		},
  		error: function(response, xhr,err ) {
			alert("Error!");
		}
  	});
}
        
function getRanges(checkOnly) {
   	var jsonRanges="";
    var table = document.getElementById("rangesTable");
	if (table != null) {
 		var rowCount = table.rows.length;
        if (rowCount>0) {
        	var colCount = table.rows[0].cells.length;
        	//	var arrayVal = new Array(rowCount);
 			for(var x=1; x<rowCount; x++) {
            	var item="";
            	for(var y=0; y<colCount; y++) {
            		var val=table.rows[x].cells[y].childNodes[0].value;
            		if (typeof val == 'undefined') {
            			val = "";
     				}
     				val = val.trim();
     				if (y==0) {
     					item += '{"val": "'+val+'",';
     				} else if (y==1) {
     					item += '"label": "'+val+'",';
     				} else if (y==2) {
     					if (val=="") {
     						val="444444";
     					}
     					item += '"color": "'+val+'"}';
     				}     				
     			}
     			if (item != "") {
     				if (jsonRanges != "")
            			jsonRanges += ",";		
     				jsonRanges += item;
     			}
     		}
     	}
     }
     //alert(jsonRanges);
     return "["+jsonRanges+"]";
}
 
 
function deleteRow(tableID,item) {
	try {
    	var table = document.getElementById(tableID);
       	var rowCount = table.rows.length;
		if(rowCount <= 2) {
        	alert("Cannot delete all values.");
        	return;
        }            
        for(var i=0; i<rowCount; i++) {
        	if (i==item) {
            	table.deleteRow(i);
                break;
            } 
        }
	} catch(e) {
    	alert(e);
	}
}
        
function showRanges(taskid, type, jsonRanges) {
	if (jsonRanges == null) {
		jsonRanges = getRanges(false);
		//alert(jsonRanges);
   	}
   	$.ajax({
		url: 'ranges.php',
		type: 'GET',
		data: {taskid: taskid, type: type, ranges: jsonRanges},
		async: false,
		cache:false,
		crossDomain: true,
		success: function(response) {
			$("#divranges").html(response);
		},
		error: function(response, xhr,err ) {
			alert("Error!");
		}
	});
}
	
function defaultRanges(type) {
	if (type == "") { 
		var selectel = document.getElementById("type");
		var type = selectel.options[selectel.options.selectedIndex].value;
	}
	if (type == "") {
    	alert("WARNING! Choose the type of the task to set a default values.");
    	return;
	} else {
		jsonRanges = getRanges(true);
		if (jsonRanges != "[]" && jsonRanges != DEFAULT_RANGES && 
			jsonRanges != DEFAULT_ERRORS_RANGES && jsonRanges != DEFAULT_WORDALIGN_RANGES && jsonRanges != DEFAULT_DOCANN_RANGES) {
			if (!confirm("Do you want to discard your customization and use the default one?")) {
				return;
			}
		}
	
		var ranges ="";
		if (type == "quality") {
			ranges = "[{\"val\": \"1\",\"label\": \"1\",\"color\": \"FFFF99\"},{\"val\": \"2\",\"label\": \"2\",\"color\": \"D9FF99\"},{\"val\": \"3\",\"label\": \"3\",\"color\": \"B2FF99\"},{\"val\": \"4\",\"label\": \"4\",\"color\": \"8CFF99\"},{\"val\": \"5\",\"label\": \"5\",\"color\": \"66FF99\"}]";
		} else if (type == "errors") {
			ranges = "[{\"val\": \"0\",\"label\": \"No errors\",\"color\": \"C8E8B8\"},{\"val\": \"1\",\"label\": \"Too many errors\",\"color\": \"F0D3A8\"},{\"val\": \"2\",\"label\": \"Reordering errors\",\"color\": \"69B4FF\"},{\"val\": \"3\",\"label\": \"Wrong lexical choice\",\"color\": \"B296FF\"},{\"val\": \"4\",\"label\": \"Missing word(s)\",\"color\": \"E9FF24\"},{\"val\": \"5\",\"label\": \"Superfluous word(s)\",\"color\": \"91F0FF\"},{\"val\": \"6\",\"label\": \"Morphology errors\",\"color\": \"FF9EFF\"},{\"val\": \"7\",\"label\": \"Casing and punctuation errors\",\"color\": \"A6445B\"}]";
		} else if (type == "wordaligner") {
			ranges = "[{\"val\": \"0\",\"label\": \"Possible alignment\",\"color\": \"BABABA\"},{\"val\": \"1\",\"label\": \"Sure alignment\",\"color\": \"000000\"}]";
		}
		if (ranges != "") {
			showRanges(-1, type, ranges);
		}
	}
}

function setMandatoryRanges(selectel) {
	var type = selectel.options[selectel.options.selectedIndex].value;
	
	if (type == "errors") {
		showRanges(-1, type, DEFAULT_ERRORS_RANGES);
	} else if (type == "wordaligner") {
		showRanges(-1, type, DEFAULT_WORDALIGN_RANGES);
	} else if (type == "docann") {
		showRanges(-1, type, DEFAULT_DOCANN_RANGES);
	} else {
		showRanges(-1, type, "");
		addRange(-1,'');
	}
}


function send(form) {
	jsonRanges = getRanges(false);
	form.ranges.value = jsonRanges;
   	form.update.value = "yes";
   	form.submit();
}
	

// And you can add/remove markItUp! whenever you want
	// $(textarea).markItUpRemove();
	function markup() {
		if ($("#markItUp.markItUpEditor").length === 1) {
 			$("#markItUp").markItUp('remove');
			$("#markupbutton").html("enable HTML editor");
		} else {
			$('#markItUp').markItUp(mySettings);
			$("#markupbutton").html("disable HTML editor");
		}
 		return false;
	};
</SCRIPT>
</head>

<div style='margin: 10px; vertical-align: top; top: 0px; display: inline-block'>
<div style='float: left; vertical-align: top; top: 0px; display: inline-block; padding-right: 0px; margin: 0px'>

<?php

/*if (isset($_POST['txt'])) {
	$txtbox = $_POST['txt'];
	$color = $_POST['color'];
 
	foreach($txtbox as $a => $b) {
		if (isset($txtbox[$a]) && isset($color[$a])) {
	  		echo "$txtbox[$a]  -  $color[$a] <br />";
		}	
	}
}*/
$sentypes = getSentenceType();
 	
if ($mysession["status"] == "root" || $mysession["status"] == "admin" || $mysession["status"] == "advisor") { 
	$sentlabel="Create";
	$cancelbutton="<input type=button onclick=\"javascript:window.open('admin.php?section=task','_self');\"  value='Cancel'> ";
	$visibility_tform="visible";
	$taskinfo = array("name" => "",
				  "type" => "",
				  "descr" => "",
				  "randout" => "N",
				  "instructions" => "",
				  "ranges" => "[]",
				  "owner" => $mysession['userid']);
	if (isset($ranges) && $ranges != "[]") {
		$taskinfo["ranges"] = $ranges;
	}
	if (isset($id)) {
	$tasks = getTasks($mysession["userid"]);
	if ($id<0 || isset($tasks{$id})) {
		$query = "";
		if (isset($action) && $action="remove") {		
			if (removeTask($id) == 1) {
				$id=-1;
				print "<script>alert('The task information and all annotations refering to it have been removed.'); \nwindow.open(\"admin.php?section=task\", \"_self\");</script>"; 	
				##print "<button style='position: absolute;' onclick=\"this.style.visibility='hidden'; document.getElementById('tform').style.visibility='visible';\">Create a new task</button>";
				##$visibility_tform="hidden";
			} else {
				print "<script>alertify.alert('ERROR! The task hasn't been removed correctly.'); </script>"; 
			}
		} else {
			if ($id == -1 || (isset($update) && $update=="yes")) {
				while (list ($key,$val) = each($taskinfo)) {
					if (isset($$key)) {
						if ($key == "randout" && $$key == "on") {
							$$key = "Y";
						} 
						$taskinfo[$key] = $$key;
					}
					$query .= ",$key=\"".addslashes($taskinfo[$key])."\"";				
				}	
				#print "Q: " .$query ."<br>";		 
			} else {
				$taskinfo = getTaskInfo($id);
				$sentlabel = "Update";
				$cancelbutton = "<input type=button onclick=\"javascript:window.open('admin.php?section=task','_self');\"  value='Cancel'> ";	
		
			}
			
			if ($id == -1) {
				if ($taskinfo["name"] != "" && $taskinfo["type"] != "") {
					$res = safe_query("INSERT INTO task (name,owner) VALUES ('_NEW_','".$mysession['userid']."');");
					if ($res == 1) {
						$id = mysql_insert_id();
					}
				} else {
					if ($taskinfo["type"] == "") {
						print "<br><font color=red>WARNING!</font> The type is mandatory.<br>";
					} else if ($taskinfo["name"] == "") {
						print "<br><font color=red>WARNING!</font> The name is mandatory.<br>";
					}  
					$cancelbutton = "<input type=button onclick=\"javascript:window.open('admin.php?section=task','_self');\"  value='Cancel'> ";
				}
			}
			if (!empty($query) && $id != -1) {
				$query = "UPDATE task SET ".substr($query, 1). " WHERE id=$id";
				if (safe_query($query) != 1) {
					print "<script>alertify.alert('ERROR! This user has not been saved correctly.');</script>"; 	
				} else {
					print "<script>window.open(\"admin.php?section=task\", \"_self\");</script>"; 
				}
				#print "QUERY: $query<br>";
				$sentlabel = "Update";
				$cancelbutton = "<input type=button onclick=\"javascript:window.open('admin.php?section=task','_self');\"  value='Cancel'> ";	
		
			}
		}		
	} else {
		$id = "-1";
	}
}
if (!isset($id) || $id<0 || !isset($tasks{$id})) {
	$id = -1;
	print "<button style='position: absolute; margin-left: 0px; float: left;' onclick=\"this.style.visibility='hidden'; document.getElementById('tform').style.visibility='visible';\">Create a new task</button>";
	$visibility_tform="hidden";
}
?>
</div>

<div style="white-space: nowrap; float: left; padding-left: 2px; display: inline; position: relative; top: 20px">
<?php	
	reset($tasks);
	$ttype = "";
	while (list ($tid,$tarr) = each($tasks)) {
		if ($tarr[1] != $ttype) {
			$ttype = $tarr[1];
			print "<br><b>".$taskTypes[$ttype] ." tasks</b><hr>";
		}
		if (isset($id) && $id == $tid) {
			print "<div style='position: absolute; padding-bottom: 2px; left:0px; background: #5c0120'>&nbsp;&nbsp;</div><li type=square class=selected>";
		} else {
			print "<li type=square class=row>";
		}
		print "<a href=\"javascript:delTask($tid);\"><img border=0 width=12 src='img/remove.png'></a> <a href='admin.php?section=task&id=$tid'>".$tarr[0]."</a> <font color=#444 title='this task has $tarr[2] annotations'>[".$tarr[2]."]</font></li>";
	}	
}

?>
<br><br>
</div>

<form id="tform" style='margin-right: -2px; border: 2px solid #5c0120; float:left; visibility: <?php echo $visibility_tform; ?>' name="tform" heigth=80 action="admin.php?section=task" method="post" enctype="multipart/form-data">
  <input type=hidden name="id" value="<?php echo $id; ?>">
  <input type=hidden name="ranges" id="ranges" value="">
  <input type=hidden name="update" value="">
  <table border=0 cellspacing=0 cellpadding=4>
  <tr><th bgcolor=#ddd align=right>Task type: </th><td>
  	<?php	
  	if ($id < 0) {
		print "<select id=\"type\" name=\"type\" onchange=\"setMandatoryRanges(this)\"><option value=''> -- SELECT --\n";
		while (list ($stype, $ltype) = each($taskTypes)) {
			print "<option value='$stype'";
			if ($stype == $taskinfo['type']) {
				print " selected";
			}
			print ">".$ltype."\n";
		}
	} else {
		print "<input type=hidden name=\"type\" value=\"".$taskinfo['type']."\">" .$taskTypes[$taskinfo['type']];
	}
	?>
	</select></td></tr>
  <tr><th bgcolor=#ddd align=right>
 	Task name:</th><td><input TYPE=text name="name" value="<?php echo $taskinfo['name']; ?>"> <font size=-1 color=gray>(es. TEST_Errors_EN-AR-ZH)</font> </td></tr>
<tr><th bgcolor=#ddd align=right valign=top>Short description:</th><td> <textarea rows="3" cols="50" NAME="descr" ><?php echo stripslashes($taskinfo['descr']); ?></textarea></td></tr>

<tr><th bgcolor=#ddd align=right valign=top>Instructions:</th><td> 
<button onclick="return markup();" id=markupbutton>enable HTML editor</button><br>
<textarea id="markItUp" rows="7" cols="50" NAME="instructions"><?php echo stripslashes($taskinfo['instructions']); ?></textarea>
</td></tr>

<tr><th bgcolor=#ddd align=left colspan=2>Show systems output randomly: <input type="checkbox" name=randout
<?php
if ($taskinfo['randout'] == "Y") {
	print " checked";
}
?>
></th></tr>

<tr><th bgcolor=#ddd align=left>Task customization<a href="#"><img src="img/question.png" width=18 onclick="alertify.alert('<table><td align=left><b>Quality</b>: You can choose the number of points in the rating scale. The `Label` of each point must be a number (typically from 0 or 1 to n), and correspond to the number that appears in the rating scale in the annotation interface. You can also choose the color displayed for each point in the scale.<hr><b>Error</b>: You can choose the number and type of errors you want to annotate. The `Label` of each error type appears in the error menu in the annotation interface. You can also choose a color for each error: the word(s) annotated with errors are displayed underlined with the color associated to the error.<hr><b>Word Alignment</b>: You can choose how many types of alignment you want to annotate and assign a color to each alignment type. In the annotation interface, at each click in the matrix cell the color of each given alignment type is displayed following the order set for the task.</td></table>'); return false;"></a>:</th>
<td>

<?php
$alreadyUsedValues = getUsedValues($id);	
if (count($alreadyUsedValues) == 0 && $taskinfo['type'] != "docann") {
?>
<INPUT type="button" value="Use default setting" onclick="defaultRanges('<?php echo $taskinfo['type']; ?>')" /><a href="#"><img src="img/question.png" width=18 onclick="alertify.alert('<table><td align=left><b>Quality</b>: The default setting for the quality rating task is a 5-point rating scale\n<hr><b>Error</b>: The default error typology includes 6 classes: reordering errors, wrong lexical choices, missing word(s), superfluous word(s), morphology errors, casing and punctuation errors.\n<hr><b>Alignment</b>: The default setting for the word alignment task allows to annotate 2 alignment types, distinguishing between `possible` and `sure` alignments.</td></table>'); return false;"></a>
<?php
}
?>

</td></tr>
<tr><td colspan=2>
    <div id="divranges"></div>
</td></tr>

<tr><td align=right colspan=2 align=center><div style="height: 100%; display: inline; top: 0px"><?php echo $cancelbutton; ?> 
<!-- <input type="submit" name=update value="<?php echo $sentlabel; ?>"> -->
<button onclick="send(document.tform)"><?php echo $sentlabel; ?></button></div></td></tr>
  </table>

</form>
</div>

<?php
	#show the task ranges
	if (isset($taskinfo["ranges"]) && $taskinfo["ranges"] != "[]" && $taskinfo["ranges"] != "") {
		print "<script>showRanges($id,\"".$taskinfo["type"]."\",\"".$taskinfo["ranges"]."\");</script>";
	} else {
		print "<script>addRange($id,'');</script>";
	}
?>
