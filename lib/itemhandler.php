<?php
// Itemhandler by Christian Rutsch (c) 2005

mydefine("HOOK_NEWDAY", 1);
mydefine("HOOK_FOREST", 2);
mydefine("HOOK_VILLAGE", 4);
mydefine("HOOK_SHADES", 8);
mydefine("HOOK_FIGHTNAV", 16);
mydefine("HOOK_TRAIN", 32);
//mydefine("HOOK_DRAGONKILL", 64);

function get_buff($buffid) {
	$sql = "SELECT * FROM ".db_prefix("itembuffs")." WHERE buffid = $buffid";
	$result = db_query_cached($sql, "inventory-buff-$buffid", 525600);
	$buff = db_fetch_assoc($result);

	// Here we'll sanitize the buff a little, so there are no values in it
	// which will actually cause output but which don't have an effect
	$newbuff = array();
	if ($buff['atkmod'] != "0" && $buff['atkmod'] != "1" && $buff['atkmod'] != "") $new_buff['atkmod'] = $buff['atkmod'];
	if ($buff['defmod'] != "0" && $buff['defmod'] != "1" && $buff['defmod'] != "") $new_buff['defmod'] = $buff['defmod'];
	if ($buff['dmgmod'] != "0" && $buff['dmgmod'] != "1" && $buff['dmgmod'] != "") $new_buff['dmgmod'] = $buff['dmgmod'];
	if ($buff['badguyatkmod'] != "0" && $buff['badguyatkmod'] != "1" && $buff['badguyatkmod'] != "") $new_buff['badguyatkmod'] = $buff['badguyatkmod'];
	if ($buff['badguydefmod'] != "0" && $buff['badguydefmod'] != "1" && $buff['badguydefmod'] != "") $new_buff['badguydefmod'] = $buff['badguydefmod'];
	if ($buff['badguydmgmod'] != "0" && $buff['badguydmgmod'] != "1" && $buff['badguydmgmod'] != "") $new_buff['badguydmgmod'] = $buff['badguydmgmod'];
	if ($buff['invulnerabele'] == "1") $new_buff['invulnerable'] = 1;
	if ($buff['damageshield'] != "0" && $buff['damageshield'] != "") $new_buff['damageshield'] = $buff['damageshield'];
	if ($buff['regen'] != "0" && $buff['regen'] != "") $new_buff['regen'] = $buff['regen'];
	if ($buff['lifetap'] != "0" && $buff['lifetap'] != "") $new_buff['lifetap'] = $buff['lifetap'];
	if ($buff['minioncount'] != "0" && $buff['minioncount'] != "") {
		$new_buff['minioncount'] = $buff['minioncount'];
		$new_buff['maxbadguydamage'] = $buff['maxbadguydamage'];
		$new_buff['minbadguydamage'] = $buff['minbadguydamage'];
		$new_buff['maxgoodguydamage'] = $buff['maxgoodguydamage'];
		$new_buff['mingoodguydamage'] = $buff['mingoodguydamage'];
	}
	$new_buff['rounds'] = $buff['rounds'];
	$new_buff['startmsg'] = $buff['startmsg'];
	$new_buff['roundmsg'] = $buff['roundmsg'];
	$new_buff['wearoff'] = $buff['wearoff'];
	$new_buff['effectfailmsg'] = $buff['effectfailmsg'];
	$new_buff['effectnodmgmsg'] = $buff['effectnodmgmsg'];
	$new_buff['effectmsg'] = $buff['effectmsg'];
	$new_buff['allowinpvp'] = $buff['allowinpvp'];
	$new_buff['allowintrain'] = $buff['allowintrain'];
	$new_buff['survivenewday'] = $buff['survivenewday'];
	$new_buff['startmsg'] = $buff['startmsg'];
	$new_buff['wearoff'] = $buff['wearoff'];
	$new_buff['invulnerable'] = $buff['invulnerable'];

	if ($buff['buffshortname'] == "") {
		$new_buff['name'] = $item['name'];
	} else {
		$new_buff['name'] = $buff['buffshortname'];
	}

	while (list($property,$value)=each($new_buff)) {
		$new_buff[$property] = preg_replace("/\\n/", "", $value);
	}

	return $new_buff;
}

