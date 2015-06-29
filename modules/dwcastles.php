<?php
function dwcastles_getmoduleinfo(){
    $info = array(
        "name"=>"Castle Dwellings",
		"version"=>"20060210",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
        "author"=>"<a href='http://www.joshuadhall.com' target=_new>Sixf00t4</a>",
        "category"=>"Dwellings",
        "description"=>"Gives castles as a dwelling option for players",
        "requires"=>array(
	       "dwellings"=>"20060105|By Sixf00t4, available on DragonPrime",
        ), 
        "settings"=>array(
            "castles Settings,title",
                "dwname"=>"What is the display name for this type?,text|`7castle",
                "dwnameplural"=>"What is the plural display name for this type?,text|`7castles",
                ""=>"<i>Enter display names in lowercase</i>,note",
                "globallimit"=>"How many are allowed globally? (0 = infinite),int|0",
                "goldcost"=>"What is the cost in gold?,int|100000",
                "gemcost"=>"What is the cost in gems?,int|100",
                "turncost"=>"How many turns does it cost to build?,int|35",
                "maxkeys"=>"What is max number of keys available per castle?,int|15",
                "ownersleep"=>"Enable sleeping for owner?,bool|0",
                "othersleep"=>"Enable sleeping for others?,bool|0",
                "maxsleep"=>"What is the max number of sleepers?,int|8",
                "dkreq"=>"How many DKs before they can see this type?,int|15",
				"typeid" => "What is the type number in the db?,viewonly|0",
			"Coffer Settings,title",
                "enablecof"=>"Enable coffers?,bool|1",
                "maxgold"=>"What is the max storage of gold? - 0 to disable,int|50000",
                "maxgems"=>"What is the max storage of gems? - 0 to disable,int|25",
				"goldxfer"=>"What is the amount limit for each coffer transaction of gold?(per level),int|800",
                "gemsxfer"=>"What is the amount limit for each coffer transaction of gem?,int|2",
		),
        "prefs-city"=>array(
            "showdwcastles"=>"Allow castles here?,bool|1",
            "loclimitdwcastles"=>"How many total castles are allowed here? (0 = infinite),int|0",
            "userloclimitdwcastles"=>"How many castles are allowed per person here? (0 = infinite),int|1",
			),		
    );        
    return $info;
}

function dwcastles_install(){
    module_addhook("dwellings");
    module_addhook("dwellings-list-type");
    if (!is_module_active('dwcastles')){
        $sql = "SELECT module FROM ".db_prefix("dwellingtypes")." WHERE module='dwcastles'";
        $res = db_query($sql);
        if(db_num_rows($res)==0){
            $sql = "INSERT INTO ".db_prefix("dwellingtypes")." (module) VALUES ('dwcastles')";
            db_query($sql);
        }
    }
    $sql = "SELECT typeid FROM ".db_prefix("dwellingtypes")." WHERE module='dwcastles'";
    $result = db_query($sql);
    $row = db_fetch_assoc($result);
    set_module_setting("typeid",$row['typeid'],"dwcastles");
    return true;
}

function dwcastles_uninstall() {
    $sql = "DELETE FROM ".db_prefix("dwellingtypes")." WHERE module='dwcastles'";
    db_query($sql);  
	return true;
}

function dwcastles_dohook($hookname,$args) {
	global $session;
	switch ($hookname) { 
        case "dwellings-list-type":
            addnav("Show Only Types");
            addnav(array("%s",ucfirst(translate_inline(get_module_setting("dwnameplural","dwcastles")))),"runmodule.php?module=dwellings&op=list&showonly=dwcastles&ref={$args['ref']}&sortby={$args['sortby']}&order={$args['order']}");
			break;
        case "dwellings":
            if(get_module_objpref("city",$args['cityid'],"showdwcastles")){
				output("  High on the far hills, you see waving flags of the glorious %s`0 of the rich and fabulous.",translate_inline(get_module_setting("dwnameplural")));
				if($args['allowbuy']==1 && $session['user']['dragonkills']>=get_module_setting("dkreq")){
					$cityid=$args['cityid'];
					addnav("Options");
					addnav(array("Establish a %s",ucfirst(translate_inline(get_module_setting("dwname","dwcastles")))),"runmodule.php?module=dwellings&op=buy&type=dwcastles&subop=presetup&cityid=$cityid");
				}
			}
			break;
	}
	return $args;
}

function dwcastles_run(){}
?>