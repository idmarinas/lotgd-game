<?php
/**
	A new warning has been submitted. Send out warning YoM,
	or banned YoM if they're gone over the set limit.
*/
	require_once('lib/systemmail.php');
	include('modules/warnlvl/warnlvl_functions.php');

	$id = httppost('user_id');
	$return = httppost('return');

	$sql = "SELECT name, sex, lastip, lasthit FROM " . db_prefix('accounts') . " WHERE acctid = '" . $id . "'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$username = $row['name'];
	$lastip = $row['lastip'];
	$lasthit = $row['lasthit'];

	$reasons = explode("\r\n",get_module_setting('reasons','warnlvl'));
	$reasons['999'] = translate_inline('Unknown');
	$warns_total = get_module_setting('warns','warnlvl');
	$ban_days = get_module_setting('bans','warnlvl');
	$allprefs = get_module_pref('allprefs','warnlvl',$id);
	if( !empty($allprefs) ) 
	{
		$allprefs = unserialize($allprefs);
		$count = count($allprefs['reason']);
	}
	else
	{
		$allprefs = array();
		$count = 0;
	}

	//
	// Jail time.
	//
	$jail_msg = '';
	$jail = httppost('jail');
	if( isset($jail) && $jail == 1 )
	{
		set_module_pref('injail',1,'jail',$id);
		$jail_msg = 'A short stay in Jail will do you good.`n`n';
	}
	//
	// Mute time.
	//
	$mute_msg = '';
	$mute = httppost('mute');
	if( isset($mute) && $mute == 1 )
	{
		increment_module_pref('tempmute',1,'mutemod',$id);
		$mute_msg = 'You have been muted for 1 day.`n`n';
	}

	$yom_text = httppost('yom_text');
	$reason = httppost('reason');
	$allprefs['reason'][] = $reason;
	$allprefs['comments'][] = httppost('comment');
	$allprefs['subber_id'][] = $session['user']['acctid'];
	$allprefs['date'][] = time();
	$allprefs['warnings'] = $allprefs['warnings'] + 1;
	$warns_count = $count + 1;
	$subject = translate_mail('This is a Warning')

	if( $warns_count >= $warns_total )
	{
		//
		//	Banned.
		//
		$allprefs['bandays'] = $allprefs['bandays'] + $ban_days;
		$days = translate_inline($ban_days==1?'day':'days');

		output("`#%s `3has been banned for %s %s. They `iwere`i warned.", $username, $ban_days, $days);

		addnews("`#%s `3has been `\$banned `3for `^%s %s`3. %s should have taken the %s as a sign.", $username, $ban_days, $days, translate_inline($row['sex']==1?'She':'He'), translate_inline($warns_count==1?'warning':'warnings'), TRUE);

		// Send banned YoM.
		$msg = "`^Hello %s`^, This is your `$%s warning`^, and notice that you are henceforth `\$banned for %s %s`^.`n`nReason: `$%s`^.`n`n%s`n`nThank you.`n`nThe Staff";
		$mailmessage = translate_mail(array($msg, $username, get_suffix($warns_count), $ban_days, $days, $reasons[$reason], $yom_text));
		systemmail($id,$subject,$mailmessage);

		// Add ban details to ban table.
		$until_date = date("Y-m-d H:i:s",strtotime("+$ban_days days"));
		$reason = translate_inline('Automatic ban after multiple warnings.');
		$sql = "INSERT INTO " . db_prefix('bans') . " (ipfilter,banexpire,banreason,banner,lasthit) VALUES ('" . $lastip . "','" . $until_date . "','" . $reason . "','System','" . $lasthit . "')";
		db_query($sql);
	}
	else
	{
		//
		// Warned.
		//
		output("`#%s `3has been sent a YoM, this was %s `$%s warning`3. %s in total.", $username, translate_inline($row['sex']==1?'her':'his'), get_suffix($warns_count), $allprefs['warnings']);

		addnews("`#%s `3has been warned about their behaviour, this was their `$%s warning`3.", $username, get_suffix($warns_count), TRUE);

		// Send warning YoM.
		$msg = "`^Hello %s`^, This is your `$%s warning`^.`n`nReason: `$%s`^.`n`n%s`n`n";
		$msg .= $jail_msg . $mute_msg;
		$msg .= 'Thank you.`n`nThe Staff';
		$mailmessage = translate_mail(array($msg, $username, get_suffix($warns_count), $reasons[$reason], $yom_text));
		systemmail($id,$subject,$mailmessage);
	}

	set_module_pref('allprefs',serialize($allprefs),'warnlvl',$id);

	addnav('Return');
	addnav('Return whence you came',$return);
	villagenav();
?>