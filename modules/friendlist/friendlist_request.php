<?php
function friendlist_request() {
	global $session;
	$ac = httpget('ac');
	$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$ac AND locked=0";
	$result = db_query($sql);
	if (db_num_rows($result)>0) {
		$row=db_fetch_assoc($result);
		$info = translate_inline("You have successfully sent your request to %s`Q.");
		$info = str_replace('%s',$row['name'],$info);
		require_once("lib/systemmail.php");
		$t = array("`\$Friend Request Sent");
		$mailmessage=array("%s`0`@ has sent you a Friend Request.`nIf this user has been spamming you with this, ignore them from your search function.",$session['user']['name']);
		systemmail($ac,$t,$mailmessage);
	} else {
		$info = translate_inline("That user no longer exists...");
	}
	$request = get_module_pref('request','friendlist',$ac);
	$request = rexplode($request);
	$request[]=$session['user']['acctid'];
	$request = rimplode( $request);
	set_module_pref('request',$request,'friendlist',$ac);
	output_notl($info);
}
?>
