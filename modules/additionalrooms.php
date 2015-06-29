<?php
function additionalrooms_getmoduleinfo(){
	$info = array(
		"name"=>"Dwelling additionalrooms",
		"version"=>"20060210",
		"download"=>"http://dragonprime.net/users/sixf00t4/dwellingspack.zip",
		"author"=>"Christian Rutsch",
		"category"=>"Dwellings",
		"description"=>"Forces dwelling owners to maintain their dwellings",
		"requires"=>array(
			"dwellings"=>"20060105|By Sixf00t4, available on DragonPrime",
			"inventory"=>"2.0|By Christian Rutsch, available on DragonPrime",
		), 
		"settings"=>array(
			"location"=>"Where can the shop be found?,location|".LOCATION_FIELDS,
		),
		"prefs-dwellingtypes"=>array(
			"Dwellingtype Room Prefs,title",
			"maxrooms"=>"How many rooms can be added to this dwelling?,int|1",
		),
		"prefs-dwellings"=>array(
			"Dwelling Room Prefs,title",
			"currentrooms"=>"How many rooms are associated with this dwelling?,int|0",
		),
	);
	return $info;
}

function additionalrooms_install(){
	module_addhook("dwellings-manage");
	module_addhook("dwellings-inside");
	module_addhook("village");
	// Insert sample room	
	require_once("lib/itemhandler.php");
	$kitchen = array(
		"name"=>"Small kitchen",
    	"class"=>"Rooms",
    	"description"=>"A small kitchen, a single fire with a brass cauldron above it, two wooden shelves and a small footstool",
    	"gold"=>5000,
    	"gems"=>10,
		"hide"=>true,
    	"buyable"=>true,
    	"sellable"=>true,
	);
	$sleepingroom = array(
		"name"=>"Sleeping Room",
    	"class"=>"Rooms",
    	"description"=>"This room contains nothing but a wooden bed and a small box in the corner. Just by entering the room you can feel the lice running towards you.",
    	"gold"=>25000,
    	"gems"=>50,
		"hide"=>true,
    	"buyable"=>true,
    	"sellable"=>true,
	);
	inject_item($kitchen, array("class", "description", "gold", "gems", "buyable", "sellable"));
	inject_item($sleepingroom, array("class", "description", "gold", "gems", "buyable", "sellable"));
	return true;
}

function additionalrooms_uninstall() {
	return true;
}

function additionalrooms_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {

		case "dwellings-manage":
			$item = db_prefix('item');
			$inventory = db_prefix('inventory');
			$sql = "SELECT $item.itemid AS itemid, $item.name AS name FROM $item INNER JOIN $inventory ON $item.itemid = $inventory.itemid
						WHERE $inventory.userid = {$session['user']['acctid']} AND $inventory.specialvalue={$args['dwid']}
							AND $item.class = 'Rooms'";
			$result = db_query($sql);
			if (db_num_rows($result)) {
				addnav("Manage rooms", "runmodule.php?module=additionalrooms&op=manage&dwid={$args['dwid']}");
			} else {
				addnav("Manage rooms", "");
			}
			break;
		case "dwellings-inside":
			$item = db_prefix('item');
			$inventory = db_prefix('inventory');
			$sql = "SELECT $item.itemid AS itemid, $item.name AS name FROM $item INNER JOIN $inventory ON $item.itemid = $inventory.itemid
						WHERE $inventory.userid = {$args['owner']} AND $inventory.specialvalue={$args['dwid']}
							AND $item.class = 'Rooms' ORDER BY $inventory.invid ASC";
			$result = db_query($sql);
			addnav("Rooms");
			while ($row = db_fetch_assoc($result)) {
				$roomname = translate_inline($row['name']);
				addnav(array("Enter %s", $roomname), "runmodule.php?module=additionalrooms&op=enter&room={$row['itemid']}&return={$args['dwid']}");
			}
			break;
		case "village":
			if ($session['user']['location'] == get_module_setting("location")) {
				tlschema($args['schemas']['marketnav']);
				addnav($args['marketnav']);
				tlschema();
				addnav("Interior Designer", "runmodule.php?module=additionalrooms");
			}
	}
	return $args;
}

