<?php

function basicitemshop_getmoduleinfo(){
	$info = array(
		"name"=>"Basic Item Shop",
		"version"=>"1.10",
		"author"=>"Christian Rutsch",
		"category"=>"Inventory",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1033",
		"settings"=>array(
			"Basic Item Shop, title",
				"every"=>"Shop appears in every city, bool|1",
				"location"=>"If not it appears in, location|".getsetting("villagename", LOCATION_FIELDS),
		),
	);
	return $info;
}
function basicitemshop_install(){
	module_addhook("village");
	return true;
}

function basicitemshop_uninstall(){
	return true;
}

function basicitemshop_dohook($hookname,$args){
	global $session;
	switch($hookname) {
		case "village":
			if (get_module_setting("every") || $session['user']['location'] == get_module_setting("location")) {
				tlschema($args['schemas']['marketnav']);
				addnav($args['marketnav']);
				tlschema();
				addnav("The Loiterer", "runmodule.php?module=basicitemshop");
			}
			break;
	}
	return $args;
}

function basicitemshop_run(){
	require_once("lib/itemhandler.php");
	require_once("lib/villagenav.php");
	global $session;

	$op = httpget('op');
	page_header("The Loiterer");

	switch ($op) {
		case "":
			break;
		case "buy":
			$id = (int)httpget('id');
			if (!$id) {
				shopnav("runmodule.php?module=basicitemshop&op=buy", "Loot");
			} else {
				$item = get_item($id);
				output("`@\"`6When you could give me `^%s pieces of gold`6 and `%%s Gems`6 I might consider giving away this precious item!", $item['gold'], $item['gems']);
				output("And don't try this haggling-thing with your seven starving children!`@\"");
				rawoutput("<form action='runmodule.php?module=basicitemshop&op=buy2&id=$id' method='post'>");
				rawoutput("<input type='hidden' name='gold' value='{$item['gold']}'>");
				rawoutput("<input type='hidden' name='gems' value='{$item['gems']}'>");
				rawoutput("<select name='quantity' size='1'>");
				if ($item['gold'] > 0)
					$max=floor($session['user']['gold']/$item['gold']);
				if($item['gems'] > 0)
					$max = min($max,floor($session['user']['gems']/$item['gems']));
				if($max>14){
					$gold=$item['gold'];$gems=$item['gems']?(", ".($item['gems'])." Edelsteine"):'';
					rawoutput("<option value=1>1 Stück ($gold Gold $gems)</option>");
					for($c=9;$c;$c--){
						$v=ceil($max/$c);$gold=$item['gold']*$v;$gems=$item['gems']?(", ".($item['gems']*$v)." Edelsteine"):'';
						rawoutput("<option value='$v'>$v Stück ($gold Gold $gems)</option>");
					}
				} else{
					for($c=1;$c<=$max;$c++){
						$gold=$item['gold']*$c;$gems=$item['gems']?(", ".($item['gems']*$c)." Edelsteine"):'';
						rawoutput("<option value=$c>$c Stück ($gold Gold $gems)</option>");
					}
				}
				rawoutput("</select><input type=submit value='OK!'><form>");
				addnav("", "runmodule.php?module=basicitemshop&op=buy2&id=$id");
			}
			break;
		case "buy2":
			$quantity = httppost('quantity');
			$gold = httppost('gold');
			$gems = httppost('gems');
			$id = (int)httpget('id');
			// We have to add the items first. To see how many we could really add...
			if (($sold = add_item($id, $quantity)) !== false) {
				$totalgold = $gold * $sold;
				$totalgems = $gems * $sold;
				if ($session['user']['gold'] >= $totalgold && $session['user']['gems'] >= $totalgems) {
					if($sold == $quantity) {
						output("`2You are giving `^%s pieces of gold`2 and `%%s gems`2 to the loiterer and a glad to see you are now owning a new item.", $totalgold, $totalgems);
						debuglog("spent $totalgold gold and $totalgems gems for buying $quantity items (ID: $id)");
						$session['user']['gold'] -= $totalgold;
						$session['user']['gems'] -= $totalgems;
					} else {
						output("`2The loiterer takes a look at you.");
						output("`@\"Hey Kiddie, you cannot carry more than %s additional of these things.", $sold);
						output("But these %s you can have.\"`2", $sold);
						output("You are giving `^%s pieces of gold`2 and `%%s gems`2 to the loiterer and a glad to see you are now owning a new item.", $totalgold, $totalgems);
						debuglog("spent $totalgold gold and $totalgems gems for buying $quantity items (ID: $id)");
						$session['user']['gold'] -= $totalgold;
						$session['user']['gems'] -= $totalgems;
					}
				} else {
					output("`2The loiterer looks at you with disgust. `n`n");
					output("`@\"`6You are not trying to fool me, are you?!");
					output("Come bnack, when you are rich enough to afford such valuable items!`6\"");
					output("`n`n`2Disappointed, that you did not have the smallest chance to haggle, you leave.");
					// ... then we remove them again, because they didn't have enough money for them.
					remove_item($id, $sold);
					debuglog("'s items where removed because they didn't have enough money/gems to pay for them.");
				}
			} else { // Could not add any item
				// The item might not have been added due to the following reasons:
				// 1. The Item is unique for the server and a player currently owns this item.
				// 2. The item is unique for the player and the player already owns this item.
				// 3. The total weight and/or total amount of items is already reached and this
				//    item simply won't fit in the backpack.
				output("`2The loiterer takes a look at you.");
				output("`@\"Hey Kiddie, you cannot carry any more of these items.\"`2");
				output("Having said this, he turns away to serve another well-valued customer.");
			}
			break;
		case "sell":
			$id = (int)httpget('id');
			if (!$id && httpget('id') != 'all') {
				shopnav("runmodule.php?module=basicitemshop&op=sell", "Loot", true, $session['user']['acctid'], true);
				output("`2The loiterer takes a look at your belongings to check what he might be interested in and then says: `n`n");
				if(navcount()==0) {
					output("`@\"`6I don't see anything I might want to buy from you.`@\"");
				} else if(navcount()==1) {
					output("`@\"`6This one item I'd want to have...`@\"");
				} else {
					output("`@\"`6Ahhh! A lot of precious item you have there. Come Closer!`@\"");
				}
			} else {
				if (httpget('id') == 'all') {
					$inventory = get_inventory($session['user']['acctid'], 0, "Loot");
					while ($item = db_fetch_assoc($inventory)) {
						if ($item['sellable'] == false) continue;
						$quantity += $item['quantity'];
						$goldvalue += $item['sellvaluegold'] * $item['quantity'];
						$gemvalue += $item['sellvaluegems'] * $item['quantity'];
					}
					$goldvalue = floor($goldvalue*0.9);
					$gemvalue = floor($gemvalue*0.9);
					output("`@\"`6That's pretty much, don't you think? Hmm.");
					output("Well, sorting these things will take a bit of time.");
					output("But I am willing to guve you `^%s pieces of gold`6 and `%%s gems`6 for this stuff.", $goldvalue, $gemvalue);
					output("And that's a pretty fair price, dont you think?`@\"");
					addnav("Accept", "runmodule.php?module=basicitemshop&op=sellall&gold=$goldvalue&gems=$gemvalue&quantity=$quantity");
					addnav("Reject", "runmodule.php?module=basicitemshop");
				} else {
					$item = get_inventory_item($id);
					output("`@\"`6Doesn't look that new. Hmm.");
					output("And here, look at these stains.");
					output("Giving more than `^%s pieces of gold`6 and `%%s gems`6 for this junk would be ridiculous.", $item['sellvaluegold'], $item['sellvaluegems']);
					output("And that's a pretty fair price, dont you think?`@\"");
					$ok = translate_inline("OK!");
					if ($item['quantity'] > 1) {
						output("`n`nHow many pieces do you want to sell? ");
						rawoutput("<form action='runmodule.php?module=basicitemshop&op=sell2&id=$id' method=post>");
						rawoutput("<select name='quantity'>");
						for($i=1;$i<=$item['quantity'];$i++) {
							rawoutput("<option value='$i'>$i</option>");
						}
						rawoutput("</select><input type='submit' value='$ok'><form>");
					} else {
						rawoutput("<form action='runmodule.php?module=basicitemshop&op=sell2&id=$id' method=post>");
						rawoutput("<input type='hidden' name='quantity' value=1>");
						rawoutput("</select><input type='submit' value='$ok'><form>");
					}
					addnav("", "runmodule.php?module=basicitemshop&op=sell2&id=$id");
				}
			}
			break;
		case "sell2":
			$inventory = db_prefix("inventory");
			$id = (int)httpget('id');
			$quantity = httppost('quantity');

			$sql = "SELECT * FROM $inventory WHERE userid = {$session['user']['acctid']} AND itemid = $id LIMIT $quantity";
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)) {
				$totalgold += $row['sellvaluegold'];
				$totalgems += $row['sellvaluegems'];
			}

			$session['user']['gold'] += $totalgold;
			$session['user']['gems'] += $totalgems;
			debuglog("received $totalgold gold and $totalgems gems for selling $quantity items (ID: $id)");
			output("`2The loiterer gives you `^%s pieces of gold`2 and `%%s gems`2 for ", $totalgold, $totalgems);
			if($quantity==1)
				output("your `@one item`2.");
			else
				output("your `@%s items`2. ", $quantity);
			remove_item($id, $quantity);
			break;
		case "sellall":
			$gold = httpget('gold');
			$gems = httpget('gems');
			$quantity = httpget('quantity');
			output("`2The loiterer gives you `^%s pieces of gold`2 and `%%s gems`2 for your  ", $gold, $gems);
			if($quantity==1)
				output("your `@one item`2.");
			else
				output("your `@%s items`2. ", $quantity);

			$session['user']['gold'] += $gold;
			$session['user']['gems'] += $gems;

			$sql = "SELECT itemid FROM ".db_prefix('item')." WHERE class = 'Loot' AND sellable = 1";
			$result = db_query($sql);
			$items = "(";
			while ($row = db_fetch_assoc($result)) {
				if ($items{strlen($items)-1} != "," && $counter) $items .= ",";
				$items .= $row['itemid'];
				$counter = true;
			}
			$items .= ")";
			$sql = "DELETE FROM ".db_prefix("inventory")." WHERE userid = {$session['user']['acctid']} AND itemid IN $items";
			db_query($sql);
			invalidatedatacache("inventory-user-{$session['user']['acctid']}");
			debuglog("gained $gold gold and $gems gems for selling $quantity Loot-Items at once");
			break;
	}
	addnav("Options");
	if ($op != "sell") addnav("Sell Something", "runmodule.php?module=basicitemshop&op=sell");
	if ($op != "buy") addnav("Buy Something", "runmodule.php?module=basicitemshop&op=buy");
	addnav("Back");
	villagenav();
	page_footer();
}

?>