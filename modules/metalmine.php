<?php
function metalmine_getmoduleinfo(){
	$info = array(
		"name"=>"Metal Mine",
		"version"=>"5.32",
		"author"=>"DaveS and MaryAnn",
		"category"=>"Materials",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1143",
		"settings"=>array(
			"Metal Mine Settings,title",
			"minename"=>"What is the name of the Mine?,text|Metal Mine",
			"ffs"=>"How many ffs does it take to get to the Mine?,int|2",
			"mineturnset"=>"How many Mine Turns do they get a day?,int|10",
			"runonce"=>"Reset turns in the mine only on server-generated game day?,bool|0",
			"permhps"=>"Allow player to gain permanent hitpoints from the mine?,bool|1",
			"alignbase"=>"Base metal type on player's alignment?,enum,0,Yes - a lot,1,Yes - a little,2,No|0",
			"Note: This is irrelevant if you are using 3 Mines with Different Metals,note",
			"losealign"=>"Make players that have gone to the mine but didn't help the trapped miners lose alignment points?,bool|1",
			"limitloc"=>"Choose Mine Location Setting:,enum,0,All Forests,1,One City,2,3 Mines with Different Metals|0",
			"metalloc1"=>"Location of One City Mine or Location of Ore Mine:,location|".getsetting("villagename", LOCATION_FIELDS),
			"name1"=>"Name of Ore Mine:,text|Hydra",
			"metalloc2"=>"Location of Copper Mine:,location|".getsetting("villagename", LOCATION_FIELDS),
			"name2"=>"Name of Copper Mine:,text|Arrow",
			"metalloc3"=>"Location of Mithril Mine:,location|".getsetting("villagename", LOCATION_FIELDS),
			"name3"=>"Name of Mithril Mine:,text|Swan",
			"Note: Do NOT leave the Metal Mine in a city without a Forest.,note",
			"Metal Price,title",
			"kilo"=>"What is the base price per kilo?,int|150",
			"Warning: Price= Base*ffs to get to mine. This can imbalance the game if too high!,note",
			"leveladj"=>"Divide pay for Metal by Player's level?,bool|0",
			"levelreq"=>"Require player be this level to sell Metal:,range,1,15,1|1",
			"maximumsell"=>"Maximum amount of Metal that player can sell per day:,int|0",
			"Note: If you reset turns on system day this will reset on system day. 0=Unlimited,note",
			"metalsold1"=>"Iron Ore in stock from sales:,int|0",
			"metalsold2"=>"Copper in stock from sales:,int|0",
			"metalsold3"=>"Mithril in stock from sales:,int|0",
			"Accident,title",
			"dayssince"=>"How  many days has it been without an accident?,int|0",
			"whodoneit"=>"Who caused the last accident?,text|",
			"collapse"=>"How many triggers are needed to cause a section of the mine to collapse?,int|3",
			"accident"=>"How many triggers have there been so far?,int|0",
			"down"=>"Is the mine currently closed due to an accident?,bool|0",
			"rescue"=>"How many rescue attempts are needed before the trapped miners can be freed?,int|15",
			"effort"=>"How many rescue attempts have there been so far?,int|0",
			"massyom"=>"Send out mass YoM requesting help when miners are trapped?,bool|1",
			"Note: If this is set to No there is no penalty for failing to help the miners,note",
			"Mine Store,title",
			"pickaxe1"=>"How much is the Basic Pickaxe?,int|250",
			"pickaxe2"=>"How much is the Standard Pickaxe?,int|1200",
			"pickaxe3"=>"How much is the Quality Pickaxe?,int|4700",
			"helmet1"=>"How much is the Basic Helmet?,int|220",
			"helmet2"=>"How much is the Standard Helmet?,int|1630",
			"helmet3"=>"How much is the Quality Helmet?,int|4025",
			"canary"=>"How much does the canary cost?,int|25",
			"Hall of Fame, title",
			"nosuper"=>"Exclude Superusers from the HoF?,bool|0",
			"usehof"=>"Use Metal Hall of Fame?,bool|1",
			"usehofr"=>"Use Rescue Hall of Fame?,bool|1",
			"perpage"=>"How many players per page in Hall of Fame?,int|25",
		),
		"prefs"=>array(
			"Mine User Preferences,title",
			"user_stat"=>"Display your different metals in the Stat bar?,enum,0,No,1,Under Personal Info,2,Under Materials|0",
			"maze"=>"Secret Chamber,viewonly|",
			"pqtemp"=>"Temporary Information,int|",
			"Allprefs,title",
			"Note: Please edit with caution. Consider using the Allprefs Editor instead.,note",
			"allprefs"=>"Preferences for Metal Mine,textarea|",
		),
	);
	return $info;
}
function metalmine_install(){
	module_addhook_priority("charstats", 111);
	module_addhook("footer-hof");
	module_addhook("forest");
	module_addhook("newday");
	module_addhook("newday-runonce");
	module_addhook("allprefs");
	module_addhook("allprefnavs");
	return true;
}
function metalmine_uninstall(){
	return true;
}
function metalmine_dohook($hookname,$args){
	global $session;
	require("modules/metalmine/dohook/$hookname.php");
	return $args;
}
function metalmine_run(){
	include("modules/metalmine/metalmine.php");
}
?>