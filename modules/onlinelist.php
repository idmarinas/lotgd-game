<?php

function onlinelist_getmoduleinfo(){
    $info = array(
        "name"=>"Alternative Sorting",
        "author"=>"Christian Rutsch",
        "version"=>"1.2",
        "category"=>"Administrative",
        "download"=>"http://dragonprime.net/users/XChrisX/onlinelist.zip",
        "allowanonymous" => 1,
    );
    return $info;
}

function onlinelist_install(){
    module_addhook("onlinecharlist");
    return true;
}

function onlinelist_uninstall(){
    return true;
}

function onlinelist_dohook($hookname, $args){
    define("ALLOW_ANONYMOUS",true);

    switch($hookname) {
        case "onlinecharlist":
        	$args['handled'] = true;
			$list_mods = "";
			$list_players = "";

			$sql="SELECT name,alive,location,sex,level,laston,loggedin,lastip,uniqueid FROM " . db_prefix("accounts") . " WHERE locked=0 AND loggedin=1 AND superuser > 0 AND laston > '".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY superuser DESC, level DESC";
			$result = db_query($sql);
			$count = db_num_rows($result);
			$list_mods = appoencode(sprintf(translate_inline("`bOnline Staff`n(%s Staff Member):`b`n"),$count));
			for ($i=0;$i<$count;$i++){
				$row = db_fetch_assoc($result);
				$list_mods .= appoencode("`^{$row['name']}`n");
				$onlinecount_mods++;
			}
			db_free_result($result);
			if ($onlinecount_mods == 0)
				$list_mods .= appoencode(translate_inline("`iNone`i"));

			$sql="SELECT name,alive,location,sex,level,laston,loggedin,lastip,uniqueid FROM " . db_prefix("accounts") . " WHERE superuser = 0 AND locked=0 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC";
			$result = db_query($sql);
			$count = db_num_rows($result);
			$list_players = appoencode(sprintf(translate_inline("`bCharacters Online`n(%s Players):`b`n"),$count));
			for ($i=0;$i<$count;$i++){
				$row = db_fetch_assoc($result);
				$list_players .= appoencode("`^{$row['name']}`n");
				$onlinecount_players++;
			}
			db_free_result($result);
			if ($onlinecount_players == 0)
				$list_players .= appoencode(translate_inline("`iNone`i"));

			$args['list'] = $list_mods . "<br>" . $list_players;
			$args['count'] = $onlinecount_mods + $onlinecount_players;
            break;
    }
    return $args;
}

function onlinelist_run() {
}
?>
