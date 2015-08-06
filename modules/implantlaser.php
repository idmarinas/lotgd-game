<?php

function implantlaser_getmoduleinfo(){
	$info = array(
		"name" => "Implant - Skull-Mounted Laser",
		"author" => "Dan Hall, based on generic speciality files by Eric Stevens et al",
		"version" => "2008-11-22",
		"download" => "",
		"category" => "Implants",
		"prefs" => array(
			"Implant - Skull-Mounted Laser User Prefs,title",
			"primary"=>"Power left in the battery replenished at newday,int|100",
			"secondary"=>"Power left in the main, non-replacable battery,int|500",
			"status"=>"Status of the laser - on or off,int|0",
			"powerlevel"=>"Current selected power output,int|1",
		),
	);
	return $info;
}

function implantlaser_install(){
	$condition = "if (\$session['user']['specialty'] == \"HL\") {return true;} else {return false;};";
	module_addhook("choose-specialty");
	module_addhook("set-specialty");
	module_addhook("fightnav-specialties",false,$condition);
	module_addhook("apply-specialties",false,$condition);
	module_addhook("newday",false,$condition);
	module_addhook("specialtynames");
	module_addhook("specialtymodules");
	module_addhook("specialtycolor");
	module_addhook("dragonkill");
	module_addhook("battle-victory",false,$condition);
	module_addhook("battle-defeat",false,$condition);
	module_addhook("forest",false,$condition);
	module_addhook("village",false,$condition);
	module_addhook("worldnav",false,$condition);
	return true;
}

function implantlaser_uninstall(){
	// Reset the specialty of anyone who had this specialty so they get to
	// rechoose at new day
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='HL'";
	db_query($sql);
	return true;
}