function display_item_fightnav($args) {
	global $session;
	$script= $args['script'];

	$constant = HOOK_FIGHTNAV;
	$item = db_prefix("item");
	$inventory = db_prefix("inventory");
	$acctid = $session['user']['acctid'];
	$sql = "SELECT $item.*,
						SUM(if ($inventory.charges > 1, $inventory.charges, 1)) AS quantity,
						$inventory.invid AS invid
					FROM $item
					INNER JOIN $inventory ON $item.itemid = $inventory.itemid
					WHERE ($item.activationhook & $constant)
						AND $inventory.userid = $acctid
					GROUP BY $item.itemid";
	$result = db_query($sql);

	if (db_num_rows($result) > 0) {
		addnav("Items");
		while ($item = db_fetch_assoc($result)) {
			if ((int)$item['itemid'] == 0 || $item['quantity'] == 0) continue;

			if ($item['link'] <> "")
				$linkentry = $item['link'];
			else
				$linkentry = "|";

			list($lname, $llink) = explode("|", $linkentry, 2);
			if ($lname == "") $lname = $item['name'];
			if ($llink == "") $llink = $script."op=fight&skill=ITEM&l=".$item['itemid']."&invid=".$item['invid'];
			addnav(array(" ?%s `7(%s)`0", $lname, $item['quantity']), $llink);
		}
	}
	return $args;
}

function display_item_nav($hookname, $return = false) {
	global $session;
	if ($hookname_override = httpget("hookname")) {
		$hookname = $hookname_override;
	}
	$constant = constant("HOOK_" . strtoupper($hookname));
	$item = db_prefix("item");
	$inventory = db_prefix("inventory");
	$acctid = $session['user']['acctid'];
	$sql = "SELECT $item.*,
						SUM(if ($inventory.charges > 1, $inventory.charges, 1)) AS quantity,
						$inventory.invid AS invid
					FROM $item
					INNER JOIN $inventory ON $item.itemid = $inventory.itemid
					WHERE ($item.activationhook & $constant)
						AND $inventory.userid = $acctid
					GROUP BY $item.itemid";
	$result = db_query($sql);

	if (db_num_rows($result) > 0) {
		addnav("Items");
		if ($return === false) {
			$return = URLencode($_SERVER['REQUEST_URI']);
		} else {
			$return = URLencode($return);
			$return .= "&returnhandle=1&hookname=$hookname";
		}
		while ($item = db_fetch_assoc($result)) {
			if ((int)$item['itemid'] == 0 || $item['quantity'] == 0) continue;
			if ($item['link'] <> "")
				$linkentry = $item['link'];
			else
				$linkentry = "|";
			list($lname, $llink) = explode("|", $linkentry, 2);
			if ($lname == "") $lname = $item['name'];
			if ($llink == "") $llink = "runmodule.php?module=inventory&op=activate&id=".$item['itemid']."&invid=".$item['invid'];
			$args = modulehook("inventory-changereturn", array("item"=>$item, "return"=>$return, "hookname"=>$hookname));
			$newreturn = $args['return'];
			$llink .= "&return=".$newreturn."&hookname=$hookname";
			addnav(array("%s `7(%s)`0", " ?$lname", $item['quantity']), $llink);
		}
	}
}

