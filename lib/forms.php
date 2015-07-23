<?php
function previewfield($name, $startdiv=false, $talkline="says", $showcharsleft=true, $info=false, $default=false, $jsec=false, $ucol=false, $focus=false) {
	global $schema,$session,$output,$chatsonpage;
	$talkline = translate_inline($talkline, $schema);
	$youhave = translate_inline("You have ");
	$charsleft = translate_inline(" characters left.");
	$chatsonpage+=1;
	
	//figure out whether to handle absolute or relative time
	if (!array_key_exists('commentary_autocomplete', $session['user']['prefs'])){
		$session['user']['prefs']['commentary_autocomplete'] = 0;
	}
	
	if ($session['user']['prefs']['commentary_autocomplete']){
		$autocomplete = "";
	} else {
		$autocomplete = "autocomplete='off'";
	}
	
	if (!$jsec){
		$nid = $name.$chatsonpage;
	} else {
		$nid = $jsec;
	}
	//debug($nid);
	
	if ($ucol!==false){
		$exclude = array("J");
		$colors = getcolors($exclude);
		$usercol = $colors[$ucol];
	} else {
		$usercol = "colLtCyan";
	}
	
	if ($startdiv === false)
		$startdiv = "";
	rawoutput("<script language='JavaScript'>
				function previewtext$nid(t,l){
					var out = \"<span class=\\'colLtWhite\\'>".addslashes(appoencode($startdiv))."\";
					var end = '</span>';
					var x=0;
					var y='';
					var z='';
					var max=document.getElementById('input$nid');
					var charsleft='';");
	if ($talkline !== false) {
		rawoutput("	if (t.substr(0,2)=='::'){
						x=2;
					}else if (t.substr(0,1)==':'){
						x=1;
					}else if (t.substr(0,3)=='/me'){
						x=3;");
		if ($session['user']['superuser']&SU_IS_GAMEMASTER) {
			rawoutput("
					}else if (t.substr(0,5)=='/game'){
						x=5;");
		}
		rawoutput("	}else{
						out += '</span><span class=\\'colDkCyan\\'> ".addslashes(appoencode($talkline))." \"</span><span class=\\'$usercol\\'>';
						end += '</span><span class=\\'colDkCyan\\'>\"';
					}");
	}
	if ($showcharsleft == true) {
		rawoutput("	if (x!=0) {
						if (max.maxLength!=255) max.maxLength=255;
						l=255;
					} else {
						max.maxLength=l;
					}
					if (l-t.length<0) charsleft +='<span class=\\'colLtRed\\'>';
					charsleft += '".$youhave."'+(l-t.length)+'".$charsleft."<br>';
					if (l-t.length<0) charsleft +='</span>';
					italics=0;
					document.getElementById('charsleft$nid').innerHTML=charsleft+'<br/>';");
	}
	rawoutput("		for (; x < t.length; x++){
						y = t.substr(x,1);
						if (y=='<'){
							out += '&lt;';
							continue;
						}else if(y=='>'){
							out += '&gt;';
							continue;
						}else if(y=='\\n'){
							out += '<br />';
							continue;
						}else if (y=='`'){
							if (x < t.length-1){
								z = t.substr(x+1,1);
								if (z=='0'){
									out += '</span>';
								}else if (z=='1'){
									out += '</span><span class=\\'colDkBlue\\'>';
								}else if (z=='2'){
									out += '</span><span class=\\'colDkGreen\\'>';
								}else if (z=='3'){
									out += '</span><span class=\\'colDkCyan\\'>';
								}else if (z=='4'){
									out += '</span><span class=\\'colDkRed\\'>';
								}else if (z=='5'){
									out += '</span><span class=\\'colDkMagenta\\'>';
								}else if (z=='6'){
									out += '</span><span class=\\'colDkYellow\\'>';
								}else if (z=='7'){
									out += '</span><span class=\\'colDkWhite\\'>';
								}else if (z=='q'){
									out += '</span><span class=\\'colDkOrange\\'>';
								}else if (z=='!'){
									out += '</span><span class=\\'colLtBlue\\'>';
								}else if (z=='@'){
									out += '</span><span class=\\'colLtGreen\\'>';
								}else if (z=='#'){
									out += '</span><span class=\\'colLtCyan\\'>';
								}else if (z=='$'){
									out += '</span><span class=\\'colLtRed\\'>';
								}else if (z=='%'){
									out += '</span><span class=\\'colLtMagenta\\'>';
								}else if (z=='^'){
									out += '</span><span class=\\'colLtYellow\\'>';
								}else if (z=='&'){
									out += '</span><span class=\\'colLtWhite\\'>';
								}else if (z=='Q'){
									out += '</span><span class=\\'colLtOrange\\'>';
								}else if (z==')'){
									out += '</span><span class=\\'colLtBlack\\'>';
								}else if (z=='r'){
									out += '</span><span class=\\'colRose\\'>';
								}else if (z=='R'){
									out += '</span><span class=\\'colRose\\'>';
								}else if (z=='v'){
									out += '</span><span class=\\'coliceviolet\\'>';
								}else if (z=='V'){
									out += '</span><span class=\\'colBlueViolet\\'>';
								}else if (z=='g'){
									out += '</span><span class=\\'colXLtGreen\\'>';
								}else if (z=='G'){
									out += '</span><span class=\\'colXLtGreen\\'>';
								}else if (z=='T'){
									out += '</span><span class=\\'colDkBrown\\'>';
								}else if (z=='t'){
									out += '</span><span class=\\'colLtBrown\\'>';
								}else if (z=='~'){
									out += '</span><span class=\\'colBlack\\'>';
								}else if (z=='j'){
									out += '</span><span class=\\'colMdGrey\\'>';
								}else if (z=='e'){
									out += '</span><span class=\\'colDkRust\\'>';
								}else if (z=='E'){
									out += '</span><span class=\\'colLtRust\\'>';
								}else if (z=='l'){
									out += '</span><span class=\\'colDkLinkBlue\\'>';
								}else if (z=='L'){
									out += '</span><span class=\\'colLtLinkBlue\\'>';
								}else if (z=='x'){
									out += '</span><span class=\\'colburlywood\\'>';
								}else if (z=='X'){
									out += '</span><span class=\\'colbeige\\'>';
								}else if (z=='y'){
									out += '</span><span class=\\'colkhaki\\'>';
								}else if (z=='Y'){
									out += '</span><span class=\\'coldarkkhaki\\'>';
								}else if (z=='k'){
									out += '</span><span class=\\'colaquamarine\\'>';
								}else if (z=='K'){
									out += '</span><span class=\\'coldarkseagreen\\'>';
								}else if (z=='p'){
									out += '</span><span class=\\'collightsalmon\\'>';
								}else if (z=='P'){
									out += '</span><span class=\\'colsalmon\\'>';
								}else if (z=='m'){
									out += '</span><span class=\\'colwheat\\'>';
								}else if (z=='M'){
									out += '</span><span class=\\'coltan\\'>';
								}
								else if (z=='i'){
									italics += 1;
									inum = italics;
									if (inum%2){
										out += '<i>';
									} else {
										out += '</i>';
									}
								}
								x++;
							}
						}else{
							out += y;
						}
					}
					document.getElementById(\"previewtext$nid\").innerHTML=out+end+'<br/>';
				}</script>
				");
	if ($charsleft == true) {
		rawoutput("<span id='charsleft$nid'></span>");
	}
	if (!is_array($info)) {
		if ($default) {
			rawoutput("<input name='$name' id='input$nid' ".$autocomplete." maxlength='255' onKeyUp='previewtext$nid(document.getElementById(\"input$nid\").value,255);' value='$default'>");
		} else {
			rawoutput("<input name='$name' id='input$nid' ".$autocomplete." maxlength='255' onKeyUp='previewtext$nid(document.getElementById(\"input$nid\").value,255);'>");
		}
	} else {
		if (isset($info['maxlength'])) {
			$l = $info['maxlength'];
		} else {
			$l=255;
		}
		if (isset($info['type']) && $info['type'] == 'textarea') {
			rawoutput("<textarea name='$name' id='input$nid' onKeyUp='previewtext$nid(document.getElementById(\"input$nid\").value,$l);' ");
		} else {
			rawoutput("<input name='$name' id='input$nid' ".$autocomplete." onKeyUp='previewtext$nid(document.getElementById(\"input$nid\").value,$l);' ");
		}
		foreach ($info as $key=>$val){
			rawoutput("$key='$val'");
		}
		if (isset($info['type']) && $info['type'] == 'textarea') {
			rawoutput(">");
			if ($default) {
				rawoutput($default);
			}
			rawoutput("</textarea>");
		} else {
			if ($default) {
				rawoutput(" value='$default'>");
			} else {
				rawoutput(">");
			}
		}
	}
	rawoutput("<div id='previewtext$nid'></div>");
	if ($focus){
		rawoutput("<script language='javascript'>document.getElementById('input".$nid."').focus();</script>");
	}
}

function previewfield_countup($name,$maxchars=false,$default=""){
	global $session;
	$id = $name;
	$exclude = array("J");
	$colors = getcolors($exclude);
	rawoutput("<script language='JavaScript'>
				function previewtext$id(t){
					var out = '';
					var end = '';
					var x=0;
					var y='';
					var z='';
					var charsleft = (t.length)+' Characters';
					italics=0;
					bold=0;
					document.getElementById('charsleft$id').innerHTML=charsleft;
					if (t.length==0){
						out = '&nbsp';
					}
					for (; x < t.length; x++){
						y = t.substr(x,1);
						teststring = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()-=_+;:,./?`~ \'\"[]{}\\\|<>�';
						testpos = teststring.indexOf(y);
						if (y=='<'){
							out += '&lt;';
							continue;
						}else if(y=='>'){
							out += '&gt;';
							continue;
						}else if(y=='\\n'){
							out = '<span class=\\'colLtRed\\'><strong>Use `n rather than Enter to drop down a line</strong></span>';
							continue;
						}else if (y=='`'){
							if (x < t.length-1){
								z = t.substr(x+1,1);
								if (z=='0'){
									out += '</span>';");
	foreach($colors AS $code=>$class){
		rawoutput("				}else if (z=='".$code."'){
									out += '</span><span class=\\'".$class."\\'>';");
	}
	rawoutput("
								}
								else if (z=='i'){
									italics += 1;
									inum = italics;
									if (inum%2){
										out += '<em>';
									} else {
										out += '</em>';
									}
								} else if (z=='n'){
									out += '<br />';
								} else if (z=='b'){
									bold += 1;
									bnum = bold;
									if (bnum%2){
										out += '<strong>';
									} else {
										out += '</strong>';
									}
								} else if (z=='`'){
									out += '`';
								} else {
									out = '<span class=\\'colLtRed\\'><strong>That\'s not a colour code you can use here!</strong></span>';
								}
								x++;
							}
						} else if (testpos==-1){
							out = '<span class=\\'colLtRed\\'><strong>Did you type this in a word processor?  There\'s a character in there that\'s going to cause problems.  It could be a smart quote, or a single-character ellipsis, or some wacky hyphen.  You can only use characters that you can see on your keyboard.  Please check and try again.</strong></span>';
						} else {
							out += y;
						}
					}
					document.getElementById(\"previewtext$id\").innerHTML=out;
				}</script>
				");
	rawoutput("<div id='previewtext$id'>&nbsp;</div>");
	rawoutput("<span id='charsleft$id'>0 Characters</span><br />");
	rawoutput("<textarea name='$name' id='input$id' cols='60' rows='6' onKeyUp='previewtext$id(document.getElementById(\"input$id\").value);'>".$default."</textarea>");
}

?>