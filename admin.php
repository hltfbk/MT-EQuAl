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

include("config.php");
include("functions.php");
header("Content-type: text/html; charset=utf-8");
?>

<head>
<title>MT-EQuAl: Administration page</title>
<link href="css/mtequal.css" rel="styleSheet" type="text/css">

<script type="text/javascript" src="js/alertify/alertify.min.js"></script>
<link rel="stylesheet" href="js/alertify/alertify.core.css" />
<link rel="stylesheet" href="js/alertify/alertify.default.css" />

<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/mtequal.js"></script>
<script>
alertify.set({ buttonFocus: "cancel" });
	
function showUpload ($taskid, $type) {
	el = document.getElementById("uploadpane");
	if (el != null) {
		el.style.visibility='visible';
		form = document.getElementById("uploadform");
    	if (form != null) {
    		form.taskid.value=$taskid;
    		form.type.value=$type;    
    	}
	}
}
function hideUpload () {
	el = document.getElementById("uploadpane");
	if (el != null) {
		el.style.visibility='hidden';
    }
}
function delTask (taskid,type) {
	alertify.alert("WARNING! You are about to removing all information about this task, including the annotations of ALL THE USERS.");
	alertify.confirm("Do you really want to REMOVE this task, and both all sentences and all annotations joint to it?", function (e) {
        if (e) {
			window.open("admin.php?section=task&action=remove&id="+taskid, "_self");
		}
	});	
}

function delUser (userid) {
	alert("WARNING! You are about to removing all information about this user, including own annotations.");
	alertify.confirm("Do you really want to remove this user?", function (e) {
        if (e) {
        	window.open("admin.php?section=user&action=remove&id="+userid, "_self");
		}
	});	
}
function delSentences(taskid,type) {
	if (type=="source") {
		alert("WARNING! You are removing SOURCE sentences, so the alignment with reference and output ones could be not guaranteed any more.");
	}
	alertify.confirm("Do you really want to delete all "+type+" sentences of this task?", function (e) {
        if (e) {
        	window.open("admin.php?section=data&action=remove&taskid="+taskid+"&type="+type, "_self");
		}
	});	
}

function delAnnotations(taskid, userid, username) {
	alertify.confirm("Do you really want to delete the "+username+"'s annotations on the task "+taskid+"?", function (e) {
        if (e) {
        	window.open("admin.php?section=annotation&action=remove&taskid="+taskid+"&userid="+userid, "_self");
		}
	});	
}

function duplicateAnnotation (obj, curruserid) {
	item = obj.options[obj.selectedIndex].value
	alertify.confirm("Do you really want to import the annotation from another user?", function (e) {
        if (e) {
        	window.open("admin.php?userid="+curruserid+"&copy="+item,"_top");
  		} else {
  			obj.options[0].selected = 'selected';
  		}
  	});
}

function showSpinner() {
	$('.spinner').show();
	setTimeout("$('.spinner').hide()",28000);          	
}

$(".ziplink").click(function (e) {
    e.preventDefault();
    var _self = $(this);
	/*$.ajax({
       	type : 'HEAD',
       	url : _self.attr('href')       
    });*/
    alert("DONE!");
});
</script>

