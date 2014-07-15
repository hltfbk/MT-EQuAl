var down = null;
var WHITE = "";
//var COLOR = WHITE;
var COLOR = "red";
var ERRORID="<?php echo $errorid; ?>";
var ERRORCOLOR = "red";

/* Default value of choice */
var selectionFrom = 0;
var selectionTo = 1;

function getColor (idx) {
	return "#ccc";
}

function showRange(el,sentid,range) {
	el.style.borderBottom = "solid #000 2px";
	el.style.cursor = "nw-resize";
	var ids = range.split(" ");
	for (var i in ids) {
    	var el = document.getElementById(sentid+"."+ids[i]);
    	if (el != null) {
    		el.style.borderBottom = "solid #000 2px";
    	}
    }
}

function hideRange(el,sentid,range) {
	el.style.borderBottom = "none";
	el.style.cursor = "default";
	var ids = range.split(" ");
	for (var i in ids) {
    	var el = document.getElementById(sentid+"."+ids[i]);
    	if (el != null) {
    		el.style.borderBottom = "none";
    	}
    }
}

function save_comment(id, comment) {
	id = id.replace(/^comm/," ");
	//alert("Saving.. " + id+" "+comment); //entities
	$.ajax({
		url: 'update.php',
  		type: 'GET',
		data: "id="+id+"&userid=<?php echo $userid;?>&comment="+comment,
  		async: false,
  		cache:false,
  		crossDomain: true
  	});
  	return true;
}


function removeAnnotation(id,targetid,ranges,errid) {
  //alert(id+","+ranges);
  if (confirm("Do you really want to cancel this annotation?")) {
	$.ajax({
  		url: 'update.php',
  		type: 'GET',
      	data: "id="+id+"&targetid="+targetid+"&userid=<?php echo $userid;?>&check="+errid+"&action=remove&tokenids="+ranges,
  		async: false,
  		cache:false,
  		crossDomain: true,
  		success: function(response) {
  			//alert(id+","+target_id+","+check);
  		},
  		error: function(response, xhr,err ) {
        	//alert(err+"\nreadyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\nresponseText: "+xhr.responseText);
        	switch(xhr.status) {
				case 200: 
					alert("Data saved!");
			}
		}
  	});
	window.open("errors.php?id="+id+"&sentidx=<?php echo $sentidx; ?>","_self");
                        
  }	
}

$(document).ready(function() {
	try {
		$(document).bind("contextmenu", function(e) {
			e.preventDefault();
			$("#errortypes").css({ top: (e.pageY-2) + "px", left: (e.pageX-20) + "px" }).show(100);
		});
		$(document).mouseup(function(e) {
			var container = $("#errortypes");
			if (container.has(e.target).length == 0) {
				container.hide();
				//container.show();
				//hideErrorMenu();
				//alert(container.has(e.target).length);
			}
			//showErrorMenu();
		});
	} catch (err) {
		alert(err);
	}
});


$('div').mousedown( function () {
	if (this.id != "") {
		down = this.id;
	}
	//$("#log").html(this.id);
});
	

/*$('div').mouseover( function () {
	if (this.id != "") {
		$("#log").html(this.id);
	}
});*/


$('div').mouseup( function (event) {
	<?php if ($monitoring==1) {print "return -1;";} ?>
    
	if (this.id != "") {
		if (this.id.indexOf("error") == 0 || this.id.indexOf("comm") == 0) {
			return;
		}
		
		//if ((ERRORID == "" || ERRORID < 2)) {
		//	alert("Select an error type from the menu, please!");
		//	return;
		//}
		
		var up = this.id;
		//$("#log").html("SET " +down + " " +up);
		if (down != null && up != null) {
			var changed;
			var sentid = down.replace(/\..+/g,"");
			var sentidup = up.replace(/\..+/g,"");
			
 			//check if the annotation is done within a sentence
			if (sentid != sentidup) {
				return;
			}
			
			down = down.replace(/.+\./g,"");
			up = up.replace(/.+\./g,"");
			
			if (parseInt(down) >= parseInt(up)) {
				var tmpdown = up;
				up = down;
				down = tmpdown;
			}
			
			changed = selectTokens(sentid, down, up); 
    		if (changed) {
    			showAction(sentid,down,up,true,event);
			} else {
				resetAction(sentid);	
    		}
	    	clearSelectedText();
    		down = null;
    		up = null;
		}
    }
});
    
