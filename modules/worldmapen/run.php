<?php
/***********************************************
 World Map
 Originally by: Aes
 Updates & Maintenance by: Kevin Hatfield - Arune (khatfield@ecsportal.com)
 Updates & Maintenance by: Roland Lichti - klenkes (klenkes@paladins-inn.de)
 http://www.dragonprime.net
 Updated: Feb 23, 2008
 ************************************************/

require_once('modules/worldmapen/lib.php');

function worldmapen_run_real(){
	global $session, $badguy, $pvptimeout, $options, $outdoors, $shady;
	$outdoors = true;
	
	$op = httpget("op");
	$battle = false;
	
	if( $op == 'move'
			&& rawurldecode( httpget( 'oloc' ) ) != get_module_pref( 'worldXYZ' ) )
	{debug(get_module_pref( 'worldXYZ' ));
		$op = 'continue';
		httpset( 'op', $op );
	}
	
//	debug("Worldmap running op={$op} ...");
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
		output("`c`4`bYou've Arrived in %s.`b`0`c`n", $cname);
		output("`cYou have reached the outer gates of the city.`c");
	}
	if (!get_module_setting("worldmapenInstalled")) {
		page_header("A rip in the fabric of space and time");
		require_once("lib/villagenav.php");
		villagenav();
		output("`^The admins of this game haven't yet finished installing the worldmapen module.");
		output("You should send them a petition and tell them that they forgot to generate the initial locations of the cities.");
		output("Until then, you are kind of stuck here, so I hope you like where you are.`n`n");
		output("After all, remember:`nWherever you go, there you are.`0");
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
			module_display_events(get_module_setting("randevent"),"runmodule.php?module=worldmapen&op=continue");
			page_footer();
		}
	}
	page_header("Journey");
	//is the player looking at chat?
	if (httpget('comscroll') || httpget('comscroll')===0 || httpget('comment') || httpget('refresh')){
		$chatoverride = 1;
		require_once("lib/commentary.php");
		addcommentary();
		$loc = get_module_pref("worldXYZ","worldmapen");
		viewcommentary("mapchat-".$loc,"Chat with others who walk this path...",25);
	}
	if ($op == "beginjourney"){
		$loc = $session['user']['location'];
		$x = get_module_setting($loc."X");
		$y = get_module_setting($loc."Y");
		$z = get_module_setting($loc."Z");
		$xyz = $x.",".$y.",".$z;
		set_module_pref("worldXYZ", $xyz);
		output("`b`&The gates of %s`& stand closed behind you.`0`b`n`n",
		$session['user']['location']);
		$num = e_rand(1, 5);
		$msg = translate_inline(get_module_setting("leaveGates$num"));
		if ($msg) output_notl("`c`n`^$msg`0`n`c`n");
		worldmapen_determinenav();
		if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
		if (!$chatoverride){
			require_once("lib/commentary.php");
			addcommentary();
			$loc = get_module_pref("worldXYZ","worldmapen");
			viewcommentary("mapchat-".$loc,"Chat with others who walk this path...",25);
		}
		worldmapen_viewmapkey(true, false);
		module_display_events(get_module_setting("randevent"),"runmodule.php?module=worldmapen&op=continue");
		$loc = get_module_pref('worldXYZ');
		list($x, $y, $z) = explode(",", $loc);
		$t = worldmapen_getTerrain($x, $y, $z);
		//debug($t);
		if ($t['type']=="Forest"){
			$shady = true;
		}
	}elseif ($op == "continue") {
		checkday();
		worldmapen_determinenav();
		if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
		if (!$chatoverride){
			require_once("lib/commentary.php");
			addcommentary();
			$loc = get_module_pref("worldXYZ","worldmapen");
			viewcommentary("mapchat-".$loc,"Chat with others who walk this path...",25);
		}
		worldmapen_viewmapkey(true, false);
		$loc = get_module_pref('worldXYZ');
		list($x, $y, $z) = explode(",", $loc);
		$t = worldmapen_getTerrain($x, $y, $z);
		//debug($t);
		if ($t['type']=="Forest"){
			$shady = true;
		}
	//Turns Trading bit, added by CavemanJoe
	}elseif ($op == "tradeturn") {
		checkday();
		$pointstrade = get_module_setting("turntravel");
		output("You can trade one Turn for %s Travel Points.  Do you want to do this now?",$pointstrade);
		addnav("Yes, use a turn","runmodule.php?module=worldmapen&op=tradeturnconfirm");
		addnav("No, cancel and return to the map","runmodule.php?module=worldmapen&op=continue");
	}elseif ($op == "tradeturnconfirm") {
		$pointstrade = get_module_setting("turntravel");
		output("By conserving energy that you would have otherwise used for fighting creatures, you have gained %s Travel Points.",$pointstrade);
		$session['user']['turns']--;
		$ttoday = get_module_pref("traveltoday", "cities");
		set_module_pref("traveltoday", $ttoday-$pointstrade, "cities");
		addnav("Continue","runmodule.php?module=worldmapen&op=continue");
	}elseif ($op == "move" && !$chatoverride) {
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
		$terraincost = worldmapen_terrain_cost($x, $y, $z);
		$encounterbase = worldmapen_encounter($x, $y, $z);
		$encounterchance = get_module_pref("encounterchance");
		$encounter = ($encounterbase * $encounterchance) / 100;
		debug($encounterbase." * ".$encounterchance." / 100 = ".$encounter);
		$ttoday = get_module_pref("traveltoday", "cities");
		set_module_pref("traveltoday", $ttoday+$terraincost, "cities");
		worldmapen_terrain_takestamina($x, $y, $z);
		$xyz = $x.",".$y.",".$z;
		set_module_pref("worldXYZ", $xyz);
		// $randchance = get_module_setting("randchance");
		// if (e_rand(0,100) < $randchance){
			// $eventravel = "travel";
			// set_module_setting("randevent", $eventravel);
		// }else{
			// $eventravel = "forest";
			// set_module_setting("randevent", $eventravel);
		// }
		//Extra Gubbins pertaining to trading Turns for Travel, added by Caveman Joe
		$useturns = get_module_setting("useturns");
		$allowzeroturns = get_module_setting("allowzeroturns");
		$playerturns = $session['user']['turns'];
		$proceed = 1;
		//the Proceed value is used when the player has hit a monster, to make sure it's okay to actually run the event/monster.
		if ($playerturns == 0 && $allowzeroturns == 0) {
			$proceed = 0;
		}
		
		if (e_rand(0, 100) < $encounter && $su!= '1' && $proceed == 1 && !$chatoverride) {
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
					module_display_events(get_module_setting("randevent"),"runmodule.php?module=worldmapen&op=continue");
					page_footer();
				}
			}
			//Check if we're removing a turn when the player encounters a monster, and if so, do it
			if ($useturns==1){
				$session['user']['turns']--;
			}
			//Fix to only search for Forest type creatures, added by CavemanJoe
			$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = '{$session['user']['level']}' AND forest = 1 ORDER BY rand(".e_rand().") LIMIT 1";
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
			//debug("Worldmap run.php is debugging badguy");
			//debug($badguy);
			$session['user']['badguy']=createstring($badguy);
			$battle = true;			
		}else{
			// $args = modulehook("count-travels", array('available'=>0, 'used'=>0));
			// $free = max(0, $args['available'] - $args['used']);
			// if (get_module_setting("usestamina")==1){
				// output("`c`nYou think to yourself what a nice day it is.`c`n");
			// } else {
				// output("`c`nYou think to yourself what a nice day it is.`nYou have %s Travel Points remaining.%s`c`n",$free);
			// }
			$free = 100;
			worldmapen_determinenav();
			if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
			if (!$chatoverride){
				require_once("lib/commentary.php");
				addcommentary();
				$loc = get_module_pref("worldXYZ","worldmapen");
				viewcommentary("mapchat-".$loc,"Chat with others who walk this path...",25);
			}
			worldmapen_viewmapkey(true, false);
			module_display_events(get_module_setting("randevent"),"runmodule.php?module=worldmapen&op=continue");
		}
		$loc = get_module_pref('worldXYZ');
		list($x, $y, $z) = explode(",", $loc);
		$t = worldmapen_getTerrain($x, $y, $z);
		//debug($t);
		if ($t['type']=="Forest"){
			$shady = true;
		}
	}elseif ($op == "gypsy"){
		$outdoors = false;
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
				output("`5\"`!Enjoy your newfound sight,`5\"  the gypsy says as she walks away to greet some patrons that have just strolled in.");
				$session['user']['gold']-=$worldmapCostGold;
				set_module_pref("worldmapbuy",1);
				require_once("lib/villagenav.php");
				villagenav();
			}
		}
	} elseif ($op=="viewmap"){
		worldmapen_determinenav();
		worldmapen_viewmap(true);
		if (is_module_active("medals")){
			require_once "modules/medals.php";
			medals_award_medal("boughtmap","Bearer of the Map","This player purchased the World Map from the Comms Tent!","medal_islandmap.png");
		}

	} elseif ($op == "camp"){
		if ($session['user']['loggedin']) {
			$session['user']['loggedin'] = 0;
			$session['user']['restorepage'] = "runmodule.php?module=worldmapen&op=wake";
			saveuser();
			invalidatedatacache("charlisthomepage");
			invalidatedatacache("list.php-warsonline");
		}
		$session = array();
		redirect("index.php","Redirected to Index from World Map");
	} elseif ($op == "wake") {
		if ($session['user']['hitpoints']>0){ // runmodule.php calls do_forced_nav,
			$session['user']['alive']=true; // and that resets ['alive'], so
		}else{                            // this is from common.php to make sure 
			$session['user']['alive']=false;// the player is not half-dead after log-in.
		}
		output("You yawn and stretch and look around your campsite.`n`n");
		output("Ah, how wonderful it is to sleep in the open air!`n");
		output("The world seems full of possibilities today.`n`n");
		checkday();
		worldmapen_determinenav();
		if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
		if (!$chatoverride){
			require_once("lib/commentary.php");
			addcommentary();
			$loc = get_module_pref("worldXYZ","worldmapen");
			viewcommentary("mapchat-".$loc,"Chat with others who walk this path...",25);
		}
		worldmapen_viewmapkey(true, false);
		$loc = get_module_pref('worldXYZ');
		list($x, $y, $z) = explode(",", $loc);
		$t = worldmapen_getTerrain($x, $y, $z);
		//debug($t);
		if ($t['type']=="Forest"){
			$shady = true;
		}
	} elseif ($op=="combat") {
		// Okay, we've picked a person to fight.
		require_once("lib/pvpsupport.php");
		$name = httpget("name");
		$badguy = setup_target($name);
		$failedattack = false;
		if ($badguy===false) {
			output("`0`n`nYou survey the area again.`n");
			worldmapen_determinenav();
		} else {
			$battle = true;
			$badguy['type'] = 'pvp';
			//$options['type'] = 'pvp';
			$session['user']['badguy']=createstring($badguy);
			$session['user']['playerfights']--;
		}
	} elseif (($op=="fight" || $op=="run")){
		if (!$chatoverride && !httpget("frombio")){
			$battle = true;
		} else {
			worldmapen_determinenav();
			if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
			worldmapen_viewmapkey(true, false);
		}
		// $args = modulehook("count-travels", array('available'=>0,'used'=>0));
		// $free = max(0, $args['available'] - $args['used']);
		// if (get_module_setting("usestamina")==1){
			$free = 100;
		// }
		if ($op == "run" && !$pvp) {
			if (!$chatoverride){
				if (e_rand(1, 5) < 3 && $free) {
					// They managed to get away.
					output("You set off running at a breakneck pace!`n`n");
					output("A short time later, you have managed to avoid your opponent, so you stop to catch your breath.");
					$ttoday = get_module_pref("traveltoday", "cities");
					set_module_pref("traveltoday", $ttoday+1, "cities");
					output("As you look around, you realize that all you really managed was to run in circles.");
					$battle = false;
					worldmapen_determinenav();
					if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
					require_once("lib/commentary.php");
					addcommentary();
					$loc = get_module_pref("worldXYZ","worldmapen");
					viewcommentary("mapchat-".$loc,"Chat with others who walk this path...",25);
					worldmapen_viewmapkey(true, false);
				} else {
					output("You try to run, but you don't manage to get away!`n");
					$op = "fight";
					httpset('op', $op);
				}
			} else {
				if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
				require_once("lib/commentary.php");
				addcommentary();
				$loc = get_module_pref("worldXYZ","worldmapen");
				viewcommentary("mapchat-".$loc,"Chat with others who walk this path...",25);
				worldmapen_determinenav();
				worldmapen_viewmapkey(true, false);
			}
		} elseif ($op=="run" && $pvp) {
			output("Your pride prevents you from running");
			$op = "fight";
			httpset('op', $op);
		}
		$loc = get_module_pref('worldXYZ');
		list($x, $y, $z) = explode(",", $loc);
		$t = worldmapen_getTerrain($x, $y, $z);
		//debug($t);
		if ($t['type']=="Forest"){
			$shady = true;
		}
	}
	if ($battle){
		include_once("battle.php");
		if( isset( $enemies ) && !$pvp )
			$badguy = &$enemies;

		if ($victory){
			if ($pvp) {
				require_once("lib/pvpsupport.php");
				$aliveloc = $badguy['location'];
				pvpvictory($badguy, $aliveloc, $options);
				addnews("`4%s`3 defeated `4%s`3 while they were camped in the wilderness.`0", $session['user']['name'], $badguy['creaturename']);
				$badguy=array();
			} else {
				if (!$chatoverride && !httpget('frombio')){
					//is talking
					require_once("lib/forestoutcomes.php");
					forestvictory($badguy, false);
				}
			}
			//has just beaten a badguy
			worldmapen_determinenav();
			if (get_module_setting("smallmap")) worldmapen_viewsmallmap();
			if (!$chatoverride){
				require_once("lib/commentary.php");
				addcommentary();
				$loc = get_module_pref("worldXYZ","worldmapen");
				viewcommentary("mapchat-".$loc,"Chat with others who walk this path...",25);
			}
			worldmapen_viewmapkey(true, false);
		}elseif ($defeat){
			// Reset the players body to the last city they were in
			$session['user']['location'] = get_module_pref('lastCity');
			if ($pvp) {
				require_once("lib/pvpsupport.php");
				require_once("lib/taunt.php");
				$killedloc = $badguy['location'];
				$taunt = select_taunt();
				pvpdefeat($badguy, $killedloc, $taunt, $options);
				addnews("`4%s`3 was defeated while attacking `4%s`3 as they were camped in the wilderness.`0`n%s", $session['user']['name'], $badguy['creaturename'], $taunt);
			} else {
				require_once("lib/forestoutcomes.php");
				forestdefeat($badguy,"in the wild");
			}
			output("`n`n`&You are sure that someone, sooner or later, will stumble over your corpse and return it to %s`& for you.`0" , $session['user']['location']);
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
?>