<link href="css/mtequal.css" rel="styleSheet" type="text/css">
<link href="css/wordaligner.css" rel="styleSheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/mtequal.js"></script>
	
<?php
header("Content-type: text/html; charset=utf-8");
include("config.php");
include("functions.php");

#activate the Javascript functions
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
 	if (!isset($sentidx)) {
 		$sentidx=-1;
 	}
}
?>
</head>

<div id="errortypes" onclick="this.style.visibility='hidden';" style="font-size: 10px"></div>
<?php
print "<div class=donebottom>";
if ($sentidx > 0) {
	$prevpage = "wordaligner.php?id=".($id-1)."&taskid=$taskid&sentidx=".($sentidx-1);
	$nextpage = "wordaligner.php?id=".($id+1)."&taskid=$taskid&sentidx=".($sentidx+1);
	print "<button id=prev name=prev onclick=\"javascript:next('$prevpage');\">&nbsp;« prev&nbsp;</button> &nbsp;";
}
print "<button style='width: 170' id=done name=done onclick=\"javascript:doneAndIndex('$id','$userid',this);\" disabled></button> &nbsp;";
if ($sentidx > 0) {
	print "<button id=next name=next onclick=\"javascript:next('$nextpage');\">&nbsp;next »&nbsp;</button>";
}		
print "</div>";
if (empty($mysession["status"])) {
	print "<script>window.open('index.php','_self');</script>";
}

