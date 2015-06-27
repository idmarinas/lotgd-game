<?php
$to = httppost('to');
$sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE login='$to'";
$result = db_query($sql);
$return = (int) httppost("returnto");
if(db_num_rows($result)>0){
	$row1 = db_fetch_assoc($result);
	if (getsetting("onlyunreadmails",true)) {
		$maillimitsql = "AND seen=0";
	} else {
		$maillimitsql = "";
	}
	$sql = "SELECT count(messageid) AS count FROM " . db_prefix("mail") . " WHERE msgto='".$row1['acctid']."' $maillimitsql";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	if ($row['count']>=getsetting("inboxlimit",50)) {
		output("`\$You cannot send that person mail, their mailbox is full!`0`n`n");
	}else{
		$subject = str_replace("`n","",httppost('subject'));
		$body = str_replace("`n","\n",httppost('body'));
		$body = str_replace("\r\n","\n",$body);
		$body = str_replace("\r","\n",$body);
		$body = addslashes(substr(stripslashes($body),0,(int)getsetting("mailsizelimit",1024)));
		require_once("lib/systemmail.php");
		systemmail($row1['acctid'],$subject,$body,$session['user']['acctid']);
		invalidatedatacache("mail-{$row1['acctid']}");
		output("Your message was sent!`n");
		if (httppost('sendclose')) rawoutput("<script language='javascript'>window.close();</script>");
		if (httppost('sendback')) {
			$return=0;
		}
	}
}else{
	output("Could not find the recipient, please try again.`n");
}
if ($return>0){
	$op="read";
	httpset('op','read');
	$id = $return;
	httpset('id',$id);
}else{
	$op="";
	httpset('op', "");
}
?>
