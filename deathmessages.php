<?php
// addnews ready
// mail ready
// translator ready
require_once("common.php");
require_once("lib/http.php");

tlschema("deathmessage");

check_su_access(SU_EDIT_CREATURES);

page_header("Deathmessage Editor");
require_once("lib/superusernav.php");
superusernav();
$op = httpget('op');
$deathmessageid = httpget('deathmessageid');
switch ($op) {
case "edit":
	addnav("Deathmessages");
	addnav("Return to the Deathmessage editor","deathmessages.php");
	rawoutput("<form action='deathmessages.php?op=save&deathmessageid=$deathmessageid' method='POST'>",true);
	addnav("","deathmessages.php?op=save&deathmessageid=$deathmessageid");
	if ($deathmessageid!=""){
		$sql = "SELECT * FROM " . db_prefix("deathmessages") . " WHERE deathmessageid=\"$deathmessageid\"";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		require_once("lib/substitute.php");
		$badguy=array('creaturename'=>'`2The Nasty Rabbit', 'creatureweapon'=>'Rabbit Ears');
		$deathmessage = substitute_array($row['deathmessage'],array("{where}"),array("in the fields"));
		$deathmessage = call_user_func_array("sprintf_translate", $deathmessage);
		output("Preview: %s`0`n`n", $deathmessage);
	} else {
		$row = array('deathmessageid'=>0, 'deathmessage'=>"");
	}
	output("The following codes are supported (case matters):`n");
	output("{goodguyname}	= The player's name (also can be specified as {goodguy}`n");
	output("{goodguyweapon}	= The player's weapon (also can be specified as {weapon}`n");
	output("{armorname}	= The player's armor (also can be specified as {armor}`n");
	output("{himher}	= Subjective pronoun for the player (him her)`n");
	output("{hisher}	= Possessive pronoun for the player (his her)`n");
	output("{heshe}		= Objective pronoun for the player (he she)`n");
	output("{badguyname}	= The monster's name (also can be specified as {badguy}`n");
	output("{badguyweapon}	= The monster's weapon (also can be specified as {creatureweapon}`n");
	output("{where}         = The location like 'in the forest' or 'in the fields' or whatnot`n");
	$save = translate_inline("Save");
	output("`n`n`4Deathmessage: ");
	rawoutput("<input name='deathmessage' value=\"".HTMLEntities($row['deathmessage'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='70'><br>");
	output("Is this a Forest Deathmessage: ");
	rawoutput("<input name='forest' ".((int)$row['forest']?"checked":"")." value='1' type='checkbox'><br>");
	output("Is this a Graveyard Deathmessage: ");
	rawoutput("<input name='graveyard' ".((int)$row['graveyard']?"checked":"")." value='1' type='checkbox'><br>");
	output("Is a Taunt displayed along with it?");
	rawoutput("<input name='taunt' ".((int)$row['taunt']?"checked":"")." value='1' type='checkbox'><br>");
	rawoutput("<input type='submit' class='button' value='$save'>");
	rawoutput("</form>");
	break;
case "del":
	$sql = "DELETE FROM " . db_prefix("deathmessages") . " WHERE deathmessageid=\"$deathmessageid\"";
	db_query($sql);
	$op = "";
	httpset("op", "");
	break;
case "save":
	$deathmessage = httppost('deathmessage');
	$forest = (int) httppost('forest');
	$graveyard = (int) httppost('graveyard');
	$taunt = (int) httppost('taunt');
	if ($deathmessageid!=""){
		$sql = "UPDATE " . db_prefix("deathmessages") . " SET deathmessage=\"$deathmessage\",taunt=$taunt,forest=$forest,graveyard=$graveyard,editor=\"".addslashes($session['user']['login'])."\" WHERE deathmessageid=\"$deathmessageid\"";
	}else{
		$sql = "INSERT INTO " . db_prefix("deathmessages") . " (deathmessage,taunt,forest,graveyard,editor) VALUES (\"$deathmessage\",$taunt,$forest,$graveyard,\"".addslashes($session['user']['login'])."\")";
	}
	db_query($sql);
	$op = "";
	httpset("op", "");
	break;
}
if ($op == "") {
	output("`i`\$Note: These messages are NEWS messages the user will trigger when he/she dies in the forest or graveyard.`0`i`n`n");
	$sql = "SELECT * FROM " . db_prefix("deathmessages");
	$result = db_query($sql);
	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
	$op = translate_inline("Ops");
	$t = translate_inline("Deathmessage String");
	$auth = translate_inline("Author");
	$f = translate_inline("Forest Message");
	$g = translate_inline("Graveyard Message");
	$ta = translate_inline("With Taunt");
	rawoutput("<tr class='trhead'><td nowrap>$op</td><td>$t</td><td>$f</td><td>$g</td><td>$ta</td><td>$auth</td></tr>");
	$i=true;
	while ($row=db_fetch_assoc($result)) {
		$i=!$i;
		rawoutput("<tr class='".($i?"trdark":"trlight")."'>",true);
		rawoutput("<td nowrap>");
		$edit = translate_inline("Edit");
		$del = translate_inline("Del");
		$conf = translate_inline("Are you sure you wish to delete this deathmessage?");
		$id = $row['deathmessageid'];
		rawoutput("[ <a href='deathmessages.php?op=edit&deathmessageid=$id'>$edit</a> | <a href='deathmessages.php?op=del&deathmessageid=$id' onClick='return confirm(\"$conf\");'>$del</a> ]");
		addnav("","deathmessages.php?op=edit&deathmessageid=$id");
		addnav("","deathmessages.php?op=del&deathmessageid=$id");
		rawoutput("</td><td>");
		output_notl("%s", $row['deathmessage']);
		rawoutput("</td><td>");
		output_notl("%s", deathmessages_bool($row['forest']));
		rawoutput("</td><td>");
		output_notl("%s", deathmessages_bool($row['graveyard']));
		rawoutput("</td><td>");
		output_notl("%s", deathmessages_bool($row['taunt']));
		rawoutput("</td><td>");
		output_notl("%s", $row['editor']);
		rawoutput("</td></tr>");
	}
	addnav("","deathmessages.php?c=".httpget('c'));
	rawoutput("</table>");
	addnav("Deathmessages");
	addnav("Add a new deathmessage","deathmessages.php?op=edit");
}
function deathmessages_bool($value) {
	if ($value) {
		return translate_inline("Yes");
	} else {
		return translate_inline("No");
	}
}
page_footer();
?>
