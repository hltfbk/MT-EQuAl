<?php 
/*
MT-EQuAl: a Toolkit for Human Assessment of Machine Translation Output

Copyright 2014, Christian Girardi (cgirardi@fbk.eu)

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

session_start();
include("initdb.conf");

			  
if (isset($_REQUEST)) {
    $PHP_SELF = $_SERVER['PHP_SELF'];
    $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
    $REMOTE_HOST = "";
    while (list($key,$val) = each($_REQUEST)) {
		#print "$key = ";
		if (is_array($val) && count($val) > 0) {
		    #print "L: $key = " . implode(" ",$val) ."<br>";
	    	eval("$\$key = array('" .implode("','",$val) . "');");
		} else {
		    #print "V: $key = $val<br>";
	    	eval("$\$key = '" . addslashes($val) . "';");
		}
    }
   
    if (isset($_SESSION) && isset($_SESSION["mysession"])) {
		$mysession = $_SESSION["mysession"];
	}

}

#debugging layer
if (DEBUG == "yes") {
	print"<div class=debug>";
	if (isset($mysession)) {
		while (list ($key, $value) = each($mysession)) {
			print $key.": <b>" .$value."</b><br>\n";
		}
	}
	print "</div>";
}	


# db connection	
$db = @mysql_pconnect(DB_HOST,DB_USER,DB_PASSWORD) or die("<center><br><h3>Fatal Error: unable to connect to the MTEval database server (attempted on ". DB_HOST . ").<br>Please contact ".SYSADMIN.".</h3></center>");  
    
if (!mysql_ping ($db)) {
	//here is the major trick, you have to close the connection (even though its not currently working) for it to recreate properly.
	mysql_close($db);
	$db = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("Error: cannot connect to " . DB_HOST);
}
if ( !@mysql_select_db(DB_NAME,$db) ) {
	#echo "<p>Unable to find the ".DB_NAME." database on ".DB_HOST.". Please contact ".SYSADMIN.".<p>";
	echo "<p>Unable to find the MTEval database. Please contact ".SYSADMIN.".<p>";
    #echo mysql_errno($db) . ": " . mysql_error($db). "\n";
	exit();
}  

?>