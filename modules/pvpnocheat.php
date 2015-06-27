<?php
// This module prevents players to see others with the same
// uniqueid or IP address for PvP
// Version info
// 1.0 initial release
// 1.1 fixed debuglog
// 1.2 rewrite target identity

function pvpnocheat_getmoduleinfo(){
	$info = array(
		"name"=>"PvP NoCheat",
		"version"=>"1.3",
		"author"=>"Daniel Kalchev",
		"category"=>"PVP",
//		"allowanonymous"=>true,
//		"override_forced_nav"=>true,
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1236",
		"settings"=>array(
			"PvP No Cheat Settings,title",
			"sameid"=>"Hide same ID players?,bool|1",
			"sameidmsg"=>"Messages to show if same ID players were found?,text|`4You see %s clones of yourself sleeping here, sharing your ID.`n`0",
			"showsameidmsg"=>"Show message to same ID message?,bool|1",
			"Note: The messages can contain %s to indicate the number of clones found,note",
			"sameip"=>"Hide same IP players?,bool|1",
			"sameipmsg"=>"Messages to show if same IP players were found?,text|`4You see %s clones of yourself sleeping here, sharing your IP.`n`0",
			"showsameipmsg"=>"Show message to same IP message?,bool|1",
		),
	);
	return $info;
}

function pvpnocheat_install(){
	module_addhook("pvpmodifytargets");
	return true;
}

function pvpnocheat_uninstall(){
	return true;
}

function pvpnocheat_dohook($hookname,$args){
	global $session;

	switch($hookname){
	case "pvpmodifytargets":
		$sameid=get_module_setting("sameid");
		$sameip=get_module_setting("sameip");
		$sameidnames=0;
		$sameipnames=0;
                while (list($key,$row)=each($args)) {
			$sql="SELECT uniqueid,lastip,location FROM ".db_prefix('accounts')." WHERE acctid = ". $row['acctid'];
                        $result=db_query($sql);
                        $sleeper=db_fetch_assoc($result);

                        if ($sleeper['uniqueid']==$session['user']['uniqueid'] && $sameid) {
                                $args[$key]['invalid'] = 1;
                                if ($session['user']['location']!=$sleeper['location']) continue;
                                $sameidnames++;
                        }
                        if ($sleeper['lastip']==$session['user']['lastip'] && $sameip) {
                                $args[$key]['invalid'] = 1;
                                if ($session['user']['location']!=$sleeper['location']) continue;
                                $sameipnames++;
                        }
                }
		if ($sameidnames>0) {
			if (get_module_setting('showsameidmsg'))
				output(get_module_setting('sameidmsg'),$nameidnames);
			debuglog("had $nameidnames instances in the field with the same ID");
		}
		if ($sameipnames>0)
			if (get_module_setting('showsameipmsg'))
				output(get_module_setting('sameipmsg'),$nameipnames);
			debuglog("had $sameipnames instances in the field with the same IP address");

		break;
	}
	return $args;
}

function pvpnocheat_run(){
}

?>
