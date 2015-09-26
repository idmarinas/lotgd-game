<?php
function dragoneggs_gardens25(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("The Gardens");
	output("`c`b`2The Gardens`b`c`7`n`n");
	output("You agree to the deal and give the `&Dragon Egg Token`7 to the strange man.`n`n");
	if ($op2==1){
		output("You take the `^500 gold`7 and receive `@2 turns`7.");
		$session['user']['gold']+=500;
		$session['user']['turns']+=2;
		debuglog("traded 1 dragon egg point for 500 gold and 2 turns while researching in the Gardens.");
	}elseif ($op2==2){
		output("You gain `\$1 permanent hitpoint`7 and receive `@1 turn`7.");
		$session['user']['turns']++;
		$session['user']['maxhitpoints']++;
		debuglog("traded 1 dragon egg point for 1 turn and 1 permanent hitpoint while researching in the Gardens.");
	}elseif ($op2==3){
		output("You gain a `%gem`7 and gain `@3 turn`7.");
		$session['user']['gems']++;
		$session['user']['turns']+=3;
		debuglog("traded 1 dragon egg point for 1 gem and 3 turns while researching in the Gardens.");
	}elseif ($op2==4){
		output("You gain an`& attack point`7.");
		$session['user']['attack']++;
		debuglog("traded 1 dragon egg point for 1 attack while researching in the Gardens.");
	}else{
		output("You receive a `^Retainer`7.");
		set_module_pref("retainer",2);
		debuglog("traded 1 dragon egg point for a retainer while researching in the Gardens.");
		rawoutput("<small><small>");
		output("`c`^Notes on Retainers`c");
		output("Retainers are a nice cushion for those lucky enough to obtain one.`n`n");
		output("If you get one, then once a system day you may receive a small stipend. If you have a lucky day, it will be more than the standard amount. On an unlucky day, you won't get anything.  If it's a REALLY bad day, you'll lose the retainer.");
		rawoutput("<big><big>");
	}
	increment_module_pref("dragoneggs",-1,"dragoneggpoints");
	addnav("Return to the Gardens","gardens.php");
	villagenav();
}
?>