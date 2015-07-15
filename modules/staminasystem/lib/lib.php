<?php

/*
Altered core files to make the Stamina system work,based on 1.1.1:

battle.php
Added two hooks, one at the start of each round and one at the end

newday.php
Commented out portions of the code pertaining to spending DK points on forest fights
Commented out "Turns for today set to [whatever]"
*/

/*
=======================================================
GET DEFAULT ACTION LIST
Returns arrays for every Action default.
=======================================================
*/

function get_default_action_list() {
	$actions = unserialize(get_module_setting("actionsarray", "staminasystem"));
	if (!is_array($actions)) {
		$actions = array();
		set_module_setting("actionsarray", serialize($actions), "staminasystem");
	}
	$actions = unserialize(get_module_setting("actionsarray", "staminasystem"));
	return $actions;
}

/*
=======================================================
GET PLAYER ACTION LIST
Returns arrays for every action for the given player.
=======================================================
*/

function get_player_action_list($userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$actions = unserialize(get_module_pref("actions", "staminasystem", $userid));
	if (!is_array($actions)) {
		$actions = array();
		set_module_pref("actions", serialize($actions), "staminasystem", $userid);
	}
	$actions=unserialize(get_module_pref("actions", "staminasystem", $userid));
	return $actions;
}

/*
=======================================================
GET ACTION DETAILS
Returns full info array for a given Action in a player's inventory.
Also sets default values if the player has not yet performed that action.
Returns False if the action is not installed.
=======================================================
*/

function get_player_action($action, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$playeractions=unserialize(get_module_pref("actions", "staminasystem", $userid));
	//Check to see if this action is set for this player, and if not, set it
	if (!isset($playeractions[$action])){
		$defaultactions = get_default_action_list();
		if (isset($defaultactions[$action])){
			$playeractions[$action] = $defaultactions[$action];
			$playeractions[$action]['level'] = 0;
			$playeractions[$action]['dkexp'] = 0;
			$playeractions[$action]['naturalcost'] = $playeractions[$action]['maxcost'];
			set_module_pref("actions", serialize($playeractions), "staminasystem", $userid);
			return($playeractions[$action]);
		} else {
			return false;
		}
	} else {
		return($playeractions[$action]);
	}
}

/*
*******************************************************
INSTALL ACTION
Used in modules' Install fields, this sets the default values for this Action.
*******************************************************
*/

function install_action($actionname, $action){
	global $session;
	$defaultactions = get_default_action_list();
	$defaultactions[$actionname] = $action;
	set_module_setting("actionsarray",serialize($defaultactions),"staminasystem");
	return true;
}

/*
*******************************************************
UNINSTALL ACTION
Cleans up all data pertaining to an action.  Use this in your module's Uninstall function.
*******************************************************
*/

function uninstall_action($actionname) {
	//Remove information from the actions array
	$defaultactions = get_default_action_list();
	unset($defaultactions[$actionname]);
	set_module_setting("actionsarray",serialize($defaultactions),"staminasystem");
	//Now remove the action from each user's modulepref
	$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
	$results = db_query($sql);
	for ($i=0; $i<db_num_rows($results);$i++){
		$row = db_fetch_assoc($results);
		$playeractions = unserialize(get_module_pref("actions","staminasystem",$row['acctid']));
		unset($playeractions[$actionname]);
		set_module_pref("actions",serialize($playeractions),"staminasystem",$row['acctid']);
	}
	return true;
}

/*
*******************************************************
SET A BUFF
Temporarily increase or reduce the cost of and/or experience gained from performing an action.
*******************************************************
*/

function apply_stamina_buff($referencename, $buff, $userid=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$bufflist = unserialize(get_module_pref("buffs", "staminasystem", $userid));
	$bufflist[$referencename] = $buff;
	set_module_pref("buffs", serialize($bufflist), "staminasystem", $userid);
}

/*
*******************************************************
CALCULATE ACTION COST
Returns the cost of performing an action, taking buffs into account.
*******************************************************
*/

function stamina_calculate_buffed_cost($action, $userid=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$active_action_buffs = stamina_get_active_buffs($action, $userid);
	$actiondetails = get_player_action($action, $userid);
	$naturalcost = $actiondetails['naturalcost'];
	$buffedcost = $naturalcost;
	if (is_array($active_action_buffs)){
		foreach($active_action_buffs as $key => $values){
			$buffedcost = $buffedcost * $values['costmod'];
		}
	}
	return $buffedcost;
}

/*
*******************************************************
CALCULATE EXPERIENCE GAIN
Returns the experience gained for the given action, taking buffs into account
*******************************************************
*/

function stamina_calculate_buffed_exp($action, $userid=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$active_action_buffs = stamina_get_active_buffs($action, $userid);
	$actiondetails = get_player_action($action, $userid);
	$startingexp = $actiondetails['expperrep'];
	$buffedexp = $startingexp;
	if (is_array($active_action_buffs) && $active_action_buffs){
		foreach($active_action_buffs as $buff => $values){
			$buffedexp = $buffedexp * $values['expmod'];
		}
	}
	return $buffedexp;
}

