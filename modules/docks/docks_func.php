<?php
function docks_expeditionnav(){
	addnav("Deep Sea Fishing");
	$op = httpget('op');
	$fishingtoday=get_module_pref("fishingtoday");
	if ($fishingtoday<5){
		$fishingleft=5-$fishingtoday;
		$op3=get_module_pref("quality");
		addnav("More Fishing","runmodule.php?module=docks&op=$op&op2=fish&op3=$op3");
		addnav("Check Gauges","runmodule.php?module=docks&op=$op&op2=gauge&op3=$op3");
		output("`n`n`c`@Fishing Turns Left: `^%s`c",$fishingleft);
	}else{
		output("`n`n`c`\$No Fishing Turns Left`7`c");
		set_module_pref("bait",0);
	}
	addnav("Move the Ship","runmodule.php?module=docks&op=$op&loc=".get_module_pref("pqtemp"));
}
function docks_baitnav(){
	page_header("Bait and Tackle Shop");
	addnav("Bait Shop");
	addnav("Chat with Hoglin","runmodule.php?module=docks&op=docks&op2=fishchat");
	addnav("Fishing Poles","runmodule.php?module=docks&op=docks&op2=fishpoles");
	addnav("Bait","runmodule.php?module=docks&op=docks&op2=fishbait");
	if (get_module_pref("fishbook")==0) addnav("Fishing Books","runmodule.php?module=docks&op=docks&op2=fishbooks");
	else addnav("Read Your Fishing Book","runmodule.php?module=docks&op=docks&op2=readfishbook&op3=store");
	addnav("Notices","runmodule.php?module=docks&op=docks&op2=fishnotices");
	addnav("Biggest Fish Caught","runmodule.php?module=docks&op=docks&op2=bigfish");
	addnav("Most Fish by Weight","runmodule.php?module=docks&op=docks&op2=fishweight");
	addnav("Most Fish by Number","runmodule.php?module=docks&op=docks&op2=numberfish");
	addnav("Docks");
	addnav("Return to the Docks","runmodule.php?module=docks&op=docks&op2=enter");
}
function docks_noticenav(){
	addnav("Notices");
	addnav("For Sale - Used Fish","runmodule.php?module=docks&op=docks&op2=forsale&op3=usedfish");
	addnav("For Sale - New Fish","runmodule.php?module=docks&op=docks&op2=forsale&op3=newfish");
	addnav("For Sale - Stick with String","runmodule.php?module=docks&op=docks&op2=forsale&op3=stickstring");
	addnav("Wanted - Used Fish","runmodule.php?module=docks&op=docks&op2=wanted&op3=usedfish");
	addnav("Job Available #1","runmodule.php?module=docks&op=docks&op2=jobavailable&op3=1");
	addnav("Job Available #2","runmodule.php?module=docks&op=docks&op2=jobavailable&op3=2");
	addnav("Job Available #3","runmodule.php?module=docks&op=docks&op2=jobavailable&op3=3");
	addnav("Job Available #4","runmodule.php?module=docks&op=docks&op2=jobavailable&op3=4");
	addnav("Job Available #5","runmodule.php?module=docks&op=docks&op2=jobavailable&op3=5");
}

