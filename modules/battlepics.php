<?php
/************************************************************************
*                                                                       *
*	Author: Melisande                     				*
*	Email:  geekwraith@gmail.com                                    *
*	9/01/2004                                                       *
*	This code may be used freely, but please include this           *
*	comment block.  I hope you enjoy it.                            *
*                                                                       *
*************************************************************************/
/*
Modified to 1.1 22/10/2005 by Dorian and eph
- changed defunct $HTTP_SERVER_VARS to $_SERVER, now graveyard and pvp work correctly
- removed the upload stuff, just add your pics per SSH/FTP. No core editing necessary anymore. (It never worked on my server anyway.)
- changed output to rawoutput to stop the pics from appearing in the translator tool
- aligned the pics to the left
- changed category from forest to general
- added download link

how to use: just create the directory "battlepics" in the main lotgd folder and put your pics in.
Name monsters: [level]_[monstername].[ending], e.g. 2_scarecrow.jpg
Name masters: [level]_[mastername].[ending], e.g. 1_mireraband.jpg
Name graveyard monsters: grave_[monstername].[ending], e.g. grave_rat.png
Name Green Dragon: 18_thegreendragon0.[ending]
Name Special Events monsters: special_[monstername].[ending], e.g. special_penguinoverlord.png

level = 1, 2, 12 etc
monstername = the creature's name in lowercase and without spaces
ending = pics can either be jpg, png or gif
Special Events monsters can be anything called by an event with the badguy command, e.g. the tatoo monster (core), penguin overlord (Chris Vorndran) etc
*/

require_once("lib/http.php");

global $bp_id, $bp_creature, $bp_level;

/*===================  module API functions ===================*/

function battlepics_install(){
	
	module_addhook("battle"); 
	
	set_module_setting("allowforestpics",true);
	set_module_setting("allowpvppics",false);
	set_module_setting("allowtrainpics",true);
	set_module_setting("allowdragonpics",true);
	set_module_setting("allowgravepics",true);
	set_module_setting("defaultbattlepic","forest.jpg");
	set_module_setting("defaultgravepic","graveyard.jpg");
	set_module_setting("defaultmasterpic","master.jpg");
	set_module_setting("defaultpvppic","pvp.jpg");
	set_module_setting("dragonpic","18_thegreendragon0.jpg");
	set_module_setting("battlepicdir","./images/battlepics/");
	set_module_setting("defaultuserprefs",true);
	
	return true;
}

function battlepics_getmoduleinfo(){
	$info = array(
		"name"=>"Battlepics",
		"author"=>"Melisande, mod. by Dorian and eph",
		"version"=>"1.1",
		"category"=>"General",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=525",
		"settings"=>array(
    	"Image Display,title",
    	"allowforestpics"=>"Display images of creatures during forest fights,bool|true",
    	"allowgravepics"=>"Display images when tormenting in the graveyard,bool",
    	"allowtrainpics"=>"Display images when fighting master,bool",
    	"allowpvppics"=>"Display images during pvp fights,bool",
    	"allowdragonpics"=>"Display image when fighting the Green Dragon,bool",
    	"defaultbattlepic"=>"Name of image to use when none exists for a live creature,255 chars",
    	"defaultgravepic"=>"Name of image to use when none exists for a graveyard creature,255 chars",
			"defaultmasterpic"=>"Name of image to use when none exists for a master,255 chars",
			"defaultpvppic"=>"Name of image to use in pvp battles,255 chars",
			"dragonpic"=>"Name of image to use when fighting the Green Dragon,255 chars",
     	"battlepicdir"=>"Directory of battle images,viewonly",
		),
		"prefs"=>array(
			"Image Display Preferences,title",
			"user_display"=>"Show pictures of opponents during fights,bool|1",
		)
	);
	return $info;
}


function battlepics_uninstall(){
	output("Uninstalling this module.`n");
	return true;
}

function battlepics_dohook($hookname, $args){
	switch($hookname){
		case "battle":
			battlepics_main($args, $hookname); //$args value: $badguy array from battle.php
			break;
			}
	return $args;
}


/*==================== core battlepics code ====================*/

/*create filename based on creature's name and level*/
function battlepics_prepname($bp_creature,$bp_level,$bp_picdir,$bp_type='xxx') {
	$bp_mod = strtolower(preg_replace("/[\r\n\s\W]/","",$bp_creature));
	$bp_mod = "{$bp_picdir}{$bp_level}_{$bp_mod}.$bp_type";

	/*because you just know someone's going to use the . in the allow list,
	* or even worse use it sometimes but not always*/
	$bp_mod = preg_replace("/\.\./","\.",$bp_mod);

	return $bp_mod;
}

