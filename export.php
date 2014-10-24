<?php
ini_set('max_execution_time', 1000);

include("config.php");
include("functions.php");
if ($mysession["status"] == "root" || $mysession["status"] == "admin" || $mysession["status"] == "advisor") { 
	$namefile = "mteval_export".$format;
	if (isset($userid)) {
		$namefile .= "_".$userid;
	} else {
		$userid = null;
	}
	$namefile .= "-".date("Ymd_His");
	
	header("Pragma: public");
    header("Expires: -1");
    header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
    header("Content-type: application/zip");
	header("Content-Disposition: attachment; filename=\"".$namefile.".zip\"");
	header("Content-Transfer-Encoding: binary");
	
	if (isset($userid)) {
		//export CVD format
		if ($format == "csv") {
			exportCSV($userid);
		} else if ($format == "xml") {
			exportXML($userid);
		} 
	} else if (isset($taskid)) {
		if ($format == "csv") {
			exportTaskCSV($taskid);
		} else if ($format == "xml") {
			exportTaskXML($taskid);
		} else if ($format == "iob2") {
			exportTaskIOB2($taskid);
		}
	}
}
?>
