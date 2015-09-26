<?php
function dragoneggs_heidi1(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Heidi's Place");
	output("`c`b`&Heidi, the Well-wisher`b`c`7`n");
	$op2--;
	if($op2==0){
		output("You realize you've finished reading the spell.");
		addnav("Return to Heidi's Place","runmodule.php?module=heidi");
		villagenav();
	}else{
		output("You read a spell");
		if (e_rand(1,6)>4){
			$exp=e_rand(4,9)*$session['user']['level'];
			if ($op2==4) output("and strange lights dance around you");
			elseif ($op2==3) output("and you glimpse the future");
			elseif ($op2==2) output("and your fingers tingle with power");
			else output("and you see what you need to do");
			output("and feel more knowledgable.  You `#gain `^%s`7 experience.",$exp);
			$session['user']['experience']+=$exp;
			debuglog("gained $exp experience by reading a spell while researching dragon eggs at Heidi's Place.");
		}else{
			output("but nothing happens.");
		}
		addnav("Continue Reading the Spell","runmodule.php?module=dragoneggs&op=heidi1&op2=$op2");
	}
}
?>