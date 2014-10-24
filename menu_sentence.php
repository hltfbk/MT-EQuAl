 <div id="top" style="padding-bottom: 25px; padding-left: 19px; width: 80%; font-size: 13px">
   	    <ul id="menu">
		    <li><a href="index.php#<?php print $id; ?>"><?php echo str_replace("_"," ",getTaskName($mysession["taskid"])); ?> index</a></li>
        </ul>
</div>    
        <div style="display: inline; border-bottom: 1px solid #ddd; border-left: 1px solid #ddd; border-right: 1px solid #ddd; position: absolute; right: 200px; width: 170px; top: 0px; padding-bottom: 4px;padding-top: 3px; font-size: 14px">&nbsp;<i>
        <?php 
        	if (isset($sentidx) && $sentidx != -1) {
        		echo "annotation n.$sentidx";
        	} else {
        		echo "&nbsp;&nbsp;<a href='javascript:history.back()'>Go back</a>";
        	}
        ?>
        </i></div>
        <br>
