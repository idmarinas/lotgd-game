<?php
	module_addhook("battle-victory");
	module_addhook("charstats");
	module_addhook("dragonkill");
	module_addhook("footer-healer");
	module_addhook("footer-train");
	module_addhook("footer-bank");
	module_addhook("footer-stables");
	module_addhook("footer-hof");
	module_addhook("footer-gypsy");
	module_addhook("footer-outhouse");
	if (is_module_active("jail")) module_addhook("footer-jail");
	else module_addhook("footer-djail");
	if (is_module_active("jeweler")) module_addhook("footer-jeweler");
	else module_addhook("footer-armor");
	
	//Thanks to kickme for help with this creature installation
	$creaturename=translate_inline(array("","Dragon Sympathizer Initiate","Dragon Sympathizer Entrant","Low Dragon Sympathizer","Apprentice Dragon Sympathizer","Steward Dragon Sympathizer","Warden Dragon Sympathizer","Master Dragon Sympathizer","Outer Circle Dragon Sympathizer","Inner Circle Dragon Sympathizer","Dragon Sympathizer Assassin","Bishop Dragon Sympathizer","Deacon Dragon Sympathizer","Dragon Sympathizer High Master","Dragon Sympathizer Grandmaster","Dragon Sympathizer High Priest","Dragon Sympathizer High Priestess"));
	$creatureweapon=translate_inline(array("","Fists","Scarred Fists","Small Knife","Small Dagger","Sharp Knife","Razor Sharp Knife","Razor Sharp Dagger","Blood Knife","Razor Blood Knife","Assassin's Dagger","Jeweled Blood Dagger","Ceremonial Dagger","Ceremonial Blood Dagger","Poisoned Dagger","Spear of the Dragon","Sword of the Dragon"));
	$creaturelose=translate_inline(array("","Now I will never become a full Dragon Sympathizer!","You haven't won; you've delayed the inevitable!","My sacrifice is nothing compared to the sacrifice of your Kingdom!","My training is over. And soon so shall be yours!","My service is over. Your pain is just beginning.","The Green Dragon will never be defeated! Never!!","I will be avenged. You have not won!","You find a dragon-watching textbook in his backpack.","My blood is on your hands. It will not wash off!","I reveal no secrets before I die. None!","I give you no words and no respect.","The Green Dragon will not perish though I do!","My faith carries me through death. Does yours? ","Your death comes next. I have foreseen it!","Do you think the poison will kill you? I leave you that question.","An egg will hatch soon! A Green Dragon will be here!","My death is a sacrifice and it just strengthens the Green Dragon!"));
	$creaturegold=array("",36,97,148,162,198,234,268,302,336,369,402,435,467,499,531,563);
	$creatureexp=array("",14,24,34,45,55,66,77,89,101,114,127,141,156,172,189,207);
	$creaturehealth=array("",10,21,32,43,53,64,74,84,94,105,115,125,135,145,155,166);
	$creatureattack=array("",1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31);
	$creaturedefense=array("",1,3,4,6,7,8,10,11,13,14,15,17,18,20,21,22);
	if (get_module_setting("level1")==0){
		for ($i=1;$i<=16;$i++) {
			$name=$creaturename[$i];
			$weapon=mysql_real_escape_string($creatureweapon[$i]);
			$lose=mysql_real_escape_string($creaturelose[$i]);
			$gold=$creaturegold[$i];
			$exp=$creatureexp[$i];
			$health=$creaturehealth[$i];
			$attack=$creatureattack[$i];
			$defense=$creaturedefense[$i];
			$sql = "INSERT INTO ".db_prefix("creatures")." (creaturename, creaturelevel, creatureweapon, creaturelose, creaturewin, creaturegold, creatureexp, creaturehealth, creatureattack, creaturedefense, createdby, forest, graveyard) VALUES ('$name', $i, '$weapon', '$lose', NULL, $gold, $exp, $health, $attack, $defense,'DaveS', 1, 0)";
			db_query($sql);
			$id = db_insert_id();
			if ($id > 0){
				set_module_setting("level".$i,$id);
				output("`i`@Set ID for `2%s`@ to `^%s`n`i",$name,$id);
			}else{
				output("`4Failed to Set ID for %s!`n",$name);
			}
		}
	}
	return true;
?>