/*$bp_pagetype values: default, none*/
function battlepics_display($bp_showpic,$bp_pagetype,$hookname = null) {
	global $bp_creature, $bp_id, $bp_level;
	if ($bp_pagetype != 'none') {
		rawoutput("<img class='battlepics' src='$bp_showpic'>"); //eph: added align
	}

	return true;
}

function battlepics_main($args, $hookname = null, $id = null){
	global $_SERVER;
	global $bp_id, $bp_creature, $bp_level;
	
	$bp_self = $_SERVER['PHP_SELF']; 
	$bp_id = httpget(id);
	
	if ($hookname == "battle") $bp_badguy = $args; 
	
  /*find which types of fights pic display is disabled for*/
  $bp_forest = get_module_setting("allowforestpics");
  $bp_pvp = get_module_setting("allowpvppics");
  $bp_train = get_module_setting("allowtrainpics");
  $bp_grave = get_module_setting("allowgravepics");
  $bp_dragon = get_module_setting("allowdragonpics");
  
  /*it'd be nice to make this list configurable so pages can be added to the possibilities through admin interface*/
  $bp_enabledfights = array('forest'=>$bp_forest,'pvp'=>$bp_pvp,'train'=>$bp_train,'graveyard'=>$bp_grave,'dragon'=>$bp_dragon);
  
  /*build string of disabled fight types for preg_match pattern*/
  $bp_delimiter = '/';
  $bp_ext = '\.php';
  
  foreach ($bp_enabledfights as $location => $enabled) {
  	if (!$bp_enabledfights[$location]) {
  		if ($bp_pattern) {
  			$bp_branch = '|';
  		}
  		$bp_pattern = $bp_pattern.$bp_branch.$location.$bp_ext;
  		unset($bp_branch);
  	}
  }

  /*see if battle.php was called from a page for a disabled fight type*/
  if ($bp_pattern) {
  	$bp_pattern = $bp_delimiter.$bp_pattern.$bp_delimiter;
  	preg_match($bp_pattern,$bp_self,$matches);
  }

  /*only execute if ((not a disabled type of fight) && (battle pics are enabled in user preferences))
  */

  if ((!$matches[0]) && (get_module_pref("user_display"))) {
 	$bp_creature = $bp_badguy[creaturename];
	if (preg_match("/graveyard\.php/",$bp_self)){
		$bp_level = "grave";
	} else {
	 	$bp_level = $bp_badguy[creaturelevel];
	}
  	
    $bp_picdir = get_module_setting('battlepicdir');
  	// Build array for pictypes
	$bp_allowedtypes = array("jpg","png","gif");
  	/*if an image exists for the creature, display it and exit loop*/
  	foreach ($bp_allowedtypes as $bp_type) {
  		$bp_showpic = battlepics_prepname($bp_creature,$bp_level,$bp_picdir,$bp_type);
  		$bp_showpic2 = battlepics_prepname($bp_creature,"special",$bp_picdir,$bp_type);
		//rawoutput("Bildname ist: " . $bp_showpic . " oder " . $bp_showpic2);
 		if ((file_exists($bp_showpic)) && (!httppost('bp_savepics')) ) {
  			 battlepics_display($bp_showpic,$bp_pagetype, $hookname);
  			 $matched = 1;
  			 break;
		}
 		if ((file_exists($bp_showpic2)) && (!httppost('bp_savepics')) ) {
			 $bp_showpic=$bp_showpic2;
  			 battlepics_display($bp_showpic,$bp_pagetype, $hookname);
  			 $matched = 1;
  			 break;

  		}
  	}
  	
  	
  	/* make another recursive preg_match check here, so any number of pages
		*	can be checked for and appropriate default assigned, such as for special events, etc.
  	* $bp_{$fighttype}pic; <-- dragonpic, masterpic, pvppic, villagepic, etc.
  	* and yeah, there's a comment on this earlier too :oP
  	*/
  	
  	
    /*if no image file exists for the foe, display the appropriate default*/

		/*this is really icky... instead, get the filename itself and do it that way*/
    if (!$matched) {
  		if (preg_match("/graveyard\.php/",$bp_self)) {
  			$bp_showpic = $bp_picdir . get_module_setting('defaultgravepic');
  			$bp_pagetype = 'default';
  		}
			elseif (preg_match("/train\.php/",$bp_self)) {
  			$bp_showpic = $bp_picdir . get_module_setting('defaultmasterpic');
  			$bp_pagetype = 'default';
  		}
			elseif (preg_match("/pvp\.php/",$bp_self)) {
  			$bp_showpic = $bp_picdir . get_module_setting('defaultpvppic');
  			$bp_pagetype = 'default';
  		}
    	else {
  			$bp_showpic = $bp_picdir . get_module_setting('defaultbattlepic');
  			$bp_pagetype = 'default';
  		}
  			battlepics_display($bp_showpic,$bp_pagetype, $hookname);
    }
  }
	return true;
}

?>
