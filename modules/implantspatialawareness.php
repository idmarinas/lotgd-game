<?php

function implantspatialawareness_getmoduleinfo(){
	$info = array(
		"name" => "Implant - Spatial Awareness",
		"author" => "Dan Hall, based on generic speciality files by Eric Stevens et al",
		"version" => "2008-11-22",
		"download" => "fix this",
		"category" => "Implants",
		"prefs" => array(
			"Implant - Spatial Awareness User Prefs,title",
			"skill"=>"Skill points in Spatial Awareness,int|0",
			"uses"=>"Uses of spatial Awareness allowed,int|0",
		),
	);
	return $info;
}

function implantspatialawareness_install(){
	module_addhook("choose-specialty");
	module_addhook("set-specialty");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("newday");
	module_addhook("incrementspecialty");
	module_addhook("specialtynames");
	module_addhook("specialtymodules");
	module_addhook("specialtycolor");
	module_addhook("dragonkill");
	module_addhook("battle-victory");
	return true;
}

function implantspatialawareness_uninstall(){
	// Reset the specialty of anyone who had this specialty so they get to
	// rechoose at new day
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='SA'";
	db_query($sql);
	return true;
}

function implantspatialawareness_dohook($hookname,$args){
	global $session,$resline;

	$spec = "SA";
	$name = "Spatial Awareness";
	$ccode = "`Q";

	switch ($hookname) {
	case "dragonkill":
		set_module_pref("uses", 0);
		set_module_pref("skill", 0);
		break;
	case "choose-specialty":
		if ($session['user']['specialty'] == "" ||
				$session['user']['specialty'] == '0') {
			addnav("$ccode$name`0","newday.php?setspecialty=$spec$resline");
			output("`5\"This, here, is a `QSpatial Awareness`5 implant.  As you grow in strength and combat efficacy, you'll learn how to use it in combination with your own heightened senses.  This Implant works just like the old Season One implants, if that means anything to you.  It's a very simple Implant, good for beginners.  It's only good for combat use.\"`n`n");
		}
		break;
	case "set-specialty":
		if($session['user']['specialty'] == $spec) {
			page_header($name);
			output("`QInside your head is a tiny microcontroller that heightens your perception in combat.  It has a limited battery charge, but will recharge itself overnight.  As you level up in your combat skills, you'll unlock new abilities.`n`nRemember, this Implant doesn't do anything on its own, but complements your existing combat skill, so levelling up is the only way to make it more effective.");
		}
		break;
	case "specialtycolor":
		$args[$spec] = $ccode;
		break;
	case "specialtynames":
		$args[$spec] = translate_inline($name);
		break;
	case "specialtymodules":
		$args[$spec] = "implantspatialawareness";
		break;
	case "incrementspecialty":
		if($session['user']['specialty'] == $spec) {
			$new = get_module_pref("skill") + 1;
			set_module_pref("skill", $new);
			$c = $args['color'];
			$name = translate_inline($name);
			output("`n%sYou gain a level in `&%s%s to `#%s%s!",
					$c, $name, $c, $new, $c);
			$x = $new % 3;
			if ($x == 0){
				output("`n`^You gain an extra use point!`n");
				set_module_pref("uses", get_module_pref("uses") + 1);
			}else{
				if (3-$x == 1) {
					output("`n`^Only 1 more skill level until you gain an extra use point!`n");
				} else {
					output("`n`^Only %s more skill levels until you gain an extra use point!`n", (3-$x));
				}
			}
			output_notl("`0");
		}
		break;
	case "newday":
		$bonus = getsetting("specialtybonus", 1);
		if($session['user']['specialty'] == $spec) {
			$name = translate_inline($name);
			if ($bonus == 1) {
				output("`n`2For having the %s%s`2 implant, you receive `^1`2 extra %s%s`2 use for today.`n",$ccode, $name, $ccode, $name);
			} else {
				output("`n`2For having the %s%s`2 implant, you receive `^%s`2 extra %s%s`2 uses for today.`n",$ccode, $name,$bonus, $ccode,$name);
			}
		}
		$amt = (int)(get_module_pref("skill") / 3);
		if ($session['user']['specialty'] == $spec) $amt = $amt + $bonus;
		set_module_pref("uses", $amt);
		break;
	case "fightnav-specialties":
		$uses = get_module_pref("uses");
		$script = $args['script'];
		if ($uses > 0) {
			addnav(array("$ccode$name (%s points)`0", $uses),"");
			addnav(array("$ccode &#149; Requisition Search`7 (%s)`0", 1),
					$script."op=fight&skill=$spec&l=1", true);
		}
		if ($uses > 1) {
			addnav(array("$ccode &#149; Pressure Points`7 (%s)`0", 2),
					$script."op=fight&skill=$spec&l=2",true);
		}
		if ($uses > 2) {
			addnav(array("$ccode &#149; Nimble`7 (%s)`0", 3),
					$script."op=fight&skill=$spec&l=3",true);
		}
		if ($uses > 4) {
			addnav(array("$ccode &#149; StandStill`7 (%s)`0", 5),
					$script."op=fight&skill=$spec&l=5",true);
		}
		break;
	case "battle-victory":
		$name = "SA1";
		if (has_buff($name)) {
		$x = 10;
		$y = 40;
		$amount = e_rand($x,$y);
		$amount = $amount * $session['user']['level'];
		output("`Q`nUsing your powers of Spatial Awareness, you find an additional `^%s Requisition`Q dropped by less perceptive players!`0`n",$amount);
		$session['user']['gold']+=$amount;
		strip_buff($name);
		}
	break;
	case "apply-specialties":
		$skill = httpget('skill');
		$l = httpget('l');
		if ($skill==$spec){
			if (get_module_pref("uses") >= $l){
				switch($l){
				case 1:
					apply_buff('SA1', array(
						"startmsg"=>"`QYou ramp up your awareness of your surroundings.  You notice all the coins that other contestants have left lying beneath leaves, under logs, all over the area.  You resolve to pick them up, just as soon as you've dispatched this foe...",
						"name"=>"Requisition Search",
						"rounds"=>-1,
						"schema"=>"module-implantspatialawareness"
					));
					break;
				case 2:
					apply_buff('SA2',array(
						"startmsg"=>"`QYou step back, parrying your enemy's furious thrusts, and take a second or two to look it up and down, locating its weakest points...",
						"name"=>"`QPressure Points",
						"rounds"=>10,
						"wearoff"=>"`QYour concentration fades, and your knowledge of {badguy}'s pressure points seems to fade away.",
						"atkmod"=>1.2,
						"roundmsg"=>"`QYou strike {badguy} directly on a vital pressure point, causing serious damage!",
						"schema"=>"module-implantspatialawareness"
					));
					break;
				case 3:
					apply_buff('SA3',array(
						"startmsg"=>"`QYou breathe deeply, concentrate, plan a course of action that will leave your foe bewildered and unable to strike, and begin to move your feet...",
						"name"=>"`QNimble",
						"rounds"=>10,
						"defmod"=>1.4,
						"roundmsg"=>"`QYou move so fast that {badguy} can only land glancing blows!",
						"wearoff"=>"`QYour body can no longer keep up with your mind - you're exhausted, and your Nimbleness wears off.",
						"schema"=>"module-implantspatialawareness"
					));
					break;
				case 5:
					apply_buff('SA5',array(
						"startmsg"=>"`QYou stop, meditate for a moment, and open your eyes.  {badguy} is running towards you, so slowly that it's like your foe is running through oil.  You smile, and launch attack after attack while time is slowed down.",
						"name"=>"`QStandStill",
						"rounds"=>1,
						"minioncount"=>round(e_rand(5,10)),
						"minbadguydamage"=>"round(<attack>*0.5,0);",
						"maxbadguydamage"=>"round(<attack>,0);",
						"effectmsg"=>"`QYou take advantage of your speeded-up state to attack {badguy}`Q again, causing a further `^{damage} `Qdamage!",
						"wearoff"=>"`QYour concentration fades; {badguy} seems to speed up again, and lunges at you.",
						"schema"=>"module-implantspatialawareness"
					));
					break;
				}
				set_module_pref("uses", get_module_pref("uses") - $l);
			}else{
				apply_buff('SA0', array(
					"startmsg"=>"Exhausted, you try to bluff.  'I can kill you with my mind,' you call out in what you hope is a sinister voice.  {badguy} looks at you for a minute, thinking, then shrugs.  Laughing, it swings at you again.",
					"rounds"=>1,
					"schema"=>"module-implantspatialawareness"
				));
			}
		}
		break;
	}
	return $args;
}

function implantspatialawareness_run(){
}
?>
