<?php
function dragoneggs_jewelry21(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Oliver, the Jeweler");
	output("`c`b`&Oliver's Jewelry`b`c`7`n");
	if ($op2==1){
		if ($session['user']['turns']>1 && e_rand(1,3)==1){
			$session['user']['turns']-=2;
			output("You spend `@2 turns`7 listening to his rambling and act like it isn't that big of a deal.");
			output("`n`nHe seems impressed with your fortitude and offers to join you on your adventures.");

			if (isset($session['bufflist']['ally'])) {
				if ($session['bufflist']['ally']['type']=="boulder"){
					$ally=1;
				}else{
					output("`n`nRealizing that you've found help from someone new, %s`7 decides to leave.",$session['bufflist']['ally']['name']);
					$ally=0;
				}
			}else $ally=0;
			if ($ally==0){
				apply_buff('ally',array(
					"name"=>translate_inline("`%Andy Arrow"),
					"rounds"=>60,
					"survivenewday"=>1,
					"startmsg"=>translate_inline("Andy gets ready to tell a REALLY long and disjointed story."),
					"wearoff"=>translate_inline("Andy finishes his story and leaves."),
					"minioncount"=>1,
					"effectmsg"=>translate_inline("`5Andy relates his story to {badguy}`5 and causes outright confusion... {badguy}`5 stumbles and loses `^{damage}`5 hitpoints."),
					"effectnodmgmsg"=>translate_inline("{badguy} `5doesn't hear the story."),
					"effectfailmsg"=>translate_inline("{badguy} `5doesn't hear the story."),
					"minbadguydamage"=>0,
					"maxbadguydamage"=>e_rand(1,round($session['user']['level']/4))+2,
				));
				output("`n`nYou gain the help of `%Andy Arrow`7!");
				debuglog("lost 2 turns to recruit Andy Arrow while researching dragon eggs at the Jeweler.");
			}else{
				$session['bufflist']['ally']['rounds'] += 20;
				output("`n`n`%Andy Arrow`7 decides to help you out for another `^20 rounds`7!");
				debuglog("gained the help of ally Andy Arrow for an additional 20 rounds at the cost of 2 turns while researching dragon eggs at the Jeweler.");
			}
			if (is_module_active("dlibrary")){
				if (get_module_setting("ally6","dlibrary")==0){
					set_module_setting("ally6",1,"dlibrary");
					addnews("%s`^ was the first person to meet `%Andy Arrow`^ at the Jeweler.",$session['user']['name']);
				}
			}
		}else{
			output("You egg him on, but he tells you that there's not enough time to REALLY explain what happened.  He leaves you with a dissapointed look on his face.");
		}
	}else{
		output("You decide you've got more important things to do than listen to this blowhard!");
	}
	addnav("Return to Oliver's Jewelry","runmodule.php?module=jeweler");
	villagenav();
}
?>