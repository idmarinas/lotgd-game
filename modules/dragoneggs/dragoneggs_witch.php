<?php
function dragoneggs_witch(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Old House");
	output("`c`b`&The Old House`b`c`7`n");
	$open=get_module_setting("witchopen");
	//If the player isn't in the right city, move them to it.
	if ($session['user']['location']!= get_module_setting("oldhouseloc","oldhouse")){
		$session['user']['location'] = get_module_setting("oldhouseloc","oldhouse");
	}
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("witchmin") && get_module_setting("witchlodge")>0 && get_module_pref("witchaccess")==0){
		output("You don't have enough `@Green Dragon Kills`7 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("witchmin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`7 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("witchlodge")>0 && get_module_pref("witchaccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("You're out of research turns for today.");
	}else{
		output("You decide to look for Dragon Eggs in the Old House.`n`n");
		increment_module_pref("researches",1);
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		debuglog("used a research turn in the Old House.");
		switch($case){
			case 1: case 2:
				output("You are looking around when you walk into a room with a witch sitting at a desk.");
				output("`n`n`#'Excuse me, but I was wondering if you could help me,'`7 you say.`n`n`@"); 
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
					output("'Yes I can,'`7 she replies. She casts a spell on you.");
					output("`n`n`QYou Advance in your Specialty`7.");
					require_once("lib/increment_specialty.php");
					increment_specialty("`Q");
					debuglog("increment specialty by researching at the Old House.");
				}else{
					output("'No, I cannot,'`7 she replies as she waves her arms to dismiss you.");
					output("`n`nYou are `iCursed`i!");
					if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their curse by 5 rounds by researching at the Old House.");
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
						debuglog("received a curse by researching at the Old House.");
					}
				}
			break;
			case 3: case 4:
				page_header("Elsewhere");
				output("You walk upstairs and step through a door; finding yourself transported somewhere else!");
				increment_module_pref("researches",-1);
				$dks=$session['user']['dragonkills'];
				$min=get_module_setting("mindk");
				$chance=e_rand(1,21);
				if ($chance==1){
					//Block the healer links from occurring in the capital if cities is active
					if ((is_module_active("cities") && $session['user']['location']!= getsetting("villagename", LOCATION_FIELDS))||is_module_active("cities")==0) $openh=1;
					else $openh=0;
					if($openh==1 && (get_module_setting("healopen")==1 ||($dks>=get_module_setting("healmin")+$min && ((get_module_setting("heallodge")>0 && get_module_pref("healaccess")>0) || get_module_setting("heallodge")==0)))) addnav("Healer's Hut","runmodule.php?module=dragoneggs&op=hospital&op3=nav");
					else $chance=2;
				}
				if ($chance==2){
					if(get_module_setting("bankopen")==1 || ($dks>=get_module_setting("bankmin")+$min && ((get_module_setting("banklodge")>0 && get_module_pref("bankaccess")>0) || get_module_setting("banklodge")==0))) addnav("Ye Olde Bank","runmodule.php?module=dragoneggs&op=bank&op3=nav");
					else $chance=3;
				}
				if($chance==3){
					if(get_module_setting("uniopen")==1 || ($dks>=get_module_setting("unimin")+$min && ((get_module_setting("unilodge")>0 && get_module_pref("uniaccess")>0) || get_module_setting("unilodge")==0))) addnav("Bluspring's Warrior Training","runmodule.php?module=dragoneggs&op=train&op3=nav");
					else $chance=4;
				}
				if($chance==4){
					if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
						$innname=getsetting("innname", LOCATION_INN);
					}else{
						$innname=translate_inline("The Boar's Head Inn");
					}
					if(get_module_setting("innopen")==1 || ($dks>=get_module_setting("innmin")+$min && ((get_module_setting("innlodge")>0 && get_module_pref("innaccess")>0) || get_module_setting("innlodge")==0))) addnav(array("Return to %s",$innname),"runmodule.php?module=dragoneggs&op=inn&op3=nav");
					else $chance=5;
					//check if the player is a member at the inner sanctum
					if (is_module_active("sanctum")){
						if(get_module_pref("member","sanctum")>0){
							addnav("Inner Sanctum","runmodule.php?module=dragoneggs&op=sanctum&op3=nav");
							blocknav("runmodule.php?module=dragoneggs&op=inn&op3=nav");
							$chance=4;
						}
					}
				}
				if($chance==5){
					if(get_module_setting("hofopen")==1 ||($dks>=get_module_setting("hofmin")+$min && ((get_module_setting("hoflodge")>0 && get_module_pref("hofaccess")>0) || get_module_setting("hoflodge")==0))) addnav("Hall of Fame","runmodule.php?module=dragoneggs&op=historical&op3=nav");
					else $chance=6;
				}
				if($chance==6){
				if((is_module_active("djail") || is_module_active("jail")==1) && (get_module_setting("policeopen")==1 || ($dks>=get_module_setting("policemin")+$min && ((get_module_setting("policelodge")>0 && get_module_pref("policeaccess")>0) || get_module_setting("policelodge")==0)))) addnav("Jail","runmodule.php?module=dragoneggs&op=police&op3=nav");
					else $chance=7;
				}
				if($chance==7){
					if(get_module_setting("weaponsopen")==1 ||($dks>=get_module_setting("weaponsmin")+$min && ((get_module_setting("weaponslodge")>0 && get_module_pref("weaponsaccess")>0) || get_module_setting("weaponslodge")==0))) addnav("MightyE's Weapons","runmodule.php?module=dragoneggs&op=weapons&op3=nav");
					else $chance=8;
				}
				if($chance==8){
					if(get_module_setting("armoropen")==1 ||($dks>=get_module_setting("armormin")+$min && ((get_module_setting("armorlodge")>0 && get_module_pref("armoraccess")>0) || get_module_setting("armorlodge")==0))) addnav("Pegasus Armor","runmodule.php?module=dragoneggs&op=armor&op3=nav");
					else $chance=9;
				}
				if($chance==9){
					if(is_module_active("bakery") && (get_module_setting("dineropen")==1 ||($dks>=get_module_setting("dinermin")+$min && ((get_module_setting("dinerlodge")>0 && get_module_pref("dineraccess")>0) || get_module_setting("dinerlodge")==0)))) addnav("Hara's Bakery","runmodule.php?module=dragoneggs&op=diner&op3=nav");
					else $chance=10;
				}
				if($chance==10){
					if(get_module_setting("gypsyopen")==1 ||($dks>=get_module_setting("gypsymin")+$min && ((get_module_setting("gypsylodge")>0 && get_module_pref("gypsyaccess")>0) || get_module_setting("gypsylodge")==0))) addnav("Gypsy's Tent","runmodule.php?module=dragoneggs&op=gypsy&op3=nav");
					else $chance=11;
				}
				if($chance==11){
					if(is_module_active("heidi") && (get_module_setting("heidiopen")==1 || ($dks>=get_module_setting("heidimin")+$min && ((get_module_setting("heidilodge")>0 && get_module_pref("heidiaccess")>0) || get_module_setting("heidilodge")==0)))) addnav("Heidi's Place","runmodule.php?module=dragoneggs&op=heidi&op3=nav");
					else $chance=12;
				}
				if($chance==12){
					if(is_module_active("jeweler") && (get_module_setting("jewelryopen")==1 || ($dks>=get_module_setting("jewelrymin")+$min && ((get_module_setting("jewelrylodge")>0 && get_module_pref("jewelryaccess")>0) || get_module_setting("jewelrylodge")==0)))) addnav("Oliver's Jewelry","runmodule.php?module=dragoneggs&op=jewelry&op3=nav");
					else $chance=13;
				}
				if($chance==13){
					if(is_module_active("petra") && (get_module_setting("tattooopen")==1 || ($dks>=get_module_setting("tattoomin")+$min && ((get_module_setting("tattoolodge")>0 && get_module_pref("tattooaccess")>0) || get_module_setting("tattoolodge")==0)))) addnav("Petra's Tattoo Parlor","runmodule.php?module=dragoneggs&op=tattoo&op3=nav");
					else $chance=14;
				}
				if($chance==14){
					if(is_module_active("pqgiftshop") && (get_module_setting("magicopen")==1 || ($dks>=get_module_setting("magicmin")+$min && ((get_module_setting("magiclodge")>0 && get_module_pref("magicaccess")>0) || get_module_setting("magiclodge")==0)))) addnav(array("%s's Gift Shop",get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=dragoneggs&op=magic&op3=nav");
					else $chance=15;
				}
				if($chance==15){
					if(get_module_setting("animalopen")==1 ||($dks>=get_module_setting("animalmin")+$min && ((get_module_setting("animallodge")>0 && get_module_pref("animalaccess")>0) || get_module_setting("animallodge")==0))) addnav("Merick's Stables","runmodule.php?module=dragoneggs&op=animal&op3=nav");
					else $chance=16;
				}
				if($chance==16){
					if(get_module_setting("rockopen")==1 ||($dks>=get_module_setting("rockmin")+$min && ((get_module_setting("rocklodge")>0 && get_module_pref("rockaccess")>0) || get_module_setting("rocklodge")==0))) addnav("Curious Looking Rock","runmodule.php?module=dragoneggs&op=rock&op3=nav");
					else $chance=17;
				}
				if($chance==17){
					if(is_module_active("oldchurch") && (get_module_setting("churchopen")==1 ||($dks>=get_module_setting("churchmin")+$min && ((get_module_setting("churchlodge")>0 && get_module_pref("churchaccess")>0) || get_module_setting("churchlodge")==0)))) addnav("Church","runmodule.php?module=dragoneggs&op=church&op3=nav");
					else $chance=18;
				}
				if($chance==18){
					if(get_module_setting("newsopen")==1 ||($dks>=get_module_setting("newsmin")+$min && ((get_module_setting("newslodge")>0 && get_module_pref("newsaccess")>0) || get_module_setting("newslodge")==0))) addnav("Daily News","runmodule.php?module=dragoneggs&op=news&op3=nav");
					else $chance=19;
				}
				if($chance==19){
					//Block the docks links from occurring in the capital if cities is active
					if ((is_module_active("cities") && $session['user']['location']!= getsetting("villagename", LOCATION_FIELDS))||is_module_active("cities")==0) $openh=1;
					else $openh=0;
					if($openh==1&&(is_module_active("docks") || is_module_active("oceanquest")) && (get_module_setting("docksopen")==1 ||($dks>=get_module_setting("docksmin")+$min && ((get_module_setting("dockslodge")>0 && get_module_pref("docksaccess")>0) || get_module_setting("dockslodge")==0)))) addnav("Docks","runmodule.php?module=dragoneggs&op=docks&op3=nav");
					else $chance=20;
				}
				if($chance==20){
					//Block the outhouse links from occurring in the capital if cities is active
					if ((is_module_active("cities") && $session['user']['location']!= getsetting("villagename", LOCATION_FIELDS))||is_module_active("cities")==0) $openh=1;
					else $openh=0;
					if(is_module_active("outhouse") && $openh==1 && (get_module_setting("bathopen")==1 ||($dks>=get_module_setting("bathmin")+$min && ((get_module_setting("bathlodge")>0 && get_module_pref("bathaccess")>0) || get_module_setting("bathlodge")==0)))) addnav("Outhouse","runmodule.php?module=dragoneggs&op=bath&op3=nav");
					else $chance=21;
				}
				if($chance==21){
					if((is_module_active("library")||is_module_active("dlibrary")) && (get_module_setting("libraryopen")==1 ||($dks>=get_module_setting("librarymin")+$min && ((get_module_setting("librarylodge")>0 && get_module_pref("libraryaccess")>0) || get_module_setting("librarylodge")==0)))) addnav("Public Library","runmodule.php?module=dragoneggs&op=library&op3=nav");
					else $chance=22;
				}
				if($chance==22){
					addnav(array("%s Square",getsetting("villagename", LOCATION_FIELDS)),"runmodule.php?module=dragoneggs&op=town&op3=nav");
				}
				blocknav("runmodule.php?module=oldhouse");
				blocknav("village.php");
			break;
			case 5: case 6:
				output("You find yourself in a bedroom and look around.");
				$hps=$session['user']['hitpoints'];
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if (($level>6 && $chance<=3) || ($level<=6 && $chance<=2)){
					output("`n`nYou open the closet and see a strange box. You blow the dust off of it to open it.");
					output("`n`nYou find a `%gem`7!");
					$session['user']['gems']++;
					debuglog("received a gem by researching at the Old House.");
				}else{
					output("`n`nStrange voices enter your head.  You become disoriented and fall, nearly cracking your skull open.");
					output("`n`nYou `\$lose all your hitpoints except 1`7.");
					$session['user']['hitpoints']=1;
					debuglog("lost all hitpoints except 1 by researching at the Old House.");
				}
			break;
			case 7: case 8:
				output("You feel a hand brush against you.  You turn quickly but only see a glimpse of a whispy figure.");
				output("Was it a ghost? What is going on? You feel another hand touch you and suddenly you pass out.`n`n");
				$chance=e_rand(1,5);
				$level=$session['user']['level'];
				if ((($level>10 && $chance>4) || ($level<=10 && $chance>3)) && $session['user']['weapondmg']>1 && $session['user']['weapon']!="`7Rusty Pitchfork`0"){
					output("You wake some time later and notice that your `7%s`7 has disappeared! You look around for something else, but only find a `bRusty Pitchfork`b.  It's only half as good as your old weapon!",$session['user']['weapon']);
					$session['user']['weapon']="`7Rusty Pitchfork`0";
					$dmg=floor($session['user']['weapondmg']*.5);
					$session['user']['attack']-=$session['user']['weapondmg'];
					$session['user']['attack']+=$dmg;
					$session['user']['weapondmg']=$dmg;
					$value=round($session['user']['weaponvalue']*.25);
					$session['user']['weaponvalue']=$value;
					$session['user']['weapon']="`7Rusty Pitchfork`0";
					debuglog("lost their old weapon and now has a Rusty Pitchfork worth $value gold and $dmg attack by researching at the Old House.");
				}else{
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
					output("You awaken to feel your powers restored. You specialty has been revitalized.");
					debuglog("had specialty points restored by researching at the Old House.");
					//end XChrisX code
				}
			break;
			case 9: case 10:
				output("It's a rat!");
				set_module_pref("monster",3);
				addnav("Fight the Rat","runmodule.php?module=dragoneggs&op=attack");
				blocknav("runmodule.php?module=oldhouse");
				blocknav("village.php");
			break;
			case 11: case 12: case 13: case 14:
				output("As you're doing research, you find a man standing before you holding a scroll.");
				output("`n`n`@'Please tell me what this says,'`7 he asks.");
				$hps=$session['user']['hitpoints'];
				$level=$session['user']['level'];
				$chance=e_rand(1,6);
				if (($level>6 && $chance<=3) || ($level<=6 && $chance<=2)){
					output("`n`nYou examine the parchment and explain that it's probably a `iDragon Egg Hatching Ritual`i that one of the dragon sympathists have designed.");
					output("`n`nHe smiles and gives you a nod. `@'Yes, I agree.  I'm quite impressed with your knowledge.  I will accompany you on your journey.'");
					output("`n`n`7You go through some brief introductions and `bAllan the Researcher`b joins your cause.");
					if (isset($session['bufflist']['ally'])) {
						if ($session['bufflist']['ally']['type']=="allanresearch"){
							$ally=1;
						}else{
							output("`n`nRealizing that you've found help from someone new, %s`7 decides to leave.",$session['bufflist']['ally']['name']);
							$ally=0;
						}
					}else $ally=0;
					if ($ally==0){
						apply_buff('ally',array(
							"name"=>translate_inline("`7Allan the Researcher"),
							"rounds"=>50,
							"wearoff"=>translate_inline("`7Allan leaves to go do some other research."),
							"defmod"=>1.05,
							"atkmod"=>1.05,
							"survivenewday"=>1,
							"type"=>"allanresearch",
						));
						output("`n`nYou gain the help of Allan the Researcher!");
						debuglog("gained the help of ally Allan the Researcher by researching at the Old House.");
					}else{
						$session['bufflist']['ally']['rounds'] += 17;
						output("`n`nAllan the Researcher decides to help you out for another `^17 rounds`7!");
					debuglog("gained the help of ally Allan the Researcher for an additional 17 rounds by researching at the Old House.");
					}
					if (is_module_active("dlibrary")){
						if (get_module_setting("ally3","dlibrary")==0){
							set_module_setting("ally3",1,"dlibrary");
							addnews("%s`^ was the first person to meet `7Allan the Researcher`^ at the Old House.",$session['user']['name']);
						}
					}
				}else{
					output("`n`nYou have no clue what it is he's looking at and go on your way.");
				}
			break;
			case 15: case 16:
				output("You sit down at a desk and try to go through the drawers.  You look up at the mirror and watch as your name appears written in blood.`n`n");
				if ($session['user']['gems']>2){
					output("You are so disturbed by this that you `%drop 2 gems`7.");
					debuglog("lost 2 gems by researching at the Old House.");
					$session['user']['gems']-=2;
				}elseif ($session['user']['gems']==2){
					output("You are so disturbed that you `%drop all your gems`7.");
					debuglog("lost 2 gems by researching at the Old House.");
					$session['user']['gems']-=2;
				}elseif ($session['user']['gems']==1){
					output("You are so distured that you `%drop all your gems`7.");
					debuglog("lost 1 gem by researching at the Old House.");
					$session['user']['gems']--;
				}else{
					output("You look twice and notice that it's just red lipstick that magically appeared on the mirror so it doesn't bother you too much.");
				}
			break;
			case 17: case 18:
				output("You find yourself in the library of the Old House and settle down to do some serious research.`n`n");
				$chance=e_rand(1,2);
				if ($chance>1 && e_rand(1,4)<4) $chance=1;
				if ($session['user']['turns']>=$chance){
					output("You spend `^%s`@ turns`7 reading in the library and `%find a gem`7 for each turn!",$chance);
					$session['user']['turns']-=$chance;
					$session['user']['gems']+=$chance;
					debuglog("gained $chance gems by spending $chance turns researching dragon eggs at the Old House.");
				}elseif ($session['user']['turns']>0){
					output("You fall asleep on a book and wake a `@turn`7 later.  The page has some ancient symbols that you read to cast a spell.  A `%gem`7 appears! You `%gain a gem`7.");
					$session['user']['turns']--;
					$session['user']['gems']++;
					debuglog("gained a gem by spending a turn researching dragon eggs at the Old House.");
				}else{
					output("You don't find anything worthwhile.");
				}
			break;
			case 19: case 20:
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				output("You open a cabinet and look through it.");
				if ((($level>6 && $chance<=3) || ($level<=6 && $chance<=2)) && $session['user']['armor']!="`)Black Cloak`0"){
					output("You find a quality article of clothing; a new `)Black Cloak`7, nicer than your %s`7.",$session['user']['armor']);
					$session['user']['armor']="`)Black Cloak`0";
					$session['user']['armordef']++;
					$session['user']['defense']++;
					debuglog("received a Black Cloak (1 higher armor than their previous) by researching at the Old House.");
				}else{
					output("You find a very pretty moth.  It sits on your shoulder and makes you look more charming.");
					$session['user']['charm']++;
					debuglog("gained a charm while researching in the Old House.");
				}
			break;
			case 21: case 22:
				output("You walk through the house looking for anything of interest.  You see a huge table with food laid out in the dining room.");
				output("`n`nFiguring that the food must be for you, you sit down for a meal.`n`n");
				$chance=e_rand(1,4);
				if ($chance==1){
					output("You get a terrible case of stomach pains.  You `\$lose all your hitpoints except 1`7.");
					$session['user']['hitpoints']--;
					debuglog("lost all hitpoints except 1 by researching at the Old House.");
				}elseif ($chance==2){
					output("It turns out to be a nice and tasty meal.");
					if ($session['user']['turns']>0){
						output("`n`nYou gorge yourself and `@lose a turn`7.  The food turns out to be revitalizing and your hitpoints are restored to full.");
						$session['user']['hitpoints']=$session['user']['maxhitpoints'];
						$session['user']['turns']--;
						debuglog("restored hitpoints and lost a turn by researching at the Old House.");
					}else{
						output("Suddenly, you realize the turkey is dry! It ruins the whole meal for you.");
					}
				}else{
					output("You feel quite satisfied.  You `\$gain a permanent hitpoint`7!");
					$session['user']['maxhitpoints']++;
					debuglog("gained a permanent hitpoint by researching at the Old House.");
				}
			break;
			case 23: case 24:
				output("You wander into the library and hit the books.`n`nSuddenly, it all becomes very clear to you.");
				$rand=e_rand(1,2);
				if ($rand==1 && e_rand(1,3)<3) $rand=1;
				output("`n`nYou `%gain %s %s`7!",$rand,translate_inline($rand>1?"gems":"gem"));
				$session['user']['gems']+=$rand;
				debuglog("gained $rand gems by researching at the Old House.");
			break;
			case 25: case 26: case 27: case 28:
				output("You walk into the attic and see 5 witches chanting in a circle.  Something goes wrong as they cast a spell to transport a dragon egg into the room.  However, a `QHeat Vampire`7 appears to protect the egg!");
				output("`n`nThe `QHeat Vampire`7 kills the witches instantly and turns to face you!");
				set_module_pref("monster",4);
				addnav("Fight the `QHeat Vampire","runmodule.php?module=dragoneggs&op=attack");
				blocknav("runmodule.php?module=oldhouse");
				blocknav("village.php");
			break;
			case 29: case 30: case 31: case 32: case 33: case 34: case 35:
				output("You don't find anything of value.");
			break;
			case 36:
				dragoneggs_case36();
			break;
		}
	}
	addnav("Return to The Old House","runmodule.php?module=oldhouse");
	villagenav();
}
?>