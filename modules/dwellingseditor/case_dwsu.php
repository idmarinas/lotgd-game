<?php
	addnav("Module Prefs");
	module_editor_navs("prefs-dwellings","runmodule.php?module=dwellingseditor&op=dweditmodule&dwid=$dwid&mdule=");
    $sql = "SELECT * FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
    $result = db_query($sql);
    $row = db_fetch_assoc($result);
    output("`c`b%s`b`n`n%s`c`n`n",$row['name'],$row['description']);
	require_once("lib/commentary.php");
	addcommentary();
	commentdisplay("", "dwellings-$dwid","Speak into this dwelling",25,"echoes through the walls");
?>