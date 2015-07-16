<?php
function friendlist_list() {
	global $session;
	$friends = rexplode(get_module_pref('friends'));
	$request = rexplode(get_module_pref('request'));
	$ignored = rexplode(get_module_pref('ignored'));
	$iveignored = rexplode(get_module_pref('iveignored'));
	output("`b`@Friends:`b`n");
	rawoutput("<table style='text-align:center;' cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr class='trhead'><td>".translate_inline("Name")."</td><td>".translate_inline("Logged In")."</td><td>".translate_inline("Location")."</td><td>".translate_inline("Alive")."</td><td>".translate_inline("Operations")."</td></tr>");
	$last = date("Y-m-d H:i:s", strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	$x=0;
	if (implode(",",$friends)!='') {
		$sql = "SELECT name,acctid,login,laston,alive,loggedin,location FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(',',$friends).") AND locked=0 ORDER BY login";
		$result = db_query($sql);
		while ($row=db_fetch_assoc($result)) {
				$ac=$row['acctid'];
				$x++;
				rawoutput("<tr class='".($x%2?"trlight":"trdark")."'>");
				rawoutput("<td><a href='mail.php?op=write&to=".rawurlencode($row['login'])."'>".appoencode("`&".$row['name'],false)."</a></td>");
				addnav("","mail.php?op=write&to=".rawurlencode($row['login']));
				$loggedin=$row['loggedin'];
				if ($row['laston']<$last) {
					$loggedin=false;
				}
				$loggedin = translate_inline($loggedin?"`^Yes`0":"`%No`0");
				rawoutput("<td>".appoencode($loggedin,false)."</td>");
				rawoutput("<td><span class='colLtYellow'>".htmlentities($row['location'])."</span></td>");
				$alive = translate_inline($row['alive']?"`@Yes`0":"`\$No`0");
				rawoutput("<td>".appoencode($alive,false)."</td>");
				$ops = "[<a href='runmodule.php?module=friendlist&op=deny&ac=$ac' class='colDkGreen'>".translate_inline("Remove")."</a>] - [<a href='runmodule.php?module=friendlist&op=ignore&ac=$ac' class='colDkGreen'>".translate_inline("Ignore")."</a>]";
				addnav("","runmodule.php?module=friendlist&op=deny&ac=$ac");
				addnav("","runmodule.php?module=friendlist&op=ignore&ac=$ac");
				rawoutput("<td>$ops</td></tr>");
		}
	}
	if ($x==0) {
		rawoutput("<tr class='trlight'><td colspan='5'>");
		output("`^You have no friends");
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	$friends = rimplode( $friends);
	set_module_pref('friends',$friends);
	output("`n`b`@Friend Requests:`b`n");
	rawoutput("<table style='text-align:center;' cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr class='trhead'><td>".translate_inline("Name")."</td><td>".translate_inline("Operations")."</td></tr>");
	$x=0;
	$request=array_unique($request);
	if (implode(",",$request)!='') {
		$sql = "SELECT name,acctid,login,laston,alive,loggedin,location FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(',',$request).") AND locked=0 ORDER BY login";
		$result = db_query($sql);
		while ($row=db_fetch_assoc($result)) {
			$ac=$row['acctid'];
			$x++;
			rawoutput("<tr class='".($x%2?"trlight":"trdark")."'>");
			rawoutput("<td>".appoencode($row['name'],false)."</td>");
			$ops = "[<a href='runmodule.php?module=friendlist&op=accept&ac=$ac' class='colDkGreen'>".translate_inline("Accept")."</a>] - [<a href='runmodule.php?module=friendlist&op=deny&ac=$ac' class='colDkGreen'>".translate_inline("Deny")."</a>] - [<a href='runmodule.php?module=friendlist&op=ignore&ac=$ac' class='colDkGreen'>".translate_inline("Ignore")."</a>]";
			addnav("","runmodule.php?module=friendlist&op=accept&ac=$ac");
			addnav("","runmodule.php?module=friendlist&op=deny&ac=$ac");
			addnav("","runmodule.php?module=friendlist&op=ignore&ac=$ac");
			rawoutput("<td>$ops</td></tr>");
		}
	}
	if ($x==0) {
		rawoutput("<tr class='trlight'><td colspan='2'>");
		output("`^You have no requests");
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	$request = rimplode( $request);
	set_module_pref('request',$request);
	output("`n`b`@Ignored You:`b`n");
	rawoutput("<table style='text-align:center;' cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr class='trhead'><td>".translate_inline("Name")."</td><td>".translate_inline("Operations")."</td></tr>");
	$x=0;
	$ignored=array_unique($ignored);
	if (implode(",",$ignored)!='') {
		$sql = "SELECT name,acctid,login,laston,alive,loggedin,location FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(',',$ignored).") AND locked=0 ORDER BY login";
		$result = db_query($sql);
		while ($row=db_fetch_assoc($result)) {
			$x++;
			$ac=$row['acctid'];
			rawoutput("<tr class='".($x%2?"trlight":"trdark")."'>");
			rawoutput("<td>".appoencode($row['name'],false)."</td>");
			if (!in_array($ac,$iveignored)) {
				$ops = "[<a href='runmodule.php?module=friendlist&op=ignore&ac=$ac' class='colDkGreen'>".translate_inline("Ignore")."</a>]";
				addnav("","runmodule.php?module=friendlist&op=ignore&ac=$ac");
			} else {
				$ops = appoencode("`i[".translate_inline("Nothing")."]`i",false);
			}
			rawoutput("<td>$ops</td></tr>");
		}
	}
	if ($x==0) {
		rawoutput("<tr class='trlight'><td colspan='2'>");
		output("`^No one has ignored you");
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	$ignored = rimplode( $ignored);
	set_module_pref('ignored',$ignored);
	output("`n`b`@You've Ignored:`b`n");
	rawoutput("<table style='text-align:center;' cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr class='trhead'><td>".translate_inline("Name")."</td><td>".translate_inline("Operations")."</td></tr>");
	$x=0;
	$iveignored=array_unique($iveignored);
	if (implode(",",$iveignored)!='') {
	$sql = "SELECT name,acctid,login,laston,alive,loggedin,location FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(',',$iveignored).") AND locked=0 ORDER BY login";
	$result = db_query($sql);
		while ($row=db_fetch_assoc($result)) {
			$x++;
			$ac=$row['acctid'];
			rawoutput("<tr class='".($x%2?"trlight":"trdark")."'>");
			rawoutput("<td>".appoencode($row['name'],false)."</td>");
			$ops = "[<a href='runmodule.php?module=friendlist&op=unignore&ac=$ac' class='colLtRed'>".translate_inline("Unignore")."</a>]";
			addnav("","runmodule.php?module=friendlist&op=unignore&ac=$ac");
			rawoutput("<td>$ops</td></tr>");
		}
	}
	if ($x==0) {
		rawoutput("<tr class='trlight'><td colspan='2'>");
		output("`^You've haven't ignored anyone");
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	$iveignored = rimplode( $iveignored);
	set_module_pref('iveignored',$iveignored);
}
?>
