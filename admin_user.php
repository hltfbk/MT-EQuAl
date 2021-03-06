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
</head>

<div style='margin: 10px; vertical-align: top; top: 0px; display: inline-block'>
<div style='float: left; vertical-align: top; top: 0px; display: inline-block; padding-right: 0px; margin: 0px'>

<?php
if ($mysession["status"] == "root" || $mysession["status"] == "admin" || $mysession["status"] == "advisor") { 
	$userlist = getUserStats($mysession["userid"],$mysession["status"]);

	$sentlabel="Create";
	$cancelbutton="<input type=button onclick=\"javascript:window.open('admin.php?section=user','_self');\"  value='Cancel'> ";
	$visibility_tform="visible";
	$userinfo = array("name" => "",
				  "username" => "",
				  "password" => "",
				  "email" => "",
				  "status" => "",
				  "team" => "",
				  "activated" => "N",
				  "notes" => "");
	
	if (isset($id) && ($id<0 || array_key_exists($id, $userlist))) {
		$query = "";
		if (isset($action) && $action="remove") {		
			if (removeUser($id) == 1) {
				$id=-1;
				print "<script>alertify.alert('The user information and all his annotations have been removed correctly.'); </script>"; 	
				#print "<button style='position: absolute; margin-left: 0px; float: left;' onclick=\"showForm(this);\">Create a new user</button>";
				#$visibility_tform="hidden";
			}
		} else {
			if ($id == -1 || (isset($update) && $update=="Update")) {
				while (list ($key,$val) = each($userinfo)) {
					if (isset($$key)) {
						if ($key == "activated" && $$key == "on") {
							$$key = "Y";
						} 
						$userinfo[$key] = trim($$key);
						#print "$key: ".$userinfo[$key] ."<br>";
					}
					$query .= ",$key=\"".$userinfo[$key]."\"";				
				}	  
			} else {
				$userinfo = getUserInfo($id);
				$sentlabel = "Update";
				$cancelbutton = "<input type=button onclick=\"javascript:window.open('admin.php?section=user','_self');\"  value='Cancel'> ";	
			}
			
			if ($id == -1) {
				if ($userinfo["name"] != "" && $userinfo["username"] != "" && $userinfo["password"] != "") {
					$res = safe_query("INSERT INTO user (name,registered,refuser) VALUES ('_NEW_',now(),".$mysession["userid"].");");
					if ($res == 1) {
						$id = mysql_insert_id();
					}
				} else {
					if ($userinfo["name"] == "") {
						print "<font color=red>WARNING!</font> The name is mandatory.<br>";
					} else if ($userinfo["username"] == "") {
						print "<font color=red>WARNING!</font> The username is mandatory.<br>";
					} else if ($userinfo["password"] == "") {
						print "<font color=red>WARNING!</font> The password is mandatory.<br>";
					} 
					$cancelbutton = "<input type=button onclick=\"javascript:window.open('admin.php?section=user','_self');\"  value='Cancel'> ";
				}
			}
			if (!empty($query) && $id != -1) {
				$query = "UPDATE user SET ".substr($query, 1). " WHERE id=$id";
				if (safe_query($query) != 1) {
					print "<img src='img/database_error.png'> ERROR! This user has not been saved correctly.<br>"; 
				} else {
					print "<script>window.open(\"admin.php?section=user\", \"_self\");</script>"; 
				}
				removeUserTask($id, 0);
				if (isset($utasks) && is_array($utasks)) {
					foreach ($utasks as $tid) {
						addUserTask($id ,$tid);
					}
				}
				//print "QUERY: $query<br>";
				$sentlabel = "Update";
				$cancelbutton = "<input type=button onclick=\"javascript:window.open('admin.php?section=user','_self');\"  value='Cancel'> ";	
			}
		}
	} else {
		$id = -1;
		print "<button id='newuser' style='position: absolute; margin-left: 0px; float: left;' onclick=\"showForm(this);\" disabled>Create a new user</button>";
		$visibility_tform="hidden";
	}
?>
<span class='spinner'><img width=25 src='img/spinner.gif' valign=bottom></span>
</div>
<div style="white-space: nowrap; float: left; left: 0px; padding-left: 2px; display: inline-block; position: relative; top: 20px">
<?php	
$userlist = getUserStats($mysession["userid"],$mysession["status"]);
$prev_status = "";
if (count($userlist) > 0) {
	while (list ($uid,$uarr) = each($userlist)) {
		$tasknum = count(getTasks($uid));
		if ($uarr[3] != $prev_status) {
			$prev_status = $uarr[3];
			print "<br><b>".ucfirst($prev_status)." users</b><hr>";
		}
		if (isset($id) && $id == $uid) {
			print "<div style='position: absolute; padding-bottom: 3px; left:0px; background: #5c0120'>&nbsp;&nbsp;</div><li type=square class=selected>";
		} else {
			print "<li type=square class=row>";
		}
		if ($mysession["userid"] != $uid) {
			print "<a href=\"javascript:delUser($uid);\"><img border=0 width=12 src='img/remove.png'></a> ";
		} else {
			print "&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		if ($uarr[2] == "N") {
			print "<s><font color=#666 title='this user is not active'>".$uarr[0]."</font></s>";
		} else {
			print $uarr[0];
		}
		print " - <a href='admin.php?section=user&id=$uid' title='tasks: $tasknum";
		if ($tasknum > 0) {
			$doneInfo = getUserLastDone($uid);
			print "\ndone: ".$doneInfo[0];
			if ($doneInfo[0] > 0) {
		 		print "\nlast: ".$doneInfo[1];
			}
		}
		print "'>". $uarr[1]."</a></li>";
		
	}	
}
?>
<br><br>
</div>

<form id="tform" style='margin-right: -2px; border: 2px solid #5c0120; float:left; visibility: <?php echo $visibility_tform; ?>' name="tform" heigth=80 action="admin.php?section=user" method="post" enctype="multipart/form-data">
  <input type=hidden name="id" value="<?php echo $id; ?>">
  <table border=0 cellspacing=0 cellpadding=4>
  <tr><th bgcolor=#ddd align=right>Full name<font color=darkred>*</font>:</th><td><input TYPE=text name="name" size=30 value="<?php echo $userinfo['name']; ?>"></td></tr>
  <tr><th bgcolor=#ddd align=right>User name<font color=darkred>*</font>:</th><td><input TYPE=text name="username" size=15 value="<?php echo $userinfo['username']; ?>"></td></tr>
  <tr><th bgcolor=#ddd align=right>Password<font color=darkred>*</font>:</th><td><input TYPE=password name="password" size=15 value="<?php echo $userinfo['password']; ?>"></td></tr>
  <tr><th bgcolor=#ddd align=right>E-mail:</th><td><input TYPE=text name="email" size=30 value="<?php echo $userinfo['email']; ?>"></td></tr>
  <tr><th bgcolor=#ddd align=right>User type: </th><td><select name="status">
  <?php
	foreach (array_keys($userTypes) as $utype) {
		print "<option value='$utype'";
		if ($utype == $userinfo['status']) {
			print " selected";
		}
		print "> ".ucfirst(strtolower($utype));
	}
  ?>
  </select></td></tr>
 <tr><th bgcolor=#ddd align=right>Team:</th><td><input TYPE=text name="team" value="<?php echo $userinfo['team']; ?>"></td></tr>
 <tr><th bgcolor=#ddd align=right valign=top>Notes:</th><td> <textarea rows="4" cols="50" name="notes" value="<?php echo $userinfo['notes']; ?>"><?php if (isset($notes)) { echo $notes;} ?></textarea></td></tr>
 <tr><th bgcolor=#ddd align=right valign=top>Tasks:</th><td><i><font size=-1>(hold CTRL for multiple selection)</font></i><br>
  <select multiple="multiple" name="utasks[]" size=8 style='font-size: 13px'><option value=''>none

<?php	
	$allTasks = getTasks($id);
	
	$ttype = "";
	$tasklist = getTasks($mysession["userid"]);
  	while (list ($tid,$tarr) = each($tasklist)) {	
  		if ($tarr[1] != $ttype) {
			$ttype = $tarr[1];
			print "<option value='' disabled='disabled'>--- ".ucfirst($ttype) ." tasks --- \n";
		}
			
		print "<option value='$tid'";
		if (isset($allTasks[$tid])) {
			print " selected";
		}	
		print "> ".$tarr[0];
	}	
?>
</select></td></tr>
<tr><th bgcolor=#ddd align=right>Active:</th><td> <input type="checkbox" name="activated"
<?php
	if ($userinfo['activated'] == "Y") {
		print " checked";
	}
?>
></td></tr>
<tr><td align=right colspan=2 align=center><?php echo $cancelbutton; ?> <input type="submit" name=update value="<?php echo $sentlabel; ?>"></td></tr>

  </table>
</form>

<?php
	print "</div>";
} else {
	print "WARNING! You don't have enough permission to modify user accounts.";
}
?>
<script>
$('.spinner').hide();
var button = document.getElementById('newuser');
if (button != null) {
	button.disabled = false;
}
</script>