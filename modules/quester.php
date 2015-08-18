<?php

/****************************************************/
/********************* Credits **********************/
/****************************************************/
/* I would like to thank GenmaC for the inspiration */
/****************************************************/
/**** I would also like to thank everyone in the ****/
/*** Dragonprime IRC Chat for help with the names ***/
/****************************************************/
/**** Also, thank Middleclaw for the 0.9.2 ideas ****/
/****************************************************/
/* Finally thanks Lyrelle and Laroux for the tweaks */
/* and thanks to ThornKRT for the block cities idea */
/****************************************************/

function quester_getmoduleinfo(){
	$info = array(
		"name" => "Quester",
		"author" => "Derek",
		"version" => "0.9.3",
		"download" => "http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1156",
		"category" => "Quest",
		"description" => "Quest module that generates quests on the spot.",
        "settings"=>array(
        "Quester Settings,title",
        "normreward"=>"How much gold shall be rewarded for a normal level of the quest?,int|1000",
        "bossreward"=>"How much gold shall be rewarded for a boss level of the quest?,int|5000",
        "lvl2" => "Should the players be able to do level 2 quests (one normal and one boss)?,bool|0",
        "lvl3" => "Should the players be able to do level 3 quests (two normal and one boss)?,bool|1",
        "lvl4" => "Should the players be able to do level 4 quests (three normal and one boss)?,bool|1",
        "lvl5" => "Should the players be able to do level 5 quests (four normal and one boss)?,bool|1",
        "lvl6" => "Should the players be able to do level 6 quests (five normal and one boss)?,bool|0",
        "autolvls" => "Should the levels be chosen at random (Select no for the player to chose)?,bool|1",
        "When levels are selected at random only the levels that you set to 'yes' will be used.,note",
        "excludecities" => "What cities should not be used (seperate with a semicolon [;])?,text|Degolburg;Isla de Wen",
        "hoflist" => "How many players should be in the hof?,int|25",
        "Quester Credits,title",
        "I would like to thank GenmaC for the inspiration.,note",
        "I would also like to thank everyone in the Dragonprime IRC Chat for help with the names.,note",
        "Also thank Middleclaw for the 0.9.2 ideas.,note",
        "Finally thanks Lyrelle and Laroux for the tweaks and thanks to ThornKRT for the block cities idea.,note",
        ),
		"prefs"=>array(
		"Quester Prefs,title",
		"questpoints" => "How many Quester Points does this player have?,int|0",
		"for coding purposes only,note",
		"questlevel" => "What is the level of the quest this player is on (0 is no quest)?,int|0",
		"leveltype" => "What type of level is this player on (yes is monster and no is item)?,bool|0",
		"levelname" => "What is the name of the object on this level?,text|",
		"levelloc" => "What is the location of the object on this level (monster or item)?,location|".getsetting('villagename', LOCATION_FIELDS),
		"completed" => "Has this user completed this level?,bool|0",
		"queststage" => "What stage of the quest is the user on?,int|1",
		),
		);
		return $info;
}

function quester_install(){
	module_addeventhook("forest", "return 100;");
	module_addhook("charstats");
	module_addhook("ale");
	module_addhook("footer-hof");
	return true;
}

function quester_uninstall(){
	return true;
}

function quester_dohook($hookname,$args){
	global $session;
	$op=httpget('op');
	switch ($hookname){
		//potion book in bio
		case "ale":
			addnav("Cedrik");
			addnav("Rumors","runmodule.php?module=quester&op=enter");
		break;
		case "footer-hof":
			addnav("Warrior Rankings");
			addnav("Quester","runmodule.php?module=quester&op=hof");
		break;
		case "charstats":
			if (get_module_pref("questpoints") > 0) {
				addcharstat("Extra Info");
				addcharstat("Quester Points", get_module_pref("questpoints"));
			}
		break;
	}
	return $args;
}

