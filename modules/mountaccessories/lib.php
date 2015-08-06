<?php

/*
Required functions and features:
acc Editor with hooks for other modules (simple pref unserializer/editor).
Stables navigation page which lists all available accs for player's Mount.
Function to apply effects from the addons that the user has purchased (include hooks for other modules).
New Day function which incorporates the above function.
/*

/*
=======================================================
GET MASTER LIST OF MOUNT ACCESSORIES
Returns arrays for every accessory default.
=======================================================
*/

function get_default_accs_list() {
	$accs = unserialize(get_module_setting("accessories", "mountaccessories"));
	if (!is_array($accs)) {
		$accs = array();
		set_module_setting("accessories", serialize($accs), "mountaccessories");
	}
	$accs = unserialize(get_module_setting("accessories", "mountaccessories"));
	return $accs;
}

/*
=======================================================
GET ACCESSORIES FOR ANY GIVEN MOUNT
Returns arrays for every accessory intended for a specific Mount.
=======================================================
*/

function get_mount_accs_list($mountid) {
	$accs = unserialize(get_module_setting("accessories", "mountaccessories"));
	debug($accs);
	foreach($accs AS $acc => $details){
		if ($details['mountid']==$mountid){
			$mountaccs[$acc] = $details;
		}
	}
	debug($mountaccs);
	return $mountaccs;
}

/*
=======================================================
GET PLAYER ACCESSORY LIST
Returns arrays for every accessory for the given player.
Rather than assigning a list of ID's to the player, the entire accessory is duplicated.
This uses a little more disk space but makes it possible to affect a player's individual accessory - allowing, for example, damage to accessories.
=======================================================
*/

function get_player_acc_list($userid=false) {
	global $session;
	$accs = unserialize(get_module_pref("accessories", "mountaccessories", $userid));
	if (!is_array($accs)) {
		$accs = array();
		set_module_pref("accessories", serialize($accs), "mountaccessories", $userid);
	}
	$accs=unserialize(get_module_pref("accessories", "mountaccessories", $userid));
	return $accs;
}

/*
=======================================================
GET ACCESSORY DETAILS
Returns full info array for a given accessory in a player's pref.
Returns False if the player does not have the accessory.
=======================================================
*/

function get_player_acc($acc, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$playeraccs = get_player_acc_list($userid);
	debug($acc);
	debug($playeraccs);
	debug("Are they set?");
	if (!isset($playeraccs[$acc])){
		return false;
	}
	return($playeraccs[$acc]);
}

/*
*******************************************************
GIVE ACCESSORY
Gives the given accessory to the given player.
Returns False if the accessory is not installed, not intended for the player's mount, or if the player already has the accessory.
*******************************************************
*/

function give_accessory($acc, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	if (!isset($playeraccs[$acc])){
		$defaultaccs = get_default_accs_list();
		if (isset($defaultaccs[$acc])){
			$playeraccs[$acc] = $defaultaccs[$acc];
			set_module_pref("accessories", serialize($playeraccs), "mountaccessories", $userid);
			return($playeraccs[$acc]);
		} else {
			return false;
		}
	}
}

/*
*******************************************************
TAKE MONEY
Takes the player's gold and/or gems.
Returns false if the accessory is not installed, or if the user doesn't have enough resources.
*******************************************************
*/

function mountaccessories_takemoney($acc, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$defaultaccs = get_default_accs_list();
	if (isset($defaultaccs[$acc])){
		$goldcost = $defaultaccs[$acc]['goldcost'];
		$gemcost = $defaultaccs[$acc]['gemcost'];
		if ($session['user']['gold'] >= $goldcost && $session['user']['gems'] >= $gemcost){
			$session['user']['gold'] -= $goldcost;
			$session['user']['gems'] -= $gemcost;
			return true;
		} else {
			debug("Not enough money, returning false");
			return false;
		}
	} else {
		debug("This accessory doesn't exist, returning false");
		return false;
	}
}

/*
*******************************************************
APPLY ACCESSORY
Applies effects of the selected Mount accessory.  Standard functionality applies buffs - turns are only applied at newday.
Includes a hook for other accessory effects to be applied.
*******************************************************
*/

