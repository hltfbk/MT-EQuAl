<?php
header("Content-type: text/html; charset=utf-8");
include("config.php");
include("functions.php");

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
}

$ranges = $mysession["taskranges"];
$errors = getErrors($id,$targetid,$userid);
$hash_target = getSystemSentences($id,$taskid);
$i=0;
while (list ($sentence_id, $sentence_item) = each($hash_target)) {
	if ($sentence_id==$targetid) {
		break;
	}
	$i++;
}
				
$checkid = 0;
while (list ($val,$attrs) = each($ranges)) {
	if ($val <= 1) {
		if (count($errors) == 0 || isset($errors[0]) || isset($errors[1])) {
			$color="#".$attrs[1];
			$bordercolor="4px solid ".$color;
			if (isset($errors[$val])) {
				$bordercolor="4px solid red";
			}
			if ($val == 0) {
				print "<table cellspacing=4>";
			} 
			print "<td style='padding: 1px; background: $color; border: $bordercolor; box-shadow: 2px 2px 2px #888; font-size:13px' id='check.$i.$checkid' align=center onmouseover='fadeIn(this);'  onmouseout='fadeOut(this,\"".$attrs[1]."\");' onClick=\"check('$id','$targetid',$userid,$val,$checkid,".count($ranges).",$i,".count($hash_target).");\" nowrap>".$attrs[0] ."</td></tr>";
			if ($val == 1) {
				print "</table>";
			}
			$checkid++;
		} 
	} 
}
				
while (list ($errID, $errARRAY) = each($errors)) {
	if ($errID > 1) {
	  	$tokenids = explode(",",$errARRAY[0]);
		$texts = explode("__BR__",$errARRAY[1]);
		$annotations = "";
		for($r=0; $r<count($tokenids); $r++) {
			$delicon = "";
			if (count($ranges) > 1) {
				if ($monitoring==0) {	
					$delicon = "<a href=\"javascript:removeAnnotation($id,$targetid,'".$tokenids[$r]."',$errID);\"><img src='img/delete.png' width=12></a>";
				}
			}
			$annotations .= "- $delicon<div style='display: inline; font-size:17px' onmouseover=\"javascript:showRange(this,$targetid,'".$tokenids[$r]."');\" onmouseout=\"javascript:hideRange(this,$targetid,'".$tokenids[$r]."');\">&nbsp;";
			if (trim($texts[$r]) == "") {
				$annotations .= "<small>_SPACE_</small>";
			} else {
				$annotations .= $texts[$r];
			}
			$annotations .="</div><br>";
		}
		print "<div style='background: #".$ranges[$errID][1]."; white-space: nowrap;'> ";
		if ($monitoring==0) {	
			print "<button id=reset.$i.$errID onclick=\"javascript:reset('$id','$targetid',$taskid,$userid,$errID,".count($ranges).",$i,".count($hash_target).");\">reset</button>";
		}
		print "&nbsp;<i><small><b>".$ranges[$errID][0].":</b></small></i></div>$annotations";
	}
}	  
?>
