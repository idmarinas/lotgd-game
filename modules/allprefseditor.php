<?php
/**
	Modified by MarcTheSlayer

	05/02/2009 - v1.0b
	Added the 'editid' setting to store the id of the last edited user.
*/
function allprefseditor_getmoduleinfo(){
	$info = array(
		"name"=>"Allprefs Editor",
		"version"=>"1.0b",
		"author"=>"DaveS, modified by `@MarcTheSlayer",
		"category"=>"Administrative",
		"download"=>"",
		"settings"=>array(
			"Allprefs Editor Prefs,title",
			"editid"=>"ID of the last person to be edited.,int|1"
		)
	);
	return $info;
}
function allprefseditor_install(){
	module_addhook("footer-user");
	module_addhook("superuser");
	return true;
}
function allprefseditor_uninstall(){
	return true;
}
function allprefseditor_dohook($hookname,$args){
	global $session;
	$id = httpget('userid');
//
//	Modified by MarcTheSlayer.
//
//	if ($id=="") $id=1;
	if ($id=="") $id = get_module_setting('editid','allprefseditor');
//
//	$op = httpget('op');	
	switch($hookname){
		case "footer-user":
		//
		//	Modified by MarcTheSlayer.
		//
		//	$id = httpget('userid');
		//	if ($id=="") $id=1;
		//
			$op = httpget('op');
			if ($op=="edit"){
				addnav("Operations");
				addnav("Allprefs Editors","runmodule.php?module=allprefseditor&userid=$id");
			}
		break;
		case "superuser":
			if ($session['user']['superuser'] & SU_EDIT_USERS){
				addnav("Editors");
				addnav("Allprefs Editor","runmodule.php?module=allprefseditor&userid=$id");
			}
		break;
	}
	return $args;
}
function allprefseditor_run(){
page_header("Allprefs Editor");
$id = httpget('userid');
addnav("Navigation");
addnav("Return to the Grotto","superuser.php");
villagenav();
addnav("Edit user","user.php?op=edit&userid=$id");
modulehook("allprefs");
allprefseditor_search();
page_footer();
}
function allprefseditor_search(){
	$id = httpget('userid');
//
//	Modified by MarcTheSlayer.
//
//	if ($id=="") $id=1;
	if( $id == '' )
	{
		$id = get_module_setting('editid','allprefseditor');
	}
	else
	{
		set_module_setting('editid',$id,'allprefseditor');
	}
//
//
	$sql1 = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$id'";
	$result1 = db_query($sql1);
	$row1 = db_fetch_assoc($result1);
	output("`^`c`bCurrent User:`b`0 %s`c",$row1['name']);
	$subop1 = httpget('subop1');
	output("`nSearch for Another Player: ");
	$search = translate_inline("Search");
	rawoutput("<form action='runmodule.php?module=allprefseditor&subop1=search&userid=$id' method='POST'><input name='name' id='name'><input type='submit' class='button' value='$search'></form>");
	addnav("","runmodule.php?module=allprefseditor&subop1=search&userid=$id");
	if ($subop1=="search"){
		$search = "%";
		$name = httppost('name');
		for ($i=0;$i<strlen($name);$i++){
			$search.=substr($name,$i,1)."%";
		}
		$sql = "SELECT acctid,name,level,login FROM " . db_prefix("accounts") . " WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
		$result = db_query($sql);
		$max = db_num_rows($result);
		if ($max > 100) {
			output("^Listing first 100:");
			$max = 100;
		}
		$o = translate_inline("Op");
		$n = translate_inline("Name");
		$l = translate_inline("Login");
		$a = translate_inline("AcctID");
		$le = translate_inline("Level");
		rawoutput("<table align=center> <tr class='trhead'><td>$o</td><td>$n</td><td>$l</td><td>$a</td><td>$le</td></tr>");
		for ($i=0;$i<$max;$i++){
			$n=$n+1;
			$row = db_fetch_assoc($result);
			$playerid=$row['acctid'];
			rawoutput("<tr class='".($n%2?"trdark":"trlight")."'><td><a href='runmodule.php?module=allprefseditor&userid=$playerid'>");
			output_notl("[ Edit ]");
			rawoutput("</a></td><td>");
			output_notl("`&%s", $row['name']);
			rawoutput("</td><td>");
			output_notl("%s", $row['login']);
			rawoutput("</td><td align=center>");
			output_notl("%s", $row['acctid']);
			rawoutput("</td><td align=center>");
			output_notl("`^%s", $row['level']);
			rawoutput("</td></tr>");
			addnav("","runmodule.php?module=allprefseditor&userid=$playerid");
		}
		rawoutput("</table>");
	}
}
?>