function quester_runevent($type,$link) {
	global $session;
	$battle = false;
	$op = httpget('op');
	$act = httpget('act');
	$session['user']['specialinc'] = "module:quester";
	$battle = false;
	if ((is_module_active("cities") && $session['user']['location'] != get_module_pref("levelloc")) || get_module_pref("questlevel") == 0 || get_module_pref("completed") == 1) {
		//the player isn't on a quest, or is not in the right city, so do somthing else
		//this code is from the Find Gems v1.1 core module Forest Special by Eric Stevens, so I give him full credit.
		output("`^Fortune smiles on you and you find a `%gem`^!`0`n`n");
		$session['user']['gems']++;
		debuglog("found a gem in the dirt");
		$session['user']['specialinc'] = "";
	} else {
		if ($op == "search" || $op == "") {
			if (get_module_pref("leveltype") == 0) {
				switch (e_rand(1,5)) {
					case 1:
						output("As you walk through the forest, you trip on something. Though hurt, you get back up. You know it couldn't have been just a rock.  You look behind you, and guess what? It's the %s! You think how incredibly lucky you are as you pick it up and make your way back to the path.`n`n",get_module_pref("levelname"));
						set_module_pref("completed",1);
						$session['user']['specialinc'] = "";
						addnav("Actions");
						addnav("Return to Forest","forest.php");
					break;
					case 2:
						output("You decide to search high and low for the %s, knowing it would be hard to find. You finish searching every part of the forest, and you're just about to give up when you spot a rocky outcrop. You could've swarn that it sparkled at you, so you decide to dig it up a bit. After a bit of digging, you find the %s! It did take a little longer than you thought, however.`n`n",get_module_pref("levelname"),get_module_pref("levelname"));
						set_module_pref("completed",1);
						if ($session['user']['turns'] > 0) { $session['user']['turns']--; }
						$session['user']['specialinc'] = "";
						addnav("Actions");
						addnav("Return to Forest","forest.php");
					break;
					case 3:
						output("As you search the forest for somthing to kill, you hear a scream. After following the sound, you see an old man being attacked by a lesser creature. He seems to be trying to run, but for some odd reason he won't drop the bag of rocks he's carrying on his back. You know that the lesser creature would be no problem for you to handel, but you were already doing somthing. What should you do?`n`n");
						addnav("Help the old man","forest.php?op=quester3&act=yes");
						addnav("Ignore the old man","forest.php?op=quester3&act=no");
					break;
					case 4:
						output("You see an old man struggling with a large sack of rocks. He can barely carry them by himself. He looks so miserable. What should you do?`n`n");
						addnav("Help the old man","forest.php?op=quester4&act=yes");
						addnav("Ignore the old man","forest.php?op=quester4&act=no");
					break;
					case 5:
						$monstername = quester_combine(1);
						output("You decide to look around for the %s. After looking for a bit, you see it! There it is, guarded by the %s. This is just perfect. As you try and decide what to do next, you accidentally step on a twig! The monster hears the `i snap `i and decides to attack! You get on your guard to fight yet another foe.`n`n",get_module_pref("levelname"),$monstername);
						$badguy = array(
						"creaturename"=>translate_inline($monstername),
						"creaturelevel"=>$session['user']['level']+1,
						"creatureweapon"=>translate_inline("Power of the ".$monstername),
						"creatureattack"=>$session['user']['attack']*1.10,
						"creaturedefense"=>$session['user']['defence']*1.10,
						"creaturehealth"=>$session['user']['hitpoints']*1.10,
						"diddamage"=>0);
						$session['user']['badguy'] = createstring($badguy);
						$battle = true;
					break;
				}
			} else {
				if (get_module_pref("questlevel") == get_module_pref("queststage")) {
					$badguy = array(
						"creaturename"=>translate_inline(get_module_pref("levelname")),
						"creaturelevel"=>$session['user']['level']+2,
						"creatureweapon"=>translate_inline("Ultimate Power of the ".get_module_pref("levelname")),
						"creatureattack"=>$session['user']['attack']*1.25,
						"creaturedefense"=>$session['user']['defence']*1.25,
						"creaturehealth"=>$session['user']['hitpoints']*1.25,
						"creatureexp"=>round($session['user']['experience']/8, 0),
						"creaturegold"=>0,
						"diddamage"=>0);
				} else {
					$badguy = array(
						"creaturename"=>translate_inline(get_module_pref("levelname")),
						"creaturelevel"=>$session['user']['level']+1,
						"creatureweapon"=>translate_inline("Power of the ".get_module_pref("levelname")),
						"creatureattack"=>$session['user']['attack']*1.10,
						"creaturedefense"=>$session['user']['defence']*1.10,
						"creaturehealth"=>$session['user']['hitpoints']*1.10,
						"creatureexp"=>round($session['user']['experience']/10, 0),
						"creaturegold"=>0,
						"diddamage"=>0);
				}
				$session['user']['badguy'] = createstring($badguy);
				$battle = true;
			}
		} else if ($op == "quester3") {
			if ($act == "yes") {
				output("You pity the old man, so you jump over and chop the lesser monster in half. 'Thank you, you saved my life,' he says, 'Here, I have a nice reward for you, I found it in the ground. I am an archaeologist, hence these rocks. They were too important to leave behind.'`n`n");
				output("At first you don't really think you want a dirty old artifact that you have no use for, but when he takes it out, you are dumbfounded. He's giving you the %s you needed for Cedrik! How lucky you are!`n`n",get_module_pref("levelname"));
				set_module_pref("completed",1);
			} else {
				output("You decide that helping an old man is a waste of your time, so you ignore him and move on. You're not very nice, are you?`n`n");
			}
			$session['user']['specialinc'] = "";
			addnav("Actions");
			addnav("Return to Forest","forest.php");
		} else if ($op == "quester4") {
			if ($act == "yes") {
				output("You pity the old man, so you walk over and ask him if he needs any help. He says, 'Why, thank you kind young lad.' After carrying his very heavy sack for him to the village, he says, 'You are so kind. Here, I have a nice reward for you, I found it in the ground. I am an archaeologist, hence these rocks. Here, I can give you this little trinket I dug up.'`n`n");
				output("At first you don't really think you want a dirty old artifact that you have no use for, but when he takes it out, you are dumbfounded. He's giving you the %s you needed for Cedrik! How lucky you are!`n`n",get_module_pref("levelname"));
				if ($session['user']['turns'] > 0) { $session['user']['turns']--; }
				set_module_pref("completed",1);
			} else {
				output("You decide that helping an old man is a waste of your time, so you ignore him and move on. You're not very nice, are you?`n`n");
			}
			$session['user']['specialinc'] = "";
			addnav("Actions");
			addnav("Return to Forest","forest.php");
		}
	}

	if ($op == "fight"){
		$battle = true;
	}

	if ($battle) {
		include("battle.php");
		if ($victory) {
			if (get_module_pref("questlevel") == get_module_pref("queststage")) {
				output("`nYou give the final blow, and the %s falls to the ground. However, being immortal, the creature begins to regenerate. You know now that you must seal this beast once and for all, so you start to chant the incantationThunder Bluff to banish the creature to the next dimension. As it regenerates, it starts to slowly come at you. However, the time it has taken for it to regenerate was just what you needed. After regenerating, the beast starts running at you at full force. But your skills with magic shine through, becuase you finish the spell just as the monster's attack is 2 inches away from your skull. The monster disappears into nothingness, and you fall to the ground for a quick breather, knowing that it's finally over.`n`n",get_module_pref("levelname"));
				output("`4You have defeated the %s!!!.`0`n`n",get_module_pref("levelname"));
    	    	$exp = round($session['user']['experience'] * 0.10, 0);
    	    	addnews("`4`b%s`b has sealed the `b%s`b into another realm, freeing our world from it's terrible immortal evil!",$session['user']['name'],get_module_pref("levelname"));
			} else {
				if (get_module_pref("leveltype") == 1) {
					output("`nWith one final blow, you smite the %s onto the mountainside. Darkness takes you, but it is not your time yet. You awaken thereafter, though you can't tell how long you have been out. You remember now that you have killed a powerful monster. You don't know if Cedrik will believe you, so you ponder a solution. An idea comes to you...  You cut the head off of the corpse, and keep it to take back to Cedrik as proof of your victory.`n`n",get_module_pref("levelname"));
					output("`4You have defeated the %s!!!.`0`n`n",get_module_pref("levelname"));
				} else {
				  	output("`nWith one finaly blow, you smite your foe onto the mountainside. Darkness takes you, but it is not your time yet. You awaken thereafter, though you can't tell how long you were out. You remember now that you needed the item that thing was guarding. You stand up and take the %s from the creature's grasp, and you head back to Cedrik.`n`n",get_module_pref("levelname"));
				  	output("`4You have defeated the monster and taken the %s!!!.`0`n`n",get_module_pref("levelname"));
				}
				$exp = round($session['user']['experience'] * 0.08, 0);
			}
	    	if ($exp < 100){ $exp = 100; }
		    output("`4You gained `7%s`4 experience from the fight!`0`n`n", $exp);
			$session['user']['experience'] += $exp;
			set_module_pref("completed",1);
    	    if ($session['user']['hitpoints'] < 1) { $session['user']['hitpoints'] = 1; }
			$badguy=array();
			$session['user']['badguy'] = "";
			$session['user']['specialinc'] = "";
			addnav("Actions");
			addnav("Return to Forest","forest.php");
		} else if ($defeat) {
			output("`n`4Fight as you will, you find that you cannot defeat the monster. You know you can't run, and that fighting this foe any more would prove useless. You decide that there is no way out, and you kneel down before the creature. With one final blow, it sends you into the underworld.`0`n`n");
			output("`b`\$You have died!`0`b`n`n");
			output("You lose 10% of your experience, and all of your gold on hand!`n`n");
			debuglog("was slain by the ".get_module_pref("levelname")." and lost ".$session['user']['gold']." gold.");
			$session['user']['gold'] = 0;
			$session['user']['experience'] *= 0.9;
			$session['user']['alive'] = false;
			$badguy=array();
			$session['user']['badguy'] = "";
			$session['user']['specialinc'] = "";
			addnav("Daily News","news.php");
		} else {
			require_once("lib/fightnav.php");
			if ($type == "forest") {
				fightnav(true,false);
			} else {
				fightnav(true,false,$link);
			}
		}
	}
}

