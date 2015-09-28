<?php
if (get_module_pref('injail'))
{
	if ($session['user']['restorepage'] != "runmodule.php?module=jail&op=wakeup")
		addnav("To your cell", "runmodule.php?module=jail&op=wakeup");
	blocknav("news.php");
	blocknav("village.php");
	if (!get_module_setting('moredays') && !get_module_setting('runonce'))
	{
			if ($evil < get_module_setting('minevil'))
			{
				if ($evil < 0) $newevil = $evil * (-1);
				else $newevil = $evil;
				$evil = $evil + round($newevil * get_module_setting('evilremoved') * .01);
				set_module_pref ('alignment', $evil, 'alignment');
			}

//RPGee.com
//			set_module_pref('wantedlevel', 0); <-- ORIGINAL LINE
			if (get_module_pref('wantedlevel') > 0) increment_module_pref('wantedlevel', -1);
//END RPGee.com

			set_module_pref('injail', 0);
			set_module_pref('barrister', 0);
			set_module_pref('witness2', 0);
			set_module_pref('witness1', 0);
			set_module_pref('towncries', get_module_setting('maxtowncries'));     
			set_module_pref('suicideattempts', 0);
			set_module_pref('wenttocourt', 0);
	}
	elseif (get_module_setting('moredays') && !get_module_setting('runonce'))
	{
		if (get_module_pref('daysin') > 0) increment_module_pref('daysin', - 1);
		if (get_module_pref('daysin') == 0)
		{
			if ($evil < get_module_setting('minevil'))
			{
				if ($evil < 0) $newevil = $evil * (-1);
				else $newevil = $evil;
				$evil = $evil + round($newevil * get_module_setting('evilremoved') * .01);
				set_module_pref ('alignment', $evil, 'alignment');
			}
			set_module_pref('injail', 0);

//RPGee.com - reduce wanted level by 1 instead of setting to zero.
//			set_module_pref('wantedlevel', 0); <-- ORIGINAL LINE
			if (get_module_pref('wantedlevel') > 0) increment_module_pref('wantedlevel', -1);
//END RPGee.com

			set_module_pref('barrister', 0);
			set_module_pref('witness2', 0);
			set_module_pref('witness1', 0);
			set_module_pref('towncries', get_module_setting('maxtowncries'));     
			set_module_pref('suicideattempts', 0);
			set_module_pref('wenttocourt', 0);
		}
	}
	elseif (get_module_setting('runonce') && get_module_setting('moredays'))
	{
		if (get_module_pref('daysin') == 0)
		{
			if ($evil < get_module_setting('minevil'))
			{
				if ($evil < 0) $newevil = $evil * (-1);
				else $newevil = $evil;
				$evil = $evil + round($newevil * get_module_setting('evilremoved') * .01);
				set_module_pref ('alignment', $evil, 'alignment');
			}
			set_module_pref('injail', 0);

//RPGee.com - reduce wanted level by 1 instead of setting to zero.
//			set_module_pref('wantedlevel', 0); <-- ORIGINAL LINE
			if (get_module_pref('wantedlevel') > 0) increment_module_pref('wantedlevel', -1);
//END RPGee.com

			set_module_pref('barrister', 0);
			set_module_pref('witness2', 0);
			set_module_pref('witness1', 0);
			set_module_pref('towncries', get_module_setting('maxtowncries'));     
			set_module_pref('suicideattempts', 0);
			set_module_pref('wenttocourt', 0);
		}
	}
}
?>