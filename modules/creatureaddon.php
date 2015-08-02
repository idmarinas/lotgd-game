<?php

/***************************************************************************/
/* Name: Creature Addon                                                    */
/* ver 1.1                                                                 */
/* Billie Kennedy => dannic06@gmail.com                                    */
/*                                                                         */
/***************************************************************************/

function creatureaddon_getmoduleinfo(){
	$info = array(
		"name"=>"Creature Addon",
		"author"=>"Billie Kennedy",
		"version"=>"1.1",
		"category"=>"General",
		"download"=>"http://www.orpgs.com/modules.php?name=Downloads&d_op=viewdownload&cid=6",
		"vertxtloc"=>"http://www.orpgs.com/downloads/",
		"prefs-creatures"=>array(
			"Creature Addons,title",
			"addhit"=>"How many hit points do you want to add?,int|0",
			"addattack"=>"How much additional attack do you wish to add?,int|0",
			"adddefense"=>"How much additional defense do you wish to add?,int|0",
			"addgold"=>"How much additional gold for this creature?,int|0",
			"addexperience"=>"How much additional experience for this creature?,int|0",
			"gemchance"=>"What are the chances of finding an extra gem with this creature?,range,0,100,1|0",
			"gemmessage"=>"What is the message shown when the user finds a gem for this creature?,text",
			"description"=>"Give the creature a full description.,textarea",
			"image"=>"What is the image name?,text",
			"The images should be placed in the images directory of your root.  Use the entire file name: ie 'creature.jpg',note",
		),
	);
	return $info;
}

function creatureaddon_install(){
	module_addhook("creatureencounter");
	module_addhook("battle-victory");
	module_addhook("gravefight-start");
	return true;
}

function creatureaddon_uninstall(){

	return true;
}

function creatureaddon_dohook($hookname,$args){
	global $session;

	switch($hookname){
		case "gravefight-start":
			if (get_module_objpref("creatures",  $args['creatureid'], "description")){
				output (stripslashes(get_module_objpref("creatures",  $args['creatureid'], "description")));
			}
		break;
		case "creatureencounter":
			$args['creaturegold'] += get_module_objpref("creatures",  $args['creatureid'], "addgold");
			$args['creaturehealth'] += get_module_objpref("creatures",  $args['creatureid'], "addhit");
			$args['creatureattack'] += get_module_objpref("creatures",  $args['creatureid'], "addattack");
			$args['creaturedefense'] += get_module_objpref("creatures",  $args['creatureid'], "adddefense");
			//Para añadir más experiencia debido a la dificultad
			$args['creatureexp'] += get_module_objpref("creatures",  $args['creatureid'], "addexperience");

			if (get_module_objpref("creatures",  $args['creatureid'], "image")){
				rawoutput("<table width = \"100%\"><tr><td width=\"100%\" align = \"center\"><img src=\"./images/".get_module_objpref("creatures",  $args['creatureid'], "image")."\"></td></tr></table>");
			}
			if (get_module_objpref("creatures",  $args['creatureid'], "description") && !httpget("nodesc")){
				output (stripslashes(get_module_objpref("creatures",  $args['creatureid'], "description")));
			}

		break;

		case "battle-victory":

			if ($session['user']['level'] < 15 && e_rand(1,100) <= get_module_objpref("creatures", $args['creatureid'], "gemchance") && get_module_objpref("creatures",  $args['creatureid'], "gemmessage")) {
				$message = get_module_objpref("creatures",  $args['creatureid'], "gemmessage");
				output($message);
				debug("Creature Addon module is awarding a gem.");
				$session['user']['gems']++;
				debuglog("found a gem when slaying a ".$args['creaturename']);
			}

        break;
    }
	return $args;
}

function creatureaddon_run(){
	global $session, $args;

	$op=httpget('op');
	$header = $args['creaturename']." Description";
	popup_header($header);

	$description = get_module_objpref("creatures",  $args['creatureid'], "description");
	output($description);
	popup_footer();

	}
?>