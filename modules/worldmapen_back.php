<?php
/***********************************************
 World Map
 Originally by: Aes 
 Updates & Maintenance by: Kevin Hatfield - Arune 
 Email: (khatfield@ecsportal.com)
 http://www.dragonprime.net
 Updated: Sept 15, 2007
 ************************************************/
function worldmapen_getmoduleinfo(){
	$info = array(
		"name"=>"World Map",
		"version"=>"0.8c",
		"author"=>"<a href=\"http://logd.ecsportal.com\" target=_new>Originally: AES - Maintained by: Kevin Hatfield</a>",
		"category"=>"Map",
		"download"=>"http://logd.ecsportal.com/download/worldmap08c.zip",
		"vertxtloc"=>"http://www.dragonprime.net/users/khatfield/",
		"settings"=>array(
			"World Map Settings,title",
			"worldmapsizeX"=>"How wide is the world? (X),int|5",
			"worldmapsizeY"=>"How long is the world? (Y),int|5",
			"extraTravels"=>"How many additional travels are they given per day,int|5",
			"manualmove"=>"Turn on Superuser manual movement?,bool|0",
			"viewRadius"=>"How many squares far can a player see while traveling?,range,0,10,2",
			"worldmapAcquire"=>"Can the world map be purchased?,bool|1",
			"worldmapCostGold"=>"How much gold does the Wold Map cost?,int|10000",
			"enableTerrains"=>"Enable Terrains?,bool|1",
			"showcompass"=>"Show images/compass.gif?,bool|0",
			"compasspoints"=>"8 point compass?,bool|0",
			"showcities"=>"Show the cities in the key? / Will show all cities,bool|0",
			"smallmap"=>"Show small map?,bool|1",
			"showforestnav"=>"Show the forest link in village?,bool|0",
			"wmspecialchance"=>"Chance for a special during travel,int|7",
			"randevent"=>"Random Event -Don't Edit,text|forest",
			"randchance"=>"Percent chance you will get a travel module instead of forest,range,5,50,5",
			
			"Visual Map Settings,title",
			"colorUserLoc"=>"What color background is the users current location?,text|#FF9900",
			"colorPlains"=>"Color of plains? (default tile),text|#008800",
			"colorForest"=>"Color of dense forest?,text|#005500",
			"colorRiver"=>"Color of river?,text|#0000CC",
			"colorOcean"=>"Color of ocean?,text|#000066",
			"colorDesert"=>"Color of desert?,text|#DDDD33",
			"colorSwamp"=>"Color of swamp?,text|#669900",
			"colorMount"=>"Color of mountains?,text|#999999",
			"colorSnow"=>"Color of Snow?,text|#FFFFFF",
			
			"Boundary Messages,title",
			"nBoundary"=>"Northern boundary,text|To the north are the impenetrable mountains of Loa.",
			"eBoundary"=>"Eastern boundary,text|The vast ocean of silence lay to your east.  Long before you can remember ships stopped sailing across to the other continents.  But why?",
			"sBoundary"=>"Southern boundary,text|To the south you can see a great ravine that seems to stretch on forever.",
			"wBoundary"=>"Western boundary,text|To the west lays the barren wasteland of the Goiu desert.  No one has ever survived out there.",
			
			"Gate Messages,title",
			"LeaveGates1"=>"Leave gates of village. (1)|A shiver runs down your back as you face the forest around you.",
			"LeaveGates2"=>"Leave gates of village. (2)|You're all alone now...",
			"LeaveGates3"=>"Leave gates of village. (3)|The sound of the forest settles in around you as you think to yourself what evil must lurk within.",
			"LeaveGates4"=>"Leave gates of village. (4)|Perhaps I should go back in...",
			"LeaveGates5"=>"Leave gates of village. (5)|A howling noise bellows from deep within the forest.  You hear the guards from the other side of the gates yell \"Good Luck!\" and what sounds like \"they'll never make it.",
			
			"Terrain Encounter Settings,title",            
			"encounterPlains"=>"Chance of encountering a monster when crossing plains?,int|20",
			"encounterForest"=>"Chance of encountering a monster when crossing dense forests?,int|85",
			"encounterRiver"=>"Chance of encountering a monster when crossing rivers?,int|20",
			"encounterOcean"=>"Chance of encountering a monster when crossing oceans?,int|20",
			"encounterDesert"=>"Chance of encountering a monster when crossing deserts?,int|85",
			"encounterSwamp"=>"Chance of encountering a monster when crossing swamps?,int|85",
			"encounterMount"=>"Chance of encountering a monster when crossing mountains?,int|20",
			"encounterSnow"=>"Chance of encountering a monster when crossing snow?,int|20",
			
			"Terrain Settings,title",            
			"moveCostPlains"=>"Movement cost for crossing plains?,int|1",
			"moveCostForest"=>"Movement cost for crossing dense forests?,int|1",
			"moveCostRiver"=>"Movement cost for crossing rivers?,int|1",
			"moveCostOcean"=>"Movement cost for crossing oceans?,int|5",
			"moveCostDesert"=>"Movement cost for crossing deserts?,int|2",
			"moveCostSwamp"=>"Movement cost for crossing swamps?,int|2",
			"moveCostMount"=>"Movement cost for crossing mountains?,int|3",
			"moveCostSnow"=>"Movement cost for crossing snow?,int|3",			
		),
		"prefs"=>array(
			"World Map User Preferences,title",
			"worldXYZ"=>"World Map X Y Z (seperated by commas!)|0,0,0",
			"canedit"=>"Does user have rights to edit the map?,bool|0",
			"lastCity"=>"Where did the user leave from last?|",   
			"worldmapbuy"=>"Did user buy map?,bool|0",
		)
	);
	return $info;
}
function worldmapen_install(){
	if (!is_module_installed("cities")) {
	output("`b`^***** This module requires the Multiple Cities module to be installed. *****`b`7");
	return false;
	} else {
		module_addhook("village");
		module_addhook("villagenav");
		module_addhook("mundanenav");
		module_addhook("superuser");
		module_addhook("pvpcount");
		module_addhook("footer-gypsy");
		module_addhook("count-travels");
		module_addhook("changesetting");
	 return true;
	}
}
function worldmapen_uninstall(){
		return true;
}
function worldmapen_dohook($hookname,$args)
{
	global $session;
	// If the cities module is deactivated, we do nothing.
	if (!is_module_active("cities")) return $args;
	switch($hookname){
	case "pvpcount":
		if ($args['loc'] != "World") break;
		$args['handled'] = 1;
		if ($args['count'] == 1) {
			output("`&There is `^1`& person camping in the wilderness whom you might find interesting.`0`n");
		} else {
			output("`&There are `^%s`& people camping in the wilderness whom you might find interesting.`0`n", $args['count']);
		}
		break;
	case "villagenav":
		if ($session['user']['location'] != 'World') break;
		addnav("V?Return to the World", "runmodule.php?module=worldmapen&op=continue");
		$args['handled'] = 1;
		break;
	case "mundanenav":
		if ($session['user']['location'] != 'World') break;
		addnav("M?Return to the Mundane", "runmodule.php?module=worldmapen&op=continue");
		$args['handled'] = 1;
		break;
	case "count-travels":
		$args['available'] += get_module_setting("extraTravels");
		break;
	case "footer-gypsy":
		if (get_module_setting("worldmapAcquire") == 1 && $op==""){
			addnav("Map");
			addnav("Ask about World Map",
					"runmodule.php?module=worldmapen&op=gypsy");
		}
		break;
	case "changesetting":
		// We only care about the names of locations.
		if ($args['setting'] != "villagename") break;
		$old = $args['old'];
		$new = $args['new'];
		// Handle any locations of the old name and convert them.
		$x = get_module_setting($old.'X');
		$y = get_module_setting($old.'Y');
		$z = get_module_setting($old.'Z');
		set_module_setting('worldmapen'.$new.'X', $x);
		set_module_setting('worldmapen'.$new.'Y', $y);
		set_module_setting('worldmapen'.$new.'Z', $z);
		set_module_setting('worldmapen'.$old.'X', "");
		set_module_setting('worldmapen'.$old.'Y', "");
		set_module_setting('worldmapen'.$old.'Z', "");
		// Handle any players who last city was the old name.
		$sql = "UPDATE " . db_prefix("module_userprefs") . " SET value='".addslashes($new) . "' WHERE value='".addslashes($old) ."' AND modulename='worldmapen' AND setting = 'lastCity'";
		db_query($sql);
		break;
	case "village":
		blocknav("runmodule.php?module=cities&op=travel");
		if (get_module_setting("showforestnav")==0) blocknav("forest.php");
	addnav($args["gatenav"]);
		addnav("Journey","runmodule.php?module=worldmapen&op=beginjourney");
		break;
	case "superuser":
		if (($session['user']['superuser'] & SU_EDIT_USERS) || get_module_pref("canedit")) {
			addnav("Module Configurations");
			addnav("World Map Editor","runmodule.php?module=worldmapen&op=edit&admin=true");
		}
		break;
	}
		return $args;        
}
function worldmapen_editor()
{
	global $session;
	$op = httpget("op");
	$act = httpget("act");
	$subop = httpget("subop");
	page_header("World Editor");
	require_once("lib/superusernav.php");
	superusernav();
	if ($subop == ""){
		worldmapen_viewmap(false);
	} elseif ($subop == "regen") {
		worldmapen_defaultcityloc();
		worldmapen_viewmap(false);
	} elseif ($subop == "manual") {
		$vloc = array();
		$vname = getsetting("villagename", LOCATION_FIELDS);
		$vloc[$vname] = "village";
		$vloc = modulehook("validlocation", $vloc);
		ksort($vloc);
	if ($act == "save"){
			foreach($vloc as $loc=>$val) {
				$space_valx = preg_replace('/\s/', '_',$loc.'X');
				$space_valy = preg_replace('/\s/', '_',$loc.'Y');
				set_module_setting($loc.'X',httppost($space_valx));
				set_module_setting($loc.'Y',httppost($space_valy));
				set_module_setting($loc.'Z', 1);
				// Eventually we'll do the Z coord too
				// set_module_setting($loc.'Z',
				//        httppost($loc."Z"));
			}
			output("`^`bSettings saved successfully.`b`n");
			reset($vloc);
		}
		output("`^Maximum X value is `b%s`b`n",
			 get_module_setting("worldmapsizeX"));
		output("`^Maximum Y value is `b%s`b`n",
			 get_module_setting("worldmapsizeY"));
		//load_module_settings("worldmap");
		$worldarray=array("World Locations,title");
		foreach($vloc as $loc=>$val) {
		$mapx=get_module_setting("worldmapsizeX");
		$mapy=get_module_setting("worldmapsizeY");
		//Added to allow setting cities outside of the map. - Making cities inaccessible via normal travel.
			$myx = $mapx+1;
			$worldarray[] = array("Locations for %s,title", $loc);
			$worldarray[$loc.'X']=array("X Coordinate,range,1,$myx,1");
			$worldarray[$loc.'Y']=array("Y coordinate,range,1,$mapy,1");
		}
		rawoutput("<form method='post' action='runmodule.php?module=worldmapen&op=edit&subop=manual&act=save&admin=true'>");
		require_once("lib/showform.php");
		global $module_settings;
		showform($worldarray, $module_settings['worldmapen']);
		rawoutput("</form>");
		addnav("","runmodule.php?module=worldmapen&op=edit&subop=manual&act=save&admin=true");    
		addnav("E?Return to World Map Editor","runmodule.php?module=worldmapen&op=edit&admin=true");
	}elseif ($subop == "terrain"){
			if ($act == "save"){
				if (get_module_setting('worldmapTerrain')==1){
					db_query("DELETE FROM ". db_prefix("module_settings") ." where modulename='worldmapen' AND setting LIKE 'Terrain%'");
					output("Settings Updated Successfully");
				}else{
					set_module_setting('worldmapTerrain', "1");   
			  output("Settings Saved Successfully");
		  }
					// Make one big SQL statment to insert all terrain data.  This provides better performance
					// than doing one SQL statment or set_module_setting per record.
					$sql = "INSERT INTO ". db_prefix("module_settings") ." (modulename, setting, value) VALUES ";     
				reset ($_POST);
				while(list($key, $val) = each ($_POST)){
					$newkey = str_replace("_",",","$key");
				$sql .= "(\"worldmapen\",\"Terrain-$newkey,1\",\"$val\"),";          
				//print $newkey . " = " . $val . "<br>";
			  }
			  $sql = substr($sql,0, -1);
			  //echo $sql;
			  db_query($sql);
			}
			// -----------------------------------------------------------------------
			// BEGIN - Java script to determine the terrain type by clicking on a td cell
			// -----------------------------------------------------------------------
			rawoutput("<script language=\"javascript\">");
			rawoutput("colors = [\"#008800\", \"#005500\", \"#0000CC\", \"#000066\", \"#DDDD33\", \"#669900\", \"#999999\", \"#FFFFFF\"];");
			rawoutput("cRGB = [];");
			rawoutput("function toRGB(color){");
			rawoutput("var rgb = \"rgb(\" + parseInt(color.substring(1,3), 16) + \", \" + parseInt(color.substring(3,5), 16) + \", \" + parseInt(color.substring(5,8), 16) + \")\";   ");
			rawoutput("return rgb;");
			rawoutput("}");
			rawoutput("for(var i=0; i<colors.length; i++){");
			rawoutput("cRGB[i] = toRGB(colors[i]);");
			rawoutput("}");
			rawoutput("function changeColor(target){");
			rawoutput("var swapper = navigator.appVersion.indexOf(\"MSIE\")!=-1 ? toRGB(document.getElementById(target).style.backgroundColor) : document.getElementById(target).style.backgroundColor;");
			rawoutput("var set = false;");
			rawoutput("var xx;");
			rawoutput("for(var i=0; i<cRGB.length; i++){");
			rawoutput("if(swapper == cRGB[i]){");
			rawoutput("if(((i+1)) >= cRGB.length){");
			rawoutput("xx = 0;");
			rawoutput("}else{");
			rawoutput("xx = i+1;");
			rawoutput("}");
			rawoutput("document.getElementById(target).style.background = colors[xx];");
			rawoutput("document.getElementById(target+\"b\").value = xx;");
			rawoutput("set = true;");
			rawoutput("i=cRGB.length;");
			rawoutput("}");
			rawoutput("}");
			rawoutput("set ? null : (document.getElementById(target).style.background = colors[1], document.getElementById(target+\"b\").value = 1);");
			rawoutput("}");
			rawoutput("</script>");
			// -----------------------------------------------------------------------
			// END - Java script to determine the terrain type by clicking on a td cell
			// -----------------------------------------------------------------------    
			worldmapen_viewmap(false);
		}        
			addnav("Replace Cities","runmodule.php?module=worldmapen&op=edit&subop=regen");
			addnav("Manually Place Cities","runmodule.php?module=worldmapen&op=edit&subop=manual");
			addnav("Edit terrain type","runmodule.php?module=worldmapen&op=edit&subop=terrain");           
	page_footer();
}
function worldmapen_run(){
	global $session, $badguy, $pvptimeout;
	$op = httpget("op");
	// handle the admin editor first
	if ($op == "edit") {
		if (!get_module_pref("canedit")) check_su_access(SU_EDIT_USERS);
		if (get_module_setting("worldmapenInstalled")!=1){
			set_module_setting('worldmapenInstalled', "1");
			worldmapen_defaultcityloc();
		}
		worldmapen_editor();
	}
	if ($op == "destination"){
			$cname = httpget("cname");
				$session['user']['location']=$cname;
				addnav(array("Enter %s",$cname),"village.php");
				output("`c`4`bYou've Arrived in %s .`0`b`c`n", $cname);
				output("`cYou have reached the outer gates of the city.`c");
	}
	if (!get_module_setting("worldmapenInstalled")) {
		page_header("A rip in the fabric of space and time");
		require_once("lib/villagenav.php");
		villagenav();
		output("`^The admins of this game haven't yet finished installing the worldmapen module.");
		output("You should send them a petition and tell them that they forgot to generate the initial locations of the cities.");
		output("Until then, you are kind of stuck here, so I hope you like where you are.`n`n");
		output("After all, remember:`nWherever you go, there you are.");
		page_footer();
	}
		$subop = httpget("subop");
		$act = httpget("act");
		$type = httpget("type");
		$name = httpget("name");
		$direction = httpget("dir");
		$su = httpget("su");
		$buymap = httpget("buymap");
		$worldmapCostGold = get_module_setting("worldmapCostGold");
		$pvp = httpget('pvp');
		require_once("lib/events.php");
		if ($session['user']['specialinc'] != "" || httpget("eventhandler")){
		$in_event = handle_event(get_module_setting("randevent"),
			"runmodule.php?module=worldmapen&op=continue&", "Travel");
		if ($in_event) {
			addnav("Continue","runmodule.php?module=worldmapen&op=continue");
			module_display_events(get_module_setting("randevent"),"runmodule.php?module=worldmapen&op=continue&");
			page_footer();
		}
	}
		page_header("Journey");
	if ($op == "beginjourney"){
			$loc = $session['user']['location'];
			$x = get_module_setting($loc."X");
			$y = get_module_setting($loc."Y");
			$z = get_module_setting($loc."Z");
			$xyz = $x.",".$y.",".$z;
			set_module_pref("worldXYZ", $xyz);
			output("`&`bThe gates of %s stand closed behind you.`b`n`n",
			$session['user']['location']);
			$num = e_rand(1, 5);
			$msg = get_module_setting("leaveGates$num");
			output_notl("`c`n`^%s`7`n`c`n",$msg);             
			worldmapen_determinenav();
		if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
		module_display_events(get_module_setting("randevent"),"runmodule.php?module=worldmapen&op=continue&");
			}elseif ($op == "continue") {
			checkday();
			output("`c`nYou think to yourself what a nice day it is.`c`n");
			worldmapen_determinenav();
		if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
			}elseif ($op == "move") {
			checkday();
		if ($session['user']['location'] != 'World') {
			set_module_pref("lastCity", $session['user']['location']);
			$session['user']['location'] = "World";
		}
		$session['user']['restorepage'] = "runmodule.php?module=worldmapen&op=continue";
		$loc = get_module_pref('worldXYZ');
		list($x, $y, $z) = explode(",", $loc);
		if ($direction == "north") $y += 1;
		if (get_module_setting("compasspoints") == "1" AND $direction == "northeast"){
			$y += 1;
			$x += 1;
		}
		if (get_module_setting("compasspoints") == "1" AND $direction == "northwest"){
			$y += 1;
			$x -= 1;
		}
		if ($direction == "east") $x += 1;
		if ($direction == "south") $y -= 1;
		if (get_module_setting("compasspoints") == "1" AND $direction == "southeast"){
			$y -= 1;
			$x += 1;
		}
		if (get_module_setting("compasspoints") == "1" AND $direction == "southwest"){
			$y -= 1;
			$x -= 1;
		}
		if ($direction == "west") $x -= 1;
		$terraincost = worldmapen_terrain_cost(get_module_setting("Terrain-$x,$y,$z"));
		$encounter = worldmapen_encounter(get_module_setting("Terrain-$x,$y,$z"));
		$ttoday = get_module_pref("traveltoday", "cities");
		set_module_pref("traveltoday", $ttoday+$terraincost, "cities");
		$xyz = $x.",".$y.",".$z;
		set_module_pref("worldXYZ", $xyz);
	$randchance = get_module_setting("randchance");
		if (e_rand(0,100) < $randchance){
				$eventravel = "travel";
				set_module_setting("randevent", $eventravel);
		}else{
				$eventravel = "forest";
				 set_module_setting("randevent", $eventravel);
	   }
	if (e_rand(0, 100) < $encounter && $su!= '1') {
			// They've hit a monster!
		if (module_events(get_module_setting("randevent"), get_module_setting("wmspecialchance"),"runmodule.php?module=worldmapen&op=continue&") != 0) {
					page_header("Something Special!");
					if (checknavs()) {
						page_footer();
					} else {
						// Reset the special for good.
						$session['user']['specialinc'] = "";
						$session['user']['specialmisc'] = "";
						$skipvillagedesc=true;
						$op = "";
						httpset("op", "");
						addnav("Continue","runmodule.php?module=worldmapen&op=continue&");
						module_display_events(get_module_setting("randevent"),"runmodule.php?module=worldmapen&op=continue&");
					page_footer();
				}
		}
			$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = '{$session['user']['level']}' ORDER BY rand(".e_rand().") LIMIT 1";
			$result = db_query($sql);
			restore_buff_fields();
			if (db_num_rows($result) == 0) {
				// There is nothing in the database to challenge you,
				// let's give you a doppleganger.
				$badguy = array();
				$badguy['creaturename']="An evil doppleganger of ".$session['user']['name'];
				$badguy['creatureweapon']=$session['user']['weapon'];
				$badguy['creaturelevel']=$session['user']['level'];
				$badguy['creaturegold']= rand(($session['user']['level'] * 15),($session['user']['level'] * 30));
				$badguy['creatureexp'] = round($session['user']['experience']/10, 0);
				$badguy['creaturehealth']=$session['user']['maxhitpoints'];
				$badguy['creatureattack']=$session['user']['attack'];
				$badguy['creaturedefense']=$session['user']['defense'];
			} else {
				$badguy = db_fetch_assoc($result);
				require_once("lib/forestoutcomes.php");
				$badguy = buffbadguy($badguy);
			}
			calculate_buff_fields();
			$badguy['playerstarthp']=$session['user']['hitpoints'];
			$badguy['diddamage']=0;
			$badguy['type'] = 'world';
			$session['user']['badguy']=createstring($badguy);
			$battle = true;
		}else{
			output("`c`nYou think to yourself what a nice day it is.`c`n");
			worldmapen_determinenav();
			if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
			module_display_events(get_module_setting("randevent"),"runmodule.php?module=worldmapen&op=continue&");
	}
		}elseif ($op == "gypsy"){
		if ($buymap == ''){
			output("`5\"`!Ah, yes.  An adventurer.  I could tell by looking into your eyes,`5\" the gypsy says.`n");
			output("\"`!Many people have lost their way while journeying without a guide such as this.");
			output("It will let you see all the world.`5\"`n");
			output("\"`!Yes, yes.  Let's see...  What sort of price should we put on this?");
			output("Hmm.  How about `^%s`! gold?`5\"",$worldmapCostGold);
			addnav(array("Buy World Map `0(`^%s gold`0)", $worldmapCostGold),
					"runmodule.php?module=worldmapen&op=gypsy&buymap=yes");
			addnav("Forget it","village.php");
		} elseif ($buymap == 'yes'){
			if ($session['user']['gold'] < $worldmapCostGold){
				output("`5\"`!What do you take me for?  A blind hag?  Come back when you have the money`5\"");
				addnav("Leave quickly","village.php");
			}else{
				output("`5\"`!Enjoy your new found sight,`5\"  the gypsy says as she walks away to greet some patrons that have just strolled in.");
				$session['user']['gold']-=$worldmapCostGold;
				set_module_pref("worldmapbuy",1);
				require_once("lib/villagenav.php");
				villagenav();
			}
		}
	} elseif ($op=="viewmap"){
		worldmapen_determinenav();
		worldmapen_viewmap(true);
		
	} elseif ($op == "camp"){
		if ($session['user']['loggedin']) {
			$session['user']['loggedin'] = 0;
			$session['user']['restorepage'] = "runmodule.php?module=worldmapen&op=wake";
			saveuser();
		}
		$session = array();
		redirect("index.php");
	} elseif ($op == "wake") {
		output("You yawn and stretch and look around your campsite.`n`n");
		output("Ah, how wonderful it is to sleep in the open air!`n");
		output("The world seems full of possibilities today.`n`n");
		checkday();
		worldmapen_determinenav();
		if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
	} elseif ($op=="combat") {
		// Okay, we've picked a person to fight.
		require_once("lib/pvpsupport.php");
		$name = httpget("name");
		$badguy = setup_target($name);
		$failedattack = false;
		if ($badguy===false) {
			output("`n`nYou survey the area again.`n");
			worldmapen_determinenav();
		} else {
			$battle = true;
			$session['user']['badguy']=createstring($badguy);
			$session['user']['playerfights']--;
		}
	} elseif ($op=="fight" || $op=="run"){
			$battle = true;
			$args = modulehook("count-travels", array('available'=>0,'used'=>0));
			$free = max(0, $args['available'] - $args['used']);
		if ($op == "run" && !$pvp) {
			if (e_rand(1, 5) < 3 && $free) {
				// They managed to get away.
				output("You set off running at a breakneck pace!`n`n");
				output("A short time later, you have managed to avoid your opponent, so you stop to catch your breath.");
				$ttoday = get_module_pref("traveltoday", "cities");
				set_module_pref("traveltoday", $ttoday+1, "cities");
				output("As you look around, you realize that all you really managed was to run in circles.");
				$battle = false;
				worldmapen_determinenav();
			} else {
				output("You try to run, but you don't manage to get away!`n");
				$op = "fight";
				httpset('op', $op);
			}
		} elseif ($op=="run" && $pvp) {
			output("Your pride prevents you from running");
			$op = "fight";
			httpset('op', $op);
		}
	}
	  if ($battle){
		include_once("battle.php");
		if ($victory){
			if ($pvp) {
				require_once("lib/pvpsupport.php");
				$aliveloc = $badguy['location'];
				pvpvictory($badguy, $aliveloc);
				addnews("`4%s`3 defeated `4%s`3 while they were camped in the wilderness.", $session['user']['name'], $badguy['creaturename']);
				$badguy=array();
			} else {
				require_once("lib/forestoutcomes.php");
				forestvictory($enemies, false);
			}
			worldmapen_determinenav();
			if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
		}elseif ($defeat){
			// Reset the players body to the last city they were in
			$session['user']['location'] = get_module_pref('lastCity');
			if ($pvp) {
				require_once("lib/pvpsupport.php");
				require_once("lib/taunt.php");
				$killedloc = $badguy['location'];
				pvpdefeat($badguy, $killedloc, $taunt);
				$taunt = select_taunt_array();
				addnews("`4%s`3 was defeated while attacking `4%s`3 as they were camped in the wilderness.`n%s", $session['user']['name'], $badguy['creaturename'], $taunt);
			} else {
				require_once("lib/forestoutcomes.php");
				forestdefeat($enemies,"in the wild");
			}
			output("`n`n`&You are sure that someone, sooner or later, will stumble over your corpse and return it to %s for you." , $session['user']['location']);
		}else{
			require_once("lib/fightnav.php");
			$allow = true;
			$extra = "";
			if ($pvp) {
				$allow=false;
				$extra="pvp=1&";
			}
			fightnav($allow,$allow,"runmodule.php?module=worldmapen&$extra");
		}
	}
	page_footer();
}
// -----------------------------------------------------------------------
// BEGIN - FUNCTIONS
// -----------------------------------------------------------------------
// -----------------------------------------------------------------------
// BEGIN - worldmapen_defaultcityloc determines the default city locations
//         for all cities in the game!
// -----------------------------------------------------------------------
function worldmapen_defaultcityloc(){
		global $session;
		$i = 0;
		$citylocX = 0;
		$citylocY = 0;
		$citylocations = array();
		$citylocations[][] = "";
		$vloc = array();
		$vname = getsetting("villagename", LOCATION_FIELDS);
		$vloc[$vname]= "village";
		$vloc = modulehook("validlocation", $vloc);
	  foreach($vloc as $loc=>$val) {
			$k = 0;
			while ($k == 0){
				foreach($citylocations as $val1){
					if (($val1[0] == $citylocX) && ($val1[1] == $citylocY)){
						$k = 0;
						
						$citylocX = e_rand(1, get_module_setting("worldmapsizeX"));
						$citylocY = e_rand(1, get_module_setting("worldmapsizeY"));
					}else{
						$k++;
						$citylocations[$i][0] = $citylocX;
					  $citylocations[$i][1] = $citylocY;
						 set_module_setting($loc.'X', $citylocX);
					  set_module_setting($loc.'Y', $citylocY);
				  set_module_setting($loc.'Z', "1");
					 }
				}                                                
			}
			$i++;
		}
}
// -----------------------------------------------------------------------
// END - worldmapen_defaultcityloc determines the default city locations
//         for all cities in the game!
// -----------------------------------------------------------------------
// -----------------------------------------------------------------------
// BEGIN - worldmapen_viewmap allows players to view the world map if they
//         have purchased one from the Gypsy or Item Shop
// -----------------------------------------------------------------------
function worldmapen_viewmap($showloc){
	global $session;
	$op = httpget("op");
	$act = httpget("act");
	$subop = httpget("subop");
	if (get_module_pref("worldmapbuy") == 1 || ($session['user']['superuser'] & SU_EDIT_USERS)){
				$colorUserLoc = get_module_setting("colorUserLoc");
				$vloc = array();
				$vname = getsetting("villagename", LOCATION_FIELDS);
				$vloc[$vname] = "village";
				$vloc = modulehook("validlocation", $vloc);              
				$loc = get_module_pref("worldXYZ");
				list($worldmapX, $worldmapY, $worldmapZ) = explode(",", $loc);
				$cellcolor = get_module_setting("colorPlains");
				$cellvalue = "0";
				$sizeX = get_module_setting("worldmapsizeX");
				$sizeY = get_module_setting("worldmapsizeY");
				$rowspanY = $sizeX+1;
				output("`^`c`bWorld Map`b`c`n");
	// -----------------------------------------------------------------------
	// BEGIN - Display the simple map
	// -----------------------------------------------------------------------  
		if ($op=="viewmap" || $subop=="" || $subop=="regen"){            
		output_notl("`c");
		rawoutput("<table border=0 cellpadding=0 cellspacing=1>");
		rawoutput("<tr>");
		rawoutput("<td valign='middle' rowspan='$rowspanY' width='10'>");
		output_notl("`6`bY`b`0");
		rawoutput("</td>");
			for ($y = $sizeY;$y > 0;$y--){
		  rawoutput("<tr>");
		  rawoutput("<td width='20' align='right'>$y&nbsp;&nbsp;</td>");
				for ($x = 1;$x <= $sizeX;$x++){
					
					switch(get_module_setting("Terrain-$x,$y,1")){
						case "0":
							$cellcolor = get_module_setting("colorPlains");
							$cellvalue = "0";
						break;
						case "1":
							$cellcolor = get_module_setting("colorForest");
							$cellvalue = "1";
						break;
						case "2":
							$cellcolor = get_module_setting("colorRiver");
							$cellvalue = "2";
						break;
						case "3":
							$cellcolor = get_module_setting("colorOcean");
							$cellvalue = "3";
						break;
						case "4":
							$cellcolor = get_module_setting("colorDesert");
							$cellvalue = "4";
						break;
						case "5":
							$cellcolor = get_module_setting("colorSwamp");
							$cellvalue = "5";
						break;
						case "6":
							$cellcolor = get_module_setting("colorMount");
							$cellvalue = "6";
						break;
						case "7":
							$cellcolor = get_module_setting("colorSnow");
							$cellvalue = "7";
						break;						
					}
					
					if ($showloc){
					if ($y == $worldmapY && $x == $worldmapX){
					rawoutput("<td bgcolor='$colorUserLoc' align='center' valign='middle' height='25' width='30'>");
							}else{
								rawoutput("<td bgcolor='$cellcolor' align=center valign=middle height=25 width=30>");
							}
						}else{
							rawoutput("<td bgcolor='$cellcolor' align=center valign=middle height=25 width=30>");
					}
					 foreach($vloc as $loc=>$val) {
					if ($y == get_module_setting($loc."Y") && $x == get_module_setting($loc."X")) {
						output_notl(substr($loc, 0, 3));
						$city = true;
						break;
					} else {
						$city = false;
					}
				}
				if (!$city) {
					rawoutput("<img src='images/trans.gif' height='25' width='30'>");
				}
				rawoutput("</td>");
			}
			rawoutput("</tr>");
			}
			rawoutput("<tr>");
		rawoutput("<td colspan='2'>&nbsp;</td>");
		for ($x = 1;$x <= $sizeX ;$x++){
			rawoutput("<td align=middle><br>$x</td>");
		}
		rawoutput("</tr>");
		rawoutput("<tr><td colspan='2'></td><td align='center' colspan='$sizeX'>");
		output_notl("`6`bX`b`0");
		rawoutput("</td></tr>");
		rawoutput("</table>");
	if (get_module_setting("showcompass") == 1){
		output_notl("`c");
		worldmapen_showcompass();
		}else{
		output_notl("`c");
		}
	}
		// -----------------------------------------------------------------------
		// END - Display the simple map
		// -----------------------------------------------------------------------
		// -----------------------------------------------------------------------
		// BEGIN - Display the advanced map with terrain editor
		// -----------------------------------------------------------------------
		
		if ($subop=="terrain"){
			rawoutput("<form method='post' action='runmodule.php?module=worldmapen&op=edit&subop=terrain&act=save'>");
			output_notl("`c");
			rawoutput("<table border=0 cellpadding=0 cellspacing=1>");
			rawoutput("<tr>");
			rawoutput("<td valign='middle' rowspan='$rowspanY' width='10'>");
			output_notl("`6`bY`b`0");
			rawoutput("</td>");
			for ($y = $sizeY;$y > 0;$y--){
			  rawoutput("<tr>");
			  rawoutput("<td width='20' align='right'>$y&nbsp;&nbsp;</td>");
				for ($x = 1;$x <= $sizeX;$x++){
						
					switch(get_module_setting("Terrain-$x,$y,1")){
						case "0":
							$cellcolor = get_module_setting("colorPlains");
							$cellvalue = "0";
						break;
						case "1":
							$cellcolor = get_module_setting("colorForest");
							$cellvalue = "1";
						break;
						case "2":
							$cellcolor = get_module_setting("colorRiver");
							$cellvalue = "2";
						break;
						case "3":
							$cellcolor = get_module_setting("colorOcean");
							$cellvalue = "3";
						break;
						case "4":
							$cellcolor = get_module_setting("colorDesert");
							$cellvalue = "4";
						break;
						case "5":
							$cellcolor = get_module_setting("colorSwamp");
							$cellvalue = "5";
						break;
						case "6":
							$cellcolor = get_module_setting("colorMount");
							$cellvalue = "6";
						break;
						case "7":
							$cellcolor = get_module_setting("colorSnow");
							$cellvalue = "6";
						break;                      
					}    
					// We do y x y for the id to address issues when x = 11 y = 5 and x = 1 and y = 15    
					rawoutput("<td id=\"".$y."".$x."".$y."\" onclick=\"changeColor(this.id);\" bgcolor=".$cellcolor." align=center valign=middle height=25 width=30><input type=\"hidden\" id=\"".$y."".$x."".$y."b\" name=\"".$x.".".$y."\" value=\"$cellvalue\"/>");
					foreach($vloc as $loc=>$val) {
					  	if ($y == get_module_setting($loc."Y") && $x == get_module_setting($loc."X")) {
							output_notl(substr($loc, 0, 3));
							$city = true;
							break;
					  	} else {
							$city = false;
					  	}
					}
				  	if (!$city) {
						rawoutput("<img src='images/trans.gif' height='25' width='30'>");
				  	}
				}
				rawoutput("</tr>");
			}
				rawoutput("<td colspan='2'></td>");
				for ($x = 1;$x <= get_module_setting("worldmapsizeX");$x++){
					rawoutput("<td align=middle>$x</td>");
				}
			rawoutput("</tr><tr><td colspan=2></td><td align=center colspan=".get_module_setting("worldmapsizeX").">");
			output_notl("`6`bX`b");
			rawoutput("</td></tr>");
			rawoutput("</table>");
			output_notl("`c");
			rawoutput("<center><input type=submit value=\"".translate_inline("Save Terrain")."\">");
			rawoutput("</form></center>");
			addnav("","runmodule.php?module=worldmapen&op=edit&subop=terrain&act=save");
		}
		// -----------------------------------------------------------------------
		// END - Display the advanced map with terrain editor
		// -----------------------------------------------------------------------
		worldmapen_viewmapkey(true,true);
			rawoutput("<hr>");
			rawoutput("<table cellpadding=2 cellspacing=5 border=0><tr>");
			rawoutput("<td bgcolor=".get_module_setting("colorPlains")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Plains")." - ".get_module_setting("moveCostPlains")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorForest")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Dense Forest")." - ".get_module_setting("moveCostForest")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorRiver")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("River")." - ".get_module_setting("moveCostRiver")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorOcean")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Ocean")." - ".get_module_setting("moveCostOcean")."</td>");
			rawoutput("</tr><tr>");
			rawoutput("<td bgcolor=".get_module_setting("colorDesert")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Desert")." - ".get_module_setting("moveCostDesert")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorMount")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Mountains")." - ".get_module_setting("moveCostMount")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorSwamp")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Swamp")." - ".get_module_setting("moveCostSwamp")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorSnow")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Snow")." - ".get_module_setting("moveCostSnow")."</td>");
			rawoutput("<td bgcolor=111111 height=5 width=10><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Map Edge")."</td>");
			rawoutput("</tr></table>");
	}
}
// -----------------------------------------------------------------------
// END - worldmapen_viewmap allows players to view the world map if they
//         have purchased one from the Gypsy or Item Shop
// -----------------------------------------------------------------------
function worldmapen_viewsmallmap(){
	$colorUserLoc = get_module_setting("colorUserLoc");
	$vloc = array();
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$vloc[$vname] = "village";
	$vloc = modulehook("validlocation", $vloc);
	$loc = get_module_pref("worldXYZ");
	list($worldmapX, $worldmapY, $worldmapZ) = explode(",", $loc);
	$sizeX = get_module_setting("worldmapsizeX");
	$sizeY = get_module_setting("worldmapsizeY");
	$viewRadius = get_module_setting("viewRadius");
	$smallmapsize = (2 * $viewRadius) + 1;
	$rowspanY = $sizeX + 1;
	$smallmapY = $worldmapY + floor($smallmapsize / 2);
	$i=0;
		output_notl("`c");
		rawoutput("<table cellpadding=0 cellspacing=5 width=100% border=0>");
			rawoutput("<tr><td height=1 bgcolor=000000></td><tr>");
			rawoutput("</table>");
		rawoutput("<table border=0 cellpadding=0 cellspacing=1>");
		rawoutput("<tr>");
			for ($y = $smallmapsize;$y > 0;$y--){
				$smallmapX = ($worldmapX - floor($smallmapsize / 2));
		  rawoutput("<tr>");
				for ($x = 1;$x <= $smallmapsize;$x++){
					
					switch(get_module_setting("Terrain-$smallmapX,$smallmapY,1")){
						case "0":
							$cellcolor = get_module_setting("colorPlains");
						break;
						case "1":
							$cellcolor = get_module_setting("colorForest");
						break;
						case "2":
							$cellcolor = get_module_setting("colorRiver");
						break;
						case "3":
							$cellcolor = get_module_setting("colorOcean");
						break;
						case "4":
							$cellcolor = get_module_setting("colorDesert");
						break;
						case "5":
							$cellcolor = get_module_setting("colorSwamp");
						break;
						case "6":
							$cellcolor = get_module_setting("colorMount");
						break;
						case "7":
							$cellcolor = get_module_setting("colorSnow");
						break;						
						default:
							$cellcolor = "000000";
						break;
					}
					if ($i == floor($smallmapsize * $smallmapsize / 2)) {
						rawoutput("<td bgcolor='$colorUserLoc' align=center valign=middle height=25 width=30>");
					} else if ($x < $sizeX){
						rawoutput("<td bgcolor='$cellcolor' align=center valign=middle height=25 width=30>");
					}
					foreach($vloc as $loc=>$val) {
						if ($smallmapY == get_module_setting($loc."Y") && $smallmapX == get_module_setting($loc."X")) {
							output_notl("%s", substr($loc, 0, 3));
							$city = true;
							break;
						}else{
							$city = false;
						}
					}
					if (!$city) {
						rawoutput("<img src='images/trans.gif' height='25' width='30'>");
					}
			  rawoutput("</td>");
			  $smallmapX++;
			  $i++;
			}
			rawoutput("</tr>");
			$smallmapY--;
			}
		rawoutput("</table>");
	if (get_module_setting("showcompass") == 1){
		output_notl("`c");
		worldmapen_showcompass();
	}else{
	output_notl("`c");
	}
	if ($showloc){
		worldmapen_viewmapkey(true,false);
	}else{
		worldmapen_viewmapkey(false,false);
		} 
	}
