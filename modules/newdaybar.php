<?php

/*
 * Title:       New Day Bar
 * Date:	Sep 06, 2004
 * Version:	1.2
 * Author:      Joshua Ecklund
 * Email:       m.prowler@cox.net
 * Purpose:     Add a countdown timer for the new day to "Personal Info"
 *              status bar.
 *
 * --Change Log--
 *
 * Date:    	Jul 30, 2004
 * Version:	1.0
 * Purpose:     Initial Release
 *
 * Date:        Aug 01, 2004
 * Version:     1.1
 * Purpose:     Various changes/fixes suggested by JT Traub (jtraub@dragoncat.net)
 *
 * Date:        Sep 06, 2004
 * Version:     1.2
 * Purpose:     Updated to use functions included in 0.9.8-prerelease.3
 *
 */

function newdaybar_getmoduleinfo(){
	$info = array(
		"name"=>"New Day Bar",
		"version"=>"1.2",
		"author"=>"Joshua Ecklund",
                "download"=>"http://dragonprime.net/users/mProwler/newdaybar.zip",
		"category"=>"Stat Display",
		"settings"=>array(
			"New Day Bar Module Settings,title",
			"showtime"=>"Show time to new day,bool|1",
			"showbar"=>"Show time as a bar,bool|1",
		)
	);
	return $info;
}

function newdaybar_install(){
	module_addhook("charstats");
	return true;
}

function newdaybar_uninstall(){
	return true;
}

function newdaybar_dohook($hookname,$args){
	global $session;

	switch($hookname){
		case "charstats":
                        require_once("lib/datetime.php");

                        $details = gametimedetails();
                        $secstonewday = secondstonextgameday($details);

                        $newdaypct = round($details['realsecstotomorrow'] / $details['secsperday'] * 100,0);
	        	$newdaynon = 100 - $newdaypct;

        		$newdaytxt = date("G\\h i\\m s\\s",$secstonewday);

		        if ($newdaypct > 100) { $newdaypct = 100; $newdaynon = 0; }
        		elseif ($newdaypct < 0) { $newdaypct = 0; $newdaynon = 100; }

		        $color = "#00ff00";
        		$ccode = "`@";
			$hicode = "`&";
	                $stat = "Next day";

			$showtime = get_module_setting("showtime");
			$showbar  = get_module_setting("showbar");
	        	$new = "";

			if (!$showtime && !$showbar) $new="`b`\$hidden`b";
			if ($showtime) $new .= $ccode . $newdaytxt;
			if ($showbar) {
				if ($showtime) $new .= "<br />";
				$new .= "<table style='border: solid 1px #000000' bgcolor='#777777' cellpadding='0' cellspacing='0' width='100%' height='5' title='$newdaytxt'><tr><td width='$newdaypct%' bgcolor='$color'></td><td width='$newdaynon%'></td></tr></table>";
			}
			setcharstat("Personal Info", $stat, $new);
			break;
	}
	return $args;
}

function newdaybar_run(){

}
?>
