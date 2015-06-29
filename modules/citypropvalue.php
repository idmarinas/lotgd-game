<?php

function citypropvalue_getmoduleinfo(){
    $info = array(
        "name"=>"City Property Values",
		"version"=>"20051220",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
        "author"=>"<a href='http://www.joshuadhall.com'>Sixf00t4</a>",
        "category"=>"Dwellings",
        "description"=>"Allows dwelling types to cost more per city",
        "requires"=>array(
	       "dwellings"=>"20051118|By Sixf00t4, available on DragonPrime",
        ), 
		"prefs-city"=>array(
			"Dwelling Object Prefs,title",
			"goldchange"=>"How much to multiply dwelling gold cost by?,int|1",
			"gemchange"=>"How much to multiply dwelling gem cost by?,int|1",
        ),    
	);
	return $info;
}
function citypropvalue_install(){
    module_addhook("dwellings-pay-costs"); 
    module_addhook("dwellings-buy-valuecheck");    
    return true;
}
function citypropvalue_uninstall() {
	return true;
}
function citypropvalue_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
        case "dwellings-buy-valuecheck":
			require_once("modules/cityprefs/lib.php");
			$cityid = get_cityprefs_cityid("location",$session['user']['location']);
            $args['costgold']=round($args['costgold']*get_module_objpref("city",$cityid,"goldchange"));
            $args['costgems']=round($args['costgems']*get_module_objpref("city",$cityid,"gemchange"));
			break;
        case "dwellings-pay-costs":
			require_once("modules/cityprefs/lib.php");
			$cityid = get_cityprefs_cityid("location",$session['user']['location']);
            $args['costgold']=round($args['costgold']*get_module_objpref("city",$cityid,"goldchange"));
            $args['costgems']=round($args['costgems']*get_module_objpref("city",$cityid,"gemchange"));
			break;
	}
	return $args;
}
?>