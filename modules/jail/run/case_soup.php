<?php
if ($session['user']['gems'] == 0)
{
	output
	(
		"Since you do not have a gem to give to the guard, he sits the warm, hearty, full bowl of your favorite soup, 
		just out of reach from your cell.");
	addnav("Back to your cell", "runmodule.php?module=jail");
} 
else
{
	output("The guard takes your gem, and hands you a warm bowl of soup. You feel much better.");
	addnav("Back to your cell", "runmodule.php?module=jail");
	$session['user']['gems'] -- ;
	$session['user']['hitpoints'] += 5;
}
?>