/*
*******************************************************
GET ACTIVE BUFFS
Returns an array of buffs that relate to a particular action.
*******************************************************
*/

function stamina_get_active_buffs($action, $userid=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	
	$bufflist = unserialize(get_module_pref("buffs", "staminasystem", $userid));
	
	if (is_array($bufflist)) {
		foreach($bufflist as $buff => $values){
			if ($values['action'] == $action || $values['action']=="Global"){
				$active_action_buffs[$buff] = $values;
			}
		}
	}
	return($active_action_buffs);
}

/*
*******************************************************
ADVANCE BUFFS
Removes a round from buffs related to the given action, then removes the buff from the array if rounds are zero.
Also outputs round and wearoff messages.
*******************************************************
*/

function stamina_advance_buffs($action, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$bufflist = unserialize(get_module_pref("buffs", "staminasystem", $userid));
	if (is_array($bufflist)){
		foreach($bufflist as $buff => $values){
			if ($values['action'] == $action || $values['action']=="Global"){
				output_notl("%s`n",stripslashes($values['roundmsg']));
				$values['rounds']--;
				if ($values['rounds']==0){
					output_notl("%s`n",stripslashes($values['wearoffmsg']));
					unset($bufflist[$buff]);
				} else {
					$bufflist[$buff]=$values;
				}
			}
		}
	}
	if (count($bufflist)!=0){
		set_module_pref("buffs", serialize($bufflist), "staminasystem", $userid);
	} else {
		set_module_pref("buffs", "array()", "staminasystem", $userid);
	}
	return true;
}

/*
*******************************************************
STRIP A BUFF
Removes a Stamina buff.
*******************************************************
*/

function strip_stamina_buff($buff, $userid=false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$bufflist = unserialize(get_module_pref("buffs", "staminasystem", $userid));
	if (is_array($bufflist)){
		unset($bufflist[$buff]);
		set_module_pref("buffs", serialize($bufflist), "staminasystem", $userid);
	}
}

/*
*******************************************************
REMOVE ALL BUFFS
Empties the player's Buffs array.  Used at newday.
*******************************************************
*/

function stamina_strip_all_buffs($userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	set_module_pref("buffs", "array()", "staminasystem", $userid);
	return true;
}

/*
*******************************************************
GET DISPLAY COST
Returns a percentage of the player's total Stamina that is used when performing this action, by default to two decimal places.
*******************************************************
*/

function stamina_getdisplaycost($action, $precision=2, $userid=false){
	global $session;
	$costval = stamina_calculate_buffed_cost($action, $userid);
	$total = get_stamina(4, $userid);
	$costpct = round(($costval/$total)*100, $precision);
	return $costpct;
}

/*
*******************************************************
TAKE ACTION COST
Calculates buffs, removes stamina, and returns the amount taken.
*******************************************************
*/

function stamina_take_action_cost($action, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$totalcost = stamina_calculate_buffed_cost($action, $userid);
	removestamina($totalcost, $userid);
	return $totalcost;
}

/*
*******************************************************
AWARD EXPERIENCE
Calculates buffs, awards experience, returns experience awarded.
*******************************************************
*/

function stamina_award_exp($action, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$totalexp = stamina_calculate_buffed_exp($action, $userid);
	$actionlist = get_player_action_list($userid);
	$actionlist[$action]['exp'] += $totalexp;
	set_module_pref("actions",serialize($actionlist),"staminasystem",$userid);
	return $totalexp;
}


/*
*******************************************************
PROCESS ACTION
Calculates buffs, awards experience, removes cost, advances buffs, returns Stamina used and Experience gained.
*******************************************************
*/

function process_action($action, $userid=false) {
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$info_to_return = array("points_used" => 0, "exp_earned" => 0);
	$info_to_return['points_used']  = stamina_take_action_cost($action, $userid);
	$info_to_return['exp_earned']  = stamina_award_exp($action, $userid);
	stamina_advance_buffs($action, $userid);
	return $info_to_return;
}

//
// GET STAMINA VALUES
// Returns the current Stamina values for the player.
// Syntax:
// get_stamina(type, realvalue, userid);
/*
Type:
0 = Red
1 (default) = Amber
2 = Green
3 = Total
4 = Starting value (will not return as percentage)

Realvalue:
false (default) = Returns a percentage value of total.
true = Returns actual value.

Example usage:

$stamina = get_stamina();
Will return a percentage of the amber stamina value for the current player, so that the module author can adjust the outcome based on how knackered the player is.
You can return the red, green and total values too.  Example:

$red = get_stamina(0);
Returns the red value as a percentage.

$green = get_stamina(2, 1);
Returns the green value in terms of actual Stamina points.

$total = get_stamina(3, 1);
Returns the player's total Stamina points.

*/

