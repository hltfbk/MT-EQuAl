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
if ($mysession["status"] == "admin") { 
	$sentlabel="Create";
	$cancelbutton="";
	$tasklist = getTasks($mysession["username"]);	
	$userinfo = array("name" => "",
				  "username" => "",
				  "password" => "",
				  "email" => "",
				  "status" => "",
				  "team" => "",
				  "activated" => "N",
				  "notes" => "");
	
	if (isset($id)) {
		$query = "";
		if (isset($action) && $action="remove") {		
			if (removeUser($id) == 1) {
				$id=-1;
				print "<img width=15 src='img/done.png'> DONE! The user information and all done annotations by him have been removed correctly.<br>"; 	
			}
		} else {
			if ($id == -1 || (isset($update) && $update=="Update")) {
				while (list ($key,$val) = each($userinfo)) {
					if (isset($$key)) {
						if ($key == "activated" && $$key == "on") {
							$$key = "Y";
						} 
						$userinfo[$key] = $$key;
					}
					$query .= ",$key=\"".$userinfo[$key]."\"";				
				}
				if (isset($utasks) && is_array($utasks)) {
					$tasknames = "";
					foreach ($utasks as $tid) {
						if ($tid == "all") {
							$tasknames .= " all";
						} else {
							$tasknames .= " ".$tasklist[$tid][0]; 
						}
					}
					$userinfo["tasks"]  = trim($tasknames);
					$query .= ",tasks=\"".$userinfo["tasks"]."\"";
				} else {
					$userinfo["tasks"] ="";
				}	  
			} else {
				$userinfo = getUserInfo($id);
			}
			$sentlabel = "Update";
			$cancelbutton = "<input type=button onclick=\"javascript:window.open('admin.php?section=user','_self');\"  value='Cancel'> ";	
			if ($id == -1) {
				$res = safe_query("INSERT INTO user (name,registered) VALUES ('_NEW_',now());");
				if ($res == 1) {
					$id = mysql_insert_id();
				}
			}
			if (!empty($query) && $id != -1) {
				$query = "UPDATE user SET ".substr($query, 1). " WHERE id=$id";
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
<form heigth=80 action="admin.php?section=user" method="post" enctype="multipart/form-data">
  <table border=0 cellspacing=0 cellpadding=4>
  <input type=hidden name=id value="<?php if (isset($id)) {echo $id;} else { echo '-1';} ?>">
  <tr><td>Full name:</td><td><input TYPE=text name="name" size=30 value="<?php echo $userinfo['name']; ?>"></td></tr>
  <tr><td>Login name:</td><td><input TYPE=text name="username" size=15 value="<?php echo $userinfo['username']; ?>"></td></tr>
  <tr><td>E-mail:</td><td><input TYPE=text name="email" size=30 value="<?php echo $userinfo['email']; ?>"></td></tr>
  <tr><td>Password:</td><td><input TYPE=text name="password" size=15 value="<?php echo $userinfo['password']; ?>"></td></tr>
  <tr><td>User type: </td><td><select name="status">
  <?php
	foreach ($userTypes as $utype) {
		print "<option value='$utype'";
		if ($utype == $userinfo['status']) {
			print " selected";
		}
		print "> ".ucfirst(strtolower($utype));
	}
  ?>
  </select></td></tr>
  <tr><td>
  <tr><td valign=top>Tasks:</td><td><select multiple="multiple" name="utasks[]" size=8 style='font-size: 13px'>
  <?php	
	$utasks = split(" ",$userinfo['tasks']);
	print "<option value='all'";
	if (in_array("all", $utasks)) {
		print " selected";
	}
	print "> all\n";
  	while (list ($tid,$tarr) = each($tasklist)) {
		print "<option value='$tid'";
		if (in_array($tarr[0], $utasks)) {
			print " selected";
		}	
		print "> ".$tarr[0];
	}	
?>
  </select></td></tr>
 <tr><td>Team:</td><td><input TYPE=text name="team" value="<?php echo $userinfo['team']; ?>"></td></tr>
 <tr><td>Notes:</td><td> <textarea rows="4" cols="50" name="notes" value="<?php echo $userinfo['notes']; ?>"><?php if (isset($notes)) { echo $notes;} ?></textarea></td></tr>
<tr><td>Activated:</td><td> <input type="checkbox" name="activated"
<?php
	if ($userinfo['activated'] == "Y") {
		print " checked";
	}
?>
></td></tr>
<tr><td align=right colspan=2><?php echo $cancelbutton; ?> <input type="submit" name=update value="<?php echo $sentlabel; ?>"></td></tr>

  </table>
</form>
</div>

<div style="float:left; border-left: 1px solid #000; padding: 2px; display: inline-block; position: relative; top: 10px">ALL USERs<hr>
<?php	
	$userlist = getUserStats();
	while (list ($uid,$uarr) = each($userlist)) {
		$tasknum = $uarr[2];
		if ($tasknum != "all") {
			$tasknum = count(split(" ",$uarr[2]));
		}
		if (isset($id) && $id == $uid) {
			print "<li type=square class=selected>";
		} else {
			print "<li type=square class=row>";
		}
		print "<a href=\"javascript:delUser($uid);\"><img border=0 width=12 src='img/remove.png'></a> ";
		if ($uarr[4] == "N") {
			print "<s><font color=#999 title='this user is not active'>";
		}
		print $uarr[0];
		if ($uarr[4] == "N") {
			print "</font></s>";
		}
		print " - <a href='admin.php?section=user&id=$uid'>". $uarr[1]."</a> (tasks: $tasknum)";
	}	

?>
</div>
</div>
<?php
} else {
	print "WARNING! You don't have enoght permission to modify user accounts.";
}
?>