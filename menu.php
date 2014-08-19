<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script>
var maxHeight = 400;

$(function(){
	$(".dropdown > li").hover(function() {
		 var $container = $(this),
             $list = $container.find("ul"),
             $anchor = $container.find("a"),
             height = $list.height() * 1.1,       // make sure there is enough room at the bottom
             multiplier = height / maxHeight;     // needs to move faster if list is taller
        
        // need to save height here so it can revert on mouseout            
        $container.data("origHeight", $container.height());
        
        // so it can retain it's rollover color all the while the dropdown is open
        $anchor.addClass("hover");
        
        // make sure dropdown appears directly below parent list item    
        $list
            .show()
            .css({
                paddingTop: $container.data("origHeight")
            });
        
        // don't do any animation if list shorter than max
        if (multiplier > 1) {
            $container
                .css({
                    height: maxHeight,
                    overflow: "hidden"
                })
                .mousemove(function(e) {
                    var offset = $container.offset();
                    var relativeY = ((e.pageY - offset.top) * multiplier) - ($container.data("origHeight") * multiplier);
                    if (relativeY > $container.data("origHeight")) {
                        $list.css("top", -relativeY + $container.data("origHeight"));
                    };
                });
        }
        
    }, function() {
    
        var $el = $(this);
        
        // put things back to normal
        $el
            .height($(this).data("origHeight"))
            .find("ul")
            .css({ top: 0 })
            .hide()
            .end()
            .find("a")
            .removeClass("hover");
    
    });
    
});
</script>

<style type="text/css" style="display: none !important;">
/* 
	LEVEL ONE
*/
ul.dropdown                         { display: inline; position: relative;}
ul.dropdown li                      { font-weight: bold; float: left; width: auto; position: relative; }
ul.dropdown a:hover		            { color: #fff; }
ul.dropdown li a                    { display: block; color: #222; position: relative; z-index: 2000; }

/* 
	LEVEL TWO
*/
ul.dropdown ul 						{ display: none; position: absolute; left: 0; width:100%; z-index: 1000; }
ul.dropdown ul li 					{ font-weight: normal; font-size: 14px; width: 200px;  color: #000;}
ul.dropdown ul li a:hover			{ display: block; color: #B0D730; background: #fff none repeat scroll 0 0; !important; } 
ul.dropdown ul li a					{ display: block; padding: 10px;  border-bottom: dotted 1px #606060; !important; } 

</style>

<body>
<div style="margin-left:50px; width: 100%; position: fixed">
    	 	
        <ul id="menu" class="dropdown">
         <img style="float:left;" alt="" src="img/menu/menu_left.png"/>
         
          <?php
            if (!isset($mysession) || empty($mysession["status"])) {
          ?>
            <li><form style="margin: 0px" method="post" name="form" action="index.php">
<input type="hidden" name="sent" value="login">
&nbsp;&nbsp;User: <input type="text" name="login" maxlength=30 size=10 value="">
&nbsp;&nbsp;Password: <input type="password" name="password" maxlength=30 size=10>
&nbsp;&nbsp;<input type=submit value="Login" name="auth">
</form></li>
		  <?php
			} else {
				if (isset($mysession["taskid"])) {
					print "<li><a href='index.php?taskid=". $mysession["taskid"] ."'>".str_replace("_"," ",$mysession["tasknow"]) ."</a></li>";
				}
		   ?>
                     	
			<li style="width: 200px">Tasks
                <ul style="margin-top: 0; z-index: 1001" >
                <!-- <ul style="margin-left=120px; z-index:999;" id="task"> -->
                    <?php
                   	$tasks = getTasks($mysession["username"]);
    				while (list ($tid,$val) = each($tasks)) {
					#foreach ($tasks as $task) {
                   		print "<li><a href='index.php?taskid=".$tid."'>".str_replace("_"," ",ucfirst($val[0]))."</a></li>";
                   	}                  		        	                	
                    ?>
                    
                </ul>          		
            </li>
            
    <?php
            	#if you are an admin
            	if ($mysession["status"] == "admin" || $mysession["status"] == "advisor") {
            		print "<li><a href='admin.php'>Admin</a></li>";  	
            	}

            }
	?>
            <li style="width: 100px"><a href="#">Help</a>
                <ul style="margin-top: -10;">
                    <li style="width: 90px"><a href="credits.php">Credits</a></li>
                </ul>
            </li>
            <?php
            	if (!empty($mysession["status"])) {
           			print "<li><a href='index.php?logout=yes'>Logout: <b>" .$mysession["username"] ."</b></a></li>";
           		} 	
           	?> 
           	
        <img style="float:left;" alt="" src="img/menu/menu_right.png"/>
        </ul>
    </div>
    <div style="margin-left: 60px">
    <div class=index><center>
    <?php
    
    if (empty($mysession["status"])) {
            print "<h3>This is an end user interface for the evaluation of Machine Translation systems</h3>\n<p>Sign in, please!</p>";
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