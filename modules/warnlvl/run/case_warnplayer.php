<?php
/**
	Display the current warnings of the player and a form for submitting another.
	
	If enabled, delete any warnings that have gone past the set time allowed.
*/
	include('modules/warnlvl/warnlvl_functions.php');

	$id = httpget('id');
	$ret = httpget('ret');
	$return = cmd_sanitize($ret);
	$return = substr($return,strrpos($return,"/")+1);

	$reasons = explode("\r\n",get_module_setting('reasons','warnlvl'));
	$warns_total = get_module_setting('warns','warnlvl');
	$ban_days = get_module_setting('bans','warnlvl');
	$keep_days = get_module_setting('days','warnlvl');
	$seconds = 60 * 60 * 24 * $keep_days;
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

	$sql = "SELECT name, lastip, uniqueid FROM " . db_prefix('accounts') . " WHERE acctid = '" . $id . "'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$row_name = $row['name'];
	$unique_id = HTMLEntities($row['uniqueid'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	$last_ip = HTMLEntities($row['lastip'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"));

	$sql = "SELECT banexpire FROM " . db_prefix('bans') . " WHERE uniqueid = '" . $unique_id . "' OR ipfilter = '" . $last_ip . "'";
	$result = db_query($sql);
	$rows_returned = db_num_rows($result);

	$name = translate_inline('Name');
	$reason = translate_inline('Reason');
	$comment = translate_inline('Comment');
	$yom = translate_inline('YoM Text');
	$yom_text = translate_inline('You have failed to follow one or more of the site rules. Your behaviour is unacceptable and needs to be improved to continue to stay in this Realm. We will quite happily cast you out of the realm and into the darkest recesses of the abyss never to return again.');
	$warnings = translate_inline('Warnings');
	$bandays = translate_inline('Days Banned');
	$in_total = translate_inline(' in total.');
	$submit = translate_inline('Submit');

	rawoutput('<form action="runmodule.php?module=warnlvl&op=warnplayersubmit" method="POST">');
	addnav('',"runmodule.php?module=warnlvl&op=warnplayersubmit");
	rawoutput('<table border="0" cellpadding="2" cellspacing="1" bgcolor="#000000" align="center">');
	rawoutput('<tr class="trhead"><td>' . $name . ':</td><td>' . appoencode('`#'.$row_name) . '</td></tr>');

	if( !empty($allprefs) )
	{
		rawoutput('<tr class="trhead"><td>' . $warnings . ':</td><td>' . (!empty($allprefs['warnings'])?$allprefs['warnings']:0) . $in_total .'</td></tr>');
		rawoutput('<tr class="trhead"><td>' . $bandays . ':</td><td>' . (!empty($allprefs['bandays'])?$allprefs['bandays']:0) . $in_total . '</td></tr>');

		$change = FALSE;
		for( $i=0; $i<$count; $i++ )
		{
			if( $keep_days == 0 || ($allprefs['date'][$i] + $seconds) > time() )
			{
				$subber_id = $allprefs['subber_id'][$i];
				$sql = "SELECT name FROM " . db_prefix('accounts') . " WHERE acctid = '" . $subber_id . "'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);

				$class = ( $i%2 ) ? 'trlight' : 'trdark';
				$trans_reasons = ( $reasons[$allprefs['reason'][$i]] == 999 ) ? translate_inline('Unknown') : translate_inline($reasons[$allprefs['reason'][$i]]);
				rawoutput('<tr class="' . $class . '"><td>' . $reason . ':</td><td>' . $trans_reasons . '</td></tr>');
				rawoutput('<tr class="' . $class . '"><td valign="top">' . $comment . ':</td><td>');
				output('%s', stripslashes($allprefs['comments'][$i]));
				rawoutput('</td></tr><tr class="' . $class . '"><td colspan="2">');
				output('`2Warned by `@%s `2about `@%s `2ago.', $row['name'], translate_inline(time_since($allprefs['date'][$i])));
				rawoutput('</td></tr>');
			}
			else
			{
				//
				// Delete warning.
				//
				unset($allprefs['reason'][$i]);
				unset($allprefs['comments'][$i]);
				unset($allprefs['subber_id'][$i]);
				unset($allprefs['date'][$i]);
				$change = TRUE;
			}
		}

		if( $change )
		{
			$count = count($allprefs['reason']);
			set_module_pref('allprefs',serialize($allprefs),'warnlvl',$id);
		}
	}

	if( $rows_returned <= 0 )
	{
		$warns_left = $warns_total - $count;
		$warns_left = ( $warns_left < 1 ) ? '1' : $warns_left;
		rawoutput('<tr class="trhead"><td colspan="2">');
		output('`#%s `3has `$%s %s `3so far. %s more for a %s day ban.', $row_name, $count, translate_inline($count==1?'warning':'warnings'), $warns_left, $ban_days);
		rawoutput('</td></tr>');

		if( $warns_left == 1 )
		{
			rawoutput('<tr class="trdark"><td colspan="2">');
			output('`^If you warn this person once more, they will be banned for %s %s. If you are sure then please proceed.', $ban_days, translate_inline($ban_days==1?'day':'days'));
			rawoutput('</td></tr>');
		}

		$options = '';
		$reasons_total = count($reasons);
		for( $i=0; $i<$reasons_total; $i++ )
		{
			$options .= '<option value="' . $i . '">' . $reasons[$i] . '</option>';
		}
		$option = translate_inline('Please Select a Reason');
		rawoutput('<tr class="trhead"><td>' . $reason . ':</td><td><select name="reason"><option value="999">' . $option . '</option>' . $options . '</select></td></tr><tr class="trhead"><td colspan="2">');
		output('Only other moderators will see these comments.');
		rawoutput('</td></tr><tr class="trhead"><td valign="top">' . $comment . ':</td><td><textarea name="comment" rows="7" cols="40"></textarea></td></tr>');

		rawoutput('<tr class="trhead"><td colspan="2">');
		output('Put in the box below your warning message for the YoM.');
		rawoutput('</td></tr><tr class="trhead"><td valign="top">' . $yom . ':</td><td><textarea name="yom_text" rows="7" cols="40">' . $yom_text . '</textarea></td></tr>');

		//
		// Give option to send person to jail.
		//
		if( is_module_active('jail') && $warns_left > 1 )
		{
			$jail_them = translate_inline('Send To Jail');
			rawoutput('<tr class="trhead"><td>' . $jail_them . '?</td><td><input type="checkbox" name="jail" value="1" /></td></tr>');
		}

		//
		// Give option to mute person.
		//
		if( is_module_active('mutemod') && $warns_left > 1 )
		{
			$mute_them = translate_inline('Mute For 1 Day');
			rawoutput('<tr class="trhead"><td>' . $mute_them . '?</td><td><input type="checkbox" name="mute" value="1" /></td></tr>');
		}

		rawoutput('<tr class="trhead"><td>&nbsp;</td><td><input type="hidden" name="user_id" value="' . $id . '" /><input type="hidden" name="return" value="' . $return . '" />');
		rawoutput('<input type="hidden" name="warns_count" value="' . $count . '" /><input type="submit" value=" ' . $submit . ' " class="button" /></td></tr>');
	}
	else
	{
		rawoutput('<tr class="trhead"><td colspan="2">');
		output('`#%s `3is currently banned and therefore can\'t be warned.', $row_name);
		rawoutput('</td></tr>');
	}

	rawoutput('</table></form><br />');

	addnav('Return');
	addnav('Return whence you came',$return);
	villagenav();
?>