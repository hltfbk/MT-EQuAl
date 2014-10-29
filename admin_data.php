<!--
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
-->

<head>
<style>
tr.row:hover {
background:#cf4;
}
td:hover {
 background: #ececec;
}
div.uploadpane {
	visibility: hidden;
	height: 100%;
    width: 100%;
    position: fixed;
    left:0;
    top:0;
    z-index:10 !important;
    background-color: rgba(64, 64, 64, 0.5);
}
div.uploadform {
	left: 30%; 
	top: 30%; 
	position: absolute; background: #efefef; z-index: 11;   
    padding: 10px;
    opacity:1.0; 
    filter:alpha(opacity=100); /* internet explorer */
    -khtml-opacity: 1;      /* khtml, old safari */
    -moz-opacity: 1;       /* mozilla, netscape */
    opacity: 1;   		/* fx, safari, opera */
    margin: auto auto auto auto;
    vertical-align: middle;
}

</style>

<script>
function upload() {
	form = document.getElementById("uploadform");
    if (form != null) {
    	if (form.upfile.value == "") {
    		alert("WARNING! You must upload a valid CSV file or ZIP archive.");
    	} else if (form.tokenization.selectedIndex == 0) {
    		alert("WARNING! You must select the tokenization that will be applied.");
    	} else {
    		form.submit();
    	}
    } else {
    	alert("WARNING! A Javascript error occurred.");
    }
}

function showUpload (taskid, tasktype, filetype) {
	el = document.getElementById("uploadpane");
	if (el != null) {
		el.style.visibility='visible';
		form = document.getElementById("uploadform");
    	if (form != null) {
    		form.taskid.value=taskid;
    		form.filetype.value=filetype;
    		form.tokenization.selectedIndex = -1;
    		if (tasktype == "wordaligner") {
    			form.tokenization.options[4]=null;
    		} else {
    			form.tokenization.options[4]=new Option("NO", "0", false, false);
    		}
    	}
	}
}
function hideUpload () {
	el = document.getElementById("uploadpane");
	if (el != null) {
		el.style.visibility='hidden';
    }
}

function delSentences(taskid,type) {
	if (type=="source") {
		alert("WARNING! You are removing SOURCE sentences, so the alignment with reference and output ones could be not guaranteed any more.");
	}
	alertify.confirm("Do you really want to delete all "+type+" sentences of this task?", function (e) {
        if (e) {
        	window.open("admin.php?section=data&action=remove&taskid="+taskid+"&filetype="+type, "_self");
		}
	});	
}

</script>
</head>

<?php
//add new sentence
$errmsg="";
$tasks = getTasks($mysession["userid"]);
    
if (!empty($mysession["status"]) && ($mysession["status"] == "admin" || $mysession["status"] == "advisor")) {
  if (isset($taskid) && isset($tasks[$taskid])) {
  	  if (isset($action) && $action="remove") {
  		if (isAnnotatedTask($taskid) == 0) {
  			if (isset($filetype)) {
  				deleteSentences($taskid,$filetype);
				$errmsg="DONE! The $filetype resources have been removed.";
			} else {
				$errmsg="ERROR! The type information about the task is missing.";
			}
  		} else {
  			print "<script>alert('Warning! This resource cannot be deleted because some annotations are joined to it.');</script>";
  		}
  	  } else {
   		$ftmp = $_FILES['upfile']['tmp_name'];
  	
   		if (!empty($ftmp)) {
    		if ($_FILES["upfile"]["error"] > 0) {
    			$errmsg = "Upload error! Try again or contact the administrator.";
			} else {
				$oname = basename($_FILES['upfile']['name']);
	
			  	if (file_exists($ftmp)) {
  					if (preg_match("/\.zip$/", $oname)) {
  						$zip1 = new ZipArchive;
						$extract1 = $zip1->open($ftmp);
						if ($extract1 === TRUE) {
    		   				//Extract the archive contents
	    				    $zip1->extractTo(dirname($ftmp));
	    				    if (isset($filetype) && $filetype != "") {
      							for ($i = 0; $i < $zip1->numFiles; $i++) {
			    					$f = dirname($ftmp)."/".$zip1->getNameIndex($i);
			    					if (file_exists($f) && is_file($f)) {
    									$errmsg .= addFileData($taskid,$filetype,$tokenization,$f, basename($f));
      								}
      							}
      						} else {
      							#first upload all source files
      							for ($i = 0; $i < $zip1->numFiles; $i++) {
			    					$f = dirname($ftmp)."/".$zip1->getNameIndex($i);
    								$itype=basename(dirname($f));
    								if ($itype == "source") {
    									$errmsg .= addFileData($taskid,"source",$tokenization,$f, basename($f));
      								}
      							}
      							#then the rest of the files
      							for ($i = 0; $i < $zip1->numFiles; $i++) {
			    					$f = dirname($ftmp)."/".$zip1->getNameIndex($i);
    								$itype=basename(dirname($f));
    								if ($itype != "source" && in_array($itype, $sentenceTypes)) {
    									$errmsg .= addFileData($taskid,$itype,$tokenization,$f, basename($f));
      								}
      							}
      						}			
							$zip1->close(); 
   						} else {
   							$errmsg = "Failed to open zip file (code: $extract1)<br>";
   						}
			  		} else {
			  			if (isset($filetype)) {
  							$errmsg .= addFileData($taskid,$filetype,$tokenization,$ftmp,$oname);
  						}
					}	
				} else {
					$errmsg = "ERROR! Uploaded file hasn't been parsed correctly.";
				}
				if ($errmsg == "") {
    				$errmsg = "DONE! The data has been added.";
    			}
			}
		} 
		#print "Uploading... taskid: $taskid, filetype: $filetype<br>\n"; #.$_FILES['upfile']['tmp_name'].
  	}
  
  }
}