function implantlaser_dohook($hookname,$args){
	global $session,$resline;

	$spec = "HL";
	$name = "Skull-Mounted Laser";
	$ccode = "`$";

	switch ($hookname) {
	case "dragonkill":
		set_module_pref("primary", 0);
		set_module_pref("secondary", 0);
		set_module_pref("status", 0);
		break;
	case "choose-specialty":
		if ($session['user']['dragonkills'] < 5) {
			break;
		}
		if ($session['user']['specialty'] == "" || $session['user']['specialty'] == '0') {
			addnav("$ccode$name`0","newday.php?setspecialty=$spec$resline");
			output("\"`5This one - goodness, it's rather heavy - actually bolts on to the side of your head, rather than going in the brain.  It's a `\$Skull-Mounted Laser`5, you see.  Most of the weight is taken up by the batteries - you get one small battery which is recharged every day, and one large battery that has to last you for your whole adventure.  It's a very, very powerful combat-orientated Implant, but you've got to keep an eye on your battery levels, or you'll be in trouble later on.\"`n`n");
		}
		break;
	case "set-specialty":
		if($session['user']['specialty'] == $spec) {
			page_header($name);
			output("`\$Not all implants are as subtle as a chip in the brain.  You learn this after examining the housebrick-sized box jutting out of the side of your head.`n`n");
			output("It's painted black, and features a small aperature on the front.  The warning symbols for Laser Radiation and Oxidizing Element are displayed in yellow triangles just above your ear.`n`n");
			output("The user manual states that the laser's output power is pretty well infinitely-adjustable - however, taking it up too high will deplete the primary battery pretty well straight away, prompting the laser to begin draining the secondary battery.`n`nThe medic's warnings about the secondary battery come back to haunt you; use it sparingly, because it has to last you for a long time.");
			clear_module_pref("primary");
			clear_module_pref("secondary");
		}
		break;
	case "specialtycolor":
		$args[$spec] = $ccode;
		break;
	case "specialtynames":
		$args[$spec] = translate_inline($name);
		break;
	case "specialtymodules":
		$args[$spec] = "implantlaser";
		break;
	case "newday":
		if ($session['user']['specialty'] == $spec) {
			output("`nThe primary battery on your Head-Mounted Laser has been charged overnight, and is now at full capacity.`n");
			$primary = ($session['user']['level']*2)+20;
			set_module_pref("primary",$primary);
			set_module_pref("status",0);
		}
		set_module_pref("status",0);
		strip_buff('headlaser');
		break;
	case "forest":
		if($session['user']['specialty'] == $spec) {
			if (get_module_pref("primary") > 0 || get_module_pref("secondary") > 0){
				addnav(array("$ccode `bSkull-Mounted Laser`b`0",""));
				addnav(array("Battery: (%s/%s)`0", get_module_pref("primary"), get_module_pref("secondary")),"");
				addnav(array("Power output: %sKw`0", get_module_pref("powerlevel")),"");
				addnav("Increase laser power`0","runmodule.php?module=implantlaser&op=inc&from=forest");
				if (get_module_pref("powerlevel")>1){
					addnav("Decrease laser power`0","runmodule.php?module=implantlaser&op=dec&from=forest");
				}
			}
		}
		break;
	case "village":
		if($session['user']['specialty'] == $spec) {
			if (get_module_pref("primary") > 0 || get_module_pref("secondary") > 0){
				addnav(array("$ccode `bSkull-Mounted Laser`b`0",""));
				addnav(array("Battery: (%s/%s)`0", get_module_pref("primary"), get_module_pref("secondary")),"");
				addnav(array("Power output: %sKw`0", get_module_pref("powerlevel")),"");
				addnav("Increase laser power`0","runmodule.php?module=implantlaser&op=inc&from=village");
				if (get_module_pref("powerlevel")>1){
					addnav("Decrease laser power`0","runmodule.php?module=implantlaser&op=dec&from=village");
				}
			}
			set_module_pref("status",0);
			strip_buff('headlaser');
		}
		break;
	case "worldnav":
		if($session['user']['specialty'] == $spec) {
			if (get_module_pref("primary") > 0 || get_module_pref("secondary") > 0){
				addnav(array("$ccode `bSkull-Mounted Laser`b`0",""));
				addnav(array("Battery: (%s/%s)`0", get_module_pref("primary"), get_module_pref("secondary")),"");
				addnav(array("Power output: %sKw`0", get_module_pref("powerlevel")),"");
				addnav("Increase laser power`0","runmodule.php?module=implantlaser&op=inc&from=worldnav");
				if (get_module_pref("powerlevel")>1){
					addnav("Decrease laser power`0","runmodule.php?module=implantlaser&op=dec&from=worldnav");
				}
			}
			set_module_pref("status",0);
			strip_buff('headlaser');
		}
		break;
	case "fightnav-specialties":
		if($session['user']['specialty'] == $spec) {
			// Evaluate the number of rounds that the battle has lasted thus far.  Because this is only called once per click, and the user can choose to play five rounds, ten rounds or to the end of the fight, we've got to get the number of rounds by looking at the remaining rounds left in the buff we set up the last time the user clicked to fight.
			if (has_buff("headlaser")) {
				$roundsplayed = 1000 - $session['bufflist']['headlaser-roundtrack']['rounds'];
				set_module_pref("primary", get_module_pref("primary") - (get_module_pref("powerlevel") * $roundsplayed));
				if (get_module_pref("primary") < 0){
					$discrepancy = get_module_pref("primary");
					$discrepancy = $discrepancy - $discrepancy - $discrepancy;
					debug ($discrepancy);
					set_module_pref("secondary", get_module_pref("secondary") - $discrepancy);
					set_module_pref("primary",0);
					if (get_module_pref("secondary") < 0){
						set_module_pref("secondary",0);
						set_module_pref("status",0);
						strip_buff('headlaser');
						strip_buff('headlaser-roundtrack');
					}
				}
			} else {
				$roundsplayed = 0;
				set_module_pref("status",0);
			}
			apply_buff('headlaser-roundtrack',array(
				"rounds"=>1000,
				"dmgmod"=>1,
			));
			$script = $args['script'];
			$primary = get_module_pref("primary");
			$secondary = get_module_pref("secondary");
			if ($primary > 0 || $secondary > 0) {
				addnav(array("$ccode `bSkull-Mounted Laser`b`0",""));
				addnav(array("Battery: %s (%s)`0", $primary, $secondary),"");
				addnav(array("Power output: %sKw`0", get_module_pref("powerlevel")),"");
				if (get_module_pref("status") == 0){
					if (($primary+$secondary) > get_module_pref("powerlevel")){
					addnav(array("$ccode &#149; Turn on laser`0"),
							$script."op=fight&skill=$spec&l=on", true);
					} else {
						addnav("Not enough battery power to fire laser","");
					}
					addnav(array("$ccode &#149; Increase laser power`0"),
							$script."op=fight&skill=$spec&l=inc", true);
					if (get_module_pref("powerlevel")>1){
						addnav(array("$ccode &#149; Decrease laser power`0"),
								$script."op=fight&skill=$spec&l=dec", true);
					}
				}
				if (get_module_pref("status") == 1){
					addnav(array("$ccode &#149; Turn off laser`0"),
							$script."op=fight&skill=$spec&l=off", true);
				}
			}
		}
		break;
	case "apply-specialties":
		if($session['user']['specialty'] == $spec) {
			$skill = httpget('skill');
			$l = httpget('l');
			if ($skill==$spec){
				switch($l){
				case "inc":
					set_module_pref("powerlevel",get_module_pref("powerlevel")+1);
					output("`\$You reach up and twiddle the knobs on your housebrick-sized cranial implant.  You have `bincreased`b your laser's output power by one kilowatt, taking it up to %s.`n",get_module_pref("powerlevel"));
					break;
				case "dec":
					set_module_pref("powerlevel",get_module_pref("powerlevel")-1);
					output("`\$You reach up and twiddle the knobs on your housebrick-sized cranial implant.  You have `bdecreased`b your laser's output power by one kilowatt, taking it down to %s.`n",get_module_pref("powerlevel"));
					break;
				case "on":
					if (get_module_pref("secondary")>0 || get_module_pref("primary")>0){
						apply_buff('headlaser',array(
							"startmsg"=>"`\$Your laser steams into life, sending a beam of burning light towards {badguy}!",
							"name"=>"`\$Head-Mounted Laser",
							"effectmsg"=>"`\${badguy} yelps in pain as the laser burns into its body, doing `^{damage}`\$ points' worth of damage!",
							"rounds"=>-1,
							"minioncount"=>1,
							"minbadguydamage"=>ceil(get_module_pref("powerlevel")/2),
							"maxbadguydamage"=>ceil(get_module_pref("powerlevel")*2),
							"schema"=>"module-implantlaser"
						));
						set_module_pref("status",1);
					} else {
						output("Your laser's batteries are DEAD!");
					}
					break;
				case "off":
					set_module_pref("status",0);
					strip_buff('headlaser');
					break;
				}
			}
		}
		break;
	case "battle-defeat":
	case "battle-victory":
		if($session['user']['specialty'] == $spec) {
			if (get_module_pref("status")==1){
				set_module_pref("status",0);
				if (has_buff("headlaser")) {
					$roundsplayed = 1001 - $session['bufflist']['headlaser-roundtrack']['rounds'];
					set_module_pref("primary", get_module_pref("primary") - (get_module_pref("powerlevel") * $roundsplayed));
					if (get_module_pref("primary") < 0){
						set_module_pref("secondary", get_module_pref("secondary") - (get_module_pref("powerlevel") * $roundsplayed));
						set_module_pref("primary",0);
						if (get_module_pref("secondary") < 0){
							set_module_pref("secondary",0);
							set_module_pref("status",0);
							strip_buff('headlaser');
							strip_buff('headlaser-roundtrack');
						}
					}
				}
				output("`n`\$Your head-mounted laser, seeing its target suitably chastised, switches off automatically.`n");
			}
			strip_buff('headlaser');
		}
		break;
	}
	return $args;
}

