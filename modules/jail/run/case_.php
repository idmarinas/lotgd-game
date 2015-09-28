<?php
$injail = get_module_pref('injail');
set_module_pref("village", $session['user']['location']);
if($session['user']['alive'] == 0) redirect("shades.php");
if($injail == 1)
{
	$session['user']['restorepage'] = "runmodule.php?module=jail&op=wakeup";
	output("`7You are in your jail cell, there is nothing to do.`n`n");
	require_once("lib/commentary.php");
	addcommentary();
	viewcommentary("jail","Whine about being in jail",20,"whines");
	injailnav();
}
else
{
	output(" `2You wonder into the jail and spot %s sitting at his desk.`n", $sheriffname);
	addnav("Talk to the sheriff", "runmodule.php?module=jail&op=talk");
	addnav("Represent someone", "runmodule.php?module=jail&op=represent");
	addnav("Return to village", "village.php");
	require_once("lib/commentary.php");
	addcommentary();
	viewcommentary("jail", "Taunt those in jail",20,"taunts");
}
?>