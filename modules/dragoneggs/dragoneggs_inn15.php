<?php
function dragoneggs_inn15(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
		$innname=getsetting("innname", LOCATION_INN);
		$barkeep = getsetting('barkeep','`tCedrik');
		
	}else{
		$innname=translate_inline("The Boar's Head Inn");
		$barkeep =translate_inline("`%Cedrik");
	}
	page_header("%s",$innname);
	rawoutput("<span style='color: #9900FF'>");
	$hps=$session['user']['hitpoints'];
	$level=$session['user']['level'];
	output("`\$`c`b~ ~ ~ Fight ~ ~ ~`b`c`n`@You have encountered `^Intruder`@ which lunges at you with `%Crow Bar`@!");
	output("`n`n`2Level: `6%s`n`2`bStart of round:`b`nIntruder's Hitpoints: `6%s`2`nYOUR Hitpoints: `6%s`^`n",$level,$hps+12,$hps);
	output("`bIntruder`\$ surprises you and gets the first round of attack!`b`n`n");
	$chance=e_rand(1,5);
	if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
		output("`^Intruder`4 tries to hit you but you `^RIPOSTE`4 for `^%s `4points of damage!`n",$hps+11);
		output("`b`&You disarm her and she looks at you with a glimmer of admiration.`n");
		output("`\$You have defeated the Intruder!`n`n`b");
		output("`1'My name is Emily Ratcliff. I have to admit I admire your skills. What is your story?'`0 she asks.");
		output("`n`nFiguring that it may be worth your effort, you tell her about your quest to destroy all the Dragon Eggs.");
		output("She contemplates the issues carefully. `1'It would be an honor if I can fight by your side,'`0 she says as she picks up her crowbar.");
		if (isset($session['bufflist']['ally'])) {
			if ($session['bufflist']['ally']['type']=="emilyratcliff"){
				$ally=1;
			}else{
				output("`n`nRealizing that you've found help from someone new, %s`0 decides to leave.",$session['bufflist']['ally']['name']);
				$ally=0;
			}
		}else $ally=0;
		if ($ally==0){
			apply_buff('ally',array(
				"name"=>translate_inline("`1Emily Ratcliff"),
				"rounds"=>50,
				"wearoff"=>translate_inline("`1Emily retreats into the shadows to search for her own adventures."),
				"defmod"=>1.05,
				"survivenewday"=>1,
				"type"=>"emilyratcliff",
			));
			output("`n`nYou gain the help of `1Emily Ratcliff`0!");
			debuglog("gained the help of ally Emily Ratcliff by researching at the Boar's Head Inn.");
		}else{
			$session['bufflist']['ally']['rounds'] += 16;
			output("`n`n`1Emily Ratcliff`0 decides to help you out for another `^16 rounds`0!");
			debuglog("gained the help of ally Emily Ratcliff for an additional 16 rounds by researching at the Boar's Head Inn.");
		}
		if (is_module_active("dlibrary")){
			if (get_module_setting("ally1","dlibrary")==0){
				set_module_setting("ally1",1,"dlibrary");
				addnews("%s`^ was the first person to meet `1Emily Radcliff`^ at the Boar's Head Inn.",$session['user']['name']);
			}
		}
	}else{
		if ($hps>1) output("`^Intruder`4 hits you for `\$%s`4 points of damage!`n",$hps-1);
		else output("`^Intruder`4 is about to hit you when you pass out.`n");
		output("`&Just before the intruder has a chance to hit you again, %s`& barges in and chases her away.",$barkeep);
		output("`n`n%s`0 thanks you for trying to help out and gives you a healing elixir that restores all your hitpoints to full.",$barkeep);
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		debuglog("had hitpoints restored to full by researching at the Boar's Head Inn.");
	}
	addnav(array("Return to %s",$innname),"inn.php");
	villagenav();
}
?>