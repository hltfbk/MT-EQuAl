<form action="admin.php?section=stats" method=GET>
<input type=hidden name=section value="stats" />
Choose a task: <select onChange="submit()" name='id'><option value=''>
<?php
if (isset($mysession) && $mysession["status"] == "admin") { 
	$tasks = getTasks(null);
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
	$hash_report = getAnnotationReport($id);
	$systems = array();
	$statsTable = "<div style='margin:5px; display: inline-block; padding: 4px; border: 1px solid #000'><table cellspacing=1	 cellpadding=0 border=0><tr bgcolor=#ccc><td align=center>Annotation type</td><td colspan=4 align=center>Systems</td></tr><tr><td></td><td bgcolor='#000'><img width=1></td>";
	while (list ($eval,$counters) = each($hash_report)) {
		$values = split(",", $counters);
		foreach ($values as $val) {
			$items = split(" ", $val);
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
	$taskinfo = getTaskInfo($id);
	$ranges = rangesJson2Array($taskinfo["ranges"]);
	while (list ($evalid, $attrs) = each($ranges)) {
		$statsTable .= "<tr align=right><th bgcolor='".$attrs[1]."'>".$attrs[0]."&nbsp;&nbsp;</th><td bgcolor='#000'><img width=1></td>\n";
		reset($systems);
		while (list ($system,$tot) = each($systems)) {
			$statsTable .= "<td>";
			if (isset($anntot[$evalid."-".$system])) {
				$counters = $hash_report[$evalid];
				$values = split(",", $counters);
				$annotationdetail = "";
				foreach ($values as $val) {
					$items = split(" ", $val);
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
	print "<strong>&nbsp;&nbsp;This task has been annotated by <b>".count($annotators) ."</b> users</strong></br>".$statsTable;
	
	/*while (list ($eval,$counters) = each($hash_report)) {
		$system ="";
		$values = split(",", $counters);
		print "<div style='margin:5px; display: inline-block; padding: 4px; border: 1px solid #000'><table cellspacing=0 cellpadding=0 border=0>";
		print "<tr><td colspan=3 align=center>$eval</td></tr>";
		foreach ($values as $val) {
			$items = split(" ", $val);
			if ($items[0] != $system) {
				if ($system != "") {
					print "<tr><td></td><td colspan=4 height=1 bgcolor='#000'><img width=1></td></tr>";
					print "<tr><th>&nbsp;".$system."&nbsp;</th><th bgcolor='#000'><img width=1></th><th align=right>&nbsp;".$anntot[$eval."-".$system]. "&nbsp;</th><th bgcolor='#000'><img width=1></th><th align=right><small>TOTALE</small></th></tr>";
				}
				print "<tr><td colspan=5 height=1 bgcolor='#000'><img width=1></td></tr>";
				$system = $items[0];
			} 
			print "<tr><td></td><td bgcolor='#000'><img width=1></td><td align=right>".$items[2]."&nbsp;</td><td bgcolor='#000'><img width=1></td><td width=52% align=right nowrap>&nbsp; <small>user ".$items[1]."</small></td></tr>";	
		}
		print "<tr><td></td><td colspan=4 height=1 bgcolor='#000'><img width=1></td></tr>";
		print "<tr><th>&nbsp;".$items[0]."&nbsp;</th><th bgcolor='#000'><img width=1></th><th align=right>&nbsp;".$anntot[$eval."-".$items[0]]. "&nbsp;</th><th bgcolor='#000'><img width=1></th><th align=right><small>TOTALE</small></th></tr>";
		print "</table></div>";
	}*/
	
		$sentid = "";
		$sourceid="";
		$userid="";
		$sentence="";
		$user_annotations=array();
		$sentence_records = getAgreementSentences($id);
		$count=0;
		$num=0;
		$max=20;
		if (!isset($from)) {
			$from=0;
		}
		print "<br><strong>Show sentence annotations: </strong>";
		if ($from > 0) {
			print "<button onclick=\"location.href='admin.php?section=stats&id=$id&from=".($from-$max)."'\">prev</button> |";
		}	
		print " <button onclick=\"location.href='admin.php?section=stats&id=$id&from=".($from+$max)."'\">next</button>";
		
		while ($row = mysql_fetch_array($sentence_records)) {
			if ($userid != "" && ($userid != $row["user_id"] || $sentid != $row["output_id"] || $sourceid != $row["linkto"])) {
				if (count($user_annotations) > 0) {
					$auser = "<tr heigth=2>";
					for ($i=0; $i < count($tokens); $i++) {
						if (isset($user_annotations["-1"])) {
							$auser .= "<td bgcolor=".$user_annotations["-1"]."></td>";
						} else if (isset($user_annotations[$i+1])) {
							$auser .= "<td bgcolor=".$user_annotations[$i+1]."></td>";
						} else {
							$auser .= "<td></td>";
						}
					}
					$sentence = $auser ."</tr>\n".$sentence;
				}
				$user_annotations=array();
			} 
				
			if ($sentid != "" && $sentid != $row["output_id"]) {
				if ($num>=$from) {
					print "<table cellspacing=0 cellpadding=2 border=1>".$sentence."</table><br>";
					$sentence="";
					$count++;
					if ($count > $max) {
						break;
					}
				}
				$sentence="";
				
			}
			if ($sourceid != $row["linkto"]) {
				$num++;
				if ($num>=$from) {
					print "<hr>";
				}
				$sentence = "<a href='".$taskinfo["type"].".php?id=".$row["linkto"]."&taskid=".$id ."'>".$row["linkto"]."</a><br>".$sentence;
				
				$sourceid = $row["linkto"];			
			}
			if ($sentid != $row["output_id"]) {
				$tokens = getTokens($row["lang"], $row["text"]);
			}	
			
			if (trim($row["evalids"]) == "") {
				$user_annotations["-1"]=$ranges[$row["eval"]][1];
			} else {
				$tokenids = split("[ |,]", trim($row["evalids"]));
				if (count($tokenids) > 0) {
					foreach ($tokenids as $tid) {
						$user_annotations[$tid]=$ranges[$row["eval"]][1];
					}
				}
			} 
				
			if ($sentid != $row["output_id"]) {
				$sentence .= "<tr bgcolor=#fff><td>".join("</td><td>", $tokens)."</td></tr>\n";
				$sentid = $row["output_id"];
				$userid = "";
			}
			$userid = $row["user_id"];
			
		}
		//end while
		if ($sentence != "") {
			$auser = "<tr heigth=2>";
			for ($i=0; $i < count($tokens); $i++) {
				if (isset($user_annotations["-1"])) {
					$auser .= "<td bgcolor=".$user_annotations["-1"]."></td>";
				} else if (isset($user_annotations[$i+1])) {
					$auser .= "<td bgcolor=".$user_annotations[$i+1]."></td>";
				} else {
					$auser .= "<td></td>";
				}
			}
			$sentence = $auser ."</tr>\n".$sentence;
				
			if ($num>=$from) {
				print "<table cellspacing=0 cellpadding=2 border=1>".$sentence."</table><br>";
			}
		}		
}
?>
