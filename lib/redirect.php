<?php
// translator ready
// addnews ready
// mail ready
function redirect($location,$reason=false){
	global $session,$REQUEST_URI;
	// This function is deliberately not localized.  It is meant as error
	// handling.
	if (strpos($location,"badnav.php")===false) {
		//deliberately html in translations so admins can personalize this, also in one schema
		$session['allowednavs']=array();
		addnav("",$location);
		$failoutput=new output_collector();
		$failoutput->output_notl("`lWhoops, your navigation is broken. Hopefully we can restore it.`n`n");
		$failoutput->output_notl("`\$");
		$failoutput->rawoutput("<a href=\"".HTMLEntities($location, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">".translate_inline("Click here to continue.","badnav")."</a>");
		$failoutput->output_notl(translate_inline("`n`n`\$If you cannot leave this page, notify the staff via <a href='petition.php'>petition</a> `\$and tell them where this happened and what you did. Thanks.","badnav"),true);
		$text=$failoutput->get_output();
		$session['output']="<html><head><link href=\"templates/common/colors.css\" rel=\"stylesheet\" type=\"text/css\"></head><body style='background-color: #000000'>$text</body></html>";
	}
	restore_buff_fields();
	$session['debug'].="Redirected to $location from $REQUEST_URI.  $reason<br>";
	saveuser();
	//header("Location: $location");
	$host  = $_SERVER['HTTP_HOST'];
	if ($_SERVER['SERVER_PORT']==443) $http="https";
		else $http="http";
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	header("Location: $http://$host$uri/$location");
	// obsolete
	//echo "<html><head><meta http-equiv='refresh' content='0;url=$location'></head></html>";
	//echo "<a href='$location'>$location</a><br><br>";
	//$session['debug']="$http://$host$uri/$location";
	//echo $location;
	
	// we should never hit this one here. in case we do, show the debug output along with some text
	// this might be the case if your php session handling is messed up or something. 
	echo translate_inline("Whoops. There has been an error concering redirecting your to your new page. Please inform the admins about this. More Information for your petition down below:\n\n");
	echo $session['debug'];
	exit();
}
?>
