<?php
	global $session;
	$op2 = httpget('op2');
	$op = httpget('op');
	page_header("Sheldon Gang Hideout");
	output("`n`c`b`QSheldon Gang Hideout`c`b`n");
if ($op=="") {
	if (get_module_pref("member")==-1){
		output("You walk into a small shack... obviously these people are not living the high life.  In fact, you start to realize that you've just stepped over to the seedy side of life.");
		output("A quick look around the room reveals a wretched hive of villainy and disruputable types, ones you'll not find anywhere else in the kingdom.");
		output("`n`nSuddenly, you have second thoughts.  Slowly, you start to back towards the door when you're head is suddenly covered with a foul smelling pillow case.");
		output("`n`nWarrior, you are about to die.");
		addnav("Continue","runmodule.php?module=sheldon&op=continue&op2=1");
	}elseif (get_module_pref("member")>0){
		output("You hear quiet whispering and planning.  What is the next thing the gang is going to do??`n`n");
		output("`q`cNewest Gang Member: %s`q`n`n`c",get_module_setting("newestmember"));
		require_once("lib/commentary.php");
		addcommentary();
		viewcommentary("sheldon","What do you got to say?",20,"says");
		addnav("Sheldon Gang");
		addnav("Gang List","runmodule.php?module=sheldon&op=memberlist");
		addnav("Raid the Village","runmodule.php?module=sheldon&op=raid");
		modulehook ("sheldon-enter"); 
		addnav("Return");
		villagenav();
	}else{
		output("`qYou have somehow onto the door of a shack; with loud and dangerous voices coming from behind it.");
		output("`n`nYou better leave.");
		villagenav();
	}
}
if ($op=="continue"){
	if ($op2==1){
		output("You're hustled into a back room where your hands are bound and you're tied to a chair.  The smelly sack is removed from your head and you gasp in gratitude only to realize the air is thick with the smell of sweat and blood.");
		output("`n`nYou're punched in the face!");
		output("`n`nYou spit out a mouthful of blood and yell `#'STOP!'`Q");
		output("`n`nAnother punch lands in your stomach and you recoil in pain.");
		output("`n`nYou recover long enough to look up and see a huge gorilla getting ready to punch you and you try to steel yourself for the blow.");
		output("`n`nLucky for you, it doesn't land, and you realize the 'gorilla' is actually an uncouth and hairy gang member.  He steps aside and soon you're face to face with none other than `4Razor Sheldon`Q, leader of the Sheldon Gang.");
		addnav("Continue","runmodule.php?module=sheldon&op=continue&op2=2");
	}elseif ($op2==2){
		output("He steps up to you and pulls your hair back, stares deep into your eye, and walks away; murmuring something to one of his lieutenants.");
		output("`n`nThe Lieutenant, a man named `iSlice`i, pulls out a huge knife and approaches you.  He exposes your arm and with a motion faster than your eyes can follow he carves an `)Assassin's Dagger`Q into your arm.");
		output("`n`n`4'You're lucky,'`Q he says, `4'Sheldon thinks you're worthy of being in our gang.  Otherwise my knife wouldn't have stopped on your shoulder; it would have stopped on your throat.'");
		output("`n`n`QThe ropes holding your arms are cut and you're released. `4'Welcome to the Sheldon Gang!'");
		addnav("Continue","runmodule.php?module=sheldon&op=continue&op2=3");
	}elseif ($op2==3){
		output("Your arm aches, but another gang member expertly bandages it.  Your `\$hitpoints points drop to half`Q from the experience, but otherwise you're doing well.");
		output("`n`nAnother gang member brings you back to the headquarters entrance and asks if you'd like to go on the next raid of the city.");
		output("`n`nIt turns out you have the opportunity to do some gang activity once a day.  Sure, the chances are pretty high that you'll be tossed into jail, but it sure seems worth it, doesn't it?");
		output("`n`n`3'One other thing,'`Q one of the members mentions, `3'You're scar is probably going to heal completely after you kill a `@Green Dragon`3 and your membership will end at that time.'");
		increment_module_setting("sheldonnum",1);
		set_module_setting("newestmember",$session['user']['name']);
		set_module_pref("member",get_module_setting("sheldonnum"));
		$session['user']['hitpoints']*=.5;
		addnav("Headquarters","runmodule.php?module=sheldon");
	}
}
if ($op=="raid"){
	if (get_module_pref("raid")==0){
		output("You're on a raid with the gang!`n`n");
		set_module_pref("raid",1);
		switch(e_rand(1,11)){
			case 1:
				output("You decide to go rob the `^Bank`Q.`n`n");
				output("Everything's going well until suddenly you're surrounded by the `!Police`Q.`n`n");
				if (is_module_active("jail") || is_module_active("djail") && e_rand(1,3)==1){
					output("You fight your way out, but soon enough you're knocked unconcious; your `\$hitpoints knocked down to 1`Q.  Looks like you're going to jail!");
					if (is_module_active("jail")) $jail="jail";
					else $jail="djail";
					set_module_pref("injail",1,"$jail");
					addnav("To Jail", "runmodule.php?module=$jail");
					debuglog("lost all hitpoints except 1 and got sent to jail while raiding with the Sheldon Gang.");
				}else{
					output("You fight your way out, running as fast as you can.  You finally collapse back at the hideout... you get away but you've been wounded.  You only have `\$1 hitpoint`Q left.");
					debuglog("lost all hitpoints except 1 while raiding with the Sheldon Gang.");
					addnav("Back to the Hideout","runmodule.php?module=sheldon");
				}
				$session['user']['hitpoints']=1;
			break;
			case 2: case 3:
				output("You decide to go rob the `^Bank`Q.`n`n");
				output("`#'YOU! DOWN ON THE FLOOR! NOBODY MOVE AND NOBODY GETS HURT!!'`Q you scream.");
				output("`n`nSoon enough, you're counting your haul and splitting it with your fellow gang members back at the hideout.");
				$gold=e_rand(200,300);
				$session['user']['gold']+=$gold;
				output("`n`nYou made `^%s gold`Q!!!",$gold);
				debuglog("stole $gold gold from the bank while raiding with the Sheldon Gang.");
				addnews("Someone robbed the Bank!! It looks like the Sheldon Gang is out of control again!",true);
				addnav("Back to the Hideout","runmodule.php?module=sheldon");
			break;
			case 4: case 5:
				output("You decide to go raid the `\$Healer's Hut`Q.`n`n");
				output("What are all these bottles??? Who knows... you grab as many as you can and head back to the hideout.");
				output("You randomly pop some of the pills... Is this really smart???`n`n");
				addnews("Someone robbed the Hospital!! It looks like the Sheldon Gang is out of control again!",true);
				switch(e_rand(1,15)){
					case 1:
						output("YES!! You took something REALLY good! You `@Gain 4 turns`Q!");
						$session['user']['turns']+=4;
						debuglog("gained 4 turns after raiding the pharmacy with the Sheldon Gang.");
					break;
					case 2: case 3:
						output("YES!! You took something PRETTY good! You `@Gain 3 turns`Q!");
						$session['user']['turns']+=3;
						debuglog("gained 3 turns after raiding the pharmacy with the Sheldon Gang.");
					break;
					case 4: case 5:
						output("YES!! You took something good! You `@Gain 2 turns`Q!");
						$session['user']['turns']+=2;
						debuglog("gained 2 turns after raiding the pharmacy with the Sheldon Gang.");
					break;
					case 6: case 7: case 8: case 9:
						output("YES!! You took something that's not bad! You `@Gain 1 turn`Q!");
						$session['user']['turns']++;
						debuglog("gained 1 turn after raiding the pharmacy with the Sheldon Gang.");
					break;
					case 10: case 11: case 12: case 13:
						output("Well, it didn't do anything.");
						debuglog("nothing happened after raiding the pharmacy with the Sheldon Gang.");
					break;
					case 14:
						if ($session['user']['turns']>1){
							output("You take something that upsets your stomach.  You `\$lose 2 turns`Q.");
							$session['user']['turns']-=2;
							debuglog("lost 2 turns after raiding the pharmacy with the Sheldon Gang.");
						}else{
							output("You get a horrible stomach ache... and it's not going to go away by tomorrow.");
							apply_buff('stomach',array(
								"name"=>"`4Stomach Ache",
								"rounds"=>10,
								"wearoff"=>"`4Your stomach feels better.",
								"atkmod"=>.9,
								"survivenewday"=>1,
							));
							debuglog("received a stomache ache buff will raiding the pharmacy with the Sheldon Gang.");
						}
					break;
					case 15:
						if ($session['user']['turns']>0){
							output("You take something that upsets your stomach.  You `\$lose 1 turn`Q.");
							$session['user']['turns']--;
							debuglog("lost 1 turn after raiding the pharmacy with the Sheldon Gang.");
						}else{
							output("You get a nauseous feeling in your stomach... and it's not going to go away by tomorrow.");
							apply_buff('nausea',array(
								"name"=>"`4Nausea",
								"rounds"=>5,
								"wearoff"=>"`4Your stomach feels better.",
								"atkmod"=>.95,
								"survivenewday"=>1,
							));
							debuglog("received a nausea buff will raiding the pharmacy with the Sheldon Gang.");
						}
					break;
				}
				addnav("Back to the Hideout","runmodule.php?module=sheldon");
			break;
			case 6: case 7:
				output("You decide to go rob the `1Weapons Shop`Q.`n`n");
				switch(e_rand(1,15)){
					case 1: case 2: case 3: case 4: case 5: case 6:  case 7:
						output("`!MightyE`Q sees you guys coming and pulls out a VERY large sword.");
						output("You and the rest of the gang huddle together and decide it'll be much more fun to go back to the hideout and pretend you were successful rather than face `!MightyE`Q.");
						debuglog("unsuccessfully tried to raid the Weapons Shop with the Sheldon Gang.");
					break;
					case 8: case 9: case 10: case 11: case 12: case 13:
						output("You sneak in and nobody sees.  You find a box full of glowing rocks.  One of them turns out to be valuable!");
						$session['user']['gems']++;
						output("`n`nYou `%gain a gem`Q!");
						debuglog("gained 1 clue on a raid to the Weapons Shop with the Sheldon Gang.");
					break;
					case 14: case 15:
						output("You sneak in and nobody sees. You find a box full of glowing rocks.  Two of them turn out to be valuable!");
						$session['user']['gems']+=2;
						output("`n`nYou `%gain 2 gems`Q!");
						debuglog("gained 2 gems on a raid to the Weapons Shop with the Sheldon Gang.");
					break;
					case 16:
						output("You sneak in and nobody sees.  You find a box full of glowing rocks.  Two of them turn out to be valuable!");
						$session['user']['gems']+=3;
						output("`n`nYou `%gain 3 gems`Q!");
						debuglog("gained 3 gems on a raid to the Weapons Shop with the Sheldon Gang.");
					break;
				}
				addnav("Back to the Hideout","runmodule.php?module=sheldon");
			break;
			case 8: case 9:
				output("Your raiding party doesn't find anything worth doing.");
				addnav("Back to the Hideout","runmodule.php?module=sheldon");
			break;
			case 10:
				output("Oh no! It's the cops! Will you fight to the death or will you surrender?");
				addnav("Surrender","runmodule.php?module=sheldon&op=surrender");
				addnav("Fight","runmodule.php?module=sheldon&op=attack");
			break;
			case 11:
				if (e_rand(1,18)==1){
					output("You're gathering some of the Gang Members for a raid when `bSheldon`b pulls you aside.");
					output("`4'I don't like your attitude.  You're out of the Gang,'`Q he says.");
					output("`n`nBefore you have a chance to question, `iSlice`i throws you on a chair and cuts your arm; removing the Sheldon Gang Scar.");
					output("`n`n`4'Now get out of here before I get mean,'`Q says `bSheldon`b.");
					set_module_pref("member",0);
					debuglog("was kicked out of the Sheldon gang while raiding with the Sheldon Gang.");
					villagenav();
				}else{
					output("You don't find anything.");
					addnav("Back to the Hideout","runmodule.php?module=sheldon");
				}
			break;
		}
	}else{
		output("Hmmm... You can only raid once a day.  Try again tomorrow.");
		addnav("Back to the Hideout","runmodule.php?module=sheldon");
	}
}
if ($op=="surrender"){
	if (is_module_active("jail") || is_module_active("djail") && e_rand(1,2)==1){
		if (is_module_active("jail")) $jail="jail";
		else $jail="djail";
		set_module_pref("injail",1,"$jail");
		addnav("To Jail", "runmodule.php?module=$jail");
		output("Off to Jail you go!  And they keep all your money, too.  Dirty Cops!!");
		debuglog("lost all gold and got sent to jail while raiding with the Sheldon Gang.");
	}else{
		output("You pretend you're going to surrender and then start running as fast as you can.  You finally collapse back at the hideout... you get away but you've been wounded.  You only have `\$1 hitpoint`Q left.");
		output("`n`nYou realize you've `^lost your gold pouch`Q!");
		debuglog("lost all hitpoints except 1 and all gold while raiding with the Sheldon Gang.");
		addnav("Back to the Hideout","runmodule.php?module=sheldon");
		$session['user']['hitpoints']=1;
	}
	$session['user']['gold']=0;
}
if ($op=="attack") {
	$rand=e_rand(105,115)/100;
	$badguy = array(
		"creaturename"=>"`!A Policeman",
		"creaturelevel"=>16,
		"creatureweapon"=>"a baton",
		"creatureattack"=>$session['user']['attack']+2,
		"creaturedefense"=>$session['user']['defense']+3,
		"creaturehealth"=>round($session['user']['maxhitpoints']*$rand),
		"diddamage"=>0,
		"type"=>"police");
	$session['user']['badguy']=createstring($badguy);
	$op="fight";
}
if ($op=="fight"){
	$battle=true;
}
if ($battle){       
	include("battle.php");  
	if ($victory){
		$expbonus=$session['user']['dragonkills']*5;
		$expgain =($session['user']['level']*50+$expbonus);
		$session['user']['experience']+=$expgain;
		output("`n`n`QYou defeat the policeman and run off to hide.");
		output("`QYou gain `^%s `#experience`Q.`n",$expgain);
		output("`QYou gain `^10 gold`Q.  It seems like the police aren't paid very well here.`n`n");
		$session['user']['gold']+=10;
		debuglog("defeated a policeman for 10 gold and $expgain experience with the Sheldon Gang.");
		addnews("Someone beat a police officer to unconsciousness.  The Sheldon Gang is suspected of being involved.");
		addnav("Back to the Hideout","runmodule.php?module=sheldon");
	}elseif($defeat){
		$exploss = round($session['user']['experience']*.05);
		output("`n`n`QThe policman defeats you.  All your gold is confiscated.`n");
		output("You lose `^%s `#experience`Q.`n",$exploss);
		if (is_module_active("jail") || is_module_active("djail") && e_rand(1,2)==1){
			output("`nYou're taken to jail to think about what you did.  They interrogate you, but you refuse to betray the Sheldon Gang.");
			if (is_module_active("jail")) $jail="jail";
			else $jail="djail";
			set_module_pref("injail",1,"$jail");
			addnav("To Jail", "runmodule.php?module=$jail");
			debuglog("lost $exploss experience and all gold and all hitpoints except 1 and got sent to jail for fighting as part of the Sheldon Gang.");
			addnews("%s `Q was sent to jail for fighting with a Policeman.  There are strong suspicions that %s`Q is involved with the Sheldon Gang, but there's no solid proof.",$session['user']['name'],translate_inline($session['user']['sex']?"she":"he"));
		}else{
			output("`nYou're left for dead in the middle of the village.  No gold, only 1 hitpoint, and you've lost experience.  Maybe you should reconsider your life of crime!");
			villagenav();
			debuglog("lost $exploss experience and all gold, left with 1 hitpoint for fighting as part of the Sheldon Gang.");
			addnews("%s `Q was seen beaten to a pulp.  There are strong suspicions that %s is involved with the Sheldon Gang, but there's no solid proof.",$session['user']['name'],translate_inline($session['user']['sex']?"she":"he"));
		}
		$session['user']['experience']-=$exploss;
		$session['user']['hitpoints']=1;
		$session['user']['gold']=0;
	}else{
		require_once("lib/fightnav.php");
		fightnav(true,false,"runmodule.php?module=sheldon");
	}
}
if ($op=="memberlist") {
	$pp = 40;
	$page = httpget('page');
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'sheldon' AND setting = 'member' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("accounts").".name FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'sheldon' AND setting = 'member' AND value > 0 ORDER BY (0-value) DESC $limit";
	$result = db_query($sql);
	$rank = translate_inline("Number");
	$name = translate_inline("Name");
	$none = translate_inline("No Revealed Members");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td></tr>");
	if (db_num_rows($result)==0) output_notl("<tr class='trlight'><td colspan='3' align='center'>`&$none`0</td></tr>",true);
	else{
		for($i = $pageoffset; $i < $cond && $count; $i++) {
			$row = db_fetch_assoc($result);
			if ($row['name']==$session['user']['name']){
			rawoutput("<tr class='trhilight'><td>");
		}else{
			rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
		}
		$j=$i+1;
		output_notl("$j.");
		rawoutput("</td><td>");
		output_notl("`&%s`0",$row['name']);
		rawoutput("</td></tr>");
		}
	}
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=sheldon&op=memberlist&page=".($p/$pp+1));
		}
	}
	addnav("Sheldon Gang");
	addnav("Headquarters","runmodule.php?module=sheldon");
	addnav("Return");
	villagenav();
}
page_footer();
?>