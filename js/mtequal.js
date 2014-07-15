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

function fadeOut (el,i) {
	el.style.cursor="normal";
	if (el.style.backgroundColor != "red") {
		el.style.backgroundColor=getColor(i);	
	} 
}

function fadeIn (el) {
	el.style.cursor="pointer";
	if (el.style.backgroundColor != "red") {
		el.style.backgroundColor="#999";
	} 
}

function duplicateAnnotation (obj, curruserid) {
	item = obj.options[obj.selectedIndex].value
	if (confirm("Do you really want to import the annotation from another user?")) {
		window.open("admin.php?userid="+curruserid+"&copy="+item,"_top");
  	} else {
  		obj.options[0].selected = 'selected';
  	}
}

function check(id,target_id,user_id,check,checkid,totcheck) {
	//alert(id+","+target_id+", user_id:"+user_id+", check:"+check+", checkid:"+checkid+", totcheck:"+totcheck);
	var radioEl = document.getElementById("check."+checkid+"."+check);
	var action="";
    if (radioEl.style.backgroundColor == "red") {
    	check=-1;
    	action="remove";
    }
    			
    $.ajax({
  		url: 'update.php',
  		type: 'GET',
      	data: "id="+id+"&targetid="+target_id+"&userid="+user_id+"&check="+check+"&action="+action,
  		async: false,
  		cache:false,
  		crossDomain: true,
  		success: function(response) {
  			if (response != "1") {
  				//alert("update.php?id="+id+"&targetid="+target_id+"&userid="+user_id+"&check="+check+"&action="+action);
  			
  			
  				alert(response + " Sorry but an error occured saving data. Try again, please!");
  			} else {
  				//controllo se sono stati attivati almeno un radio per ogni check, se cos`i attivo il bottone DONE! 
				for(var c=selectionFrom; c<=selectionTo; c++) {
    				var checked = 0;
      				radioEl = document.getElementById("check."+checkid+"."+c);
      				if (c == check) {	  	  			
						radioEl.style.backgroundColor = "red";
					} else {
						radioEl.style.backgroundColor=getColor(c);
					}
  				}
  			}
  		},
  		error: function(response, xhr,err ) {
        	//alert(err+"\nreadyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\nresponseText: "+xhr.responseText);
        	switch(xhr.status) {
				case 200: 
					alert("Data saved!");
			}
		}
  	});
   	
  	
  	//controllo se attivare bottone done se c'e` almeno un assegnamento per ogni sistema
 	var checked = 0;
 	for(var i=1; i<=totcheck; i++) {
   		for(var c=selectionFrom; c<=selectionTo; c++) {
   			radioEl = document.getElementById("check."+i+"."+c);
   			if (radioEl != null && radioEl.style.backgroundColor == "red") {
   				checked++;
   				break;
   			} else {
   				resetEl = document.getElementById("reset."+i);
   				if (resetEl != null) {
   					checked++;
   					break;
   				}
   			}
   		}
 	}
 	
 	if (checked == totcheck) {
		activateDone(0);
  	} else {
  		notDoneYet();
    }
}