function additionalrooms_run(){
	require_once("lib/itemhandler.php");
	require_once("lib/villagenav.php");
	global $session;

	$op = httpget('op');
	page_header("The Interior Designer");

	switch ($op) {
		case "":
			page_header("The Interior Designer");
			output("`vYou enter the large shop of the `GInterior Designer`v.");
			output("Everywhere you can see woolen fabrics lying around, statues of naked men, woman and beasts you have never seen before.");
			output("Pieces of raw china and and unfinished marble can be seen in a corner of the room and two dwarves and a young elf are using their hammers and chisel to create wonderful new pieces of art.`n");
			output("But surely the most eye-catching sight is the `Glarge male felyne`v who seems to be running this shop.");
			output("Only clothed in a way too thin satin tunic, you ");
			if ($session['user']['sex'] == SEX_MALE) {
				output("try to not look at him too close.");
				output("People might think strange of you.");
			} else {
				output("can hardly keep you eyes off of this wonderful sight.");
				output("Seems you will be willing to buy simple anything from him.");
			}
			output("He welcomes you with a smile, \"`GWelcome to my shop of thousand choices!");
			output("Here you will find anything you need for your dwelling!`v\"");
			addnav("Options");
			addnav("Buy Something", "runmodule.php?module=additionalrooms&op=buy");
			addnav("Back");
			villagenav();
			break;
		case "buy":
			page_header("The Interior Designer");
			$id = (int)httpget('id');
			if (!$id) {
				shopnav("runmodule.php?module=additionalrooms&op=buy", "Rooms");
			} else {
				$item = get_item($id);
				output("`v\"`GAaaaw, a fine choice you made. Only `^%s pieces of gold`G and `%%s Gems`G and your dwelling will shine in a new light!", $item['gold'], $item['gems']);
				output("This is really nothing compared to the effect it will have on your visitors!`v\", he purrs.");
				rawoutput("<form action='runmodule.php?module=additionalrooms&op=buy2&id=$id' method='post'>");
				rawoutput("<input type='hidden' name='gold' value='{$item['gold']}'>");
				rawoutput("<input type='hidden' name='gems' value='{$item['gems']}'>");
				rawoutput("<input type='hidden' name='quantity' value='1'>");
				rawoutput("<input type=submit value='OK!'><form>");
				addnav("", "runmodule.php?module=additionalrooms&op=buy2&id=$id");
			}
			addnav("Options");
			if ($op != "buy") addnav("Buy Something", "runmodule.php?module=additionalrooms&op=buy");
			addnav("Back");
			villagenav();
			break;
		case "buy2":
			page_header("The Interior Designer");
			require_once("modules/dwellings/func.php");
			$gold = httppost('gold');
			$gems = httppost('gems');
			$id = httpget('id');
			$sql = "SELECT dwid, name, type, location FROM ".db_prefix("dwellings")." WHERE ownerid = {$session['user']['acctid']} AND status = 1";
			$result = db_query($sql);
			addnav("Where...");
			$possible=0;
			while ($row = db_fetch_assoc($result)) {
				$crooms = get_module_objpref("dwellings", $row['dwid'], "currentrooms");
				$mrooms = get_module_objpref("dwellingtypes", get_typeid($row['type']), "maxrooms");
				if ($crooms >= $mrooms) {
					addnav(array("%s`0 in %s (%s/%s)", $row['name'], $row['location'], $crooms, $mrooms), "");
				} else {
					addnav(array("%s`0 in %s (%s/%s)", $row['name'], $row['location'], $crooms, $mrooms), "runmodule.php?module=additionalrooms&op=buy3&gold=$gold&gems=$gems&dwid={$row['dwid']}&id=$id");
					$possible++;
				}
				if ($possible) {
					output("`v\"`GTo which of your dwellings should this room be attached?`v\", the `Gfelyne`v asks you.");
				} else {
					output("`v\"`gIt seems to me, there is no dwelling which can hold another room...`v\", the `Gfelyne`v purrs annoyed.");
				}
			}
			addnav("Options");
			addnav("Buy Something", "runmodule.php?module=additionalrooms&op=buy");
			addnav("Back");
			villagenav();
			break;
		case "buy3":
			page_header("The Interior Designer");
			$gold = httpget('gold');
			$gems = httpget('gems');
			$id = (int)httpget('id');
			$dwid = httpget('dwid');
			if ($session['user']['gold'] >= $gold && $session['user']['gems'] >= $gems) {
				$crooms = get_module_objpref("dwellings", $dwid, "currentrooms");
				set_module_objpref("dwellings", $dwid, "currentrooms", $crooms + 1);
				debuglog("spent $gold gold and $gems gems for buying 1 room (ID: $id)");
				$session['user']['gold'] -= $gold;
				$session['user']['gems'] -= $gems;
				if(! add_item($id, 1, $session['user']['acctid'], $dwid)) {
					output("Something went totally wrong when adding this new item.");
					output("Maybe you are not allowed to own more items than you are currently carrying?");
				} else {
				}
			} else {
				output("`vThe felyne musters you from top to toe.`n`n");
				output("\"`GI do not think you can affort such beauties.");
				output("Come bnack, when you are abel to pay for this!`v\"");
				output("`n`n`vDisappointed, that you did not have the smallest chance to haggle, you leave.");
			}
			addnav("Options");
			addnav("Buy Something", "runmodule.php?module=additionalrooms&op=buy");
			addnav("Back");
			villagenav();
			break;
		case "enter":
			$room = httpget('room');
			$item = get_item((int)$room);
			page_header($item['name']);
			$return = httpget('return');
			addnav("Return");
			addnav("Return to the dwelling", "runmodule.php?module=dwellings&op=enter&dwid=$return");
			$inventory = db_prefix("inventory");
			$sql = "SELECT * FROM $inventory WHERE invid = $room";
			$result = db_query($sql);
			if ($row = db_fetch_assoc($result)) {
				require_once("lib/commentary.php");
				addcommentary();
				// This is okay, because the description needs to be translated. 
				// Single language servers will not need to translate this, 
				// dual language servers will be able to translate it.
				output($item['description']);
				output_notl("`n`n");
				commentdisplay("`@Converse with others:", "rooms-".$row['invid'], "Talk", 25);
			} else {
				output("This room has already been torn down. Please return to the dwelling's main room.");
			}
			break;
		case "manage":
			page_header("Room Management");
			$dwid = httpget('dwid');
			$crooms = get_module_objpref("dwellings", $dwid, "currentrooms");
			$invid = httpget('invid');
			if ($invid != 0) {
				$itemid = httpget('item');
				remove_item((int)$itemid, 1, false, $invid);
				set_module_objpref("dwellings", $dwid, "currentrooms", $crooms-1);
			}
			addnav("Tear down a room...");
			$item = db_prefix('item');
			$inventory = db_prefix('inventory');
			$sql = "SELECT $item.itemid, $item.name AS name, $inventory.invid AS invid FROM $item INNER JOIN $inventory ON $item.itemid = $inventory.itemid
						WHERE $inventory.userid = {$session['user']['acctid']} AND $inventory.specialvalue=$dwid
						AND $item.class = 'Rooms' ORDER BY $inventory.invid ASC";
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)) {
				$roomname = translate_inline($row['name']);
				addnav(array("Delete %s", $roomname), "runmodule.php?module=additionalrooms&op=manage&invid={$row['invid']}&item={$row['itemid']}&dwid=$dwid");
			}
			addnav("Return");
			addnav("Dwelling Management","runmodule.php?module=dwellings&op=manage&dwid=$dwid");
			break;
	}
	page_footer();
}
?>