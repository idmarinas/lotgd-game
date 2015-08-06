<?php

function implantteslaframe_getmoduleinfo(){
	$info = array(
		"name" => "Implant - Tesla Frame",
		"author" => "Dan Hall, based on generic speciality files by Eric Stevens et al",
		"version" => "2008-11-22",
		"download" => "fix this",
		"category" => "Implants",
		"prefs" => array(
			"Specialty - Tesla Frame User Prefs,title",
			"charge"=>"Current charge,int|0",
			"maxcharge"=>"Maximum charge,int|0",
		),
	);
	return $info;
}

function implantteslaframe_install(){
	module_addhook("choose-specialty");
	module_addhook("set-specialty");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("specialtynames");
	module_addhook("specialtymodules");
	module_addhook("specialtycolor");
	module_addhook("dragonkill");
	module_addhook("battle-victory");
	return true;
}

function implantteslaframe_uninstall(){
	// Reset the specialty of anyone who had this specialty so they get to
	// rechoose at new day
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='TF'";
	db_query($sql);
	return true;
}

function implantteslaframe_dohook($hookname,$args){
	global $session,$resline;

	$spec = "TF";
	$name = "Tesla Frame";
	$ccode = "`J";

	switch ($hookname) {
	case "dragonkill":
		set_module_pref("charge", 0);
		set_module_pref("maxcharge", 10);
		break;
	case "choose-specialty":
		if ($session['user']['dragonkills'] < 8) {
			break;
		}
		if ($session['user']['specialty'] == "" ||
				$session['user']['specialty'] == '0') {
			addnav("$ccode$name`0","newday.php?setspecialty=$spec$resline");
			output("`5\"Now, this combat-orientated Implant is a little different, and I can't recommend it to beginners at all.  It's called a `JTesla Frame.`5  It's not terribly useful at first, and because it relies on your opponent striking, it's very hard to use effectively.  But, if you time it just right, you can do some pretty serious damage with this Implant.  Like I say, it takes an awful lot of practice and skill to get used to it.\"`n`n");
		}
		break;
	case "set-specialty":
		if ($session['user']['dragonkills'] < get_module_setting("mindk")) {
			break;
		}
		if($session['user']['specialty'] == $spec) {
			page_header($name);
			output("`JMay as well throw away your razor - you're now covered from head to toe in stubble-length black spikes, about a fingernail's width apart.  Frankly, you look bloody scary.`n`n");
			output("The crackling electricity arcing between the spikes makes you look even more frightening.`n`nTesla Frames work by amplifying and reflecting the damage that an opponent does to you.  They charge up over several rounds, and can be discharged either halfway or completely at any point thereafter.  There's no limit on the number of times you can use this ability.`n`nThe higher the charge, the better the effect will be.  The effect only lasts a fraction of a second, and doesn't have any effect if your opponent doesn't hurt you, so watch out!");
			set_module_pref("charge",0);
			set_module_pref("maxcharge",10);
		}
		break;
	case "specialtycolor":
		$args[$spec] = $ccode;
		break;
	case "specialtynames":
		$args[$spec] = translate_inline($name);
		break;
	case "specialtymodules":
		$args[$spec] = "implantteslaframe";
		break;
	case "fightnav-specialties":
		if($session['user']['specialty'] == $spec) {
			// Evaluate the number of rounds that the battle has lasted thus far.  Because this is only called once per click, and the user can choose to play five rounds, ten rounds or to the end of the fight, we've got to get the number of rounds by looking at the remaining rounds left in the buff we set up the last time the user clicked to fight.
			if (has_buff("charge")) {
				$roundsplayed = (1000-$session['bufflist']['charge']['rounds']);
			}
			apply_buff('charge', array(
				"dmgmod"=>1,
				"rounds"=>1000,
			));
			set_module_pref("charge", get_module_pref("charge") + $roundsplayed);
			if (get_module_pref("charge") > get_module_pref("maxcharge")){
				set_module_pref("charge", get_module_pref("maxcharge"));
			}
			$charge = get_module_pref("charge");
			$maxcharge = get_module_pref("maxcharge");
			$script = $args['script'];
			addnav(array("$ccode$name (%s/%s)`0", $charge, $maxcharge),"");
			if ($charge<5) {
				addnav("5 points needed","");
			}
			if ($charge >= 5) {
				addnav(array("$ccode &#149; Partial Discharge"),
						$script."op=fight&skill=$spec&l=1", true);
			}
			if ($charge >= 10) {
				addnav(array("$ccode &#149; Full Discharge"),
						$script."op=fight&skill=$spec&l=2",true);
			}
			// if ($charge >= 5) {
				// addnav(array("$ccode &#149; Partial Discharge without guard"),
						// $script."op=fight&skill=$spec&l=3", true);
			// }
			// if ($charge >= 10) {
				// addnav(array("$ccode &#149; Full Discharge without guard"),
						// $script."op=fight&skill=$spec&l=4",true);
			// }
		}
		break;
	break;
	case "apply-specialties":
		$skill = httpget('skill');
		$l = httpget('l');
		$charge = get_module_pref("charge");
		if ($skill==$spec){
			switch($l){
			case 1:
				$charge = floor($charge/2);
				apply_buff('TFPartial', array(
					"startmsg"=>"`JYou discharge half of your Tesla Frame's power!`0",
					"name"=>"Tesla Frame",
					"rounds"=>1,
					"damageshield"=>ceil($charge/10),
					"roundmsg"=>"`JWith a sound like a thousand people clapping once in perfect time, blue flashes of electricity arc all over your body!`0",
					"effectmsg"=>"`JCrackling electricity surges into {badguy} causing {damage} points of damage!`0",
					"schema"=>"module-implantteslaframe"
				));
				break;
			case 2:
				apply_buff('TFFull', array(
					"startmsg"=>"`JYou discharge your Tesla Frame's full power!`0",
					"name"=>"Tesla Frame",
					"rounds"=>1,
					"damageshield"=>ceil($charge/10),
					"roundmsg"=>"`JWith a sound like a thousand people clapping once in perfect time, blue flashes of electricity arc all over your body!`0",
					"effectmsg"=>"`JCrackling electricity surges into {badguy} causing {damage} points of damage!`0",
					"schema"=>"module-implantteslaframe"
				));
				$charge = 0;
				break;
			case 3:
				$charge = floor($charge/2);
				apply_buff('TFPartial', array(
					"startmsg"=>"`JYou let down your guard completely, and discharge half of your Tesla Frame's power!`0",
					"name"=>"Tesla Frame",
					"rounds"=>1,
					"defmod"=>0,
					"damageshield"=>ceil($charge/10),
					"roundmsg"=>"`JWith a sound like a thousand people clapping once in perfect time, blue flashes of electricity arc all over your body!`0",
					"effectmsg"=>"`JCrackling electricity surges into {badguy} causing {damage} points of damage!`0",
					"schema"=>"module-implantteslaframe"
				));
				break;
			case 4:
				apply_buff('TFFull', array(
					"startmsg"=>"`JYou let down your guard completely, and discharge your Tesla Frame's full power!`0",
					"name"=>"Tesla Frame",
					"rounds"=>1,
					"defmod"=>0,
					"damageshield"=>ceil($charge/10),
					"roundmsg"=>"`JWith a sound like a thousand people clapping once in perfect time, blue flashes of electricity arc all over your body!`0",
					"effectmsg"=>"`JCrackling electricity surges into {badguy} causing {damage} points of damage!`0",
					"schema"=>"module-implantteslaframe"
				));
				$charge = 0;
				break;
			}
			set_module_pref("charge",$charge);
			set_module_pref("maxcharge",get_module_pref("maxcharge")+1);
		}
		break;
	}
	return $args;
}

function implantteslaframe_run(){
}
?>
