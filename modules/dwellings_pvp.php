<?php

function dwellings_pvp_getmoduleinfo(){
	$info = array(
		"name"=>"Dwellings PvP",
		"author"=>"Chris Vorndran",
		"version"=>"1.2",
		"category"=>"Dwellings",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1035",
		"requires"=>array(
			"dwellings"=>"1.0|Dwellings Project Team, http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		),
		"settings"=>array(
			"Dwellings PvP Settings,title",
				"refund"=>"Refund if player overpays?,bool|1",
				"altlist"=>"Alternate Listing for PvP?,bool|0",
				"Alternate Listing means that the person's name will be masked with their DK title and that their bio link will be disabled. This is meant to decrease the amount of player bashing by a single group.,note",
				"whatif"=>"Auto-renew Guards when their time runs out?,bool|1",	
		),
		"prefs-dwellingtypes"=>array(
			"Dwellings PvP Settings,title",
				"pvp"=>"Does this building allow PvP?,bool|1",
				"top-band"=>"How many levels above can a person attack?,int|2",
				"bottom-band"=>"How many levels below can a person attack?,int|1",
			"Guard Cost Settings,title",
				"buy-guard"=>"Does this type of dwelling allow for a Guard?,bool|1",
				"cost-gold"=>"How much does a Guard cost `iin gold`i?,int|50000",
				"cost-gems"=>"How much does a Guard cost `iin gems`i?,int|100",
				"guard-length"=>"How long does the Guard last for `igamedays`i?,int|14",
		),
		"prefs-dwellings"=>array(
			"Dwellings Guard Settings,title",
				"gold-paid"=>"How much gold has been paid?,int|0",
				"gems-paid"=>"How many gems have been paid?,int|0",
				"bought"=>"Has a guard been purchased for this dwelling?,bool|0",
				"isauto"=>"Is auto-purchase enabled for this particular dwelling?,bool|0",
				"run-out"=>"How many days until the guard runs out?,int|0",
		),
	);
	return $info;
}
function dwellings_pvp_install(){
	require("modules/dwellings_pvp/install.php");
	return true;
}
function dwellings_pvp_uninstall(){
	return true;
}
function dwellings_pvp_dohook($hookname,$args){
	global $session,$pvptime,$pvptimeout;
	$pvptime = getsetting("pvptimeout",600);
	$pvptimeout = date("Y-m-d H:i:s",strtotime("-$pvptime seconds"));
	require("modules/dwellings_pvp/dohook/$hookname.php");
	return $args;
}
function dwellings_pvp_run(){
	global $session,$badguy,$pvptime,$pvptimeout,$options;
	$pvptime = getsetting("pvptimeout",600);
	$pvptimeout = date("Y-m-d H:i:s",strtotime("-$pvptime seconds"));
	$last = date("Y-m-d H:i:s", strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	$ac = db_prefix("accounts");
	$mu = db_prefix("module_userprefs");
	$dw = db_prefix("dwellings");
	$cl = db_prefix("clans");
	$op = httpget('op');
	$dwid = httpget('dwid');
	page_header("Dwellings PvP");
	
	if ($op != "fight1" && $op != "fight") require_once("modules/dwellings_pvp/run/case_$op.php");
	
	if ($op == "fight1"){
		$name = rawurldecode(httpget('name'));
		require_once("modules/dwellings/lib.php");
		if (is_numeric($name)) $name = getlogin($name);
		require_once("lib/pvpsupport.php");
		$badguy = setup_target($name);
		require_once("lib/battle-skills.php");
		suspend_buffs("allowinpvp","`&The gods prevent you from using any special abilities!`0");
		$session['user']['badguy'] = createstring($badguy);
		$session['user']['playerfights']--;
		$op = "fight";
	}
	if ($op == "fight"){
		$options['type'] = 'pvp';
		$battle = true;
	}	
    if ($battle){
        include("battle.php");
        if ($victory){
			$killedin = sprintf("%s Dwellings",$session['user']['location']);
			require_once("lib/pvpsupport.php");
			pvpvictory($badguy, $killedin, $options);
            addnews("`4%s`3 defeated `4%s`3 while they were sleeping in their Dwelling.", $session['user']['name'], $badguy['creaturename']);
            $badguy = array();
			unsuspend_buffs("allowinpvp","`&The gods have restored your special abilities!`0");
			addnav("Leave");
			addnav("Hamlet Registry","runmodule.php?module=dwellings&op=list&ref=hamlet");
        }elseif ($defeat){
			$killedin = sprintf("%s Dwellings",$session['user']['location']);
			require_once("lib/taunt.php");
			$taunt = select_taunt_array();
			require_once("lib/pvpsupport.php");
			pvpdefeat($badguy, $killedin, $taunt, $options);
			unsuspend_buffs("allowinpvp","`&The gods have restored your special abilities!`0");
            addnews("`4%s`3 was defeated while attacking `4%s`3 as they were sleeping in their Dwelling.`n%s", $session['user']['name'], $badguy['creaturename'], $taunt);
			output("`n`n`&You are sure that someone, sooner or later, will stumble over your corpse and return it to %s for you." , $session['user']['location']);
			addnav("Return to the Shades","shades.php");
        }else{
			$script = "runmodule.php?module=dwellings_pvp&op=fight";
			require_once("lib/fightnav.php");
	        fightnav(false,false,$script);
        }
    }
	page_footer();
}
?>