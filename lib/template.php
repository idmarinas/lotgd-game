<?php
// translator ready
// addnews ready
// mail ready

function templatereplace($itemname,$vals=false){
	global $template;
	if (!isset($template[$itemname]))
		output("`bWarning:`b The `i%s`i template part was not found!`n", $itemname);
	$out = $template[$itemname];
	if (!is_array($vals)) return $out;
	foreach ($vals as $key=>$val) {
		if (strpos($out,"{".$key."}")===false){
			output("`bWarning:`b the `i%s`i piece was not found in the `i%s`i te".
					"mplate part! (%s)`n", $key, $itemname, $out);
			$out .= $val;
		}else{
			$out = str_replace("{"."$key"."}",$val,$out);
		}
	}
	return $out;
}

function prepare_template($force=false){
	if (!$force) {
		if (defined("TEMPLATE_IS_PREPARED")) return;
		define("TEMPLATE_IS_PREPARED",true);
	}
 	
 	global $templatename, $templatemessage, $template, $session, $y, $z, $y2, $z2, $copyright, $lc, $x, $templatetags,$_defaultskin;
	 if (!isset($_COOKIE['template'])) $_COOKIE['template']="";
	$templatename="";
	$templatemessage="";
	if ($_COOKIE['template']!="")
		$templatename=$_COOKIE['template'];
	if ($templatename=="" || !file_exists("templates/$templatename"))
		$templatename=getsetting("defaultskin", $_defaultskin);
	if ($templatename=="" || !file_exists("templates/$templatename"))
		$templatename=$_defaultskin;
	$template = loadtemplate($templatename);
	if (isset ($session['templatename']) && $session['templatename'] == $templatename &&
			$session['templatemtime']==filemtime("templates/$templatename")){
		//We do not have to check that the template is valid since it has
		//not changed.
	}else{
		//We need to double check that the template is valid since the name
		// or file mod time have changed.

		//tags that must appear in the header
		$templatetags=array("title","headscript","script");
		foreach ($templatetags as $val) {
			if (strpos($template['header'],"{".$val."}")===false && $val)
				$templatemessage .=
					"You do not have {".$val."} defined in your header\n";
		}
		//tags that must appear in the footer
		$templatetags=array();
		foreach ($templatetags as $val) {
			if (strpos($template['footer'],"{".$val."}")===false && $val)
				$templatemessage .=
					"You do not have {".$val."} defined in your footer\n";
		}

		//tags that may appear anywhere but must appear
		$templatetags=array("nav","stats","petition","motd","mail",
				"paypal","source","version", "copyright");
		foreach ($templatetags as $key=>$val) {
			if (!$key) array_push($templatetags,$y2^$z2);
			if (strpos($template['header'],"{".$val."}")===false &&
					strpos($template['footer'],"{".$val."}")===false && $val)
				$templatemessage .=
					"You do not have {".$val."} defined in either your header or footer\n";
		}
		if ($templatemessage==""){
			$session['templatename'] = $templatename;
			$session['templatemtime'] = filemtime("templates/$templatename");
		}
	}
	if ($templatemessage!=""){
		echo "<b>You have one or more errors in your template page!</b><br>".nl2br($templatemessage);
		$template=loadtemplate("yarbrough.htm");
	}else {
		$y = 0;
		$z = $y2^$z2;
		if ($session['user']['loggedin'] && $x > ''){
	// I am sick of the hash stuff ... that does not seem to work :-/
	//	$$z = $x;
		}
		$$z = $lc . $$z . "<br />";
	}

}
?>
