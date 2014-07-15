<html>
<head>
<link href="css/mtequal.css" rel="styleSheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/mtequal.js"></script>
	
<?php
header("Content-type: text/html; charset=utf-8");
include("config.php");
include("functions.php");


$sentence_hash = getSentence($id);
	if (!isset($sentence_hash["source"])) {
		header("Location: index.php#".($id-1)); 
		exit;
	}
?>

<style>
html{height:100%}body{height:100%;min-width:980px;overflow:hidden;font-family:verdana,arial,helvetica;font-size:12px;margin:0;padding:0;}
</style>
</head>

<body>

<div style="background-color: #FFFFFF; z-index:9999; position: absolute; width: 100%; height: 100%; border-right: 1px solid #222; border-left: 1px solid #222">
<table cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr height="1%">
<td style="top:0; width:100%">

<?php
include("menu_sentence.php");

$monitoring=0;
if (isset($userid) && $userid != $mysession['userid'] && ($mysession["status"] == "admin" || $mysession["status"] == "advisor")) {
	$time = date( "d/m/Y H:m:s", time() );
	print "<div style='display: inline-block; background: yellow; border: dashed #777 1px; border-radius: 0px 0px 15px 15px;  padding: 9px; font-size:12px; position:absolute; top: 0px; margin-left: 320px; z-index:1000'>Monitoring... sentence <b>$id</b>, user: <b>$userid</b> ($time)<br><a href='admin.php#user$userid' style='float:right'>« Back to Admin</a></div><br>";
	$monitoring=1;
	$sentidx=-1;
} else {
	if (isset($mysession['userid'])) {
		$userid = $mysession['userid'];
		if (!isset($taskid)) {
 			$taskid = $mysession["taskid"];
 		}
		
	} else {
		print "<br><font color=red>Access denied!</font> You are an unregistered user or your session has expired. Please <a href='index.php' target='_top'>login</a> again!";
		return;
	} 
}
if (empty($mysession["status"])) {
	print "<script>window.open('index.php','_self');</script>";
}

$errorlabel = "Errors";
if (!isset($errorid)) {
	$errorid = "";
} else {
	if (!empty($errorid) && $errorid >= 0) {
		$errorlabel = $evalcodes["errors"][$errorid];
	}
}
?>

<span style="float: right; padding-right: 20px; padding-top: 9px; width:20%;">
<div style='float: right; right: 0px; top:0px; display: inline-block; position: fixed;text-align: left; background: #eee; font-size: 12px; padding-top: 10px; padding-left: 10px; padding-right: 10px; padding-bottom: 10px; border: solid #999 1px; border-radius: 0px 0px 0px 15px; z-index: 1000'>
		<button href="#collapse1" class="nav-toggle" style='float: right; margin-top: -4px;'>read more</button><div style="float: right; margin-right: 20px">Task instructions<br></div>
		<div id="collapse1" style="display:none; font-size: 14px;">
		<br><br>
In this task you are presented with a source sentence and 2 automatic translations. Since the source sentence is presented in isolation, a reference human translation is also given with the only purpose of better understanding the source text if necessary.<br>
<br>
For each automatic translation, you are asked to identify the type of errors present (if any) and mark their position in the translation. Remember that the automatic translations must be compared directly with the source text (and not with the reference translation).<br>
<br> 
The error typology is composed of four main classes:<br>
 
<ul>
<ol start="1"> 
<li><b>Reordering errors</b></li>
<li><b>Lexicon errors</b></li>
<dt>- a. addition of words<dt>
<dt>- b. missing word(s) (i.e. source word dropped)</dt>
<dt>- c. incorrect choice of words (including not recognized idioms)</dt>
<dt>- d. misspelled words, diacritics errors</dt>
<dt>- e. foreign source words passed through (including proper names)</dt>
<li><b>Morphology errors</b></li>
	<dt>- a. errors with conjugation, tense, modality, declension, concordance in number, gender, case.</dt>
<li><b>Casing and punctuation errors.</b></li>
	<dt>- a. capitalization errors (lowercase/uppercase)</dt>
	<dt>- b. errors involving punctuation and parentheses.</dt>
</ol>
</ul>
	</div>	
</div>
</span>

<?php
    $sentence_hash = getSentence($id);
	print "<div style='display: block; width: 100%; float: left;left: 0px;'><div class=label>SOURCE: </div>" .showSentence ($sentence_hash["source"][0], $sentence_hash["source"][1], "source")."<div>";
	if (isset($sentence_hash["reference"])) {
		print "<div class=labelref>REFERENCE: </div>" . showSentence ($sentence_hash["reference"][0], $sentence_hash["reference"][1], "reference")."<div>";;
	}
?>
 
  </div>
                                
                               <!-- <div id="bottom">
										<span style="float: left; padding-left: 20px;">
											<a href="http://adf.ly/?id=1272398" target="_blank">AdF.ly</a> : shorten urls and earn money										</span>
										<span style="float: right; padding-right: 20px;">
											<a href="http://adf.ly/advertiser/advertising" target="_blank">Your Site Here: 10,000 visitors / $5.00</a>
										</span>
										
									</div>-->
							
								</div>
							</td>
						</tr>
						<tr>
							<td valign=top>
							<div style='display: inline-block; box-shadow: 3px -5px 5px #888; position: relative;  margin-bottom: 5px; width: 100%; height: 6px; '>
							</div>
							<iframe src="errors_output.php?id=<?php echo $id; ?>&taskid=<?php echo $taskid; ?>&userid=<?php echo $userid; ?>&sentidx=<?php echo $sentidx; ?>&monitoring=<?php echo $monitoring; ?>" style="border: 0px; padding-left: 0px; margin-top: -10px; width:100%; height:100%"></iframe>
							</td>
						</tr>
					</table>
                    
				</div>


<script>
$(document).ready(function() {
  	$('.nav-toggle').click(function() {
		//get collapse content selector
		var collapse_content_selector = $(this).attr('href');					
		//make the collapse content to be shown or hide
		var toggle_switch = $(this);
		$(collapse_content_selector).toggle(function(){
			if($(this).css('display')=='none'){
				//change the button label to be 'Show'
				if (this.id.indexOf("comm") == 0) {
					toggle_switch.html("<img src='img/addcomment.png' style='vertical-align: top; float: right;' width=80>");
					
					el = document.getElementById(this.id+"_text");
					if (el != null) {
						save_comment(this.id,el.value);
						$("#"+this.id+"_label").html(el.value);
						elComment = document.getElementById(this.id+"_label");
						elComment.style.visibility = "visible";
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
<?php
if (isset($userid) && $userid != $mysession['userid'] && ($mysession["status"] == "admin" || $mysession["status"] == "advisor")) {
	print "<script>\n  setTimeout(\"window.open('errors.php?id=$id&userid=$userid&taskid=$taskid','_self')\", 5000);\n</script>\n";
}
?>			
</body>
</html>