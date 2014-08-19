<table><td>
<TABLE id="rangesTable" style="font-size:13px" cellpadding=2 cellspacing=0 border="0">
<TR><TH>value<br><small>(integer)</small></TH><TH>label</TH><TH align=left>color</TH></TR>
<?php
include("config.php");
$idx=0;
if (isset($ranges)) {
	$dec = json_decode(stripslashes($ranges));

	for($idx=0;$idx<count($dec);$idx++){
    	$obj = (Array) $dec[$idx];
    	//echo $obj["name"]." ".$obj["id"]."<br>";
    	print "<TR>\n    <TD><INPUT type=\"text\" name=\"val[]\" value=\"".$obj["val"]."\" size=4 /></TD>\n";
        print "    <TD><INPUT type=\"text\" name=\"txt[]\" value=\"".$obj["label"]."\"></TD>\n";
        print "    <TD><input style=\"cursor: pointer; border-bottom: 1px solid #999;border-right: 1px solid #999\" class=\"color\" name=\"color[]\" value=\"".$obj["color"]."\" size=\"6\" />&nbsp;<a href=\"javascript:deleteRow('rangesTable',".($idx+1).");\"><img src=\"img/delrange.png\" width=16></a>";
        
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
<td valign=bottom><a href="javascript:addRange();"><img src="img/addrange.png" width=16 title="Add new value"></a></td></table>

<script>jscolor.init();</script>