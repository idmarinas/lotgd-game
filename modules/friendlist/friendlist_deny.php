<?php
function friendlist_deny() {
	global $session;
	$ignored = rexplode(get_module_pref('ignored'));
	$friends = rexplode(get_module_pref('friends'));
	$request = rexplode(get_module_pref('request'));
	$ac = httpget('ac');
	$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$ac AND locked=0";
	$result = db_query($sql);
	if (in_array($ac,$friends)) {
		$info = translate_inline("That user has been removed.");
		require_once("lib/systemmail.php");
		$t = "`\$Friend List Removal";
		$mailmessage=array("%s`0`@ has deleted you from %s Friend List.",$session['user']['name'],($session['user']['sex']?translate_inline("her"):translate_inline("his")));
		$friends = array_diff($friends, array($ac));
		$friends = rimplode( $friends);
		set_module_pref('friends',$friends);
		$act = $session['user']['acctid'];
		$friends = rexplode(get_module_pref('friends','friendlist',$ac));
		$friends = array_diff($friends, array($act));
		$friends = rimplode( $friends);
		set_module_pref('friends',$friends,'friendlist',$ac);
		invalidatedatacache("friendliststat-".$session['user']['acctid']);
		invalidatedatacache("friendliststat-".$ac);
	} else {
		$info = translate_inline("That user has been denied.");
		require_once("lib/systemmail.php");
		$t = "`\$Friend Request Denied";
		$mailmessage=array("%s`0`@ has denied you your Friend Request.",$session['user']['name']);
		$request = array_diff($request, array($ac));
		$request = rimplode( $request);
		set_module_pref('request',$request);
	}
	if (db_num_rows($result)>0) {
		systemmail($ac,$t,$mailmessage);
		$row=db_fetch_assoc($result);
		$info = sprintf_translate("%s has been removed",$row['name']);
	}

	output_notl($info);
}
?>