function cleanBgColor (sentid) {
	i=1;
	while (true) {
		var el = document.getElementById(sentid+"."+i);
    	if (el != null) {
    		el.style.backgroundColor = WHITE;
    	}
    	i++;	
	}
}

function selectTokens (sentid, down, up) {
    color = null;
    var changed = false;
	var askedconfirm = false;
	//check the spaces
	if (down.indexOf("-") >0 && down==up) {
		var el = document.getElementById(sentid+"."+down);
		if (el != null) {
			//alert(down+" "+up+ " --- " +changed);
			if (el.style.backgroundColor == COLOR) {
				el.style.backgroundColor = WHITE;
			} else {
				el.style.backgroundColor = COLOR;
				changed = true;
			}
		}
	} else {
		
	    for (var i=parseInt(down); i<=parseInt(up); i++) {
    		var el = document.getElementById(sentid+"."+i);
    		//alert(i + " " + up+"/"+down +"  " +el);
    		if (el != null) {
    			if (COLOR == WHITE && el.style.backgroundColor == COLOR) {
    				continue;
    			}
    			changed = true;
    			if (color == null) {
    				if (el.style.backgroundColor == COLOR) {
    					if (!askedconfirm) {
    						askedconfirm = true;
							/*if (confirm("ATTENTION! Do you want to REMOVE this annotation?")) {
								//continue;
							} else {
								return false;
							}*/
						}
						color = WHITE;
    				} else {
    					color = COLOR;
    				}
	    		}
    			//alert(i + " (" +el.style.backgroundColor +") " + color);
    			
    			el.style.backgroundColor = color;
    			//el.style.borderBottom = "0px solid #000";   	
    		}
    		if (i != up || color == WHITE) {
    			el = document.getElementById(sentid+"."+i+"-"+(parseInt(i)+1));
    			if (el != null) {
	    			el.style.backgroundColor = color;
    				//el.style.borderBottom = "0px solid #000";   		
    			}  	
    		}
    	}
    	if (color==WHITE) {
			el = document.getElementById(sentid+"."+(parseInt(down)-1)+"-"+down);
    		if (el != null) {
    			el.style.backgroundColor = color;   		
    		} 
	    }
	}
	
	return changed;
}

function clearSelectedText() {
	if (window.getSelection) {
		window.getSelection().removeAllRanges();
	} else if (document.getSelection) {
		document.getSelection().empty();
	} else {
		var selection = document.selection && document.selection.createRange();
		if (selection.text) {
			selection.collapse(true);
			selection.select();
		}
	}
}

