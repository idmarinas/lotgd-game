<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/is_email.php");
require_once("lib/safeescape.php");
require_once("lib/sanitize.php");

function systemmail($to,$subject,$body,$from=0,$noemail=false){
	$sql = "SELECT prefs,emailaddress FROM " . db_prefix("accounts") . " WHERE acctid='$to'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	db_free_result($result);
	$prefs = unserialize($row['prefs']);
	$serialized=0;
	if ($from==0){
		if (is_array($subject)){
			$subject = serialize($subject);
			$serialized=1;
		}
		if (is_array($body)){
			$body = serialize($body);
			$serialized+=2;
		}
		$subject = safeescape($subject);
		$body = safeescape($body);
	}else{
		$subject = safeescape($subject);
		$subject=str_replace("\n","",$subject);
		$subject=str_replace("`n","",$subject);
		$body = safeescape($body);
		if ((isset($prefs['dirtyemail']) && $prefs['dirtyemail']) || $from==0){
		}else{
			$subject=soap($subject,false,"mail");
			$body=soap($body,false,"mail");
		}
	}

	$sql = "INSERT INTO " . db_prefix("mail") . " (msgfrom,msgto,subject,body,sent) VALUES ('".(int)$from."','".(int)$to."','$subject','$body','".date("Y-m-d H:i:s")."')";
	db_query($sql);
	invalidatedatacache("mail-$to");
	$email=false;
	if (isset($prefs['emailonmail']) && $prefs['emailonmail'] && $from>0){
		$email=true;
	}elseif(isset($prefs['emailonmail']) && $prefs['emailonmail'] &&
			$from==0 && isset($prefs['systemmail']) && $prefs['systemmail']){
		$email=true;
	}
	$emailadd = "";
	if (isset($row['emailaddress'])) $emailadd = $row['emailaddress'];

	if (!is_email($emailadd)) $email=false;
	if ($email && !$noemail){
		if ($serialized&2){
			$body = unserialize(stripslashes($body));
			$body = translate_mail($body,$to);
		}
		if ($serialized&1){
			$subject = unserialize(stripslashes($subject));
			$subject = translate_mail($subject,$to);
		}

		$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$from'";
		$result = db_query($sql);
		$row1=db_fetch_assoc($result);
		db_free_result($result);
		if ($row1['name']!="")
			$fromline=full_sanitize($row1['name']);
		else
			$fromline=translate_inline("The Green Dragon","mail");

		$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$to'";
		$result = db_query($sql);
		$row1=db_fetch_assoc($result);
		db_free_result($result);
		$toline = full_sanitize($row1['name']);

		// We've inserted it into the database, so.. strip out any formatting
		// codes from the actual email we send out... they make things
		// unreadable
		$body = preg_replace("'[`]n'", "\n", $body);
		$body = full_sanitize($body);
		$subject = htmlentities(full_sanitize($subject), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
		require("lib/settings_extended.php");
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

		require_once("lib/sendmail.php");
		$to=array($emailadd=>$toline);
		$from=array(getsetting("gameadminemail","postmaster@localhost")=>getsetting("gameadminemail","postmaster@localhost"));
		send_email($to,$mailbody,$mailsubj,$from,false,"text/plain");
//		mail($row['emailaddress'],$mailsubj,str_replace("`n","\n",$mailbody),$header);
	}
	invalidatedatacache("mail-$to");
}

?>
