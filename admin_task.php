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
li.row:hover {
	background:#cf4;
}
li.selected {
	background:#cdcdcd;
}
</style>
</head>

<?php
$sentypes = getSentenceType();
	
if ($mysession["status"] == "admin") { 
	$sentlabel="Create";
	$cancelbutton="";
	$taskinfo = array("name" => "",
				  "type" => "",
				  "descr" => "",
				  "randout" => "N",
				  "owner" => $mysession['userid']);
	
	if (isset($id)) {
		$query = "";
		if (isset($action) && $action="remove") {		
			if (removeTask($id) == 1) {
				$id=-1;
				print "<img width=15 src='img/done.png'> DONE! The task information and all annotations refering to it have been removed correctly.<br>"; 	
			}
		} else {
			if ($id == -1 || (isset($update) && $update=="Update")) {
				while (list ($key,$val) = each($taskinfo)) {
					if (isset($$key)) {
						if ($key == "randout" && $$key == "on") {
							$$key = "Y";
						} 
						$taskinfo[$key] = $$key;
					}
					$query .= ",$key=\"".$taskinfo[$key]."\"";				
				}			 
			} else {
				$taskinfo = getTaskInfo($id);
			}
			$sentlabel = "Update";
			$cancelbutton = "<input type=button onclick=\"javascript:window.open('admin.php?section=task','_self');\"  value='Cancel'> ";	
		
			if ($id == -1) {
				$res = safe_query("INSERT INTO task (name,owner) VALUES ('_NEW_','".$mysession['userid']."');");
				if ($res == 1) {
					$id = mysql_insert_id();
				}
			}
			if (!empty($query) && $id != -1) {
				$query = "UPDATE task SET ".substr($query, 1). " WHERE id=$id";
				if (safe_query($query) != 1) {
					print "<img src='img/database_error.png'> ERROR! This user has not been saved correctly.<br>"; 
				}
				//print "QUERY: $query<br>";
			}
		}
	}
?>
<div style='margin: 10px; vertical-align: top; top: 0px; display: inline-block'>
<div style='margin: 10px; float: left; vertical-align: top; top: 0px; display: inline-block'>
<form heigth=80 action="admin.php?section=task" method="post" enctype="multipart/form-data">
  <input type=hidden name=id value="<?php if (isset($id)) {echo $id;} else { echo '-1';} ?>">
  <table border=0 cellspacing=0 cellpadding=4>
  <tr><td>Task type: </td><td><select name=type><option value=''> -- SELECT --
	<?php	
	foreach ($taskTypes as $stype) {
		print "<option value='$stype'";
		if ($stype == $taskinfo['type']) {
			print " selected";
		}
		print ">".ucfirst(strtolower($stype))."\n";
	}
	?>
	</select></td></tr>
  <tr><td>
 	Task name:</td><td><input TYPE=text name="name" value="<?php echo $taskinfo['name']; ?>"> <font size=-1 color=gray>(es. TEST_Errors_EN-AR-ZH)</font> </td></tr>
<tr><td>Description:</td><td> <textarea rows="4" cols="50" NAME="descr" ><?php echo $taskinfo['descr']; ?></textarea></td></tr>
<tr><td colspan=2>Show systems output randomly: <input type="checkbox" name=randout
<?php
if ($taskinfo['randout'] == "Y") {
	print " checked";
}
?>
></td></tr>
<tr><td align=right colspan=2><?php echo $cancelbutton; ?> <input type="submit" name=update value="<?php echo $sentlabel; 
?>"></td></tr>
  </table>
</form>
</div>

<div style="float:left; border-left: 1px solid #000; padding: 2px; display: inline-block; position: relative; top: 10px">ALL TASKs
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
?>
</div>
</div>
