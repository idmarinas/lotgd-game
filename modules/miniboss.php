<?php

function miniboss_getmoduleinfo(){
	$info = array(
		"name"=>"Mini Bosses",
		"version"=>"5.03",
		"author"=>"DaveS",
		"category"=>"Forest",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1145",
		"settings"=>array(
			"Mini Bosses - Settings,title",
			"level"=>"What level does the  player encounter the Mini Boss?,int|10",
			"1st MiniBoss,title",
			"monster1"=>"`\$What type of monster is the 1st MiniBoss?,text|Werewolf",
			"weapon1"=>"`\$What is the monster's weapon?,text|his Deadly Claws",
			"exp1"=>"`\$Percentage of experience lost if defeated by MiniBoss:,range,0,100,1|6",
			"hp1"=>"`\$Multiplier for MiniBoss hitpoints:,floatrange,1.0,3.5,0.1|1.0",
			"att1"=>"`\$Multiplier for MiniBoss attack:,floatrange,1.0,2.5,0.1|1.0",
			"def1"=>"`\$Multiplier for MiniBoss defense:,floatrange,1.0,2.5,0.1|1.0",
			"2nd MiniBoss,title",
			"monster2"=>"`QWhat type of monster is the 2nd MiniBoss?,text|Centaur",
			"weapon2"=>"`QWhat is the monster's weapon?,text|his Crushing Hooves",
			"exp2"=>"`QPercentage of experience lost if defeated by MiniBoss:,range,0,100,1|7",
			"hp2"=>"`QMultiplier for MiniBoss hitpoints:,floatrange,1.0,3.5,0.1|1.1",
			"att2"=>"`QMultiplier for MiniBoss attack:,floatrange,1.0,2.5,0.1|1.1",
			"def2"=>"`QMultiplier for MiniBoss defense:,floatrange,1.0,2.5,0.1|1.1",
			"3rd MiniBoss,title",
			"monster3"=>"`^What type of monster is the 3rd MiniBoss?,text|Unicorn",
			"weapon3"=>"`^What is the monster's weapon?,text|her Unicorn Horn",
			"exp3"=>"`^Percentage of experience lost if defeated by MiniBoss:,range,0,100,1|8",
			"hp3"=>"`^Multiplier for MiniBoss hitpoints:,floatrange,1.0,3.5,0.1|1.2",
			"att3"=>"`^Multiplier for MiniBoss attack:,floatrange,1.0,2.5,0.1|1.1",
			"def3"=>"`^Multiplier for MiniBoss defense:,floatrange,1.0,2.5,0.1|1.1",
			"4th MiniBoss,title",
			"monster4"=>"`6What type of monster is the 4th MiniBoss?,text|Cyclops",
			"weapon4"=>"`6What is the monster's weapon?,text|a Huge Boulder",
			"exp4"=>"`6Percentage of experience lost if defeated by MiniBoss:,range,0,100,1|9",
			"hp4"=>"`6Multiplier for MiniBoss hitpoints:,floatrange,1.0,3.5,0.1|1.2",
			"att4"=>"`6Multiplier for MiniBoss attack:,floatrange,1.0,2.5,0.1|1.2",
			"def4"=>"`6Multiplier for MiniBoss defense:,floatrange,1.0,2.5,0.1|1.2",
			"5th MiniBoss,title",
			"monster5"=>"`2What type of monster is the 6th MiniBoss?,text|Golem",
			"weapon5"=>"`2What is the monster's weapon?,text|its Deadly Fists",
			"exp5"=>"`2Percentage of experience lost if defeated by MiniBoss:,range,0,100,1|10",
			"hp5"=>"`2Multiplier for MiniBoss hitpoints:,floatrange,1.0,3.5,0.1|1.3",
			"att5"=>"`2Multiplier for MiniBoss attack:,floatrange,1.0,2.5,0.1|1.0",
			"def5"=>"`2Multiplier for MiniBoss defense:,floatrange,1.0,2.5,0.1|1.3",
			"6th MiniBoss,title",
			"monster6"=>"`@What type of monster is the 6th MiniBoss?,text|Troll",
			"weapon6"=>"`@What is the monster's weapon?,text|her Fetid Claws",
			"exp6"=>"`@Percentage of experience lost if defeated by MiniBoss:,range,0,100,1|11",
			"hp6"=>"`@Multiplier for MiniBoss hitpoints:,floatrange,1.0,3.5,0.1|1.4",
			"att6"=>"`@Multiplier for MiniBoss attack:,floatrange,1.0,2.5,0.1|.9",
			"def6"=>"`@Multiplier for MiniBoss defense:,floatrange,1.0,2.5,0.1|1.2",
			"7th MiniBoss,title",
			"monster7"=>"`#What type of monster is the 7th MiniBoss?,text|Giant",
			"weapon7"=>"`#What is the monster's weapon?,text|a Huge Boulder",
			"exp7"=>"`#Percentage of experience lost if defeated by MiniBoss:,range,0,100,1|12",
			"hp7"=>"`#Multiplier for MiniBoss hitpoints:,floatrange,1.0,3.5,0.1|1.3",
			"att7"=>"`#Multiplier for MiniBoss attack:,floatrange,1.0,2.5,0.1|1.3",
			"def7"=>"`#Multiplier for MiniBoss defense:,floatrange,1.0,2.5,0.1|1.3",
			"8th MiniBoss,title",
			"monster8"=>"`3What type of monster is the 8th MiniBoss?,text|Vampire",
			"weapon8"=>"`3What is the monster's weapon?,text|his Piercing Fangs",
			"exp8"=>"`3Percentage of experience lost if defeated by MiniBoss:,range,0,100,1|13",
			"hp8"=>"`3Multiplier for MiniBoss hitpoints:,floatrange,1.0,3.5,0.1|1.4",
			"att8"=>"`3Multiplier for MiniBoss attack:,floatrange,1.0,2.5,0.1|1.4",
			"def8"=>"`3Multiplier for MiniBoss defense:,floatrange,1.0,2.5,0.1|1.1",
			"9th MiniBoss,title",
			"monster9"=>"`&What type of monster is the 9th MiniBoss?,text|Titan",
			"weapon9"=>"`&What is the monster's weapon?,text|Huge Sword",
			"exp9"=>"`&Percentage of experience lost if defeated by MiniBoss:,range,0,100,1|14",
			"hp9"=>"`&Multiplier for MiniBoss hitpoints:,floatrange,1.0,3.5,0.1|1.3",
			"att9"=>"`&Multiplier for MiniBoss attack:,floatrange,1.0,2.5,0.1|1.5",
			"def9"=>"`&Multiplier for MiniBoss defense:,floatrange,1.0,2.5,0.1|1.1",
			"10th MiniBoss,title",
			"monster10"=>"`)What type of monster is the 10th MiniBoss?,text|Flying Drake",
			"weapon10"=>"`)What is the monster's weapon?,text|Flaming Breath",
			"exp10"=>"`)Percentage of experience lost if defeated by MiniBoss:,range,0,100,1|15",
			"hp10"=>"`)Multiplier for MiniBoss hitpoints:,floatrange,1.0,3.5,0.1|1.4",
			"att10"=>"`)Multiplier for MiniBoss attack:,floatrange,1.0,2.5,0.1|1.4",
			"def10"=>"`)Multiplier for MiniBoss defense:,floatrange,1.0,2.5,0.1|1.4",
		),
		"prefs"=>array(
			"Mini Bosses - Preferences,title",
			"miniboss"=>"Has player defeated the Mini Boss this DK?,enum,0,No,1,Yes,2,Lost|0",
		),
	);
	return $info;
}

