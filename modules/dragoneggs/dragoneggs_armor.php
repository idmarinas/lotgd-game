<?php
function dragoneggs_armor(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Pegasus Armor");
	output("`c`b`%Pegasus Armor`b`c`5");
	$open=get_module_setting("armoropen");
	if ($open==0 && $session['user']['dragonkills']<get_module_setting("armormin") && get_module_setting("armorlodge")>0 && get_module_pref("armoraccess")==0){
		output("You don't have enough `@Green Dragon Kills`5 to research here.  Once you do, you will need to purchase access through the lodge. Please check back frequently to see if you qualify. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && $session['user']['dragonkills']<get_module_setting("armormin")+get_module_setting("mindk")){
		output("You don't have enough `@Green Dragon Kills`5 to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif ($open==0 && get_module_setting("armorlodge")>0 && get_module_pref("armoraccess")==0){
		output("You will need to purchase access through the Lodge to research here. Feel free to search other locations as there are others that are available for you to research.");
		dragoneggs_colors();
	}elseif (get_module_pref("researches")>=get_module_setting("research")){
		output("You're out of research turns for today.");
	}else{
		output("You decide to look for Dragon Eggs at Pegasus Armor.`n`n");
		$rumor36=0;
		if (is_module_active("rumors")){
			if($session['user']['dragonkills']>=get_module_setting("mindk","rumors")+get_module_setting("mindk")) $rumor36=1; 
		}
		if($rumor36==1) $case=e_rand(1,36);
		else $case=e_rand(1,35);
		increment_module_pref("researches",1);
		switch($case){
		//switch(7){
			case 1: case 2: 
				output("You are looking around at some of the antique pieces of armor when your elbow bumps a crystal visor.  You watch as it appears to tumble to the ground in slow motion.");
				if ($session['user']['gold']>=500){
					output("`^<a href=\"runmodule.php?module=dragoneggs&op=armor1\">`n`n`c`^<<`%CRASH`^>>`5`c`n</a>",true);
					addnav("","runmodule.php?module=dragoneggs&op=armor1");
				}else output("`n`n`c`^<<`%CRASH`^>>`5`c`n");
				output("You look around but nobody noticed!! Seems like you should exit... stage left... NOW!");
				blocknav("armor.php");
			break;
			case 3: case 4:
				$level=$session['user']['level'];
				$chance=e_rand(1,5);
				if ((($level>4 && $chance<=4) || ($level<=4 && $chance<=2)) && $session['user']['gold']>10){
					output("You see a `^1 gold piece`5 on the ground.  Woo hoo!");
					$session['user']['gold']++;
					debuglog("found a gold while researching dragon eggs at Pegasus Armor.");
				}else{
					if ($session['user']['gold']>250){
						output("You look at some of the ancient belt buckles and decide you may want to buy something.  When you reach for your wallet you notice that something's wrong.");
						output("You open it up and count... you're missing `^250 gold`5!!");
						$session['user']['gold']-=250;
						debuglog("lost 250 gold while researching dragon eggs at Pegasus Armor.");
					}else{
						output("You see a cool looking piece of metal plate and decide it might be worth researching. You take out your wallet and it's empty!");
						output("Some urchin must have stolen all your money.");
						$session['user']['gold']=0;
						debuglog("lost all money while researching dragon eggs at Pegasus Armor.");
					}
				}
			break;
			case 5: case 6:
				output("You don't find anything useful and you're starting to get frustrated.");
				if ($session['user']['gems']>1){
					output("`#'I can teach you something useful.  Give me 2 gems and you've got a deal,'`5 says `#Pegasus`5.");
					output("`n`nWill you give her `%2 gems`5 for 'something useful'?");
					addnav("Give 2 gems","runmodule.php?module=dragoneggs&op=armor5");
				}else{
					output("`n`n`#Pegasus`5 feels bad for you and tosses you a rock... it's a `%gem`5!!");
					$session['user']['gems']++;
					debuglog("got a gem while researching dragon eggs at Pegasus Armor.");
				}
			break;
			case 7: case 8:
				$previous= strpos($session['user']['armor'],"`)Sunglasses `^& ")!==false ? 1 : 0;
				if ($previous==0){
					output("`#Pegasus`5 approaches you as you search her shop.  `#'I have something that you might find useful,'`5 she says as she pulls out some very cool sunglasses.");
					output("`#'I've been pretty unhappy since I started wearing these.  Perhaps you'd like them?'`5 she says as she offers them to you.");
					addnav("Take the Sunglasses","runmodule.php?module=dragoneggs&op=armor7");
				}else{
					if (e_rand(1,2)==1){
						output("`#Pegasus`5 gives you a `%gem`5 that she found in the back room.");
						$session['user']['gems']++;
						debuglog("got a gem while researching dragon eggs at Pegasus Armor.");
					}else{
						output("`#Pegasus`5 is about to give you a `%gem`5 when she changes her mind.  Sometimes you never know how your fortune is going to go.");
					}
				}
			break;
			case 9: case 10:
				output("`#'Fortune Cookie?'`5 offers `#Pegasus`5.");
				output("`n`nWhy not?");
				addnav("Take a Fortune Cookie","runmodule.php?module=dragoneggs&op=armor9");
			break;
			case 11: case 12:
				output("`#Pegasus`5 tries to entice you to spend some more money in her store.  `#'Take a marble from the jar.  If it's black, you'll win `^200 gold`#.'");
				addnav("Take A Marble","runmodule.php?module=dragoneggs&op=armor11");
				blocknav("armor.php");
				blocknav("village.php");
			break;
			case 13: case 14:
				output("You look in the bottom drawer and see a scrap of paper covering something.  It's a `%gem`5!!");
				$session['user']['gems']++;
				debuglog("got a gem while researching dragon eggs at Pegasus Armor.");
			break;
			case 15: case 16: case 17: case 18:
				page_header("Somewhere Else");
				output("You walk to the back of the shop and turn just in time to see a club coming down to hit you on the head. When you wake up, you find you're Somewhere Else.");
				increment_module_pref("researches",-1);
				$dks=$session['user']['dragonkills'];
				$min=get_module_setting("mindk");
				$chance=e_rand(1,21);
				//output("`n`nChance=%s`n`n",$chance);
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
					if((is_module_active("djail") || is_module_active("jail")) && (get_module_setting("policeopen")==1 || ($dks>=get_module_setting("policemin")+$min && ((get_module_setting("policelodge")>0 && get_module_pref("policeaccess")>0) || get_module_setting("policelodge")==0)))) addnav("Jail","runmodule.php?module=dragoneggs&op=police&op3=nav");
					else $chance=7;
				}
				if($chance==7){
					if(get_module_setting("weaponsopen")==1 ||($dks>=get_module_setting("weaponsmin")+$min && ((get_module_setting("weaponslodge")>0 && get_module_pref("weaponsaccess")>0) || get_module_setting("weaponslodge")==0))) addnav("MightyE's Weapons","runmodule.php?module=dragoneggs&op=weapons&op3=nav");
					else $chance=8;
				}
				if($chance==8){
					if(is_module_active("oldhouse") && (get_module_setting("witchopen")==1 || ($dks>=get_module_setting("witchmin")+$min  && ((get_module_setting("witchlodge")>0 && get_module_pref("witchaccess")>0) || get_module_setting("witchlodge")==0)))) addnav("Old House","runmodule.php?module=dragoneggs&op=witch&op3=nav");
					else $chance=9;
				}
				if($chance==9){
					if(is_module_active("bakery") && (get_module_setting("dineropen")==1 || ($dks>=get_module_setting("dinermin")+$min && ((get_module_setting("dinerlodge")>0 && get_module_pref("dineraccess")>0) || get_module_setting("dinerlodge")==0)))) addnav("Hara's Bakery","runmodule.php?module=dragoneggs&op=diner&op3=nav");
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
				blocknav("armor.php");
				blocknav("village.php");
			break;
			//here
			case 19: case 20:
				output("You look at some of the armor and realize that it would REALLY chafe to have to wear it.");
				$chance=e_rand(1,9);
				$level=$session['user']['level'];
				if (($level>10 && $chance<=4) || ($level<=10 && $chance<=3)){
					output("Luckily, it wouldn't fit because it's too small.");
				}else{
					output("`n`nJust thinkin about it makes you cringe.  You're `iCursed`i!!");
					if ($session['bufflist']['blesscurse']['atkmod']==0.8) {
						$session['bufflist']['blesscurse']['rounds'] += 5;
						debuglog("increased their curse by 5 rounds by researching at Pegasus Armor.");
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
						debuglog("received a curse by researching at Pegasus Armor.");
					}
				}
			break;
			case 21: case 22: case 23: case 24:
				if ($session['user']['armordef']>15){
					output("`#'I'll give you `^13,000 gold`# for your %s`#,'`5 says `#Pegasus`5.",$session['user']['armor']);
					addnav("Sell your Armor","runmodule.php?module=dragoneggs&op=armor21");
				}else{
					if (e_rand(1,5)==1){
						output("`#Pegasus`5 takes a look at your %s`5 and tells you that it can be improved really easily.",$session['user']['armor']);
						output("`n`nShe takes your armor from you, goes into the back room for a couple of minutes, and comes back out and hands it back to you.  It doesn't seem different, but you know that it's better by 1!");
						$session['user']['defense']++;
						$session['user']['armordef']++;
						debuglog("improved armor by 1 by researching at Pegasus Armor.");
					}else{
						output("`#Pegasus`5 tells you that she likes your armor.  You strike up a nice conversation and feel happy.");
						if (isset($session['bufflist']['happy'])) {
							$session['bufflist']['happy']['rounds'] += 5;
						}else{
							apply_buff('happy',
								array("name"=>translate_inline("Happy"),
									"rounds"=>10,
									"wearoff"=>translate_inline("`0You're not as happy."),
									"defmod"=>1.1,
									"roundmsg"=>translate_inline("`0You are happy."),
								)
							);
						}
						debuglog("received a happy buff by researching at Pegasus Armor.");
					}
				}
			break;
			case 25: case 26: case 27: case 28:
				$array=array(0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,10,10,10,10,10,10,10,10);
				if ($session['user']['armordef']>1 && $session['user']['armordef']<29){
					$offer=$array[$session['user']['armordef']];
					output("`#Pegasus`5 talks to you in confidence. `#'I have a really interesting book.  It's worth a nice price. However, I can't give it to you for free.  If you give me your armor, I'll give you the book.'");
					output("`5`n`nYou consider it carefully.  The book is worth `^%s`% gems`5.  Will you accept the offer?",$offer);
					addnav("Trade your Armor","runmodule.php?module=dragoneggs&op=armor25&op2=$offer");
				}else{
					$session['user']['gold']+=50;
					output("`#Pegasus`5 takes a look at your shoddy armor and tosses you `^50 gold`5.  `#'Go buy something nicer'`5 she tells you.");
					debuglog("gained 50 gold by researching at Pegasus Armor.");
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
	addnav("Return to Pegasus Armor","armor.php");
	villagenav();
}
?>