function quester_run(){
	global $session;
	page_header('Rumors in the realm');
	$op=httpget('op');
	$act=httpget('act');
	addnav("Actions");
	switch ($op) {
		case "enter":
		    if (get_module_pref("questlevel") == 0) {
				output("You decide to ask Cedrik if he has heard any rumors, but he gives you a very serious look. 'You want to know some Rumors?' he asks, and you start to feel sorry for asking.`n`n");
				output("Cedrik looks at you and says, 'I know of a few rumors for an adventurer like yourself, however you look like the kind of person who would run out to inspect these rumors and get yourself killed.' Are you sure you want to hear them?`n`n");
				addnav("Accept","runmodule.php?module=quester&op=start");
				addnav("Decline","inn.php?op=bartender");
			} else if (get_module_pref("completed") == 0) {
				output("You decide to ask Cedrik if he has heard any rumors. He looks at you as if you were nuts, and says, 'You want more rumors? Why? You already have a quest going from a rumor I told you, and you didn't event finish it. Haven't you gone to %s yet?`n`n", get_module_pref("levelloc"));
				output("Just a little scared, you wonder if you should ask Cedrik if you can start another quest, or if you should leave him alone lest something bad happen.`n`n");
				addnav("Abandon Quest","runmodule.php?module=quester&op=abandon");
				addnav("Leave","inn.php?op=bartender");
			} else {
				output("You tell Cedrik you've finished the quest. 'Oh yeah? Lets see your proof!' he responds. You don't know whether he's pleased with you or not, but you proceed to show him your proof.`n`n");
				if (get_module_pref("questlevel") == get_module_pref("queststage")) {
					if (get_module_setting("bossreward") > 0) {
						output("You start to reach into your backpack, but then you stop and stare at Cedrik realizing that you have no proof, since you banished the %s to another dimension. After a few seconds, Cedrik laughs and says, 'Hahaha, just kidding. I could feel the evil lift from the world, so I know you did it. Good job. Your reward is %s gold, and you definitely deserve it.' `n`n",get_module_pref("levelname"),get_module_setting("bossreward"));
					} else {
						output("You start to reach into your backpack, but then you stop and stare at Cedrik realizing that you have no proof, since you banished the %s to another dimension. After a few seconds, Cedrik laughs and says, 'Hahaha, just kidding. I could feel the evil lift from the world, so I know you did it. Good job.' `n`n",get_module_pref("levelname"));
					}
					quester_finish();
				} else {
					if (get_module_pref("leveltype") == 0) {
						if (get_module_setting("normreward") > 0) {
							output("You show him the %s you got from your quest, and Cedrik smiles. 'You've done well, young adventurer. Your reward is %s gold.' `n`n",get_module_pref("levelname"),get_module_setting("normreward"));
						} else {
							output("You show him the %s you got from your quest, and Cedrik smiles. 'You've done well, young adventurer.' `n`n",get_module_pref("levelname"));
						}
					} else {
						if (get_module_setting("normreward") > 0) {
							output("You show him the head of the %s, and Cedrik smiles. 'You've done well, young adventurer. Your reward is %s gold.' `n`n",get_module_pref("levelname"),get_module_setting("normreward"));
						} else {
							output("You show him the head of the %s, and Cedrik smiles. 'You've done well, young adventurer.' `n`n",get_module_pref("levelname"));
						}
					}
					quester_nextlvl();
					if (get_module_pref("questlevel") == get_module_pref("queststage")) {
						output("This time, Cedrik looks at you more serious than you have ever seen. You begin to tremble just from the way he's looking at you. He says in the most serious tone of voice you have ever heard a man speak in, 'This is it. The preparations are done. It is time to rid the world of this great evil once and for all.'`n`n");
						output("'It's time I told you the name of this foe. It is known as the `b%s`b, and is very powerful. This foe is immortal, so you cannot defeat him. However, there is another way to rid the world of its evil. Once you beat it down to unconsciousness, you must seal it into another dimension. This will rid the world of it without killing it.'`n`n",get_module_pref("levelname"));
						output("'You can find this last foe in the forests of `b%s`b. This will be your toughest foe yet, so be cautious. I hope I will see you again in one piece.' Then Cedrik turns back to his drinks, and you decide that you're not getting any younger, so you take a deep breath and head off to rid the world of this evil.`n`n",get_module_pref("levelloc"));
					} else {
						if (get_module_pref("leveltype") == 0) {
							output("Cedrik turns to a scroll and says, 'This part of the quest should be rather easy. You need to go and find the `b%s`b, down in the forests of `b%s`b. This is a very rare and powerful item, and it will help you greatly in your quest, as it's powerful magic can be used to seal away the great foe you must face. Be careful, however, as you never know what dangers you will find with it.' He hands you the scroll and you go off to find and take this item.`n`n",get_module_pref("levelname"),get_module_pref("levelloc"));
						} else {
							output("Cedrik leans twards you and says softly, 'This part of the quest could be a little tricky. You need to seek out and kill the `b%s`b, down in the forests of `b%s`b. This is one of your great foe's most loyal underlings, and, if not killed, will come to his aid. This will, in turn, pose a great threat to you as you battle your foe. So go now and kill this evil being.' He leans back and you go off to find and kill this monster.`n`n",get_module_pref("levelname"),get_module_pref("levelloc"));
						}
					}
				}
				addnav("Continue","inn.php?op=bartender");
			}
		break;
		case "abandon":
			if ($act == "baby") {
				output("You explain to Cedrik that you are a little baby and you want your bottle. He laughs and says, 'Yeah, I thought so. Ok then, I'll be sure to get someone else to do this for me.'`n`n");
				quester_abandon();
				addnav("Continue","inn.php?op=bartender");
			} else if ($act == "not") {
				output("You forget why you said you wanted to stop, and tell Cedrik that if you're a baby he must be a fetus. You start to laugh, but when you notice the look he's giving you, you quiet down. `n`n");
				output("After about 6 seconds of silence, Cedrik laughs too and says, 'you are the funny one, but rememer not to confuse funny with stupid around me, got it?' You nod, and leave.`n`n");
				addnav("Continue","inn.php?op=bartender");
			} else {
				output("After telling Cedrik you want to abandon the quest, he replies, 'What? Chickening out? I guess I should've expected as much. Fine, I'll go find soneone else to do this. That is, if you're absolutly sure you want to be a baby'`n`n");
				addnav("I'm a baby.","runmodule.php?module=quester&op=abandon&act=baby");
				addnav("I'm not a baby!","runmodule.php?module=quester&op=abandon&act=not");
			}
		break;
		case "start":
			if (!isset($_GET['level'])) {
				if (get_module_pref("questpoints") == 0) {
					if ($session['user']['dragonkills'] > 0) {
						output("Cedrik smiles and says, 'Why, aren't you the brave one. Ok, I know a rumor. I have heard that there is a great monster terrorizing the world, perhaps even more powerful than the Green Dragon itself! I heard that his monster was spawned from all of the evil in this world, which has taken the form of this hideous beast. You look like you have slain the Green Dragon about %s times, so I think you may be just the one for this job.'`n`n",$session['user']['dragonkills']);
					} else {
						output("Cedrik smiles and says, 'Why, aren't you the brave one. Ok, I know a rumor. I have heard that there is a great monster terrorizing the world, perhaps even more powerful than the Green Dragon itself! I heard that his monster was spawned from all of the evil in this world, which has taken the form of this hideous beast. I will help you slay this monster, but I'm not sure if you can handel it. At least you seem to be brave enough.'`n`n");
					}
				} else {
					output("Cedrik smiles and says, 'You're as brave as ever I see. Ok, I know another rumor, but it won't please you. I have heard that a new being was spawned from the deep evil released from this world by the great monster you killed, perhaps even more powerful than the last one! This new evil being has come back from another world through a dimensional rift, and is once again terrorizing this world. I think you may be just the one for this job. So, do you think you can handle it?'`n`n");
				}
				if (get_module_setting("autolvls") == 0) {
					if (get_module_setting("lvl2") == 1) { addnav("Accept (lvl2)","runmodule.php?module=quester&op=start&level=2"); }
					if (get_module_setting("lvl3") == 1) { addnav("Accept (lvl3)","runmodule.php?module=quester&op=start&level=3"); }
					if (get_module_setting("lvl4") == 1) { addnav("Accept (lvl4)","runmodule.php?module=quester&op=start&level=4"); }
					if (get_module_setting("lvl5") == 1) { addnav("Accept (lvl5)","runmodule.php?module=quester&op=start&level=5"); }
					if (get_module_setting("lvl6") == 1) { addnav("Accept (lvl6)","runmodule.php?module=quester&op=start&level=6"); }
				} else {
					addnav("Accept","runmodule.php?module=quester&op=start&level=rand");
				}
				addnav("Decline","inn.php?op=bartender");
			} else {
			  	if (httpget('level') == "rand") {
			  		$randlvl = e_rand(2,6);
					while (get_module_setting("lvl".$randlvl) == 0) {
						$randlvl = e_rand(2,6);
					}
					quester_start($randlvl);
				} else {
					quester_start(httpget('level'));
				}
				output("Great. Your courage is admirable, but be warned: you will need more than courage to complete this quest. Now, for your first task.`n`n");
				if (get_module_pref("leveltype") == 0) {
					output("Cedrik turns to a scroll and says, 'The first part of the quest should be rather easy. You need to go and find the `b%s`b, down in the forests of `b%s`b. This is a very rare and powerful item, and it will help you greatly in your quest, as it's powerful magic can be used to seal away the great foe you must face. Be careful, however, as you never know what dangers you will find with it.' He hands you the scroll and you go off to find and take this item.`n`n",get_module_pref("levelname"),get_module_pref("levelloc"));
				} else {
					output("Cedrik leans towards you and says softly, 'The first part of the quest could be a little tricky. You need to seek out and kill the `b%s`b, down in the forests of `b%s`b. This is one of your great foe's most loyal underlings, and, if not killed, will come to his aid. This will, in turn, pose a great threat to you as you battle your foe. So go now and kill this evil being.' He leans back and you go off to find and kill this monster.`n`n",get_module_pref("levelname"),get_module_pref("levelloc"));
				}
				addnav("Continue","inn.php?op=bartender");
			}
		break;
		case "hof":
	    	page_header("Most Quester Points");
	    	$acc = db_prefix("accounts");
	    	$mp = db_prefix("module_userprefs");
	    	$sql = "SELECT $acc.name AS name,
	    		$acc.acctid AS acctid,
	    		$mp.value AS questpoints,
	    		$mp.userid FROM $mp INNER JOIN $acc
	    		ON $acc.acctid = $mp.userid
	    		WHERE $mp.modulename = 'quester'
	    		AND $mp.setting = 'questpoints'
	    		AND $mp.value > 1 ORDER BY ($mp.value+0)
	    		DESC limit ".get_module_setting("hoflist")."";
	    	$result = db_query($sql);
	    	$rank = translate_inline("Points");
	    	$name = translate_inline("Name");
	    	output("`n`b`c`@Most Quest Points`n`n`c`b");
	    	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
	    	rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
	    	for ($i=0;$i < db_num_rows($result);$i++){
	    		$row = db_fetch_assoc($result);
	    		if ($row['name']==$session['user']['name']){
	    			rawoutput("<tr class='trhilight'><td>");
	    		}else{
	    			rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align=left>");
	    		}
	    		output_notl("%s",$row['name']);
	    		rawoutput("</td><td align=right>");
	    		output_notl("%s",$row['questpoints']);
	    		rawoutput("</td></tr>");
	    	}
	    	rawoutput("</table>");
	    	addnav("Back to HoF", "hof.php");
    	break;
	}
	page_footer();
}