function get_stamina($type = 1, $realvalue = false, $userid = false) {
	global $session;
	
	if ($userid === false) $userid = $session['user']['acctid'];
	
	$totalstamina = get_module_pref("stamina", "staminasystem", $userid);
	$maxstamina = get_module_pref("daystamina", "staminasystem", $userid);
	$totalpct = ($totalstamina/$maxstamina)*100;
	$redpoint = get_module_pref("red", "staminasystem", $userid);
	$amberpoint = get_module_pref("amber", "staminasystem", $userid);
	
	$greenmax = $maxstamina - $redpoint - $amberpoint;
	$greenvalue = $totalstamina - $redpoint - $amberpoint;
	$greenpct = ($greenvalue/$greenmax)*100;
	if ($greenvalue < 0) {
		$greenvalue = 0;
		$greenpct = 0;
	}
	
	$ambermax = $amberpoint;
	$ambervalue = $totalstamina - $redpoint;
	$amberpct = ($ambervalue/$ambermax)*100;
	if ($ambervalue < 0) {
		$ambervalue = 0;
		$amberpct = 0;
	}
	if ($ambervalue > $amberpoint) {
		$ambervalue = $amberpoint;
		$amberpct = 100;
	}
	
	$redmax = $redpoint;
	$redvalue = $totalstamina;
	$redpct = ($redvalue/$redmax)*100;
	if ($redvalue > $redpoint) {
		$redvalue = $redpoint;
		$redpct = 100;
	}
	
	switch ($type) {
		case 0:
			if ($realvalue === false){
				$returnvalue = $redpct;
			} else {
				$returnvalue = $redvalue;
			}
			break;
		case 1:
			if ($realvalue === false){
				$returnvalue = $amberpct;
			} else {
				$returnvalue = $ambervalue;
			}
			break;
		case 2:
			if ($realvalue === false){
				$returnvalue = $greenpct;
			} else {
				$returnvalue = $greenvalue;
			}
			break;
		case 3:
			if ($realvalue === false){
				$returnvalue = $totalpct;
			} else {
				$returnvalue = $totalstamina;
			}
			break;
		case 4:
			$returnvalue = $maxstamina;
			break;
	}
	
	return $returnvalue;
}



/*
*******************************************************
PROCESS NEW DAY
Awards levels, reduces action costs, strips buffs, resets Stamina to starting value.
*******************************************************
*/

function stamina_process_newday($userid = false) {
	global $session;
	
	if ($userid === false) $userid = $session['user']['acctid'];
	
	modulehook("stamina-newday-intercept");
	
	$actions = get_player_action_list($userid);

	foreach($actions AS $key => $values){
		$exp = $values['exp'];
		$currentlvl = $values['level'];
		$newlevel = floor($values['exp']/$values['expforlvl']);
		$levelsgained = 0;
		while ($newlevel > $currentlvl) {
			// add experience, increase level, reduce costs
			$currentlvl++;
			$levelsgained++;
			$values['naturalcost'] -= $values['costreduction'];
			if ($values['naturalcost'] < $values['mincost']) {
				$values['naturalcost'] = $values['mincost'];
			};
		};
		if ($levelsgained == 1){
			output("You gained a level in %s!`n", $key);
		}
		if ($levelsgained > 1){
			output("You gained %s levels in %s!`n", $levelsgained, $key);
		}
		$values['level'] += $levelsgained;
		$actions[$key]=$values;
	};
	set_module_pref("actions", serialize($actions), "staminasystem", $userid);
	// remove buffs
	stamina_strip_all_buffs($userid);

	$startingstamina = get_module_pref("daystamina","staminasystem",$userid);
	set_module_pref("stamina",$startingstamina,"staminasystem",$userid);
	
	modulehook("stamina-newday");
	
	return true;
}

/*
*******************************************************
PROCESS DRAGON KILL
Retains a percentage of experience points, and resets action costs
*******************************************************
*/

function stamina_process_dragonkill($userid = false){
	global $session;
	if ($userid === false) $userid = $session['user']['acctid'];
	$actions = get_player_action_list($userid);
	foreach($actions AS $key => $values){
		$values['dkexp'] = round(($values['exp'] / 100) * $values['dkpct']) + $values['dkexp'];
		$values['exp'] = $values['dkexp'];
		$values['level'] = 0;
		$values['naturalcost'] = $values['maxcost'];
		$actions[$key]=$values;
	};
	set_module_pref("actions", serialize($actions), "staminasystem", $userid);
	return true;
}

/*
*******************************************************
ADD AND REMOVE STAMINA
Simple functions to add or remove Stamina from players.
*******************************************************
*/

function addstamina($amount, $userid = false){
	global $session;
	
	if ($userid === false) $userid = $session['user']['acctid'];
	
	$newstamina = get_module_pref("stamina", "staminasystem", $userid) + $amount;
	set_module_pref("stamina",$newstamina,"staminasystem",$userid);
	
	return $newstamina;
}

function removestamina($amount, $userid = false){
	global $session;
	
	if ($userid === false) $userid = $session['user']['acctid'];
	
	$newstamina = get_module_pref("stamina", "staminasystem", $userid) - $amount;
	if ($newstamina < 0){
		$newstamina = 0;
	}
	set_module_pref("stamina",$newstamina,"staminasystem",$userid);
	
	return $newstamina;
}

?>