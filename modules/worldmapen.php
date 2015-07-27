<?php
/***********************************************
 World Map
 Originally by: Aes
 Updates & Maintenance by: Kevin Hatfield - Arune (khatfield@ecsportal.com)
 Updates & Maintenance by: Roland Lichti - klenkes (klenkes@paladins-inn.de)
 Updates & Maintenance by: Dan Hall - Caveman Joe (cavemanjoe@gmail.com)
 http://www.dragonprime.net
 Updated: Feb 23, 2008
 ************************************************/

require_once('modules/worldmapen/lib.php');

function worldmapen_getmoduleinfo(){
	$info = array(
	"name"=>"World Map",
	"version"=>"0.9x",
	"author"=>"Originally: AES and Kevin Hatfield, Maintained by Roland Lichti, Stamina and Mount interaction added by Caveman Joe",
	"category"=>"Map",
	"download"=>"http://www.paladins-inn.de/download/worldmapen09.zip",
	"vertxtloc"=>"http://www.dragonprime.net/users/klenkes/",
	"requires"=>array("cities"=>"1.0|This module requires the Multiple Cities module to be installed"),
	"settings"=>array(
		"World Map Settings,title",
		"worldmapsizeX"=>"How wide is the world? (X),int|5",
		"worldmapsizeY"=>"How long is the world? (Y),int|5",
		"extraTravels"=>"How many additional travels are they given per day,int|5",
		"manualmove"=>"Turn on Superuser manual movement?,bool|0",
		"viewRadius"=>"How many squares far can a player see while traveling?,range,0,10,2",
		"worldmapAcquire"=>"Can the world map be purchased?,bool|1",
		"worldmapCostGold"=>"How much gold does the World Map cost?,int|10000",
		"enableTerrains"=>"Enable Terrains?,bool|1",
		"showcompass"=>"Show images/compass.png?,bool|0",
		"compasspoints"=>"8 point compass?,bool|0",
		"showcities"=>"Show the cities in the key? / Will show all cities,bool|0",
		"smallmap"=>"Show small map?,bool|1",
		"showforestnav"=>"Show the forest link in village?,bool|0",
		"wmspecialchance"=>"Chance for a special during travel,int|7",
		"randevent"=>"Random Event -Don't Edit,text|forest",
		"randchance"=>"Percent chance you will get a travel module instead of forest,range,5,100,5",
		
		"Turns and Stamina,title",
		"useturns"=>"Use one of the player's Turns when they encounter a monster?,bool|0",
		"allowzeroturns"=>"Allow the fight to go ahead if the player's Turns are zero?,bool|1",
		"turntravel"=>"Allow the player to trade one of his Turns for this many Travel points (set to zero to disable),int|0",
		"usestamina"=>"Expanded Stamina system is installed and should be used instead of Travel points,bool|0",
		
		"Visual Map Settings,title",
		"colorUserLoc"=>"What color background is the users current location?,text|worldmapen-terrain-current",
		"colorPlains"=>"Color of plains? (default tile),text|worldmapen-terrain-plains",
		"colorForest"=>"Color of dense forest?,text|worldmapen-terrain-forest",
		"colorRiver"=>"Color of river?,text|worldmapen-terrain-river",
		"colorOcean"=>"Color of ocean?,text|worldmapen-terrain-ocean",
		"colorDesert"=>"Color of desert?,text|worldmapen-terrain-desert",
		"colorSwamp"=>"Color of swamp?,text|worldmapen-terrain-swamp",
		"colorMountains"=>"Color of mountains?,text|worldmapen-terrain-mount",
		"colorSnow"=>"Color of Snow?,text|worldmapen-terrain-snow",
		"colorEarth"=>"Color of Earth?,text|worldmapen-terrain-earth",
		"colorAir"=>"Color of Air?,text|worldmapen-terrain-air",
		'All colors must be in CSS here is class name.,note',
	
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
		"encounterMountains"=>"Chance of encountering a monster when crossing mountains?,int|20",
		"encounterSnow"=>"Chance of encountering a monster when crossing snow?,int|20",
		"encounterEarth"=>"Chance of encountering a monster when under surface?,int|1",
		"encounterAir"=>"Chance of encountering a monster when traveling in the air?,int|0",

		"Terrain Settings,title",
		"moveCostPlains"=>"Movement cost for crossing plains?,int|1",
		"moveCostForest"=>"Movement cost for crossing dense forests?,int|1",
		"moveCostRiver"=>"Movement cost for crossing rivers?,int|1",
		"moveCostOcean"=>"Movement cost for crossing oceans?,int|5",
		"moveCostDesert"=>"Movement cost for crossing deserts?,int|2",
		"moveCostSwamp"=>"Movement cost for crossing swamps?,int|2",
		"moveCostMountains"=>"Movement cost for crossing mountains?,int|3",
		"moveCostSnow"=>"Movement cost for crossing snow?,int|3",
		"moveCostEarh"=>"Movement costs for crossing earth?,int|1000",
		"moveCostAir"=>"Movement costs for crossing air?,int|1000",
	),
	"prefs"=>array(
		"World Map User Preferences,title",
		"worldXYZ"=>"World Map X Y Z (separated by commas!)|0,0,0",
		"canedit"=>"Does user have rights to edit the map?,bool|0",
		"lastCity"=>"Where did the user leave from last?|",
		"worldmapbuy"=>"Did user buy map?,bool|0",
		"encounterchance"=>"Player's encounter chance expressed as a percentage of normal,int|100",
		"fuel"=>"The reduced-cost moves that a player has left because of his Mount,int|0",
		//"user_blindoutput"=>"BETA OPTION for blind or visually impaired players using a screen reader - Show textual information about your location on the World Map?,bool|0",
	),
	"prefs-mounts"=>array(
		"World Map Mount Preferences,title",
		"All values are expressed as a decimal value of normal,note",
		"encounterPlains"=>"Encounter rate for crossing plains?,float|1",
		"encounterForest"=>"Encounter rate for crossing dense forests?,float|1",
		"encounterRiver"=>"Encounter rate for crossing rivers?,float|1",
		"encounterOcean"=>"Encounter rate for crossing oceans?,float|1",
		"encounterDesert"=>"Encounter rate for crossing deserts?,float|1",
		"encounterSwamp"=>"Encounter rate for crossing swamps?,float|1",
		"encounterMountains"=>"Encounter rate for crossing mountains?,float|1",
		"encounterSnow"=>"Encounter rate for crossing snow?,float|1",
		"encounterEarth"=>"Encounter rate for crossing earth?,float|1",
		"encounterAir"=>"Encounter rates for crossing air?,float|1",
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
		module_addhook("boughtmount");
		module_addhook("newday");
		module_addhook("items-returnlinks");
	}
	if (is_module_installed("staminasystem")) {
		require_once('modules/staminasystem/lib/lib.php');
		install_action("Travelling - Plains",array(
			"maxcost"=>5000,
			"mincost"=>2500,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>25,
			"class"=>"Travelling"
		));
		install_action("Travelling - Forest",array(
			"maxcost"=>10000,
			"mincost"=>4000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>60,
			"class"=>"Travelling"
		));
		install_action("Travelling - River",array(
			"maxcost"=>15000,
			"mincost"=>5000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>100,
			"class"=>"Travelling"
		));
		install_action("Travelling - Ocean",array(
			"maxcost"=>25000,
			"mincost"=>7500,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>175,
			"class"=>"Travelling"
		));
		install_action("Travelling - Mountains",array(
			"maxcost"=>20000,
			"mincost"=>6000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>140,
			"class"=>"Travelling"
		));
		install_action("Travelling - Snow",array(
			"maxcost"=>25000,
			"mincost"=>7500,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>175,
			"class"=>"Travelling"
		));
		install_action("Travelling - Earth",array(
			"maxcost"=>5000,
			"mincost"=>2500,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>25,
			"class"=>"Travelling"
		));
		install_action("Travelling - Swamp",array(
			"maxcost"=>12500,
			"mincost"=>5000,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>75,
			"class"=>"Travelling"
		));
		install_action("Travelling - Air",array(
			"maxcost"=>25000,
			"mincost"=>7500,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>175,
			"class"=>"Travelling"
		));
		install_action("Travelling - Desert",array(
			"maxcost"=>25000,
			"mincost"=>7500,
			"firstlvlexp"=>500,
			"expincrement"=>1.1,
			"costreduction"=>175,
			"class"=>"Travelling"
		));
	} 
	return true;
}

function worldmapen_uninstall(){
	require_once('modules/staminasystem/lib/lib.php');
	uninstall_action("Travelling - Plains");
	uninstall_action("Travelling - Forest");
	uninstall_action("Travelling - River");
	uninstall_action("Travelling - Ocean");
	uninstall_action("Travelling - Mountains");
	uninstall_action("Travelling - Snow");
	uninstall_action("Travelling - Earth");
	uninstall_action("Travelling - Swamp");
	uninstall_action("Travelling - Air");
	uninstall_action("Travelling - Desert");
	
	return true;
}

function worldmapen_run() {
	require_once('modules/worldmapen/run.php');
	return worldmapen_run_real();
}

function worldmapen_editor() {
	require_once('modules/worldmapen/editor.php');
	return worldmapen_editor_real();
}

function worldmapen_dohook($hookname,$args){
	global $session;
	
	// If the cities module is deactivated, we do nothing.
	if (!is_module_active("cities")) 
		return $args;
	
	if (file_exists("modules/worldmapen/dohook/{$hookname}.php")) {
		require("modules/worldmapen/dohook/{$hookname}.php");
	} else {
		debug("Sorry, I don't have the hook '{$hookname}' programmed.");
	}

	return $args;
}
?>
