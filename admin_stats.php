<form action="admin.php?section=stats" method=GET>
<input type=hidden name=section value="stats" />
Choose a task: <select onChange="submit()" name='id'><option value=''>
<?php
if (isset($mysession)) { 
	if ($mysession["status"] == "admin") {
		$tasks = getTasks(null);
	} else if ($mysession["status"] == "advisor") {
		$tasks = getTasks($mysession["userid"]);
	}
	$ttype = "";
	while (list ($tid,$tarr) = each($tasks)) {
		if ($tarr[1] != $ttype) {
			$ttype = $tarr[1];
			print "<option value='' disabled='disabled'>--- ".ucfirst($ttype) ." tasks --- \n";
		}
		print "<option value='$tid'";
		if (isset($id) && $id == $tid) {
			print " selected";
		} 
		print "> &nbsp;".$tarr[0]."\n";
	}	
}
?>
</select>
</form>

<?php
$anntot=array();
$annid="";
$annotators=array();
$statsTable = "";
if (isset($id)) {
	$taskinfo = getTaskInfo($id);
	
	if (count($taskinfo) > 0) {
	
	$hash_report = getAnnotationReport($id);
	$systems = array();
	$statsTable = "<div style='margin:5px; display: inline-block; padding: 4px; border: 1px solid #000'><table cellspacing=1	 cellpadding=0 border=0><tr bgcolor=#ccc><td align=center>Annotation type</td><td colspan=4 align=center>Systems</td></tr><tr><td></td><td bgcolor='#000'><img width=1></td>";
	while (list ($eval,$counters) = each($hash_report)) {
		$values = explode(",", $counters);
		foreach ($values as $val) {
			$items = explode(" ", $val);
			if (!in_array($items[1],$annotators)) {
				array_push($annotators, $items[1]);
			}
			if (!isset($systems[$items[0]])) {
				$systems[$items[0]]=0;
				$statsTable .= "<th>&nbsp;&nbsp;".$items[0]."</th>";
			}

			if (isset($anntot[$eval."-".$items[0]])) {
				$anntot[$eval."-".$items[0]] += $items[2];
			} else {
				$anntot[$eval."-".$items[0]] = $items[2];
			}
			$systems[$items[0]] += $items[2];
		}					
	}
	$statsTable .= "</tr>\n";
	$statsTable .= "<tr><td></td><td colspan=".(count($systems)+1)." height=1 bgcolor='#000'><img width=1></td></tr>";
					
	reset($hash_report);
	
	$ranges = rangesJson2Array($taskinfo["ranges"]);
	while (list ($evalid, $attrs) = each($ranges)) {
		$statsTable .= "<tr align=right><th bgcolor='".$attrs[1]."'>".$attrs[0]."&nbsp;&nbsp;</th><td bgcolor='#000'><img width=1></td>\n";
		reset($systems);
		while (list ($system,$tot) = each($systems)) {
			$statsTable .= "<td>";
			if (isset($anntot[$evalid."-".$system])) {
				$counters = $hash_report[$evalid];
				$values = explode(",", $counters);
				$annotationdetail = "";
				foreach ($values as $val) {
					$items = explode(" ", $val);
					if ($items[0] == "$system") {
						$annotationdetail .= "user ".$items[1].": ".$items[2]."\n";
					}
				}
				$statsTable .= "&nbsp;&nbsp;".$anntot[$evalid."-".$system]."<a href='#' title='".$annotationdetail."'>[#]</a></td>";
			} else {
				$statsTable .= "-&nbsp;</td>";
			}
		}
		$statsTable .= "</tr>";
	}
	$statsTable .= "<tr><td></td><td colspan=".(count($systems)+1)." height=1 bgcolor='#000'><img width=1></td></tr>";
	$statsTable .= "<tr align=right><td><td width=1 bgcolor='#000'><img width=1></td></td>";
	reset($systems);
	while (list ($system,$tot) = each($systems)) {
		$statsTable .= "<th>$tot</th>";
	}
	
	$statsTable .= "</tr></table>";	
	print "<strong>&nbsp;&nbsp;This task has been annotated by <b>".count($annotators) ."</b> users</strong></br>";
	
	if (count($annotators) > 0) {	
		$sentid = "";
		$sourceid="";
		$userid="";
		$sentence="";
		$user_annotations=array();
		$sentence_records = getAgreementSentences($id);
		$outputNum = 0;
		$count=0;
		$num=0;
		$max=20;
		if (!isset($from)) {
			$from=0;
		}
		
		$outputText = "";
		$hashusers = getUserStats(null,"admin");
		while ($row = mysql_fetch_array($sentence_records)) {
			if ($userid != "" && ($userid != $row["user_id"] || $sentid != $row["output_id"] || $sourceid != $row["linkto"])) {
				if (count($user_annotations) > 0 && isset($hashusers{$userid})) {
					$auser = "";
					#Hjerson
					if ($userid == 47) {
                    	$auser .= "\n<tr><td style='font-size: 7px; background: #000; color:#fff' colspan=".count($tokens).">&#x2798; H j e r s o n</td></tr>";
                    # da 1 a 20 (#48 to #139)
                    } else if ($userid == 48) {
                    	$auser .= "\n<tr><td style='font-size: 7px; background: #000; color:#fff' colspan=".count($tokens).">&#x2798; U s e r s  &nbsp; f r o m &nbsp; 1 &nbsp; t o &nbsp; 20 &nbsp; (a n n o t a t i o n &nbsp; f r o m &nbsp; s c r a t c h)</td></tr>";
                    
                    # da 21 a 40 (#140 to 159)
                    } else if ($userid == 140) {
                    	$auser .= "\n<tr><td style='font-size: 7px; background: #000; color:#fff' colspan=".count($tokens).">&#x2798; U s e r s  &nbsp; f r o m &nbsp; 21 &nbsp; t o &nbsp; 40 &nbsp; (H j e r s o n &nbsp; r e v i s i o n)</td></tr>";
                    
                    # da 41 a 60 (#160 to 179)
                    } else if ($userid == 160) {
                    	$auser .= "\n<tr><td style='font-size: 7px; background: #000; color:#fff' colspan=".count($tokens).">&#x2798; U s e r s  &nbsp; f r o m &nbsp; 41 &nbsp; t o &nbsp; 60 &nbsp; (h u m a n &nbsp; a n n o t a t i o n &nbsp; r e v i s i o n)</td></tr>";
                    } 
                    
                    $auser .= "<tr heigth=2 title='User ".$hashusers{$userid}[0]."'>";
					for ($i=0; $i < count($tokens); $i++) {
						if (isset($user_annotations["-1"])) {
							$auser .= "<td><table width=100% border=0 cellspacing=0 cellpadding=2><td bgcolor=".$user_annotations["-1"]."></td></table>";
						} else if (isset($user_annotations[$i+1])) {
							#$auser .= "<td bgcolor=".$user_annotations[$i+1]."></td>";
							$auser .= "<td><table width=100% border=0 cellspacing=0 cellpadding=2>";
							$cols = explode(" ", trim($user_annotations[$i+1]));
							foreach ($cols as $col) {
								$auser .= "<td bgcolor=".$col."></td>";
							}
							$auser .= "</table></td>";
						} else {
							$auser .= "<td></td>";
						}
					}
					$auser .= "</tr>";
					$sentence .= "\n".$auser;
				}
				$user_annotations=array();
			} 
				
			if ($sentid != "" && $sentid != $row["output_id"]) {
				if ($num>=$from) {
					$outputText .= "OUTPUT $outputNum:<br><table cellspacing=0 cellpadding=0 border=1>".$sentence."</table><br>\n";
				}
				$sentence="";				
			}
			
			if ($sourceid != $row["linkto"]) {
				$outputNum = 0;
				$count++;
				if ($count > $max) {
					break;
				}
				$num++;
				if ($num>=$from) {
					$outputText .= "<hr><a href='".$taskinfo["type"].".php?id=".$row["linkto"]."&taskid=".$id ."'><i>sentence n.$num</i></a><br>";					
				}
				$sourceid = $row["linkto"];			
			}
			if ($sentid != $row["output_id"]) {
				$outputNum++;
				$tokens = getTokens($row["lang"], $row["text"], $row["tokenization"]);
			}	
			
			if (trim($row["evalids"]) == "") {
				$user_annotations["-1"]=$ranges[$row["eval"]][1];
			} else {
				$tokenids = preg_split("[ |,]", trim($row["evalids"]));
				if (count($tokenids) > 0) {
					foreach ($tokenids as $tid) {
						if (strpos($tid,'-') !== false) {
							$tid = preg_replace('/-.*$/',"",$tid); 
						}
						
						#print  $sentid  ."# " .$tid ." ## ". $ranges[$row["eval"]][0] ."<br>";	
						if (!isset($user_annotations[$tid])) {
							$user_annotations[$tid]=$ranges[$row["eval"]][1];
						} else {					
							$user_annotations[$tid].=" ".$ranges[$row["eval"]][1];
						}
					}
				}
			} 
				
			if ($sentid != $row["output_id"]) {
				$sentence .= "<tr bgcolor=#fff><td style='padding: 1px'>".join("</td><td style='padding: 2px'>", $tokens)."</td></tr>\n";
				$sentid = $row["output_id"];
				$userid = "";
			}
			$userid = $row["user_id"];
			
		}
		//end while
		
		if (count($user_annotations) > 0 && isset($hashusers{$userid})) {
					$auser = "";
					#Hjerson
					if ($userid == 47) {
                    	$auser .= "\n<tr><td style='font-size: 7px; background: #000; color:#fff' colspan=".count($tokens).">&#x2798; H j e r s o n</td></tr>";
                    # da 1 a 20 (#48 to #139)
                    } else if ($userid == 48) {
                    	$auser .= "\n<tr><td style='font-size: 7px; background: #000; color:#fff' colspan=".count($tokens).">&#x2798; U s e r s  &nbsp; f r o m &nbsp; 1 &nbsp; t o &nbsp; 20 &nbsp; (a n n o t a t i o n &nbsp; f r o m &nbsp; s c r a t c h)</td></tr>";
                    
                    # da 21 a 40 (#140 to 159)
                    } else if ($userid == 140) {
                    	$auser .= "\n<tr><td style='font-size: 7px; background: #000; color:#fff' colspan=".count($tokens).">&#x2798; U s e r s  &nbsp; f r o m &nbsp; 21 &nbsp; t o &nbsp; 40 &nbsp; (H j e r s o n &nbsp; r e v i s i o n)</td></tr>";
                    
                    # da 41 a 60 (#160 to 179)
                    } else if ($userid == 160) {
                    	$auser .= "\n<tr><td style='font-size: 7px; background: #000; color:#fff' colspan=".count($tokens).">&#x2798; U s e r s  &nbsp; f r o m &nbsp; 41 &nbsp; t o &nbsp; 60 &nbsp; (h u m a n &nbsp; a n n o t a t i o n &nbsp; r e v i s i o n)</td></tr>";
                    } 
                    
                    $auser .= "<tr heigth=2 title='User ".$hashusers{$userid}[0]."'>";
					for ($i=0; $i < count($tokens); $i++) {
						if (isset($user_annotations["-1"])) {
							$auser .= "<td><table width=100% border=0 cellspacing=0 cellpadding=2><td bgcolor=".$user_annotations["-1"]."></td></table>";
						} else if (isset($user_annotations[$i+1])) {
							#$auser .= "<td bgcolor=".$user_annotations[$i+1]."></td>";
							$auser .= "<td><table width=100% border=0 cellspacing=0 cellpadding=2>";
							$cols = explode(" ", trim($user_annotations[$i+1]));
							foreach ($cols as $col) {
								$auser .= "<td bgcolor=".$col."></td>";
							}
							$auser .= "</table></td>";
						} else {
							$auser .= "<td></td>";
						}
					}
					$auser .= "</tr>";
					                       
					$sentence .= "\n".$auser;

			if ($num>=$from) {
				$outputText .= "OUTPUT $outputNum:<br><table cellspacing=0 cellpadding=0 border=1>".$sentence."</table><br>\n";
			}
		}	
		
		
		if ($outputText != "") {
			print "$statsTable<br><strong>Annotated sentences: </strong>";
			if ($from > 0) {
				print "<button onclick=\"location.href='admin.php?section=stats&id=$id&from=".($from-$max)."'\">prev</button> |";
			}	
			if ($count > $max+1) {
				print " <button onclick=\"location.href='admin.php?section=stats&id=$id&from=".($from+$max+1)."'\">next</button>";
			}
			print $outputText;
		} 
	  }
	} else {
		print "WARNING! This task is not valid.";
	}	
}
?>