if ($taskid > 0 && isset($id) && isset($userid)) {	
	$hash_target = getSystemSentences($id,$taskid);
	$i = 1;
	$checked = 0;
	print "<div style='width: 100%; position: relative; height: 100%; margin-top:0px; margin-left: -28px;  padding-right: 47px; margin-bottom: auto; overflow-y: auto;'>";
	if (count($hash_target) > 0) {
	$sentence_hash = getSentence($id,$taskid);
	while (list ($sentence_id, $sentence_item) = each($hash_target)) {
		$errors = getErrors($id, $sentence_id, $userid);
		
		print "<table cellpadding=0 cellspacing=2 border=0> <td valign=top>";
		print "<div class='cell'>";
		//print "<div style='display: table-cell; float: left; width: 666px'>";
		
		//Add output row
		$sent = showSentence ($sentence_item[0], $sentence_item[1], "output", "no",$sentence_id);
		//ripristino eventuali errori nei carattri con lastring vuota se non sono stati fatte delle anotazioni
		#if(count($errors) == 0) {
		#	$sent = preg_replace("/<img src='img\/check_error.png' width=16>/","",$sent);
		#}
		print "<div class=row><div class=label>OUTPUT <b>$i</b>: </div>$sent</div>";
		//end output row
		
		//Add comment row
		print "<div class=row><div class=cell>";
		$comment = getComment($sentence_id,$userid);
		
		if ($monitoring==0) {		
		?>
		<!-- comment -->
		<a href='#comm<?php echo $sentence_id; ?>' class='nav-toggle'><img src='img/addcomment.png' style='vertical-align: top; float: right;' width=80></a>&nbsp;</div>
		<div class=cell>
			<div id="comm<?php echo $sentence_id; ?>" style="display:none; font-size: 12px;">
				<textarea id=comm<?php echo $sentence_id; ?>_text rows=2 cols=75 style='background: lightyellow'><?php echo $comment; ?></textarea>
			</div>
			<div style="display: inline-block; top: 10px;" id=comm<?php echo $sentence_id; ?>_label><?php echo $comment; ?></div>		
		
		<?php
		} else {
			if ($comment !="") {
				print "<small><i>Comment:</i></small> </div>$comment<div class=cell>";
			}
		}
		?>
		<!-- fine comment -->
		<?php
		print "</div>";
		//end comment row	
			
		print "</div></table>";
		//end cell (output+comment)
		
		//print "###### $id, $sentence_id, $userid";
		$hash_eval = getErrors($id, $sentence_id, $userid);
		
		//start matrix		
		echo "<script type=\"text/Javascript\">\nvar sourceString=\"". preg_replace('/\"/',"&quot;", join(" ",getTokens($sentence_hash["source"][0], trim($sentence_hash["source"][1])))) ."\";\n".
			"var targetString=\"".preg_replace('/\"/',"&quot;", join(" ",getTokens($sentence_item[0], trim($sentence_item[1]))))."\";\n";
		if (isset($hash_eval[0])) {		
			echo "var initialPossAlignments=\"".$hash_eval[0][0]."\";\n";
		} else {	
			echo "var initialPossAlignments=\"\";\n";
		}
		if (isset($hash_eval[1])) {
			echo "var initialSureAlignments=\"".$hash_eval[1][0]."\";\n";
		} else {	
			echo "var initialSureAlignments=\"\";\n";
		}
		
		//echo "var initialSureAlignments=\"1-6 2-0\";\n";
		//echo "var initialPossAlignments=\"0-6\";\n";
	
?>
	var disagreed_alignments_type1="";
	var disagreed_alignments_type2="";
	var disagreed_alignments_type3="";
		
    // the URL directory where rotated word images are stored
	//var imageDirectory = "http://ironman.jhu.edu/wordImageServer/";
	var imageDirectory = "./img/";

	// specify whether the languages should be written right-to-left
	var sourceIsRTL = false;
	var targetIsRTL = false;

	// indicates whether to switch the view from the source being 
	// along top (default) or the target on top (transposed).
	// The results still keep the same source / target names.
	var viewTransposed = false;


	// read in the values for this sentence pair

	// ${source} and ${target} should contain whitespace delimited words
	//var sourceString = "${source}";
	//var targetString = "${target}";
	//var initialSureAlignments = "${sureAlignments}";

	// sure alignments should have the format "0-0 0-1 1-2" where the first number 
	// in each pair is the index of the source word, and the second it the target
	//var initialPossAlignments = "${possAlignments}";


	// indicates which rows and columns are highlighted 
	var initialSourceHighlights = "${sourceHighlights}";
	var initialTargetHighlights = "${targetHighlights}";

	var initialDisagreedAlignmentsType1 = "";
	var initialDisagreedAlignmentsType2 = "";
	var initialDisagreedAlignmentsType3 = "";
	

	var sourceIsRTL = false;
	var targetIsRTL = true;


	if(viewTransposed) {
		var tmp = sourceString;
		sourceString = targetString;
		targetString = tmp;
		tmp = initialSourceHighlights;
		initialSourceHighlights = initialTargetHighlights;
		initialTargetHighlights = tmp;
		tmp = sourceIsRTL;
		sourceIsRTL = targetIsRTL;
		targetIsRTL = tmp;
		initialSureAlignments = transposeAlignments(initialSureAlignments);
		initialPossAlignments = transposeAlignments(initialPossAlignments);
		initialDisagreedAlignmentsType1 = transposeAlignments(disagreed_alignments_type1);
		initialDisagreedAlignmentsType2 = transposeAlignments(disagreed_alignments_type2);
		initialDisagreedAlignmentsType3 = transposeAlignments(disagreed_alignments_type3);
	}


	// split the source and target sentences into words
	var whitespacePattern = /\s/;
	var sourceWords = sourceString.split(whitespacePattern);
	var targetWords = targetString.split(whitespacePattern);

	var width = sourceWords.length;
	var height = targetWords.length;
	
	// initialize the sureGrid and the probGrid
	var sureGrid = initalizeBooleanGrid(width, height, initialSureAlignments);
	var probGrid = initalizeBooleanGrid(width, height, initialPossAlignments);
	var disagreed_alignments_type1Grid = initalizeBooleanGrid(width, height, initialDisagreedAlignmentsType1);
	var disagreed_alignments_type2Grid = initalizeBooleanGrid(width, height, initialDisagreedAlignmentsType2);
	var disagreed_alignments_type3Grid = initalizeBooleanGrid(width, height, initialDisagreedAlignmentsType3);

	// initialize the highlighted rows and columns
	var sourceHighlights = initalizeBooleanArray(width, initialSourceHighlights);
	var targetHighlights =  initalizeBooleanArray(height, initialTargetHighlights);
	
		
	if(sourceIsRTL) {
		writeHtmlAlignmentTableRTL(sourceWords, targetWords, sureGrid, probGrid, sourceHighlights, targetHighlights, imageDirectory);
	} else {
		writeHtmlAlignmentTable(sourceWords, targetWords, sureGrid, probGrid, disagreed_alignments_type1Grid, disagreed_alignments_type2Grid, disagreed_alignments_type3Grid, sourceHighlights, targetHighlights, imageDirectory);
	}


	// log the time...
	date = new Date();
	timeAtStart = date.getTime();
	timeOfLastModification = date.getTime();
	activeTime = 0;

	// Transposes the string form of the alignment.  Changes each x-y into y-x
	function transposeAlignments(alignmentString) {
		var transposedAlignmentsString = "";
		var whitespacePattern = /\s/;
		var dash = '-';
		var points = alignmentString.split(whitespacePattern);
		for(i = 0; i < points.length; i++) {
			if(points[i].indexOf(dash) > 0) {
				var point = points[i].split(dash);
				var x = point[0];
				var y = point[1];
				var transposedAlignmentsString = transposedAlignmentsString + y + "-" + x + " ";
			}
		}	
		transposedAlignmentsString.replace(/\s$/, '');
		return transposedAlignmentsString;
	}

	// Returns an initialized boolean grid.  Sets the points to true
	// that are included in the alignmentString as "x-y".
	function initalizeBooleanGrid(width, height, alignmentString) {
		var grid = new Array(width);
		for (i = 0; i < grid.length; i++) {
			grid[i] = new Array(height);
			for(j = 0; j < height; j++) {
				grid[i][j] = false;
			}
		}
		// Set the points in alignmentString to true
		var whitespacePattern = /\s/;
		var dash = '-';
		var points = alignmentString.split(whitespacePattern);
		var errmsg = "";
		for(i = 0; i < points.length; i++) {
			if(points[i].indexOf(dash) > 0) {
				var point = points[i].split(dash);
				var x = point[0];
				var y = point[1];
				if (typeof(grid[x]) == 'undefined' || typeof(grid[x][y]) == 'undefined') {
					errmsg += " "+x+"-"+y;
				} else {
					grid[x][y] = true;
				}
			}
		}	
		if (errmsg != "") {
			alert("WARNING! These offsets" + errmsg +" are out of the matrix");
		}
		return grid;
	}


	// Returns an initialized boolean array
	function initalizeBooleanArray(length, indexOfTruesString) {
		// pad the indexOfTruesString with spaces
		indexOfTruesString = " " + indexOfTruesString + " ";
		var array = new Array(length);
		for (i = 0; i < array.length; i++) {
			array[i] = false;
		}

		// set the points in alignmentString to true
		var whitespacePattern = /\s/;
		var indicies = indexOfTruesString.split(whitespacePattern);
		for(i = 0; i < indicies.length; i++) {
			var index = indicies[i];
			array[index] = true;
		}	
		return array;
	}

	// This method outputs the HTML table with clickable grid squares 
	// that are indexed into the sure and prob alignment boolean grids.
	// There is an alternate version for source languages that should be
	// displayed right-to-left.
	function writeHtmlAlignmentTable(sourceWords, targetWords, sureGrid, probGrid, disagreed_alignments_type1Grid, 
					 disagreed_alignments_type2Grid, disagreed_alignments_type3Grid, highlightedSourceWords, highlightedTargetWords, imageDirectory) {
		var smallerFont = false;
		var size = 20;
		var fontSize = 0;
		if(sourceWords.length > 20 || targetWords.length > 20) {
			size = 15;
			smallerFont = true;
			fontSize = -1;
		}
		document.write('<table style="margin-left: 35px">\n');
		// print the source words as a table header
		document.write('<tr>\n');
		
		// write the source words
		document.write('\t<td></td>\n');
		for(i = 0; i < sourceWords.length; i++) {
			var word = sourceWords[i];
			document.write('\t<td valign="bottom" bgcolor=#ADE9E6 onmouseover="this.style.cursor=\'crosshair\'" onClick="javascript:clickColumn(<?php echo $sentence_id; ?>,' + i + ')">');
			//document.write('<div class=verticaltext >' + word + '</div>');
			document.write('<div class=verticaltext>' + word + '</div>');
			//document.write('<img src = "' + getImagePath(word, imageDirectory) + '" width="' + size + '" alt="' + word + '"' + ' title="' + word + '" border="0">');
			
			document.write('</td>\n');
		}
		document.write('\t<td></td>\n');
		document.write('</tr>\n');

		for(row = 0; row < targetWords.length; row++) {
			// print the target word
			document.write('<tr heigth=\"'+size+'px\">\n');	
			var targetWord = targetWords[row];
			if(!targetIsRTL) {
				document.write('<td>');	
			} else {
				document.write('<td align=left bgcolor=#E6E6FA onmouseover="this.style.cursor=\'crosshair\'" onClick="javascript:clickRow(<?php echo $sentence_id; ?>,' + row + ')">');	
			}
			if(smallerFont) { 
				document.write('<font size=' + fontSize + '>');
			}
			document.write(targetWord);
			if(smallerFont) document.write('</font>');
			document.write('</td>\n\t');
			// print this row
			for(column = 0; column < sourceWords.length; column++) {			
				if (disagreed_alignments_type1Grid[column][row]) {				
					document.write('<td width="' + size +'" class="blue" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '"');
				} else if (disagreed_alignments_type2Grid[column][row]) {				
					document.write('<td width="' + size +'" class="yellow" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '"');
				} else if (disagreed_alignments_type3Grid[column][row]) {				
					document.write('<td width="' + size +'" class="red" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '"');
				} else if(sureGrid[column][row]) {
					document.write('<td width="' + size +'" class="black" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '"');
				} else if(probGrid[column][row]) {
					document.write('<td width="' + size +'" class="gray" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '"');
				} else {
					if(highlightedSourceWords[column] || highlightedTargetWords[row]) {
						document.write('<td width="' + size +'" class="highlight" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '"');
					} else {
						document.write('<td width="' + size +'" class="white" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '"');
					}
				}
				document.write(" onmouseover=\"this.style.cursor='pointer'\" onClick=\"javascript:clickButton('<?php echo $id; ?>','<?php echo $sentence_id; ?>','<?php echo $userid; ?>','" + column + "','" + row + "')\">");
				document.write('<img src= "'+ imageDirectory + 'clearpixel.gif" border="0" ');
				document.write('title="' + targetWords[row] + ', ' + sourceWords[column]+ ' ['+column+','+row+']" ');
				document.write('title="' + targetWords[row] + ', ' + sourceWords[column]+ '" ');
				document.write('width="' + size + '" height="' + size + '">');
				document.write('</td>\n');	
			}
			
			// print the target word again
			if(!targetIsRTL) {
				document.write('<td>');	
			} else {
				document.write('<td align=left bgcolor=#E6E6FA onmouseover="this.style.cursor=\'crosshair\'" onclick="javascript:clickRow(<?php echo $sentence_id; ?>,' + row + ')">');	
			}
			if(smallerFont) document.write('<font size=' + fontSize + '>');
			document.write(targetWord);
			if(smallerFont) document.write('</font>');
			document.write('</td>\n\t');
			
			document.write('</tr>');	
			document.write('\n');
		}
		

		// write the source words again
		document.write('\t<td></td>\n');
		for(i = 0; i < sourceWords.length; i++) {
			var word = sourceWords[i];
			document.write('\t<td valign=bottom bgcolor=#ADE9E6 onmouseover="this.style.cursor=\'crosshair\'" onclick="javascript:clickColumn(<?php echo $sentence_id; ?>,' + i + ')">');
			document.write('<div style="margin-top: '+(word.length/2*13)+ 'px;"><div class=verticaltext-rtl>' + word + '</div></div>');
			//document.write('<div class=verticaltext-rtl>' + word + '</div>');
			//document.write('<img src = "' + getImagePath(word, imageDirectory) + '" width="' + size + '" alt="' + word + '"' + ' title="' + word + '" border="0">');
			
			document.write('</td>\n');
		}
		document.write('\t<td></td>\n');
		document.write('</tr>\n');

		document.write('</table>\n');
		
	//	document.write('<p> PIPPO: ' + sureGrid[4][1] + sureGrid[1][4] + ' </p>');
			
	}


	// An alternate version of writeHtmlAlignmentTable which displays the
	// source language in a right to left (RTL) fashion. 
	function writeHtmlAlignmentTableRTL(sourceWords, targetWords, sureGrid, probGrid, 
					 highlightedSourceWords, highlightedTargetWords, imageDirectory) {
		var smallerFont = false;
		var size = 15;
		var fontSize = 0;
		if(sourceWords.length > 20 || targetWords.length > 20) {
			size = 11;
			smallerFont = true;
			fontSize = -1;
		}
			
		document.write('<table>\n');
		// print the source words as a table header
		document.write('<tr>\n');
		
		// write the source words
		document.write('\t<td></td>\n');
		for(i = sourceWords.length-1; i >= 0; i--) {
			var word = sourceWords[i];
			document.write('\t<td valign="bottom">');
			document.write('<a href="javascript:clickColumn(<?php echo $sentence_id; ?>,' + i + ')">');
			document.write('<div class=verticaltext ><div style="width: ' + fontSize + 'px">' + word + '</div></div>');
			//document.write('<img src = "' + getImagePath(word, imageDirectory) + '" width="' + size + '" alt="' + word + '"' + ' title="' + word + '" border="0">');
			
			document.write('</a>');
			document.write('</td>\n');
		}
		document.write('\t<td></td>\n');
		document.write('</tr>\n');

		for(row = 0; row < targetWords.length; row++) {
			// print the target word
			document.write('<tr>\n');	
			var targetWord = targetWords[row];
			document.write('<td>');	
			if(smallerFont) { 
				document.write('<font size=' + fontSize + '>');
			}
			document.write('<span class="blacklink"><a href="javascript:clickRow(<?php echo $sentence_id; ?>,' + row + ')">');
			document.write(targetWord);
			document.write('<a href="javascript:clickRow(<?php echo $sentence_id; ?>,' + row + ')"></span>');
			if(smallerFont) document.write('</font>');
			document.write('</td>\n\t');
			// print this row
			for(column = sourceWords.length-1; column >= 0; column--) {
				if(sureGrid[column][row]) {
					document.write('<td class="black" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '">');
				} else if(probGrid[column][row]) {
					document.write('<td class="gray" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '">');
				} else {
					if(highlightedSourceWords[column] || highlightedTargetWords[row]) {
						document.write('<td class="highlight" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '">');
					} else {
						document.write('<td class="white" id="<?php echo $sentence_id; ?>.' + column + '.' + row + '">');
					}
				}
				document.write("<a href=\"javascript:clickButton('<?php echo $id; ?>','<?php echo $sentence_id; ?>','<?php echo $userid; ?>','" + column + "','" + row + "')\">");
				document.write('<img src= "'+ imageDirectory + 'clearpixel.gif" border="0" ');
				document.write('title="' + targetWords[row] + ', ' + sourceWords[column]+ '" ');
				document.write('width="' + size + '" height="' + size + '"></a>');
				document.write('</td>\n');	
			}
			
			// print the target word again
			document.write('<td>');	
			if(smallerFont) document.write('<font size=' + fontSize + '>');
			document.write('<span class="blacklink"><a href="javascript:clickRow(<?php echo $sentence_id; ?>,' + row + ')">');
			document.write(targetWord);
			document.write('<a href="javascript:clickRow(<?php echo $sentence_id; ?>,' + row + ')"></span>');
			if(smallerFont) document.write('</font>');
			document.write('</td>\n\t');
			
			document.write('</tr>');	
			document.write('\n');
		}
		

		// write the source words again
		document.write('\t<td></td>\n');
		for(i = sourceWords.length-1; i >= 0; i--) {
			var word = sourceWords[i];
			document.write('\t<td valign="top">');
			document.write('<a href="javascript:clickColumn(<?php echo $sentence_id; ?>,' + i + ')">');
			document.write('<div class=verticaltext-rtl>' + word + '</div>');
			//document.write('<img src = "' + getImagePath(word, imageDirectory) + '" width="' + size + '" alt="' + word + '"' + ' title="' + word + '" border="0">');
			
			document.write('</a>');
			document.write('</td>\n');
		}
		document.write('\t<td></td>\n');
		document.write('</tr>\n');

		document.write('</table>\n');
	}

	
function clickButton(id, sentid, userid, x, y) { 
	button = document.getElementById(sentid+"."+x+"."+y);
	if (button != null) {
		activateDone("<?php echo $monitoring;?>");
		if (button.className == "white" || button.className == "highlight") {
			button.className = "gray";
			//alert(id+","+ sentid+","+ userid+","+x+","+y + " :: " +getCheckedButton(sentid,"gray"));
			saveAlignment(id, sentid, userid, 0, getCheckedButton(sentid,"gray")); 
		} else if (button.className == "gray") {
			button.className = "black";
			saveAlignment(id, sentid, userid, 0, getCheckedButton(sentid,"gray")); 
			saveAlignment(id, sentid, userid, 1, getCheckedButton(sentid,"black")); 
		} else {
			button.className = "white";
			saveAlignment(id, sentid, userid, 1, getCheckedButton(sentid,"black")); 	
		}
	}
		
	//alert("SURE: "+getCheckedButton(sentid,"black")+"\nPOSS: " +getCheckedButton(sentid,"gray"));
}
	
function clickRow(sentid, y) { 
	for(var x = 0; x < width; x++) {
		var button = document.getElementById(sentid+"."+x+"."+y);
		if (button.className == "white") {
			button.className = "highlight";
		} else if (button.className == "highlight") {	
			button.className = "white";
		}
	}
}
	
function clickColumn(sentid, x) { 
	for(var y = 0; y < height; y++) {
		var button = document.getElementById(sentid+"."+x+"."+y);
		if (button.className == "white") {
			button.className = "highlight";
		} else if (button.className == "highlight") {	
			button.className = "white";
		} 
	}
}
	
function getCheckedButton (id, color) {
	var ranges="";
	for (var x=0; x >= 0; x++) {
		var y=0;
		el = document.getElementById(id+"."+x+"."+y);
        if (el == null) {
	  		break;
       	}
       	while (true) {
			if (el.className == color) {
                ranges += x+"-"+y+" ";
            }
            y++;
        	
        	el = document.getElementById(id+"."+x+"."+y);
        	if (el == null) {
	  			break;
       		}      		 	
    	}
    }
    return ranges.replace(/\s+$/g, "");
}

function saveAlignment (id, sentid, userid, checkid, alignids) {
$.ajax({
  url: 'update.php',
  type: 'GET',
  data: {id: id, targetid: sentid, userid: userid, check: checkid, alignids: alignids},
  async: false,
  cache:false,
  crossDomain: true,
  success: function(response) {
  	if (response == "error") {
  		//$("#log").html("");
  		alert("Warning! A problem occurred during saving the data. Try again later!");
  	} 
  },
  error: function(response, xhr,err ) {
        //alert(err+"\nreadyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\nresponseText: "+xhr.responseText);
        //alert(ERRORID);
        switch(xhr.status) {
			case 200: 
				$("#log").html('<font color=gray>Data saved!</font>');
      			break;
    		case 404:
      			$("#log").html('<font color=red>Could not contact server.</font>');
      			break;
    		case 500:
      			$("#log").html('<font color=red>A server-side error has occurred.</font>');
      			break;
    		}   
    	setTimeout(function(){$("#log").html('')}, 3000);		
    }
  });
  
}

function boolGridToString(grid) {
	var gridString = "";
	for(i = 0; i < grid.length; i++) {
		row = grid[i];
		for(j = 0; j < row.length; j++) {
			if(grid[i][j]) {
				gridString += i + "-" + j + " ";
			}
		}
	}
	// remove the training space
	gridString = gridString.substring(0, gridString.length-1);
	if(viewTransposed) {
		//gridString = transposeAlignments(gridString);
	}
	return gridString;
}
	
// Converts an array of highlights into a string
function highlightsToString(array) {
	var arrayString = "";
	for(i = 0; i < array.length; i++) {
		if(array[i]) {
			arrayString += i + " ";
		}
	}
	// remove the training space
	arrayString = arrayString.substring(0, arrayString.length-1);
	return arrayString;
}
	
function goto (url) {
	var button = document.getElementById("updatebutton");
	if (button == null || button.disabled == true) {
		window.location = url;
	} else {
	   if (confirm("You have unsaved changes, are you sure that you want to cancel? All of your changes will be lost.")) {
	   	   window.location = url;
	   }
	}
}
	
</script>

<?php  
		//end matrix
	
		
		print "<div style='display: inline-block; border-top: dashed #666 1px; width: 100%'>&nbsp;</div>";
		$i++;
	}
	#print "</div>";
	
	#print count($hash_target) ."!= $checked || ".isDone($id,$userid);
	if (isDone($id,$userid) > 0) {
		print "<script>alreadyDone();</script>";
	} else {
		if ($checked != count($hash_target)) {
	 		print "<script>notDoneYet();</script>";
		} else {
			print "<script>activateDone(".$monitoring.");</script>";
		}
	} 
	} else {
		print "<h3><font color=red>No output found!</font></h3>";
	}
} 
?>