function miniboss_install(){
	module_addhook("forest");
	module_addhook("village");
	module_addhook("newday");
	module_addhook("dragonkill");
	return true;
}
function miniboss_uninstall(){
	return true;
}
function miniboss_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
		case "village":
			$level = $session['user']['level'];
			$tolevel = get_module_setting("level");
			$dks = $session['user']['dragonkills'];
			$expreqd = exp_for_next_level($level, $dks);
			if ($level>=$tolevel && $session['user']['experience']>$expreqd && get_module_pref("miniboss")==0){
				redirect("runmodule.php?module=miniboss&op=forcedfight");
			}
			$home = $session['user']['location']==get_module_pref("homecity","cities");
			if ($level>=$tolevel && get_module_pref("miniboss")==0 && $level<15){
				blocknav("train.php");
				if ($home){
					tlschema($args['schemas']['fightnav']);
					addnav($args['fightnav']);
					tlschema();
					addnav("Bluspring's Warrior Training","runmodule.php?module=miniboss&op=traintoboss");
				}
			}
		break;
		case "dragonkill":
			set_module_pref("miniboss",0);
		break;
		case "forest":
			if ($session['user']['level']>=get_module_setting("level") && get_module_pref("miniboss")==0){
				$dks=$session['user']['dragonkills'];
				$type=ceil($dks/10);
				if ($dks==0) $type=1;
				elseif ($type>10) $type=10;
				$num=$dks-((floor($dks/10))*10);
				$prefix=array("`\$Fire","`QOrange","`^Yellow","`6Gold","`2Green","`@Forest","`#Ocean","`3Sky","`&Snow","`)Death");
				$name=get_module_setting("monster".$type);
				$color=$prefix[$num];
				addnav(array("Seek the %s %s",$color,$name),"runmodule.php?module=miniboss&op=enter");
				blocknav("forest.php?op=dragon",false);
			}
		break;
		case "newday":
			if (get_module_pref("miniboss")==2) set_module_pref("miniboss",0);
		break;
	}
	return $args;
}
function miniboss_run(){
	 include("modules/miniboss/miniboss.php");
}
?>