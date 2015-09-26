<?
$bondtotal = $baillvl * $session['user']['level'];
if($session['user']['dragonkills'] > 0) $bondtotal = $bondtotal * $session['user']['dragonkills'] * $baildk;
if ($bondtotal > get_module_setting('maxbond')) $bondtotal = get_module_setting('maxbond');
$session['user']['gold'] -= $bondtotal; 
output("%s let's you out of the cell. He isn't happy about it. You're on a steady watch from now on.", $sheriffname); 
set_module_pref('injail', 0);

//RPGee.com - This is supposed to decrease the wanted level, but doesn't
//set_module_pref("wantedlevel",-1); <-- ORIGINAL LINE
if (get_module_pref('wantedlevel') > 0) increment_module_pref('wantedlevel', -1);
//END RPGee.com

$align = get_module_pref('alignment', 'alignment');
$align = ceil($align - $align * (get_module_setting('bailevil') * .01));

//RPGee.com - supposed to decrease alignment
//set_module_pref("alignement",$align); <-- ORIGINAL LINE
set_module_pref('alignment', $align, 'alignment');
//END RPGee.com

addnav("Continue", "village.php");

//RPGee.com - added in pref resets for days in jail
set_module_pref('daysin', 0);
//END

?>