function run_newday_buffs() {
	global $session;
	require_once("lib/buffs.php");
	$constant = HOOK_NEWDAY;
	$item = db_prefix("item");
	$inventory = db_prefix("inventory");
	$sql = "SELECT
				$item.itemid AS itemid,
				$item.buffid AS buffid,
				$item.name AS name,
				$item.exectext AS exectext,
				$item.noeffecttext AS noeffecttext,
				$item.execvalue AS execvalue,
				$inventory.charges AS charges,
				$inventory.invid AS invid
			FROM $item
			INNER JOIN
				$inventory ON $inventory.itemid = $item.itemid
				AND	$inventory.userid= {$session['user']['acctid']}
				AND (($item.activationhook & 1) != 0)";
	$result = db_query($sql);
	$alreadydone = array();
	while ($item = db_fetch_assoc($result)) {
		if (isset($alreadydone[$item['itemid']]) && $alreadydone[$item['itemid']] == true) {
			continue;
		} else {
			$alreadydone[$item['itemid']] = true; // prevent that more than one item of a kind activates...
			if ($item['buffid'] > 0) {
				apply_buff($item['name'], get_buff($item['buffid']));
			}
			if ($item['execvalue'] > "") {
				require_once("lib/itemeffects.php");
				if ($item['exectext'] > "") {
					output($item['exectext'], $item['name']);
				} else {
					output("You activate %s!", $item['name']);
				}
				output_notl("%s`n", get_effect($item, $item['noeffecttext']));
			}
			if ($item['charges'] > 1) {
				uncharge_item((int)$item['itemid'], false, $item['invid']);
			} else if (isset($item['invid'])) {
				remove_item((int)$item['itemid'], 1, false, $item['invid']);
			} else {
				remove_item((int)$item['itemid'], 1);
			}
		}
	}
}

function get_item_by_name($itemname) {
	$sql = "SELECT * FROM ".db_prefix("item")." WHERE name = '$itemname' LIMIT 1";
	$result = db_query_cached($sql, "item-name-$itemname");
	$item = db_fetch_assoc($result);
	if (db_num_rows($result) == 0)
		return false;
	else
		return $item;
}

function get_item_by_id($itemid) {
	$sql = "SELECT * FROM ".db_prefix("item")." WHERE itemid = $itemid LIMIT 1";
	$result = db_query_cached($sql, "item-id-$itemid");
	$item = db_fetch_assoc($result);
	if (db_num_rows($result) == 0)
		return false;
	else
		return $item;
}

function get_item($item){
	if(!is_int($item))
		return get_item_by_name($item);
	else
		return get_item_by_id($item);
}

function get_random_item($class = false) {
	$chance = e_rand(0,100);
	if ($class === false) {
		$sql = "SELECT * FROM ".db_prefix("item")." WHERE $chance <= findchance ORDER BY rand(".e_rand().") LIMIT 1";
	} else {
		$sql = "SELECT * FROM ".db_prefix("item")." WHERE class = '$class' AND $chance <= findchance ORDER BY rand(".e_rand().") LIMIT 1";
	}
	$result = db_query($sql);
	if (db_num_rows($result) == 1) $item = db_fetch_assoc($result);
	else $item = false;
	return $item;
}

function add_item_by_name($itemname, $qty=1, $user=0, $specialvalue="", $sellvaluegold=false, $sellvaluegems=false) {
	$item = get_item_by_name($itemname);
	return add_item_by_id((int)$item['itemid'], $qty, $user, $specialvalue, $sellvaluegold, $sellvaluegems);
}

