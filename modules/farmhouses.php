<?php
function farmhouses_getmoduleinfo(){
    $info = array(
        "name"=>"Farmhouse Dwellings",
		"version"=>"20060120",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
        "author"=>"<a href='http://www.joshuadhall.com' target=_new>Sixf00t4</a>",
        "category"=>"Dwellings",
        "description"=>"Gives farmhouses as a dwelling option for players",
        "requires"=>array(
	       "dwellings"=>"20060105|By Sixf00t4, available on DragonPrime",
        ), 
        "settings"=>array(
            "farmhouses Settings,title",
                "dwname"=>"What is the display name for this type?,text|`qfarmhouse",
                "dwnameplural"=>"What is the plural display name for this type?,text|`qfarmhouses",
                ""=>"<i>Enter display names in lowercase</i>,note",
                "globallimit"=>"How many are allowed globally? (0 = infinite),int|0",
                "goldcost"=>"What is the cost in gold?,int|20000",
                "gemcost"=>"What is the cost in gems?,int|10",
                "turncost"=>"How many turns does it cost to build?,int|5",
                "maxkeys"=>"What is max number of keys available per farmhouse?,int|8",
                "ownersleep"=>"Enable sleeping for owner?,bool|0",
                "othersleep"=>"Enable sleeping for others?,bool|0",
                "maxsleep"=>"What is the max number of sleepers?,int|2",
                "dkreq"=>"How many DKs before they can see this type?,int|3",
				"typeid" => "What is the type number in the db?,viewonly|0",
			"Coffer Settings,title",
                "enablecof"=>"Enable coffers?,bool|1",
                "maxgold"=>"What is the max storage of gold? - 0 to disable,int|5000",
                "maxgems"=>"What is the max storage of gems? - 0 to disable,int|8",
 				"goldxfer"=>"What is the amount limit for each coffer transaction of gold? (per level),int|500",
                "gemsxfer"=>"What is the amount limit for each coffer transaction of gem?,int|2",
		),
        "prefs-city"=>array(
            "showfarmhouses"=>"Allow farmhouses here?,bool|1",
            "loclimitfarmhouses"=>"How many total farmhouses are allowed here? (0 = infinite),int|0",
            "userloclimitfarmhouses"=>"How many farmhouses are allowed per person here? (0 = infinite),int|1",
			),		
    );        
    return $info;
}

function farmhouses_install(){
    module_addhook("dwellings");
    module_addhook("dwellings-list-type");
    if (!is_module_active('farmhouses')){
        $sql = "SELECT module FROM ".db_prefix("dwellingtypes")." WHERE module='farmhouses'";
        $res=db_query($sql);
        if(db_num_rows($res)==0){
            $sql = "INSERT INTO ".db_prefix("dwellingtypes")." (module) VALUES ('farmhouses')";
            db_query($sql);
        }
    }
	$sql = "SELECT typeid FROM ".db_prefix("dwellingtypes")." WHERE module='farmhouses'";
    $result = db_query($sql);
    $row = db_fetch_assoc($result);
    set_module_setting("typeid",$row['typeid'],"farmhouses");
    return true;
}

function farmhouses_uninstall() {
    $sql = "DELETE FROM ".db_prefix("dwellingtypes")." WHERE module='farmhouses'";
    db_query($sql);  
	return true;
}

function farmhouses_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
 
        case "dwellings-list-type":
            addnav("Show Only Types");
            addnav(array("%s",ucfirst(get_module_setting("dwnameplural","farmhouses"))),"runmodule.php?module=dwellings&op=list&showonly=farmhouses&ref={$args['ref']}&sortby={$args['sortby']}&order={$args['order']}");
        break;

        case "dwellings":
            if(get_module_objpref("city",$args['cityid'],"showfarmhouses")){
				output("  Off in the distance, you can see smoke rising from chimneys of a few %s.`0",get_module_setting("dwnameplural"));
				if($args['allowbuy']==1 && $session['user']['dragonkills']>=get_module_setting("dkreq")){
					$cityid=$args['cityid'];
					addnav("Options");
					addnav(array("Establish a %s",ucfirst(get_module_setting("dwname","farmhouses"))),"runmodule.php?module=dwellings&op=buy&type=farmhouses&subop=presetup&cityid=$cityid");
				}
			}
        break;
	}
	return $args;
}

function farmhouses_run(){}
?>