function quester_start($level) {
	$type = e_rand(0,1);
	set_module_pref("questlevel",$level);
	set_module_pref("leveltype",$type);
	set_module_pref("levelname",quester_combine($type));
	set_module_pref("completed",0);
	set_module_pref("queststage",1);
	quester_cities();
	output("`4`bYou have started a quest!`b`0`n`n");
}

function quester_nextlvl() {
	global $session;
	increment_module_pref("queststage");
	if (get_module_pref("questlevel") == get_module_pref("queststage")) {
		set_module_pref("leveltype",1);
		set_module_pref("levelname",quester_combine(1));
	} else {
		$type = e_rand(0,1);
		set_module_pref("leveltype",$type);
		set_module_pref("levelname",quester_combine($type));
	}
	set_module_pref("completed",0);
	quester_cities();
	$session['user']['gold'] += get_module_setting("normreward");
	output("`4`bYou have moved to the next part of this quest!`b`0`n`n");
}

function quester_finish() {
	global $session;
	increment_module_pref("questpoints",get_module_pref("questlevel"));
	set_module_pref("questlevel",0);
	set_module_pref("leveltype",0);
	set_module_pref("levelname","");
	set_module_pref("completed",0);
	set_module_pref("queststage",1);
	if (is_module_active("cities")) {
		set_module_pref("levelloc",getsetting('villagename', LOCATION_FIELDS));
	}
	$session['user']['gold'] += get_module_setting("bossreward");
	output("`4`bYou have completed this quest!`b`0`n`n");
}

