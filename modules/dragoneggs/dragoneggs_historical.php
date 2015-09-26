<?php
function dragoneggs_historical(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hall of Fame");
	output("`c`b`^The Hall of Fame`b`c`@`n");
	//This will fix their current location just in case they are being transported to the capital city
	if (is_module_active("cities") && $session['user']['location']!= getsetting("villagename", LOCATION_FIELDS)){
		$session['user']['location'] = getsetting("villagename", LOCATION_FIELDS);
	}
	$open=get_module_setting("hofopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("hofmin") && get_module_setting("hoflodge")>0 && get_module_pref("hofaccess")==0){
		output("You don't have enough `@Green Dragon Kills`@ to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("hofmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`@ to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("hoflodge")>0 && get_module_pref("hofaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`@You're out of research turns for today.");
	}else{
		output("`@You decide to look for Dragon Eggs at the Hall of Fame.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(25){
			case 1: case 2:
				$chance=e_rand(1,11);
				$level=$session['user']['level'];
				output("You decide to read up on the history of Green Dragons throughout the centuries.");
				output("`n`nBoy, this is exciting, isn't it?`n`n");
				if (($level>10 && $chance<=3) || ($level<=10 && $chance<=2)){
					output("You get bored and put the book down and start opening drawers.  AHA!  There's something shiny!");
					if ($session['user']['gems']==0){
						output("You find `%2 gems`@.");
						$session['user']['gems']=2;
						debuglog("found 2 gems while researching dragon eggs at the Hall of Fame.");
					}else{
						output("You find `%a gem`@!");
						$session['user']['gems']++;
						debuglog("found 1 gems while researching dragon eggs at the Hall of Fame.");
					}
				}else{
					output("No, it's not exciting.  You don't find anything useful.");
				}
			break;
			case 3: case 4:
				$dks=$session['user']['dragonkills'];
				if ((is_module_active("library") || is_module_active("dlibrary")) && (get_module_setting("libraryopen")==1 || ($dks>=get_module_setting("librarymin")+get_module_setting("mindk") && ((get_module_setting("librarylodge")>0 && get_module_pref("libraryaccess")>0) || get_module_setting("librarylodge")==0)))){
					output("You see one of the historical archivists preparing to leave to the library for some research.  He invites you along.`n`n");
					if (get_module_pref("researches")>=2) output("If you decide to go, you may have 2 extra research turns for the day!");
					else output("If you decide to go, you may have an extra research turn for the day!");
					output("Do you accept?`n`n");
					addnav("Yes","runmodule.php?module=dragoneggs&op=historical3&op2=yes1");
				}else{
					output("You see one of the historical archivists leaving to %s Square`@ to research there.  He looks at you and invites you to come along.",getsetting("villagename", LOCATION_FIELDS));
					addnav("Yes","runmodule.php?module=dragoneggs&op=historical3&op2=yes2");
				}
				addnav("No","runmodule.php?module=dragoneggs&op=historical3&op2=no");
				blocknav("village.php");
				blocknav("hof.php");
			break;
			case 5: case 6:
				output("You are trying to get to some of the really important books.");
				output("`n`nWould you like to slip some cash to the archivist for access to the Private Reading Room?`n`n");
				$cost=min($session['user']['level']*50,500);
				if ($session['user']['gold']<$cost){
					if ($session['user']['gold']<=0) $gold=translate_inline("nothing");
					else $gold="`^".$session['user']['gold'];
					output("You take a look at your current funds and realize that %s`@ won't be enough to bribe a very official looking archivist.",$gold);
				}else{
					output("Figuring that `^%s gold`@ should do the trick, you decide whether or not to slide him the money.`n`n",$cost);
					addnav("Try to Bribe Him","runmodule.php?module=dragoneggs&op=historical5");
				}
			break;
			case 7: case 8: 
				$chance=e_rand(1,13);
				$level=$session['user']['level'];
				if (($level>11 && $chance<=3) || ($level<=11 && $chance<=2) && $session['user']['turns']>1){
					output("You find an amazing book on improving your `&attack`@ and `&defense`@.");
					output("`n`nYou spend a turn reading it, but boy is it worth it!");
					$session['user']['attack']++;
					$session['user']['defense']++;
					$session['user']['turns']--;
					debuglog("spent a turn to gain an attack and a defense while researching dragon eggs at the Hall of Fame.");
				}else{
					output("You don't find anything worthy of your time.  What a waste!");
				}
			break;
			case 9: case 10:
				output("You find an interesting book on geneology.");
				output("`n`nThen, you realize that it's from your family tree!");
				output("`n`nOh good lord above! You read details about your family that are horrible! Oh the humanity!! You are `iCursed`i!");
				if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
					$session['bufflist']['blesscurse']['rounds'] += 5;
					debuglog("increased their curse by 5 rounds while researching dragon eggs at the Hall of Fame.");
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
					debuglog("was cursed while researching dragon eggs at the Hall of Fame.");
				}
			break;
			case 11: case 12:
				output("You knock some books over and they land on the foot of a person perusing the stacks.");
				output("`n`nYou look over and see a huge gorilla of a man looking very mad. You stammer an apology but he doesn't seem to care.");
				output("`n`nUh oh! It looks like you're in for a fight!");
				set_module_pref("monster",15);
				addnav("Fight Book Gorilla-Man","runmodule.php?module=dragoneggs&op=attack");
				blocknav("village.php");
				blocknav("hof.php");
			break;
			case 13: case 14:
				output("`2'Let's go Bird Watching!'`@ exclaims one of the archivists. `#'Hey, that's a great idea'`@ you hear another one exclaim.");
				output("`n`nThey turn to look at you. `2'Would you like to come too?'`@ they ask.");
				if (get_module_pref("researches")>=2) output("`n`nIf you decide to go, you may have 2 extra research turns for the day!`n`n");
				else output("`n`nIf you decide to go, you may have an extra research for the day!`n`n");
				output("Do you accept?");
				addnav("Yes","runmodule.php?module=dragoneggs&op=historical13&op2=yes");
				addnav("No","runmodule.php?module=dragoneggs&op=historical13&op2=no");
				blocknav("village.php");
				blocknav("hof.php");
			break;
			case 15: case 16:
				output("Hey! A nice chair!");
				output("`n`nYou get a nice butt-groove going as you read some of the more interesting periodicals.");
				output("`n`nThis is the good life! You are `iBlessed`i!");
				if ($session['bufflist']['blesscurse']['atkmod']==1.2) {
					$session['bufflist']['blesscurse']['rounds'] += 5;
					debuglog("increased their blessing by 5 rounds while researching dragon eggs at the Hall of Fame.");
				}else{
					apply_buff('blesscurse',
						array("name"=>translate_inline("Blessed"),
							"survivenewday"=>1,
							"rounds"=>15,
							"wearoff"=>translate_inline("The burst of energy passes."),
							"atkmod"=>1.2,
							"defmod"=>1.1,
							"roundmsg"=>translate_inline("Energy flows through you!"),
						)
					);
					debuglog("was blessed while researching dragon eggs at the Hall of Fame.");
				}
			break;
			case 17: case 18:
				$dks=$session['user']['dragonkills'];
				if ($badloc==0 &&(get_module_setting("animalopen")==1 || ($dks>=get_module_setting("animalmin")+get_module_setting("mindk") && ((get_module_setting("animallodge")>0 && get_module_pref("animalaccess")>0) || get_module_setting("animallodge")==0)))){
					output("`2'I hear they need some help at the Stables,'`@ explains an archivist.");
					addnav("Yes","runmodule.php?module=dragoneggs&op=historical17&op2=yes1");
				}else{
					output("`2'I hear they need some help at %s Square`2,'`@ explains an archivist.",getsetting("villagename", LOCATION_FIELDS));
					addnav("Yes","runmodule.php?module=dragoneggs&op=historical17&op2=yes2");
				}
				output("`2'Will you come with me?'`@ he asks.");
				if (get_module_pref("researches")>=2) output("`n`nIf you decide to go, you may have 2 extra research turns for the day!`n`n");
				else output("`n`nIf you decide to go, you may have an extra research for the day!`n`n");
				output("Do you accept?");
				addnav("No","runmodule.php?module=dragoneggs&op=historical17&op2=no");
				blocknav("village.php");
				blocknav("hof.php");
			break;
			case 19: case 20:
				if (get_module_pref("researches")<get_module_setting("research")){
					increment_module_pref("researches",1);
					output("Oh, this is a complete waste of your time.  You spend an additional research turn doing nothing useful at all!");
					debuglog("used 2 research turns while researching dragon eggs at the Hall of Fame.");
				}else{
					output("You settle down to read a book on how to research books on researching books.  You get a bit of a headache since none of this is making any sense.");
					if (isset($session['bufflist']['bitheadache'])) {
						$session['bufflist']['bitheadache']['rounds'] += 5;
						debuglog("worsed the bitheadache buff by 5 rounds will researching dragon eggs at the Hall of Fame.");
					}else{
						apply_buff('bitheadache',
							array("name"=>translate_inline("Bit of a Headache"),
								"rounds"=>3,
								"wearoff"=>translate_inline("The headache goes away."),
								"atkmod"=>0.8,
								"roundmsg"=>translate_inline("Oh what a horrible headache you have!"),
							)
						);
						debuglog("developed a bit of a headache buff will researching dragon eggs at the Hall of Fame.");
					}
				}
			break;
			case 21: case 22: 
				output("Interviewing some of the Hall of Fame members might be a good way to start.  You ask a couple of them some probing questions.");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if ($session['user']['charm']>25) $chance--;
				elseif ($session['user']['charm']<10) $chance++;
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
					output("One of the old codgers takes a liking to you and hands you something shiny.");
					output("`n`nYou `%gain a gem`@.");
					$session['user']['gems']++;
					debuglog("gained a gem by researching at the Hall of Fame.");
				}else{
					output("You can't convince anyone to tell you anything worthwhile.");
				}
			break;
			case 23: case 24:
				output("You find a book that looks very useful. In fact, you believe that it's going to help make you stronger.");
				output("Unfortunately, the pages are stuck together.`n`n");
				if ($session['user']['turns']>0){
					output("Will you try to get them apart?");
					addnav("Pull Pages Apart","runmodule.php?module=dragoneggs&op=historical23");
				}else{
					output("You don't have the time to get them to come apart.  That's too bad.");
				}
			break;
			case 25: case 26: case 27: case 28:
				if (get_module_pref("dragoneggs","dragoneggpoints")>0){
					output("You find an old professor wandering through the records at the Hall of Fame. You offer to help him find whatever he's looking for.");
					output("`&'Ahh, yes, thank you.  Well, I think you could use my help.  Shall we exchange services?' Professor Ottoman`@ offers.");
					output("`n`nYou ask him what he's talking about and he explains that he'd work as your ally if you can give him a `&Dragon Egg Point`@.");
					output("`n`nAre you interested?");
					addnav("Give a Dragon Egg Point?");
					addnav("Yes","runmodule.php?module=dragoneggs&op=historical25&op2=yes");
					addnav("No","runmodule.php?module=dragoneggs&op=historical25&op2=no");
					blocknav("village.php");
					blocknav("hof.php");
				}else{
					output("An old professor is looking through the stacks. `&'Well, if you had a dragon egg point I would offer to work by your side.  However, I don't see that happening,'`@ he explains.");
					output("`n`nYou decide to continue searching for something useful.");
				}
			break;
			case 29: case 30: case 31: case 32: case 33: case 34: case 35:
				output("You don't find anything of value.");
			break;
			case 36:
				dragoneggs_case36();
			break;
		}
	}
	addnav("Return to Hall of Fame","hof.php");
	villagenav();
}
?>