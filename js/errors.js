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

var down = null;
var WHITE = "";
//var COLOR = WHITE;
var COLOR = "red";


$('div').mousedown( function () {
	if (this.id != "" && this.id.indexOf(".") > 0) {
		down = this.id;
	}
});
    
$('div').mouseup( function (event) {
	if (event.which==3 || (event.ctrlKey && event.which==1)) {
		if (OUTPUTID != null && isSelected(OUTPUTID) == 1) {
			moveObject('errortypes',event); 
			$("#errortypes").show(100);
			return false;
		}
	}
	
	if (this.id != "") {
	  if (this.id.indexOf(".") > 0) {
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
    			showAction(sentid, event);
			} 
	    	clearSelectedText();
    		down = null;
    		up = null;
		}
	  }
    }
});


function showAction(id, event) {	
	if (OUTPUTID != null && OUTPUTID != id) {
		cleanBgColor(OUTPUTID);
	}
	OUTPUTID = id;
			
    //moveObject('errortypes',event); 
    return false;
}

function cleanBgColor (sentid) {
	i=1;
	while (true) {
		var el = document.getElementById(sentid+"."+i);
    	if (el != null) {
    		el.style.backgroundColor = WHITE;
    	} else {
    		break;
    	}
    	//check spaces
		el = document.getElementById(sentid+"."+i+"-"+(i+ 1));
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
			//alert(down+" "+up+ " --- " +el.style.backgroundColor+ " COLOR:" +COLOR+ " " +(el.style.backgroundColor == COLOR));
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
