<?php
$acc=db_prefix('accounts');
$mail=db_prefix('mail');
$sql = "SELECT $mail.*,$acc.name,$acc.acctid FROM $mail LEFT JOIN $acc ON $acc.acctid=$mail.msgfrom WHERE msgto=\"".$session['user']['acctid']."\" AND messageid=\"".$id."\"";
$result = db_query($sql);
if (db_num_rows($result)>0){
	$row = db_fetch_assoc($result);
	$reply = translate_inline("Reply");
	$del = translate_inline("Delete");
	$forward = translate_inline("Forward");
	$unread = translate_inline("Mark Unread");
	$report = translate_inline("Report to Admin");
	$prev = translate_inline("< Previous");
	$next = translate_inline("Next >");
	$problem = "Abusive Email Report:\nFrom: {$row['name']}\nSubject: {$row['subject']}\nSent: {$row['sent']}\nID: {$row['messageid']}\nBody:\n{$row['body']}";
	$problemplayer = (int)$row['msgfrom'];
	$status_image="";
	if ((int)$row['msgfrom']==0){
		$row['name']=translate_inline("`i`^System`0`i");
		// No translation for subject if it's not an array
		$row_subject = @unserialize($row['subject']);
		if ($row_subject !== false) {
			$row['subject'] = call_user_func_array("sprintf_translate", $row_subject);
		}
		// No translation for body if it's not an array
		$row_body = @unserialize($row['body']);
		if ($row_body !== false) {
			$row['body'] = call_user_func_array("sprintf_translate", $row_body);
		}
	} elseif ($row['name']=="") {
		$row['name']=translate_inline("`^Deleted User");
	} else {
		//get status
		$online=(int)is_player_online($row['acctid']);
		$status=($online?"online":"offline");
		$status_image="<img src='images/$status.gif' alt='$status'>";
	}
	if (!$row['seen']) {
		output("`b`#NEW`b`n");
	}else{
		output("`n");
	}
	$sql = "SELECT messageid FROM $mail WHERE msgto='{$session['user']['acctid']}' AND messageid < '$id' ORDER BY messageid DESC LIMIT 1";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		$srow = db_fetch_assoc($result);
		$pid = $srow['messageid'];
	}else{
		$pid = 0;
	}
	$sql = "SELECT messageid FROM $mail WHERE msgto='{$session['user']['acctid']}' AND messageid > '$id' ORDER BY messageid  LIMIT 1";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		$srow = db_fetch_assoc($result);
		$nid = $srow['messageid'];
	}else{
		$nid = 0;
	}
	output("`b`2From:`b `^%s",$row['name']);
	output_notl($status_image."`n",true);
	output("`b`2Subject:`b `^%s`n",$row['subject']);
	output("`b`2Sent:`b `^%s`n",$row['sent']);
	rawoutput("<table style=\"width:50%;border:0;cellspacing:10;\">");
	rawoutput("<tr><td><a href='mail.php?op=write&replyto={$row['messageid']}' class='motd'>$reply</a></td><td><a href='mail.php?op=address&id={$row['messageid']}' class='motd'>$forward</a><td>");
	if ($pid > 0) {
		rawoutput("<a href='mail.php?op=read&id=$pid' class='motd'>".htmlentities($prev, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</a>");
		rawoutput("</td><td nowrap='true'>");
	}else{
		rawoutput(htmlentities($prev), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
		rawoutput("</td><td nowrap='true'>");
	}
	if ($nid > 0){
		rawoutput("<a href='mail.php?op=read&id=$nid' class='motd'>".htmlentities($next, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</a></td>");
	}else{
		rawoutput(htmlentities($next, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</td>");
	}
	rawoutput("</tr></table><br/>");
	output_notl(sanitize_mb(str_replace("\n","`n",$row['body'])));
	$sql = "UPDATE $mail SET seen=1 WHERE  msgto=\"".$session['user']['acctid']."\" AND messageid=\"".$id."\"";
	db_query($sql);
	invalidatedatacache("mail-{$session['user']['acctid']}");
	rawoutput("<table width='50%' border='0' cellpadding='0' cellspacing='5'><tr>
		<td><a href='mail.php?op=write&replyto={$row['messageid']}' class='motd'>$reply</a></td>
		<td><a href='mail.php?op=del&id={$row['messageid']}' class='motd'>$del</a></td>
		</tr><tr>
		<td><a href='mail.php?op=unread&id={$row['messageid']}' class='motd'>$unread</a></td>");
	// Don't allow reporting of system messages as abuse.
	if ((int)$row['msgfrom']!=0) {
		rawoutput("<td><a href=\"petition.php?problem=".rawurlencode($problem)."&abuse=yes&abuseplayer=$problemplayer\" class='motd'>$report</a></td>");
	} else {
		rawoutput("<td>&nbsp;</td>");
	}
	rawoutput("</tr><tr>");
	rawoutput("<td nowrap='true'>");
	if ($pid > 0) {
		rawoutput("<a href='mail.php?op=read&id=$pid' class='motd'>".htmlentities($prev, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</a>");
	}else{
		rawoutput(htmlentities($prev), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	}
	rawoutput("</td><td nowrap='true'>");
	if ($nid > 0){
		rawoutput("<a href='mail.php?op=read&id=$nid' class='motd'>".htmlentities($next, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</a>");
	}else{
		rawoutput(htmlentities($next, ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
	}
	rawoutput("</td>");
	rawoutput("</tr></table>");
}else{
	output("Eek, no such message was found!");
}
?>
