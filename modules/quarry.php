<?php
function quarry_getmoduleinfo(){
	$info = array(
		"name"=>"The Quarry",
		"version"=>"5.27",
		"author"=>"DaveS",
		"category"=>"Materials",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=147",
		"settings"=>array(
			"Quarry Settings,title",
			"quarryfinder"=>"Who found the quarry?,text|0",
			"quarryloc"=>"Where was the quarry found?,location|".getsetting("villagename", LOCATION_FIELDS),
			"quarryturns"=>"How many turns can they use in a day at the quarry?,int|3",
			"runonce"=>"Reset turns in the quarry only on server-generated game day?,bool|0",
			"usequarry"=>"Use quarry without being found in lost ruins (if lostruins active)?,bool|0",
			"blockmin"=>"Random blocks available range minimum (if lostruins active)?,int|85",
			"blockmax"=>"Random blocks available range maximum (if lostruins active)?,int|115",
			"blocksleft"=>"How many blocks are left in this Quarry (if lostruins active)?,int|100",
			"newsclosed"=>"Has the Quarry been announced as closed on the news?,bool|0",
			"alloworchard"=>"Allow players to find the lime seed here?,bool|1",
			"ruler"=>"What is the name of the ruler that rewards the player?,text|The King",
			"Stone Pay,title",
			"blockpaymin"=>"How much will the Slatemaker pay for a Stone minimum?,int|250",
			"blockpaymax"=>"How much will the Slatemaker pay for a Stone maximum?,int|350",
			"blockpay"=>"How much is the Slatemaker paying for a Stone today?,int|350",
			"leveladj"=>"Divide pay for a Block of Stone by Player's level?,bool|0",
			"levelreq"=>"Require player be this level to sell Stone:,range,1,15,1|1",
			"maximumsell"=>"Maximum amount of stone that player can sell per day:,int|0",
			"Note: If you reset turns on system day this will reset on system day. 0=Unlimited,note",
			"stonesold"=>"Stone in stock from sales:,int|0",
			"Rewards,title",
			"case13ge"=>"`%Case 13: Gem reward for finding a cache of gems:,enum,1,1-2 Gems,2,2-3 Gems,3,3-5 Gems,4,5-10 Gems|2",
			"case20ge"=>"`%Case 20: Gem reward for perfect work by a mason:,enum,1,1-2 Gems,2,2-3 Gems,3,3-5 Gems,4,5-10 Gems|4",
			"case20bge"=>"`%Case 20b: Gem reward for perfect work if masons is not active:,enum,1,1-2 Gems,2,2-3 Gems,3,3-5 Gems,4,5-10 Gems,5,8-15 Gems|5",
			"case26g"=>"`^Case 26: Gold reward for carving a stone and finding a pocket of gold:,int|650",
			"caseb"=>"`%Boulder Attack: Gem reward for destroying the boulder:,enum,1,1-2 Gems,2,2-3 Gems,3,3-5 Gems,4,5-10 Gems|2",
			"casef"=>"`%Fossil Monster Attack: Gem reward for the Fossil Monster Heart:,enum,1,1-2 Gems,2,2-3 Gems,3,3-5 Gems,4,5-10 Gems|3",
			"Stone Giant Attack, title",
			"giantatk"=>"Number of counters before the Stone Giants attack(if lostruins inactive), int|3",
			"sgcount"=>"Current number of Stone Giant Counters(if lostruins inactive), int|0",
			"sgpercent"=>"Percentage that a counter will trigger on newday(if lostruins inactive), int|30",
			"underatk"=>"Is the Quarry currently under attack?, bool|0",
			"numbgiant"=>"How many Giants start an attack, int|40",
			"giantleft"=>"How many Giants are left?, int|40",
			"Death Insurance, title",
			"insurecost"=>"How much does insurance cost?,int|100",
			"inspaygold"=>"How much gold does insurance pay at death?,int|1000",
			"inspaygems"=>"How many gems does insurance pay at death?,int|5",
			"Hall of Fame, title",
			"usehof"=>"Use Hall of Fame for Blocks of Wood?,bool|1",
			"usehofgiants"=>"Use Hall of Fame for Giant Killers?,bool|1",
			"nosuper"=>"Exclude Superusers from the HoF?,bool|0",
			"perpage"=>"How many players per page in Hall of Fame?,int|50",
		),
		"prefs"=>array(
			"Quarry,title",
			"user_stat"=>"Display your number of blocks in the Stat bar?,enum,0,No,1,Under Personal Info,2,Under Materials|0",
			"Quarry User Preferences,title",
			"Note: Please edit with caution. Consider using the Allprefs Editor instead.,note",
			"allprefs"=>"Preferences for Quarry,textarea|",
		),
		"prefs-city"=>array(			"quarryhere" => "Allow the Quarry to appear here?, bool|1",		),
	);
	return $info;
}
function quarry_install(){
	require_once("modules/quarry/quarry_install.php");
	return true;
}
function quarry_uninstall(){
	return true;
}
function quarry_dohook($hookname,$args){
	global $session;
	require("modules/quarry/dohook/$hookname.php");
	return $args;
}
function quarry_run(){
	include("modules/quarry/quarry.php");
}
?>