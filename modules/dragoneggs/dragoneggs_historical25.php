<?php
function dragoneggs_historical25(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hall of Fame");
	output("`c`b`^The Hall of Fame`b`c`@`n");
	if ($op2=="no"){
		output("`#'I'm sorry Professor,'`@ you explain, `#'but I don't think I can use your help right now.'`@");
		output("`n`nHe shrugs and goes back to looking through the stacks.");
	}else{
		if (is_module_active("dlibrary")){
			if (get_module_setting("ally12","dlibrary")==0){
				set_module_setting("ally12",1,"dlibrary");
				addnews("%s`^ was the first person to meet `&Professor Ottoman`^ at the Hall of Fame.",$session['user']['name']);
			}
		}
		increment_module_pref("dragoneggs",-1,"dragoneggpoints");
		output("You hand over the dragon egg point and the Professor shakes your hand vigorously. `&'Good show! Let's go save the kingdom!,'`@ he says.  You suddenly realize that he's going to stick around for a while!");
		if (isset($session['bufflist']['ally'])) {
			if ($session['bufflist']['ally']['type']=="ottoman"){
				$ally=1;
			}else{
				output("`n`nRealizing that you've found help from someone new, %s`@ decides to leave.",$session['bufflist']['ally']['name']);
				$ally=0;
			}
		}else $ally=0;
		if ($ally==0){
			apply_buff('ally',array(
				"name"=>translate_inline("`&Professor Ottoman"),
				"rounds"=>100,
				"wearoff"=>translate_inline("`&The Professor decides to go back to his studies.`0"),
				"defmod"=>1.02,
				"atkmod"=>1.02,
				"survivenewday"=>1,
				"type"=>"ottoman",
			));
			output("`n`nYou gain the help of `&Professor Ottoman`@!");
			debuglog("gained the help of ally Professor Ottoman for a dragon egg point by researching at the Hall of Fame.");
		}else{
			$session['bufflist']['ally']['rounds'] += 33;
			output("`n`n`&Professor Ottoman`@ decides to help you out for another `^33 rounds`@!");
			debuglog("gained the help of ally Professor Ottoman for an additional 33 rounds for a dragon egg point by researching at the Hall of Fame.");
		}
	}
	addnav("Return to Hall of Fame","hof.php");
	villagenav();
}
?>