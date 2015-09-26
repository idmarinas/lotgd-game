<?php
function docks_getmoduleinfo(){
	$info = array(
		"name"=>"Dragon Eggs Docks",
		"version"=>"1.01",
		"author"=>"DaveS",
		"category"=>"Dragon Expansion",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1362",
		"settings"=>array(
			"River Docks,title",
			"dockdks"=>"Minimum Dks required to go to the docks:,int|1",
			"limitloc"=>"Limit Docks to exist in only one forest?,bool|0",
			"oceanloc"=>"If Limited: Where do the Docks appear,location|".getsetting("villagename", LOCATION_FIELDS),
			"Note: Do NOT leave the Docks in a city without a Forest.,note",
			"interface"=>"Disable the graphics interface?,bool|0",
			"pictures"=>"Disable pictures?,bool|0",
			"Fishing,title",
			"fishingpole"=>"Price of a fishingpole:,int|500",
			"fishingbait"=>"Price of nightcrawlers per day:,int|25",
			"dockfish"=>"Biggest fish caught on the dock:,int|0",
			"dockfishangler"=>"Who caught the biggest dock fish?,text|",
			"fishmin"=>"How many ounces of fish must the player have caught to go fishing on the `&Corinth`0?,int|500",
			"captaincrouton"=>"Who caught Captain Crouton?,text|",
			"croutongold"=>"Gold Reward for catching Captain Crouton:,int|100000",
			"croutongems"=>"Gem Reward for catching Captain Crouton:,int|25",
			"Don't worry. It's a 1 in 1.4 million chance to ever catch him!,note",
		),
		"prefs"=>array(
			"Fishing,title",
			"bait"=>"Does the player have Bait?,bool|0",
			"pole"=>"Does the player have a Fishing Pole?,bool|0",
			"stickstring"=>"Has the player been offered a stick and a string?,bool|0",
			"fishbook"=>"Does the player have the fish book?,bool|0",
			"readbook"=>"Has the player read the book at least once?,bool|0",
			"fishingtoday"=>"Number of times the player gone fishing today?,range,0,5,1|0",
			"bigfish"=>"Largest Fish Player has ever caught in ounces:,int|0",
			"fishweight"=>"How many ounces of fish has the player caught?,int|0",
			"numberfish"=>"How many fish has the player caught?,int|0",
			"forestworms"=>"Did the player find some Nightcrawlers to sell to Hoglin?,bool|0",
			"wind1"=>"Wind at Spot 1:,range,1,30,1|1",
			"depth1"=>"Depth at Spot 1:,range,50,125,1|50",
			"temp1"=>"Temp at Spot 1:,range,60,90,1|60",
			"wind2"=>"Wind at Spot 2:,range,1,30,1|1",
			"depth2"=>"Depth at Spot 2:,range,50,125,1|50",
			"temp2"=>"Temp at Spot 2:,range,60,90,1|60",
			"wind3"=>"Wind at Spot 3:,range,1,30,1|30",
			"depth3"=>"Depth at Spot 3:,range,50,125,1|50",
			"temp3"=>"Temp at Spot 3:,range,60,90,1|60",
			"wind4"=>"Wind at Spot 4:,range,1,30,1|1",
			"depth4"=>"Depth at Spot 4:,range,50,125,1|50",
			"temp4"=>"Temp at Spot 4:,range,60,90,1|60",
			"quality"=>"Quality of fishing spot:,range,1,4,1|1",
			"direction"=>"Which direction are they facing?,enum,1,East,2,West|1",
			"Docks Interface,title",
			"user_interface"=>"Disable the Graphics Interface (Travel using the graphics):,bool|0",
			"Note: This may be disabled by the administrator,note",
			"Maps,title",
			"mazeturn"=>"Maze Turn,int|",
			"fishmap"=>"Fishing Map,viewonly|",
			"pqtemp"=>"Temporary Information,int|",
		),
		"requires"=>array(
			"dragoneggs"=>"1.0|Dragon Eggs Expansion by DaveS",
		),
	);
	return $info;
}
function docks_install(){
	module_addhook("forest");
	module_addhook("newday");
	module_addhook("dragonkill");
	module_addhook("newday-runonce");
	module_addhook("footer-hof");
	module_addeventhook("forest", "return 100;");
	return true;
}
function docks_uninstall(){
	return true;
}
function docks_dohook($hookname,$args){
	global $session;
	require("modules/docks/dohook/$hookname.php");
	return $args;
}
function docks_run(){
	include("modules/docks/docks.php");
}
function docks_runevent($type){
	include("modules/docks/docks_event.php");
}
?>