function apply_accessory($acckey, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$acc = get_player_acc($acckey, $userid);
	if (isset($acc['buffname'])){
		$name = $acc['buffname'];
	}
	if (isset($acc['buffrounds'])){
		$rounds = $acc['buffrounds'];
	} else {
		$rounds = 0;
	}
	if (isset($acc['buffwearoffmsg'])){
		$wearoff = $acc['buffwearoffmsg'];
	}
	if (isset($acc['buffeffectmsg'])){
		$effectmsg = $acc['buffeffectmsg'];
	}
	if (isset($acc['buffnodmgmsg'])){
		$effectnodmgmsg = $acc['buffnodmgmsg'];
	}
	if (isset($acc['buffeffectfailmsg'])){
		$effectfailmsg = $acc['buffeffectfailmsg'];
	}
	if (isset($acc['buffatkmod'])){
		$atkmod = $acc['buffatkmod'];
	} else {
		$atkmod = 1;
	}
	if (isset($acc['buffdefmod'])){
		$defmod = $acc['buffdefmod'];
	} else {
		$defmod = 1;
	}
	if (isset($acc['buffinvulnerable'])){
		$invulnerable = $acc['buffinvulnerable'];
	}
	if (isset($acc['buffregen'])){
		$regen = $acc['buffregen'];
	}
	if (isset($acc['buffminioncount'])){
		$minioncount = $acc['buffminioncount'];
	}
	if (isset($acc['buffminbadguydamage'])){
		$minbadguydamage = $acc['buffminbadguydamage'];
	}
	if (isset($acc['buffmaxbadguydamage'])){
		$maxbadguydamage = $acc['buffmaxbadguydamage'];
	}
	if (isset($acc['buffmingoodguydamage'])){
		$mingoodguydamage = $acc['buffmingoodguydamage'];
	}
	if (isset($acc['buffmaxgoodguydamage'])){
		$maxgoodguydamage = $acc['buffmaxgoodguydamage'];
	}
	if (isset($acc['bufflifetap'])){
		$lifetap = $acc['bufflifetap'];
	}
	if (isset($acc['buffdamageshield'])){
		$damageshield = $acc['buffdamageshield'];
	}
	if (isset($acc['buffbadguydmgmod'])){
		$badguydmgmod = $acc['buffbadguydmgmod'];
	} else {
		$badguydmgmod = 1;
	}
	if (isset($acc['buffbadguyatkmod'])){
		$badguyatkmod = $acc['buffbadguyatkmod'];
	} else {
		$badguyatkmod = 1;
	}
	if (isset($acc['buffbadguydefmod'])){
		$badguydefmod = $acc['buffbadguydefmod'];
	} else {
		$badguydefmod = 1;
	}
	apply_buff($acckey, array(
		"name"=>$name,
		"rounds"=>$rounds,
		"wearoff"=>$wearoff,
		"effectmsg"=>$effectmsg,
		"effectnodmgmsg"=>$effectnodmgmsg,
		"effectfailmsg"=>$effectfailmsg,
		"atkmod"=>$atkmod,
		"defmod"=>$defmod,
		"invulnerable"=>$invulnerable,
		"regen"=>$regen,
		"minioncount"=>$minioncount,
		"minbadguydamage"=>$minbadguydamage,
		"maxbadguydamage"=>$maxbadguydamage,
		"mingoodguydamage"=>$mingoodguydamage,
		"maxgoodguydamage"=>$maxgoodguydamage,
		"lifetap"=>$lifetap,
		"damageshield"=>$damageshield,
		"badguydmgmod"=>$badguydmgmod,
		"badguyatkmod"=>$badguyatkmod,
		"badguydefmod"=>$badguydefmod,
		)
	);
	modulehook("mountaccessories_apply_accessory", $acc);
	return true;
}

/*
*******************************************************
APPLY ALL ACCESSORIES
Applies effects of all Mount accessories.  Used at newday.
*******************************************************
*/

function apply_all_accessories($userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$accs = get_player_acc_list($userid);
	foreach($accs AS $acc => $details){
		apply_accessory($acc, $userid);
		$session['user']['turns'] += $details['turns'];
		if (isset($details['newdaymsg'])){
			output_notl("%s",$details['newdaymsg']);
		}
	}
}

/*
*******************************************************
STRIP ACCESSORIES
Removes Accessories, strips associated Buffs.  Includes a hook to remove effects of modules intended to work with Mount Accessories.
Use when player is selling a Mount.
*******************************************************
*/

function strip_accessories() {
	global $session;
	$accs = get_player_acc_list($userid);
	foreach($accs AS $acc => $details){
		strip_buff($acc);
		debug("Stripping Accessories");
		debug($acc);
		modulehook("mountaccessories_strip_accessories", $acc);
	}
	$blank = array();
	set_module_pref("accessories", serialize($blank), "mountaccessories");
}

?>