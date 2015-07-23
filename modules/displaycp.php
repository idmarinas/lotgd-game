<?php

function displaycp_getmoduleinfo(){
	$info = array(
		"name"=>"Stat Display Control Panel",
		"author"=>"Chris Vorndran",
		"version"=>"1.22",
		"category"=>"Stat Display",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=63",
		"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
		"description"=>"This module will display many different things for a user, including drunkeness, specialty, charm, DKs and many other things.",
		"settings"=>array(
			"Drunkeness Bar Settings,title",
			"sober"=>"Name of True Soberness,text|Sobrio",
			"level1"=>"Name of Level One of Drunkeness,text|Mareado",
			"level2"=>"Name of Level Two of Drunkeness,text|Alegre",
			"level3"=>"Name of Level Three of Drunkeness,text|Cantarín",
			"level4"=>"Name of Level Four of Drunkeness,text|Como una cuba",
			"level5"=>"Name of True Drunkeness,text|Cogorza",
			"Favor Display Settings,title",
			"wo"=>"Which heading does this fall under,enum,0,Vital Info,1,Personal Info,2,Extra Info|0",
			"Admin Overrides,title",
			"spec"=>"Allow users to show Specialty,bool|1",
			"charm"=>"Allow users to show Charm,bool|1",
			"dk"=>"Allow users to show Dragonkills,bool|0",
			"dsdk"=>"Allow users to see Days Since DK,bool|0",
			"gib"=>"Allow users to show Gold in Bank,bool|1",
			"mast"=>"Allow users to show Seen Master,bool|1",
			"fav"=>"Allow users to show Favor,bool|1",
			"donate"=>"Allow users to show Donation Points,bool|1",
			"drunk"=>"Allow users to show Drunkeness,bool|0",
			"pf"=>"Allow users to show PvPs,bool|0",
		),
		"prefs"=>array(
			"Stat Display Control Panel,title",
			"user_showspec"=>"Do you wish for Specialty to be displayed?,bool|1",
			"user_showcharm"=>"Do you wish for Charm to be displayed?,bool|1",
			// "user_showdk"=>"Do you wish for Dragonkills to be displayed?,bool|1",
			// "user_showdsdk"=>"Do you wish for Day Since DK to be displayed?,bool|1",
			"user_showgib"=>"Do you wish for Gold In Bank to be displayed?,bool|1",
			"user_showmast"=>"Do you wish to see if you have fought your master yet today?,bool|1",
			"user_sfav"=>"Do you wish to see Favor `iwhilst alive`i?,bool|1",
			"user_showpart"=>"Do you wish to see your current Donation Points?,bool|1",
			// "user_showfull"=>"Do you wish to see your total Donation Points?,bool|0",
			"user_showdrunk"=>"Do you wish to see your Drunkeness?,bool|1",
			// "user_showpf"=>"Do you wish to see your PvPs?,bool|0",
			"user_note"=>"Some of these may be overridden by your local Admin,note",
		),
	);
	return $info;
}
function displaycp_install(){
	module_addhook("charstats");
	return true;
}
function displaycp_uninstall(){
	return true;
}
function displaycp_dohook($hookname,$args){
	global $session;
	$specialty = modulehook("specialtynames");
	switch ($hookname){
		case "charstats":
			$point=getsetting('moneydecimalpoint',",");
			$sep=getsetting('moneythousandssep',".");
			
			if (get_module_pref("user_showspec") && get_module_setting("spec")){
				$spec = $specialty[$session['user']['specialty']];
				setcharstat ("Character Info","Specialty",$spec);
			}
			// if (get_module_pref("user_showdk") && get_module_setting("dk")){
			// 	$amnt = $session['user']['dragonkills'];
			// 	setcharstat ("Extra Info","Dragonkills",$amnt);
			// }
			// if (get_module_pref("user_showdsdk") && get_module_setting("dsdk")){
			// 	$amnt = $session['user']['age'];
			// 	setcharstat ("Extra Info","Days Since DK",$amnt);
			// }
			if (get_module_pref("user_showcharm")  && get_module_setting("charm")){
				$amnt = $session['user']['charm'];
				setcharstat ("Personal Info","Charm",$amnt);
			}
			if (get_module_pref("user_showgib") && get_module_setting("gib")){
				$amnt = $session['user']['goldinbank'];
				setcharstat ("Extra Info","Gold in Bank",number_format($amnt,0,$point,$sep));
			}
			if (get_module_pref("user_showmast") && get_module_setting("mast")){
				$amnt = translate_inline($session['user']['seenmaster'] == 0?"No Viewed":"Viewed");
				setcharstat("Extra Info","Master",$amnt);
			}
			if (get_module_pref("user_sfav") && ($session['user']['alive']) 
				&& get_module_setting("fav")){
				if (get_module_setting("wo") == 0) $title = "Vital Info";
				if (get_module_setting("wo")) $title = "Personal Info";
				if (get_module_setting("wo") == 2) $title = "Extra Info";
				$amnt = $session['user']['deathpower'];
				setcharstat($title,"Favor",$amnt);
			}
			if (get_module_pref("user_showpart") && get_module_setting("donate")){
				$amnt = $session['user']['donation']-$session['user']['donationspent'];
				setcharstat("Extra Info","`&Donation Points (`#Available`&)`0",number_format($amnt,0,$point,$sep));
			}
			// if (get_module_pref("user_showfull") && get_module_setting("donate")){
			// 	$amnt = $session['user']['donation'];
			// 	setcharstat("Extra Info","`&Donation Points (`#Total`&)`0",$amnt);
			// }
			if (get_module_pref('user_showdrunk') && get_module_setting("drunk") && $session['user']['alive']){
				$len = 0;
				$max = 100;
				$drunk = get_module_pref("drunkeness","drinks");
				if($drunk > $max) $len = $max;
				else $len = $drunk;
				$pct = round($len / $max * 100, 5);
				
				if ($pct > 100) $pct = 100;
				elseif ($pct < 0) $pct = 0;
				
				if ($drunk < 5){
					$level = get_module_setting("sober");
					$barcolor = "progress-drunkeness-sober";
				}elseif ($drunk < 20){
					$level = get_module_setting("level1");
					$barcolor = "progress-drunkeness-lvl1";
				}elseif ($drunk < 40){
					$level = get_module_setting("level2");
					$barcolor = "progress-drunkeness-lvl2";
				}elseif ($drunk < 60){
					$level = get_module_setting("level3");
					$barcolor = "progress-drunkeness-lvl3";
				}elseif ($drunk < 80){
					$level = get_module_setting("level4");
					$barcolor = "progress-drunkeness-lvl4";
				}else{
					$level = get_module_setting("level5");
					$barcolor = "progress-drunkeness-lvl5";
				}
				$drunk = "<div class='drunkeness'>
						  	<div class='progress-drunkeness $barcolor' style='width: $pct%;'>&nbsp;`b$level`b</div>
						  </div>";
				setcharstat("Character Info","Drunkeness",$drunk);
			}
			// if (get_module_pref('user_showpf') && get_module_setting("pf")){
			// 	setcharstat("Extra Info","Player Fights (PVPs)",$session['user']['playerfights']);
			// }	
			break;
		}
	return $args;
}
function displaycp_run(){
}
?>