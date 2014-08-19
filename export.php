<?php
	include("config.php");
	include("functions.php");
	
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
	
	if (!empty($mysession["status"]) && $mysession["status"] == "admin" || $userid=="3") {
		//export CVD format
		if ($format == "csv") {
			exportCSV($userid);
		} else if ($format == "xml") {
			exportXML($userid);
		} 
	}

?>
