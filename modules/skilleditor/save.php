<?php
	$skillid = httppost('id');
	$name = stripslashes(httppost('name'));
	$type = stripslashes(httppost('type'));
	$startmsg = stripslashes(httppost('startmsg'));
	$effectmsg = stripslashes(httppost('effectmsg'));
	$effectnodmgmsg = stripslashes(httppost('effectnodmgmsg'));
	$effectfailmsg = stripslashes(httppost('effectfailmsg'));
	$wearoff = stripslashes(httppost('wearoff'));
	$roundmsg = stripslashes(httppost('roundmsg'));
	//catch the submitted value, check to see if it's empty. if it is, declare it as 0 and move on
	//if it isn't, use original value instead
	$levelreq = httppost('levelreq');
	if ($levelreq == "") $levelreq = 0;
	else $levelreq = httppost('levelreq');
  $minioncount = httppost('minioncount');
	if ($minioncount == "") $minioncount = 0;
	else $minioncount = httppost('minioncount');
	$lifetap = httppost('lifetap');
	if ($lifetap == "") $lifetap = 0;
	else $lifetap = httppost('lifetap');
	$manacost = httppost('manacost');
	if ($manacost == "") $manacost = 0;
	else $manacost = httppost('manacost');
	$rounds = httppost('rounds');
	if ($rounds == "") $rounds = 0;
	else $rounds = httppost('rounds');
	$dmgshield = httppost('damageshield');
	if ($dmgshield == "") $dmgshield = 0;
	else $dmgshield = httppost('damageshield');
	$badguydmgmod = httppost('badguydmgmod');
	if ($badguydmgmod == "") $badguydmgmod = 0;
	else $badguydmgmod = httppost('badguydmgmod');
	$badguyatkmod = httppost('badguyatkmod');
	if ($badguyatkmod == "") $badguyatkmod = 0;
	else $badguyatkmod = httppost('badguyatkmod');
	$badguydefmod = httppost('badguydefmod');	
	if ($badguydefmod == "") $badguydefmod = 0;
	else $badguydefmod = httppost('badguydefmod');
	$atkmod = httppost('atkmod');
	if ($atkmod == "") $atkmod = 0;
	else $atkmod = httppost('atkmod');
	$defmod = httppost('defmod');
	if ($defmod == "") $defmod = 0;
	else $defmod = httppost('defmod');
	$mingoodguydamage = httppost('mingoodguydamage');
	if ($mingoodguydamage == "") $mingoodguydamage = 0;
	else $mingoodguydamage = httppost('mingoodguydamage');
	$maxgoodguydamage = httppost('maxgoodguydamage');
	if ($maxgoodguydamage == "") $maxgoodguydamage = 0;
	else $maxgoodguydamage = httppost('maxgoodguydamage');
	$minbadguydamage = httppost('minbadguydamage');
	if ($minbadguydamage == "") $minbadguydamage = 0;
	else $minbadguydamage = httppost('minbadguydamage');
	$maxbadguydamage = httppost('maxbadguydamage');
	if ($maxbadguydamage == "") $maxbadguydamage = 0;
	else $maxbadguydamage = httppost('maxbadguydamage');
	$regen = httppost('regen');
	if ($regen == "") $regen = 0;
	else $regen = httppost('regen');
	
	if ($skillid>0){
		$sql = "UPDATE ".db_prefix("skills")." 
			SET name=\"$name\",
			type=\"$type\",
			startmsg=\"$startmsg\",
			effectmsg=\"$effectmsg\",
			effectnodmgmsg=\"$effectnodmgmsg\",
			effectfailmsg=\"$effectfailmsg\",
			wearoff=\"$wearoff\",
			roundmsg=\"$roundmsg\",
			levelreq=$levelreq,
			minioncount=$minioncount,
			lifetap=$lifetap,
			manacost=$manacost,
			rounds=$rounds,
			mingoodguydamage=$mingoodguydamage,
			maxgoodguydamage=$maxgoodguydamage,
			minbadguydamage=$minbadguydamage,
			maxbadguydamage=$maxbadguydamage,
			damageshield=$dmgshield,
			badguydmgmod=$badguydmgmod,
			badguyatkmod=$badguyatkmod,
			badguydefmod=$badguydefmod,
			atkmod=$atkmod,
			defmod=$defmod, 
			regen=$regen 
			WHERE id=$skillid";
		output("`6The skill \"`^%s`6\" has been successfully edited.`n`n",$name);
	}else{
		$sql = "INSERT INTO ".db_prefix("skills")." 										
		(name,type,startmsg,effectmsg,effectnodmgmsg,effectfailmsg,wearoff,roundmsg,levelreq,minioncount,lifetap,manacost,rounds,damageshield,badguydmgmod,badguyatkmod,badguydefmod,atkmod,defmod,regen,mingoodguydamage,maxgoodguydamage,minbadguydamage,maxbadguydamage) 
		VALUES (\"$name\",\"$type\", \"$startmsg\", \"$effectmsg\", \"$effectnodmgmsg\", \"$effectfailmsg\", \"$wearoff\", \"$roundmsg\",$levelreq,$minioncount,$lifetap,$manacost,$rounds,$dmgshield,$badguydmgmod,$badguyatkmod,$badguydefmod,$atkmod,$defmod,$regen,$mingoodguydamage,$maxgoodguydamage,$minbadguydamage,$maxbadguydamage)";
		output("`6The item \"`^$name`6\" has been saved to the database.`n`n");
	}
	db_query($sql);
	invalidatedatacache("modules-skilleditor-viewgoods");
	invalidatedatacache("modules-skilleditor-enter");
	$op = "";
	httpset("op", $op);
?>
