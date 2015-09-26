<?
$witness1	= get_module_pref('witness1');
$witness2	= get_module_pref('witness2');
$barrister	= get_module_pref('barrister');
set_module_pref('wenttocourt', 1);

output("You are returned to your cell to await the court's decision. Nothing to do but sit around and wait for a message.`n"); 
$chance = 10; 
if (get_module_pref('alignment', 'alignment') < 10) $chance=9; 
if (get_module_pref('alignment', 'alignment') < -50) $chance=8; 
if (get_module_pref('alignment', 'alignment') < -125) $chance=6; 
if (get_module_pref('alignment', 'alignment') < -250) $chance=3; 
if (get_module_pref('alignment', 'alignment') <- 300) $chance=1; 
if (get_module_pref('alignment', 'alignment') <- 301) $chance=0;
$randchance = e_rand(($session['user']['level']*1.9),($session['user']['level']*10)); 
$chance = $chance * $session['user']['level']; 
if($chance > $randchance)
{
	require_once("lib/systemmail.php");
	systemmail($session['user']['acctid'],"`^The Verdict is in!`0","The court has decided that you are menace to society, and are not releasing you."); 
	systemmail($witness1,"`^The Verdict is in!`0","The court has decided that ".$session['user']['name']." is a menace to society, and will not be released."); 
	systemmail($witness2,"`^The Verdict is in!`0","The court has decided that ".$session['user']['name']." is a menace to society, and will not be released."); 
	systemmail($barrister,"`^The Verdict is in!`0","The court has decided that ".$session['user']['name']." is a menace to society, and will not be released."); 
	injailnav();

//RPGee.com - added in number of days to addnews
//	addnews("%s `#has been sentenced to the rest of the day in jail!",$name); <-- ORIGINAL LINE
	addnews("%s `#has been sentenced to the rest of his jail time of %s!",$name, $daysin);
//END

}
else
{
	require_once("lib/systemmail.php");
	systemmail($session['user']['acctid'],"`^The Verdict is in!`0","The court has decided that you are innocent!"); 
	systemmail($witness1,"`^The Verdict is in!`0","The court has decided that ".$session['user']['name']." is free to go!"); 
	systemmail($witness2,"`^The Verdict is in!`0","The court has decided that ".$session['user']['name']." is free to go!"); 
	systemmail($barrister,"`^The Verdict is in!`0","The court has decided that ".$session['user']['name']." is free to go!"); 
	addnews("".$session['user']['name']." `#has been proven innocent!"); 
	addnav("Continue", "runmodule.php?module=jail&op=verdictleave"); 
} 
?>