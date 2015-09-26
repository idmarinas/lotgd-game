<?php
function dragoneggs_gypsy25(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Gypsy Seer's Graveyard");
	output("`c`b`3Gypsy Seer's Graveyard`b`c`5`n");
	$session['user']['experience']-=$op2;
	if (isset($session['bufflist']['ally'])) {
		if ($session['bufflist']['ally']['type']=="graveyardgreg"){
			$ally=1;
		}else{
			output("Realizing that you've found help from someone new, %s`5 decides to leave.`n`n",$session['bufflist']['ally']['name']);
			$ally=0;
		}
	}else $ally=0;
	if ($ally==0){
		apply_buff('ally',array(
			"name"=>translate_inline("`)Graveyard Greg"),
			"rounds"=>60,
			"survivenewday"=>1,
			"startmsg"=>translate_inline("Greg grabs his shovel and gets ready for some batting practice."),
			"wearoff"=>translate_inline("Greg needs to get back to work at the Graveyard."),
			"minioncount"=>1,
			"effectmsg"=>translate_inline("`5Greg swings his shovel at {badguy} and causes `^{damage}`5 damage."),
			"effectnodmgmsg"=>translate_inline("{badguy} `5narrowly dodges Greg's Shovel."),
			"effectfailmsg"=>translate_inline("{badguy} `5narrowly dodges Greg's Shovel."),
			"minbadguydamage"=>0,
			"maxbadguydamage"=>$session['user']['level'],
			"type"=>"graveyardgreg",
		));
		output("You give some of your experience to `)Graveyard Greg`5 and he agrees to accompany you for a while.");
		debuglog("gave $op2 experience to get Graveyard Greg to join as an ally while researching dragon eggs at the Gypsy Seer's Tent.");
	}else{
		$session['bufflist']['ally']['rounds'] += 20;
		output("You give some of your experience to `)Graveyard Greg`5 and he agrees to accompany you for another `^20 rounds`5.");
		debuglog("gave $op2 experience to get Graveyard Greg to join as an ally for another 20 rounds while researching dragon eggs at the Gypsy Seer's Tent.");
	}
	if (is_module_active("dlibrary")){
		if (get_module_setting("ally5","dlibrary")==0){
			set_module_setting("ally5",1,"dlibrary");
			addnews("%s`^ was the first person to meet `)Graveyard Greg`^ at the Gypsy's Graveyard.",$session['user']['name']);
		}
	}
	debuglog("gave $op2 experience to get Graveyard Greg to join as an ally while researching dragon eggs at the Gypsy Seer's Tent.");
	addnav("Return to the Gypsy Seer's Tent","gypsy.php");
	villagenav();
}
?>