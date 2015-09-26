<?php

function dlibrary_getmoduleinfo(){
	$info = array(
		"name"=>"Dragon Egg Library",
		"author"=>"DaveS",
		"version"=>"1.0",
		"category"=>"Dragon Expansion",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1362",
		"settings"=>array(
			"Library Settings,title",
			"libraryloc"=>"Location of the Dragon Egg Library,location|".getsetting("villagename", LOCATION_FIELDS),
			"Note: If you have a different library installed this will show up as links in the library.,note",
			"ally1"=>"Has someone encountered Emily Ratcliff?,bool|0",
			"ally2"=>"Has someone encountered Rex the Dog?,bool|0",
			"ally3"=>"Has someone encountered Allan the Researcher?,bool|0",
			"ally4"=>"Has someone encountered Zithria the Gypsy?,bool|0",
			"ally5"=>"Has someone encountered Graveyard Greg?,bool|0",
			"ally6"=>"Has someone encountered Andy Arrow?,bool|0",
			"ally7"=>"Has someone encountered Ryan Dean?,bool|0",
			"ally8"=>"Has someone encountered Sir Tarascon?,bool|0",
			"ally9"=>"Has someone encountered Boulder Billings?,bool|0",
			"ally10"=>"Has someone encountered Jack DeQuin?,bool|0",
			"ally11"=>"Has someone encountered Spot the Stray?,bool|0",
			"ally12"=>"Has someone encountered Professor Ottoman?,bool|0",
		),
		"requires"=>array(
			"dragoneggs"=>"1.0|Dragon Eggs Expansion by DaveS",
		),
	);
	return $info;
}
function dlibrary_install(){
	module_addhook("village");
	if (is_module_active("library")) module_addhook("footer-library");
	return true;
}
function dlibrary_uninstall(){
	return true;
}
function dlibrary_dohook($hookname,$args){
	global $session;
	$op = httpget('op');
	switch ($hookname){
		case "village":
			if (is_module_active("library")==0 && $session['user']['location']==get_module_setting("libraryloc") && $session['user']['dragonkills']>=get_module_setting("mindk","dragoneggs")){
				tlschema($args['schemas']['tavernnav']);
				addnav($args['tavernnav']);
				tlschema();
				addnav("Dragon Egg Library","runmodule.php?module=dlibrary&op=enter");
			}
		break;
		case "footer-library":
			if ($op=="enter" && $session['user']['dragonkills']>=get_module_setting("mindk","dragoneggs")){
				output("`n`n`2You notice a section of the library dedicated to finding out more about Dragon Eggs. You don't even need a library card to go over there.");
				addnav("Branches");
				addnav("Dragon Egg Basics","runmodule.php?module=dlibrary&op=enter");
			}
		break;
		}
	return $args;
}
function dlibrary_run(){
	global $session;
	$op = httpget('op');
	if (is_module_active("library")) $library="library";
	else{
		$library="dlibrary";
	}
	page_header(array("%s Public Library",get_module_setting("libraryloc",$library)));

	if ($op=="enter"){
		if (is_module_active("library")==0){
			output("`b`c`^Library`b`c`n`2");
			output("The Public Library has some very nice periodicals and research tombs.  You feel a sense of comfort every time you come here.");
			output("`n`nIn addition, there's a wall dedicated to people that have helped the destroy the Dragon Eggs.`n`n");
		}else{
			output("`2There's a wall dedicated to people that have helped the destroy the Dragon Eggs.`n`n");
		}
		$counta=0;
		for ($i=1;$i<=12;$i++) {
			$counta += get_module_setting("ally".$i);
		}
		if ($counta==0){
			output("Currently, the wall is empty.");
		}else{
			addnav("Examine the Wall","runmodule.php?module=dlibrary&op=wall");
			output("You can look at the pictures on the walls and read a small bio under each person.");
		}
		$open="";
		if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
			$innname=getsetting("innname", LOCATION_INN);
		}else{
			$innname="The Boar's Head Inn";
		}
		if (is_module_active("pqgiftshop")) $gift=get_module_setting('gsowner','pqgiftshop')."'s Gift Shop";
		else $gift="Gift Shop";
		$array=array("","heal","bank","uni","inn","witch","hof","police","weapons","armor","diner","gypsy","heidi","library","jewelry","tattoo","magic","animal","gardens","rock","church","news","docks","bath");
		$name=array("","Healer's Hut","Ye Olde Bank","Bluspring's Warrior Training",$innname,"Old House","Hall of Fame","Jail","MightyE's Weapons","Pegasus Armor","Hara's Bakery","Ze Gypsy Tent","Heidi's Place","Library","Oliver's Jewelry","Petra's Tattoo Parlor",$gift,"Merick's Stables","Gardens","Curious Looking Rock","Church","Daily News","The Docks","Outhouse");
		$count=0;
		for ($i=1;$i<=23;$i++) {
			$loc=$array[$i];
			$place=$name[$i];
			if (get_module_setting($loc."open","dragoneggs")==1 || (get_module_setting($loc."min","dragoneggs")==0 && get_module_setting($loc."lodge","dragoneggs")==0)){
				$open.="`n".$place;
				$count++;
			}
		}
		$open.="`n".getsetting("villagename", LOCATION_FIELDS);
		if ($open!=""){
			if ($count==23){
				if (get_module_setting("mindk","dragoneggs")>0){
					output("`n`n`@A bulletin announces:`n`n`c `&`bALL locations are open for research by all warriors today with at least `^%s `@Dragon Kills`&!`b`c`n",get_module_setting("mindk","dragoneggs"));
				}else{
					output("`n`n`@A bulletin announces:`n`n`c `&`bALL locations are open for research by all warriors today!`b`c`n");
				}
			}else{
				if (get_module_setting("mindk","dragoneggs")>0){
					output("`n`n`@A bulletin announces that the following locations are open for research by all warriors today with at least `^%s `@Dragon Kills`&:`c`&%s`c",get_module_setting("mindk","dragoneggs"),$open);
				}else{
					output("`n`n`@A bulletin announces that the following locations are open for research by all warriors today:`c`&%s`c",$open);
				}
			}
		}else output("`n`n`@There are no additional locations available to search today.");
		if (is_module_active("library")) addnav("Return to Main Hall","runmodule.php?module=library&op=enter");
	}
	if ($op=="wall"){
		output("`b`c`^Allies of the Kingdom`2`b`c`n");
		output("`i`cNote: You can only have one ally helping you at any one time.  If a new ally joins you the old one will leave.`i`c`n");
		//`1 = Emily
		if (get_module_setting("ally1")==1){
			output("`c`1`bEmily Ratcliff`b`n");
			output("Emily is a young but energetic adventurer.  She has been arrested for breaking and entering, but defends herself by saying it's part of her 'research'.`c`n");
		}
		//`q = Rex
		if (get_module_setting("ally2")==1){
			output("`c`q`bRex the Dog`b`n");
			output("He's a loyal and faithful dog.  If he's helping you, you can feel reassured that you're under the protection of a good friend.`c`n");
		}
		//`2 = Allan
		if (get_module_setting("ally3")==1){
			output("`c`2`bAllan the Researcher`b`n");
			output("Allan claims that he knows what to do to save the kingdom but it's going to take more research.  If only he had more time...`c`n");
		}
		//`@ = Zithria
		if (get_module_setting("ally4")==1){
			output("`c`@`bZithria the Gypsy`b`n");
			output("Claiming to have more and greater powers than that 'Hack' at the Gypsy Seer's Tent, Zithria sees a future where the kingdom can be saved.`c`n");
		}
		//`) = Greg
		if (get_module_setting("ally5")==1){
			output("`c`)`bGraveyard Greg`b`n");
			output("Although he's a little rough on the edges, nobody can swing a shovel like Graveyard Greg!`c`n");
		}
		//`% = Andy
		if (get_module_setting("ally6")==1){
			output("`c`%`bAndy Arrow`b`n");
			output("To call him long-winded is a bit of a compliment. For some reason, the people of the kingdom have taken a liking to him.`c`n");
		}
		//`Q = Ryan
		if (get_module_setting("ally7")==1){
			output("`c`Q`bRyan Dean`b`n");
			output("He's a rough player with a freshly-healing tattoo of a star on his arm. Some have taken to wonder whether it's more than just a symbol.`c`n");
		}
		//`# = Sir Tarascon
		if (get_module_setting("ally8")==1){
			output("`c`#`bSir Tarascon`b`n");
			output("A highly respected citizen, his brawn is reknowned and his heart is great.  The monsters of the forest tremor with fear when he is seen protecting the city.`c`n");
		}
		//`3 = Boulder Billings
		if (get_module_setting("ally9")==1){
			output("`c`3`bBoulder Billings`b`n");
			output("A large man with a penchance for a good drink and adventure. He likes to seek adventure in the caves of the kingdom.`c`n");
		}
		//`4 = Jack DeQuin
		if (get_module_setting("ally10")==1){
			output("`c`4`bJack DeQuin`b`n");
			output("The largest man in the kingdom is Jack DeQuin.  You don't tug on his cape. You don't pull off his mask.  You don't mess around with Jack.`c`n");
		}
		//`5 = Spot the Stray
		if (get_module_setting("ally11")==1){
			output("`c`5`bSpot the Stray`b`n");
			output("If you can get him to trust you, he'll fight with you for the rest of the day.`c`n");
		}
		//`& = Professor Ottoman
		if (get_module_setting("ally12")==1){
			output("`c`&`bProfessor Ottoman`b`n");
			output("The Professor is the kind of person you can depend upon.  He'll be around forever. Well, sometimes maybe it'd be nice if he'd go away.  He's quite persistent!`c`n");
		}
		addnav("Return to Main Hall","runmodule.php?module=$library&op=enter");
	}
villagenav();
page_footer();
}
?>