function reset(id,targetid,userid,errid,sentidx) {
  //alert(id+","+targetid+","+errid);
  if (confirm("Do you really want to cancel all the annotations in this error category?")) {
	$.ajax({
  		url: 'update.php',
  		type: 'GET',
      	data: "id="+id+"&targetid="+targetid+"&userid="+userid+"&check="+errid+"&action=reset",
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
	window.open("errors.php?id="+id+"&sentidx="+sentidx,"_top");
                        
  	//$("#errors"+targetid).html("<table cellspacing=4><tr><td style='background: #ccc; border: solid #444 1px; font-size:13px' id='check."+targetid+".0' align=center onmouseover='fadeIn(this);'  onmouseout='fadeOut(this,0);' onClick=\"check('"+id+"','"+targetid+"',"+userid+",0,1,2);\" nowrap>No errors</td></tr><tr><td style='background: #ccc; border: solid #444 1px; font-size:13px' id='check."+targetid+".1' align=center onmouseover='fadeIn(this);'  onmouseout='fadeOut(this,1);' onClick=\"check('"+id+"','"+targetid+"',"+userid+",1,2,2);\">Too many errors</td><tr></table>");
  }	
}

function next(page) {
	window.open(page,'_top');
}

function notDoneYet()  {
	doneEl = document.getElementById("done");
 	if (doneEl != null) {
 		doneEl.innerHTML='Not completed yet.';
 		doneEl.style.background='#ddd';
 		doneEl.disabled=true;
 		doneEl.style.color='#666';
 	}
}

function alreadyDone()  {
	doneEl = document.getElementById("done");
 	if (doneEl != null) {
 		doneEl.innerHTML='&nbsp;&nbsp;&nbsp;&nbsp;DONE!&nbsp;&nbsp;&nbsp;&nbsp;';
 		doneEl.style.background='lightgreen';
 		doneEl.disabled=true;
 		doneEl.style.color='black';
 	}
}

function activateDone(monitoring) {
	doneEl = document.getElementById("done");
 	if (doneEl != null) {
 		doneEl.innerHTML='&nbsp;&nbsp;&nbsp;&nbsp;Done?&nbsp;&nbsp;&nbsp;&nbsp;';
 		
 		if (monitoring==0) {
 			doneEl.disabled=false;
 		}
 		doneEl.style.background='lightyellow';
 		doneEl.style.color='black';
 	}
}

function doneAndIndex(id,user_id,button) {
	done(id,user_id,button);
	//window.open('index.php#'+id,'_top');
}

function done(id,user_id,button) {
	button.disabled=true;
   	$.ajax({
 		url: 'update.php',
  		type: 'GET',
      	data: "id="+id+"&userid="+user_id+"&completed=Y",
  		async: false,
  		cache:false,
  		crossDomain: true,
  		success: function(response) {
  			if (response != 1) {
  				alert("Sorry but an error occured saving data. Try again, please!");
  			} else {
  				alreadyDone();
  			}
  		},
  		error: function(response, xhr,err ) {
        	//alert(err+"\nreadyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\nresponseText: "+xhr.responseText);
        	//alert(ERRORID);
        	switch(xhr.status) {
				case 200: 
					alert("Data saved!");
			}
   		}
	});
    	
}

function getObject(name) {
    if (document.getElementById) {
        //alert (document.getElementById(name).);
        return document.getElementById(name);
    } else if (document.getElementsByTagName) {
        var elements = document.getElementsByTagName("*");
        for(i=0; i < elements.length; i++)
            if(elements.item(i).getAttribute("name") == name || elements.item(i).getAttribute("id") == name)
                return elements.item(i);

    } else if (document.layers[name]) {
        // NN 4 DOM.. note: this won't find nested layers
        return document.layers[objectId];
    } else if (document.all) {
        return document.all[name];
        //return eval ('document.all.'+name);
    }
    return null;
}

function moveObject( obj, e ) {
    // step 1
    var tempX = 0;
    var tempY = 0;
    var offset = 2;

    // step 2
    obj = getObject( obj );

    if (obj==null) return;

    // step 3
    if (document.all) {
        tempX = event.clientX + document.body.scrollLeft;
        tempY = event.clientY + document.body.scrollTop;
    } else {
        tempX = e.pageX;
        tempY = e.pageY;
    }

    // step 4
    if (tempX < 0){tempX = 0}
    if (tempY < 0){tempY = 0}

    // step 5
    obj.style.top  = (tempY + offset) + 'px';
    obj.style.left = (tempX + offset) + 'px';
    obj.style.visibility = 'visible'; //hidden
    //obj.style.display = 'block';   //none

}

function hideErrorMenu() {
    var obj = getObject('errortypes');
    //obj.style.visibility = 'hidden';
    obj.style.display = 'none';
}

function showErrorMenu() {
	var obj = getObject('errortypes');
    obj.style.visibility = 'hidden';
}

function resetAction(id) {
	var i = 1;
	var el = document.getElementById(id+"."+i);
	var foundRed=0;
	while (el != null) {
    	if (el.style.backgroundColor != "") {
    		foundRed=1;
    		break;
    	}
    	el = document.getElementById(id+"."+i+"-"+(i+1));
    	if (el != null && el.style.backgroundColor != "") {
    		foundRed=1;
    		break;
    	}
    	i++;
    	el = document.getElementById(id+"."+i);
    }		
    if (foundRed == 0) {
    	$("#errortypes").html("<table bgcolor=lightyellow><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='img/bullet_error.png'> No selection!&nbsp;</td></table>");
    	return 1;
    }
    return 0;
}	
	
					
function showAction(id,down,up,send,event) {	
	if (resetAction(id) == 0) {
		var eventHeader="onmouseover=\"this.className='highlight'\" onmouseout=\"this.className='whitebg'\"";
		var eventAttrs="onmouseover=\"this.className='yellow'\" onmouseout=\"this.className='whitebg'\"";
	$("#errortypes").html("<table width=200 border=0 cellspacing=0 cellpadding=2 style='background-color: #ccc; color: #000; font-size: 16px; box-shadow: 3px 3px 3px #888888; '><tr><td onclick=\"javascript:saveAnnotationRanges('"+id+"','"+down+"','"+up+"',true,'2');\" "+eventAttrs+">Reordering errors</td></tr><tr><td onclick=\"javascript:saveAnnotationRanges('"+id+"','"+down+"','"+up+"',true,'3');\" "+eventAttrs+">Lexicon errors</td></tr><tr><td onclick=\"javascript:saveAnnotationRanges('"+id+"','"+down+"','"+up+"',true,'4');\" "+eventAttrs+">Missing word(s)</td></tr><tr><td onclick=\"javascript:saveAnnotationRanges('"+id+"','"+down+"','"+up+"',true,'5');\" "+eventAttrs+">Morphology errors</td></tr><tr><td nowrap onclick=\"javascript:saveAnnotationRanges('"+id+"','"+down+"','"+up+"',true,'6');\" "+eventAttrs+">Casing and punctuation errors</td></tr><tr><td nowrap onclick=\"javascript:saveAnnotationRanges('"+id+"','"+down+"','"+up+"',true,'7');\" "+eventAttrs+">Superfluous</td></tr></table>");
	}
	moveObject('errortypes',event);
    /*var obj = getObject('errortypes');
   
    if (obj) {
        obj.style.visibility = 'visible';
    }*/
    return false;
}

