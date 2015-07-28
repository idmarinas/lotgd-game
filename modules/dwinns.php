<?php

require_once("lib/http.php");

function dwinns_getmoduleinfo(){
	$info = array(
	"name"=>"User controlled inns as dwellings ",
	"version"=>"2.16",
	"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1064",
	"author"=>"SexyCook and Maeher",
	"category"=>"Dwellings",
	"description"=>"Gives user controlled Inns for the dwelling system",
	"requires"=>array(
		"dwellings"=>"20060724|By Sixf00t4, available on DragonPrime",
	),
	"settings"=>array(
		"dwinns Settings,title",
		"dwname"=>"What is the display name for this type?,text|`qinn",
		"dwnameplural"=>"What is the plural display name for this type?,text|`qinns",
		""=>"<i>Enter display names in lowercase</i>,note",
		"globallimit"=>"How many are allowed globally? (0 = infinite),int|0",
		"goldcost"=>"What is the cost in gold?,int|25000",
		"gemcost"=>"What is the cost in gems?,int|25",
		"turncost"=>"How many turns does it cost to build?,int|25",
		"maxkeys"=>"What is max number of keys available per inn?,int|123456789",
		""=>"<i>Set keys to 123456789 to enable public entry</i>,note",
		"ownersleep"=>"Enable sleeping for owner?,bool|1",
		"othersleep"=>"Enable sleeping for others?,bool|1",
		"maxsleep"=>"What is the max number of sleepers?,int|2",
		"dkreq"=>"How many DKs before they can see this type?,int|10",
		"typeid" => "What is the type number in the db?,viewonly|10",
		"Coffer Settings,title",
		"enablecof"=>"Enable coffers?,bool|1",
		"maxgold"=>"What is the max storage of gold? - 0 to disable - 12345 for unlimited,int|10000",
		"maxgems"=>"What is the max storage of gems? - 0 to disable - 12345 for unlimited,int|0",
		"goldxfer"=>"What is the amount limit for each coffer transaction of gold?(per level),int|250",
		"gemsxfer"=>"What is the amount limit for each coffer transaction of gem?,int|2",
		"Inn own settings,title",
		"roomgold"=>"How much gold is needed to add a new room (times stars/2)?,int|5000",
		"roomgems"=>"How many gems are needed to add a new room (times stars/2)?,int|3",
		"maxmealrand"=>"min e_rand for rotting meals is 0 - max e_rand is x * number of stars,int|3",
		"maxdwinnsmeals"=>"maximum amount of meals a player can consume on an inn per day,int|3",
		"maxrottenmeals"=>"maximum percentage of meals that can go rotten per day,range,1,100,10|50",
		"maxstaleales"=>"maximum percentage of ales that can go stale per day,range,1,100,10|50",
		"basisrooms"=>"How many rooms should a new inn have?,int|2",
		"basisprice"=>"What's the basis price of a night in the inn (including increments!),int|12",
		"multaddgold"=>"What's the gold multiplicator to the room price when buying an ad in the village (price * x)?,int|1000",
		"multaddgems"=>"What's the gems multiplicator to the stars number when buying an ad in the village (stars * x)?,int|2",
		"multimpgold"=>"What's the gold multiplicator to the stars number when improving the inn (stars+1 * x)?,int|5000",
		"multimpgems"=>"What's the gems multiplicator to the stars number when improving the inn (stars+1 * x)?,int|5",
		"ownerguest"=>"Allow the owner of the inn to use the facilities as a guest?,bool|1",
		"aledrunk"=>"What is the drunkness value of the ale (bad ale makes you just as drunk as good one),range,1,100|50",
		"maxalepoints"=>"What is the maximum amount of points the user can distribute to the attributes of his ale in total?,int|15",
		"maxalepointseach"=>"What is the maximum amount of points the user can distribute to each attribute of his ale?,int|10",
	),
	"prefs-city"=>array(
		"showdwinns"=>"Allow dwinns here?,bool|1",
		"loclimitdwinns"=>"How many total inns are allowed here? (0 = infinite),int|0",
		"userloclimitdwinns"=>"How many inns are allowed per person here? (0 = infinite),int|1",
	),
	"prefs"=>array(
		"sleepingindwinn"=>"In which dwinn is the user sleeping, if any, 0 if none?,int|0",
		"dwinnsmeals"=>"How many meals has the user eat today at the inn, int|0",
	),
	);
	return $info;
}

function dwinns_install(){
	global $session;
	require("modules/dwinns/install.php");
	return true;
}

function dwinns_uninstall() {
	require("modules/dwinns/uninstall.php");
	return true;
}

function dwinns_dohook($hookname,$args) {
	global $session;
	require("modules/dwinns/dohook/$hookname.php");
	return $args;
}

function dwinns_run(){
	global $session;
	$op = httpget('op');
	require("modules/dwinns/run/$op.php");
	page_footer();
}
?>