function docks_fight($op) {
	$temp=get_module_pref("pqtemp");
	page_header("Fight");
	global $session,$badguy;
	$op2 = httpget('op2');

	if ($op=='fishermanfight'){
		$name=translate_inline("A Burly Fisherman");
		$weapon=translate_inline("Flying Fists");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$session['user']['level'],
			"creatureweapon"=>$weapon,
			"creatureattack"=>$session['user']['attack']*.9,
			"creaturedefense"=>$session['user']['defense']*.9,
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.1),
			"diddamage"=>0,
			"type"=>"burly",
		);
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=='fishcrew'){
		$name=translate_inline("a Beefy Fisherman");
		$weapon=translate_inline("Beefy Fists");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$session['user']['level'],
			"creatureweapon"=>$weapon,
			"creatureattack"=>$session['user']['defense'],
			"creaturedefense"=>$session['user']['attack'],
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.1),
			"diddamage"=>0,
			"type"=>"beefy",
		);
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=='fishshark'){
		$name=translate_inline("a shark that you're reeling in");
		$weapon=translate_inline("strength against you");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>1,
			"creatureweapon"=>$weapon,
			"creatureattack"=>1,
			"creaturedefense"=>1,
			"creaturehealth"=>2000,
			"diddamage"=>0,
			"type"=>"fishshark",
		);
		$session['user']['badguy']=createstring($badguy);
		$op="fight";
	}
	if ($op=="fight"){
		global $badguy;
		$battle=true;
		$fight=true;
		if ($battle){
			require_once("battle.php");
			if ($victory){
				if($badguy['creaturename']=="A Burly Fisherman"){
					$expmultiply = e_rand(10,20);
					$expbonus=$session['user']['dragonkills']*2;
					$expgain =($session['user']['level']*$expmultiply+$expbonus);
					$session['user']['experience']+=$expgain;
					output("`n`@You decide leaving him beaten to a pulp is good enough for you.`n");
					output("`n`#You have gained `7%s `#experience.`n",$expgain);
					addnav("Dock Fishing");
					$fishingtoday=get_module_pref("fishingtoday");
					if ($fishingtoday<5){
						$fishingleft=5-$fishingtoday;
						addnav("More Fishing","runmodule.php?module=docks&op=docks&op2=godockfishing");
						output("`n`n`c`@Fishing Turns Left: `^%s`c",$fishingleft);
					}else{
						output("`n`n`c`\$No Fishing Turns Left`7`c");
						set_module_pref("bait",0);
					}
					addnav("Read Rules","runmodule.php?module=docks&op=docks&op2=fishingrules");
					addnav("Chat with Fishermen","runmodule.php?module=docks&op=docks&op2=fishingchat");
					addnav("Docks");
					addnav("Return to the Docks","runmodule.php?module=docks&op=docks&op2=enter");
				}elseif($badguy['creaturename']=="a Beefy Fisherman"){
					$expbonus=$session['user']['dragonkills']*3;
					$expgain =($session['user']['level']*20+$expbonus);
					$session['user']['experience']+=$expgain;
					output("`n`7You stand over the Beefy Fisherman and beat your chest.  You've won!`n");
					output("`n`@`bYou've gained `#%s experience`@.`b`n`n",$expgain);
					addnav("Deep Sea Fishing");
					$fishingtoday=get_module_pref("fishingtoday");
					if ($fishingtoday<5){
						$fishingleft=5-$fishingtoday;
						$op3=get_module_pref("quality");
						if (get_module_setting("interface")==0 && get_module_pref("user_interface")==0){
							addnav("More Fishing","runmodule.php?module=docks&op=fishingexpedition&op2=fish&op3=$op3");
							addnav("Check Gauges","runmodule.php?module=docks&op=fishingexpedition&op2=gauge&op3=$op3");
						}else{
							addnav("More Fishing","runmodule.php?module=docks&op=fishingexpeditiona&op2=fish&op3=$op3");
							addnav("Check Gauges","runmodule.php?module=docks&op=fishingexpeditiona&op2=gauge&op3=$op3");
						}
						output("`n`n`c`@Fishing Turns Left: `^%s`c",$fishingleft);
					}else{
						output("`n`n`c`\$No Fishing Turns Left`7`c");
						set_module_pref("bait",0);
					}
					if (get_module_setting("interface")==0 && get_module_pref("user_interface")==0) addnav("Move the Ship","runmodule.php?module=docks&op=fishingexpedition&loc=".get_module_pref("pqtemp"));
					else addnav("Move the Ship","runmodule.php?module=docks&op=fishingexpeditiona");
				}elseif($badguy['creaturename']=="a shark that you're reeling in"){
					output("`n`7You haul in the shark to the applause of everyone on board!  You've won! The captain shakes your hand and you feel proud. You `@gain a turn`7!");
					$session['user']['turns']++;
					addnav("Deep Sea Fishing");
					$weight=e_rand(700,1000);
					$pounds=floor($weight/16);
					$ounces=$weight-($pounds*16);
					output("`n`nYou check the weight:`n`n`&");
					if ($pounds>0) output("%s %s%s`7",$pounds,translate_inline($pounds>1?"Pounds":"Pound"),translate_inline($ounces>0?",":""));
					if ($ounces>0) output("`&%s %s`7",$ounces,translate_inline($ounces>1?"Ounces":"Ounce"));
					increment_module_pref("numberfish",1);
					increment_module_pref("fishweight",$weight);
					if ($weight>get_module_pref("bigfish")){
						output("`n`nThis is the biggest fish you've ever caught!");
						set_module_pref("bigfish",$weight);
					}
					$fishingtoday=get_module_pref("fishingtoday");
					if ($fishingtoday<5){
						$fishingleft=5-$fishingtoday;
						$op3=get_module_pref("quality");
						if (get_module_setting("interface")==0 && get_module_pref("user_interface")==0){
							addnav("More Fishing","runmodule.php?module=docks&op=fishingexpedition&op2=fish&op3=$op3");
							addnav("Check Gauges","runmodule.php?module=docks&op=fishingexpedition&op2=gauge&op3=$op3");
						}else{
							addnav("More Fishing","runmodule.php?module=docks&op=fishingexpeditiona&op2=fish&op3=$op3");
							addnav("Check Gauges","runmodule.php?module=docks&op=fishingexpeditiona&op2=gauge&op3=$op3");
						}
						output("`n`n`c`@Fishing Turns Left: `^%s`c",$fishingleft);
					}else{
						output("`n`n`c`\$No Fishing Turns Left`7`c");
						set_module_pref("bait",0);
					}
					if (get_module_setting("interface")==0 && get_module_pref("user_interface")==0) addnav("Move the Ship","runmodule.php?module=docks&op=fishingexpedition&loc=".get_module_pref("pqtemp"));
					else addnav("Move the Ship","runmodule.php?module=docks&op=fishingexpeditiona");
				}
				$badguy=array();
				$session['user']['badguy']="";
			}elseif ($defeat){
				if($badguy['creaturename']=="A Burly Fisherman"){
					$expbonus=$session['user']['dragonkills']*5;
					$exploss =($session['user']['level']*30+$expbonus);
					if ($exploss>$session['user']['experience']) $exploss=$session['user']['experience'];
					$session['user']['experience']-=$exploss;
					output("`n`7The Burly Fisherman stands over you and beats his chest, accepts the applause from the other anglers, and leaves you with one hitpoint left.`n");
					output("`n`@`bYou've lost `#%s experience`@.`b`n`n",$exploss);
					$session['user']['hitpoints']=1;
					addnav("Deep Sea Fishing");
					$fishingtoday=get_module_pref("fishingtoday");
					if ($fishingtoday<5){
						$fishingleft=5-$fishingtoday;
						$op3=get_module_pref("quality");
						addnav("More Fishing","runmodule.php?module=docks&op=docks&op2=godockfishing");
						output("`n`n`c`@Fishing Turns Left: `^%s`c",$fishingleft);
					}else{
						output("`n`n`c`\$No Fishing Turns Left`7`c");
						set_module_pref("bait",0);
					}
					addnav("Read Rules","runmodule.php?module=docks&op=docks&op2=fishingrules");
					addnav("Chat with Fishermen","runmodule.php?module=docks&op=docks&op2=fishingchat");
					addnav("Docks");
					addnav("Return to the Docks","runmodule.php?module=docks&op=docks&op2=enter");
				}elseif($badguy['creaturename']=="a Beefy Fisherman"){
					$expbonus=$session['user']['dragonkills']*6;
					$exploss =($session['user']['level']*40+$expbonus);
					if ($exploss>$session['user']['experience']) $exploss=$session['user']['experience'];
					$session['user']['experience']-=$exploss;
					output("`n`7The Beefy Fisherman stands over you and beats his chest.  You've lost!`n");
					output("`n`@`bYou've lost `#%s experience`@.`b`n`n",$exploss);
					output("You spend your next turn swobbing the deck. The captain gives you a potion that restores half of your hitpoints.");
					$session['user']['hitpoints']=round($session['user']['maxhitpoints']/2);
					$session['user']['turns']--;
					addnav("Deep Sea Fishing");
					$fishingtoday=get_module_pref("fishingtoday");
					if ($fishingtoday<5){
						$fishingleft=5-$fishingtoday;
						$op3=get_module_pref("quality");
						if (get_module_setting("interface")==0 && get_module_pref("user_interface")==0){
							addnav("More Fishing","runmodule.php?module=docks&op=fishingexpedition&op2=fish&op3=$op3");
							addnav("Check Gauges","runmodule.php?module=docks&op=fishingexpedition&op2=gauge&op3=$op3");
						}else{
							addnav("More Fishing","runmodule.php?module=docks&op=fishingexpeditiona&op2=fish&op3=$op3");
							addnav("Check Gauges","runmodule.php?module=docks&op=fishingexpeditiona&op2=gauge&op3=$op3");
						}
						output("`n`n`c`@Fishing Turns Left: `^%s`c",$fishingleft);
					}else{
						output("`n`n`c`\$No Fishing Turns Left`7`c");
						set_module_pref("bait",0);
					}
					if (get_module_setting("interface")==0 && get_module_pref("user_interface")==0) addnav("Move the Ship","runmodule.php?module=docks&op=fishingexpedition&loc=".get_module_pref("pqtemp"));
					else addnav("Move the Ship","runmodule.php?module=docks&op=fishingexpeditiona");
				}elseif($badguy['creaturename']=="a shark that you're reeling in"){
					output("`n`7You run out of energy and the shark gets away.");
					if ($session['user']['turns']>0){
						output("You lose a turn recovering your strength.");
						$session['user']['turns']--;
					}
					output("The captain gives you a potion that restores half of your hitpoints.");
					$session['user']['hitpoints']=round($session['user']['maxhitpoints']/2);
					addnav("Deep Sea Fishing");
					$fishingtoday=get_module_pref("fishingtoday");
					if ($fishingtoday<5){
						$fishingleft=5-$fishingtoday;
						$op3=get_module_pref("quality");
						if (get_module_setting("interface")==0 && get_module_pref("user_interface")==0){
							addnav("More Fishing","runmodule.php?module=docks&op=fishingexpedition&op2=fish&op3=$op3");
							addnav("Check Gauges","runmodule.php?module=docks&op=fishingexpedition&op2=gauge&op3=$op3");
						}else{
							addnav("More Fishing","runmodule.php?module=docks&op=fishingexpeditiona&op2=fish&op3=$op3");
							addnav("Check Gauges","runmodule.php?module=docks&op=fishingexpeditiona&op2=gauge&op3=$op3");
						}
						output("`n`n`c`@Fishing Turns Left: `^%s`c",$fishingleft);
					}else{
						output("`n`n`c`\$No Fishing Turns Left`7`c");
						set_module_pref("bait",0);
					}
					if (get_module_setting("interface")==0 && get_module_pref("user_interface")==0) addnav("Move the Ship","runmodule.php?module=docks&op=fishingexpedition&loc=".get_module_pref("pqtemp"));
					else addnav("Move the Ship","runmodule.php?module=docks&op=fishingexpeditiona");			
				}
				$badguy=array();
				$session['user']['badguy']="";
			}else{
				require_once("lib/fightnav.php");
				fightnav(true,false,"runmodule.php?module=docks");
			}
		}
	}
	page_footer();
}
?>