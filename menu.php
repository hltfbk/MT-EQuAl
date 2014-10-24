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
ul.dropdown                         { display: inline; position: relative; white-space: nowrap;}
ul.dropdown li                      { font-weight: bold; float: left; width: auto; position: relative;}
ul.dropdown a:hover		            { color: #fff; }
ul.dropdown li a                    { display: block; color: #222; position: relative; }

/* 
	LEVEL TWO
*/
ul.dropdown ul 						{ display: none; position: absolute; left: 0; width:100%;  }
ul.dropdown ul li 					{ font-weight: normal; font-size: 14px; width: 100%;  color: #000;}
ul.dropdown ul li a:hover			{ display: block; color: #B0D730; background: #fff none repeat scroll 0 0; !important; } 
ul.dropdown ul li a					{ display: block; padding: 10px;  border-bottom: dotted 1px #606060; !important; } 
</style>
<div style="position: fixed; z-index: 100; width: 100%">
<ul id="menu">
     <?php
        if (!isset($mysession) || empty($mysession["status"])) {
     ?>
		<form style="display:inline; float: left; margin: 0px; color: #fff; margin: 10px 30px 10px 20px" method="post" name="form" action="index.php">
<input type="hidden" name="sent" value="login">
&nbsp;&nbsp;User: <input type="text" name="login" maxlength=30 size=10 value="<?php if (!empty($login)) {echo $login;} ?>">
&nbsp;&nbsp;Password: <input type="password" name="password" maxlength=30 size=10>
&nbsp;&nbsp;<input type=submit value="Login" name="auth">
</form>
	 <?php
		} else {
			$tasks = getTasks($mysession["userid"]);
    		if (count($tasks) == 1) { 
    			while (list ($tid, $val) = each($tasks)) {
    				if ($mysession["taskid"] != $tid) {
    					$taskinfo = getTaskInfo($tid);
						$mysession["taskid"] = $tid;
						$mysession["tasknow"] = $taskinfo["name"];
						$mysession["tasksysnum"] = countTaskSystem($tid);
						$mysession["tasktype"] = $taskinfo["type"];
						$mysession["taskistr"] = $taskinfo["instructions"];
						$mysession["taskranges"] = rangesJson2Array($taskinfo["ranges"]); 
					}  					
    			}
    		}
    		
			if ($mysession["status"] != "root") {
			  if (isset($mysession["taskid"])) {
				#print "<div style='display:inline; float :left; color: #fff; margin: 10px 30px 10px 10px'>".str_replace("_"," ",$mysession["tasknow"]) ."</div>";
				print "<li><a href='index.php?taskid=". $mysession["taskid"] ."'>".str_replace("_"," ",$mysession["tasknow"]) ."</a></li>";
			  }
			  $tasks = getTasks($mysession["userid"]);
			  if (count($tasks) > 0) {
		?>
    	
    		<li><a href="#" class="drop">Tasks</a><!-- Begin Tasks Item -->
			<div class="dropdown_1column align_left">
            <div style="overflow-y: auto; overflow-x: hidden; width: auto; height: 300px">
			<ul class="simple">
                <!-- <ul style="margin-left=120px; z-index:999;" id="task"> -->
        <?php
            $tasktype="";
           	while (list ($tid, $val) = each($tasks)) {
    			if ($tasktype != $val[1]) {
    				print "<hr><li>".$taskTypes[$val[1]]."</li><hr>";
    				$tasktype = $val[1];
    			}
				print "<li><a href='index.php?taskid=".$tid."'><b>".str_replace("_"," ",ucfirst($val[0]))."</b></a></li>";
            }                  		        	                	
        ?>                    
        	</ul> 
    		</div></div>
    		</li><!-- End Tasks Item -->
		<?php
    	  }
    	} 
    			
        #activate admin menu
        if ($mysession["status"] == "root" || $mysession["status"] == "admin" || $mysession["status"] == "advisor") {
        	print "<li><a href='admin.php' class='button'>Admin</a></li>";  	
        }
    }
	?>
    <li><a href="#" class="drop">Help</a>
    <div class="dropdown_2columns align_left">
    	<div style="">
        <ul class="simple dropdown">
        	<li><a href="docs/MT-Equal_annotation_instructions.pdf" target="mtequal_docs">Annotation Instructions</a></li>
            <li><a href="docs/MT-Equal_project_management_instructions.pdf" target="mtequal_docs">Project Management Instructions</a></li>                   
            <li><a href="credits.php">Credits</a></li>
    	</ul>
    	</div>
    </div>
    </li>
    
    <?php
    	if (!empty($mysession["status"])) {
        	print "<li class='drop'><a href='index.php?logout=yes'>Logout: <b>" .$mysession["username"] ."</b></a></li>";
        } 	
	?>
	</div>
</ul>
</div>