function add_item_by_id($itemid, $qty=1, $user=0, $specialvalue="", $sellvaluegold=false, $sellvaluegems=false, $charges=false) {
	global $session, $totalcount, $totalweight;
	if ($qty < 1) return false;
	if ($user === 0) $user = $session['user']['acctid'];
	$inventory = db_prefix("inventory");
	$item = db_prefix("item");
	if (!(isset($totalcount) && isset($totalweight))) {
		$sql = "SELECT COUNT($inventory.itemid) AS totalcount, $item.weight AS totalweight
					FROM $inventory
					INNER JOIN $item ON $item.itemid = $inventory.itemid
					WHERE $inventory.userid = $user
					GROUP BY $inventory.itemid";
		$result = db_query($sql);
		while($row=db_fetch_assoc($result)){
			$totalcount += $row['totalcount'];
			$totalweight += $row['totalweight'] * $row['totalcount'];
		}
	}
	// $inv = db_fetch_assoc($result);
	$maxcount = get_module_setting("limit", "inventory");
	$maxweight = get_module_setting("weight", "inventory");
	// We must not add more items than the player actually may carry!
	$sql = "SELECT gold, gems, charges, uniqueforserver, weight, uniqueforplayer FROM $item WHERE itemid = $itemid";
	$result = db_query($sql);
	$item_raw = db_fetch_assoc($result);
	$maxitems_count = max(0,$maxcount-$totalcount);
	if ($item_raw['weight'] > 0) {
		$maxitems_weight = max(0,floor(($maxweight-$totalweight)/$item_raw['weight']));
	} else {
		$maxitems_weight = $qty;
	}
	debug("Trying to add $qty items. Item's weight is {$item_raw['weight']}");
	if ($maxcount > 0) {
		debug("In theory only $maxitems_count should be added (totalcount)");
	} else {
		debug("There is no restriction on quantity active.");
	}
	if ($maxweight > 0) {
		debug("In theory only $maxitems_weight should be added (totalweight)");
	}else {
		debug("There is no restriction on weight active.");
	}

	if ($maxcount > 0 && $maxweight > 0 && $item_raw['weight']) {
		// limitation on total qty AND weight AND item is not weightless
		$qty = min($qty, $maxitems_count, $maxitems_weight);
		debug("Reducing real quantity to $qty. (count/weight-restriction)");
	}
	if ($maxweight > 0 && $maxcount == 0 && $item_raw['weight'] > 0) {
		// no limitation on total qty AND item is not weightless
		$qty = min($qty, $maxitems_weight);
		debug("Reducing real quantity to $qty. (weight-restriction)");
	}
	if ($maxcount > 0 && $maxweight == 0) {
		// no limitation on weight.
		$qty = min($qty, $maxitems_count);
		debug("Reducing real quantity to $qty. (count-restriction)");
	}
	debug("Totalcount / MaxCount is: $totalcount / $maxcount");
	debug("MaxWeight is: $totalweight / $maxweight");
	debug("Item weight: ".$item_raw['weight']);
	debug("Quantity to add was: $qty");
	if ($maxcount != 0 && $totalcount >= $maxcount) {
		debug("Too many items, will not add this one!");
		return false;
	} else if ($maxweight && $totalweight >= $maxweight) {
		debug("Items are too heavy. Item hasn't been added!");
		return false;
	} else if ($qty <= 0) {
		debug("Zero items added.");
		return false;
	} else {
		if ($sellvaluegold === false) $sellvaluegold = round($item_raw['gold'] * (get_module_setting("sellgold", "inventory")/100));
		if ($sellvaluegems === false) $sellvaluegems = round($item_raw['gems'] * (get_module_setting("sellgems", "inventory")/100));
		if ($charges === false) $charges = $item_raw['charges'];
		if ($item_raw['uniqueforserver']) {
			$sql = "SELECT * FROM $inventory WHERE itemid = $itemid LIMIT 2";
			$result = db_query($sql);
			if (db_num_rows($result) > 0) {
				debug("UNIQUE item has not been added because already someone else owns this!");
				return false;
			}
		}
		if ($item_raw['uniqueforplayer']) {
			$sql = "SELECT * FROM $inventory WHERE itemid = $itemid AND userid = $user LIMIT 2";
			$result = db_query($sql);
			if (db_num_rows($result) > 0) {
				debug("UNIQUEFORPLAYER item has not been added because this player already owns this item!");
				return false;
			}
		}
		$totalcount  += $qty;
		$totalweight += $qty * $item_raw['weight'];
		$sql = "INSERT INTO $inventory (`userid`, `itemid`, `sellvaluegold`, `sellvaluegems`, `specialvalue`, `charges`) VALUES ";
		for ($i=0;$i<$qty;$i++) {
			if($i){
				$sql .= ",";
			}
			$sql .= "($user, $itemid, $sellvaluegold, $sellvaluegems, '$specialvalue', '$charges')";
		}
		db_query($sql);
//		debuglog("has gained $qty item.", false, false, "item-$itemid", $qty);
		debuglog("has gained $qty item (ID: $itemid).");
		invalidatedatacache("inventory-user-$user");
		return $qty;
	}
}

function add_item($item, $qty=1, $user=0, $specialvalue="", $sellvaluegold=false, $sellvaluegems=false) {
	if(!is_int($item))
		return add_item_by_name($item, $qty, $user, $specialvalue, $sellvaluegold, $sellvaluegems);
	else
		return add_item_by_id($item, $qty, $user, $specialvalue, $sellvaluegold, $sellvaluegems);
}


