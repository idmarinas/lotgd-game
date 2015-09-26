<?php
function dragoneggs_docks(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("The Docks");
	output("`c`b`^The Docks`b`c`7`n");
	if (is_module_active("oceanquest")) $docks="oceanquest";
	else $docks="docks";
	//This will TRY to fix their current location just in case they are being transported here from the capital city
	if (is_module_active("cities") && $session['user']['location']== getsetting("villagename", LOCATION_FIELDS)){
		if ($session['user']['location'] !=get_module_pref("homecity","cities")) $session['user']['location']=get_module_pref("homecity","cities");
		elseif (is_module_active("racehuman") && $session['user']['location'] != get_module_setting("villagename","human")) $session['user']['location']=get_module_setting("villagename","human");
		elseif (is_module_active("raceelf") && $session['user']['location'] != get_module_setting("villagename","elf")) $session['user']['location']=get_module_setting("villagename","elf");
		elseif (is_module_active("racedwarf") && $session['user']['location'] != get_module_setting("villagename","dwarf")) $session['user']['location']=get_module_setting("villagename","dwarf");
	}
	$open=get_module_setting("docksopen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("docksmin") && get_module_setting("dockslodge")>0 && get_module_pref("docksaccess")==0){
		output("You don't have enough `@Green Dragon Kills`7 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("docksmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("dockslodge")>0 && get_module_pref("docksaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("`7You're out of research turns for today.");
	}else{
		output("`7You decide to look for Dragon Eggs at the Docks.`n`n");
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
				output("After chatting with one of the managers of the docks, you find yourself helping him out.`n`n`1");
				$total=0;
				if (e_rand(1,2)==1) $total++;
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)) $total++;
				if ($total==0){
					output("'Thanks for your help,'`7 he says, `1'I can't offer you anything, but I'll spread a good word about your name.'");
					output("`n`n`7You `&gain 2 charm`7.");
					$session['user']['charm']+=2;
					debuglog("gained 2 charm while researching dragon eggs at the Docks.");
				}else{
					$multiply=e_rand(80,150)*$total;
					output("'Great job.  Here's something for your troubles,'`7 he says as he hands you `^%s gold`7.",$multiply);
					$session['user']['gold']+=$multiply;
					debuglog("gained $multiply gold while researching dragon eggs at the Docks.");
				}
			break;
			case 3: case 4:
				output("Realizing that there's not much to find here today, you have a seat at the end of the docks and watch the water.");
				output("`n`nA sense of peace and serenity passes over you. You feel energized and `@gain a turn`7.");
				$session['user']['turns']++;
				debuglog("gained a turn while researching dragon eggs at the Docks.");
			break;
			case 5: case 6:
				output("Hey! There's something floating in the water.");
				output("`n`nYou know you can't resist grabbing for it, so you do.");
				addnav("Grab for it!","runmodule.php?module=dragoneggs&op=docks5");
				blocknav("runmodule.php?module=$docks&op=docks&op2=enter");
				blocknav("forest.php");
			break;
			//
			case 7: case 8:
				if ($session['user']['turns']>0){
					$amount=$session['user']['level']*60;
					if ($session['user']['level']>5) $amount=300;
					output("`2'Got some time to help?'`7 asks one of the dock workers.");
					output("`n`nYou ask how much they're paying and he says you can make `^%s gold`7 for each turn you work.",$amount);
					output("`n`nAre you interested?");
					addnav("Work");
					addnav("Spend 1 Turn","runmodule.php?module=dragoneggs&op=docks7&op2=1&op3=$amount");
					if ($session['user']['turns']>1) addnav("Spend 2 Turns","runmodule.php?module=dragoneggs&op=docks7&op2=2&op3=$amount");
					if ($session['user']['turns']>2) addnav("Spend 3 Turns","runmodule.php?module=dragoneggs&op=docks7&op2=3&op3=$amount");
					addnav("Leave");
				}else{
					output("You are approached by a dock worker who offers to pay you to help work but you don't have any turns left to do any work.");
				}
			break;
			case 9: case 10:
				output("You stand at the end of the docks and notice something in the distance.");
				output("It must be a mile away... yet the size of it startles you. Even from this far away you can see two huge malevolent eyes.  Are they staring at you???");
				if ($session['user']['gems']>0){
					output("`n`nYou are so disturbed that you `%drop a gem`7.");
					$session['user']['gems']--;
					debuglog("lost a gem while researching dragon eggs at the Docks.");
				}else{
					output("You try to clear your mind of the image by leaving.");
					blocknav("runmodule.php?module=$docks&op=docks&op2=enter");
				}
			break;
			case 11: case 12:
				output("You look over the docks and notice a piece of wood floating in the water.");
				output("`n`nUnable to restrain yourself, you grab for the wood and pick it up.  It seems pretty insignificant until you turn it over.");
				output("`n`nThere, in deep red lettering (Is it paint or blood??) are the words `iThe Reticent`i.");
				output("`n`nYou think about it for a moment and realize that `iThe Reticent`i was a ship that sank several weeks ago. Over 120 passengers died in the shark feeding frenzy that followed.`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
					output("Luckily you can clear the image from your mind.");
				}else{
					output("You can't clear the image from your mind.");
					output("You are `icursed`i!");
					if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their curse by 5 rounds while researching dragon eggs at the Docks.");
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
						debuglog("was cursed while researching dragon eggs at the Docks.");
					}
				}
			break;
			case 13: case 14:
				output("The impulse to dive into the waters starts to overcome you. You can end it all so easily.");
				output("`n`nIt would be so easy... so easy...`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,13);
				if (($level>8 && $chance<=12) || ($level<=8 && $chance<=11)){
					output("Luckily, you regain your sense of composure and step back.");
				}else{
					output("You find yourself falling into the water and let the dark sea envelope you.  For the moment you welcome the embrace of death.");
					output("`n`nThen, you feel strong arms grab you and pull you back to the dock. You're surrounded by anglers helping you get back on your feet.");
					output("`n`n`@'Aye, we've all felt that impulse. You should take a break for the rest of the day,'`7 a fisherman tells you.");
					output("`n`nYou're done researching for the day.");
					set_module_pref("researches",get_module_setting("research"));
					debuglog("almost drowned and lost the rest of the day's research turns while researching dragon eggs at the Docks.");
				}
			break;
			case 15: case 16:
				output("A crowd is gathered around something on the docks.  You go to research and smell a dreadful stench.  It's overwhelming!`n`n");
				$previous= strpos($session['user']['armor'],"Reinforced ")!==false ? 1 : 0;
				if ($session['user']['turns']>2 && $session['user']['gems']>2 && $previous==0){
					output("It's a strange creature.  Something completely alien.  If you spend `@3 turns`7 and `%3 gems`7 you may be able to glean something of value from it.");
					addnav("Examine the Creature","runmodule.php?module=dragoneggs&op=docks15");
				}else{
					output("You get closer and realize it's the carcass of a dead swordfish.  Nothing interesting here.");
				}
			break;
			case 17: case 18:
				output("A small man sits on the docks shivering. You can't help but ask what's wrong.");
				if ($session['user']['title'] !="Leech"){
					output("`n`n`4'Please... please just take this away from me.  I'll give you `^200 gold`4 if you do.  PLEASE!'`7 he begs.");
					output("`n`nWhat could be so horrible that someone would make such an offer? Are you going to accept?");
					addnav("Take the Item","runmodule.php?module=dragoneggs&op=docks17");
				}else{
					output("`n`nAs soon as you approach him he runs away screaming.  How odd!");
				}
			break;
			case 19: case 20:
				output("Hey... there's a box on the docks full of socks.`n`nWill you take it?");
				addnav("Take the Box","runmodule.php?module=dragoneggs&op=docks19");
			break;
			case 21: case 22: case 23: case 24:
				$cost=min($session['user']['level']*50,500);
				output("`3'Psst.. Want me to take care of something for you?'`7 asks a strange man in a trenchcoat.");
				output("`n`nHe asks for `^%s gold`7 to make your life better. Are you interested?",$cost);
				if ($session['user']['gold']>=$cost) addnav("Give Money","runmodule.php?module=dragoneggs&op=docks21&op2=$cost");
				else output("`#'I don't have that much money!' `7 you tell the stranger.  He suddenly loses interest in you.");
			break;
			case 25: case 26: case 27: case 28:
				output("`5'I have to tell you schumthing,'`7 says one of the drunks on the docks.`n`n");
				$level=$session['user']['level'];
				$chance=e_rand(1,9);
				if (($level>8 && $chance<=3) || ($level<=8 && $chance<=2)){
					output("He speaks a magical chant.  You feel your specialty improve.`n");
					require_once("lib/increment_specialty.php");
					increment_specialty("`@");
				}else{
					output("He spouts nothing but mindless gibberish.  And he smells.  That was fun.");
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
	addnav("Return to the Docks","runmodule.php?module=$docks&op=docks&op2=enter");
	addnav("Return to the Forest","forest.php");
}
?>