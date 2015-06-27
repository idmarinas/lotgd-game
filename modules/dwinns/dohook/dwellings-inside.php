<?
	$dwid = httpget("dwid");
	
	$sql = "SELECT * FROM " . db_prefix("dwellings") . " WHERE dwid=$dwid";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$type = $row['type'];
	
	if($type=="dwinns"){
		$sql = "SELECT * FROM " . db_prefix("dwinns") . " WHERE dwid=$dwid";
		$result = db_query($sql);
		if (db_num_rows($result)==0){
			$playerid = $session['user']['acctid'];
			$basisrooms = get_module_setting("basisrooms","dwinns");
			$basisprice = get_module_setting("basisprice","dwinns");
			
			$sql = "INSERT INTO ".db_prefix("dwinns")." (dwid,rooms,guests,stars,price,brewname,alerounds,aleattack,aledefense,drinkqual,brewexp,meals,drinks,villageadd,brewdays,logrooms,logmeals,logdrinks) VALUES ($dwid,$basisrooms,0,0,$basisprice,'',5,5,5,0,0,0,0,0,0,0,0,0)";
			db_query($sql);
		}
		$sql = "SELECT ownerid FROM " . db_prefix("dwellings") . " WHERE dwid=$dwid";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$ownerid = $row['ownerid'];
		$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid=$ownerid";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$ownename = $row['name'];
		
		$sql = "SELECT rooms,guests,price,stars FROM " . db_prefix("dwinns") . " WHERE dwid=$dwid";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		
		output("On a plaque behind the owner, %s, you can read:`n",$ownename); 
		output("Number of rooms: %s`n",$row['rooms']); 
		output("Number of guests: %s`n",$row['guests']); 
		output("Quality of the establishment (0-10): %s stars`n",$row['stars']); 
		output("Price of a room per evening: %s gold`n`n`n",$row['price'] * $session['user']['level']);
		switch($row['stars']){
			case "10": output("`&A wooden door on the right has a sign displaying 'Sauna'. You wonder if it's mixed... "); 
			case "9": output("`&From a glass door on the left side you see an indoor pool. The rich swim, the very very rich just sit on the sides and chat about the newest rumours. ");
			case "8": output("`&An indoor water fountain with a fat cherub in the middle decorate the center of the entrance room. You check the bottom of the fountain for coins, but the staff cleans it up every couple of minutes. ");
			case "7": output("`&The light of a dozen chandeliers reflects of the pure white marble floors. Now this is CLASS. ");
			case "6": output("`&The tight shirts of the employees show every bulging muscles of their chests, while the maids have such short skirts, they should just be called 'sk'. You just can't move your eyes away. ");
			case "5": output("`&A sign advertises a quick and inexpensive room service. Maybe you WILL order lobster tonight. ");
			case "4": output("`&The mellow sound of jazz comes from a jukebox on a corner of the entrance room. This really relaxes you. ");
			case "3": output("`&A couple of guests seem to be talking about the bedding here. It seems the beds are especially confy and fluffy. ");
			case "2": output("`&A very aromatic smell comes from the kitchen. It isn't the cooking from SexyCook, but close enough. ");
			case "1": output("`&A few cats can be seen wandering around the place. Interesting enough you haven't seen a rat the whole time you've been standing here. `n`n`n"); break;
			case "0": output("`&This place is a dump! Rats and roaches actually own the place and only let you use it, if you leave it dirtier than it was before.`n`n`n"); break;
		}
		
		if($session['user']['acctid']!=$ownerid){
			blocknav("runmodule.php?module=dwellings&op=coffers&dwid=$dwid");
			blocknav("runmodule.php?module=dwellings&op=keys&subop=giveback&dwid=$dwid");
			blocknav("runmodule.php?module=dwellings&op=logout&dwid=$dwid&type=dwinns");
		}
		if(get_module_setting("ownerguest")==1 || $session['user']['acctid']!=$ownerid){
			addnav("Dwelling");
			if(get_module_pref("sleepingindwinn")!=$dwid)
				addnav("Get a room","runmodule.php?module=dwinns&op=room&dwid=$dwid");
			else
				addnav("Go to your room","runmodule.php?module=dwinns&op=room&dwid=$dwid");
			addnav("Sit at a table","runmodule.php?module=dwinns&op=table&dwid=$dwid");
			addnav("Sit at the bar","runmodule.php?module=dwinns&op=bar&dwid=$dwid");
		}
		
		if($session['user']['acctid']==$ownerid){
			blocknav("runmodule.php?module=dwellings&op=logout&dwid=$dwid&type=dwinns");
			addnav("Dwellings Extras");
			addnav("Pay for adds","runmodule.php?module=dwinns&op=village-add&dwid=$dwid");
			addnav("Kitchen","runmodule.php?module=dwinns&op=meals&dwid=$dwid");
			addnav("Brewery","runmodule.php?module=dwinns&op=drinks&dwid=$dwid");
			addnav("Build new rooms","runmodule.php?module=dwinns&op=rooms&dwid=$dwid");
			if($row['stars']<10)
				addnav("Improve the place","runmodule.php?module=dwinns&op=improve&dwid=$dwid");
			addnav("Change room price","runmodule.php?module=dwinns&op=room-price&dwid=$dwid");
			addnav("Check Statistics","runmodule.php?module=dwinns&op=stats&dwid=$dwid");
		}
	}
?>
