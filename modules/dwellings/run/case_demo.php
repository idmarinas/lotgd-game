<?php
	$pct = (get_module_setting("demoper","dwellings")/100);
	$sql = "SELECT gold,gems FROM ".db_prefix("dwellings")." WHERE dwid='".httpget('dwid')."'";
	$res = db_query($sql);
	$row = db_fetch_assoc($res);
	$gems = round($row['gems']*$pct);
	$gold = round($row['gold']*$pct);

	$submit = httppost('submit');
	if (!$submit){
		output("`#You approach the Dwellings Commission, seeing as how you are unhappy with the progression of your recently funded dwelling.");
		output("If you are going to demolish your dwelling now, you shall recieve `^%s `#gold and `%%s `#gems.",$gold,$gems);
		output("Are you sure you wish to demolish your dwelling?`n`n");
		rawoutput("<form action='runmodule.php?module=dwellings&op=demo&dwid=$dwid' method='POST'>");
		rawoutput("<input type='submit' class='button' name='submit' value='".translate_inline("Sign Papers")."'/></form>");
	}else{
		output("`#You listen as a large wrecking ball is lifted into the air and carried off from the Dwellings Commission.");
		$session['user']['gems']+=$gems;
		$session['user']['gold']+=$gold;
		debuglog("recieved $gold gold and $gems gems for demolishing their dwelling.");
		require_once("modules/dwellings/lib.php");
		$sql = "DELETE FROM ".db_prefix("dwellings")." WHERE dwid='$dwid'";
		db_query($sql);
		// dwellings_deleteforowner($session['user']['acctid']);
	}
	addnav("","runmodule.php?module=dwellings&op=demo&dwid=$dwid");
?>