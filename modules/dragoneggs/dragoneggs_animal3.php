<?php
function dragoneggs_animal3(){
	global $session;
	page_header("Merick's Stables");
	output("`c`b`^Merick's Stables`b`c`7`n");
	$chance=0;
	if (e_rand(1,2)==1) $chance++;
	if (($session['user']['level']>4 && e_rand(1,3)==1) || ($session['user']['level']<5 && e_rand(1,4)==1)) $chance++;
	if ($chance==2){
		output("You find a spell book!");
		output("`n`n`@You Advance in your Specialty`&.");
		require_once("lib/increment_specialty.php");
		increment_specialty("`@");
		debuglog("increment specialty by researching at Merick's Stables.");
	}elseif ($chance==1){
		output("You find the diary of a warrior from long ago and start to read it:");
		output("`c`n`i`#...isn't careful will die a painful death.  However, a cautious warrior remembers these important gems...`i`c`n");
		output("`7You `%find a gem`7!!");
		$session['user']['gems']++;
		output("Unfortunately, you get a nasty papercut from one of the pages and `\$lose 1/2 your hitpoints`7.");
		$session['user']['hitpoints']*=.5;
		debuglog("lost 1/2 hitpoints but gained a gem while researching dragon eggs at Merick's Stables.");
	}else{
		output("You read a very disturbing passage.  You're `iCursed`i and you `%lose a gem`7!!");
		if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
			$session['bufflist']['blesscurse']['rounds'] += 5;
		}else{
			if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
				$session['bufflist']['blesscurse']['rounds'] += 5;
				debuglog("increased their curse by 5 rounds and lost a gem by researching at Merick's Stables.");
			}else{
				apply_buff('blesscurse',
					array("name"=>translate_inline("Cursed"),
						"rounds"=>15,
						"survivenewday"=>1,
						"wearoff"=>translate_inline("The burst of energy passes."),
						"atkmod"=>0.8,
						"defmod"=>0.9,
						"roundmsg"=>translate_inline("Dark Energy flows through you!"),
					)
				);
				debuglog("was cursed and lost a gem by researching at Merick's Stables.");
			}
		}
		$session['user']['gems']--;
	}
	addnav("Return to Merick's Stables","stables.php");
	villagenav();
}
?>