<?php
// translator ready
// addnews ready
// mail ready
require_once 'lib/is_email.php';
require_once 'lib/safeescape.php';
require_once 'lib/sanitize.php';

function systemmail($to, $subject, $body, $from = 0, $noemail = false)
{
    global $session;

	$sql = "SELECT prefs, emailaddress FROM " . DB::prefix("accounts") . " WHERE acctid='$to'";
	$result = DB::query($sql);
	$row = DB::fetch_assoc($result);
	DB::free_result($result);
	$prefs = unserialize($row['prefs']);
	$serialized=0;
    if ($from == 0)
    {
        if (is_array($subject))
        {
			$subject = serialize($subject);
			$serialized=1;
		}
        if (is_array($body))
        {
			$body = serialize($body);
			$serialized+=2;
		}
		$subject = safeescape($subject);
		$body = safeescape($body);
    }
    else
    {
		$subject = safeescape($subject);
		$subject=str_replace("\n","",$subject);
		$subject=str_replace("`n","",$subject);
		$body = safeescape($body);
		if ((isset($prefs['dirtyemail']) && $prefs['dirtyemail']) || $from == 0) {}
        else
        {
			$subject = soap($subject,false,"mail");
			$body = soap($body,false,"mail");
		}
	}

    $insert = DB::insert('mail');
    $insert->values([
        'msgfrom' => (int) $from,
        'msgto' => (int) $to,
        'subject' => $subject,
        'body' => $body,
        'sent' => date('Y-m-d H:i:s'),
        'originator' => $session['user']['acctid']
    ]);

	DB::execute($insert);
	invalidatedatacache("mail-$to");
	$email=false;
    if (isset($prefs['emailonmail']) && $prefs['emailonmail'] && $from > 0) { $email = true; }
    elseif (isset($prefs['emailonmail']) && $prefs['emailonmail'] && $from == 0 && isset($prefs['systemmail']) && $prefs['systemmail']) { $email = true; }
	$emailadd = '';
	if (isset($row['emailaddress'])) { $emailadd = $row['emailaddress']; }

	if (!is_email($emailadd)) $email=false;
    if ($email && !$noemail)
    {
        if ($serialized&2)
        {
			$body = unserialize(stripslashes($body));
			$body = translate_mail($body,$to);
		}
        if ($serialized&1)
        {
			$subject = unserialize(stripslashes($subject));
			$subject = translate_mail($subject,$to);
		}

		$sql = "SELECT name FROM " . DB::prefix("accounts") . " WHERE acctid='$from'";
		$result = DB::query($sql);
		$row1=DB::fetch_assoc($result);
		DB::free_result($result);
		if ($row1['name']!="")
			$fromline=full_sanitize($row1['name']);
		else
			$fromline=translate_inline("The Green Dragon","mail");

		$sql = "SELECT name FROM " . DB::prefix("accounts") . " WHERE acctid='$to'";
		$result = DB::query($sql);
		$row1=DB::fetch_assoc($result);
		DB::free_result($result);
		$toline = full_sanitize($row1['name']);

		// We've inserted it into the database, so.. strip out any formatting
		// codes from the actual email we send out... they make things
		// unreadable
		$body = preg_replace("'[`]n'", "\n", $body);
		$body = full_sanitize($body);
		$subject = htmlentities(full_sanitize($subject), ENT_COMPAT, getsetting("charset", "UTF-8"));

        require_once 'lib/settings_extended.php';

		$subj = translate_mail($settings_extended->getSetting('notificationmailsubject'),$to);
		$msg = translate_mail($settings_extended->getSetting('notificationmailtext'),$to);
		$replace=array(
			"{subject}"=>stripslashes($subject),
			"{sendername}"=>$fromline,
			"{receivername}"=>$toline,
			"{body}"=>stripslashes($body),
			"{gameurl}"=>($_SERVER['SERVER_PORT']==443?"https":"http")."://".($_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME'])),
			);
		$keys=array_keys($replace);
		$values=array_values($replace);

		$mailbody=str_replace($keys,$values,$msg);
		$mailsubj=str_replace($keys,$values,$subj);
		$mailbody=str_replace("`n","\n\n",$mailbody);
		lotgd_mail($row['emailaddress'],$mailsubj,str_replace("`n","\n",$mailbody));
	}
	invalidatedatacache("mail-$to");
}

?>
