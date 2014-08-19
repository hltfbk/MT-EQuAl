<html>
<head>
<link href="css/mtequal.css" rel="styleSheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/mtequal.js"></script>
	
<?php
header("Content-type: text/html; charset=utf-8");
include("config.php");
include("functions.php");

#activate the Javascript functions

	if (!isset($userid)) {
		$userid = $mysession['userid'];
	} 
	
	if (!isset($taskid)) {
 		$taskid = $mysession["taskid"];
 	}

if (isset($monitoring) && $monitoring == 1) {
	$sentidx=-1;
} else {
	$monitoring=0;
 	if (!isset($sentidx)) {
 		$sentidx=-1;
 	}
}
?>
</head>

<body>
<div id="errortypes" onclick="this.style.visibility='hidden';" style="font-size: 10px"></div>
<?php
$prevAndnextIDs = getPrevNext($taskid, $id);	
print "<div class=donebottom>";
$prevpage = "quality.php?id=".$prevAndnextIDs[0]."&taskid=$taskid&sentidx=".($sentidx-1);
$nextpage = "quality.php?id=".$prevAndnextIDs[1]."&taskid=$taskid&sentidx=".($sentidx+1);
print "<button id=prev name=prev onclick=\"javascript:next('$prevpage');\">&nbsp;« prev&nbsp;</button> &nbsp;";
print "<button style='width: 170' id=done name=done onclick=\"javascript:doneAndIndex('$id','$userid',this);\" disabled></button> &nbsp;";
print "<button id=next name=next onclick=\"javascript:next('$nextpage');\">&nbsp;next »&nbsp;</button>";
		
print "</div>";
if (empty($mysession["status"])) {
	print "<script>window.open('index.php','_self');</script>";
}

if ($taskid > 0 && isset($id) && isset($userid)) {
	
	$hash_target = getSystemSentences($id,$taskid);
	$i = 0;
	$checked = 0;
	print "<div style='width: 100%; position: relative; height: 100%; margin-top:0px; margin-left: -28px;  padding-right: 46px; margin-bottom: auto; overflow-y: auto;'>";
	if (count($hash_target) > 0) {
	while (list ($sentence_id, $sentence_item) = each($hash_target)) {
		$errors = getQuality($id,$sentence_id,$userid);
		print "<table cellpadding=0 cellspacing=2 border=0> <td valign=top>";
		print "<div class='cell'>";
		//print "<div style='display: table-cell; float: left; width: 666px'>";
		
		//Add output row
		$sent = showSentence ($sentence_item[0], $sentence_item[1], "output", "no",$sentence_id);
		//ripristino eventuali errori nei carattri con lastring vuota se non sono stati fatte delle anotazioni
		#if(count($errors) == 0) {
		#	$sent = preg_replace("/<img src='img\/check_error.png' width=16>/","",$sent);
		#}
		print "<div class=row><div class=label>OUTPUT <b>".($i+1)."</b>: </div>$sent</div>";
		//end output row
		
		//Add comment row
		/*print "<div class=row><div class=cell>";
		$comment = getComment($sentence_id,$userid);
		
		if ($monitoring==0) {		
		?>
		<!-- comment -->
		<a href='#comm<?php echo $sentence_id; ?>' class='nav-toggle'><img src='img/addcomment.png' style='vertical-align: top; float: right;' width=80></a>&nbsp;</div>
		<div class=cell>
			<div id="comm<?php echo $sentence_id; ?>" style="display:none; font-size: 12px;">
				<textarea id=comm<?php echo $sentence_id; ?>_text rows=2 cols=75 style='background: lightyellow'><?php echo $comment; ?></textarea>
			</div>
			<div style="display: inline-block; top: 10px;" id=comm<?php echo $sentence_id; ?>_label><?php echo $comment; ?></div>		
		
		<?php
		} else {
			if ($comment !="") {
				print "<small><i>Comment:</i></small> </div>$comment<div class=cell>";
			}
		}
		<!-- fine comment -->
		print "</div>";
		//end comment row	
		*/
			
		print "</div>";
		//end cell (output+comment)
		
		//start error cell 
		print "<td valign=top>";
		print "<div class=cell><table cellspacing=4>";
		$ranges = $mysession["taskranges"];
		$checkid = 0;
		while (list ($val,$attrs) = each($ranges)) {
			$color=$attrs[1];
			$bordercolor="000";
			if ($errors == $val) {
				$color="F00";
				$bordercolor=$attrs[1];
				$checked++;
			}
			print "<td width=18 style='background: #$color; border: 1px solid #$bordercolor' id='check.$i.$checkid' align=center onmouseover='fadeIn(this);' onmouseout='fadeOut(this,\"".$attrs[1]."\");' onClick=\"check('$id','$sentence_id',$userid,$val,$checkid,".count($ranges).",$i,".count($hash_target).");\">&nbsp;$val&nbsp;</td>";
			$checkid++;
		}
		print "</table></div>";
		print "</td>";
		//end error cell
		
		print "</table><div style='display: inline-block; border-top: dashed #666 1px; width: 100%'>&nbsp;</div>";
		$i++;
	
	}
	#print "</div>";
	
	#print count($hash_target) ."!= $checked || ".isDone($id,$userid);
	if (isDone($id,$userid) > 0) {
		print "<script>alreadyDone();</script>";
	} else {
		if ($checked != count($hash_target)) {
	 		print "<script>notDoneYet();</script>";
		} else {
			print "<script>activateDone(".$monitoring.");</script>";
		}
	} 
	} else {
		print "<h3><font color=red>No output found!</font></h3>";
	}
} 
?>
<div class=log id=log></div>       

<script>
$(document).ready(function() {
  	$('.nav-toggle').click(function() {
		//get collapse content selector
		var collapse_content_selector = $(this).attr('href');					
		//make the collapse content to be shown or hide
		var toggle_switch = $(this);
		$(collapse_content_selector).toggle(function(){
			if ($(this).css('display')=='none'){
				//change the button label to be 'Show'
				if (this.id.indexOf("comm") == 0) {
					toggle_switch.html("<img src='img/addcomment.png' style='vertical-align: top; float: right;' width=80>");
					
					el = document.getElementById(this.id+"_text");
					if (el != null) {
						save_comment(this.id,el.value);
						$("#"+this.id+"_label").html(el.value);
						elComment = document.getElementById(this.id+"_label");
						elComment.style.visibility = "visible";
						//activateDone();
					} else {
						alert("Error while saving the comment! Please contact the administrator. (code: 1001)");
					}	
				} else {
					toggle_switch.html('read more');
				}
				
			}else{
				//change the button label to be 'Hide'
				if (this.id.indexOf("comm") == 0) {
					$("#"+this.id+"_text").focus();
					elabel = this.id.replace(/_label/,"");
					elComment = document.getElementById(elabel+"_label");
					//alert(el.id);
					if (elComment != null) {
						elComment.style.visibility = "hidden";
					}
					toggle_switch.html("<img src='img/savecomment.png' style='vertical-align: top; float: right;' width=40>");
				} else {
					toggle_switch.html('close');
				}
			}
		});
	});
});	

</script>
<br></br>

</div>
</body>
</html>
