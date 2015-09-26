<?php
function dragoneggs_heidi25(){
	global $session;
	page_header("Heidi's Place");
	$hps=$session['user']['hitpoints'];
	$level=$session['user']['level'];
	output("`\$`c`b~ ~ ~ Fight ~ ~ ~`b`c`n`@You have encountered `^Very Large Man`@ preparing for an `iArm Wrestling Match`i with a `%Huge Arm`@!");
	output("`n`n`2Level: `6%s`n`2`bStart of round:`b`nVery Large Man's Hitpoints: `6%s`2`nYOUR Hitpoints: `6%s`^`n",$level,$hps+1,$hps);
	output("`bVery Large Man`\$ gets ready and puts his arm before yours...`b`n`n");
	if ($session['user']['hitpoints']>=$session['user']['maxhitpoints']){
		output("`^Very Large Man`4 hits pulls you for `\$%s`4 points of damage!",$hps-1);
		output("`n`n`7You feel like your arm is almost yanked off! You lose definitively and `\$lose all your hitpoints except 1`7.");
		$session['user']['hitpoints']=1;
		output("`n`n`#'Ah, it was a worthy battle,'`7 the Very Large Man says. `#'Would you like some help on your adventures?'`7 he asks.");
		if (isset($session['bufflist']['ally'])) {
			if ($session['bufflist']['ally']['type']=="sirtarascon"){
				$ally=1;
			}else{
				output("`n`nRealizing that you've found help from someone new, %s`7 decides to leave.",$session['bufflist']['ally']['name']);
				$ally=0;
			}
		}else $ally=0;
		if ($ally==0){
			apply_buff('ally',array(
				"name"=>translate_inline("`#Sir Tarascon"),
				"rounds"=>25,
				"wearoff"=>translate_inline("`#Sir Tarascon explains that there are others that need his help and takes his leave."),
				"atkmod"=>1.4,
				"defmod"=>.9,
				"survivenewday"=>1,
				"type"=>"sirtarascon",
			));
			output("`n`nYou gain the help of `#Sir Tarascon`7!");
			debuglog("gained the help of ally Sir Tarascon and lost all hitpoints except 1 by researching at Heidi's Place.");
		}else{
			$session['bufflist']['ally']['rounds'] += 8;
			output("`n`n`#Sir Tarascon`7 decides to help you out for another `^8 rounds`7!");
			debuglog("gained the help of ally Sir Tarascon for an additional 8 rounds and lost all hitpoints except 1 by researching at Heidi's Place.");
		}
		if (is_module_active("dlibrary")){
			if (get_module_setting("ally8","dlibrary")==0){
				set_module_setting("ally8",1,"dlibrary");
				addnews("%s`^ was the first person to meet `#Sir Tarascon`^ at Heidi's Place.",$session['user']['name']);
			}
		}
	}else{
		output("`^Very Large Man`4 tries to yank your arm off you but you `^PULL BACK`4 for `^%s `4points of damage!`n`n",$hps+e_rand(1,3));
		output("`7You quickly defeat the Very Large Man and suddenly wonder if perhaps he let you win.");
		output("He leaves the shop without saying a word.");
	}
	addnav("Return to Heidi's Place","runmodule.php?module=heidi");
	villagenav();
}
?>