//show stored data
	
	print "<table border=1 cellspacing=0 cellpadding=2><tr bgcolor=#ccc><th>Task name</th>";
	foreach ($sentenceTypes as $stype) { 
		print "<th>$stype</th>";			
	}
	print "</tr>\n";	
	while (list ($tid,$tarr) = each($tasks)) {
		print "<tr class=row align=right><td nowrap><a href='admin.php?section=task&id=$tid'>".$tarr[0]."</a> <a href=\"javascript:showUpload($tid,'".$tarr[1]."','');\"><img src='img/add.png'></a></td>";
		$count_hash = countTaskSentences($tid);
		foreach ($sentenceTypes as $stype) { 
			print "<td nowrap>";
			if ($tarr[1] != "docann" || $stype == "source") {
				if (isset($count_hash[$stype])) {
					print $count_hash[$stype] ." <a href=\"javascript:delSentences($tid,'$stype');\"><img border=0 width=11 src='img/delete.png'></a>";	
				}
				print " <a href=\"javascript:showUpload($tid,'".$tarr[1]."','$stype');\"><img src='img/add.png'></a>";
			}
			print "</td>";
		}
		if (isset($taskid) && $tid == $taskid && !empty($errmsg)) {
			print "<td bgcolor=lightyellow>$errmsg</td>";
		}
		print "</tr>\n";
    }
	print "</table>";

//create upload form
if (!empty($mysession["status"]) && ($mysession["status"] == "admin" || $mysession["status"] == "root")) {
 ?>

<div id=uploadpane class=uploadpane>
<div class=uploadform> 
<div style="float: right; padding-left:10px">[<a href="javascript:hideUpload();">X</a>]</div>
<br>
<form action="admin.php?section=data" method="post" id=uploadform enctype="multipart/form-data">
<input type=hidden name=taskid value="" />
<input type=hidden name=filetype value="" />

 Upload your file <img src="img/question.png" width=18 onclick="alertify.alert('<div class=textleft><b>CSV format</b>: You can upload the sentences for a specific task using an UTF-8 encoded CSV file. One for the source sentences, one for the reference translation (optional), and one file for each of MT outputs to be evaluated.<br> Each file must contain three columns per line, separated by a tabular space:<br>- sentence ID;<br>- language (en, ar, it, zh,...);<br>- the sentence (using UTF-8 encoding).<br>This format is accepted for quality rating, annotation of translation errors, and word alignment tasks.<br><br><b>Raw text and TextPro format</b>: for the annotation document task you can upload a raw text or a TextPro output file (*.txp).<br><br><b>Zip archive</b><br>Multiple files can be uploaded as a zip file.<br><br><u>NB: the max size of the upload file must be <?php echo (int)(ini_get('upload_max_filesize')); ?>Mb</u></div>'); return false;"></a>:<br>
<input type="file" id="upfile" name="upfile"> 
 <!-- </br><br> (you can catch just <INPUT TYPE=text NAME=limit value="<?php if (isset($limit)) { echo $limit;} ?>" size=5> sentences from the file) -->
 
  </br><br>
  Tokenization: <select name="tokenization">
  	<option value='' disabled>
	<option value='1'>YES, using spaces only
	<option value='2'>YES, using spaces and punctuations	
	<option value='3'>YES, character by character
	</select>
	</br></br>
  <a href="javascript:upload()"><input type="button" name="Upload" value="Upload"></a>	
  <input type="button" onclick="javascript:hideUpload();" value="Cancel">
</form>
</div>
</div>
<?php
}
?>
