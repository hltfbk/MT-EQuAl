 <div id="top" style="margin-left:70px; padding-bottom: 25px; width: 80%;font-size: 13px">
   	<img style="float: left;" src="img/menu/menu_left.png">
        <ul id="menu">     	
            <li><a href="index.php#<?php print $id; ?>">« &nbsp;<i><?php echo str_replace("_"," ",getTaskName($mysession["taskid"])); ?></i></a></li>
            <li><div style="width:3	0;">&nbsp;</div></li>	        	
        </ul>
        <img style="float:left;" alt="" src="img/menu/menu_right.png"/>
        <div style="display: inline-block; border-bottom: 1px solid #ddd; position: relative; width: 150px; top: 15px; padding-right: 20px; font-size: 14px">&nbsp;<i><?php if (isset($sentidx) && $sentidx != -1) {echo "sentence n. $sentidx";} ?></i></div>
</div>    
   