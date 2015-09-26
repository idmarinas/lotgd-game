<?php
function dragoneggs_victory(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$monster=get_module_pref("monster");
	set_module_pref("monster",0);
	if ($monster==1){
		//Fire Hound
		$expgain =$session['user']['level']*22+$session['user']['dragonkills'];
		output("`nYou dispatch the dog and wipe your brow. Well done!`n`n");
		output("`@`bYou gain `^%s `#experience`@.`b",$expgain);
		if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
			$innname=getsetting("innname", LOCATION_INN);
		}else{
			$innname=translate_inline("The Boar's Head Inn");
		}
		addnav(array("Return to %s",$innname),"inn.php");
		addnews("%s`5 killed a `4Fire Hound`5 encountered in the Boar's Head Inn.",$session['user']['name']);
		debuglog("slayed a Fire Hound to gain $expgain experience that attacked while researching the Inn.");
	}elseif ($monster==2){
		//Wraith
		$expbonus=$session['user']['dragonkills']*3;
		$expgain =$session['user']['level']*19+$expbonus;
		$session['user']['gems']++;
		output("`n`%You slay the wraith and save the Order!`n`n");
		output("`@`bYou gain `^%s `#experience`@ and `%a gem`@.`b`n`n",$expgain);
		addnav("Return to the Order","runmodule.php?module=sanctum");
		debuglog("slayed a wraith to gain $expgain experience and a gem that attacked while researching the Order of the Inner Sanctum.");
	}elseif ($monster==3){
		//Rat
		$expbonus=$session['user']['dragonkills'];
		$expgain =$session['user']['level']*15+$expbonus;
		output("`n`%You kill the rat!`n`n");
		output("`@`bYou gain `^%s `#experience`@.`b`n`n",$expgain);
		addnav("Return to The Old House","runmodule.php?module=oldhouse");
		debuglog("slayed a rat to gain $expgain experience while researching the Old House.");
	}elseif ($monster==4){
		//Heat Vampire
		$expbonus=$session['user']['dragonkills']*5;
		$expgain =$session['user']['level']*25+$expbonus;
		output("`n`%You defeat the `QHeat Vampire`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@.`b`n`n",$expgain);
		if ($session['user']['gems']>=3){
			addnav("Continue","runmodule.php?module=dragoneggs&op=witch25");
			blocknav("village.php");
		}else{
			addnav("Return to The Old House","runmodule.php?module=oldhouse");
		}
		debuglog("slayed a Heat Vampire to gain $expgain experience while researching the Old House.");
	}elseif ($monster==5){
		//Rthithc
		$expbonus=$session['user']['dragonkills']*4;
		$expgain =$session['user']['level']*20+$expbonus;
		output("`n`%You defeat the `\$Rhthithc`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@.`b`n`n",$expgain);
		$chance=e_rand(1,9);
		$level=$session['user']['level'];
		if (($level>11 && $chance<=2) || ($level<=11 && $chance<=1)){
			addnav("Continue","runmodule.php?module=dragoneggs&op=town1");
			blocknav("village.php");
		}elseif (($level>9 && $chance<=3) || ($level<=9 && $chance<=2) && $session['user']['gems']>=4){
			addnav("Continue","runmodule.php?module=dragoneggs&op=town1&op2=4");
			blocknav("village.php");
		}else{
			output("After all the carnage, you can't find the `&Dragon Egg`@ to destroy it.");
		}
		debuglog("slayed a Rhthithc to gain $expgain experience while researching the Capital Town Square.");
	}elseif ($monster==6){
		//Zombie
		$expbonus=$session['user']['dragonkills']*3;
		$expgain =$session['user']['level']*15+$expbonus;
		$gold=e_rand(150,275);
		$session['user']['gold']+=$gold;
		output("`n`%You defeat the `\$Zombie`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@ and find `^%s gold`@!`b`n`n",$expgain,$gold);
		$chance=e_rand(1,5);
		$level=$session['user']['level'];
		if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
			output("`&You notice a dragon egg!");
			if ($session['user']['gems']>=2){
				output("Luckily, because you have `%2 gems`& you're able cast a spell to destroy it.  You lose `%2 gems`& but gain a Dragon Egg Point.");
				$session['user']['gems']-=2;
				increment_module_pref("dragoneggs",1,"dragoneggpoints");
				increment_module_pref("dragoneggshof",1,"dragoneggpoints");
				debuglog("killed a Zombie and gained $expgain experience and a dragon egg point and lost 2 gems while researching at the Gypsy Seer's Tent.");
				addnews("%s`^ killed a `\$Zombie`^, gained $gold gold, and `&Destroyed a Dragon Egg! Thank you!",$session['user']['name']);
			}else{
				output("Unfortunately, you don't have enough gems to cast a spell to destroy it.  You miss out on an easy opportunity to destroy an egg.");
				debuglog("killed a Zombie, gained $gold gold, and gained $expgain experience while researching at the Gypsy Seer's Tent.");
			}
		}else debuglog("killed a Zombie, gained $gold gold, and gained $expgain experience while researching at the Gypsy Seer's Tent.");
		addnav("Return to the Gypsy Seer's Tent","gypsy.php");
	}elseif ($monster==7){
		//Green Slime
		$expbonus=$session['user']['dragonkills']*7;
		$expgain =$session['user']['level']*25+$expbonus;
		output("`n`%You defeat the `@Green Slime`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@!`b`n`n",$expgain);
		$chance=e_rand(1,5);
		$level=$session['user']['level'];
		if ((($level>8 && $chance<=3) || ($level<=8 && $chance<=2)) && $session['user']['gems']>=5) {
			output("`&You see a dragon egg!");
			output("Luckily, because you have `%5 gems`& you're able to cast a spell to destroy it.  You lose `%5 gems`& but gain a Dragon Egg Point.");
			$session['user']['gems']-=5;
			increment_module_pref("dragoneggs",1,"dragoneggpoints");
			increment_module_pref("dragoneggshof",1,"dragoneggpoints");
			debuglog("killed a Green Slime and gained $expgain experience and a dragon egg point and lost 5 gems while researching at the Jeweler's.");
			addnews("%s`^ killed some `@Green Slime`^ and `&Destroyed a Dragon Egg.",$session['user']['name']);
		}else debuglog("killed a  Green Slime and gained $expgain experience while researching at the Jeweler's.");
		addnav("Return to Oliver's Jewelry","runmodule.php?module=jeweler");
	}elseif ($monster==8){
		//Lumberjack
		$expbonus=$session['user']['dragonkills']*4;
		$expgain =$session['user']['level']*15+$expbonus;
		$gold=e_rand(10,15)*$session['user']['level'];
		$session['user']['gold']+=$gold;
		output("`n`%You defeat the `\$Lumberjack`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@ and `^%s gold`@!`b`n`n",$expgain,$gold);
		debuglog("killed a Lumberjack, gained $gold gold and $expgain experience while researching at the Tattoo Parlor.");
		addnav("Return to the Tattoo Parlor","runmodule.php?module=petra");
	}elseif ($monster==9){
		//Rat-Thing
		$expbonus=$session['user']['dragonkills']*2;
		$expgain =$session['user']['level']*15+$expbonus;
		$gold=e_rand(5,15)*$session['user']['level']+66;
		$session['user']['gold']+=$gold;
		output("`n`%You defeat the `\$Rat-Thing`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@ and a hoard of money... `^%s gold`@!`b`n`n",$expgain,$gold);
		debuglog("killed a Rat-Thing, gained $gold gold and $expgain experience while researching at Heidi's Place.");
		addnav("Return to the Heidi's Place","runmodule.php?module=heidi");
	}elseif ($monster==10){
		//Gastropian
		$expbonus=$session['user']['dragonkills']*3;
		$expgain =$session['user']['level']*15+$expbonus;
		$gold=e_rand(10,30)*$session['user']['level'];
		$session['user']['gold']+=$gold;
		output("`n`%You defeat the `\$Gastropian`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@ and find `^%s gold`@ on the spelunker.`b`n`n",$expgain,$gold);
		debuglog("killed a Gastropian, gained $gold gold and $expgain experience while researching at the Merick's Stables.");
		addnav("Return to the Merick's Stables","stables.php");
	}elseif ($monster==11){
		//Stalagaryth
		$expbonus=$session['user']['dragonkills']*3;
		$expgain =$session['user']['level']*12+$expbonus;
		output("`n`%You defeat the `\$Stalagaryth`%!`n`n");
		if (e_rand(1,2)==1 || $session['user']['gems']<=2){
			output("`@`bYou gain `^%s `#experience`@.`b You better get out of here.`n`n",$expgain);
			debuglog("killed a Stalagaryth and gained $expgain experience while researching at the Merick's Stables.");
		}else{
			output("`@`bYou gain `^%s `#experience`@.`b`n`nYou suddenly notice a dragon egg under the body of the Stalagaryth... Using `%3 gems`@ you successfully cast a spell to `&destroy the egg`@!",$expgain);
			addnews("%s`^ killed a `@Stalagaryth`^ and `&Destroyed a Dragon Egg.",$session['user']['name']);
			$session['user']['gems']-=3;
			increment_module_pref("dragoneggs",1,"dragoneggpoints");
			increment_module_pref("dragoneggshof",1,"dragoneggpoints");
			debuglog("killed a Stalagaryth and gained $expgain experience and a dragon egg point by spending 3 gems while researching at the Merick's Stables.");
		}
		addnav("Return to the Merick's Stables","stables.php");
	}elseif ($monster==12){
		//Swamgrythph
		$expbonus=$session['user']['dragonkills']*4;
		$expgain =$session['user']['level']*35+$expbonus;
		output("`n`%You defeat the `@Swamgrythph`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@!`b`n`n",$expgain);
		$chance=e_rand(1,5);
		$level=$session['user']['level'];
		if ((($level>8 && $chance<=3) || ($level<=8 && $chance<=2)) && $session['user']['gems']>=5) {
			output("`&You notice a dragon egg!");
			output("Luckily, because you have `%5 gems`& you're able to cast a spell to destroy it.  You lose `%5 gems`& but gain a Dragon Egg Point.");
			$session['user']['gems']-=5;
			increment_module_pref("dragoneggs",1,"dragoneggpoints");
			increment_module_pref("dragoneggshof",1,"dragoneggpoints");
			debuglog("killed a Swamgrythph and gained $expgain experience and a dragon egg point and lost 5 gems while researching at the Gardens.");
			addnews("%s`^ killed some `@Swamgrythph`^ and `&Destroyed a Dragon Egg.",$session['user']['name']);
		}else debuglog("killed a  Swamgrythph and gained $expgain experience while researching at the Gardens.");
		addnav("Return to the Gardens","gardens.php");
	}elseif ($monster==13){
		//Sheldon Boy
		$expbonus=$session['user']['dragonkills']*5;
		$expgain =$session['user']['level']*30+$expbonus;
		output("`n`%You defeat the `@Sheldon Boy`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@!`b`n`n",$expgain);
		output("`QYou go to examine his weapon but realize it was damaged in battle and is useless.  Instead you find a wallet with over `^200 gold`Q!");
		$gold=e_rand(201,299);
		$session['user']['gold']+=$gold;
		debuglog("killed a Sheldon Boy and gained $expgain experience and $gold gold while researching at the Curious Looking Rock.");
		addnav("Return to the Curious Looking Rock","rock.php");
	}elseif ($monster==14){
		//Blupe
		$expbonus=$session['user']['dragonkills']*2;
		$expgain =$session['user']['level']*20+$expbonus;
		output("`n`%You defeat the `!Blupe`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@!`b`n`n",$expgain);
		if ($session['user']['gems']>=3) {
			output("`&You quickly cast an egg destroying spell using `%3 gems`&.");
			output("You lose `%3 gems`& but gain a Dragon Egg Point.");
			$session['user']['gems']-=3;
			increment_module_pref("dragoneggs",1,"dragoneggpoints");
			increment_module_pref("dragoneggshof",1,"dragoneggpoints");
			addnews("%s`^ killed a `!Blupe`^ and destroyed a dragon egg.  The Kingdom is a safer place now.",$session['user']['name']);
			debuglog("killed a Blupe and gained $expgain experience and a dragon egg point for 3 gems while researching at the Curious Looking Rock.");
		}else{
			debuglog("killed a Blupe and gained $expgain experience and lost 2 charm while researching at the Curious Looking Rock.");
			output("`4You can't destroy the egg though for lack of gems to cast a spell to do it.  You have to leave without finishing the job.  It's not very charming; so you lose `&2 charm`4.");
			$session['user']['charm']-=2;
		}
		addnav("Return to the Curious Looking Rock","rock.php");
	}elseif ($monster==15){
		//Book Gorilla-Man
		$expbonus=$session['user']['dragonkills']*4;
		$expgain =$session['user']['level']*25+$expbonus;
		$gold=e_rand(25,35)*$session['user']['level'];
		$session['user']['gold']+=$gold;
		output("`n`%You defeat the `\$Book Gorilla-Man`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@ and find `^%s gold`@ on the freak of nature.`b`n`n",$expgain,$gold);
		debuglog("killed a Book Gorilla-Man, gained $gold gold and $expgain experience while researching at the Hall of Fame.");
		addnav("Return to the Hall of Fame","hof.php");
	}elseif ($monster==16){
		//Dragon Sympathist
		$expbonus=$session['user']['dragonkills']*2;
		$expgain =$session['user']['level']*20+$expbonus;
		$session['user']['gems']++;
		output("`n`%You defeat the `\$Dragon Sympathist`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@ and find `%a gem`@.`b`n`n",$expgain);
		debuglog("killed a Dragon Sympathist, gained a gem and $expgain experience while researching at the Library.");
		if (is_module_active("library")) $library="library";
		else $library="dlibrary";
		addnav("Return to the Library","runmodule.php?module=$library&op=enter");
	}elseif ($monster==17){
		//Crazed Inmate
		$expbonus=$session['user']['dragonkills']*3;
		$expgain =$session['user']['level']*15+$expbonus;
		output("`n`%You defeat the `\$Crazed Inmate`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@!`b`n`n",$expgain);
		debuglog("killed a Crazed Inmate and gained $expgain experience while researching at the Jail.");
		if (is_module_active("jail")) $jail="jail";
		else $jail="djail";
		addnav("Return to the Jail","runmodule.php?module=$jail");
	}elseif ($monster==18){
		//Robber
		$gold=e_rand(5,10)*$session['user']['level'];
		$session['user']['gold']+=$gold;
		$expbonus=$session['user']['dragonkills']*4;
		$expgain =$session['user']['level']*30+$expbonus;
		output("`n`%You defeat the `\$Robber`%!`n`n");
		output("`@`bYou gain `^%s `#experience`@ and find `^%s gold (I guess he was on the poor side)`@.`b`n`n",$expgain,$gold);
		debuglog("killed a Robber and gained $gold gold and $expgain experience while researching at the Weapon Store.");
		addnav("Return to MightyE's Weapons","weapons.php");
	}elseif ($monster==19){
		//Bear
		$expbonus=$session['user']['dragonkills']*4;
		$expgain =$session['user']['level']*39+$expbonus;
		output("`n`%Silly `qB`^ear`%! When will you ever learn?`n");
		output("`n`@`bYou've gained `^%s `#experience`@.`b`n`n",$expgain);
		if(is_module_active("bearhof")) increment_module_pref("bearkills",1,"bearhof");
		addnav(array("Return to %s's Gift Shop",get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=pqgiftshop");
		debuglog("killed a Bear and gained $expgain experience while researching at the Gift Shop.");
		addnews("%s`@ killed a Bear that was pretending to be stuffed.  Silly Bear!!",$session['user']['name']);
	}elseif ($monster==20){
		//Newsboy 1
		$expgain =0;
		output("`n`%You thoroughly defeat the `2Newsboy`% and decide to leave.`n");
		output("`nAs you turn to leave, you hear a voice crying out `@'I... WANT... MY... TWO... `^GOLD... PIECES`@!!!!'");
		set_module_pref("monster",21);
		addnav("Attack the Newsboy (AGAIN!)","runmodule.php?module=dragoneggs&op=attack");
		debuglog("killed Newsboy 1 of 2 while researching at the Daily News.");
		blocknav("village.php");
	}elseif ($monster==21){
		//Newsboy 2
		$expbonus=$session['user']['dragonkills']*2;
		$expgain =$session['user']['level']*45+$expbonus;
		output("`n`%You defeat the `2Newsboy`% again.`n");
		if ($session['user']['gold']>1){
			output("`n`2To add insult to injury, you toss `^2 gold pieces`2 at him.  You hear a contented sigh come from the body.");
			$session['user']['gold']-=2;
			debuglog("killed Newsboy 1 of 2 and gained $expgain experience but lost 2 gold while researching at the Daily News.");
		}else debuglog("killed Newsboy 1 of 2 and gained $expgain experience while researching at the Daily News.");
		output("`n`@`bYou've gained `^%s `#experience`@.`b`n`n",$expgain);
		addnav("Return to the Daily News","news.php");
		addnews("%s`2 killed a Persistent Newsboy. If you listen closely, you can still hear the echoes of `@'I want my `^2 Gold Pieces`@!",$session['user']['name']);
	}
	villagenav();
	$session['user']['experience']+=$expgain;
}
?>