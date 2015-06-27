<?php

function coffer_ban_getmoduleinfo(){
	$info = array(
		"name"=>"Dwellings Coffer Ban",
		"author"=>"Chris Vorndran",
		"version"=>"1.0",
		"category"=>"Dwellings",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1120",
		"prefs-dwellings"=>array(
			"Coffer Ban - Dwelling Prefs,title",
			"banlist"=>"Those that have been banned in this particular dwelling,viewonly|",
		),
		"prefs-dwellingtypes"=>array(
			"Coffer Ban - Type Settings,title",
				"allow-ban"=>"Does this dwelling type allow coffer banning?,bool|1",
		),
	);
	return $info;
}
function coffer_ban_install(){
	module_addhook("dwellings-sold");
	module_addhook("dwellings-inside");
	module_addhook("dwellings-manage");
	return true;
}
function coffer_ban_uninstall(){
	return true;
}
function coffer_ban_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "dwellings-sold":
			$banlist = array();
			set_module_objpref("dwellings",$args['dwid'],"banlist",serialize($banlist),"coffer_ban");
			break;
		case "dwellings-inside":
			$banlist = unserialize(get_module_objpref("dwellings",$args['dwid'],"banlist","coffer_ban"));
			if (!is_array($banlist)) {
				$banlist = array();
				set_module_objpref("dwellings",$args['dwid'],"banlist",serialize($banlist),"coffer_ban");
			}
			if (in_array($session['user']['acctid'],$banlist)) 
				blocknav("runmodule.php?module=dwellings&op=coffers",true);
			break;
		case "dwellings-manage":
			$typeid = get_module_setting("typeid",$args['type']);
			if (get_module_objpref("dwellingtypes",$typeid,"allow-ban","coffer_ban"))
				addnav("Coffer Ban","runmodule.php?module=coffer_ban&dwid=".$args['dwid']);
			break;
	}
	return $args;
}
function coffer_ban_run(){
	global $session;
	$dwid = httpget('dwid');
	$id = httpget('id');
	$op = httpget('op');
	$banlist = unserialize(get_module_objpref("dwellings",$dwid,"banlist","coffer_ban"));
	if (!is_array($banlist)) {
		$banlist = array();
		set_module_objpref("dwellings",$dwid,"banlist",serialize($banlist),"coffer_ban");
	}
	page_header("Coffer Bans");
	
	switch($op){
		case "ban":
			if(httppost('submit')){
				$banlist[$id] = $id;
				output("User has been banned from using your coffers.`n`n");
				$subj = sprintf_translate("%s has issued a coffer ban!",$session['user']['name']);
				require_once("lib/systemmail.php");
				require_once("lib/nltoappon.php");
				systemmail($id,$subj,nltoappon(stripslashes(httppost('message'))));
				set_module_objpref("dwellings",$dwid,"banlist",serialize($banlist),"coffer_ban");
			}else{
				output("Please provide a reason for the banning.");
				output("This reason shall be mailed to the user, so they can better ascertain why they lost their coffer access.`n`n");
				rawoutput("<form action='runmodule.php?module=coffer_ban&op=ban&id=$id&dwid=$dwid' method='post'>");
				rawoutput("<textarea name='message' cols='60' rows='10'></textarea><br/>");
				rawoutput("<input type='submit' name='submit' class='button' value='".translate_inline("Submit")."'></form>");
			}
			addnav("","runmodule.php?module=coffer_ban&op=ban&id=$id&dwid=$dwid");
			break;
		case "remove":
			unset($banlist[$id]);
			output("User has been removed from the ban list.`n`n");
			set_module_objpref("dwellings",$dwid,"banlist",serialize($banlist),"coffer_ban");
			break;
	}

	$sql = "SELECT name,keyowner,keyid FROM ".db_prefix("dwellingkeys")."
			INNER JOIN ".db_prefix("accounts")." ON keyowner=acctid
			WHERE dwid = $dwid 
			AND keyowner != ".$session['user']['acctid']." 
			ORDER BY keyid ASC";
	$res = db_query($sql);
	$num = translate_inline("Num");
	$owner = translate_inline("Owner");
	$ops = translate_inline("Ops");
	$ban = translate_inline("Ban");
	$remove = translate_inline("Remove");
	rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'>");
	rawoutput("<tr class='trhead'><td>$num</td><td align=center>$owner</td><td align=center>$ops</td></tr>"); 
	$i = 0;
	debug($banlist);
	while($row = db_fetch_assoc($res)){
		$keyid = $row['keyowner'];
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
		output_notl($i+1);
		rawoutput("</td><td>");
		output_notl($row['name']);
		rawoutput("</td><td>");
		if (!in_array($keyid,$banlist)){
			rawoutput("<a href='runmodule.php?module=coffer_ban&op=ban&id=$keyid&dwid=$dwid'>$ban</a>");
			addnav("","runmodule.php?module=coffer_ban&op=ban&id=$keyid&dwid=$dwid");
		}else{
			rawoutput("<a href='runmodule.php?module=coffer_ban&op=remove&id=$keyid&dwid=$dwid'>$remove</a>");
			addnav("","runmodule.php?module=coffer_ban&op=remove&id=$keyid&dwid=$dwid");
		}
		rawoutput("</td></tr>");
		$i++;
	}
	rawoutput("</table>");
	addnav("Return to Management","runmodule.php?module=dwellings&op=manage&dwid=$dwid");
	page_footer();
}
?>