function get_inventory($user=0, $showhide=false, $class=0, $singleonly=false) {
	global $session;

	if ($user === 0) $user = $session['user']['acctid'];
	$showhide = (int)$showhide;
	$inventory = db_prefix("inventory");
	$item = db_prefix("item");
	if ($class === 0)
		$classwhere = "";
	else
		$classwhere = "AND $item.class = '$class'";
	if ($singleonly === false) {
		$sql = "SELECT $item.*, COUNT($inventory.itemid) AS quantity, SUM($inventory.charges) AS charges, $inventory.sellvaluegold AS sellvaluegold, $inventory.sellvaluegems AS sellvaluegems
				FROM $inventory
				INNER JOIN $item ON $item.itemid = $inventory.itemid
				WHERE $inventory.userid = $user
					AND $item.hide = $showhide $classwhere
				GROUP BY $item.itemid
				ORDER BY
					$item.class ASC,
					$item.name ASC";
	} else {
		$sql = "SELECT $item.*, $inventory.invid AS invid, COUNT($inventory.itemid) AS quantity,
						$inventory.charges AS charges, $inventory.sellvaluegold AS sellvaluegold, $inventory.sellvaluegems AS sellvaluegems
				FROM $inventory
				INNER JOIN $item ON $item.itemid = $inventory.itemid
				WHERE inventory.userid = $user
					AND $item.hide = $showhide $classwhere
				GROUP BY $inventory.itemid
				ORDER BY
					$item.class ASC,
					$item.name ASC";
	}
	if ($class === 0 && $singleonly===false)
		$result = db_query_cached($sql, "inventory-user-$user");
	else
		$result = db_query($sql);
	return $result;
}

function get_inventory_item($itemid, $user = false) {
	global $session;
	if (!is_int($itemid)) {
		$itemid = get_item_by_name($itemid);
		$itemid = $itemid['itemid'];
	}
	$itemid = (int)$itemid;
	if ($user === false)
		$user = $session['user']['acctid'];
	$inventory = db_prefix("inventory");

	$item = db_prefix("item");
	$sql = "SELECT $item.*, COUNT($inventory.itemid) AS quantity, SUM($inventory.charges) AS charges, $inventory.sellvaluegold AS sellvaluegold, $inventory.sellvaluegems AS sellvaluegems, $inventory.invid AS invid
				FROM $inventory
				INNER JOIN $item ON $item.itemid = $inventory.itemid
				WHERE $inventory.userid = $user
					AND $item.itemid = $itemid
				GROUP BY $item.itemid
				ORDER BY
					$item.class ASC,
					$item.name ASC";
	$result = db_query($sql);
	if ($item = db_fetch_assoc($result))
		return $item;
	else
		return false;
}

function get_inventory_item_by_id($itemid, $invid, $user = 0) {
	global $session;
	if (!is_int($itemid)) {
		$itemid = get_item_by_name($itemid);
		$itemid = $itemid['itemid'];
	}
	$itemid = (int)$itemid;
	if ($user === 0) $user = $session['user']['acctid'];
	$inventory = db_prefix("inventory");
	$item = db_prefix("item");
	$sql = "SELECT $item.*, $inventory.charges AS charges, $inventory.sellvaluegold AS sellvaluegold, $inventory.sellvaluegems AS sellvaluegems
				FROM $inventory
				INNER JOIN $item ON $item.itemid = $inventory.itemid
				WHERE $inventory.userid = $user
					AND $item.itemid = $itemid
					AND $inventory.invid = $invid
				ORDER BY
					$item.class ASC,
					$item.name ASC";
	$result = db_query($sql);
	if ($item = db_fetch_assoc($result))
		return $item;
	else
		return false;
}

function get_itemids_by_class($class) {
	if(func_num_args() > 1 || is_array($class)) {
		if(!is_array($class)) {
			$class = func_get_args();
		}
		$result = db_query("SELECT itemid FROM item WHERE class IN('".join("','", $class)."')");
	} else {
		$result = db_query("SELECT itemid FROM item WHERE class='$class'");
	}
	$ids = array();
	while($id = db_fetch_assoc($result)) {
		$ids[] = $id;
    }
	return $ids;
}

