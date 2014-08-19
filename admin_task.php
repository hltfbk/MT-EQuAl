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
li.row:hover {
	background:#cf4;
}
li.selected {
	background:#cdcdcd;
}
</style>

<SCRIPT language="javascript">
	function addRange() {
 		jsonRanges = getRanges(false);
     	$.ajax({
  			url: 'ranges.php',
			type: 'GET',
			data: {ranges: jsonRanges, action: "add"},
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
        	var arrayVal = new Array(rowCount);
 			for(var x=1; x<rowCount; x++) {
            	var item="";
            	for(var y=0; y<colCount; y++) {
            		var val=table.rows[x].cells[y].childNodes[0].value;
     				if (val=="") {
     					if (!checkOnly)
     						alert("WARNING! The values with empty field will be discarded.");
     					item="";
     					break;
     				}
     				
     				if (y==0) {
     					if (arrayVal[val] == true) {
     						alert("WARNING! A duplicate value found.");
     						break;
     					}
     					arrayVal[val] = true;
     					if (val=="" && !checkOnly) {
     						alert("WARNING! The value must be an integer.");
     					}
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
        
function showRanges(jsonRanges) {
	if (jsonRanges == null) {
		jsonRanges = getRanges(false);
		//alert(jsonRanges);
   	}
   	$.ajax({
		url: 'ranges.php',
		type: 'GET',
		data: {ranges: jsonRanges},
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
	jsonRanges = getRanges(true);
	if (jsonRanges != "[]") {
		if (!confirm("Do you want to discard your customization and use the default one?")) {
			return;
		}
	}
	var selectel = document.getElementById("type");
	var type = selectel.options[selectel.options.selectedIndex].value;
	if (type == "") {
    	alert("WARNING! Choose the type of the task to set a default values.");
	} else {
		var ranges ="";
		if (type == "quality") {
			ranges = "[{\"val\": \"1\",\"label\": \"1\",\"color\": \"FFFF99\"},{\"val\": \"2\",\"label\": \"2\",\"color\": \"D9FF99\"},{\"val\": \"3\",\"label\": \"3\",\"color\": \"B2FF99\"},{\"val\": \"4\",\"label\": \"4\",\"color\": \"8CFF99\"},{\"val\": \"5\",\"label\": \"5\",\"color\": \"66FF99\"}]";
		} else if (type == "errors") {
			ranges = "[{\"val\": \"0\",\"label\": \"No errors\",\"color\": \"12DE19\"},{\"val\": \"1\",\"label\": \"Too many errors\",\"color\": \"CF0E35\"},{\"val\": \"2\",\"label\": \"Reordering errors\",\"color\": \"4760FF\"},{\"val\": \"3\",\"label\": \"Wrong lexical choice\",\"color\": \"FFAC59\"},{\"val\": \"4\",\"label\": \"Missing word(s)\",\"color\": \"E9FF24\"},{\"val\": \"5\",\"label\": \"Superfluous word(s)\",\"color\": \"91F0FF\"},{\"val\": \"6\",\"label\": \"Morphology errors\",\"color\": \"F563FF\"},{\"val\": \"7\",\"label\": \"Casing and punctuation errors\",\"color\": \"575757\"}]";
		} else if (type == "wordaligner") {
			ranges = "[{\"val\": \"0\",\"label\": \"Possible alignment\",\"color\": \"BABABA\"},{\"val\": \"1\",\"label\": \"Sure alignment\",\"color\": \"000000\"}]";
		}
		if (ranges != "") {
			showRanges(ranges);
		}
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
 	
if ($mysession["status"] == "admin") { 
	$sentlabel="Create";
	$cancelbutton="";
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
		$query = "";
		if (isset($action) && $action="remove") {		
			if (removeTask($id) == 1) {
				$id=-1;
				print "<img width=15 src='img/done.png'> DONE! The task information and all annotations refering to it have been removed correctly.<br>"; 	
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
				#print $query ."<br>";		 
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
					print "<img src='img/database_error.png'> ERROR! This user has not been saved correctly.<br>"; 
				}
				//print "QUERY: $query<br>";
				$sentlabel = "Update";
				$cancelbutton = "<input type=button onclick=\"javascript:window.open('admin.php?section=task','_self');\"  value='Cancel'> ";	
		
			}
		}
	}
?>

<div style='margin: 10px; vertical-align: top; top: 0px; display: inline-block'>
<div style='margin: 10px; float: left; vertical-align: top; top: 0px; display: inline-block'>
<form id="tform" name="tform" heigth=80 action="admin.php?section=task" method="post" enctype="multipart/form-data">
  <input type=hidden name="id" value="<?php if (isset($id)) {echo $id;} else { echo '-1';} ?>">
  <input type=hidden name="ranges" id="ranges" value="">
  <input type=hidden name="update" value="">
  <table border=0 cellspacing=0 cellpadding=4>
  <tr><th bgcolor=#ddd align=right>Task type: </th><td><select id="type" name="type"><option value=''> -- SELECT --
	<?php	
	while (list ($stype,$ltype) = each($taskTypes)) {
		print "<option value='$stype'";
		if ($stype == $taskinfo['type']) {
			print " selected";
		}
		print ">".$ltype."\n";
	}
	?>
	</select></td></tr>
  <tr><th bgcolor=#ddd align=right>
 	Task name:</th><td><input TYPE=text name="name" value="<?php echo $taskinfo['name']; ?>"> <font size=-1 color=gray>(es. TEST_Errors_EN-AR-ZH)</font> </td></tr>
<tr><th bgcolor=#ddd align=right valign=top>Short description:</th><td> <textarea rows="3" cols="50" NAME="descr" ><?php echo $taskinfo['descr']; ?></textarea></td></tr>

<tr><th bgcolor=#ddd align=right valign=top>Instructions:</th><td> 
<button onclick="return markup();" id=markupbutton>enable HTML editor</button><br><textarea id="markItUp" rows="7" cols="50" NAME="instructions">
<?php echo $taskinfo['instructions']; ?>
</textarea>
</td></tr>

<tr><th bgcolor=#ddd align=left colspan=2>Show systems output randomly: <input type="checkbox" name=randout
<?php
if ($taskinfo['randout'] == "Y") {
	print " checked";
}
?>
></th></tr>

<tr><th bgcolor=#ddd align=left>Task customization<a href="#"><img src="img/question.png" width=18 onclick="alertify.alert('<table><td align=left><b>Quality</b>: You can choose the number of points in the rating scale. The `Label` of each point must be a number (typically from 0 or 1 to n), and correspond to the number that appears in the rating scale in the annotation interface. You can also choose the color displayed for each point in the scale.<hr><b>Error</b>: You can choose the number and type of errors you want to annotate. The `Label` of each error type appears in the error menu in the annotation interface. You can also choose a color for each error: the word(s) annotated with errors are displayed underlined with the color associated to the error.<hr><b>Word Alignment</b>: You can choose how many types of alignment you want to annotate and assign a color to each alignment type. In the annotation interface, at each click in the matrix cell the color of each given alignment type is displayed following the order set for the task.</td></table>'); return false;"></a>:</th>
<td><INPUT type="button" value="Use default setting" onclick="defaultRanges()" /><a href="#"><img src="img/question.png" width=18 onclick="alertify.alert('<table><td align=left><b>Quality</b>: The default setting for the quality rating task is a 5-point rating scale\n<hr><b>Error</b>: The default error typology includes 6 classes: reordering errors, wrong lexical choices, missing word(s), superfluous word(s), morphology errors, casing and punctuation errors.\n<hr><b>Alignment</b>: The default setting for the word alignment task allows to annotate 2 alignment types, distinguishing between `possible` and `sure` alignments.</td></table>'); return false;"></a></td></tr>
<tr><td colspan=2>
    <div id="divranges"></div>
</td></tr>

<tr><td align=right colspan=2><?php echo $cancelbutton; ?> 
<!-- <input type="submit" name=update value="<?php echo $sentlabel; ?>"> -->
<button onclick="send(document.tform)"><?php echo $sentlabel; ?></button><hr></td></tr>
  </table>
</form>
</div>

<div style="float: right; right: 0px; border-left: 1px solid #000; padding: 2px; display: inline-block; position: relative; top: 10px">ALL TASKs
<?php	
	$tasks = getTasks($mysession["username"]);
	$ttype = "";
	while (list ($tid,$tarr) = each($tasks)) {
		if ($tarr[1] != $ttype) {
			$ttype = $tarr[1];
			print "<hr><b>".ucfirst($ttype) ." tasks</b><br>";
		}
		if (isset($id) && $id == $tid) {
			print "<li type=square class=selected>";
		} else {
			print "<li type=square class=row>";
		}
		print "<a href=\"javascript:delTask($tid);\"><img border=0 width=12 src='img/remove.png'></a> <a href='admin.php?section=task&id=$tid'>".$tarr[0]."</a></li>";
	}	
}
if (isset($taskinfo["ranges"]) && $taskinfo["ranges"] != "[]" && $taskinfo["ranges"] != "") {
	print "<script>showRanges(\"".$taskinfo["ranges"]."\");</script>";
} else {
	print "<script>addRange();</script>";
}
?>
</div>
</div>
