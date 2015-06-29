<?php
function faerietrees_getmoduleinfo(){
    $info = array(
        "name"=>"Faerie Tree Dwellings",
		"version"=>"20060120",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
        "author"=>"<a href='http://www.joshuadhall.com' target=_new>Sixf00t4</a>",
        "category"=>"Dwellings",
        "description"=>"Gives faerie trees as a dwelling option for players",
        "requires"=>array(
			"racefaer"=>"1.11|Chris Vorndran, http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=40",
	       "dwellings"=>"20060105|By Sixf00t4, available on DragonPrime",
        ), 
        "settings"=>array(
            "Faerietree Settings,title",
                "dwname"=>"What is the display name for this type?,text|`%faerie tree",
                "dwnameplural"=>"What is the plural display name for this type?,text|`%faerie trees",
                ""=>"<i>Enter display names in lowercase</i>,note",
                "globallimit"=>"How many are allowed globally? (0 = infinite),int|0",
                "goldcost"=>"What is the cost in gold?,int|2500",
                "gemcost"=>"What is the cost in gems?,int|5",
                "turncost"=>"How many turns does it cost to build?,int|5",
                "maxkeys"=>"What is max number of keys available per faerietree?,int|3",
                "ownersleep"=>"Enable sleeping for owner?,bool|0",
                "othersleep"=>"Enable sleeping for others?,bool|0",
                "maxsleep"=>"What is the max number of sleepers?,int|2",
                "dkreq"=>"How many DKs before they can see this type?,int|2",
				"typeid" => "What is the type number in the db?,viewonly|0",
			"Coffer Settings,title",
                "enablecof"=>"Enable coffers?,bool|1",
                "maxgold"=>"What is the max storage of gold? - 0 to disable,int|2500",
                "maxgems"=>"What is the max storage of gems? - 0 to disable,int|5",
				"goldxfer"=>"What is the amount limit for each coffer transaction of gold?(per level),int|250",
                "gemsxfer"=>"What is the amount limit for each coffer transaction of gem?,int|2",	
		),
        "prefs-city"=>array(
            "showfaerietrees"=>"Allow faerie trees here?,bool|0",
            "loclimitfaerietrees"=>"How many total faerie trees are allowed here? (0 = infinite),int|0",
            "userloclimitfaerietrees"=>"How many faerie trees are allowed per person here? (0 = infinite),int|1",
			),		
    );        
    return $info;
}

function faerietrees_install(){
    module_addhook("dwellings");
    module_addhook("dwellings-inside");
    module_addhook("dwellings-list-type");
    if (!is_module_active('faerietrees')){
        $sql = "SELECT module FROM ".db_prefix("dwellingtypes")." WHERE module='faerietrees'";
        $res=db_query($sql);
        if(db_num_rows($res)==0){
            $sql = "INSERT INTO ".db_prefix("dwellingtypes")." (module) VALUES ('faerietrees')";
            db_query($sql);
        }
    }
    $sql = "SELECT typeid FROM ".db_prefix("dwellingtypes")." WHERE module='faerietrees'";
    $result = db_query($sql);
    $row = db_fetch_assoc($result);
    set_module_setting("typeid",$row['typeid'],"faerietrees");
    return true;
}

function faerietrees_uninstall() {
    $sql = "DELETE FROM ".db_prefix("dwellingtypes")." WHERE module='faerietrees'";
    db_query($sql);  
	return true;
}

function faerietrees_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
 
        case "dwellings-list-type":
            addnav("Show Only Types");
            addnav(array("%s",ucfirst(translate_inline(get_module_setting("dwnameplural","faerietrees")))),"runmodule.php?module=dwellings&op=list&showonly=faerietrees&ref={$args['ref']}&sortby={$args['sortby']}&order={$args['order']}");
        break;

        case "dwellings":
            if(get_module_objpref("city",$args['cityid'],"showfaerietrees")){
				output("  You see faint lights emitting from some %s`0 in the wooded area to the left.",translate_inline(get_module_setting("dwnameplural")));
				if($args['allowbuy']==1 
					&& $session['user']['race'] == 'Faerie' 
					&& $session['user']['dragonkills'] >= get_module_setting("dkreq")){
					$cityid=$args['cityid'];
					addnav("Options");
					addnav(array("Establish a %s",ucfirst(translate_inline(get_module_setting("dwname","faerietrees")))),"runmodule.php?module=dwellings&op=buy&type=faerietrees&subop=presetup&cityid=$cityid");
				}
			}
        break;

        case "dwellings-inside":
            if($args['type']=='faerietrees' && $session['user']['race'] != 'Faerie'){
				redirect("runmodule.php?module=faerietrees&dwid=".$args['dwid']."");
			}
        break;
		}
	return $args;
}

function faerietrees_run(){
	page_header("You can't fit in here!");
	$dwid = httpget('dwid');
	output("How do you expect to fit into that knot hole???  You should know only faeries can enter these type of dwellings.  Atleast you'll be able to talk into the hole.`n`n");
	require_once("lib/commentary.php");
	addcommentary();
	viewcommentary("dwellings-".$dwid, "You hear voices from the tree", 20, "speaks from outside"); 
	addnav("Back to the hamlet","runmodule.php?module=dwellings");
	page_footer();
}
?>