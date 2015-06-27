<?php
function previewfield($name, $startdiv=false, $talkline="says", $showcharsleft=true, $info=false,$script_output=true) {
	global $schema,$session,$output;
	$talkline = translate_inline($talkline, $schema);
	$youhave = translate_inline("You have ");
	$charsleft = translate_inline(" characters left.");

	$script='';
	
	if ($startdiv === false)
		$startdiv = "";
	$script.="<script language='JavaScript'>
				function previewtext$name(t,l){
					var out = \"<span class=\'colLtWhite\'>".addslashes(appoencode($startdiv))." \";
					var end = '</span>';
					var x=0;
					var y='';
					var z='';
					var max=document.getElementById('input$name');
					var charsleft='';";
	if ($talkline !== false) {
		$script.="	if (t.substr(0,2)=='::'){
						x=2;
						out += '</span><span class=\'colLtWhite\'>';
					}else if (t.substr(0,1)==':'){
						x=1;
						out += '</span><span class=\'colLtWhite\'>';
					}else if (t.substr(0,3)=='/me'){
						x=3;
						out += '</span><span class=\'colLtWhite\'>';";
		if ($session['user']['superuser']&SU_IS_GAMEMASTER) {
			$script.="
					}else if (t.substr(0,5)=='/game'){
						x=5;
						out = '<span class=\'colLtWhite\'>';";
		}
		$script.="	}else{
						out += '</span><span class=\'colDkCyan\'>".addslashes(appoencode($talkline)).", \"</span><span class=\'colLtCyan\'>';
						end += '</span><span class=\'colDkCyan\'>\"';
					}";
	}
	if ($showcharsleft == true) {
		$script.="	if (x!=0) {
						if (max.maxLength!=".getsetting('maxchars',200).") max.maxLength=".getsetting('maxchars',200).";
						l=".getsetting('maxchars',200).";
					} else {
						max.maxLength=l;
					}
					if (l-t.length<0) charsleft +='<span class=\'colLtRed\'>';
					charsleft += '".$youhave."'+(l-t.length)+'".$charsleft."<br>';
					if (l-t.length<0) charsleft +='</span>';
					document.getElementById('charsleft$name').innerHTML=charsleft+'<br/>';";
	}
	$script.="		for (; x < t.length; x++){
						y = t.substr(x,1);
						if (y=='<'){
							out += '&lt;';
							continue;
						}else if(y=='>'){
							out += '&gt;';
							continue;
						}else if (y=='`'){
							if (x < t.length-1){
								z = t.substr(x+1,1);";
		$colors=$output->get_colors();
		$switchscript=datacache("switchscript_comm".rawurlencode($name));
		if (!$switchscript) {
			$switchscript="switch (z) {
								case \"0\": out+='</span>';break;\n";
			foreach ($colors as $key=>$colorcode) {
				$switchscript.="case \"".$key."\": out+='</span><span class=\'".$colorcode."\'>';break;\n";
			}
			$switchscript.="}								
						x++;
						}
					}else{
						out += y;
					}
				}
				document.getElementById(\"previewtext$name\").innerHTML=out+end+'<br/>';
			}
			</script>
			";
			updatedatacache("switchscript_comm".rawurlencode($name),$switchscript);
		}
		$script.=$switchscript;
								
	if ($charsleft == true) {
		$script.="<span id='charsleft$name'></span>";
	}
	if (!is_array($info)) {
		//adding maxchars + a misc overflow which we don't need when javascript is enabled ^^ 100 as failsafe should be enough for a name
		$script.="<input name='$name' id='input$name' maxsize='".(getsetting('maxchars',200)+100)."' onKeyUp='previewtext$name(document.getElementById(\"input$name\").value,".getsetting('maxchars',200).");'>";
	} else {
		if (isset($info['maxlength'])) {
			$l = $info['maxlength'];
		} else {
			$l=getsetting('maxchars',200);
		}
		if (isset($info['type']) && $info['type'] == 'textarea') {
			$script.="<textarea name='$name' id='input$name' onKeyUp='previewtext$name(document.getElementById(\"input$name\").value,$l);' ";
		} else {
			$script.="<input name='$name' id='input$name' onKeyUp='previewtext$name(document.getElementById(\"input$name\").value,$l);' ";
		}
		foreach ($info as $key=>$val){
			$script.="$key='$val'";
		}
		if (isset($info['type']) && $info['type'] == 'textarea') {
			$script.="></textarea>";
		} else {
			$script.=">";
		}
	}
	$add = translate_inline("Add");
	$returnscript=$script."<div id='previewtext$name'></div>";
	$script.="<input type='submit' class='button' value='$add'><br>";
	$script.="<div id='previewtext$name'></div>";
	if ($script_output) rawoutput($script);
	return $returnscript;
}


?>
