<?
	$session['user']['alive'] = 1;
	$session['user']['location'] = get_module_pref('village');
	if(get_module_pref('injail') == 0)
	{
		output
		(
			"You survived the night in jail with only a few mental scars. %s pulls out his heavy set of keys and unlocks 
			your cell door to let you out.", $sheriffname
		);
		addnav("Take your things and go", "village.php");

//RPGee.com - Clear days until released just in case admin makes manual userpref changes
		set_module_pref('daysin', 0);
//END RPGee.com

	}
	else
	{
		injailnav();
		output
		(
			"Just as you were getting into a good sleep, the big hairy guy next to you asks if you want to talk about feelings. 
			That new day can't come fast enough."
		);
	}
?>