function uncharge_item($itemid, $user=false, $invid=false) {
	global $session;
	if (!is_int($itemid)) {
		$itemid = get_item_by_name($itemid);
		$itemid = $itemid['itemid'];
	}
	$itemid = (int)$itemid;
	if ($user === false)
		$user = $session['user']['acctid'];
	if ($invid !== false)
		$invsql = "AND invid = $invid";
	else
		$invsql = "";
	$inventory = db_prefix("inventory");
	$sql = "UPDATE $inventory SET charges = charges - 1 WHERE itemid = $itemid AND userid = $user AND charges >= 1 $invsql LIMIT 1";
	$result = db_query($sql);
	if (db_affected_rows($result) == 0) {
		debug("ERROR: Tried to uncharge item although no charges or no item present!");
	} else {
		debuglog("uncharged ".db_affected_rows($result)." items (ID: $itemid)", $user);
	}
	$sql = "DELETE FROM $inventory WHERE itemid = $itemid AND userid = $user AND charges = 0";
	$result = db_query($sql);
	$count = db_affected_rows($result);
	if ($count) debuglog("uncharged and deleted $count items (ID: $itemid)", $user);
	invalidatedatacache("inventory-user-$user");
}

function recharge_item($itemid, $user=false, $invid=false) {
	global $session;
	if (!is_int($itemid)) {
		$itemid = get_item_by_name($itemid);
		$itemid = $itemid['itemid'];
	}
	$itemid = (int)$itemid;
	if ($user === false)
		$user = $session['user']['acctid'];
	if ($invid !== false)
		$invsql = "AND invid = $invid";
	else
		$invsql = "";
	$inventory = db_prefix("inventory");
	$sql = "UPDATE $inventory SET charges = charges 1 1 WHERE itemid = $itemid AND userid = $user $invsql LIMIT 1";
	$result = db_query($sql);
	if (db_affected_rows($result) == 0) {
		debug("ERROR: Tried to recharge non-present item!");
	} else {
		debuglog("recharged ".db_affected_rows($result)." items (ID: $itemid)", $user);
	}
	invalidatedatacache("inventory-user-$user");
}


function show_inventory($user = false) {
	global $session;

	$login = httpget('login');
	if ($user === false) {
		$user = $session['user']['acctid'];
		$row['name'] = $session['user']['name'];
	} else {
		$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$user";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
	}

	$inventory = get_inventory($user);
	$count = db_num_rows($inventory);
	tlschema("inventory");
	$name = translate_inline("Name");
	$class = translate_inline("Category");
	$description = translate_inline("Description");
	$goldvalue = translate_inline("Goldvalue");
	$gemvalue = translate_inline("Gemvalue");
	$quantity = translate_inline("Quantity");
	$options = translate_inline("Options");
	$drop = translate_inline("Drop this once");

	rawoutput("<table border=0 cellpadding=2 cellspacing=2 align=center>");
	rawoutput("<tr class='trhead'><td colspan=6>");
	output("`c`b`^%s`& is carrying these items:`b`c", $row['name']);
	rawoutput("</td></tr>");
	if ($count) {
		for ($i=0;$i<$count;$i++) {
			$item = db_fetch_assoc($inventory);
			$countweight += $item['weight'] * $item['quantity'];
			$itemcounter += $item['quantity'];
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
			output("`&%s`0", translate_inline($item['name']));
			rawoutput("</td><td>");
			output("`&`i%s`i`0", translate_inline($item['class']));
			rawoutput("</td><td align='right'>");
			output("`&%s `2pcs`0", $item['quantity']);
			rawoutput("</td><td align='right'>");
			if ($user == $session['user']['acctid'])
				output("`&%s `^gold pieces`0", number_format($item['sellvaluegold']));
			else
				output_notl("&nbsp;",true);
			rawoutput("</td><td align='right'>");
			if ($user == $session['user']['acctid'])
				output("`&%s `%gems`0  ", number_format($item['sellvaluegems']));
			else
				output_notl("&nbsp;",true);
			rawoutput("</td><td align='center'>");
			if(($user == $session['user']['acctid'] && $item['droppable'] && get_module_setting("droppable", "inventory")) || ($session['user']['superuser'] & SU_EDIT_USERS)) {
				rawoutput("[&nbsp;<a href='runmodule.php?module=inventory&login=$login&user=$user&op=dropitem&id=".$item['itemid']."'>$drop</a>&nbsp;]");
				addnav("", "runmodule.php?module=inventory&login=$login&user=$user&op=dropitem&id=".$item['itemid']);
			}
			rawoutput("</td></tr><tr class='".($i%2?"trlight":"trdark")."'><td colspan=6");
			output("`7`i%s`i`0", translate_inline($item['description']));
			rawoutput("</td></tr>");
		}
		$limit = get_module_setting("limit", "inventory");
		$weight = get_module_setting("weight", "inventory");
		if ($user == $session['user']['acctid']) {
			if ($limit) {
				rawoutput("<tr><td colspan=6>");
				output("`n`cYou are currently carrying `^%s`0 / `^%s`0 items.`c", $itemcounter, $limit);
			}
			if ($weight) {
				rawoutput("<tr><td colspan=6>");
				output("`n`cYour items have a total weight of `^%s`0. You must not carry more than `^%s`0.`c", $countweight, $weight);
			}
		}
	} else {
		output("<tr><td colspan=6>`n`c`iThis player does not have any items.`i`c</td></tr>", true);
	}
	rawoutput("</table>");
	tlschema();
}

