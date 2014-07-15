<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<link href="css/bootstrap.min.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<style type="text/css">
.scrollable-menu {
	z-index: 1001;
	width: 250px;
    height: auto;
    max-height: 400px;
    overflow-x: hidden;
}
</style>

<body>
    <div style="margin-left:50px; position: fixed">
    	<img style="float:left;" alt="" src="img/menu/menu_left.png"/>
        <ul id="menu">
            <?php
            if (empty($mysession["status"])) {
            	
            ?>
            <li><form style="margin: 0px" method="post" name="form" action="index.php">
<input type="hidden" name="sent" value="login">
&nbsp;&nbsp;User: <input type="text" name="login" maxlength=30 size=10 value="">
&nbsp;&nbsp;Password: <input type="password" name="password" maxlength=30 size=10>
&nbsp;&nbsp;<input type=submit value="Login" name="auth">
</form></li>
			<?php
			} else {
			?>
			<li style="width: 150px;"><a href="index.php?task=<?php echo $mysession["tasknow"]; ?>"<b><?php echo str_replace("_"," ",$mysession["tasknow"]); ?></b></a>&nbsp;</li>
        	
			<li class="taskContainer">
                <div>Tasks</div>
                <ul class="dropdown-menu scrollable-menu" role="menu">
                <!-- <ul style="margin-left=120px; z-index:999;" id="task"> -->
                    <?php
                   	$tasks = getTasks($mysession["username"]);
    				foreach ($tasks as $task) {
                   		print "<li><a href='index.php?task=".$task[0]."'>".str_replace("_"," ",ucfirst($task[0]))."</a></li>\n";
                   	}                  		        	                	
                    ?>
                    <li class="last">
                        <img class="corner_left" alt="" src="img/menu/corner_left.png"/>
                        <img class="middle" alt="" src="img/menu/dot.gif"/>
                        <img class="corner_right" alt="" src="img/menu/corner_right.png"/>
                    </li>
                </ul>
           		
            </li>
            <li><div style="margin-botton: 0px; float: right; display: inline; left: 10px">&nbsp;
    <?php
            	#if you are an admin
            	if ($mysession["status"] == "admin" || $mysession["status"] == "advisor") {
            		print "<a href='admin.php'>Admin</a>\n";  	
            	}
            	print "</div></li>\n";
			
            }
	?>
            <li style="width: 80px"><a href="#">Help</a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="credits.php">Credits</a></li>
                </ul>
            </li>
            <?php
            	if (!empty($mysession["status"])) {
           			print "<li><a href='index.php?logout=yes'>Logout: <b>" .$mysession["username"] ."</b></a></li>";
           		} 	
           	?> 
           	
        </ul>
        <img style="float:left;" alt="" src="img/menu/menu_right.png"/>
    </div>
    <div style="float:none; clear:both;"></div>
    <div style="margin-left: 60px">
    <div class=index><center>
    <?php
    
    if (empty($mysession["status"])) {
            #print "<h3>This is an end user interface for the evaluation of Machine Translation systems</h3>\n<p>Sign in, please!</p>";
	} else if (empty($mysession["tasknow"])) {
		if ($mysession["status"] == "admin" || $mysession["status"] == "advisor") {
			#print "<br>Welcome " .$mysession["username"]."!";
		} else {
			print "<br><h4>Welcome! Choose a task to start your work.</h4>";
		}
	} 
	?>
	</center>
	</div>
    </div>
<div style='float: right; right: 0px; top:0px; display: inline-block; position: fixed;text-align: left; font-size: 12px; padding-top: 10px; padding-left: 10px; padding-right: 10px; padding-bottom: 10px; z-index: 1000'>
<a href="credits.php"><img src="img/logo_FBK.gif" height=30 title="FBK" valign=bottom border=0> 
&nbsp;<img valign=bottom src="img/hlt-logo.png" align=top height=40 title="HLT - Human Language Technology" border=0><br>
<img src="img/matecat-logo-small.png" height=23 title="MateCat project" valign=bottom border=0></a>

</div>
</body>
</html>