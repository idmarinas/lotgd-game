<?php

//translator ready
//addnews ready
//mail ready

define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);
define("IS_INSTALLER",true);


//php 5 is required for this version
//mysql 5.0.3 is required for this version
$requirements_met=true;
$php_met=true;
$mysql_met=true;

if (version_compare(PHP_VERSION, '5.0.0') < 0) {
	$requirements_met=false;
	$php_met=false;
} elseif (version_compare(mysql_get_client_info(), '5.0.3') < 0) {
	$requirements_met=false;
	$mysql_met=false;
}

if (!$requirements_met) {
	//we have NO output object possibly :( hence no nice formatting
	echo "<h1>Requirements not sufficient<br/><br/>";
	if (!$php_met) echo sprintf("You need PHP5 to install this version. Please upgrade from your existing PHP version %s.<br/>",PHP_VERSION);
	if (!$mysql_met) echo sprintf("You need Mysql 5.0 to install this version. Please upgrade from your existing Mysql version %s.<br/>",mysql_get_client_info());
	exit(1);
}

if (!file_exists("dbconnect.php")){
	define("DB_NODB",true);
}
require_once("common.php");
if (file_exists("dbconnect.php")){
	require_once("dbconnect.php");
}

$noinstallnavs=false;

invalidatedatacache("gamesettings");
$DB_USEDATACACHE = 0;
//make sure we do not use the caching during this, else we might need to run  through the installer multiple times. AND we now need to reset the game settings, as these were due to faulty code not cached before.

tlschema("installer");

$stages=array(
	"1. Introduction",
	"2. License Agreement",
	"3. I Agree",
	"4. Database Info",
	"5. Test Database",
	"6. Examine Database",
	"7. Write dbconnect file",
	"8. Install Type",
	"9. Set Up Modules",
	"10. Build Tables",
	"11. Admin Accounts",
	"12. Done!",
);

$recommended_modules = array(
	"abigail",
	"breakin",
	"calendar",
	"cedrikspotions",
//	"cities", //I don't think this is good for most people.
	"collapse",
	"crazyaudrey",
	"crying",
	"dag",
	"darkhorse",
	"distress",
	"dragonattack",
	"drinks",
	"drunkard",
	"expbar",
	"fairy",
	"findgem",
	"findgold",
	"foilwench",
	"forestturn",
	"game_dice",
	"game_stones",
	"gardenparty",
	"ghosttown",
	"glowingstream",
	"goldmine",
	"grassyfield",
	"haberdasher",
	"healthbar",
	"innchat",
	"kitchen",
	"klutz",
	"lottery",
	"lovers",
	"newbieisland",
	"oldman",
	"outhouse",
	"peerpressure",
	"petra",
	"racedwarf",
	"raceelf",
	"racehuman",
	"racetroll",
	"riddles",
	"salesman",
	"sethsong",
	"smith",
	"soulgem",
	"spa",
	"specialtydarkarts",
	"specialtymysticpower",
	"specialtythiefskills",
	"statue",
	"stocks",
	"stonehenge",
	"strategyhut",
	"thieves",
	"tutor",
	"tynan",
	"waterfall",
);

$DB_USEDATACACHE=0; //Necessary


if ((int)httpget("stage")>0)
	$stage = (int)httpget("stage");
else
	$stage = 0;
if (!isset($session['stagecompleted'])) $session['stagecompleted']=-1;
if ($stage > $session['stagecompleted']+1) $stage = $session['stagecompleted'];
if (!isset($session['dbinfo'])) $session['dbinfo']=array("DB_HOST"=>"","DB_USER"=>"","DB_PASS"=>"","DB_NAME"=>"");
if (file_exists("dbconnect.php") && (
	$stage==3 ||
	$stage==4 ||
	$stage==5
	)){
		output("`%This stage was completed during a previous installation.");
		output("`2If you wish to perform stages 4 through 6 again, please delete the file named \"dbconnect.php\" from your site.`n`n");
		$stage=6;
	}
if ($stage > $session['stagecompleted']) $session['stagecompleted'] = $stage;

page_header("LoGD Installer &#151; %s",$stages[$stage]);
switch($stage) {
	case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9: case 10:
		require_once("lib/installer/installer_stage_$stage.php");
		break;
	default:
		require_once("lib/installer/installer_stage_default.php");
		break;
}


if (!$noinstallnavs){
	if ($session['user']['loggedin']) addnav("Back to the game",$session['user']['restorepage']);
	addnav("Install Stages");

	for ($x=0; $x<=min(count($stages)-1,$session['stagecompleted']+1); $x++){
		if ($x == $stage) $stages[$x]="`^{$stages[$x]} <----";
		addnav($stages[$x],"installer.php?stage=$x");
	}
}
page_footer(false);

?>
