<?php
function orchard_getmoduleinfo() {
	$info = array(
		"name"=>"Fruit Orchard XL",
		"author"=>"Spider, Billie Kennedy<br>Modified by `#Lonny Luberts, XL by DaveS",
		"version"=>"5.22",
		"category"=>"Village",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=148",
		"settings"=>array(
			"Fruit Orchard Settings,title",
			"version"=>"This the new XL version of the orchard,viewonly|1",
			"growth"=>"How many days does it take for a tree to grow?,int|10",
			"everyday"=>"When do trees grow?,enum,0,System Newday,1,Every New Day|0",
			"healfruit"=>"How many temp hitpoints are given per level of tree when eating fruit?,int|7",
			"treesinorchard"=>"How many different types of fruit trees are there to be the orchard?,range,1,20|20",
			"lodgeseed"=>"How many lodge points are needed to buy the 19th seed?,int|25",
			"dryrot"=>"Chance of encounter to get Fruit Tree Disease (FTD) in the Forest?,enum,0,None,1,4%,2,10%,3,25%,4,40%,5,75%,6,100%|2",
			"dryenable"=>"If FTD enabled when could they encounter it again?,enum,0,Anytime,1,1x Per System Newday,2,1x Per Newday,3,1x Per Dragonkill|3",
			"treecare"=>"Number of turns spent for a chance to save their tree if it gets Fruit Tree Disease?,int|5",
			"The current fruit tree will die if they do not spend these turns before the next newday,note",
			"treechance"=>"Percent chance tree will still die despite spending the needed turns trying to save it?,range,1,100,1|10",
			"orchardloc"=>"What city is the Orchard in?,location|".getsetting("villagename", LOCATION_FIELDS),
			"usehof"=>"Use Hall of Fame?,bool|1",
			"perpage"=>"How many trees per page in Hall of Fame?,int|25",
		),
		"prefs"=>array(
			"Fruit Orchard,title",
			"user_stat"=>"Display your current fruit name or a picture in the Stat bar?,enum,0,Nothing,1,Words,2,Images|1",
			"Fruit Orchard User Preferences,title",
			"Note: Please edit with caution. Consider using the Allprefs Editor instead.,note",
			"allprefs"=>"Preferences for Fruit Orchard,textarea|",
		)
	);
	return $info;
}
function orchard_percent($from) {
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs',"orchard",$session['user']['acctid']));
	$seed= $allprefs['seed'];
	$dietreehit= $allprefs['dietreehit'];
	$dryrot= get_module_setting("dryrot","orchard");
	$ret = 0;
	if (is_module_active("cellar")){
		if ($seed==1 && $from=="cellar") $ret = 100;
	}
	if ($seed==2 && $from=="forest") $ret = 100;
	
	if (is_module_active("darkalley")){
		if ($seed==9 && $from=="darkalley") $ret = 100;
	}else{
		if ($seed==9 && $from=="forest") $ret = 100;
	}
	
	if (is_module_active("quarry")){
		if(get_module_setting("alloworchard","quarry")==1 && $seed==15){
		}elseif (get_module_setting("alloworchard","quarry")==0 && $seed==15) $ret = 100;
	}elseif ($seed==15 && $from=="forest") $ret = 100;
	
	if (is_module_active("crazyaudrey")==0 && $seed==16 && $from=="forest") $ret = 100;
	
	if ($seed==17 && $from=="forest") $ret = 100;
	if ($dryrot>0){
		if (($seed==4||$seed==6||$seed==7||$seed==10||$seed==11||$seed==14||$seed==18||$seed==19||$seed==20)&&$from=="forest"){
			if ($dryrot==1) $ret= 4;
			elseif ($dryrot==2) $ret= 10;
			elseif ($dryrot==3) $ret= 25;
			elseif ($dryrot==4) $ret= 40;
			elseif ($dryrot==5) $ret= 75;
			elseif ($dryrot==6) $ret= 100;
			if ($dietreehit==1) $ret=0;
		}
	}
	return $ret;
}

function orchard_install(){
	require_once("modules/orchard/orchard_install.php");
}

function orchard_uninstall(){
	return true;
}

function orchard_dohook($hookname, $args){
	global $session;
	require("modules/orchard/dohook/$hookname.php");
	return $args;
}

function orchard_run(){
	include("modules/orchard/orchard.php");
}

function orchard_runevent($type){
	include("modules/orchard/orchard_event.php");
}
?>