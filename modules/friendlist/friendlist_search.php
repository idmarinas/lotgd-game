<?php
function friendlist_search() {
	global $session;
	$n = httppost("n");
	rawoutput("<form action='runmodule.php?module=friendlist&op=search' method='POST'>");
	addnav("","runmodule.php?module=friendlist&op=search");
	if ($n!="") {
		$string="%";
		for ($x=0;$x<strlen($n);$x++){
			$string .= substr($n,$x,1)."%";
		}
		$sql = "SELECT name,dragonkills,acctid FROM ".db_prefix("accounts")." WHERE name LIKE '%$string%' AND acctid<>".$session['user']['acctid']." AND locked=0 ORDER BY level,dragonkills";
		$result = db_query($sql);
		if (db_num_rows($result)>0) {
			$ignored = rexplode(get_module_pref('ignored'));
			$friends = rexplode(get_module_pref('friends'));
			$request = rexplode(get_module_pref('request'));
			$iveignored = rexplode(get_module_pref('iveignored'));
			output("`@These users were found:`n");
			rawoutput("<table style='width:60%;text-align:center;' cellpadding='3' cellspacing='0' border='0'>");
			rawoutput("<tr class='trhead'><td>".translate_inline("Name")."</td><td>".translate_inline("Operations")."</td></tr>");
			for ($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
				output_notl($row['name']);
				rawoutput("</td><td>");
				if (in_array($row['acctid'],$ignored)) {
					$info = translate_inline("This user has ignored you.");
					$info .= " [<a href='runmodule.php?module=friendlist&op=ignore&ac=".$row['acctid']."' class='colDkGreen'>".translate_inline("Ignore")."</a>]";
					addnav("","runmodule.php?module=friendlist&op=ignore&ac=".$row['acctid']);
				} elseif (in_array($row['acctid'],$friends)) {
					$info = translate_inline("This user is already in your list.");
				} elseif (in_array($row['acctid'],$request)) {
					$info = translate_inline("This user has already requested to you.");
				} else {
					if (in_array($row['acctid'],$iveignored)) {
						$info = "[<a href='runmodule.php?module=friendlist&op=unignore&ac=".$row['acctid']."' class='colLtRed'>".translate_inline("Unignore")."</a>]";
						addnav("","runmodule.php?module=friendlist&op=unignore&ac=".$row['acctid']);
					} else {
						$info = "[<a href='runmodule.php?module=friendlist&op=ignore&ac=".$row['acctid']."' class='colDkGreen'>".translate_inline("Ignore")."</a>]";
						addnav("","runmodule.php?module=friendlist&op=ignore&ac=".$row['acctid']);
						$info .= " - [<a href='runmodule.php?module=friendlist&op=request&ac=".$row['acctid']."' class='colDkGreen'>".translate_inline("Request")."</a>]";
						addnav("","runmodule.php?module=friendlist&op=request&ac=".$row['acctid']);
					}
				}
				rawoutput("$info</td></tr>");
			}
			rawoutput("</table>");
		} else {
			output("`c`@`bA user was not found with that name.`b`c");
		}
		output_notl("`n");
	}
	output("`^`b`cFriend Search...`c`b");
	output("`n`nWho do you want to search for?");
	output("`n`nName of user: ");
	rawoutput("<input name='n' maxlength='50' value=\"".htmlentities(stripslashes(httppost('n')))."\">");
	$apply = translate_inline("Search");
	rawoutput("<input type='submit' class='button' value='$apply'></form>");
}

?>