<style>
#tabs {
            overflow: hidden;
            width: 100%;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        #tabs li {
            cursor: pointer;
            float: left;
            margin: 0 .8em 0 0;
        }

        #tabs a {
       		position: relative;
            background: #ddd;
            background-image: -webkit-gradient(linear, left top, left bottom, from(#eee), to(#ddd));
            background-image: -webkit-linear-gradient(top, #eee, #ddd);
            background-image: -moz-linear-gradient(top, #eee, #ddd);
            background-image: -ms-linear-gradient(top, #eee, #ddd);
            background-image: -o-linear-gradient(top, #eee, #ddd);
            background-image: linear-gradient(to bottom, #eee, #ddd);
            padding: 5px 5px;
            float: left;
            text-decoration: none;
            color: #444;
            text-shadow: 0 1px 0 rgba(255,255,255,.8);
            -webkit-border-radius: 5px 0 0 0;
            -moz-border-radius: 5px 0 0 0;
            border-radius: 5px 0 0 0;
            -moz-box-shadow: 0 2px 2px rgba(0,0,0,.4);
            -webkit-box-shadow: 0 2px 2px rgba(0,0,0,.4);
            box-shadow: 0 2px 2px rgba(0,0,0,.4);
        }

        #tabs a:hover,
        #tabs a:hover::after,
        #tabs a:focus::after {
            background: #fff;
        }

        #tabs a:focus {
            outline: 0;
        }

        #tabs a::after {
            content:'';
            position:absolute;
            z-index: 1;
            top: 0;
            right: -.5em;
            bottom: 0;
            width: 12px;
            background: #ddd;
            background-image: -webkit-gradient(linear, left top, left bottom, from(#eee), to(#ddd));
            background-image: -webkit-linear-gradient(top, #eee, #ddd);
            background-image: -moz-linear-gradient(top, #eee, #ddd);
            background-image: -ms-linear-gradient(top, #eee, #ddd);
            background-image: -o-linear-gradient(top, #eee, #ddd);
            background-image: linear-gradient(to bottom, #eee, #ddd);
            -moz-box-shadow: 2px 2px 2px rgba(0,0,0,.4);
            -webkit-box-shadow: 2px 2px 2px rgba(0,0,0,.4);
            box-shadow: 2px 2px 2px rgba(0,0,0,.4);
            -webkit-transform: skew(10deg);
            -moz-transform: skew(10deg);
            -ms-transform: skew(10deg);
            -o-transform: skew(10deg);
            transform: skew(10deg);
            -webkit-border-radius: 0 5px 0 0;
            -moz-border-radius: 0 5px 0 0;
            border-radius: 0 5px 0 0;
        }

        div.selected {
       		cursor: default;
            position: relative;
            background: #ddd;
            background-image: -webkit-gradient(linear, left top, left bottom, from(#ddd), to(#5c0120));
            background-image: -webkit-linear-gradient(top, #ddd, #5c0120);
            background-image: -moz-linear-gradient(top, #ddd, #5c0120);
            background-image: -ms-linear-gradient(top, #ddd, #5c0120);
            background-image: -o-linear-gradient(top, #ddd, #5c0120);
            background-image: linear-gradient(to bottom, #ddd, #5c0120);
            padding: 5px 5px;
            float: left;
            text-decoration: none;
            color: #ff9;
            text-shadow: 0 1px 0 rgba(255,255,255,.8);
            -webkit-border-radius: 5px 0 0 0;
            -moz-border-radius: 5px 0 0 0;
            border-radius: 5px 0 0 0;
            -moz-box-shadow: 0 2px 2px rgba(0,0,0,.4);
            -webkit-box-shadow: 0 2px 2px rgba(0,0,0,.4);
            box-shadow: 0 2px 2px rgba(0,0,0,.4);
        }
        div.selected::after {
            content:'';
            position:absolute;
            z-index: 1;
            top: 0;
            right: -.5em;
            bottom: 0;
            width: 12px;
            background-image: -webkit-gradient(linear, left top, left bottom, from(#ddd), to(#5c0120));
            background-image: -webkit-linear-gradient(top, #ddd, #5c0120);
            background-image: -moz-linear-gradient(top, #ddd, #5c0120);
            background-image: -ms-linear-gradient(top, #ddd, #5c0120);
            background-image: -o-linear-gradient(top, #ddd, #5c0120);
            background-image: linear-gradient(to bottom, #ddd, #5c0120);
            -moz-box-shadow: 2px 2px 2px rgba(0,0,0,.4);
            -webkit-box-shadow: 2px 2px 2px rgba(0,0,0,.4);
            box-shadow: 2px 2px 2px rgba(0,0,0,.4);
            -webkit-transform: skew(10deg);
            -moz-transform: skew(10deg);
            -ms-transform: skew(10deg);
            -o-transform: skew(10deg);
            transform: skew(10deg);
            -webkit-border-radius: 0 5px 0 0;
            -moz-border-radius: 0 5px 0 0;
            border-radius: 0 5px 0 0;
        }

        #tabs #current a {
    	 	background: #fff;
            z-index: 999;
        }

        #tabs #current a::after {
            background: #fff;
            z-index: 999;
        }
        
        #content {
        	width: 100%;
            background: #fff;
            padding: 2px;
            border: 2px solid #5c0120;
            position: relative;
            z-index: 2;
            -moz-border-radius: 0 5px 5px 5px;
            -webkit-border-radius: 0 5px 5px 5px;
            border-radius: 0 5px 5px 5px;
            -moz-box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
            -webkit-box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
            box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
        }
</style>
</head>
<body>


<?php
include("menu.php");
if (isset($mysession) && !empty($mysession["status"]) && $mysession["status"] == "admin") {
	$panels = array("TASK","DATA","USER","ANNOTATION","STATS & AGREEMENT");
	
	$current_pane="task";
	if (isset($section)) {
		$current_pane=$section;
	}
?>

<div class=index><div class=row><div style='margin: 10px; display: inline-block'>
<ul id="tabs">
<?php
	foreach ($panels as $pane) {
		if (strtolower($pane) == $current_pane) {
	 		print"<li><div class=selected name=\"$pane\"><i>&nbsp;$pane&nbsp;</i></div>\n";
		} else {
		 	print"<li><a href=\"admin.php?section=".strtolower(preg_replace("/ .*/","",$pane))."\" name=\"$pane\"><i>&nbsp;$pane&nbsp;</i></a>\n";
		}
	}
?>
</ul>

<?php
	print "<div id='content'>\n";
	include("admin_".$current_pane.".php");
	print "</div>";
}
?>
</body>
</html>