<?php
function lumberyard_getmoduleinfo(){
	$info = array(
		"name"=>"The Lumber Yard",
		"version"=>"5.23",
		"author"=>"DaveS, help from Chris Vorndran",
		"category"=>"Materials",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=666",
		"description"=>"Chop wood, accidentally cut down orchard trees, fight bears, lots to do!",
		"settings"=>array(
			"Lumber Yard Settings,title",
			"runonce"=>"Reset turns in the lumberyard only on server-generated game day?,bool|1",
			"alignevil"=>"How many alignment points lost for cutting when the forest is low if triggered?,int|3",
			"lumberturns"=>"How many activities can they perform in a day?,int|7",
			"Note: The following are only need if you are NOT using the cityprefs module:,note",
			"limitloc"=>"Limit Lumberyard's Location?,enum,0,No,1,Yes - One City by Location,2,Yes - By Cityprefs|2",
			// "lumberloc"=>"If Limited by Location: Where does the Lumberyard appear,location|".getsetting("villagename", LOCATION_FIELDS),
			"Note: Do NOT leave the Lumberyard's only location in a city without a Forest if you want it to be used!,note",
			"Orchard Tie-In,title",
			"chopop"=>"Allow players to chop down other players trees in the orchard?,bool|1",
			"fruitalign"=>"How many alignment points lost/gained for intentionally cutting/sparing someone's fruit tree?,int|7",
			"alloworchard"=>"Allow players to find the lemon seed here?,bool|1",
			"Wood Price,title",
			"squarepaymin"=>"How much will the Foreman pay for a Square of Wood minimum?,int|250",
			"squarepaymax"=>"How much will the Foreman pay for a Square of Wood maximum?,int|350",
			"squarepay"=>"How is the Foreman paying for a Square of Wood today?,int|250",
			"leveladj"=>"Divide pay for a Square of Wood by Player's level?,bool|1",
			"levelreq"=>"Require player be this level to sell wood:,range,1,15,1|1",
			"maximumsell"=>"Maximum amount of wood that player can sell per day:,int|50",
			"Note: If you reset turns on system day this will reset on system day. 0=Unlimited,note",
			"woodsold"=>"Wood in stock from sales:,int|0",
			"Reward Settings,title",
			"beargem"=>"Phase 1: Case 10: Gems for searching through the bear droppings?,int|1",
			"crushgold"=>"Phase 2: Case 15: Gold for finding the remains of a dead player?,int|250",
			"crushgem"=>"Phase 2: Case 15: Gems for finding the remains of a dead player?,int|1",
			"gnomegold"=>"Phase 2: Case 17: Gold learning business from the gnomes?,int|200",
			"fingergem"=>"Phase 3: Case 14: Gems from finding cut finger?,int|1",
			"Hall of Fame,title",
			"nosuper"=>"Exclude Superusers from the HoF?,bool|0",
			"usehof"=>"Use Lumber Hall of Fame?,bool|1",
			"usehofp"=>"Use Planting Hall of Fame?,bool|1",
			"perpage"=>"How many players per page in Hall of Fame?,int|25",
		),
		"prefs"=>array(
			// "Lumber Yard,title",
			// "user_stat"=>"Display your number of squares in the Stat bar?,enum,0,No,1,Under Personal Info,2,Under Materials|0",
			"Lumber Yard User Preferences,title",
			"Note: Please edit with caution. Consider using the Allprefs Editor instead.,note",
			"allprefs"=>"Preferences for Lumberyard,textarea|",
		),
		"prefs-city"=>array(
			"chophere" => "Allow the Lumberyard to appear here?, bool|1",
			"fullsize"=>"Size of the lumberyard when full,int|200",
			"remainsize"=>"Number of trees left in lumberyard,int|200",
			"plantneed"=>"Minimum number of trees before the Lumber Yard will allow chopping?,int|100",
			"clearcutter"=>"Name of person that clear cuts the lumber trees,int|Evil Douglas",
			"cutdown"=>"Is the Lumber Yard Currently Clear cut?, bool|0",
			"daygrowth"=>"Number of trees that the yard grows each system newday?,int|10",
			"clearcut"=>"Number of counters before the lumber yard is clear cut, int|10",
			"cccount"=>"Current number of clear cut counters, int|0",
			"cutpercent"=>"Percentage that a counter will trigger on newday, int|50",
		),
	);
	return $info;
}
function lumberyard_install(){
	module_addhook("forest");
	module_addhook("newday");
	module_addhook("newday-runonce");
	module_addhook("footer-hof");
	module_addhook("allprefs");
	module_addhook("allprefnavs");
	// module_addhook_priority("charstats",101);
	return true;
}
function lumberyard_uninstall(){
	return true;
}
function lumberyard_dohook($hookname,$args){
	global $session;
	require_once("modules/lumberyard/lib.php");
	require_once("modules/lumberyard/dohook/$hookname.php");
	return $args;
}
function lumberyard_run(){
	require_once("modules/lumberyard/lumberyard.php");
}
?>