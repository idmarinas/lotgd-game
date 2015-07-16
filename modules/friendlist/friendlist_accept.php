<?php
function friendlist_accept() {
	global $session;
	$ignored = rexplode(get_module_pref('ignored'));
	$request = rexplode(get_module_pref('request'));
	$friends = rexplode(get_module_pref('friends'));
	$ac = httpget('ac');
	if (in_array($ac,$ignored)) {
		$info = translate_inline("This user has ignored you.");
	} elseif (in_array($ac,$friends)) {
		$info = translate_inline("This user is already in your list.");
	} elseif (in_array($ac,$request)) {
		$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$ac AND locked=0";
		$result = db_query($sql);
		if (db_num_rows($result)>0) {
			$row=db_fetch_assoc($result);
			invalidatedatacache("friendliststat-".$session['user']['acctid']);
			invalidatedatacache("friendliststat-".$ac);
			$friends[]=$ac;
			$info = sprintf_translate("%s`Q has been added to your list.",$row['name']);
			require_once("lib/systemmail.php");
			$t = "`\$Friend Request Accepted";
			$mailmessage=array("%s`0`@ has accepted your Friend Request.",$session['user']['name']);
			systemmail($ac,$t,$mailmessage);
			$friends = rimplode($friends);
			set_module_pref('friends',$friends);
			$friends = rexplode(get_module_pref('friends','friendlist',$ac));
			$friends[]=$session['user']['acctid'];
			$friends = rimplode($friends);
			set_module_pref('friends',$friends,'friendlist',$ac);
			$request = array_diff($request, array($ac));
			$request = rimplode( $request);
			set_module_pref('request',$request);
		} else {
			$info = translate_inline("That user no longer exists...");
		}
	}
	output_notl($info);
}
?>
