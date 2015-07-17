<?php
// translator ready
// addnews ready
// mail ready

function cities_getmoduleinfo(){
	$info = array(
		"name"=>"Multiple Cities",
		"version"=>"1.0",
		"author"=>"Eric Stevens",
		"category"=>"Village",
		"download"=>"core_module",
		"allowanonymous"=>true,
		"override_forced_nav"=>true,
		"settings"=>array(
			"Cities Settings,title",
			"allowance"=>"Daily Travel Allowance,int|3",
			"coward"=>"Penalise Cowardice for running away?,bool|1",
			"travelspecialchance"=>"Chance for a special during travel,int|7",
			"safechance"=>"Chance to be waylaid on a safe trip,range,1,100,1|50",
			"dangerchance"=>"Chance to be waylaid on a dangerous trip,range,1,100,1|66",
		),
		"prefs"=>array(
			"Cities User Preferences,title",
			"traveltoday"=>"How many times did they travel today?,int|0",
			"homecity"=>"User's current home city.|",
		),
		"prefs-mounts"=>array(
			"Cities Mount Preferences,title",
			"extratravel"=>"How many free travels does this mount give?,int|0",
		),
		"prefs-drinks"=>array(
			"Cities Drink Preferences,title",
			"servedcapital"=>"Is this drink served in the capital?,bool|1",
		),
	);
	return $info;
}

function cities_install(){
	module_addhook("villagetext");
	module_addhook("village");
	module_addhook("travel");
	module_addhook("count-travels");
	module_addhook("cities-usetravel");
	module_addhook("validatesettings");
	module_addhook("newday");
	//module_addhook("charstats");
	module_addhook("mountfeatures");
	module_addhook("faq-toc");
	module_addhook("drinks-check");
	module_addhook("stablelocs");
	module_addhook("camplocs");
	module_addhook("master-autochallenge");
	return true;
}

function cities_uninstall(){
	// This is semi-unsafe -- If a player is in the process of a page
	// load it could get the location, uninstall the cities and then
	// save their location from their session back into the database
	// I think I have a patch however :)
	$city = getsetting("villagename", LOCATION_FIELDS);
	$inn = getsetting("innname", LOCATION_INN);
	$sql = "UPDATE " . db_prefix("accounts") . " SET location='".addslashes($city)."' WHERE location!='".addslashes($inn)."'";
	db_query($sql);
	$session['user']['location']=$city;
	return true;
}

