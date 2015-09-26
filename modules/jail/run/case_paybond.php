<?
$bondtotal = $baillvl*$session['user']['level'];
if ($session['user']['dragonkills'] > 0) $bondtotal = $bondtotal * $session['user']['dragonkills'] * $baildk;

//RPGee.com - bond would not be limited to module setting if the below line were not present
if ($bondtotal > get_module_setting('maxbond')) $bondtotal = get_module_setting('maxbond');
//END RPGee.com

output ("%s says you can post bail for `^%s gold `n", $sheriffname, $bondtotal);
if ($session['user']['gold'] >= $bondtotal) addnav("Pay it", "runmodule.php?module=jail&op=postbail");
addnav("Forget it", "runmodule.php?module=jail");
?>