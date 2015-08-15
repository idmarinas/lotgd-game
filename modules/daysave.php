<?php

// v1.1 fixed a bug that caused a possible infinite newday loop when not logging out after using a newday
// V1.2 Fixes newday hook, added debug info by SexyCook
// V1.3 Added hook to jail
// V1.4 commented the debugs that were getting on my nerves, added an output for 0 days, due to translation difficulties.
// V1.5 Fixed the bug that gave new players the max amount of saved days
// V2.0 CMJ update - Donation Points functionality, World Map integration, Instant Buy option, Chronosphere integration

function daysave_getmoduleinfo(){
	$info = array(
		"name"=>"Game Day Accumulation",
		"author"=>"CavemanJoe, based on daysave.php by Exxar with fixes by SexyCook",
		"version"=>"2.0",
		"category"=>"General",
		"settings"=>array(
			"startdays"=>"Number of game days with which to start a new player,int|2",
			"startslots"=>"Number of game day slots to start,int|2",
			"buyslotcost"=>"Players can buy an extra day slot in return for this many Donator Points,int|250",
			"fillslotcost"=>"Players have the option to fill up their days when buying a new day slot in exchange for this many Donator Points per day to be filled,int|10",
			"buydaycost"=>"Players have the option to buy an Instant New Day at any time for this many Donator Points,int|25",
			"maxbuyday"=>"Players can buy only this many Instant New Days per real Game Day,int|1",
		),
		"prefs"=>array(
			"days"=>"Current number of saved Game Days,int|0",
			"slots"=>"Maximum number of saved Game Days,int|0",
			"instantbuys"=>"Number of Instant New Days bought during this Game Day,int|0",
			"lastlognewday"=>"Next newday after logout,int|5",
			"initsetup"=>"Player has been initially granted their starting settings,bool|0",
			),
		);
	return $info;
}

function daysave_install(){
	module_addhook("newday");
	module_addhook("newday-runonce");
	module_addhook("player-logout");
	module_addhook("village");
	module_addhook("shades");
	module_addhook("worldnav");
	return true;
}

function daysave_uninstall(){
	return true;
}

function daysave_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "newday":
			$days=get_module_pref("days");
			$slots=get_module_pref("slots");
			$lastonnextday=get_module_pref("lastlognewday");
			$time=gametimedetails();
			$timediff=$time['gametime']-$lastonnextday;
			if ($timediff>86400){
				$addition=floor($timediff/86400);
				$days+=$addition;
				if ($days > $slots) $days=$slots;
				if($lastonnextday<1){
					$days=0;
				}
				set_module_pref("days", $days);
			}
			set_module_pref("lastlognewday", $time['tomorrow']);
		break;
		case "newday-runonce":
			//reset all players' Instant Buys counter
			$sql = "SELECT acctid FROM " . db_prefix("accounts");
			$result = db_query($sql);
			for ($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				clear_module_pref("instantbuys",false,$row['acctid']);
			}
		break;
		case "player-logout":
			$details=gametimedetails();
			set_module_pref("lastlognewday", $details['tomorrow']);
			break;
		case "village":
			//tlschema('daysavenav');
			addnav("The Fields");
			addnav("Saved Days","runmodule.php?module=daysave&op=start&return=village");
		break;
		case "shades":
			//tlschema('daysavenav');
			addnav("New Day Menu");
			addnav("Saved Days","runmodule.php?module=daysave&op=start&return=shades");
		break;
		case "worldnav":
			//tlschema('daysavenav');
			addnav("New Day Menu");
			addnav("Saved Days","runmodule.php?module=daysave&op=start&return=worldmapen");
		break;
	}
	return $args;
}

