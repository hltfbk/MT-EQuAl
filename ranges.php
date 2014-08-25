<table><td>
<TABLE id="rangesTable" style="font-size:13px" cellpadding=2 cellspacing=0 border="0">
<TR><TH>value<br><small>(integer)</small></TH><TH>label</TH><TH align=left>color</TH></TR>
<?php
include("config.php");
include("functions.php");
$idx=0;
$array_values = array();
if (isset($ranges)) {
	$alreadyUsedValues =array();
	if (isset($taskid)) {
		$alreadyUsedValues = getUsedValues($taskid);	
	}
	$dec = json_decode(stripslashes($ranges));

	for($idx=0;$idx<count($dec);$idx++){
    	$obj = (Array) $dec[$idx];
    	//echo $obj["name"]." ".$obj["id"]."<br>";
    	$warn = "";
    	$tdcolor = "";
    	if (trim($obj["val"]) == "") {
    		$tdcolor = " bgcolor='#FFE49C'";
    		$warn ="<img src=\"img/check_error.png\" title=\"WARNING! This is an empty value.\">";
	    } 
	    if (!preg_match("/^[0-9]+$/", $obj["val"])) {	
	    	$tdcolor = " bgcolor='#FFE49C'";
    		$warn ="<img src=\"img/check_error.png\" title=\"WARNING! The value must be an integer.\">";
    	} 
		if (in_array($obj["val"], $array_values)) {
    		$tdcolor = " bgcolor='#FFE49C'";
    		$warn ="<img src=\"img/check_error.png\" title=\"WARNING! A duplicate value found.\">";
    	} else {
    		array_push($array_values, $obj["val"]);
    	}	
    	
    	$disable_edit="";
    	if (empty($warn)) {
    		if (($obj["val"] == 0 || $obj["val"] == 1) && ($type == "errors" || $type == "wordaligner")) {
    			$disable_edit=" disabled='disabled'";
				$tdcolor = " bgcolor='#FFE8BA'";
				$warn = "<img src=\"img/lock_icon.png\" width=14 title=\"This value is mandatory for this task.\">";
    		} else if (in_array($obj["val"], $alreadyUsedValues)) {
				$disable_edit=" disabled='disabled'";
				$tdcolor = " bgcolor='#FFE8BA'";
				$warn = "<img src=\"img/lock_icon.png\" width=14 title=\"Consistency check! This value can't be changed because it is already used in the database.\">";
			}
		}
    	print "<TR>\n    <TD".$tdcolor."><INPUT type=\"text\" name=\"val[]\" value=\"".$obj["val"]."\" size=4 $disable_edit/>".$warn."</TD>\n";
        print "    <TD><INPUT type=\"text\" name=\"txt[]\" value=\"".$obj["label"]."\"></TD>\n";
        print "    <TD><input style=\"cursor: pointer; border-bottom: 1px solid #999;border-right: 1px solid #999\" class=\"color\" name=\"color[]\" value=\"".$obj["color"]."\" size=\"6\" />";
        if (empty($disable_edit)) {
        	print "&nbsp;<a href=\"javascript:deleteRow('rangesTable',".($idx+1).");\"><img src=\"img/delrange.png\" width=16></a>";
        }
        print "</TD>\n</TR>";
    
	}
} 
if (isset($action) && $action == "add") {
?>
        <TR>
            <TD><INPUT type="text" name="val[]" value="" size=4 /></TD>
            <TD><INPUT type="text" name="txt[]" value=""/></TD>
            <TD><input style="cursor: pointer; border-bottom: 1px solid #999;border-right: 1px solid #999" class="color" name="color[]" size="6" />&nbsp;<a href="javascript:deleteRow('rangesTable',<?php echo ($idx+1); ?>);"><img src="img/delrange.png" width=16></a></TD>
        </TR>
<?php
}
?>       
</TABLE>
</td>

<?php
	if ($type != "wordaligner") {
		print "<td valign=bottom><a href=\"javascript:addRange($taskid,'$type');\"><img src=\"img/addrange.png\" width=16 title=\"Add new value\"></a></td>";
	}
?>
</table>

<script>jscolor.init();</script>