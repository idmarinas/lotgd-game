<?php

function implantservoarms_getmoduleinfo(){
	$info = array(
		"name" => "Implant - Servo Arms",
		"author" => "Dan Hall, based on generic Specialty files by Eric Stevens et al",
		"version" => "2008-11-24",
		"download" => "",
		"category" => "Implants",
		"prefs" => array(
			"Implant - Skull-Servo Arms User Prefs,title",
			"battery"=>"Power left in the battery,int|100",
			"status"=>"1 for Offensive - 2 for Defensive - 0 for Off,int|0",
		),
	);
	return $info;
}

function implantservoarms_install(){
	$condition = "if (\$session['user']['specialty'] == \"SR\") {return true;} else {return false;};";
	module_addhook("village",false,$condition);
	module_addhook("choose-specialty");
	module_addhook("set-specialty");
	module_addhook("fightnav-specialties",false,$condition);
	module_addhook("apply-specialties",false,$condition);
	module_addhook("incrementspecialty",false,$condition);
	module_addhook("specialtynames");
	module_addhook("specialtymodules");
	module_addhook("specialtycolor");
	module_addhook("dragonkill");
	module_addhook("battle-victory",false,$condition);
	module_addhook("battle-defeat",false,$condition);
	module_addhook("newday",false,$condition);
	return true;
}

function implantservoarms_uninstall(){
	// Reset the specialty of anyone who had this specialty so they get to
	// rechoose at new day
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='SR'";
	db_query($sql);
	return true;
}

function implantservoarms_dohook($hookname,$args){
	global $session,$resline;

	$spec = "SR";
	$name = "Servo Arms";
	$ccode = "`&";

	switch ($hookname) {
	case "dragonkill":
		set_module_pref("battery", 0);
		set_module_pref("status", 0);
		break;
	case "choose-specialty":
		if ($session['user']['dragonkills'] < 2) {
			break;
		}
		if ($session['user']['specialty'] == "" ||
				$session['user']['specialty'] == '0') {
			addnav("$ccode$name`0","newday.php?setspecialty=$spec$resline");
			output("`5\"The `&Servo Arms`5 implant doesn't go in your brain, but in your spinal column instead.  As you can see, you get two lovely shiny metal arms, to supplement your existing ones.  You can set them to offensive or defensive stances, and can change their settings even in the middle of a fight if you like.  They're battery powered, so they have a finite life, but your master at the Dojo will give you a new battery in exchange for defeating him.\"`n`n");
		}
		set_module_pref("battery", 100);
		set_module_pref("status", 0);
		break;
	case "set-specialty":
		if($session['user']['specialty'] == $spec) {
			page_header($name);
			output("`&Wow, you've really done it this time.  Other contestants opted for the tiny little chips in their brains but not you, oh no.  You had to go for the enormous servo arms jutting out of your back, didn't you?  Well, don't blame me when you get propositioned by people with rather curious fetishes.`n`nYou can activate or deactivate your Servo Arms, or change their stance, at any time.  But remember, your battery has to last you for a whole level, so don't overdo it.");
			}
		break;
	case "specialtycolor":
		$args[$spec] = $ccode;
		break;
	case "specialtynames":
		$args[$spec] = translate_inline($name);
		break;
	case "specialtymodules":
		$args[$spec] = "implantservoarms";
		break;
	case "incrementspecialty":
		if($session['user']['specialty'] == $spec) {
			set_module_pref("battery",100+($session['user']['level']*5));
			output("`&`nYour defeated master hands you a new battery for your Servo Arms!`n");
		}
		break;
	case "fightnav-specialties":
		if($session['user']['specialty'] == $spec) {
			// Evaluate the number of rounds that the battle has lasted thus far.  Because this is only called once per click, and the user can choose to play five rounds, ten rounds or to the end of the fight, we've got to get the number of rounds by looking at the remaining rounds left in the buff we set up the last time the user clicked to fight.
			if (has_buff("servoarms")) {
				$roundsplayed = 1000 - $session['bufflist']['battery']['rounds'];
				set_module_pref("battery", get_module_pref("battery") - $roundsplayed);
				if (get_module_pref("battery") < 0){
					set_module_pref("battery",0);
					set_module_pref("status",0);
					strip_buff('servoarms');
				}
			} else {
				$roundsplayed = 0;
			}
			apply_buff('battery',array(
				"rounds"=>1000,
				"dmgmod"=>1,
			));
			$script = $args['script'];
			$battery = get_module_pref("battery");
			$status = get_module_pref("status");
			addnav(array("$ccode `bServo Arms`b",""));
			addnav(array("$ccode Battery: %s", $battery),"");
			if ($status == 0 && $battery > 0){
				addnav("$ccode Stance: Off","");
				addnav(array("$ccode Set to Offensive stance`0"),
						$script."op=fight&skill=$spec&l=1", true);
				addnav(array("$ccode Set to Defensive stance`0"),
						$script."op=fight&skill=$spec&l=2", true);
			}
			if ($status == 1 && $battery > 0){
				addnav("$ccode Stance: Offensive","");
				addnav(array("$ccode Set to Defensive stance`0"),
						$script."op=fight&skill=$spec&l=2", true);
				addnav(array("$ccode Turn Servos Off`0"),
						$script."op=fight&skill=$spec&l=0", true);
			}
			if ($status == 2 && $battery > 0){
				addnav("$ccode Stance: Defensive","");
				addnav(array("$ccode Set to Offensive stance`0"),
						$script."op=fight&skill=$spec&l=1", true);
				addnav(array("$ccode Turn Servos Off`0"),
						$script."op=fight&skill=$spec&l=0", true);
			}
		}
		break;
	case "apply-specialties":
		if($session['user']['specialty'] == $spec) {
			$skill = httpget('skill');
			$l = httpget('l');
			if ($skill==$spec){
				switch($l){
				case "1":
					if (get_module_pref("battery") > 0){
						apply_buff('servoarms',array(
							"startmsg"=>"`&Your Servo Arms swing around in front of you, ready to attack {badguy}!",
							"name"=>"`&Servo Arms",
							"atkmod"=>"1.25",
							"rounds"=>-1,
							"schema"=>"module-implantlaser"
						));
						set_module_pref("status",1);
					} else {
						output("`&Your Servo Arms batteries are DEAD!");
					}
					break;
				case "2":
					if (get_module_pref("battery") > 0){
						apply_buff('servoarms',array(
							"startmsg"=>"`&Your Servo Arms swing around in front of you, ready to defend against {badguy}!",
							"name"=>"`&Servo Arms",
							"defmod"=>"1.25",
							"rounds"=>-1,
							"schema"=>"module-implantlaser"
						));
						set_module_pref("status",2);
					} else {
						output("`&Your Servo Arms batteries are DEAD!");
					}
					break;
				case "0":
					set_module_pref("status",0);
					strip_buff('servoarms');
					break;
				}
			}
		}
		break;
	case "battle-defeat":
	case "battle-victory":
		if($session['user']['specialty'] == $spec) {
			if (has_buff("servoarms")) {
				$roundsplayed = 1000 - $session['bufflist']['battery']['rounds'];
				set_module_pref("battery", get_module_pref("battery") - $roundsplayed);
				if (get_module_pref("battery") < 0){
					set_module_pref("battery",0);
				}
				set_module_pref("status",0);
				strip_buff('servoarms');
				output("`&Your Servo Arms neatly turn themselves back behind your back, to save on battery power.`n");
			}
		}
		break;
	case "newday":
	case "village":
		set_module_pref("status",0);
		strip_buff('servoarms');
		break;
	}
	return $args;
}

function implantservoarms_run(){
}
?>
