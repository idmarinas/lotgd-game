<?
if(get_module_pref('suicideattempts') > 0)
{
	output("You have tried to kill yourself once already today. You must really be desperate.`n");
	addnews("".$session['user']['name']." `7is suicidal!");
	$newtitle = translate_inline("Suicidal");	
	require_once("lib/names.php");
	$newname = change_player_title($newtitle);
	$session['user']['title'] = $newtitle;
	$session['user']['name'] = $newname;
	addnav("Back to your cell", "runmodule.php?module=jail");
} 
else
{
	output
	(
		"Climbing on a small stool, you take your sheets and tie them around a pipe in the ceiling. 
		You tie the sheet like a noose around you neck and kick out the stool from beneath you.`n"
	);
	switch(e_rand(1,10))
	{
	case 1: 
	case 2: 
	output
	(
		"The pipe was not strong enough to support your weight.It breaks in half soaking you in water.The guard rushes over 
		and does not look very happy.You are going to have to pay to repair that.`n"
	);
		addnav("Back to your cell", "runmodule.php?module=jail");
		$session['user']['gold'] = 0;
		$session['user']['goldinbank'] = 0;

//RPGee.com - Supposed to increment here?
//	set_module_pref('suicideattempts',+1); <-- ORIGINAL CODE
	increment_module_pref('suicideattempts', +1);
//END RPGee.com

	break;
	case 3: 
	case 4: 
	case 5: 
		output("Just as you kick out the chair, the guard walks by and is quick to save you from death. Is this life really so cruel?`n");
		addnav("Back to your cell", "runmodule.php?module=jail");

//		set_module_pref('suicideattempts',+1); <--SAME AS ABOVE
		increment_module_pref('suicideattempts', +1);
//END

	break;
	case 6: 
		output("After about 2 minutes of not being able to breath, you heart ceases to pump. Congratulations. You are now dead.`n");
		addnews("".$session['user']['name']." `7committed suicide in the jail!");
		addnav("To the shades", "shades.php");
		$session['user']['hitpoints'] = 0;
		$session['user']['alive'] = 0;
	
//RPGee.com - added in pref resets for days in jail
		set_module_pref('daysin', 0);
//END

	break;
	case 7: 
	case 8: 
	case 9: 
	case 10: 
		output("`3`c`bOuch!`b`c  `n");
		output("`2You didn't tie that rope tight enough. You fall right to the ground and bust your head off the toilet.`n");
		$session['user']['hitpoints'] = 1;
		addnav("Back to cell", "runmodule.php?module=jail");

//RPGee.com - missing increment here
		increment_module_pref('suicideattempts', +1);
//END RPGee.com

		break;
	}
}
?>