function quester_abandon() {
	set_module_pref("questlevel",0);
	set_module_pref("leveltype",0);
	set_module_pref("levelname","");
	set_module_pref("completed",0);
	set_module_pref("queststage",1);
	if (is_module_active("cities")) {
		set_module_pref("levelloc",getsetting('villagename', LOCATION_FIELDS));
	}
	output("`4`bYou have abandoned a quest...`b`0`n`n");
}

function quester_cities() {
	if (is_module_active("cities")) {
		$locarray = array();
		$locarray = modulehook("validlocation", $locarray);
		//unset the blocked cities
		$blocked = explode(";", get_module_setting("excludecities"));
		foreach($locarray as $lockey => $locval) {
			foreach($blocked as $block) {
				if ($lockey == $block) {
					unset($locarray[$lockey]);
				}
			}
		}
		//unset the capital
		unset($locarray[getsetting("villagename", LOCATION_FIELDS)]);
		//if for some reason somthing went wrong, set the default city as the city
		if (count($locarray) == 0) {
			set_module_pref("levelloc",getsetting("villagename", LOCATION_FIELDS));
		} else {
			set_module_pref("levelloc",array_rand($locarray));
		}
	}
	debug($locarray);
}

function quester_combine($type) {
	switch ($type) {
		case 0:
			$firstarray = array('Golden', 'Great', 'Divine', 'Invincible', 'Enternal', 'Demonic', 'Angelic', 'Majestic', 'Diamond', 'Emrald', 'Ruby', 'Sapphire');
			$secondarray = array('Gem', 'Grail', 'Goblet', 'Potion', 'Longsword', 'Scythe', 'Scimitar', 'Shield', 'Orb', 'Rod', 'Wand', 'Axe', 'Flamberge');
			$thirdarray = array('a Thousand Truths', 'Virtue', 'Revenge', 'Righteousness', 'Justice', 'Doom', 'Hatred', 'Sorrow', 'Silence', 'Strength', 'Bravery', 'Courage', 'Wisdom');
		break;
		case 1:
			$firstarray = array('Dreaded', 'Revered', 'Demonic', 'Iron', 'Steel', 'Giant', 'Black', 'Evil', 'Dark', 'Fiery');
			$secondarray = array('Dragon', 'Basilisk', 'Chimrera', 'Hydra', 'Wyvern', 'Harpy', 'Harbinger', 'Sphinx', 'Satyr', 'Golem', 'Griffen', 'Hippogriff', 'Phoenix', 'Cerberus', 'Siren', 'Sea Lion', 'Sea Sperpent', 'Knight', 'Devil', 'Elemental');
			$thirdarray = array('Death', 'Doom', 'Hell', 'Hades', 'Revenge', 'Hatred', 'Power', 'Legend', 'Pain', 'Mischief');
		break;
	}
	return $firstarray[array_rand($firstarray)]." ".$secondarray[array_rand($secondarray)]." of ".$thirdarray[array_rand($thirdarray)];
}

?>