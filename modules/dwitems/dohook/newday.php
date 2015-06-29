<?php
if($session['user']['location']==get_module_setting("logoutlocation","dwellings")){
	debug("Slept Inside Dwelling");
	$dwid = get_module_pref("dwelling_saver","dwellings");
	if($dwid!=""){
		$dwitems = db_prefix("dwitems");
		$dwellingsitems = db_prefix("dwellingsitems");
		$sql = "SELECT $dwitems.name AS name, $dwitems.amount AS amount, $dwitems.chance AS chance, $dwitems.type AS type, $dwitems.newdaytext AS newdaytext, $dwellingsitems.quantity AS quantity FROM $dwitems INNER JOIN $dwellingsitems ON $dwitems.itemID = $dwellingsitems.itemID WHERE $dwellingsitems.dwid=$dwid";
		$result = db_query($sql);
		$maxchance = get_module_setting("maxchance","dwitems");
		while($row = db_fetch_assoc($result)){
			debug($row['name']);
			$quantity=$row['quantity'];
			if($quantity > 1 && $quantity > ($row['chance'] / 100 * $maxchance)){
				$quantity = floor($row['chance'] / 100 * $maxchance);
			}
			if(e_rand(1,$row['chance'])<=$quantity){
				if($row['type']!=7){
					output("`n%s`n",$row['newdaytext']);
					switch($row['type']){
						case 0:
							$session['user']['charm']+=$row['amount'];
							break;
						case 1:
							$session['user']['gold']+=$row['amount'];
							break;
						case 2:
							$session['user']['gems']+=$row['amount'];
							break;
						case 3:
							$session['user']['hitpoints']+=$row['amount'];
							break;
						case 4:
							$session['user']['maxhitpoints']+=$row['amount'];
							break;
						case 5:
							$session['user']['deathpower']+=$row['amount'];
							break;
						case 6:
							$session['user']['turns']+=$row['amount'];
							break;
					}
				}else{
					if($row['newdaytext']=="")
					require_once("modules/dwitems/items/" . preg_replace("/\s/", "_", $row['name']) . "_newday.php");
					else
					output("`n%s`n",$row['newdaytext']);
				}
			}
		}
	}
}
?>
