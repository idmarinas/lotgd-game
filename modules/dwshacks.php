<?php
function dwshacks_getmoduleinfo(){
    $info = array(
        "name"=>"Shacks as dwellings",
		"version"=>"20060120",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
        "author"=>"<a href='http://www.joshuadhall.com' ta>Sixf00t4</a>",
        "category"=>"Dwellings",
        "description"=>"Gives shacks as a dwelling option for players",
        "requires"=>array(
	       "dwellings"=>"20060105|By Sixf00t4, available on DragonPrime",
        ), 
        "settings"=>array(
            "dwshacks Settings,title",
                "dwname"=>"What is the display name for this type?,text|`qshack",
                "dwnameplural"=>"What is the plural display name for this type?,text|`qshacks",
                ""=>"<i>Enter display names in lowercase</i>,note",
                "globallimit"=>"How many are allowed globally? (0 = infinite),int|0",
                "goldcost"=>"What is the cost in gold?,int|2500",
                "gemcost"=>"What is the cost in gems?,int|3",
                "turncost"=>"How many turns does it cost to build?,int|10",
                "maxkeys"=>"What is max number of keys available per shack?,int|123456789",
                ""=>"<i>Set keys to 123456789 to enable public entry</i>,note",
                "ownersleep"=>"Enable sleeping for owner?,bool|0",
                "othersleep"=>"Enable sleeping for others?,bool|0",
                "maxsleep"=>"What is the max number of sleepers?,int|1",
                "dkreq"=>"How many DKs before they can see this type?,int|1",
				"typeid" => "What is the type number in the db?,viewonly|0",
			"Coffer Settings,title",
                "enablecof"=>"Enable coffers?,bool|1",
                "maxgold"=>"What is the max storage of gold? - 0 to disable,int|50",
                "maxgems"=>"What is the max storage of gems? - 0 to disable,int|0",
				"goldxfer"=>"What is the amount limit for each coffer transaction of gold?(per level),int|25",
                "gemsxfer"=>"What is the amount limit for each coffer transaction of gem?,int|2",
		),
        "prefs-city"=>array(
            "showdwshacks"=>"Allow dwshacks here?,bool|1",
            "loclimitdwshacks"=>"How many total shacks are allowed here? (0 = infinite),int|0",
            "userloclimitdwshacks"=>"How many shacks are allowed per person here? (0 = infinite),int|1",
			),		
    );        
    return $info;
}

function dwshacks_install(){
    module_addhook("dwellings");
    module_addhook("dwellings-manage");
    module_addhook("dwellings-list-type");
    module_addhook("dwellings-list-interact");
    if (!is_module_active('dwshacks')){
        $sql = "SELECT module FROM ".db_prefix("dwellingtypes")." WHERE module='dwshacks'";
        $res = db_query($sql);
        if(db_num_rows($res) == 0){
            $sql = "INSERT INTO ".db_prefix("dwellingtypes")." (module) VALUES ('dwshacks')";
            db_query($sql);
        }
    }
    $sql = "SELECT typeid FROM ".db_prefix("dwellingtypes")." WHERE module='dwshacks'";
    $result = db_query($sql);
    $row = db_fetch_assoc($result);
    set_module_setting("typeid",$row['typeid'],"dwshacks");
    return true;
}

function dwshacks_uninstall() {
    $sql = "DELETE FROM ".db_prefix("dwellingtypes")." WHERE module='dwshacks'";
    db_query($sql);  
	return true;
}

function dwshacks_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
 
        case "dwellings-list-type":
            addnav("Show Only Types");
            addnav(array("%s",translate_inline(ucfirst(get_module_setting("dwnameplural","dwshacks")))),"runmodule.php?module=dwellings&op=list&showonly=dwshacks&ref={$args['ref']}&sortby={$args['sortby']}&order={$args['order']}");
        break;
		
        case "dwellings-list-interact":
			if($args['type']=="dwshacks" && $args['status']==1 && get_module_setting("maxkeys")==123456789){
				addnav("","runmodule.php?module=dwellings&op=enter&dwid={$args['dwid']}");
				$tress=translate_inline("Tresspass");
				rawoutput("<a href='runmodule.php?module=dwellings&op=enter&dwid={$args['dwid']}'>$tress</a>");
			}
        break;

        case "dwellings-manage":
			$dwid=$args['dwid'];
			if(get_module_setting("maxkeys")==123456789 && $args['type']=="dwshacks") blocknav("runmodule.php?module=dwellings&op=keys&dwid=$dwid");	
        break;
		
        case "dwellings":
            if(get_module_objpref("city",$args['cityid'],"showdwshacks")){
				output("  Along the narrow pathway, precariously placed wood planks are nailed and leaning on each other in a fashion that only leaves you to assume they are supposed to be %s.`0",translate_inline(get_module_setting("dwnameplural")));
				if($args['allowbuy']==1 && $session['user']['dragonkills']>=get_module_setting("dkreq")){
					$cityid=$args['cityid'];
					addnav("Options");
					addnav(array("Establish a %s",translate_inline(ucfirst(get_module_setting("dwname","dwshacks")))),"runmodule.php?module=dwellings&op=buy&type=dwshacks&subop=presetup&cityid=$cityid");
				}
			}
        break;
	}
	return $args;
}

function dwshacks_run(){}
?>