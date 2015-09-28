<?php
$number = e_rand(1,10);
if ($number > 8)
{
	output("`2Knowing the sheriff is onto you, you make a run for it.`n");
	output("%s does manage to hit you once before you flee.`n", $sheriffname);
	$session['user']['hitpoints'] = 1;
	output("`2After a few minutes you look back and do not see the sheriff anymore. You have escaped!`n");
	addnav("Return to village", "village.php");
}
else
{
	output("`2Knowing the sheriff is onto you, you make a run for it.`n");
	output("`2As you turn the sheriff grabs your shoulder.`n");
	addnav("Give up", "runmodule.php?module=jail&op=giveup");
} 
?>