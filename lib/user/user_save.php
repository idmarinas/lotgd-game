<?php
$sql = "";
$updates=0;
$post = httpallpost();
$oldvalues = stripslashes(httppost('oldvalues'));
$oldvalues = unserialize($oldvalues);
// Handle recombining the old name
$otitle = $oldvalues['title'];
if ($oldvalues['ctitle']) $otitle = $oldvalues['ctitle'];
// now the $ctitle is the real title
//$oldvalues['name'] = $otitle . ' ' . $oldvalues['name'];
if (!isset($oldvalues['playername']) || $oldvalues['playername']=='') {
	//you need a name, this is normal after an update from <1.1.1+nb
	if ($post['playername']=='') $post['playername']=get_player_basename($oldvalues);

}
// End Naming
output_notl("`n");
foreach ($post as $key=>$val) {
	if (isset($userinfo[$key])){
		if ($key=="newpassword" ){
			if ($val>"") {
				$sql.="password=\"".md5(md5($val))."\",";
				$updates++;
				output("`\$Password value has been updated.`0`n");
				debuglog($session['user']['name']."`0 changed password to $val",$userid);
				if ($session['user']['acctid']==$userid) {
					$session['user']['password']=md5(md5($val));
				}
			}
		}elseif ($key=="superuser"){
			$value = 0;
			while (list($k,$v)=each($val)){
				if ($v) $value += (int)$k;
			}
				//strip off an attempt to set privs that the user doesn't
			//have authority to set.
			$stripfield = ((int)$oldvalues['superuser'] | $session['user']['superuser'] | SU_ANYONE_CAN_SET | ($session['user']['superuser'] & SU_MEGAUSER ? 0xFFFFFFFF : 0));
			$value = $value & $stripfield;
				//put back on privs that the user used to have but the
			//current user can't set.
			$unremovable = ~ ((int)$session['user']['superuser'] | SU_ANYONE_CAN_SET | ($session['user']['superuser'] & SU_MEGAUSER ? 0xFFFFFFFF : 0));
			$filteredunremovable = (int)$oldvalues['superuser'] & $unremovable;
			$value = $value | $filteredunremovable;
			if ((int)$value != (int)$oldvalues['superuser']){
				$sql.="$key = \"$value\",";
				$updates++;
				output("`\$Superuser values have changed.`0`n");
				if ($session['user']['acctid']==$userid) {
					$session['user']['superuser']=$value;
				}
				debuglog($session['user']['name']."`0 changed superuser to ".show_bitfield($value),$userid) . "`n";
				debug("superuser has changed to $value");
			}
		} elseif ($key=="name33" && stripslashes($val)!=$oldvalues[$key]) {
			$updates++;
			$tmp = sanitize_colorname(getsetting("spaceinname", 0),
					stripslashes($val), true);
			$tmp = preg_replace("/[`][cHw]/", "", $tmp);
			$tmp = sanitize_html($tmp);
			if ($tmp != stripslashes($val)) {
				output("`\$Illegal characters removed from player name!`0`n");
			}
			if (soap($tmp) != ($tmp)) {
				output("`^The new name doesn't pass the bad word filter!`0");
			}
			debug($tmp);
			$newname = change_player_name($tmp, $oldvalues);
			debug($newname);
			$sql.="$key = \"".addslashes($newname)."\",";
			output("`2Changed player name to %s`0`n", $newname);
			debuglog($session['user']['name'] . "`0 changed player name to $newname`0", $userid);
			$oldvalues['name']=$newname;
			if ($session['user']['acctid']==$userid) {
				$session['user']['name'] = $newname;
			}
		} elseif ($key=="title" && stripslashes($val)!=$oldvalues[$key]) {
			$updates++;
			$tmp = sanitize_colorname(true, stripslashes($val), true);
			$tmp = preg_replace("/[`][cHw]/", "", $tmp);
			$tmp = sanitize_html($tmp);
			if ($tmp != stripslashes($val)) {
				output("`\$Illegal characters removed from player title!`0`n");
			}
			if (soap($tmp) != ($tmp)) {
				output("`^The new title doesn't pass the bad word filter!`0");
			}
				$newname = change_player_title($tmp, $oldvalues);
			$sql.="$key = \"$val\",";
			output("Changed player title from %s`0 to %s`0`n", $oldvalues['title'], $tmp);
			$oldvalues[$key]=$tmp;
			if ($newname != $oldvalues['name']) {
				$sql.="name = \"".addslashes($newname)."\",";
				output("`2Changed player name to %s`2 due to changed dragonkill title`n", $newname);
				debuglog($session['user']['name'] . "`0 changed player name to $newname`0 due to changed dragonkill title", $userid);
				$oldvalues['name']=$newname;
				if ($session['user']['acctid']==$userid) {
					$session['user']['name'] = $newname;
				}
			}
			if ($session['user']['acctid']==$userid) {
				$session['user']['title'] = $tmp;
			}
		} elseif ($key=="ctitle" && stripslashes($val)!=$oldvalues[$key]) {
			$updates++;
			$tmp = sanitize_colorname(true, stripslashes($val), true);
			$tmp = preg_replace("/[`][cHw]/", "", $tmp);
			$tmp = sanitize_html($tmp);
			if ($tmp != stripslashes($val)) {
				output("`\$Illegal characters removed from custom title!`0`n");
			}
			if (soap($tmp) != ($tmp)) {
				output("`^The new custom title doesn't pass the bad word filter!`0");
			}
			$newname = change_player_ctitle($tmp, $oldvalues);
			$sql.="$key = \"$val\",";
			output("`2Changed player ctitle from `\$%s`2 to `\$%s`2`n", $oldvalues['ctitle'], $tmp);
			$oldvalues[$key]=$tmp;
			if ($newname != $oldvalues['name']) {
				$sql.="name = \"".addslashes($newname)."\",";
				if ($oldvalues['playername']=='' && !isset($post['playername'])) {
					//no valid title currently, add update
					$post['playername']=get_player_basename($tmp);
				}
				output("`2Changed player name to `\$%s`2 due to changed custom title`n", $newname);
				debuglog($session['user']['name'] . "`0 changed player name to $newname`0 due to changed custom title", $userid);
				$oldvalues['name']=$newname;
				if ($session['user']['acctid']==$userid) {
					$session['user']['name'] = $newname;
				}
			}
			if ($session['user']['acctid']==$userid) {
				$session['user']['ctitle'] = $tmp;
			}
		} elseif (($key=="playername") && stripslashes($val)!=$oldvalues[$key]) {
			$updates++;
			$tmp = sanitize_colorname(true, stripslashes($val), true);
			$tmp = preg_replace("/[`][cHw]/", "", $tmp);
			$tmp = sanitize_html($tmp);
			if ($tmp != stripslashes($val)) {
				output("`\$Illegal characters removed from playername!`0`n");
			}
			if (soap($tmp) != ($tmp)) {
				output("`^The new playername doesn't pass the bad word filter!`0");
			}
			debug($tmp);
			$newname = change_player_name($tmp, $oldvalues);
			debug($newname);
			$sql.="$key = \"$val\",";
			output("`2Changed player name from `\$%s`2 to `\$%s`2`n", $oldvalues['playername'], $tmp);
			$oldvalues[$key]=$tmp;
			if ($newname != $oldvalues['name']) {
				$sql.="name = \"".addslashes($newname)."\",";
				debuglog($session['user']['name'] . "`0 changed player name to $newname`0 due to changed custom title", $userid);
				$oldvalues['name']=$newname;
				if ($session['user']['acctid']==$userid) {
					$session['user']['name'] = $newname;
				}
			}
			if ($session['user']['acctid']==$userid) {
				$session['user']['playername'] = $tmp;
			}
		}elseif ($key=="oldvalues"){
			//donothing.
		}elseif ($oldvalues[$key]!=stripslashes($val) && isset($oldvalues[$key])){
			if ($key=='name') continue; //well, name is composed now
			$sql.="$key = \"$val\",";
			$updates++;
			output("`2 Value `\$'%s`2' has changed to '`\$%s`2'.`n", $key, stripslashes($val));
			debuglog($session['user']['name']."`0 changed $key from {$oldvalues[$key]} to $val",$userid);
			if ($session['user']['acctid']==$userid) {
				$session['user'][$key]=stripslashes($val);
			}
		}
	}
}
	$sql=substr($sql,0,strlen($sql)-1);
$sql = "UPDATE " . db_prefix("accounts") . " SET " . $sql . " WHERE acctid=\"$userid\"";
	$petition = httpget("returnpetition");
if ($petition!="")
	addnav("","viewpetition.php?op=view&id=$petition");
addnav("","user.php");
	if ($updates>0){
	db_query($sql);
	debug("Updated $updates fields in the user record with:\n$sql");
	output("%s fields in the user's record were updated.", $updates);
}else{
	output("No fields were changed in the user's record.");
}
$op = "edit";
httpset($op, "edit");
?>
