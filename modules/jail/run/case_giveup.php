<?
output("You know you have been caught and do not feel the strength the run from the law.");
output("The sheriff grabs you and takes you to your cell.");
addnews("%s`5 has given themselves up at the jail house.", $session['user']['name']);
set_module_pref('injail', 1);

//RPGee.com - Need to set it to at least one day in this case
if (get_module_setting('moredays')) set_module_pref('daysin', get_module_setting('manydays'));
//END RPGee.com

addnav("Go to your cell", "runmodule.php?module=jail");
?>