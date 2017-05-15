<?php
function get_partner($player=false)
{
	global $session;
	if (! isset($session['user']['prefs']['sexuality']) || $session['user']['prefs']['sexuality']=='')
		$session['user']['prefs']['sexuality'] = ! $session['user']['sex'];
	if ($player === false)
	{
		$partner = getsetting("barmaid");
		if ($session['user']['prefs']['sexuality'] == SEX_MALE) {
			$partner = getsetting("bard");
		}
	}
	else
	{
		if ($session['user']['marriedto'] == INT_MAX)
		{
			$partner = getsetting("barmaid");
			if ($session['user']['prefs']['sexuality'] == SEX_MALE)
			{
				$partner = getsetting("bard");
			}
		}
		else
		{
			$sql = "SELECT name FROM ".DB::prefix("accounts")." WHERE acctid = {$session['user']['marriedto']}";
			$result = DB::query($sql);
			if ($row = DB::fetch_assoc($result))
			{
				$partner = $row['name'];
			}
			else
			{
				$session['user']['marriedto'] = 0;
				$partner = getsetting("barmaid", "`%Violet");
				if ($session['user']['prefs']['sexuality']  == SEX_MALE)
				{
					$partner = getsetting("bard", "`^Seth");
				}
			}
		}
	}
//	No need to translate names...
//	tlschema("partner");
//	$partner = translate_inline($partner);
//	tlschema();
	return $partner;
}

?>
