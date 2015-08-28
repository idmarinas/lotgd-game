<?php
function inventoryviewer_getmoduleinfo(){
    $info = array(
        "name"=>"Inventory Viewer for Admins",
        "author"=>"Christian Rutsch",
        "version"=>"1.0",
        "category"=>"Administrative",
        "download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1033",
    );
    return $info;
}

function inventoryviewer_install(){
	module_addhook("modifyuserview");
	return true;
}

function inventoryviewer_uninstall(){
	return true;
}

function inventoryviewer_dohook($hookname, $args){
	global $session;
	switch($hookname) {
		case "modifyuserview":
			require_once("lib/sanitize.php");
			$inventory = db_prefix("inventory");
			$item = db_prefix("item");
			$itemid = (int)httpget('deleteitem');
			if ($itemid > 0) {
				$sql = "DELETE FROM $inventory WHERE invid = $itemid LIMIT 1";
				db_query($sql);
				if (db_affected_rows()) {
					output("Item gel�scht.`n");
				}
			} else if (httppostisset('additem') && httppost('additem') > 0) {
				require_once("lib/itemhandler.php");
				add_item((int)httppost('additem'), 1, $args['user']['acctid']);
				output("Ein Item wurde hinzugef�gt.`n");
			} 
			if (httppostisset('deleteitemarray') && count($array = httppost('deleteitemarray')) > 0){
				$in = join(",", $array);
				$sql = "DELETE FROM $inventory WHERE invid IN ($in)";
				db_query($sql);
				output("%s Items gel�scht.", db_affected_rows());
			}

			$sql = "SELECT itemid, name, class FROM $item ORDER BY class ASC, name ASC";
			$result = db_query_cached($sql, "allitems", 3600);
			if (db_num_rows($result)) {
				output("`n`nEin Item an den Spieler geben: ");
				rawoutput("<select name='additem'>");
				rawoutput("<option value='0'>Keins</option>");
				$class = "";
				while ($row = db_fetch_assoc($result)) {
					if ($class != $row['class']) {
						if ($class != "") rawoutput("</optgroup>");
						rawoutput("<optgroup label='{$row['class']}'>");
						$class = $row['class'];
					}
					rawoutput("<option value='{$row['itemid']}'>".(appoencode($row['name']))."</option>");
				}
				rawoutput("</optgroup>");
				rawoutput("</select><br>");
			}			

			array_push($args['userinfo'], "Inventar,title");
			$acctid = $args['user']['acctid'];
			$sql = "SELECT $item.class AS class, $item.name AS name, $inventory.* FROM $inventory 
						INNER JOIN $item ON $inventory.itemid = $item.itemid			
						WHERE userid = $acctid ORDER BY $item.class ASC, $item.name ASC, $inventory.invid DESC";
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)){
				$varname = 'inventory'.$row['invid'];
				$args['userinfo'][$varname] = "<input type='checkbox' name='deleteitemarray[]' value='{$row['invid']}'> ".$row['class']." / ".sanitize($row['name']).",viewonly";
				$args['user'][$varname] = sprintf("Gold %s, Gems %s, Ladungen %s [ <a href='user.php?op=edit&userid={$args['user']['acctid']}&deleteitem={$row['invid']}'>L�schen</a> ]", $row['sellvaluegold'], $row['sellvaluegems'], $row['charges']);
				addnav("", "user.php?op=edit&userid={$args['user']['acctid']}&deleteitem={$row['invid']}");
			}
			break;
	}
	return $args;
}
?>