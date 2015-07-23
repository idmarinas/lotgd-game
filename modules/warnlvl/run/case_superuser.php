<?php
/**
	Display all warnings for a player in form boxes for editing.
*/
	include('modules/warnlvl/warnlvl_functions.php');
	require_once('modules/allprefseditor.php');
	allprefseditor_search();

	page_header('Warning Level Allprefs Editor');

	$subop = httpget('subop');
	$id = httpget('userid');

	addnav('Navigation');
	addnav('Return to the Grotto','superuser.php');
	addnav('Edit user',"user.php?op=edit&userid=$id");

	modulehook('allprefnavs');

	$allprefs = get_module_pref('allprefs','warnlvl',$id);
	if( !empty($allprefs) ) 
	{
		$allprefs = unserialize($allprefs);
		$count = count($allprefs['reason']);
	}
	else
	{
		$count = 0;
	}

	if( $subop != 'edit' )
	{
		unset($allprefs);
		$allprefs = array();
		for( $i=0; $i<$count; $i++ )
		{
			$delete = httppost("delete$i");
			if( !$delete )
			{
				$allprefs['reason'][] = httppost("reason$i");
				$allprefs['comments'][] = httppost("comments$i");
				$allprefs['subber_id'][] = httppost("subber_id$i");
				$allprefs['date'][] = httppost("date$i");
			}
		}

		$allprefs['warnings'] = httppost('warnings');
		$allprefs['bandays'] = httppost('bandays');
		set_module_pref('allprefs',serialize($allprefs),'warnlvl',$id);
		$count = count($allprefs['reason']);

		output('Allprefs Updated`n');
		$subop = 'edit';
	}

	if( $subop == 'edit' )
	{
		if( $count )
		{
			$reasons = explode("\r\n",get_module_setting('reasons','warnlvl'));
			$reasons['999'] = translate_inline('Unknown');

			$title = translate_inline('Warning Level Preferences For Player');
			$writemail = translate_inline('Write Mail');
			$biopage = translate_inline('Bio Page');
			$reason = translate_inline('Reason');
			$comment = translate_inline('Comments');
			$subber = translate_inline('Submitter');
			$date = translate_inline('Submission Date');
			$delete = translate_inline('Tick box to delete');
			$ago = translate_inline(' ago');
			$warnings = translate_inline('Total Warnings');
			$bandays = translate_inline('Total Days Banned');
			$click = translate_inline('Save');

			rawoutput("<form action='runmodule.php?module=warnlvl&op=superuser&userid=$id' method='POST'>");
			addnav('',"runmodule.php?module=warnlvl&op=superuser&userid=$id");
			rawoutput('<table border="0" cellpadding="2" cellspacing="1" bgcolor="#000000" align="center">');
			rawoutput('<tr><td colspan="2" class="trhead"><b>' . $title . '</b></td></tr>');

			for( $i=0; $i<$count; $i++ )
			{
				$subber_id = $allprefs['subber_id'][$i];
				$sql = "SELECT name, login FROM " . db_prefix('accounts') . " WHERE acctid = '" . $subber_id . "'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);

				$options = '';
				foreach( $reasons as $key => $value )
				{
					$selected = ( $key == $allprefs['reason'][$i] ) ? ' selected="selected"' : '';
					$options .= '<option value="' . $key . '"' . $selected . '>' . $reasons[$key] . '</option>';
				}
				rawoutput('<tr class="trlight"><td>' . $reason . ':</td><td><select name="reason' . $i . '">' . $options . '</select></td></tr>');
				rawoutput('<tr class="trdark"><td valign="top">' . $comment . ':</td><td><textarea class="input" name="comments' . $i . '" cols="40" rows="5">' . htmlentities(str_replace("`n", "\n", $allprefs['comments'][$i]), ENT_COMPAT, getsetting("charset", "ISO-8859-1")) . '</textarea></td></tr>');
				rawoutput("<tr class=\"trlight\"><td>$subber:</td><td><input type=\"text\" name=\"subber_id$i\" size=\"3\" maxlength=\"5\" value=\"$subber_id\" />&nbsp;<a href=\"mail.php?op=write&to=" . rawurlencode($row['login']) . "\" target=\"_blank\" onClick=\"" . popup("mail.php?op=write&to=" . rawurlencode($row['login'])) . ";return false;\">");
				rawoutput('<img src="images/newscroll.GIF" width="16" height="16" alt="' . $writemail . '" border="0"></a> <a href="bio.php?char=' . $subber_id . '&ret=' . urlencode($_SERVER['REQUEST_URI']) . '" title="' . $biopage . '">'  . appoencode($row['name']) . '</a></td></tr>');
				rawoutput('<tr class="trdark"><td>' . $date . ':</td><td>' . date('D, jS F, Y', $allprefs['date'][$i]) . ' (' . translate_inline(time_since($allprefs['date'][$i])) . $ago .')</td></tr>');
				rawoutput('<tr class="trlight"><td>' . $delete . ':</td><td><input type="checkbox" name="delete' . $i . '" value="1" /></td></tr>');
				rawoutput('<tr class="trdark"><td colspan="2"><input type="hidden" name="date' . $i . '" value="' . $allprefs['date'][$i] . '" />&nbsp;</td></tr>');
			}

			rawoutput('<tr class="trlight"><td>' . $warnings . ':</td><td><input type="text" name="warnings" size="3" maxlength="3" value="' . $allprefs['warnings'] . '" /></td></tr>');
			rawoutput('<tr class="trlight"><td>' . $bandays . ':</td><td><input type="text" name="bandays" size="3" maxlength="3" value="' . $allprefs['bandays'] . '" /></td></tr>');
			rawoutput('<tr class="trdark"><td colspan="2">&nbsp;</td></tr>');
			rawoutput('<tr class="trlight"><td colspan="2"><input type="submit" class="button" value=" ' . $click . ' "></td></tr>');
			rawoutput('</table></form><br />');
		}
		else
		{
			output('`3This player has no current warnings.');
		}
	}
?>