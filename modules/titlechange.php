<?php

function titlechange_getmoduleinfo(){
	$info = array(
		"name"=>"Title Change",
		"author"=>"JT Traub, modified by `2Oliver Brendel",
		"version"=>"1.0",
		"download"=>"",
		"category"=>"Lodge",
		"settings"=>array(
			"Title Change Module Settings,title",
			"initialpoints"=>"How many donator points needed to get first title change?,int|5000",
			"extrapoints"=>"How many additional donator points needed for subsequent title changes?,int|0",
			"bold"=>"Allow bold?,bool|1",
			"italics"=>"Allow italics?,bool|1",
			"blank"=>"Allow blank titles?,bool|1",
			"length"=>"Allow how many chars (remember:UTF-8 take up more bytes! And mind the table hard limit) can a title have?,int|25",
		),
		"prefs"=>array(
			"Title Change User Preferences,title",
			"timespurchased"=>"How many title changes have been bought?,int|0",
		),
	);
	return $info;
}

function titlechange_install(){
	module_addhook("lodge");
	module_addhook("pointsdesc");
	return true;
}
function titlechange_uninstall(){
	return true;
}

function titlechange_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "pointsdesc":
		$args['count']++;
		$format = $args['format'];
		$str = translate("The ability to choose a custom in-game title upon reaching %s and every %s points thereafter. (this doesn't use up those points) [NOTE: A title is the prefix on your name like Farmboy or Page]");
		$str = sprintf($str, get_module_setting("initialpoints"),
				get_module_setting("extrapoints"));
		output($format, $str, true);
		break;
	case "lodge":
		// If they have less than what they need just ignore them
		$times = get_module_pref("timespurchased");
		if ($session['user']['ctitle']!='') addnav("Reset your Custom Title (free)","runmodule.php?module=titlechange&op=titlereset");
		if (get_module_setting("initialpoints") + ($times * get_module_setting("extrapoints")) > $session['user']['donation'])
			break;
		addnav("Set Custom Title (free)","runmodule.php?module=titlechange&op=titlechange");
		break;
	}
	return $args;
}

function titlechange_run(){
	require_once("lib/sanitize.php");
	require_once("lib/names.php");
	global $session;
	$op = httpget("op");

	page_header("Hunter's Lodge");
	switch ($op) {
		case "titlechange":
			output("`3`bCustomize Title`b`0`n`n");
			output("`7Because you have earned sufficient points, you have been granted the ability to set a custom title of your choosing.");
			output("The title must be appropriate, and the admin of the game can reset if it isn't (as well as penalize you for abusing the game).");
			output("The title may not be more than 25 characters long including any characters used for colorization!.`n`n");
			$otitle = get_player_title();
			if ($otitle=="`0") $otitle="";
			output("`7Your title is currently`^ ");
			rawoutput($otitle);
			output_notl("`0`n");
			output("`7which looks like %s`n`n", $otitle);
			if (httpget("err")==1) output("`\$Please enter a title.`n");
			output("`7How would you like your title to look?`n");
			rawoutput("<form action='runmodule.php?module=titlechange&op=titlepreview' method='POST'>");
			$n=htmlentities($otitle, ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
			debug($n);
			rawoutput("<input id='input' name='newname' width='25' maxlength='25' value=\"$n\">");
			rawoutput("<input type='submit' class='button' value='Preview'>");
			rawoutput("</form>");
			addnav("", "runmodule.php?module=titlechange&op=titlepreview");
			break;
		case "titlepreview":
			$ntitle = stripslashes(rawurldecode(httppost('newname')));
			$ntitle=newline_sanitize($ntitle);

			if ($ntitle=="") {
				if (get_module_setting("blank")) {
					$ntitle = "`0";
				}
				else{
					redirect("runmodule.php?module=titlechange&op=titlechange&err=1");
				}
			}
			if (!get_module_setting("bold")) $ntitle = str_replace("`b", "", $ntitle);
			if (!get_module_setting("italics")) {
				$ntitle = str_replace("`i", "", $ntitle);
				$ntitle = str_replace("`B", "", $ntitle);
			}
			$ntitle = preg_replace("/[`][cHw]/", "", $ntitle);
			$ntitle = sanitize_html($ntitle);
			$ntitle = substr($ntitle,0,get_module_setting('length')); //for multibytes entered
			$nname = get_player_basename();
			output("`7Your new title will look like this: %s`0`n", $ntitle);
			output("`7Your entire name will look like: %s %s`0`n`n",
					$ntitle, $nname);
			output("`7Is this how you wish it to look?");
			addnav("`bConfirm Custom Title`b");
			addnav("Yes", "runmodule.php?module=titlechange&op=changetitle&newname=".rawurlencode($ntitle));
			addnav("No", "runmodule.php?module=titlechange&op=titlechange");
			break;
		case "changetitle":
			$ntitle=stripslashes(rawurldecode(httpget('newname')));
			$fromname = $session['user']['name'];
			$newname = change_player_ctitle($ntitle);
			$session['user']['ctitle'] = $ntitle;
			$session['user']['name'] = $newname;
			addnews("%s`^ has become known as %s.",$fromname,$session['user']['name']);
			debuglog("changed the player title from $fromname to ".$session['user']['name']);
			set_module_pref("timespurchased", get_module_pref("timespurchased")+1);
			output("Your custom title has been set.");
			modulehook("namechange", array());
			break;
		case "titlereset":
			output("`3`bCustomize Title`b`0`n`n");
			output("`7You want to reset your title?`n`nBy doing so, you will get the respective title you would have for your current accomplishments (certain kills...) again as if there was never a custom title.");
			output("`n`n`\$The points you have paid for your current title are used, you will not get them back!");
			addnav("Confirm...");
			addnav("Reset my title!","runmodule.php?module=titlechange&op=titlereset_yes");
			addnav("No! I keep it!","lodge.php");
			break;
		case "titlereset_yes":
			output("`7Your title has been reset.");
			$ntitle='';
			$fromname=$session['user']['name'];
			$newname = change_player_ctitle($ntitle);
			$session['user']['ctitle'] = $ntitle;
			$session['user']['name'] = $newname;
			addnews("%s`^ has become known as %s.",$fromname,$session['user']['name']);
			debuglog("reset his/her title from $fromname to ".$session['user']['name']);
			break;
	}
	addnav("Navigation");
	addnav("L?Return to the Lodge","lodge.php");
	page_footer();
}
?>