function worldmapen_showcompass() {
		global $nlink, $elink, $wlink, $slink, $nelink, $nwlink, $selink, $swlink;
			rawoutput("<table cellpadding=0 cellspacing=5 width=100% border=0>");
			rawoutput("<tr><td height=1 bgcolor=000000></td><tr>");
			rawoutput("</table>"); 
			rawoutput('<center><IMG SRC="images/compass.gif" WIDTH=198 HEIGHT=234 BORDER=0 ALT="" USEMAP="#compass_Map"></center>');
			rawoutput('<MAP NAME="compass_Map">');
			rawoutput('<AREA SHAPE="poly" ALT="NorthWest" COORDS="67,109, 14,53, 31,39, 84,95" HREF="' . $nwlink . '">');
			rawoutput('<AREA SHAPE="poly" ALT="West" COORDS="67,138, 0,138, 0,116, 66,115" HREF="' . $wlink . '">');
			rawoutput('<AREA SHAPE="poly" ALT="SouthWest" COORDS="70,139, 85,156, 30,210, 18,201" HREF="' . $swlink . '">');
			rawoutput('<AREA SHAPE="poly" ALT="South" COORDS="109,157, 107,234, 89,234, 87,157" HREF="' . $slink . '">');
			rawoutput('<AREA SHAPE="poly" ALT="SouthEast" COORDS="125,144, 180,201, 167,210, 111,155" HREF="' . $selink . '">');
			rawoutput('<AREA SHAPE="poly" ALT="East" COORDS="130,115, 198,118, 198,136, 129,140" HREF="' . $elink . '">');
			rawoutput('<AREA SHAPE="poly" ALT="NorthEast" COORDS="179,51, 167,39, 111,99, 126,112" HREF="' . $nelink . '">');
			rawoutput('<AREA SHAPE="poly" ALT="North" COORDS="110,0, 86,0, 87,95, 109,95" HREF="' . $nlink . '">');
			rawoutput('</MAP>');
}
// -----------------------------------------------------------------------
// BEGIN - World map key
// -----------------------------------------------------------------------
function worldmapen_viewmapkey($showloc,$small){
	$vloc = array();
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$vloc[$vname] = "village";
	$vloc = modulehook("validlocation", $vloc);
	$cities = translate_inline("Cities");
	$terrains = translate_inline("Terrains");
	$mapkey = translate_inline("MAP KEY");
	$colorUserLoc = get_module_setting("colorUserLoc");
	output("`n`n",true);
	rawoutput("<table cellpadding=0 cellspacing=5 width=100% border=0>");
	rawoutput("<tr><td height=1 bgcolor=000000></td><tr>");
	rawoutput("<tr><td>");
	output_notl("`b`6$mapkey`0`b");
	rawoutput("</td></tr>");
	rawoutput("</table>");
	$loc = get_module_pref("worldXYZ");
	list($worldmapX, $worldmapY, $worldmapZ) = explode(",", $loc);
	  switch(get_module_setting("Terrain-$worldmapX,$worldmapY,$worldmapZ")){
		  case "0":
			  $currentTerrain = "Plains";
			  $terrainColor = get_module_setting("colorPlains");
		  break;
		  case "1":
			  $currentTerrain = "Dense Forest";
			  $terrainColor = get_module_setting("colorForest");
		  break;
		  case "2":
			  $currentTerrain = "River";
			  $terrainColor = get_module_setting("colorRiver");
		  break;
		  case "3":
				 $currentTerrain = "Ocean";
			  $terrainColor = get_module_setting("colorOcean");
		  break;
		  case "4":
			  $currentTerrain = "Desert";
			  $terrainColor = get_module_setting("colorDesert");
		  break;
		  case "5":
			   $currentTerrain = "Swamp";
			  $terrainColor = get_module_setting("colorSwamp");
		  break;
		  case "6":
			   $currentTerrain = "Mountains";
			  $terrainColor = get_module_setting("colorMount");
		  break;
		  case "7":
			   $currentTerrain = "Snow";
			  $terrainColor = get_module_setting("colorSnow");
		  break;		  
	  }
	if ($showloc){
		rawoutput("<table cellpadding=0 cellspacing=5><tr>");
		rawoutput("<td bgcolor=$colorUserLoc height=5 width=10><img src='images/trans.gif' height=5 width=10></td><td>Current Location</td>");
		rawoutput("</tr><tr>");
	if (get_module_setting("enableTerrains")==1){
		rawoutput("<td bgcolor=$terrainColor height=5 width=10><img src='images/trans.gif' height=5 width=10></td><td>");
		output("Current Terrain: %s", $currentTerrain);
		rawoutput("</td></tr><tr>");
	}
		rawoutput("</table>");
	}
	if (get_module_setting("showcities") == 1){
		output("`n");
		rawoutput("<table cellpadding=0 cellspacing=5 border=0><tr><td>");    
		output_notl("`b$cities`b");
		rawoutput("</td></tr>");
		foreach($vloc as $loc=>$val) {
			rawoutput("<tr><td>");
			rawoutput(substr($loc,0,3),true);
			rawoutput("</td><td>= $loc</td></tr>");
		}
		rawoutput("</table>");
	}	
	if($small){
		rawoutput("<table cellpadding=0 cellspacing=5><tr>");
		rawoutput("<td bgcolor=111111 height=5 width=10><img src='images/trans.gif' height=5 width=10></td><td>Map Edge</td>");
		rawoutput("</tr><tr>");
		rawoutput("</table>");
	}else{    
		if (get_module_setting("enableTerrains") ==1){
			output("`n");
			rawoutput("<table cellpadding=0 cellspacing=5 border=0 width=100%><img src='images/trans.gif' height=5 width=10></td><td>");
			output("`0Current Terrain:  `b`^%s `b`^`n", $currentTerrain);
			rawoutput("<hr></td><tr><td>");
			output_notl("`&`b$terrains`b`&");
			rawoutput("</td></tr><tr><td>");
			output("(Terrain Color, Terrain Type, Terrain Movement Cost)");
			rawoutput("</td></tr><tr><br>");
			rawoutput("<table cellpadding=2 cellspacing=5 border=0><tr>");
			rawoutput("<td bgcolor=".get_module_setting("colorPlains")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Plains")." - ".get_module_setting("moveCostPlains")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorForest")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Dense Forest")." - ".get_module_setting("moveCostForest")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorRiver")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("River")." - ".get_module_setting("moveCostRiver")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorOcean")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Ocean")." - ".get_module_setting("moveCostOcean")."</td>");
			rawoutput("</tr><tr>");
			rawoutput("<td bgcolor=".get_module_setting("colorDesert")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Desert")." - ".get_module_setting("moveCostDesert")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorMount")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Mountains")." - ".get_module_setting("moveCostMount")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorSwamp")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Swamp")." - ".get_module_setting("moveCostSwamp")."</td>");
			rawoutput("<td bgcolor=".get_module_setting("colorSnow")."><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Snow")." - ".get_module_setting("moveCostSnow")."</td>");
			rawoutput("<td bgcolor=111111 height=5 width=10><img src='images/trans.gif' height=5 width=10></td><td>".translate_inline("Map Edge")."</td>");
			rawoutput("</tr></table>");
		}
	}
}
// -----------------------------------------------------------------------
// END - World map key
// -----------------------------------------------------------------------
// -----------------------------------------------------------------------
// BEGIN - worldmapen_determinenav determines in which direction a player
//         may move in the world.  North, East, South, West
// -----------------------------------------------------------------------
function worldmapen_determinenav()
{
	global $session, $nlink, $elink, $wlink, $slink, $nelink, $nwlink, $selink, $swlink;
	$minX = 1;
	$minY = 1;    
	$maxX = get_module_setting("worldmapsizeX");
	$maxY = get_module_setting("worldmapsizeY");
	if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO){
		addnav("X?`bSuperuser Grotto`b","superuser.php");
	}
	$campingAllowed = 1;
	$loc = get_module_pref('worldXYZ');    
	list($x, $y, $z) = explode(",", $loc);
	$vloc = array();
	$vname = getsetting("villagename", LOCATION_FIELDS);
	$vloc[$vname] = "village";
	$vloc = modulehook("validlocation", $vloc);
	foreach($vloc as $loc=>$val) {
		$cx = get_module_setting($loc.'X');
		$cy = get_module_setting($loc.'Y');
		$cz = get_module_setting($loc.'Z');
		if ($x == $cx && $y == $cy && $z == $cz) {
			$session['user']['location']=$loc;
			set_module_pref("lastCity", "");
			addnav(array("V?Enter %s", $loc), "village.php");
			addnav("Enter Forest","forest.php");
			$campingAllowed = 0;
		}
	}
	$args = modulehook("count-travels", array('available'=>0, 'used'=>0));
	$free = max(0, $args['available'] - $args['used']);
	if ($free != 0 or $free < 0) {
		addnav("You can go");
				$plusX=$x+1;
				$plusY=$y+1;
				$minusX=$x-1;
				$minusY=$y-1;
				// Might be a better way of getting the terrain movement cost for the adjacent squares
				$NterrainCost = worldmapen_terrain_cost(get_module_setting("Terrain-$x,$plusY,$z"));
				$NEterrainCost = worldmapen_terrain_cost(get_module_setting("Terrain-$plusX,$plusY,$z"));
				$NWterrainCost = worldmapen_terrain_cost(get_module_setting("Terrain-$minusX,$plusY,$z"));
				$EterrainCost = worldmapen_terrain_cost(get_module_setting("Terrain-$plusX,$y,$z"));
				$SterrainCost = worldmapen_terrain_cost(get_module_setting("Terrain-$x,$minusY,$z"));
				$SEterrainCost = worldmapen_terrain_cost(get_module_setting("Terrain-$plusX,$minusY,$z"));
				$SWterrainCost = worldmapen_terrain_cost(get_module_setting("Terrain-$minusX,$minusY,$z"));
				$WterrainCost = worldmapen_terrain_cost(get_module_setting("Terrain-$minusX,$y,$z"));
		if ($y + 1 <= $maxY && $NterrainCost <=  $free){
			$nlink = "runmodule.php?module=worldmapen&op=move&dir=north";
			addnav("", $nlink);
			addnav("8?North","runmodule.php?module=worldmapen&op=move&dir=north");
		}elseif ($NterrainCost >  $free){
			output("Can't move North`n");
			$nlink = "#";
		}else{
				$nlink = "#";
				output("`c`n`!%s`n`c",get_module_setting("nBoundary"));
		  }
		if ($x + 1 <= $maxX && $EterrainCost <=  $free){
			$elink = "runmodule.php?module=worldmapen&op=move&dir=east";
			addnav("", $elink);
			addnav("6?East","runmodule.php?module=worldmapen&op=move&dir=east");
		}elseif ($EterrainCost >  $free){
			output("Can't move East`n");
			$elink = "#";
		}else{
			$elink = "#";
			output("`c`n`!%s`c`n",get_module_setting("eBoundary"));
		}
		if ($y - 1 >= $minY && $SterrainCost <=  $free){
			$slink = "runmodule.php?module=worldmapen&op=move&dir=south";
			addnav("", $slink);
			addnav("2?South","runmodule.php?module=worldmapen&op=move&dir=south");
		}elseif ($SterrainCost >  $free){
			output("Can't move South`n");
			$slink = "#";
		}else{
			$slink = "#";
			output("`c`n`!%s`c`n",get_module_setting("sBoundary"));
		}
		if ($x - 1 >= $minX && $WterrainCost <=  $free){
			$wlink = "runmodule.php?module=worldmapen&op=move&dir=west";
			addnav("", $wlink);
			addnav("4?West","runmodule.php?module=worldmapen&op=move&dir=west");
		}elseif ($WterrainCost >  $free){
			output("Can't move West`n");
			$wlink = "#";
		}else{
			$wlink = "#";
			output("`n`c`!%s`n`c",get_module_setting("wBoundary"));
		}
		if (get_module_setting("compasspoints") == "1"){
				if ($y + 1 <= $maxY && $x + 1 <= $maxX && $NEterrainCost <=  $free){
					$nelink = "runmodule.php?module=worldmapen&op=move&dir=northeast";
					addnav("", $nelink);
					addnav("9?NorthEast","runmodule.php?module=worldmapen&op=move&dir=northeast");
				}elseif ($NEterrainCost > $free){
						output("Can't move North East`n");
						$nelink = "#";
				} else {
					$nelink = "#";
				}
				if ($y + 1 <= $maxY && $x - 1 >= $minX && $NWterrainCost <= $free){
						$nwlink = "runmodule.php?module=worldmapen&op=move&dir=northwest";
						addnav("", $nwlink);
						addnav("7?NorthWest","runmodule.php?module=worldmapen&op=move&dir=northwest");
				}elseif ($NWterrainCost > $free){
						output("Can't move North West`n");
						$nwlink = "#";
				} else {
					$nwlink = "#";
				}
				if ($y - 1 >= $minY && $x + 1 <= $maxX && $SEterrainCost <=  $free){
							$selink = "runmodule.php?module=worldmapen&op=move&dir=southeast";
							addnav("", $selink);
							addnav("3?SouthEast","runmodule.php?module=worldmapen&op=move&dir=southeast");
				}elseif ($SEterrainCost > $free){
						output("Can't move South East`n");
						$selink = "#";
				} else {
					$selink = "#";
				}
				if ($y - 1 >= $minY && $x - 1 >= $minX && $SWterrainCost <=  $free){
							$swlink = "runmodule.php?module=worldmapen&op=move&dir=southwest";
							addnav("", $swlink);
							addnav("1?SouthWest","runmodule.php?module=worldmapen&op=move&dir=southwest");
				}elseif ($SWterrainCost > $free){
						output("Can't move South West`n");
						$swlink = "#";
				} else {
						$swlink = "#";
				}
		  }
	}
	if ($session['user']['superuser'] & SU_EDIT_USERS){
	   addnav("Superuser");
	   foreach($vloc as $loc=>$val) {
	   if ($loc == $session['user']['location']) continue;
	   addnav(array("Go to %s", $loc), "runmodule.php?module=worldmapen&op=destination&cname=".htmlentities($loc));
	   }
	}
	if ($session['user']['superuser'] & SU_EDIT_USERS) {
		if (get_module_setting("manualmove") == 1){
		addnav("--");	
			addnav("Superuser");
			if ($y + 1 <= $maxY){
				addnav("Safe North",
						"runmodule.php?module=worldmapen&op=move&dir=north&su=1");
			}
			if ($x + 1 <= $maxX){
				addnav("Safe East",
						"runmodule.php?module=worldmapen&op=move&dir=east&su=1");
			}
			if ($y - 1 >= $minY){
				addnav("Safe South",
						"runmodule.php?module=worldmapen&op=move&dir=south&su=1");
			}
			if ($x - 1 >= $minX){
				addnav("Safe West",
						"runmodule.php?module=worldmapen&op=move&dir=west&su=1");
			}
		}
	}
	if ($session['user']['superuser'] & SU_INFINITE_DAYS) {
		addnav("Superuser");
		addnav("/?New Day", "newday.php");
	}
	if (get_module_pref("worldmapbuy") == 1 || ($session['user']['superuser'] & SU_EDIT_USERS)){
		addnav("Map");
		addnav("M?World Map","runmodule.php?module=worldmapen&op=viewmap");        
	}  
	if ($campingAllowed){
		worldmapen_camp_list();
	}
	modulehook("worldnav");
}
// -----------------------------------------------------------------------
// END - worldmapen_determinenav determines in which direction a player
//         may move in the world.  North, East, South, West
// -----------------------------------------------------------------------
// -----------------------------------------------------------------------
// BEGIN - World Map camping routine
// -----------------------------------------------------------------------
function worldmapen_camp_list(){
	global $session, $pvptime, $pvptimeout;
	addnav("Quit");
	addnav("Set up camp","runmodule.php?module=worldmapen&op=camp");
	if (getsetting("pvp",1) == 0) return;
	$accountBD = db_prefix("accounts");
	$userprefsBD = db_prefix("module_userprefs");
	$clansBD = db_prefix("clans");
	$loc = get_module_pref("worldXYZ");
	$lev1 = $session['user']['level']-1;
	$lev2 = $session['user']['level']+2;
	$days = getsetting("pvpimmunity", 5);
	$exp = getsetting("pvpminexp", 1500);
	$last = date("Y-m-d H:i:s", strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	$id = $session['user']['acctid'];
	$location = addslashes($session['user']['location']);
	$sql = "SELECT $accountBD.name, $accountBD.alive, $accountBD.sex, $accountBD.level, $accountBD.laston, $accountBD.loggedin, $accountBD.login, $accountBD.pvpflag, $userprefsBD.value AS location, $clansBD.clanshort, $accountBD.clanrank 
		FROM $accountBD  
			LEFT JOIN $clansBD ON $clansBD.clanid = $accountBD.clanid
			LEFT JOIN $userprefsBD ON $userprefsBD.userid = $accountBD.acctid
		WHERE
			$userprefsBD.value='$loc' 
			AND $accountBD.locked=0
			AND $accountBD.slaydragon=0
			AND ($accountBD.age>$days OR $accountBD.dragonkills>0 OR $accountBD.pk>0 OR $accountBD.experience>$exp) 
			AND ($accountBD.level>=$lev1 AND $accountBD.level<=$lev2) 
			AND $accountBD.alive=1
			AND ($accountBD.laston<'$last' OR $accountBD.loggedin=0) 
			AND $accountBD.acctid<>$id
			AND $accountBD.location='$location' 
			ORDER BY $accountBD.level DESC, $accountBD.experience DESC, $accountBD.dragonkills DESC";
	//PvP Display   
	require_once("lib/pvplist.php");
	rawoutput('<br><center>');
	pvplist($loc,"runmodule.php?module=worldmapen", "&op=combat&pvp=1",$sql);
	rawoutput('</center>');
}
// -----------------------------------------------------------------------
// END - World Map camping routine
// -----------------------------------------------------------------------
function worldmapen_terrain_cost($tmp){    
	switch($tmp){
		case "0":
		  $terraincost=get_module_setting("moveCostPlains");
			break;
			case "1":
		  $terraincost=get_module_setting("moveCostForest");
			break;
			case "2":
		  $terraincost=get_module_setting("moveCostRiver");
			break;
			case "3":
		  $terraincost=get_module_setting("moveCostOcean");
			break;
			case "4":
		  $terraincost=get_module_setting("moveCostDesert");
			break;
			case "5":
		  $terraincost=get_module_setting("moveCostSwamp");
			break;
			case "6":
		  $terraincost=get_module_setting("moveCostMount");
			break;
			case "7":
		  $terraincost=get_module_setting("moveCostSnow");
			break;			
	}
	return $terraincost;
}
function worldmapen_encounter($tmp) {
	switch ($tmp) {
		case "0":
		  $encounter=get_module_setting("encounterPlains");
			break;
		case "1":
		  $encounter=get_module_setting("encounterForest");
			break;
		case "2":
		  $encounter=get_module_setting("encounterRiver");
			break;
		case "3":
		  $encounter=get_module_setting("encounterOcean");
			break;
		case "4":
		  $encounter=get_module_setting("encounterDesert");
			break;
		case "5":
		  $encounter=get_module_setting("encounterSwamp");
			break;
		case "6":
		  $encounter=get_module_setting("encounterMount");
			break;
		case "7":
		  $encounter=get_module_setting("encounterSnow");
			break;			
	}
return $encounter;
}
// -----------------
// END - FUNCTIONS
// -----------------
?>
