<?php
function age_getmoduleinfo(){
	$info = array(
		"name"=>"Age",
		"author"=>"Lurch",
		"version"=>"1.0b",
		"category"=>"Stat Display",
		"download"=>"http://dragonprime.net/users/Lurch/age.zip",
		"prefs"=>array(
			"Age Display Preferences,title",
			"user_showspec"=>"Do you wish for Age to be displayed,bool|1",
			),
		);
	return $info;
}
function age_install(){
	module_addhook("charstats");
	return true;
}
function age_uninstall(){
	return true;
}
function age_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "charstats":
			if (get_module_pref("user_showspec") == 1){
			$title = "Personal Info";
			$name = translate_inline("Age");
			$spec = $session['user']['age']+10;
			setcharstat($title,$name,$spec);
		}
				break;
		}
	return $args;
}
function age_run(){
}
?>