function cities_dohook($hookname,$args){
	global $session;
	$city = getsetting("villagename", LOCATION_FIELDS);
	$home = $session['user']['location']==get_module_pref("homecity");
	$capital = $session['user']['location']==$city;
	switch($hookname){
    case "validatesettings":
		if ($args['dangerchance'] < $args['safechance']) {
			$args['validation_error'] = "Danger chance must be equal to or greater than the safe chance.";
		}
		break;
	case "faq-toc":
		$t = translate_inline("`@Frequently Asked Questions on Multiple Villages`0");
		output_notl("&#149;<a href='runmodule.php?module=cities&op=faq'>$t</a><br/>", true);
		break;
	case "drinks-check":
		if ($session['user']['location'] == $city) {
			$val = get_module_objpref("drinks", $args['drinkid'], "servedcapital");
			$args['allowdrink'] = $val;
		}
		break;
	case "count-travels":
		global $playermount;
		$args['available'] += get_module_setting("allowance");
		if ($playermount && isset($playermount['mountid'])) {
			$id = $playermount['mountid'];
			$extra = get_module_objpref("mounts", $id, "extratravel");
			$args['available'] += $extra;
		}
		$args['used'] += get_module_pref("traveltoday");
		break;
	case "cities-usetravel":
		global $session;
		$info = modulehook("count-travels",array());
		if ($info['used'] < $info['available']){
			set_module_pref("traveltoday",get_module_pref("traveltoday")+1);
			if (isset($args['traveltext'])) output($args['traveltext']);
			$args['success']=true;
			$args['type']='travel';
		}elseif ($session['user']['turns'] >0){
			$session['user']['turns']--;
			if (isset($args['foresttext'])) output($args['foresttext']);
			$args['success']=true;
			$args['type']='forest';
		}else{
			if (isset($args['nonetext'])) output($args['nonetext']);
			$args['success']=false;
			$args['type']='none';
		}
		$args['nocollapse'] = 1;
		return $args;
		break;
	case "master-autochallenge":
		global $session;
		if (get_module_pref("homecity")!=$session['user']['location']){
			$info = modulehook("cities-usetravel",
				array(
					"foresttext"=>array("`n`n`^Startled to find your master in %s`^, your heart skips a beat, costing a forest fight from shock.", $session['user']['location']),
					"traveltext"=>array("`n`n`%Surprised at finding your master in %s`%, you feel a little less inclined to be gallivanting around the countryside today.", $session['user']['location']),
					)
				);
			if ($info['success']){
				if ($info['type']=="travel") debuglog("Lost a travel because of being truant from master.");
				elseif ($info['type']=="forest") debuglog("Lost a forest fight because of being truant from master.");
				else debuglog("Lost something, not sure just what, because of being truant from master.");
			}
		}
		break;
	case "mountfeatures":
		$extra = get_module_objpref("mounts", $args['id'], "extratravel");
		$args['features']['Travel']=$extra;
		break;
	case "newday":
		if ($args['resurrection'] != 'true') {
			set_module_pref("traveltoday",0);
		}
		set_module_pref("paidcost", 0);
		break;
	case "villagetext":
		if ($session['user']['location'] == $city){
			// The city needs a name still, but at least now it's a bit
			// more descriptive
			// Let's do this a different way so that things which this
			// module (or any other) resets don't get resurrected.
			$args['text'] = array("`Q`b`c%s, the Capital City`b`cAll around you, the people of the city of %s move about their business.  No one seems to pay much attention to you as they all seem absorbed in their own lives and problems.  Along various streets you see many different types of shops, each with a sign out front proclaiming the business done therein.  Off to one side, you see a very curious looking rock which attracts your eye with its strange shape and color.  People are constantly entering and leaving via the city gates to a variety of destinations.`n",$city,$city);
			$args['schemas']['text'] = "module-cities";
			$args['clock']="`n`QThe clock on the inn reads `^%s.`0`n";
			$args['schemas']['clock'] = "module-cities";
			if (is_module_active("calendar")) {
				$args['calendar']="`n`QYou hear a townsperson say that today is `^%1\$s`Q, `^%3\$s %2\$s`Q, `^%4\$s`Q.`n";
				$args['schemas']['calendar'] = "module-cities";
			}
			$args['title']=array("%s, the Capital City",$city);
			$args['schemas']['title'] = "module-cities";
			$args['fightnav']="Combat Avenue";
			$args['schemas']['fightnav'] = "module-cities";
			$args['marketnav']="Store Street";
			$args['schemas']['marketnav'] = "module-cities";
			$args['tavernnav']="Ale Alley";
			$args['schemas']['tavernnav'] = "module-cities";
			$args['newestplayer']="";
			$args['schemas']['newestplayer'] = "module-cities";
		}
		if ($home){
			//in home city.
			blocknav("inn.php");
			blocknav("stables.php");
			blocknav("rock.php");
			blocknav("hof.php");
			blocknav("mercenarycamp.php");
		}elseif ($capital){
			//in capital city.
			blocknav("forest.php");
			blocknav("train.php");
			blocknav("weapons.php");
			blocknav("armor.php");
		}else{
			//in another city.
			blocknav("train.php");
			blocknav("inn.php");
			blocknav("stables.php");
			blocknav("rock.php");
			blocknav("clans.php");
			blocknav("hof.php");
			blocknav("armor.php");
			blocknav("weapons.php");
			blocknav("mercenarycamp.php");
		}
		break;
	case "charstats":
		if ($session['user']['alive']){
			addcharstat("Personal Info");
			addcharstat("Home City", get_module_pref("homecity"));
			$args = modulehook("count-travels", array('available'=>0,'used'=>0));
			$free = max(0, $args['available'] - $args['used']);
			addcharstat("Extra Info");
			addcharstat("Free Travel", $free);
		}
		break;
	case "village":
		if ($capital) {
			tlschema($args['schemas']['fightnav']);
			addnav($args['fightnav']);
			tlschema();
			addnav("H?Healer's Hut","healer.php?return=village.php");
		}
		//tlschema($args['schemas']['gatenav']);
		//addnav($args['gatenav']);
		//tlschema();
		// addnav("Travel","runmodule.php?module=cities&op=travel"); No es necesario, se usa el mapa para viajar
		if (get_module_pref("paidcost") > 0) set_module_pref("paidcost", 0);
		break;
	case "travel":
		$args = modulehook("count-travels", array('available'=>0,'used'=>0));
		$free = max(0, $args['available'] - $args['used']);
		addnav("Travelpoints");
		$p=translate_inline($free!=1?"points":"point");
		$t=translate_inline($session['user']['turns']!=1?"turns":"turns");
		addnav(array("You have `\$%s %s free`0!",$free,$p),"");
		addnav(array("You have `\$%s %s free`0!",$session['user']['turns'],$t),"");
		$tfree=$free+$session['user']['turns'];
		addnav("Safer Travel");
		$hotkey = "C";
		if ($session['user']['location']!=$city){
			addnav(array("%s?Go to %s", $hotkey, $city),"runmodule.php?module=cities&op=travel&city=".urlencode($city));
		}
		addnav("More Dangerous Travel");
		if ($session['user']['superuser'] & SU_EDIT_USERS){
			addnav("Superuser");
			addnav(array("%s?Go to %s", $hotkey, $city),"runmodule.php?module=cities&op=travel&city=".urlencode($city)."&su=1");
		}
		break;
	case "stablelocs":
		$args[$city] = sprintf_translate("The City of %s", $city);
		break;
	case "camplocs":
		$args[$city] = sprintf_translate("The City of %s", $city);
		break;
	}
	return $args;
}

function cities_dangerscale($danger) {
	global $session;
	$dlevel = ($danger ?
			get_module_setting("dangerchance"):
			get_module_setting("safechance"));
	if ($session['user']['dragonkills'] <= 1) $dlevel = round(.50*$dlevel, 0);
	elseif ($session['user']['dragonkills'] <= 30) {
		$scalef = 50/29;
		$scale = (($session['user']['dragonkills']-1)*$scalef + 50)/100;
		$dlevel = round($scale*$dlevel, 0);
	} // otherwise, dlevel is unscaled.
	return $dlevel;
}

function cities_run(){
	global $session;
	require("modules/cities/run.php");
}

function cities_faq() {
	global $session;
	require("modules/cities/faq.php");
}

?>
