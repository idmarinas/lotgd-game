<?php
global $session;
$op=httpget('op');
switch($type){
	case forest:
		if ($op==""){
			$session['user']['specialinc'] = "module:docks";
			output("`7You search for something to kill but find yourself standing in a pile of very soft soil.");
			output("`n`nSuddenly, you feel something wiggling under your feet. Oh my! It's Night crawlers! If you want to spend a turn digging some up you can.");
			addnav("Dig 'em up","forest.php?op=digupworms");
			addnav("Leave","forest.php?op=docksleave");
		}
		if ($op=="digupworms"){
			$session['user']['turns']--;
			output("`7You take your `^%s`7 and get ready to dig up some worms. `n`nYou find a good handful of Night Crawlers ",$session['user']['weapon']);
			if (get_module_pref("bait")==0){
				output("and put them in your pocket.  If you don't do something with them, they'll probably die by the end of the day.");
				set_module_pref("bait",1);
			}else{
				output("but you don't have anywhere to put them. That was poor planning!");
			}
			output("`n`nYou get up to leave when something sparkly catches your eye.");
			switch(e_rand(1,14)){
				case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8:
					output("You investigate to find that it's just a piece of useless tin foil.  Oh well!");
				break;
				case 9: case 10: case 11:
					output("You go over and find a bag of gold! You count out `^100 gold pieces`7!");
					$session['user']['gold']+=100;
				break;
				case 12: case 13:
					output("You go over and find a `%gem`7!");
					$session['user']['gems']++;
				break;
				case 14:
					output("You go over and find someone's money pouch! There's `^100 gold`7 and a `%gem`7 inside!");
					$session['user']['gems']++;
					$session['user']['gold']+=100;
				break;
			}
			addnav("Return to the Forest", "forest.php?php");
			$session['user']['specialinc']="";
		}
		if ($op=="docksleave"){
			output("`2`nYou head back into the forest.");
			addnav("Return to the Forest", "forest.php?php");
			$session['user']['specialinc']="";
		}
	break;
}
?>