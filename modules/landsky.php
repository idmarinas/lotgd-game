<?php
//home display settings&&user pref by Lonny Luberts
function landsky_getmoduleinfo(){
	$info = array(
		"name"=>"The Sky",
		"version"=>"1.5",
		"author"=>"`@CortalUX`&, with modifications by `#Lonnyl",
		"category"=>"General",
		"vertxtloc"=>"http://dragonprime.net/users/CortalUX/",
		"download"=>"http://dragonprime.net/users/CortalUX/landsky.zip",
		"settings"=>array(
			"The Sky - General,title",
			"moonBlock"=>"Should the Moons module be blocked during the day?,bool|1",
			"showhome"=>"Show the Sky on Home Page,enum,0,No,1,Above Login,2,Below Login|1",
		),
		// "prefs"=>array(
		// 	"The Sky - General,title",
		// 	"user_showsky"=>"View the Sky in the Village,bool|1",
		// ),
	);
	return $info;
}

function landsky_install(){
	if (!is_module_active('landsky')) {
		output("`n`Q`b`cInstalling the Land Sky Module.`c`b`n");
	}else{
		output("`n`Q`b`cUpdating the Land Sky Module.`c`b`n");
	}
	module_addhook("everyhit");
	module_addhook("village-desc");
	module_addhook("forest-desc");
	module_addhook("journey-desc");
	module_addhook("footer-home");
	module_addhook("index");
	module_addhook("shades");
	module_addhook("graveyard-desc");
	return true;
}

function landsky_uninstall(){
	output("`n`Q`b`cUninstalling the Land Sky Module.`c`b`n");
	return true;
}

function landsky_dohook($hookname,$args){
	global $session;
	if ($hookname=='everyhit') {
		if (get_module_setting('moonBlock')==1&&landsky_word()!=0&&landsky_word()!=1&&landsky_word()!=4) {
			blockmodule('moons');
		}
		return $args;
	}
	$r=array("racedwarf","racespecialtyreptile");
	if ($hookname == "village-desc") {
		foreach ($r as $mod) {
			if (is_module_active($mod)) {
				if ($session['user']['location']==get_module_setting('villagename', $mod)) {
					return $args;
				}
			}
		}
	}
	if (file_exists('images/landsky/sky.png')) {
		$showhome = get_module_setting("showhome");
		if ($showhome == 0&&($hookname == "footer-home" or $hookname == "index")) return $args;
		if ($showhome == 1&&$hookname == "footer-home") return $args;
		if ($showhome == 2&&$hookname == "index") return $args;
		// if ($hookname == "village-desc"&&get_module_pref("user_showsky") == 0) return $args;
		$array=landsky_calc();
		if (landsky_c()) {
			$status=array(0=>"Night",1=>"Morning",2=>"Noon",3=>"Afternoon",4=>"Evening");
		} else {
			if ($hookname=='graveyard-desc') rawoutput('<br>');
			$status=array(0=>"Night of the Soul",1=>"Morn' of Death",2=>"Torment",3=>"Eternal Pain",4=>"Rebirth");
		}
		rawoutput("<br><table style='width:auto; margin:auto;'><tr><td style='padding:0;'>");
		rawoutput("<table style='width:auto; border-spacing: 2px; border-collapse: separate; border:1px solid black;'><tr><td style='padding:0;'>");
		rawoutput("<div name='landskyInner' style='background-image: url(./images/landsky/".landsky_image()."_bg.png);background-repeat: repeat;border:0;clip:auto;clear:both;width:50px;height:50px;overflow:hidden;'>");
		rawoutput("<img src='./images/landsky/".landsky_image().".png' alt='The sky..' style='width:".$array['width']."px;height:".$array['height']."px;border:0;position:relative;top:0px;left:-".$array['offset']."px;'>");
		rawoutput("</div>");		
		rawoutput("</td></tr></table>");		
		rawoutput("</td><td style='vertical-align:middle;'>");
		$num=landsky_word();
		
		if (get_module_setting('moonBlock')==1&&landsky_word()!=0&&landsky_word()!=1&&landsky_word()!=4&&is_module_active('moons')&&$hookname!='footer-home'&&$hookname!='shades'&&$hookname!='graveyard-desc') {
			$num=0;
			if (get_module_setting('moon1','moons')==1) $num++;
			if (get_module_setting('moon2','moons')==1) $num++;
			if (get_module_setting('moon3','moons')==1) $num++;
			if (1 == $num) {
				output("It is too bright to make out the moon.");
			} else{
				output("It is too bright to make out the moons.");				
			}
		}
		modulehook("landsky-moons",$args);
		rawoutput("</td></tr></table>");
		if (landsky_c()) {
			if ($hookname == "index"&&$showhome == 1) output_notl("`c");
			output_notl("`^`c`b%s`b`c",translate_inline($status[$num]));
			if ($hookname == "index"&&$showhome == 1) output_notl("`c");
		} else {
			output_notl("`3`c`b%s`b`c",translate_inline($status[$num]));
		}
		
	}
	return $args;
}

function landsky_run(){
}

function landsky_calc() {
	if (function_exists('getimagesize')&&file_exists('images/landsky/sky.png')) {
		$size = getimagesize("images/landsky/sky.png");
		$width = $size[0];
		$height = $size[1];
	} else {
		$width = 800;
		$height = 50;
	}
	$width+=50;
	require_once('lib/datetime.php');
	$time=gametimedetails();
	$bit=$width/86400;
	$pix=$bit*$time['secssofartoday'];
	$pix=round($pix);
	$array=array('height'=>$height,'width'=>$width,'offset'=>$pix);
	return $array;
}

function landsky_word() {
	require_once('lib/datetime.php');
	$num=0;
	$if = date("G",gametime());
	if ($if<4||$if>=19) {
		$num=0;
	} elseif ($if>=4&&$if<12) {
		$num=1;
	} elseif ($if==12) {
		$num=2;
	} elseif ($if>12&&$if<=17) {
		$num=3;
	} elseif ($if>15&&$if<19) {
		$num=4;
	}
	return $num;
}

function landsky_image() {
	global $session;
	if (landsky_c()) {
		$key="sky";
	} else {
		$key="deadsky";
	}
	return $key;
}

function landsky_c() {
	global $session;
	if ($session['user']['alive']||!$session['user']['loggedin']) return true;
	return false;
}
?>