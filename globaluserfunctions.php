<?php
require_once("common.php");
require_once("lib/dhms.php");

tlschema("globaluserfunctions");

check_su_access(SU_MEGAUSER);

page_header("Global User Functions");
require_once("lib/superusernav.php");
require_once("lib/serverfunctions.class.php");
superusernav();
//addnav("Refresh the stats","stats.php");
addnav("Actions");
addnav("Reset all dragonpoints","globaluserfunctions.php?op=dkpointreset");

output("`n`c`q~~~~~ `\$Global User Functions `q~~~~~`c`n`n");

$op = httpget("op");

switch ($op) {
	case "dkpointreset":
		output("`qThis lets you reset all the dragonpoints for all users on your server.`n`n`\$Handle with care!`q`n`nIf you hit `l\"Reset!\"`q there is no turning back!`n`nAlso note that the hitpoints will be recalculated and the players can respend their points.`n`nThere is also a hook in there allowing modules to reset any things they did.");
		addnav("Dragonpoints");
		addnav("Reset!","globaluserfunctions.php?op=dkpointresetnow");
		break;
	case "dkpointresetnow":
		output("`qExecuting...");
		ServerFunctions::resetAllDragonkillPoints();
		output("... `\$done!`n`n`qIf you need to do a MOTD, you should so so now!");
		break;
	default:
		output("`QWelcome to the Global User Functions.`n`nPlease select your action.");
		break;
}

page_footer();
?>
