<?php
function dragoneggs_armor5(){
	global $session;
	page_header("Pegasus Armor");
	output("`c`b`%Pegasus Armor`b`c`5");
	output("Figuring that it's worth the try, you give `%2 gems`5 to `#Pegasus`5.");
	$chance=e_rand(1,8);
	if ($session['user']['gems']==2 && $chance==8) $chance=7;
	if ($chance<=5){
		output("`n`nShe thanks you and hands you a piece of paper with a magic spell on it.  You read it...");
		output("`n`nYou Advance in your Specialty!");
		require_once("lib/increment_specialty.php");
		increment_specialty("`5");
		$session['user']['gems']-=2;
		debuglog("incremented specialty for 2 gems while researching dragon eggs at Pegasus Armor.");
	}elseif ($chance<=7){
		output("She thanks you and hands you `%2 gems`5 back. `#'See! I taught you that sometimes you don't lose and you don't win. Isn't that great?'");
	}else{
		output("She thanks you and hands you back `%1 gem`5.  `#'See! I taught you that sometimes you can't win!'");
		$session['user']['gems']--;
		debuglog("lost a gem while researching dragon eggs at Pegasus Armor.");
	}
	addnav("Return to Pegasus Armor","armor.php");
	villagenav();
}
?>