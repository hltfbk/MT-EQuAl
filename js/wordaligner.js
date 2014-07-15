//  cgirardi@fbk.eu

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
		document.write('<table>\n');
		// print the source words as a table header
		document.write('<tr>\n');
		
		// write the source words
		document.write('\t<td></td>\n');
		for(i = 0; i < sourceWords.length; i++) {
			var word = sourceWords[i];
			document.write('\t<td valign="bottom" bgcolor=#ADE9E6 onmouseover="this.style.cursor=\'crosshair\'" onClick="javascript:clickColumn(' + i + ')">');
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
				document.write('<td align=left bgcolor=#E6E6FA onmouseover="this.style.cursor=\'crosshair\'" onClick="javascript:clickRow(' + row + ')">');	
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
					document.write('<td width="' + size +'" class="blue" id="button.' + column + '.' + row + '"');
				} else if (disagreed_alignments_type2Grid[column][row]) {				
					document.write('<td width="' + size +'" class="yellow" id="button.' + column + '.' + row + '"');
				} else if (disagreed_alignments_type3Grid[column][row]) {				
					document.write('<td width="' + size +'" class="red" id="button.' + column + '.' + row + '"');
				} else if(sureGrid[column][row]) {
					document.write('<td width="' + size +'" class="black" id="button.' + column + '.' + row + '"');
				} else if(probGrid[column][row]) {
					document.write('<td width="' + size +'" class="gray" id="button.' + column + '.' + row + '"');
				} else {
					if(highlightedSourceWords[column] || highlightedTargetWords[row]) {
						document.write('<td width="' + size +'" class="highlight" id="button.' + column + '.' + row + '"');
					} else {
						document.write('<td width="' + size +'" class="white" id="button.' + column + '.' + row + '"');
					}
				}
				document.write(' onmouseover="this.style.cursor=\'pointer\'" onClick="javascript:clickButton(' + column + ',' + row + ')">');
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
				document.write('<td align=left bgcolor=#E6E6FA onmouseover="this.style.cursor=\'crosshair\'" onclick="javascript:clickRow(' + row + ')">');	
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
			document.write('\t<td valign=bottom bgcolor=#ADE9E6 onmouseover="this.style.cursor=\'crosshair\'" onclick="javascript:clickColumn(' + i + ')">');
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
			document.write('<a href="javascript:clickColumn(' + i + ')">');
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
			document.write('<span class="blacklink"><a href="javascript:clickRow(' + row + ')">');
			document.write(targetWord);
			document.write('<a href="javascript:clickRow(' + row + ')"></span>');
			if(smallerFont) document.write('</font>');
			document.write('</td>\n\t');
			// print this row
			for(column = sourceWords.length-1; column >= 0; column--) {
				if(sureGrid[column][row]) {
					document.write('<td class="black" id="button.' + column + '.' + row + '">');
				} else if(probGrid[column][row]) {
					document.write('<td class="gray" id="button.' + column + '.' + row + '">');
				} else {
					if(highlightedSourceWords[column] || highlightedTargetWords[row]) {
						document.write('<td class="highlight" id="button.' + column + '.' + row + '">');
					} else {
						document.write('<td class="white" id="button.' + column + '.' + row + '">');
					}
				}
				document.write('<a href="javascript:clickButton(' + column + ',' + row + ')">');
				document.write('<img src= "'+ imageDirectory + 'clearpixel.gif" border="0" ');
				document.write('title="' + targetWords[row] + ', ' + sourceWords[column]+ '" ');
				document.write('width="' + size + '" height="' + size + '"></a>');
				document.write('</td>\n');	
			}
			
			// print the target word again
			document.write('<td>');	
			if(smallerFont) document.write('<font size=' + fontSize + '>');
			document.write('<span class="blacklink"><a href="javascript:clickRow(' + row + ')">');
			document.write(targetWord);
			document.write('<a href="javascript:clickRow(' + row + ')"></span>');
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
			document.write('<a href="javascript:clickColumn(' + i + ')">');
			document.write('<div class=verticaltext-rtl>' + word + '</div>');
			//document.write('<img src = "' + getImagePath(word, imageDirectory) + '" width="' + size + '" alt="' + word + '"' + ' title="' + word + '" border="0">');
			
			document.write('</a>');
			document.write('</td>\n');
		}
		document.write('\t<td></td>\n');
		document.write('</tr>\n');

		document.write('</table>\n');
	}

	function activateUpdateButton() {
		var button = document.getElementById("updatebutton");
		button.disabled=false;
	}
		
	function clickButton(x, y) { 
		activateUpdateButton();
		updateTime();
		button = document.getElementById("button."+x+"."+y);
		if(sureGrid[x][y] == false && probGrid[x][y] == false) {
			sureGrid[x][y] = false;	
			probGrid[x][y] = true;
			button.className = "gray";
		} else {
			if(sureGrid[x][y] == false) {
				sureGrid[x][y] = true;
				probGrid[x][y] = false;
				button.className = "black";
			} else {
				sureGrid[x][y] = false;
				probGrid[x][y] = false;	
				if(sourceHighlights[x] || targetHighlights[y]) {
					button.className = "highlight";
				} else {	
					button.className = "white";
				}
			}
		}
		document.mturk_form.sureAlignments.value = boolGridToString(sureGrid);
		document.mturk_form.possAlignments.value = boolGridToString(probGrid);
	}
	
	
	
	function clickRow(y) { 
		updateTime();
		targetHighlights[y] = (!targetHighlights[y]);
		var x = 0;
		for(x = 0; x < width; x++) {
			var button = document.getElementById("button."+x+"."+y);
			if(sureGrid[x][y] == false && probGrid[x][y] == false) {
				if(sourceHighlights[x] || targetHighlights[y]) {
					button.className = "highlight";
				} else {	
					button.className = "white";
				}
			} 
		}
		document.mturk_form.targetHighlights.value = highlightsToString(targetHighlights);
		document.mturk_form.sourceHighlights.value = highlightsToString(sourceHighlights);
	}
	
	function clickColumn(x) { 
		updateTime();
		sourceHighlights[x] = (!sourceHighlights[x]);
		var y = 0;
		for(y = 0; y < height; y++) {
			var button = document.getElementById("button."+x+"."+y);
			if(sureGrid[x][y] == false && probGrid[x][y] == false) {
				if(sourceHighlights[x] || targetHighlights[y]) {
					button.className = "highlight";
				} else {	
					button.className = "white";
				}
			} 
		}
		document.mturk_form.sourceHighlights.value = highlightsToString(sourceHighlights);
		document.mturk_form.targetHighlights.value = highlightsToString(targetHighlights);
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
	
	// Updates the activeTime and timeOfLastModification
	function updateTime() {
		var date = new Date();
		var currTime = date.getTime();
		var timeElapse = currTime - timeOfLastModification;
		timeOfLastModification = currTime;
		
		// only increment the active time if the time since 
		// the last modification is less than 5 minutes...
		if(timeElapse < 300000) {
			activeTime += timeElapse;
		}
		document.mturk_form.startTime.value = timeAtStart;
		document.mturk_form.endTime.value = timeOfLastModification;
		document.mturk_form.activeTime.value = activeTime;
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
	
	function getsentnum(filename) {
		//alert("ciao");
		var field = document.getElementById("sentnum");
		if (field.value != "") {
			goto('wordaligner.php?filename='+filename+'&sentnum='+ field.value);
		}
	}

