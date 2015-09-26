<?php
function dragoneggs_getmoduleinfo(){
	$info = array(
		"name"=>"Dragon Eggs Expansion",
		"version"=>"1.01",
		"author"=>"DaveS",
		"category"=>"Dragon Expansion",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1362",
		"settings"=>array(
			"Green Dragon Eggs,title",
			"notice"=>"How will players find out about the Dragon Eggs Expansion?,enum,0,No Notice,1,YoM|1",
			"NOTE: The YoM will go out the next day after installation to all players that meet the base DK number and on any day after a player has reached enough dragon kills,note",
			"mindk"=>"What is the Base dks needed to search for eggs?,int|1",
			"research"=>"How many Researches do players get a day?,int|4",
			"allopen"=>"How many system days should Research Locations be All Open?,int|0",
			"randomallopen"=>"Allow for random All Open days to occur?,enum,0,No,10,1 in 10,20,1 in 20,50,1 in 50,100,1 in 100|20",
			"retainerpay"=>"How much does the retainer pay per system day?,int|100",
			"reset"=>"Reset on Newdays or System Newdays?,enum,0,Newdays,1,System Newdays,bool|1",
			"jar"=>"How much gold more is rewarded for the jar?,int|0",
			"marbles"=>"How many marbles are in the jar?,int|0",
			"townegg"=>"Is there a town egg available?,bool|0",
			"deserter"=>"What is the name of the deserter?,text|",
			"Gem Exchange Store,title",
			"The Gem Exchange Store opens when activated by researching at Daily News,note",
			"open"=>"How many newdays does the Gem Exchange stay open?,int|24",
			"Set to 0 if you do not want the Gem Exchange to appear,note",
			"left"=>"How many newdays has the Gem Exchange been open?,int|0",
			"found"=>"Who caused the Gem Exchange to open up?,text|",
			"minlevel"=>"Minimum level to exchange gems for gold?,range,1,14,1|5",
			"minvalue"=>"Minimum exchange rate for gems?,range,100,300,50|100",
			"maxvalue"=>"Maximum exchange rate for gems?,range,350,600,50|500",
			"maxperday"=>"Maximum number of gems to exchange per day?,range,1,10,1|3",
			"Access,title",
			"Note: The Capital Village Square is always available once this dk number is reached.,note",
			"Note: Lodge Access lasts for the player's current DK.,note",
			"healmin"=>"`3Minimum DKs more than Base needed to Research Healer's Hut:,int|0",
			"heallodge"=>"`3Lodge Points needed to Research Healer's Hut:,int|0",
			"healopen"=>"`3Is this location open for all players today?,bool|0",
			"bankmin"=>"`2Minimum DKs more than Base needed to Research the Bank:,int|0",
			"banklodge"=>"`2Lodge Points needed to Research Bank:,int|0",
			"bankopen"=>"`2Is this location open for all players today?,bool|0",
			"witchmin"=>"`3Minimum DKs more than Base needed to Research the Old House:,int|0",
			"witchlodge"=>"`3Lodge Points needed to Research the Old House:,int|10",
			"witchopen"=>"`3Is this location open for all players today?,bool|0",
			"unimin"=>"`2Minimum DKs more than Base needed to Research Warrior Training:,int|1",
			"unilodge"=>"`2Lodge Points needed to Research the Warrior Training:,int|0",
			"uniopen"=>"`2Is this location open for all players today?,bool|0",
			"policemin"=>"`3Minimum DKs more than Base needed to Research the Jail:,int|1",
			"policelodge"=>"`3Lodge Points needed to Research the Jail:,int|25",
			"policeopen"=>"`3Is this location open for all players today?,bool|0",
			"innmin"=>"`2Minimum DKs more than Base needed to Research at the Inn:,int|2",
			"innlodge"=>"`2Lodge Points needed to Research at the Inn:,int|0",
			"innopen"=>"`2Is this location open for all players today?,bool|0",
			"weaponsmin"=>"`3Minimum DKs more than Base needed to Research the Weapons Store:,int|3",
			"weaponslodge"=>"`3Lodge Points needed to Research the Weapons Store:,int|0",
			"weaponsopen"=>"`3Is this location open for all players today?,bool|0",
			"dinermin"=>"`2Minimum DKs more than Base needed to Research the Hara's Bakery:,int|4",
			"dinerlodge"=>"`2Lodge Points needed to Research the Hara's Bakery:,int|0",
			"dineropen"=>"`2Is this location open for all players today?,bool|0",
			"gypsymin"=>"`3Minimum DKs more than Base needed to Research the Gypsy Tent:,int|5",
			"gypsylodge"=>"`3Lodge Points needed to Research the Gypsy Tent:,int|0",
			"gypsyopen"=>"`3Is this location open for all players today?,bool|0",
			"armormin"=>"`2Minimum DKs more than Base needed to Research the Armor Store:,int|5",
			"armorlodge"=>"`2Lodge Points needed to Research the Armor Store:,int|35",
			"armoropen"=>"`2Is this location open for all players today?,bool|0",
			"jewelrymin"=>"`3Minimum DKs more than Base needed to Research Oliver's Jewelry:,int|6",
			"jewelrylodge"=>"`3Lodge Points needed to Research Oliver's Jewelry:,int|0",
			"jewelryopen"=>"`3Is this location open for all players today?,bool|0",
			"tattoomin"=>"`2Minimum DKs more than Base needed to Research Petra's Tattoo Parlor:,int|8",
			"tattoolodge"=>"`2Lodge Points needed to Research Petra's Tattoo Parlor:,int|0",
			"tattooopen"=>"`2Is this location open for all players today?,bool|0",
			"magicmin"=>"`3Minimum DKs more than Base needed to Research Ye Ol' Gifte Shoppe:,int|10",
			"magiclodge"=>"`3Lodge Points needed to Research Ye Ol' Gifte Shoppe:,int|0",
			"magicopen"=>"`3Is this location open for all players today?,bool|0",
			"heidimin"=>"`2Minimum DKs more than Base needed to Research Heidi's Tent:,int|10",
			"heidilodge"=>"`2Lodge Points needed to Research Heidi's Place:,int|50",
			"heidiopen"=>"`2Is this location open for all players today?,bool|0",
			"gardensmin"=>"`3Minimum DKs more than Base needed to Research the Gardens:,int|15",
			"gardenslodge"=>"`3Lodge Points needed to Research the Gardens:,int|0",
			"gardensopen"=>"`3Is this location open for all players today?,bool|0",
			"animalmin"=>"`2Minimum DKs more than Base needed to Research the Stables:,int|15",
			"animallodge"=>"`2Lodge Points needed to Research Stables:,int|50",
			"animalopen"=>"`2Is this location open for all players today?,bool|0",
			"rockmin"=>"`3Minimum DKs more than Base needed to Research the Curious Looking Rock:,int|20",
			"rocklodge"=>"`3Lodge Points needed to Research the Curious Looking Rock:,int|0",
			"rockopen"=>"`3Is this location open for all players today?,bool|0",
			"churchmin"=>"`2Minimum DKs more than Base needed to Research the Church:,int|25",
			"churchlodge"=>"`2Lodge Points needed to Research the Church:,int|0",
			"churchopen"=>"`2Is this location open for all players today?,bool|0",
			"hofmin"=>"`3Minimum DKs more than Base needed to Research the HoF:,int|25",
			"hoflodge"=>"`3Lodge Points needed to Research the HoF:,int|50",
			"hofopen"=>"`3Is this location open for all players today?,bool|0",
			"newsmin"=>"`2Minimum DKs more than Base needed to Research the Daily News,int|30",
			"newslodge"=>"`2Lodge Points needed to Research the Daily News:,int|0",
			"newsopen"=>"`2Is this location open for all players today?,bool|0",
			"docksmin"=>"`3Minimum DKs more than Base needed to Research the Docks:,int|35",
			"dockslodge"=>"`3Lodge Points needed to Research the Docks:,int|50",
			"docksopen"=>"`3Is this location open for all players today?,bool|0",
			"bathmin"=>"`2Minimum DKs more than Base needed to Research the Outhouse:,int|40",
			"bathlodge"=>"`2Lodge Points needed to Research the Outhouse:,int|0",
			"bathopen"=>"`2Is this location open for all players today?,bool|0",
			"librarymin"=>"`3Minimum DKs more than Base needed to Research Library:,int|50",
			"librarylodge"=>"`3Lodge Points needed to Research Library:,int|0",
			"libraryopen"=>"`3Is this location open for all players today?,bool|0",
		),
		"prefs"=>array(
			"Dragon Egg Research,title",
			"notice"=>"Has the player received notice about the Dragon Eggs Module?,bool|0",
			"inform"=>"Has the player been informed that all locations are open today?,enum,0,N/A,1,No,2,Yes|0",
			"researches"=>"Number of researches so far:,int|0",
			"retainer"=>"Does the player have a retainer?,enum,0,No,1,Yes,2,Owed Payment|0",
			"monster"=>"Which monster are they fighting,enum,0,None,1,Fire Hound,2,Wraith,3,Rat,4,Heat Vampire,5,Rhthithc,6,Zombie,7,Green Slime,8,Lumberjack,9,Rat-Thing,10,Gastropian,11,Stalagaryth,12,Swamgrythph,13,Sheldon Boy,14,Blupe,15,Gorilla Man,16,Cultist,17,Crazed Inmate,18,Robber,19,Bear,20,Newsboy,21,Newsboy2|0",
			"quest1"=>"Which phase of the 1st quest is the player on?,range,0,6,1|0",
			"quest2"=>"Which phase of the 2nd quest is the player on?,range,0,5,1|0",
			"marblecount"=>"Has player heard the current marble count?,bool|0",
			"exchange"=>"Has the player exchanged dragon egg points for permanent hit points this DKs?,bool|0",
			"puzzlepiece"=>"Is the player looking for the missing puzzle piece?,enum,0,No,1,Yes,2,Found|0",
			"lantern"=>"Does the player have the lantern?,enum,0,No,1,Yes,2,Yes - Old|0",
			"sold"=>"How many gems has the player sold today?,range,0,10,1|0",
			"book"=>"Does the player have a book from the library?,bool|0",
			"Lodge Access,title",
			"Did the Player go to the lodge this DK and purchase access to:,note",
			"healaccess"=>"Healer's Hut?,bool|0",
			"bankaccess"=>"The Bank?,bool|0",
			"uniaccess"=>"The Warrior Training?,bool|0",
			"innaccess"=>"The Inn?,bool|0",
			"witchaccess"=>"The Old House?,bool|0",
			"hofaccess"=>" HoF?,bool|0",
			"policeaccess"=>"The Jail?,bool|0",
			"weaponsaccess"=>"The Weapons Store?,bool|0",
			"armoraccess"=>"The Armor Store?,bool|0",
			"dineraccess"=>" Hara's Bakery?,bool|0",
			"gypsyaccess"=>"The Gypsy Tent?,bool|0",
			"heidiaccess"=>" Heidi's Place?,bool|0",
			"libraryaccess"=>"The Library?,bool|0",
			"jewelryaccess"=>" Oliver's Jewelry Store?,bool|0",
			"tattooaccess"=>" Petra's Tattoo Parlor?,bool|0",
			"magicaccess"=>" Ye Ol' Gifte Shoppe?,bool|0",
			"animalaccess"=>" Merick's Stables?,bool|0",
			"gardensaccess"=>"The Gardens?,bool|0",
			"rockaccess"=>"The Curious Looking Rock?,bool|0",
			"churchaccess"=>"The Church?,bool|0",
			"newsaccess"=>"The Daily News?,bool|0",
			"docksaccess"=>"The Docks?,bool|0",
			"bathaccess"=>"The Bath House?,bool|0",
		),
		"requires"=>array(
			"dragoneggpoints"=>"1.0|Dragon Egg Points by DaveS",
		),
	);
	return $info;
}
function dragoneggs_install(){
	require_once("modules/dragoneggs/dragoneggs_install.php");
}
function dragoneggs_uninstall(){
	return true;
}
function dragoneggs_chance() {
	global $session;
	if (get_module_pref('puzzlepiece','dragoneggs')==1) $ret=100;
	else $ret=0;
	return $ret;
}
function dragoneggs_dohook($hookname,$args){
	global $session,$SCRIPT_NAME;
	$dragoneggss=get_module_pref("dragoneggss");
	$op = httpget("op");
	$op2= httpget("op2");
	require("modules/dragoneggs/dohook/$hookname.php");
	return $args;
}
function dragoneggs_runevent($type){
	global $session;
	set_module_pref("puzzlepiece",2);
	output("`^You find a puzzle piece! It seems like the one from `&Heidi's Place`^!");
	output("`n`nIf you can get a chance to go back there, perhaps you can finish that puzzle before the day is over, otherwise she may throw away the puzzle!");
	require_once("lib/forest.php");
	forest(true);
}
function dragoneggs_run(){
	include("modules/dragoneggs/dragoneggs.php");
}
function dragoneggs_case36(){
	global $session;
	if (get_module_pref("rumors","rumors")==0){
		output("You find yourself with a significant lead... You overhear a rumor!`n`n");
		$newrumor=e_rand(1,9);
		if (get_module_pref("dragoneggs","dragoneggpoints")==0 && $newrumor==2){
			$newrumor=9;
		}
		if ($newrumor==8 && is_module_active("djail")==0 && is_module_active("jail")==0) $newrumor=9;
		if ($newrumor==1) addnews("%s `4heard a rumor that there's going to a huge attack of monsters that will wound so many people that the Healer's Hut will be overrun.",$session['user']['name']);
		elseif ($newrumor==2) addnews("%s `4heard a rumor that there's going to be a shortage of warriors soon.",$session['user']['name']);
		elseif ($newrumor==3) addnews("%s `4heard a rumor that the bank is going to lose solvency and all your money will be lost.",$session['user']['name']);
		elseif ($newrumor==4) addnews("%s `4heard a rumor that Merick's Stable has too many animals and soon the streets will be full of rabid dogs and cats.",$session['user']['name']);
		elseif ($newrumor==5) addnews("%s `4heard a rumor that some new documents have revealed that the `@Green Dragon`4 is planning to attack soon.",$session['user']['name']);
		elseif ($newrumor==6) addnews("%s `4heard a rumor that the end of the world is coming!",$session['user']['name']);
		elseif ($newrumor==7) addnews("%s `4heard a rumor that a lost dragon egg is at the bath house.",$session['user']['name']);
		elseif ($newrumor==8) addnews("%s `4heard a rumor that a Vampire has been seen at the Jail and it's going to kill everyone.",$session['user']['name']);
		elseif ($newrumor==9){
			addnews("%s `4heard a rumor that there are forest creatures that are gaining power and soon nobody will be able to stop them.",$session['user']['name']);
			if ($session['user']['dragonkills']<10) $number=3;
			elseif ($session['user']['dragonkills']<20) $number=4;
			else $number=5;
			set_module_pref("progress",$number,"rumors");
		}
		set_module_pref("rumors",$newrumor,"rumors");
		require_once("modules/rumors.php");
		rumors_rumor();
	}else{
		output("You don't find anything of value.");
	}
}
function dragoneggs_navs(){
	global $session;
	$dks=$session['user']['dragonkills'];
	$min=get_module_setting("mindk");

	//Optional Modules
	if(is_module_active("oldhouse") && (get_module_setting("witchopen")==1 ||($dks>=get_module_setting("witchmin")+$min && ((get_module_setting("witchlodge")>0 && get_module_pref("witchaccess")>0) || get_module_setting("witchlodge")==0)))) addnav("Old House","runmodule.php?module=dragoneggs&op=witch&op3=nav");
	if(is_module_active("bakery") && (get_module_setting("dineropen")==1 ||($dks>=get_module_setting("dinermin")+$min && ((get_module_setting("dinerlodge")>0 && get_module_pref("dineraccess")>0) || get_module_setting("dinerlodge")==0)))) addnav("Hara's Bakery","runmodule.php?module=dragoneggs&op=diner&op3=nav");
	if(is_module_active("heidi") && (get_module_setting("heidiopen")==1 ||($dks>=get_module_setting("heidimin")+$min && ((get_module_setting("heidilodge")>0 && get_module_pref("heidiaccess")>0) || get_module_setting("heidilodge")==0)))) addnav("Heidi's Place","runmodule.php?module=dragoneggs&op=heidi&op3=nav");
	if(is_module_active("jeweler") && (get_module_setting("jewelryopen")==1 ||($dks>=get_module_setting("jewelrymin")+$min && ((get_module_setting("jewelrylodge")>0 && get_module_pref("jewelryaccess")>0) || get_module_setting("jewelrylodge")==0)))) addnav("Oliver's Jewelry","runmodule.php?module=dragoneggs&op=jewelry&op3=nav");
	if(is_module_active("petra") && (get_module_setting("tattooopen")==1 ||($dks>=get_module_setting("tattoomin")+$min && ((get_module_setting("tattoolodge")>0 && get_module_pref("tattooaccess")>0) || get_module_setting("tattoolodge")==0)))) addnav("Petra's Tattoo Parlor","runmodule.php?module=dragoneggs&op=tattoo&op3=nav");
	if(is_module_active("pqgiftshop") && (get_module_setting("magicopen")==1 ||($dks>=get_module_setting("magicmin")+$min && ((get_module_setting("magiclodge")>0 && get_module_pref("magicaccess")>0) || get_module_setting("magiclodge")==0)))) addnav(array("%s's Gift Shop",get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=dragoneggs&op=magic&op3=nav");
	if(is_module_active("oldchurch") && (get_module_setting("churchopen")==1 ||($dks>=get_module_setting("churchmin")+$min && ((get_module_setting("churchlodge")>0 && get_module_pref("churchaccess")>0) || get_module_setting("churchlodge")==0)))) addnav("Church","runmodule.php?module=dragoneggs&op=church&op3=nav");
	if((is_module_active("library")||is_module_active("dlibrary")) && (get_module_setting("libraryopen")==1 ||($dks>=get_module_setting("librarymin")+$min && ((get_module_setting("librarylodge")>0 && get_module_pref("libraryaccess")>0) || get_module_setting("librarylodge")==0)))) addnav("Library","runmodule.php?module=dragoneggs&op=library&op3=nav");
	if((is_module_active("djail") || is_module_active("jail")) && (get_module_setting("policeopen")==1 ||($dks>=get_module_setting("policemin")+$min && ((get_module_setting("policelodge")>0 && get_module_pref("policeaccess")>0) || get_module_setting("policelodge")==0)))) addnav("Jail","runmodule.php?module=dragoneggs&op=police&op3=nav");

	//Core Programs
	if(get_module_setting("gardensopen")==1 ||($dks>=get_module_setting("gardensmin")+$min && ((get_module_setting("gardenslodge")>0 && get_module_pref("gardensaccess")>0) || get_module_setting("gardenslodge")==0))) addnav("Gardens","runmodule.php?module=dragoneggs&op=gardens&op3=nav");
	if(get_module_setting("hofopen")==1 ||($dks>=get_module_setting("hofmin")+$min && ((get_module_setting("hoflodge")>0 && get_module_pref("hofaccess")>0) || get_module_setting("hoflodge")==0))) addnav("Hall of Fame","runmodule.php?module=dragoneggs&op=historical&op3=nav");
	if(get_module_setting("weaponsopen")==1 ||($dks>=get_module_setting("weaponsmin")+$min && ((get_module_setting("weaponslodge")>0 && get_module_pref("weaponsaccess")>0) || get_module_setting("weaponslodge")==0))) addnav("MightyE's Weapons","runmodule.php?module=dragoneggs&op=weapons&op3=nav");
	if(get_module_setting("armoropen")==1 ||($dks>=get_module_setting("armormin")+$min && ((get_module_setting("armorlodge")>0 && get_module_pref("armoraccess")>0) || get_module_setting("armorlodge")==0))) addnav("Pegasus Armor","runmodule.php?module=dragoneggs&op=armor&op3=nav");
	if(get_module_setting("gypsyopen")==1 ||($dks>=get_module_setting("gypsymin")+$min && ((get_module_setting("gypsylodge")>0 && get_module_pref("gypsyaccess")>0) || get_module_setting("gypsylodge")==0))) addnav("Gypsy's Tent","runmodule.php?module=dragoneggs&op=gypsy&op3=nav");
	if(get_module_setting("animalopen")==1 ||($dks>=get_module_setting("animalmin")+$min && ((get_module_setting("animallodge")>0 && get_module_pref("animalaccess")>0) || get_module_setting("animallodge")==0))) addnav("Merick's Stables","runmodule.php?module=dragoneggs&op=animal&op3=nav");
	if(get_module_setting("rockopen")==1 ||($dks>=get_module_setting("rockmin")+$min && ((get_module_setting("rocklodge")>0 && get_module_pref("rockaccess")>0) || get_module_setting("rocklodge")==0))) addnav("Curious Looking Rock","runmodule.php?module=dragoneggs&op=rock&op3=nav");
	if(get_module_setting("bankopen")==1 ||($dks>=get_module_setting("bankmin")+$min && ((get_module_setting("banklodge")>0 && get_module_pref("bankaccess")>0) || get_module_setting("banklodge")==0))) addnav("Bank","runmodule.php?module=dragoneggs&op=bank&op3=nav");
	if(get_module_setting("uniopen")==1 ||($dks>=get_module_setting("unimin")+$min && ((get_module_setting("unilodge")>0 && get_module_pref("uniaccess")>0) || get_module_setting("unilodge")==0))) addnav("Bluspring's Warrior Training","runmodule.php?module=dragoneggs&op=train&op3=nav");
	if(get_module_setting("newsopen")==1 ||($dks>=get_module_setting("newsmin")+$min && ((get_module_setting("newslodge")>0 && get_module_pref("newsaccess")>0) || get_module_setting("newslodge")==0))) addnav("Daily News","runmodule.php?module=dragoneggs&op=news&op3=nav");

	//Sanctum & Inn Check
	if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
		$innname=getsetting("innname", LOCATION_INN);
	}else{
		$innname=translate_inline("The Boar's Head Inn");
	}
	if (is_module_active("sanctum")){
		if(get_module_pref("member","sanctum")>0) addnav("Inner Sanctum","runmodule.php?module=dragoneggs&op=sanctum");
		elseif(get_module_setting("innopen")==1 ||($dks>=get_module_setting("innmin")+$min && ((get_module_setting("innlodge")>0 && get_module_pref("innaccess")>0) || get_module_setting("innlodge")==0))) addnav(array("%s",$innname),"runmodule.php?module=dragoneggs&op=inn&op3=nav");
	}elseif(get_module_setting("innopen")==1 ||($dks>=get_module_setting("innmin")+$min && ((get_module_setting("innlodge")>0 && get_module_pref("innaccess")>0) || get_module_setting("innlodge")==0))) addnav(array("%s",$innname),"runmodule.php?module=dragoneggs&op=inn&op3=nav");

	//Have to block the Healer,Outhouse, & Docks links from occurring in the capital if cities.php is active
	if ((is_module_active("cities") && $session['user']['location']!= getsetting("villagename", LOCATION_FIELDS))||is_module_active("cities")==0){
		if(get_module_setting("healopen")==1 ||($dks>=get_module_setting("healmin")+$min && ((get_module_setting("heallodge")>0 && get_module_pref("healaccess")>0) || get_module_setting("heallodge")==0))) addnav("Healer's Hut","runmodule.php?module=dragoneggs&op=hospital&op3=nav");	
		if(is_module_active("outhouse") && (get_module_setting("bathopen")==1 ||($dks>=get_module_setting("bathmin")+$min && ((get_module_setting("bathlodge")>0 && get_module_pref("bathaccess")>0) || get_module_setting("bathlodge")==0)))) addnav("Outhouse","runmodule.php?module=dragoneggs&op=bath&op3=nav");
		if((is_module_active("docks") || is_module_active("oceanquest")) && (get_module_setting("docksopen")==1 ||($dks>=get_module_setting("docksmin")+$min && ((get_module_setting("dockslodge")>0 && get_module_pref("docksaccess")>0) || get_module_setting("dockslodge")==0)))) addnav("Docks","runmodule.php?module=dragoneggs&op=docks&op3=nav");
	}
	addnav(array("%s Square",getsetting("villagename", LOCATION_FIELDS)),"runmodule.php?module=dragoneggs&op=town&op3=nav");
}
function dragoneggs_colors(){
	rawoutput("<small><small>");
	output("`n`n`c`\$`bColor Code Reminder Chart for Dragon Egg Research`b`c");
	output("If the Navigation Link color is:");
	output("`n`@`bGreen`b: You can research there right away.");
	output("`n`^`bYellow`b: Requires a minimum of Dragon Kills that you haven't achieved yet.");
	output("`n`!`bBlue`b: You need an Investigation Pass from the Hunter's Lodge.");
	output("`n`Q`bOrange`b: You will need both more Dragon kills AND a pass from the Lodge.");
	output("`n`&`bWhite`b: Special: The location is available for all players to visit today.");
	rawoutput("<big><big>");
}
?>