function implantlaser_run(){
	global $session;
	page_header("Keep playing with it like that and you'll go blind");
	switch (httpget("op")){
		case "inc":
			set_module_pref("powerlevel",get_module_pref("powerlevel")+1);
			output("You reach up and twiddle the knobs on your housebrick-sized cranial implant.  You have `bincreased`b your laser's output power by one kilowatt, taking it up to %s.",get_module_pref("powerlevel"));
			break;
		case "dec":
			set_module_pref("powerlevel",get_module_pref("powerlevel")-1);
			output("You reach up and twiddle the knobs on your housebrick-sized cranial implant.  You have `bdecreased`b your laser's output power by one kilowatt, taking it down to %s.",get_module_pref("powerlevel"));
			break;
		}
	switch (httpget("from")){
		case "forest":
			addnav("Increase laser power`0","runmodule.php?module=implantlaser&op=inc&from=forest");
			if (get_module_pref("powerlevel")>1){
				addnav("Decrease laser power`0","runmodule.php?module=implantlaser&op=dec&from=forest");
			}
			addnav("Stop fiddling with it and go back to the Jungle","forest.php");
			break;
		case "village":
			addnav("Increase laser power`0","runmodule.php?module=implantlaser&op=inc&from=village");
			if (get_module_pref("powerlevel")>1){
				addnav("Decrease laser power`0","runmodule.php?module=implantlaser&op=dec&from=village");
			}
			addnav("Stop fiddling with it and go back to the Outpost","village.php");
			break;
		case "worldnav":
			addnav("Increase laser power`0","runmodule.php?module=implantlaser&op=inc&from=worldnav");
			if (get_module_pref("powerlevel")>1){
				addnav("Decrease laser power`0","runmodule.php?module=implantlaser&op=dec&from=worldnav");
			}
			addnav("Stop fiddling with it and go back to the Map","runmodule.php?module=worldmapen&op=continue");
			break;
		}
	page_footer();
}
?>
