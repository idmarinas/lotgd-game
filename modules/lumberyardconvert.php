<?php
function lumberyardconvert_getmoduleinfo(){
	$info = array(
		"name"=>"Lumberyard Converter",
		"version"=>"5.01",
		"author"=>"DaveS",
		"category"=>"Converter",
		"download"=>"",
		"settings"=>array(
			"Lumber Yard Settings,title",
			"runonce"=>"Reset turns in the lumberyard only on server-generated game day?,bool|0",
			"fullsize"=>"Size of the lumberyard when full,int|200",
			"remainsize"=>"Number of trees left in lumberyard,int|200",
			"plantneed"=>"Minimum number of trees before the Lumber Yard will allow chopping?,int|100",
			"alignevil"=>"How many alignment points lost for cutting when the forest is low if triggered?,int|3",
			"daygrowth"=>"Number of trees that the yard grows each newday?,int|10",
			"clearcutter"=>"Name of person that clear cuts the lumber trees,text|Evil Douglas",
			"clearcut"=>"Number of counters before the lumber yard is clear cut, int|10",
			"cccount"=>"Current number of clear cut counters, int|6",
			"cutpercent"=>"Percentage that a counter will trigger on newday, int|50",
			"cutdown"=>"Is the Lumber Yard Currently Clear cut?, bool|0",
			"lumberturns"=>"How many activities can they perform in a day?,int|7",
			"lcharstats"=>"Where do you want to list Squares in Char Stats?,enum,0,Personal Info,1,Materials|0",
			"Orchard Tie-In,title",
			"chopop"=>"Allow players to chop down other players trees in the orchard?,bool|1",
			"chopchance"=>"Likelihood of chopping down another players tree?,enum,0,Never,1,Very Rare,2,Rare,3,Common",
			"Changing this setting will not effect the program as this is a placeholder setting,note",
			"fruitalign"=>"How many alignment points lost/gained for intentionally cutting/sparing someone's fruit tree?,int|7",
			"alloworchard"=>"Allow players to find the lemon seed here?,bool|1",
			"Wood Price,title",
			"squarepaymin"=>"How much will the Foreman pay for a Square of Wood minimum?,int|250",
			"squarepaymax"=>"How much will the Foreman pay for a Square of Wood maximum?,int|350",
			"squarepay"=>"How is the Foreman paying for a Square of Wood today?,int|250",
			"leveladj"=>"Divide pay for a Square of Wood by Player's level?,bool|0",
			"Reward Settings,title",
			"beargem"=>"Phase 1: Case 10: Gems for searching through the bear droppings?,int|1",
			"crushgold"=>"Phase 2: Case 15: Gold for finding the remains of a dead player?,int|250",
			"crushgem"=>"Phase 2: Case 15: Gems for finding the remains of a dead player?,int|1",
			"gnomegold"=>"Phase 2: Case 17: Gold learning business from the gnomes?,int|200",
			"fingergem"=>"Phase 3: Case 14: Gems from finding cut finger?,int|1",
			"Hall of Fame,title",
			"usehof"=>"Use Hall of Fame?,bool|1",
			"perpage"=>"How many players per page in Hall of Fame?,int|25",
		),
		"prefs"=>array(
			"Converter - Lumberyard,title",
			"allprefs"=>"All pref for new lumberyard:,textarea|",
		),
		"requires"=>array(
			"lumberyard"=>"3.0|by DaveS",
		),
	);
	return $info;
}
function lumberyardconvert_install(){
	module_addhook("superuser");
	$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
	$res = db_query($sql);
	for ($i=0;$i<db_num_rows($res);$i++){
		$row = db_fetch_assoc($res);
		$id=$row['acctid'];
		$allprefs=unserialize(get_module_pref('allprefs',"lumberyardconvert",$id));
		$allprefs['firstl']=get_module_pref("firstl","lumberyard",$id);
		$allprefs['usedlts']=get_module_pref("usedlts","lumberyard",$id);
		$allprefs['phase']=get_module_pref("phase","lumberyard",$id);
		$allprefs['squares']=get_module_pref("squares","lumberyard",$id);
		$allprefs['squareshof']=get_module_pref("squareshof","lumberyard",$id);
		$allprefs['ccspiel']=get_module_pref("ccspiel","lumberyard",$id);
		$allprefs['fruitname']=get_module_pref("fruitname","lumberyard",$id);
		$allprefs['fruitid']=get_module_pref("fruitid","lumberyard",$id);
		set_module_pref("allprefs",serialize($allprefs),"lumberyardconvert",$id);
	}
	set_module_setting("runonce",get_module_setting("runonce","lumberyard"),"lumberyardconvert");
	set_module_setting("fullsize",get_module_setting("fullsize","lumberyard"),"lumberyardconvert");
	set_module_setting("remainsize",get_module_setting("remainsize","lumberyard"),"lumberyardconvert");
	set_module_setting("plantneed",get_module_setting("plantneed","lumberyard"),"lumberyardconvert");
	set_module_setting("alignevil",get_module_setting("alignevil","lumberyard"),"lumberyardconvert");
	set_module_setting("daygrowth",get_module_setting("daygrowth","lumberyard"),"lumberyardconvert");
	set_module_setting("clearcutter",get_module_setting("clearcutter","lumberyard"),"lumberyardconvert");
	set_module_setting("clearcut",get_module_setting("clearcut","lumberyard"),"lumberyardconvert");
	set_module_setting("cccount",get_module_setting("cccount","lumberyard"),"lumberyardconvert");
	set_module_setting("cutpercent",get_module_setting("cutpercent","lumberyard"),"lumberyardconvert");
	set_module_setting("cutdown",get_module_setting("cutdown","lumberyard"),"lumberyardconvert");
	set_module_setting("lumberturns",get_module_setting("lumberturns","lumberyard"),"lumberyardconvert");
	set_module_setting("lcharstats",get_module_setting("lcharstats","lumberyard"),"lumberyardconvert");
	set_module_setting("chopop",get_module_setting("chopop","lumberyard"),"lumberyardconvert");
	set_module_setting("chopchance",get_module_setting("chopchance","lumberyard"),"lumberyardconvert");
	set_module_setting("fruitalign",get_module_setting("fruitalign","lumberyard"),"lumberyardconvert");
	set_module_setting("alloworchard",get_module_setting("alloworchard","lumberyard"),"lumberyardconvert");
	set_module_setting("squarepaymin",get_module_setting("squarepaymin","lumberyard"),"lumberyardconvert");
	set_module_setting("squarepaymax",get_module_setting("squarepaymax","lumberyard"),"lumberyardconvert");
	set_module_setting("squarepay",get_module_setting("squarepay","lumberyard"),"lumberyardconvert");
	set_module_setting("leveladj",get_module_setting("leveladj","lumberyard"),"lumberyardconvert");
	set_module_setting("beargem",get_module_setting("beargem","lumberyard"),"lumberyardconvert");
	set_module_setting("crushgold",get_module_setting("crushgold","lumberyard"),"lumberyardconvert");
	set_module_setting("crushgem",get_module_setting("crushgem","lumberyard"),"lumberyardconvert");
	set_module_setting("gnomegold",get_module_setting("gnomegold","lumberyard"),"lumberyardconvert");
	set_module_setting("fingergem",get_module_setting("fingergem","lumberyard"),"lumberyardconvert");
	set_module_setting("usehof",get_module_setting("usehof","lumberyard"),"lumberyardconvert");
	set_module_setting("perpage",get_module_setting("pp","lumberyard"),"lumberyardconvert");
	output("`b`nPLEASE DO NOT UNINSTALL THE LUMBERYARD CONVERTER MODULE YET.");
	output("`nYou should now UNINSTALL the OLD LUMBERYARD module.");
	output("`nAfter you have uninstalled the Old lumberyard Module, Copy the new Lumberyard to your module directory and install it.");
	output("`nThen go to the grotto to Converters: Convert Lumberyard.`n`n`b");
	return true;
}
function lumberyardconvert_uninstall(){
	return true;
}
function lumberyardconvert_dohook($hookname,$args){
	switch($hookname){
		case "superuser":
			addnav("Converters");
			addnav("Convert Lumberyard","runmodule.php?module=lumberyardconvert&op=super");
		break;
	}
	return $args;
}
function lumberyardconvert_run(){
	global $session;
	$op = httpget('op');
	page_header("Lumberyard Converter");
	if ($op=="super"){
		$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
		$res = db_query($sql);
		for ($i=0;$i<db_num_rows($res);$i++){
			$row = db_fetch_assoc($res);
			$id=$row['acctid'];
			set_module_pref("allprefs",get_module_pref("allprefs","lumberyardconvert",$id),"lumberyard",$id);
		}
		set_module_setting("runonce",get_module_setting("runonce","lumberyardconvert"),"lumberyard");
		set_module_setting("fullsize",get_module_setting("fullsize","lumberyardconvert"),"lumberyard");
		set_module_setting("remainsize",get_module_setting("remainsize","lumberyardconvert"),"lumberyard");
		set_module_setting("plantneed",get_module_setting("plantneed","lumberyardconvert"),"lumberyard");
		set_module_setting("alignevil",get_module_setting("alignevil","lumberyardconvert"),"lumberyard");
		set_module_setting("daygrowth",get_module_setting("daygrowth","lumberyardconvert"),"lumberyard");
		set_module_setting("clearcutter",get_module_setting("clearcutter","lumberyardconvert"),"lumberyard");
		set_module_setting("clearcut",get_module_setting("clearcut","lumberyardconvert"),"lumberyard");
		set_module_setting("cccount",get_module_setting("cccount","lumberyardconvert"),"lumberyard");
		set_module_setting("cutpercent",get_module_setting("cutpercent","lumberyardconvert"),"lumberyard");
		set_module_setting("cutdown",get_module_setting("cutdown","lumberyardconvert"),"lumberyard");
		set_module_setting("lumberturns",get_module_setting("lumberturns","lumberyardconvert"),"lumberyard");
		set_module_setting("lcharstats",get_module_setting("lcharstats","lumberyardconvert"),"lumberyard");
		set_module_setting("chopop",get_module_setting("chopop","lumberyardconvert"),"lumberyard");
		set_module_setting("chopchance",get_module_setting("chopchance","lumberyardconvert"),"lumberyard");
		set_module_setting("fruitalign",get_module_setting("fruitalign","lumberyardconvert"),"lumberyard");
		set_module_setting("alloworchard",get_module_setting("alloworchard","lumberyardconvert"),"lumberyard");
		set_module_setting("squarepaymin",get_module_setting("squarepaymin","lumberyardconvert"),"lumberyard");
		set_module_setting("squarepaymax",get_module_setting("squarepaymax","lumberyardconvert"),"lumberyard");
		set_module_setting("squarepay",get_module_setting("squarepay","lumberyardconvert"),"lumberyard");
		set_module_setting("leveladj",get_module_setting("leveladj","lumberyardconvert"),"lumberyard");
		set_module_setting("beargem",get_module_setting("beargem","lumberyardconvert"),"lumberyard");
		set_module_setting("crushgold",get_module_setting("crushgold","lumberyardconvert"),"lumberyard");
		set_module_setting("crushgem",get_module_setting("crushgem","lumberyardconvert"),"lumberyard");
		set_module_setting("gnomegold",get_module_setting("gnomegold","lumberyardconvert"),"lumberyard");
		set_module_setting("fingergem",get_module_setting("fingergem","lumberyardconvert"),"lumberyard");
		set_module_setting("usehof",get_module_setting("usehof","lumberyardconvert"),"lumberyard");
		set_module_setting("perpage",get_module_setting("perpage","lumberyardconvert"),"lumberyard");
		output("Conversion Complete.  You may now Uninstall the Lumberyard Converter Module.");
		addnav("Navigation");
		addnav("Return to the Grotto","superuser.php");
		addnav("Manage Modules","modules.php");
		villagenav();
	}
page_footer();
}
?>