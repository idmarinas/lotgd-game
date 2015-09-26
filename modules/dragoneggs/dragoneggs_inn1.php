<?php
function dragoneggs_inn1(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
		$innname=getsetting("innname", LOCATION_INN);
	}else{
		$innname=translate_inline("The Boar's Head Inn");
	}
	page_header("%s",$innname);
	rawoutput("<span style='color: #9900FF'>");
	output("`c`b`0%s`b`c`0",$innname);
	if ($op2>1){
		output("`#'I would be honored to join your organization,'`0 you tell him`n`n");
		output("You give your `%%s gems`0 and he hands you a `&Letter of Invitation`0 to the `&Order of the Inner Sanctum`0.",$op2);
		output("`n`n`&'You'll want to stop by at the Order before the day is up.  The invitation is good for today only.  Membership will last until you kill the Green Dragon or if I catch you acting inappropriately in the forest.'");
		output("`n`nHe shakes your hand, gives you directions to where the Inner Sanctum is in %s, and sends you on your way.",get_module_setting("sanctumloc","sanctum"));
		$session['user']['gems']-=$op2;
		set_module_pref("member",-1,"sanctum");
		debuglog("paid $op2 gems to join the Order of the Inner Sanctum by researching at the Boar's Head Inn.");
	}else output("`#'No thank you, I don't want to join your secret society.'`0 you tell him.  He shrugs and walks away.");
	addnav(array("Return to %s",$innname),"inn.php");
	villagenav();
}
?>