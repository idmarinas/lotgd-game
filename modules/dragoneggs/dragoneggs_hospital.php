<?php
function dragoneggs_hospital(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Healer's Hut");
	output("`c`b`#Healer's Hut`b`c`n");
	$open=get_module_setting("healopen");
	//This will TRY to fix their current location just in case they are being transported here from the capital city
	if (is_module_active("cities") && $session['user']['location']== getsetting("villagename", LOCATION_FIELDS)){
		if ($session['user']['location'] !=get_module_pref("homecity","cities")) $session['user']['location']=get_module_pref("homecity","cities");
		elseif (is_module_active("racehuman") && $session['user']['location'] != get_module_setting("villagename","human")) $session['user']['location']=get_module_setting("villagename","human");
		elseif (is_module_active("raceelf") && $session['user']['location'] != get_module_setting("villagename","elf")) $session['user']['location']=get_module_setting("villagename","elf");
		elseif (is_module_active("racedwarf") && $session['user']['location'] != get_module_setting("villagename","dwarf")) $session['user']['location']=get_module_setting("villagename","dwarf");
	}
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("healmin") && get_module_setting("heallodge")>0 && get_module_pref("healaccess")==0){
		output("You don't have enough `@Green Dragon Kills`# to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
		require_once("lib/forest.php");
		forest(true);
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("healmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`# to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
		require_once("lib/forest.php");
		forest(true);
	}elseif ($open==0 && get_module_setting("heallodge")>0 && get_module_pref("healaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
		require_once("lib/forest.php");
		forest(true);
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`3You're out of research turns for today.");
		require_once("lib/forest.php");
		forest(true);
	}else{
		output("`3You decide to research at the Healer's Hut.`n`n");
		increment_module_pref("researches",1);
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		debuglog("used a research turn in the Healer's Hut.");
		//$case=50;
		switch($case){
			case 1: case 2:
				blocknav("forest.php");
				output("You enter an area behind a curtain that acts as a morgue and start examining bodies.");
				output("The only problem is... it's not quite dead yet!`n`nA hand shoots and grabs you.");
				addnav("Fight The Zombie!","runmodule.php?module=dragoneggs&op=hospital1");
			break;
			case 3: case 4:
				output("You find yourself involved in an experimental treatment.");
				output("You feel the staff inject a strange blue substance into your arm...`n`n");
				if (e_rand(1,2)==1){
					$gain=e_rand(1,5)*ceil($session['user']['level']/3);
					output("You gain `@%s hitpoints`3!",$gain);
					$session['user']['hitpoints']+=$gain;
					debuglog("gains $gain hitpoints by researching at Healer's Hut.");
				}else{
					output("You feel nothing in particular.  Seems like you were in the control group.");
				}
			break;
			case 5: case 6:
				output("You wander over to the medical records area.");
				output("You find the chart of a dragon sympathizer that was burned casting a spell while attempting to hatch a dragon egg.");
				if (e_rand(1,4)==1){
					output("`n`nThe payment is still in the chart and falls out.");
					output("You `%gain a gem`3!");
					$session['user']['gems']++;
					debuglog("gained a gem by researching at Healer's Hut.");
				}else{
					output("`n`nIt's useless. The doctor's handwriting is indecipherable.  You can't figure heads from tales from the chart.");
				}
			break;
			case 7: case 8:
				output("The doctor tells you that there's been an unfortunate accident.");
				$id=$session['user']['acctid'];
				$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid<>'$id' ORDER BY rand(".e_rand().") LIMIT 1";
				$res = db_query($sql);
				$row = db_fetch_assoc($res);
				$name = $row['name'];
				output("You recognize it as `^%s`3!",$name);
				$chance=e_rand(1,10);
				if ($chance==1){
					output("You draw back in horror and hit your head on an open cabinet. You lose all your hitpoints except one point.");
					$session['user']['hitpoints']=1;
					debuglog("loses all hitpoints except 1 by researching at Healer's Hut.");
				}elseif ($chance<=3){
					output("You decide that it's best to research a little further and discover a `%gem`3 on the body!");
					$session['user']['gems']++;
					debuglog("gained a gem by researching at Healer's Hut.");
				}else{
					output("However, there's nothing useful here.");
				}
			break;
			case 9: case 10: case 11: case 12:
				$gold=$session['user']['level']*75;
				output("The healer tells you that he has a wonderful new injection that has given new life to warriors.");
				output("He offers to inject you with the new medication for the low low price of `^%s gold`3.  Are you interested?",$gold);
				if ($session['user']['gold']>=$gold) addnav("Yes, I am!","runmodule.php?module=dragoneggs&op=hospital9&op2=$gold");
				addnav("No thanks!","runmodule.php?module=dragoneggs&op=hospital9&op2=no");
			break;
			case 13: case 14:
				output("A traveler is heading out for the night and offers to take you along to do some Dragon Egg Research.  You can go to anywhere to do some research!");
				output("Where would you like to go?");
				increment_module_pref("researches",-1);
				addnav("Travel");
				dragoneggs_navs();
				blocknav("runmodule.php?module=dragoneggs&op=hospital&op3=nav");
				addnav("Stay");
				addnav("Stay at the Healer's Hut","runmodule.php?module=dragoneggs&op=hospital13");
			break;
			case 15: case 16:
				output("After searching the hut for anything useful, you find yourself in the waiting area.  You try to get up but a nurse tells you that you should remain seated.  The healer will be with you shortly.");
				if ($session['user']['turns']>3){
					$session['user']['turns']-=3;
					debuglog("lost 3 turns by researching at Healer's Hut.");
					output("You `@spend 3 turns`3 waiting here before you can leave.");
				}else{
					output("You spend `@the rest of your turns`3 waiting here.");
					$session['user']['turns']=0;
					debuglog("lost all turns left by researching at Healer's Hut.");
				}
			break;
			case 17: case 18:
				output("You search the hut for dragon eggs but don't find anything interesting.  Suddenly, a nurse slips a piece of paper into your pocket.");
				$chance=e_rand(1,5);
				if ($chance<3){
					//Code by XChrisX
					global $block_new_output, $mostrecentmodule;
					$mod = $mostrecentmodule;
					$safe = $session;
					$block_new_output = true; // suppress output generated by the following modules.
					$specialties = modulehook("specialtymodules", array());
					foreach($specialties as $name => $file) {
					  require_once("modules/$file.php");
					  $mostrecentmodule = $file;
					  $fname = $file . "_dohook";
					  $fname("newday", array());
					}
					$session = $safe;
					$block_new_output = false; // re-allow output
					$mostrecentmodule = $mod;
					output("`n`nYou read the spell and realize that your specialty uses uses have been restored.");
					debuglog("had specialty points restored by researching at Healer's Hut.");
					//end XChrisX code
				}else output("`n`nYou read it but it is just her shopping list.");
			break;
			case 19: case 20:
				output("You are looking for dragon eggs when you hear someone sneaking up behind you...`n`n");
				if (e_rand(1,5)<3){
					output("Before you get a chance to react, the mad healer injects you with a needle filled with a green glowing liquid.");
					output("You feel sick to your stomach.");
					if (get_module_pref("researches")<get_module_setting("research")){
						increment_module_pref("researches",1);
						debuglog("loses an extra research turn by researching at Healer's Hut.");
						output("`n`nYou don't think you'll be able to research for eggs as much today.");
					}elseif ($session['user']['turns']>2){
						output("You lose 2 turns recovering.");
						$session['user']['turns']-=2;
						debuglog("loses 2 turns by researching at Healer's Hut.");
					}else{
						output("You feel very weak.");
						$session['user']['hitpoints']=1;
						debuglog("loses all hitpoints except 1 by researching at Healer's Hut.");
					}
				}else{
					output("You turn in time to see the healer attacking you with a needle filled with a green glowing substance.");
					output("You quickly overpower him and call for help.");
					output("It turns out he's been experimenting on people without authorization. You receive a `^250 gold`3 reward!");
					$session['user']['gold']+=250;
					debuglog("gains 250 gold by researching at Healer's Hut.");
				}
			break;
			case 21: case 22:
				output("The healer tells you to have a seat on the couch.  You chat for a while and discuss one of your thoughts on how the `@Green Dragon`3 is trying to destroy the kingdom.");
				output("`n`nThe doctor talks to you for a while and you start to think that you're confused; maybe there really are no such thing as dragons!");
				if ($session['user']['gems']>0){
					$session['user']['gems']--;
					output("`n`nYou `%pay a gem`3 for services rendered.");
					debuglog("lost a gem by researching at Healer's Hut.");
				}else{
					output("`n`nYou shake your head and leave.");
				}
			break;
			case 23: case 24:
				if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
					output("You wearily settle down by the healer.  He kindly casts a spell back to your full hitpoints.");
					$session['user']['hitpoints']=$session['user']['maxhitpoints'];
					debuglog("had hitpoints restored by researching at Healer's Hut.");
				}else{
					output("The healer looks you over but realizes you're at your best.");
				}
			break;
			case 25: case 26:
				output("You try to look inconspicuous and nobody notices you.  You quietly overhear some of the staff talking about how someone lost a gem here just recently.");
				output("`n`nYou look down and see something sparkling! You `%gain a gem`3!");
				$session['user']['gems']++;
				debuglog("gained a gem by researching at Healer's Hut.");
			break;
			case 27: case 28:
				output("You are grabbed by the healer. `#'Where's your identification???'`3 he grills at you.`n`n");
				$chance=e_rand(1,5);
				if (($session['user']['level']>10 && $chance<4) || ($session['user']['level']<11 && $chance<3)){
					if ($session['user']['hitpoints']>1){
						output("You quickly explain that you are here to heal a cut on your hand.  With a flash you give yourself a light cut costing you `\$1 hitpoint`3.");
						output("The healer accepts your explanation and lets you go.");
						$session['user']['hitpoints']--;
						debuglog("lost 1 hitpoint by researching at Healer's Hut.");
					}else{
						output("You explain that you're here to get healed.  He notices that you're not looking so good and tells you that you've come to the right place.");
					}
				}else{
					output("You can't come up with a good reason.");
					if (get_module_pref("researches")<get_module_setting("research")){
						output("The healer detains you for a short time, taking the time of one of your research turns.");
						increment_module_pref("researches",1);
						debuglog("loses an extra research turn by researching at Healer's Hut.");
					}else{
						output("You find yourself wasting a research turn.");
					}
				}
			break;
			case 29: case 30: case 31: case 32: case 33: case 34: case 35:
				output("You don't find anything of value.");
			break;
			case 36:
				dragoneggs_case36();
			break;
		}
		if ($case>2 && ($case<9 || $case>14)){
			require_once("lib/forest.php");
			forest(true);
		}
	}
}
?>