<?php
function friendlist_unignore() {
	global $session;
	$ac = httpget('ac');
	$ignored = rexplode(get_module_pref('ignored','friendlist',$ac));
	$iveignored = rexplode(get_module_pref('iveignored'));
	if (in_array($ac,$iveignored)) {
		$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$ac AND locked=0";
		$result = db_query($sql);
		if (db_num_rows($result)>0) {
			$row=db_fetch_assoc($result);
			$info = sprintf_translate("%s`Q has been removed from your list.",$row['name']);
			require_once("lib/systemmail.php");
			$t = array("`\$Ignore List Removal");
			$mailmessage=array("%s`0`@ has removed you from %s ignore list.",$session['user']['name'],($session['user']['sex']?translate_inline("her"):translate_inline("his")));
			systemmail($ac,$t,$mailmessage);
		} else {
			$info = translate_inline("That user no longer exists...");
		}
	}
	$ignored = array_diff($ignored, array($session['user']['acctid']));
	$ignored = rimplode( $ignored);
	set_module_pref('ignored',$ignored,'friendlist',$ac);
	if (in_array($ac,$iveignored)) {
		$iveignored = array_diff($iveignored, array($ac));
		$iveignored = rimplode( $iveignored);
		set_module_pref('iveignored',$iveignored);
	}
	output_notl($info);
}
?>