function saveAnnotationRanges(index, down, up, send, errid) {
	//alert(index+","+down+","+up+","+send);
	ERRORID = errid;
	if (ERRORID == "") {
		ERRORID=errid;
 	//	$("#log").html('<font color=red>Select an error category from the menu.</font>');     			
 	//	return;
 	}
 	var elid = 1;
	var ranges = "";
	var entities = "";
	var prevcolor = null;
	while(true) {
		el = document.getElementById(index+"."+elid);
		if (el == null) {
			break;
		} else {
			if (el.style.backgroundColor != WHITE) {
				//alert(el.id +" "+ el.innerHTML + " " +el.style.backgroundColor);			
				if (prevcolor == null || prevcolor != el.style.backgroundColor) {
					//ranges += "("+getType(prevcolor)+"),";
					ranges += ","+elid;
					//entities += " ("+getType(prevcolor)+")__BR__";
					entities += "__BR__"+el.innerHTML;
				} else {
					ranges += " "+elid;
					entities += " " +el.innerHTML;
				}
			}
			prevcolor = el.style.backgroundColor;					
		}
		
		//check spaces
		el = document.getElementById(index+"."+elid+"-"+(elid+ 1));
		if (el != null) {
			if (el.style.backgroundColor != WHITE) {
				if (prevcolor== null || prevcolor != el.style.backgroundColor) {
					//ranges += "("+getType(prevcolor)+"),";
					ranges += ","+elid+"-"+(elid+ 1);
					//entities += " ("+getType(prevcolor)+")__BR__";
					entities += "__BR___SPACE_";
				} else {
					ranges += " "+elid+"-"+(elid+ 1);
					entities += " ";
				}							
			}
			prevcolor = el.style.backgroundColor;	
		}
		
    	elid++;	
	}
	
	ranges = ranges.replace(/^,\s*/,"");
	entities = entities.replace(/^__BR__\s*/, "");
	
	var errorlabel = "<?php echo $errorlabel ?>";
	//alert ("errorlabel: " + errorlabel + "\nranges: " + ranges + "\nentities: " + entities);


 if (send) { // && trim(ranges) != "") { 		
 	$.ajax({
  url: 'update.php',
  type: 'GET',
  data: "id=<?php echo $id;?>&targetid="+index+"&userid=<?php echo $userid;?>&check="+ERRORID+"&words="+entities.replace(/&nbsp;/gi," ")+"&tokenids="+ranges,
  async: false,
  cache:false,
  crossDomain: true,
  success: function(response) {
  	if (response == "error") {
  		//$("#log").html("");
  		alert("Warning! A problem occurred during saving the data. Try again later!");
  	} else {
		//update list of annotated tokens		
		if (entities.length > 0) {
			window.open("errors.php?id=<?php echo $id;?>&sentidx=<?php echo $sentidx; ?>","_self");
			//$("#errors"+index).html("<div style=' background-color: #dedede;'>&nbsp;<small><i><b>"+errorlabel+":</b></i></small> <button id=reset name=reset onclick=\"javascript:reset('<?php echo $id;?>','"+index+"','<?php echo $userid; ?>',"+ERRORID+");\">reset</button></div><table border=0 bgcolor=#eee style='font-size: 13px' cellspacing=0 cellpadding=1><tr><td>" + entities.replace(/<br>/g, "\\n").replace(/__BR__/g, "</td></tr><tr><td>") + "</td></tr></table>");
			
		} else {
			$("#errors"+index).html("<table cellspacing=4><tr><td style='background: #ccc; border: solid #444 1px; font-size:13px' id='check."+index+".0' align=center onmouseover='fadeIn(this);'  onmouseout='fadeOut(this,0);' onClick=\"check('<?php echo $userid;?>','"+index+"',<?php echo $userid;?>,0,0,2);\" nowrap>No errors</td></tr><tr><td style='background: #ccc; border: solid #444 1px; font-size:13px' id='check."+index+".1' align=center onmouseover='fadeIn(this);'  onmouseout='fadeOut(this,1);' onClick=\"check('<?php echo $userid;?>','"+index+"',<?php echo $userid;?>,1,1,2);\">Too many errors</td><tr></table>");
		}		
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
ERRORID = "";
}

function getType (color) {
	if (color == ERRORCOLOR) {
		return ERRORID;
	} 
	return color;
}

function setColor(selectel, index, id)  {
	var color = selectel.value;
    if (selectel.id == "corefcolor" || selectel == "corefcolor") {
    	ERRORID = id;
    	COLOR = ERRORCOLOR;
    	color = COLOR;
    	//$("#color").prop("selectedIndex",0);
    } else {
    	//ERRORID = selectel.options[selectel.options.selectedIndex].text;
    	//ERRORID = ERRORID.substr(0,3).toUpperCase();
    	ERROR = null;
    	COLOR = color;
    	//$("#corefcolor").prop("selectedIndex",0);
    }
}

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
						elDone = document.getElementById("done");
						elDone.style.disabled=false;
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
