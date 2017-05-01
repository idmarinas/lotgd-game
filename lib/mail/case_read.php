<?php
$acc=DB::prefix('accounts');
$mail=DB::prefix('mail');
$sql = "SELECT $mail.*,$acc.name,$acc.acctid FROM $mail LEFT JOIN $acc ON $acc.acctid=$mail.msgfrom WHERE msgto=\"".$session['user']['acctid']."\" AND messageid=\"".$id."\"";
$result = DB::query($sql);
if (DB::num_rows($result) > 0)
{
	$row = DB::fetch_assoc($result);
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
	if ((int)$row['msgfrom']==0)
	{
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
        $statusImage=($online?"online":"offline");
		$status_image="<img src='images/$statusImage.gif' alt='".ucfirst($statusImage)."'>";
	}

	$sql = "SELECT messageid FROM $mail WHERE msgto='{$session['user']['acctid']}' AND messageid < '$id' ORDER BY messageid DESC LIMIT 1";
	$result = DB::query($sql);
	if (DB::num_rows($result)>0){
		$srow = DB::fetch_assoc($result);
		$pid = $srow['messageid'];
	}else{
		$pid = 0;
	}
	$sql = "SELECT messageid FROM $mail WHERE msgto='{$session['user']['acctid']}' AND messageid > '$id' ORDER BY messageid  LIMIT 1";
	$result = DB::query($sql);
	if (DB::num_rows($result)>0){
		$srow = DB::fetch_assoc($result);
		$nid = $srow['messageid'];
	}else{
		$nid = 0;
	}

	//-- Buttons
	$buttonsMenuTop = '<div class="ui top attached primary buttons">';
	$buttonsMenuTop .= "<a class='ui button' href='mail.php?op=write&replyto={$row['messageid']}'>$reply</a>";
	$buttonsMenuTop .= "<a class='ui button' href='mail.php?op=del&id={$row['messageid']}'>$del</a>";
	$buttonsMenuTop .= "<a class='ui button' href='mail.php?op=unread&id={$row['messageid']}'>$unread</a>";
	// Don't allow reporting of system messages as abuse.
	if ((int) $row['msgfrom'] != 0)
	{
		$buttonsMenuTop .= "<a class='ui button' href=\"petition.php?problem=".rawurlencode($problem)."&abuse=yes&abuseplayer=$problemplayer\">$report</a>";
	}
	$buttonsMenuTop .= '</div>';

	$buttonsMenuBottom = '<div class="ui bottom attached primary buttons">';
	if ($pid > 0)
	{
		$buttonsMenuBottom .= "<a class='ui button' href='mail.php?op=read&id=$pid'>".htmlentities($prev, ENT_COMPAT, getsetting('charset', 'UTF-8'))."</a>";
	}
	else
	{
		$buttonsMenuBottom .= '<a class="ui disabled button">' . htmlentities($prev, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</a>';
	}

	if ($nid > 0)
	{
		$buttonsMenuBottom .= "<a class='ui button' href='mail.php?op=read&id=$nid'>".htmlentities($next, ENT_COMPAT, getsetting('charset', 'UTF-8'))."</a>";
	}
	else
	{
		$buttonsMenuBottom .= '<a class="ui disabled button">' . htmlentities($next, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</a>';
	}
	$buttonsMenuBottom .= '</div>';

	$label = '';
	if (! $row['seen'])
	{
		$label = ' <span class="ui teal ribbon label">'.appoencode(translate_inline('NEW')).'</span>';
	}

	rawoutput('<div class="ui fluid card">'.$buttonsMenuTop);
	rawoutput('<div class="content">'.$label.'<div class="right floated meta">');
    output('`b`2Sent:`b `^%s`0', $row['sent']);
    rawoutput('</div>');
    output('`b`2From:`b %s `^%s`0', $status_image, $row['name'], true);
	rawoutput('</div><div class="content"><span class="ui header">'.appencode($row['subject']).'</span><p>'.appoencode(sanitize_mb(str_replace("\n","`n",$row['body']))));
	rawoutput('</p></div>'.$buttonsMenuBottom.'</div>');

	$sql = "UPDATE $mail SET seen=1 WHERE  msgto=\"".$session['user']['acctid']."\" AND messageid=\"".$id."\"";
	DB::query($sql);

}
else
{
	output("Eek, no such message was found!");
}
?>
