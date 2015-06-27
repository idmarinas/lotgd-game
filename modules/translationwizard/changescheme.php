<?php

if (httppost('save'))
	{
	set_module_pref("language", httppost("language"),"translationwizard");
	//coding set by the lotgd core now with 1.1.0
	//set_module_pref("coding", httppost("coding"),"translationwizard");
	output("Language set to `^%s`0.",httppost("language"));
	//output("Coding set to `^%s`0.",httppost("coding"));
	} else
	{
	$settings= array(
		"Scheme Settings for the Wizard,title",
		"language"=>"What schema do you want to translate for?,enum,".getsetting("serverlanguages","en,English,de,Deutsch,fr,Français,dk,Danish,es,Español,it,Italian"),
		"Server supported languages only (view your game settings or before 1.1.1 prefs.php or configuration.php),note"
	);
	$lang=get_module_pref("language");
	if ($lang=="") $set=array("language"=>"en");
		else
		$set=array("language"=>$lang);//,"coding"=>$code);
	require_once("lib/showform.php");
	rawoutput("<form action='runmodule.php?module=translationwizard&op=changescheme' method='POST'>");
	//output("Note: If you change your coding table, your php version must support the version. Else you'll get error messages.");
	output_notl("`n`n");
	$info = showform($settings,$set);
	rawoutput("<input type='hidden' value='1' name='save'>");
	rawoutput("</form>");
	addnav("","runmodule.php?module=translationwizard&op=changescheme");
	}




?>