function check_qty_by_id($itemid, $user = 0) {
	global $session;
	if ($user === 0) $user = $session['user']['acctid'];
	$inventory = db_prefix("inventory");
	$sql = "SELECT COUNT(itemid) AS qty FROM $inventory WHERE userid = $user AND itemid = $itemid";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	return $row['qty'];
}

function check_qty_by_name($itemname, $user = 0) {
	$item = get_item_by_name($itemname);
	return check_qty_by_id((int)$item['itemid'], $user);
}

function check_qty($item, $user=0) {
	if(!is_int($item))
		return check_qty_by_name($item, $user);
	else
		return check_qty_by_id($item, $user);
}

function remove_item_by_id($item, $qty=1, $user=false, $invid=false) {
	global $session;

	if ($user === false)
		$user = $session['user']['acctid'];
	if ($invid !== false || !isset($invid))
		$invsql = "AND invid = $invid";
	else
		$invsql = "";

	$inventory = db_prefix("inventory");
	$sql = "DELETE FROM $inventory WHERE userid = $user AND itemid = $item $invsql LIMIT $qty";
	debuglog("removed item $item from inventory", $user);
	$result = db_query($sql);
	invalidatedatacache("inventory-user-$user");
	invalidatedatacache("inventory-item-$item-$user");
	return db_affected_rows($result);
}

function remove_item_by_name($itemname, $qty=1, $user=false, $invid=false) {
	$row = get_item_by_name($itemname);
	return remove_item_by_id((int)$row['itemid'], $qty, $user, $invid);
}

function remove_item($item, $qty=1, $user=false, $invid=false) {
	if (!is_int($item))
		return remove_item_by_name($item, $qty, $user, $invid);
	else
		return remove_item_by_id($item, $qty, $user, $invid);
}

function remove_items_by_class($class) {
	$ids = get_itemids_by_class($class);
	foreach($ids as $id) {
		remove_item((int)$id, check_qty((int)$id));
	}
}

