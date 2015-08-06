<?php

function implantchemicalpack_getmoduleinfo(){
	$info = array(
		"name" => "Implant - Chemical Pack",
		"author" => "Dan Hall, based on generic speciality files by Eric Stevens et al",
		"version" => "2009-07-05",
		"download" => "",
		"category" => "Implants",
		"prefs" => array(
			"Implant - Chemical Pack User Prefs,title",
			"charge"=>"Chemical Pack charge points,int|0",
			"maxcharge"=>"Maximum charge points,int|1000",
			"rounds"=>"Temporary number that helps calculate charge amount,int|0",
		),
	);
	return $info;
}

function implantchemicalpack_install(){
	module_addhook("choose-specialty");
	module_addhook("set-specialty");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("specialtynames");
	module_addhook("specialtymodules");
	module_addhook("specialtycolor");
	module_addhook("startofround");
	module_addhook("endofround");
	module_addhook("endofpage");
	return true;
}

function implantchemicalpack_uninstall(){
	// Reset the specialty of anyone who had this specialty so they get to
	// rechoose at new day
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='CP'";
	db_query($sql);
	return true;
}

function implantchemicalpack_dohook($hookname,$args){
	global $session,$resline;
	
	$spec = "CP";
	$name = "Chemical Pack";
	$ccode = "`2";

	switch ($hookname) {
	case "choose-specialty":
		if ($session['user']['specialty'] == "" || $session['user']['specialty'] == '0') {
			addnav("Chemical Pack","newday.php?setspecialty=CP$resline");
			output("`5\"This is your basic `2Chemical Pack`5 implant.  This one is a Combat-orientated Implant, and doesn't have any use outside of a combat situation.  It's basically a set of three little vials inside your head, with doses of morphine, adrenaline, and another little secret.  It's powered by endorphins and adrenaline, so the more you get hurt, and the longer you go without medical help, the more you'll be able to use the Implant.  The effects are quite weak, but it's at its most useful when you're in desperate need of it.\"`n`n");
		}
		break;
	case "set-specialty":
		if($session['user']['specialty'] == "CP") {
			page_header("Chemical Pack");
			output("`2Inside your head is a rather curious little set of three different chemical vials.  One will increase your attack power, one will improve your defensive power, and one will allow you to regenerate some hitpoints.`n`nThe more you use this Implant, the more potent its effects will be.");
			set_module_pref("charge", 0);
			set_module_pref("maxcharge", 1000);
		}
		break;
	case "specialtycolor":
		$args[$spec] = $ccode;
		break;
	case "specialtynames":
		$args[$spec] = translate_inline($name);
		break;
	case "specialtymodules":
		$args[$spec] = "implantchemicalpack";
		break;
	case "endofround":
		$damage = $session['user']['maxhitpoints']-$session['user']['hitpoints'];
		$charge = round(($damage/$session['user']['maxhitpoints'])*100);
		if ($charge > 0){
			increment_module_pref("charge",$charge);
			if (get_module_pref("charge") > get_module_pref("maxcharge")) set_module_pref("charge",get_module_pref("maxcharge"));
		}
		break;
	case "fightnav-specialties":
		if($session['user']['specialty'] == $spec) {
			$charge = get_module_pref("charge");
			$maxcharge = get_module_pref("maxcharge");
			$script = $args['script'];
			$barnav="<table cellpadding='0' cellspacing='0'><tr><td>";
			
			if ($charge < 800){
				$pct = ($charge/800)*100;
				$nonpct = 100-$pct;
				$barnav.="<table style='border: solid 1px #000000' bgcolor='#cc0000' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#ffff00'></td><td width='$nonpct'></td></tr></table>";
				$barnav.="<table style='border: solid 1px #000000' bgcolor='#cc0000' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#ffff00'></td><td width='$nonpct'></td></tr></table>";
			} else {
				$barnav.="<table style='border: solid 1px #000000' bgcolor='#00ff00' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td></td></tr></table>";
				$barnav.="<table style='border: solid 1px #000000' bgcolor='#00ff00' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td></td></tr></table>";
			}
			if ($charge < 1000){
				$pct = ($charge/1000)*100;
				$nonpct = 100-$pct;
				$barnav.="<table style='border: solid 1px #000000' bgcolor='#cc0000' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#ffff00'></td><td width='$nonpct'></td></tr></table>";
			} else {
				$barnav.="<table style='border: solid 1px #000000' bgcolor='#00ff00' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td></td></tr></table>";
			}
			if ($charge < 2000){
				$pct = ($charge/2000)*100;
				$nonpct = 100-$pct;
				if ($maxcharge >= 2000){
					$barnav.="<table style='border: solid 1px #000000' bgcolor='#cc0000' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#ffff00'></td><td width='$nonpct'></td></tr></table>";
					$barnav.="<table style='border: solid 1px #000000' bgcolor='#cc0000' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#ffff00'></td><td width='$nonpct'></td></tr></table>";
				} else {
					$barnav.="<table style='border: solid 1px #000000' bgcolor='#555555' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#999999'></td><td width='$nonpct'></td></tr></table>";
					$barnav.="<table style='border: solid 1px #000000' bgcolor='#555555' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#999999'></td><td width='$nonpct'></td></tr></table>";
				}
			} else {
				$barnav.="<table style='border: solid 1px #000000' bgcolor='#00ff00' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td></td></tr></table>";
			}
			if ($charge < 2500){
				$pct = ($charge/2500)*100;
				$nonpct = 100-$pct;
				if ($maxcharge >= 2500){
					$barnav.="<table style='border: solid 1px #000000' bgcolor='#cc0000' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#ffff00'></td><td width='$nonpct'></td></tr></table>";
				} else {
					$barnav.="<table style='border: solid 1px #000000' bgcolor='#555555' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#999999'></td><td width='$nonpct'></td></tr></table>";
				}
			} else {
				$barnav.="<table style='border: solid 1px #000000' bgcolor='#00ff00' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td></td></tr></table>";
			}
			if ($charge < 6000){
				$pct = ($charge/6000)*100;
				$nonpct = 100-$pct;
				if ($maxcharge >= 6000){
					$barnav.="<table style='border: solid 1px #000000' bgcolor='#cc0000' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#ffff00'></td><td width='$nonpct'></td></tr></table>";
				} else {
					$barnav.="<table style='border: solid 1px #000000' bgcolor='#555555' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct' bgcolor='#999999'></td><td width='$nonpct'></td></tr></table>";
				}
			} else {
				$barnav.="<table style='border: solid 1px #000000' bgcolor='#00ff00' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td></td></tr></table>";
			}
			$barnav.="</tr></td></table>";
			addnav(array("Chemical Pack (%s/%s)", $charge, $maxcharge),"");
			addnav(array("%s",$barnav),"",true);
			
			if ($charge >= 800) {
				if (!has_buff("CP1") && !has_buff("CP4")) {
					addnav(array("Adrenaline Lv 1 (800)"),$script."op=fight&skill=$spec&l=1");
				}
				if (!has_buff("CP2") && !has_buff("CP5")) {
					addnav(array("Painkillers Lv 1 (800)"),$script."op=fight&skill=$spec&l=2");
				}
			}
			if ($charge >= 1000) {
				if (!has_buff("CP3") && !has_buff("CP6")) {
					addnav(array("StimPack Lv 1 (1000)"),$script."op=fight&skill=$spec&l=3");
				}
			}
			if ($charge >= 2000) {
				if (!has_buff("CP4") && !has_buff("CP1")) {
					addnav(array("Adrenaline Lv 2 (2000)"),$script."op=fight&skill=$spec&l=4");
				}
				if (!has_buff("CP5") && !has_buff("CP2")) {
					addnav(array("Painkillers Lv 2 (2000)"),$script."op=fight&skill=$spec&l=5");
				}
			}
			if ($charge >= 2500) {
				if (!has_buff("CP6") && !has_buff("CP3")) {
					addnav(array("StimPack Lv 2 (2500)"),$script."op=fight&skill=$spec&l=6");
				}
			}
			if ($charge >= 6000) {
				if (!has_buff("CP7")) {
					addnav(array("Chemical High (6000)"),$script."op=fight&skill=$spec&l=7");
				}
			} 
		}
		break;
	// case "fightnav-specialties":
		// if($session['user']['specialty'] == $spec) {
			// $charge = get_module_pref("charge");
			// $maxcharge = get_module_pref("maxcharge");
			// $script = $args['script'];
			// addnav(array("Chemical Pack (%s/%s)", $charge, $maxcharge));
		// }
		// break;
	case "apply-specialties":
		$skill = httpget('skill');
		$l = httpget('l');
		if ($skill==$spec){
			$incrementmaxcharge = round(get_module_pref("charge")/50);
			set_module_pref("maxcharge", get_module_pref("maxcharge")+$incrementmaxcharge);
			switch($l){
			case 1:
				apply_buff('CP1', array(
					"startmsg"=>"`2You wriggle your eyebrows in the manner prescribed by the Implant Doctor, and feel a rush of adrenaline discharged from a vial inside your head.",
					"name"=>"`2Adrenaline Rush",
					"rounds"=>5,
					"atkmod"=>1.1,
					"roundmsg"=>"Thanks to your Adrenaline Rush, your attacks are more powerful!",
					"wearoff"=>"You feel the effects of the Adrenaline fade away.",
					"schema"=>"module-implantchemicalpack"
				));
				set_module_pref("charge", get_module_pref("charge") - 800);
				break;
			case 2:
				apply_buff('CP2',array(
					"startmsg"=>"`2You twitch your nose just as the Implant Doctor showed you, and suddenly everything goes a little bit numb.",
					"name"=>"`2Painkillers",
					"rounds"=>5,
					"defmod"=>1.1,
					"roundmsg"=>"{badguy}'s blows don't seem to hurt nearly as much...",
					"wearoff"=>"The effects of the painkillers have worn off.",
					"schema"=>"module-implantchemicalpack"
				));
				set_module_pref("charge", get_module_pref("charge") - 800);
				break;
			case 3:
				apply_buff('CP3',array(
					"startmsg"=>"`2You wriggle your ears just as the Implant Doctor showed you, and suddenly you feel a warm, healing tingle run through your body.",
					"name"=>"`2Stimpack",
					"rounds"=>5,
					"regen"=>"ceil(<maxhitpoints>/30);",
					"effectmsg"=>"Your StimPack heals you for {damage} points.",
					"wearoff"=>"The StimPack effects have worn off.",
					"schema"=>"module-implantchemicalpack"
				));
				set_module_pref("charge", get_module_pref("charge") - 1000);
				break;
			case 4:
				apply_buff('CP4', array(
					"startmsg"=>"`2You wriggle your eyebrows in the manner prescribed by the Implant Doctor, and feel a rush of adrenaline discharged from a vial inside your head.",
					"name"=>"`2Adrenaline Rush lv2",
					"rounds"=>7,
					"atkmod"=>1.2,
					"roundmsg"=>"Thanks to your Adrenaline Rush, your attacks are more powerful!",
					"wearoff"=>"You feel the effects of the Adrenaline fade away.",
					"schema"=>"module-implantchemicalpack"
				));
				set_module_pref("charge", get_module_pref("charge") - 2000);
				break;
			case 5:
				apply_buff('CP5',array(
					"startmsg"=>"`2You twitch your nose just as the Implant Doctor showed you, and suddenly everything goes a little bit numb.",
					"name"=>"`2Painkillers lv2",
					"rounds"=>7,
					"defmod"=>1.2,
					"roundmsg"=>"{badguy}'s blows don't seem to hurt nearly as much...",
					"wearoff"=>"The effects of the painkillers have worn off.",
					"schema"=>"module-implantchemicalpack"
				));
				set_module_pref("charge", get_module_pref("charge") - 2000);
				break;
			case 6:
				apply_buff('CP6',array(
					"startmsg"=>"`2You wriggle your ears just as the Implant Doctor showed you, and suddenly you feel a warm, healing tingle run through your body.",
					"name"=>"`2Stimpack lv2",
					"rounds"=>7,
					"regen"=>"ceil(<maxhitpoints>/15)",
					"effectmsg"=>"Your StimPack heals you for {damage} points.",
					"wearoff"=>"The StimPack effects have worn off.",
					"schema"=>"module-implantchemicalpack"
				));
				set_module_pref("charge", get_module_pref("charge") - 2500);
				break;
			case 7:
				apply_buff('CP7',array(
					"startmsg"=>"`@You release every vial inside your Chemical Pack, and an odd feeling of euphoria washes over you...",
					"name"=>"`@Total Chemical Release",
					"rounds"=>10,
					"atkmod"=>1.4,
					"defmod"=>1.4,
					"regen"=>"ceil(<maxhitpoints>/10)",
					"effectmsg"=>"Your StimPack heals you for {damage} points.",
					"roundmsg"=>"{badguy} cowers under your insane shower of blows, trying desperately to get a punch in!",
					"wearoff"=>"The StimPack effects have worn off.",
					"schema"=>"module-implantchemicalpack"
				));
				set_module_pref("charge", get_module_pref("charge") - 6000);
				break;
			}
		}
		break;
	}
	return $args;
}

function implantchemicalpack_run(){
}
?>
