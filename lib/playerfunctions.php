<?php

function get_player_attack($player=false) {
	global $session;
	if ($player!==false) {
		$sql="SELECT strength,wisdom,intelligence,attack FROM ".db_prefix('accounts')." WHERE acctid=".((int)$player).";";
		$result=db_query($sql);
		$row=db_fetch_assoc($result);
		if (!$row) return 0;
		$user=$row;
	} else $user =& $session['user'];
	$strbonus=round((1/3)*$user['strength'],1);
	$speedbonus=round((1/3)*get_player_speed($player),1);
	$wisdombonus=round((1/6)*$user['wisdom'],1);
	$intbonus=round((1/6)*$user['intelligence'],1);
	$miscbonus=round($user['attack']-9,1);
	$attack = $strbonus+$speedbonus+$wisdombonus+$intbonus+$miscbonus;
	return max($attack,0);
}


function explained_get_player_attack($player=false) {
	global $session;
	if ($player!==false) {
		$sql="SELECT strength,wisdom,intelligence,attack FROM ".db_prefix('accounts')." WHERE acctid=".((int)$player).";";
		$result=db_query($sql);
		$row=db_fetch_assoc($result);
		if (!$row) return 0;
		$user=$row;
	} else $user =& $session['user'];
	$strbonus=round((1/3)*$user['strength'],1);
	$speedbonus=round((1/3)*get_player_speed($player),1);
	$wisdombonus=round((1/6)*$user['wisdom'],1);
	$intbonus=round((1/6)*$user['intelligence'],1);
	$miscbonus=round($user['attack']-9,1);
	$atk = $strbonus+$speedbonus+$wisdombonus+$intbonus+$miscbonus;
	$weapondmg=(int)$user['weapondmg'];
	$levelbonus=(int)$user['level']-1;
	$miscbonus-=$weapondmg+$levelbonus;
	$explained=sprintf_translate("%s STR + %s SPD + %s WIS+ %s INT + %s Weapon + %s Train + %s MISC ",$strbonus,$speedbonus,$wisdombonus,$intbonus,$weapondmg,$levelbonus,$miscbonus);
	return $explained;
}

function get_player_defense($player=false) {
	global $session;
	if ($player!==false) {
		$sql="SELECT constitution,wisdom,defense FROM ".db_prefix('accounts')." WHERE acctid=".((int)$player).";";
		$result=db_query($sql);
		$row=db_fetch_assoc($result);
		if (!$row) return 0;
		$user=$row;
	} else $user =& $session['user'];
	$wisdombonus = round((1/4)*$user['wisdom'],1);
	$constbonus = round((3/8)*$user['constitution'],1);
	$speedbonus = round((3/8)*get_player_speed($player),1);
	$miscbonus = round($user['defense']-9,1);
	$defense = $wisdombonus+$speedbonus+$constbonus+$miscbonus;
	return max($defense,0);
}

function explained_get_player_defense($player=false) {
	global $session;
	if ($player!==false) {
		$sql="SELECT constitution,wisdom,defense FROM ".db_prefix('accounts')." WHERE acctid=".((int)$player).";";
		$result=db_query($sql);
		$row=db_fetch_assoc($result);
		if (!$row) return 0;
		$user=$row;
	} else $user =& $session['user'];
	$wisdombonus = round((1/4)*$user['wisdom'],1);
	$constbonus = round((3/8)*$user['constitution'],1);
	$speedbonus = round((3/8)*get_player_speed($player),1);
	$miscbonus = round($user['defense']-9,1);
	$defense = $wisdombonus+$speedbonus+$constbonus+$miscbonus;
	$armordef=(int)$user['armordef'];
	$levelbonus=(int)$user['level']-1;
	$miscbonus-=$armordef+$levelbonus;
	$explained=sprintf_translate("%s WIS + %s CON + %s SPD + %s Armor + %s Train + %s MISC ",$wisdombonus,$constbonus,$speedbonus,$armordef,$levelbonus,$miscbonus);
	return $explained;
}

function get_player_speed($player=false) {
	global $session;
	if ($player!==false) {
		$sql="SELECT dexterity,intelligence FROM ".db_prefix('accounts')." WHERE acctid=".((int)$player).";";
		$result=db_query($sql);
		$row=db_fetch_assoc($result);
		if (!$row) return 0;
		$user=$row;
	} else $user =& $session['user'];
	$speed = round((1/2)*$user['dexterity']+(1/4)*$user['intelligence']+(5/2),1);
	return max($speed,0);
}

function get_player_physical_resistance($player=false) {
	global $session;
	if ($player!==false) {
		$sql="SELECT constitution,wisdom,defense FROM ".db_prefix('accounts')." WHERE acctid=".((int)$player).";";
		$result=db_query($sql);
		$row=db_fetch_assoc($result);
		if (!$row) return 0;
		$user=$row;
	} else $user =& $session['user'];
	$defense = round(log($user['wisdom'])+$user['constitution']*0.08+log($user['defense']),1);
	return max($defense,0);
}

function is_player_online($player=false) {
	//don't call this with like 100 people on a screen, it's pretty high load, 1 query each call
	//do mass_is_player_online($array_of_ids) instead
	static $checked_users=array(); //remember for later, I am sucker for doing unnecessary stuff, and adding and checkin an array is better than one sql query more than necessary ;)
	if ($player===false) {
		global $session;
		$user =& $session['user'];
	} elseif (isset($checked_users[$player])) {
		$user = &$checked_users[$player];
	} else {
		//fetch the data from the DB
		$sql="SELECT laston,loggedin FROM ".db_prefix('accounts')." WHERE acctid=".((int)$player).";";
		$result=db_query($sql);
		$row=db_fetch_assoc($result);
		if (!$row) return false;
		$checked_users[$player]=$row;
		$user=$row;
	}
	if (isset($user['laston']) && isset($user['loggedin'])) {
		if (strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds") > strtotime($user['laston']) && strtotime($user['laston'])>0)  return false;
		if (!$user['loggedin']) return false;
		return true;
	} 
	return false;
}

function mass_is_player_online($players=false) {
	//don't call this with like 100 people on a screen, it's pretty high load, 1 query each call
	//do mass_is_player_online($array_of_ids) instead
	$users=array();
	if ($players===false || $players==array() || !is_array($players)) {
		return array(); //nothing to do
	} else {
		//fetch the data from the DB
		$sql="SELECT acctid,laston,loggedin FROM ".db_prefix('accounts')." WHERE acctid IN (".addslashes(implode(",",$players)).")";
		$result=db_query($sql);
		while ($user=db_fetch_assoc($result)) {
			$users[$user['acctid']]=1;
			if (isset($user['laston']) && isset($user['loggedin'])) {
				if (strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds") > strtotime($user['laston']) && $user['laston']>"")  $users[$user['acctid']]=0;
				if (!$user['loggedin']) $users[$user['acctid']]=0;
			} else $users[$user['acctid']]=0;
		}
	}
	return $users;
}

function get_player_dragonkillmod($withhitpoints=false) {
	global $session;
	$dragonpoints=array_count_values($session['user']['dragonpoints']);
	$dk=0;
	foreach ($dragonpoints as $key=>$val) {
		switch ($key) {
			//not for wisdom on full scale
			case "wis":
				$dk+=0.2*$val;
				break;
			case "con":case "str": case "int": case "dex":
				$dk+=0.3*$val;
				break;
			case "at": case "de": 
				$dk+=$val;
				break;
		}
	}
	if ($withhitpoints) $dk += (int)(($session['user']['maxhitpoints']-($session['user']['level']*10))/5);
	return $dk;
}
?>