function shopnav($return, $class, $sell=false, $user=false, $sellall=false, $showdescription=true) {
	global $session;

	if ($return{strlen($return)-1} != "&") $return .= "&";

	$inventory = db_prefix("inventory");
	$item = db_prefix("item");

	if (is_array($class))
		$class_sql = "IN (".join(",", $class).")";
	else
		$class_sql = "= '$class'";

	if ($sell === false) {
		$sql = "SELECT *
					FROM $item
					WHERE $item.class $class_sql
						AND $item.buyable <> 0
					ORDER BY
						$item.class ASC,
						$item.name ASC";
	} else {
		if ($user === false) $user = $session['user']['acctid'];
		$sql = "SELECT $item.itemid AS itemid, $item.name AS name, COUNT($inventory.itemid) AS quantity, $inventory.sellvaluegold AS sellvaluegold, $inventory.sellvaluegems AS sellvaluegems
					FROM $inventory
					INNER JOIN $item ON $item.itemid = $inventory.itemid
					WHERE $inventory.userid = $user
						AND $item.class = '$class'
						AND $item.sellable <> 0
					GROUP BY $item.itemid
					ORDER BY
						$item.class ASC,
						$item.name ASC";
	}
	$result = db_query($sql);
	if (db_num_rows($result) > 0) {
		tlschema('inventory');
		$class = translate_inline($class);
		if ($sellall == true && db_num_rows($result) > 0) {
			addnav(array("Sell all %s", $class));
			addnav("Sell all", $return."id=all");
		}
		$sell?addnav(array("Sell %s", $class)):addnav(array("Buy %s", $class));
		while($row = db_fetch_assoc($result)) {
			modulehook("shopnav", array('item'=>$row, 'sell'=>$sell));
			if ($sell === false) {
				if ($session['user']['gold'] >= $row['gold'] && $session['user']['gems'] >= $row['gems'])
					addnav(array("%s`n`^%s`0 Gold, `%%%s`0 Gems", translate_inline($row['name']), $row['gold'], $row['gems']), $return."id=".$row['itemid']);
				else
					addnav(array("%s`n`^%s`0 Gold, `%%%s`0 Gems", translate_inline($row['name']), $row['gold'], $row['gems']), "");
				if ($showdescription == true) {
					output("`#%s ", translate_inline($row['name']));
					$qty = check_qty((int)$row['itemid']);
					if ($qty > 0) output("`7- You own `^%s pieces", $qty);
					$description = translate_inline($row['description']);
					output("`n`7%s`n`n", $description);
				}
			} else {
				addnav(array("%s`n`^%s`0 Gold, `%%%s`0 Gems`n(`2%s Stück`0)", translate_inline($row['name']), $row['sellvaluegold'], $row['sellvaluegems'], $row['quantity']), $return."id=".$row['itemid']);
			}
		}
		tlschema();
	}
}

// This function updates an existing item or inserts a new item into
// the item table. Depending on the item name it will choose to either
// update all changed fields or insert this as a new item.
// Params:
// $injection: An array containing ininformation about the new item.
//					Unset values will be replaced by their defaults (via mysql
//					table definition.
// $exclude:	An array which contains the fields to be excluded from a
//					potential update procedure. Useful, if fields contain
//					semi-automatically generated values (e.g. links with ids)
// Return values:
// true			If the the item got inserted.
// false			If the the item got updated.

function inject_item($injection, $exclude=false) {
	// Borrowed basic idea from lotgd code. lib/http.php -> function: postparse();
	$sql = ""; $keys = ""; $vals = ""; $i = 0;

	$item = db_prefix("item");
	$test = get_item($injection['name']);
	if ($test = get_item($injection['name'])) {
		$update = array_diff_assoc($injection, $test);
		unset($update['itemid']);
		reset($update);
		if (is_array($exclude)) {
			while (list($key, $excl) = each($exclude)) {
				if (isset($update[$excl])) unset($update[$excl]);
			}
		}
		while(list($key, $val) = each($update)) {
			$sql .= (($i > 0) ? "," : "") . "$key='$val'";
			$keys .= (($i > 0) ? "," : "") . "$key";
			$vals .= (($i > 0) ? "," : "") . "'$val'";
			$i++;
		}
		if ($sql) {
			$sql = "UPDATE $item SET $sql WHERE itemid = {$test['itemid']}";
			db_query($sql);
			invalidatedatacache("item-name-".$injection['name']);
			invalidatedatacache("item-id-".$test['itemid']);
			debug("Updated Item '".$injection['name']."'. SQL = '$sql'");
		} else {
			debug("Nothing to update for '".$injection['name']."'");
		}
		return false;
	} else {
		reset($injection);
		while(list($key, $val) = each($injection)) {
			$keys .= (($i > 0) ? "," : "") . "$key";
			$vals .= (($i > 0) ? "," : "") . "'$val'";
			$i++;
		}
		$sql = "INSERT INTO $item ($keys) VALUES ($vals)";
		db_query($sql);
		debug("Inserted Item '".$injection['name']."'");
		invalidatedatacache("item-name-".$injection['name']);
		invalidatedatacache("item-id-".db_insert_id());
		return db_insert_id();
	}
}
?>
