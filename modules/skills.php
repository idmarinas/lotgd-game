<?php

function skills_getmoduleinfo(){
	$info = array(
		"name"=>"Skills",
		"version"=>"1.03",
		"author"=>"Aelia, with XChrisX",
		"category"=>"General",
		"download"=>"http://dragonprime.net",
        "prefs" => array(
            "Skills User Prefs,title",
            "active"=>"Skills are active?,bool|0",
			"counter"=>"Last seen value of the counter,int|0",
			"cooldown"=>"Cooldown for skills,int|0",
        ),
	);
	return $info;
}
function skills_install(){
	require_once("modules/skills/skills_install.php");
	return true;
}

function skills_uninstall(){
	require_once("modules/skills/skills_uninstall.php");
}

function skills_dohook($hookname,$args){
	global $session;
	$spec = "AW-SK";
	switch ($hookname) {
		case "fightnav-specialties":
			if (has_buff("skillsrounds")) {
				$rounds = get_module_pref("counter")- $session['bufflist']['skillsrounds']['rounds'];
				if ($rounds > 0) {
					$cooldown = get_module_pref("cooldown");
					if ($cooldown > 0) {
						$cooldown-=$rounds;
						if ($cooldown < 0)
							$cooldown = 0;		
						set_module_pref("cooldown",$cooldown);
					}
					set_module_pref("counter",$session['bufflist']['skillsrounds']['rounds']);
				}
			}

			require_once("modules/skills/skills_navs.php");
		break;
		
		case "apply-specialties":
			$skill = httpget('skill');
			if ($skill==$spec)
				require_once("modules/skills/skills_activate.php");
		break;
		
		case "newday":
			$buff = array(
			  "name"=>"Misc Skills",
			  "atkmod"=>1,
			  "rounds"=>100000);	
			apply_buff("skillsrounds", $buff);
			set_module_pref("counter",100000);
			set_module_pref("cooldown",0);
		break;
		
		case "superuser":
			if ($session['user']['superuser'] & SU_EDIT_USERS) {
				addnav("Editors");
				addnav("Skills Editor", "runmodule.php?module=skills&op=editor");
			}
		break;
	}
	return $args;
}

function skills_run(){
	$op=httpget('op');
	require_once("modules/skills/skills_editor.php");
}

?>