function daysave_run(){
	global $session;
	$op = httpget('op');
	$return = httpget('return');
	//handle new players
	if (!get_module_pref("initsetup")){
		set_module_pref("slots",get_module_setting("startslots"));
		set_module_pref("days",get_module_setting("startdays"));
		set_module_pref("initsetup",1);
	}
	$days = get_module_pref("days");
	$slots = get_module_pref("slots");
	$startdays = get_module_setting("startdays");
	$startslots = get_module_setting("startslots");
	$boughttoday = get_module_pref("instantbuys");
	$buyslotcost = get_module_setting("buyslotcost");
	$buydaycost = get_module_setting("buydaycost");
	$fillslotcost = get_module_setting("fillslotcost");
	$maxbuyday = get_module_setting("maxbuyday");
	$dps = $session['user']['donation']-$session['user']['donationspent'];

	page_header("Saved Days");
		switch ($op){
			case "start":
				output("Here are your Chronospheres.  Each coloured sphere represents one saved-up game day.`n`n");
				if ($startdays && $session['user']['dragonkills']<1){
					output("New players start the game with some days already saved up, so that they can get a feel for how this works.`n`n");
				}
				
				for ($full=1; $full<=$days; $full++){
					$daySave = translate_inline("Saved Day");
					rawoutput("<img src=\"images/daysphere-full.png\" alt=\"$daySave\" title=\"$daySave\">");
				}
				for ($empty=$full; $empty<=$slots; $empty++){
					$dayEmptySlot = translate_inline("Empty Day Slot");
					rawoutput("<img src=\"images/daysphere-empty.png\" alt=\"$dayEmptySlot\" title=\"$dayEmptySlot\">");
				}
				if ($days==1){
					$daydisp = translate_inline("Day saved");
				} else {
					$daydisp = translate_inline("Days saved");
				}
				output("`n`nChronospheres essentially allow you to save up game days for later play, simply by not logging in.  As you can see, you have %s Game %s up, out of a maximum of %s.`n`n",$days,$daydisp,$slots);
				addnav("Chronofiddling");
				if ($days){
					addnav("Use a saved day","runmodule.php?module=daysave&op=useday");
				} else {
					addnav("You have no saved days","");
				}
				if ($maxbuyday==1){
					$maxdisp = translate_inline("Day");
				} else {
					$maxdisp = translate_inline("Days");
				}
				addnav("Donator Options");
				output("Site supporters have several extra options.  As a site supporter, you can instantly start a new Game Day for your character at any time in exchange for %s Donator Points.  You can also add more Chronospheres, allowing you to save up more days to play later.  Each additional Chronosphere costs %s Donator Points, and come pre-filled.  When adding Chronospheres, you have the option of refilling your empty Spheres for a discount cost of %s Donator Points per empty Sphere.`n`nFor game balance reasons, donators can only buy %s Instant Game %s per real game day.`n`nYou currently have %s Donator Points available.  See the Hunter's Lodge in any Village for a more detailed explanation of how to get Donator Points, and other cool things you can do with them.",$buydaycost,$buyslotcost,$fillslotcost,$maxbuyday,$maxdisp,number_format($dps));
				if ($dps>=$buydaycost && $boughttoday < $maxbuyday){
					addnav(array("Buy an Instant New Day for %s Donator Points",$buydaycost),"runmodule.php?module=daysave&op=buyday");
				} else if ($dps<$buydaycost){
					addnav("Not enough Donator Points for an Instant New Day","");
				} else {
					addnav("Instant New Day limit reached","");
				}
				if ($dps>=$buyslotcost){
					addnav(array("Buy an extra Chronosphere for %s Donator Points",$buyslotcost),"runmodule.php?module=daysave&op=buyslot&return=".$return);
				} else {
					addnav("Not enough Donator Points for a new Chronosphere","");
				}
				addnav("Exit");
				if ($return=="village") {
					tlschema('nonav');
					villagenav();
				}
				else if($return=="shades") {
					tlschema('nonav');
					addnav("Back to the Shades", "shades.php");
				}
				else if($return=="worldmapen") {
					tlschema('nonav');
					addnav("Return to the World Map", "runmodule.php?module=worldmapen&op=continue");
				}
				else addnav("Your navs are corrupted!", "badnav.php");
			break;
			case "useday":
				$days-=1;
				$savedDay = translate_inline('Saved Day');
				if ($days<0) $days=0;
				set_module_pref("days", $days);
				output("You have used one of your Chronospheres.  Now go forth and have fun!`n`n");
				for ($full=1; $full<=$days; $full++){
					rawoutput("<img src=\"images/daysphere-full.png\" alt=\"$savedDay\" title=\"$savedDay\">");
				}
				for ($empty=$full; $empty<=$slots; $empty++){
					rawoutput("<img src=\"images/daysphere-empty.png\" alt=\"Empty Day Slot\" title=\"Empty Day Slot\">");
				}
				addnav("It is a New Day!","newday.php");
			break;
			case "buyday":
			$savedDay = translate_inline('Saved Day');
				output("You have bought one new Game Day in exchange for %s Donator Points, leaving you with %s points left to spend.  Your Chronospheres are unaffected.  Now go forth and have fun!`n`n",$buydaycost,number_format($dps-$buydaycost));
				for ($full=1; $full<=$days; $full++){
					rawoutput("<img src=\"images/daysphere-full.png\" alt=\"$savedDay\" title=\"$savedDay\">");
				}
				for ($empty=$full; $empty<=$slots; $empty++){
					rawoutput("<img src=\"images/daysphere-empty.png\" alt=\"Empty Day Slot\" title=\"Empty Day Slot\">");
				}
				addnav("It is a New Day!","newday.php");
				$session['user']['donationspent']+=$buydaycost;
				increment_module_pref("instantbuys");
			break;
			case "buyslot":
				$savedDay = translate_inline('Saved Day');
				$emptyDay = translate_inline('Empty Day Slot');
				$session['user']['donationspent']+=$buyslotcost;
				increment_module_pref("days");
				increment_module_pref("slots");
				$days = get_module_pref("days");
				$slots = get_module_pref("slots");
				$dps = $session['user']['donation']-$session['user']['donationspent'];
				output("You have bought one additional Chronosphere in exchange for %s Donator Points, leaving you with %s points left to spend.`n`n",$buyslotcost,number_format($dps));
				for ($full=1; $full<=$days; $full++){
					rawoutput("<img src=\"images/daysphere-full.png\" alt=\"$savedDay\" title=\"$savedDay\">");
				}
				for ($empty=$full; $empty<=$slots; $empty++){
					rawoutput("<img src=\"images/daysphere-empty.png\" alt=\"$emptyDay\" title=\"$emptyDay\">");
				}
				if ($days<$slots && $dps>=$fillslotcost){
					addnav("Fill up Chronospheres","");
					output("`n`nYou now have the option of refilling your empty Chronospheres for %s Donator Points each.`n`n",$fillslotcost);
					$empty = $slots-$days;
					for ($i=1; $i<=$empty; $i++){
						$cost = $i*$fillslotcost;
						if ($dps>=$cost){
							if ($i==1){
								$p = translate_inline("Sphere");
							} else {
								$p = translate_inline("Spheres");
							}
							addnav(array("Fill up %s %s for %s Donator Points",$i,$p,$cost),"runmodule.php?module=daysave&op=fillup&fill=".$i."&return=".$return);
						}
					}
				}
				addnav("Return");
				addnav("Back to the menu","runmodule.php?module=daysave&op=start&return=".$return);
			break;
			case "fillup":
				$savedDay = translate_inline('Saved Day');
				$emptyDay = translate_inline('Empty Day Slot');
				$fill = httpget('fill');
				$session['user']['donationspent']+=($fill*$fillslotcost);
				$dps = $session['user']['donation']-$session['user']['donationspent'];
				increment_module_pref("days",$fill);
				$days = get_module_pref("days");
				if ($fill==1){
					$p = translate_inline("Chronosphere");
				} else {
					$p = translate_inline("Chronospheres");
				}
				output("You have filled up %s %s in exchange for %s Donator Points, leaving you with %s points left to spend.`n`n",$fill,$p,number_format($fill*$fillslotcost),number_format($dps));
				for ($full=1; $full<=$days; $full++){
					rawoutput("<img src=\"images/daysphere-full.png\" alt=\"$savedDay\" title=\"$savedDay\">");
				}
				for ($empty=$full; $empty<=$slots; $empty++){
					rawoutput("<img src=\"images/daysphere-empty.png\" alt=\"$emptyDay\" title=\"$emptyDay\">");
				}
				addnav("Return","");
				addnav("Back to the menu","runmodule.php?module=daysave&op=start&return=".$return);
			break;
		}
	page_footer();
}
?>
