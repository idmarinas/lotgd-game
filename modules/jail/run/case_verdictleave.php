<?
set_module_pref('witness1', 0); 
set_module_pref('witness2', 0);
set_module_pref('barrister', 0);
set_module_pref('injail', 0);
addnav("Return to the village", "village.php"); 
output
(
	"You are free to go. You gather your belongings and head out to the village. You should be sure you compensate your barrister, 
	and maybe even your witnesses. This wouldn't be possible without them."
); 

//RPGee.com - added in pref resets for days in jail
set_module_pref('daysin', 0);
//END

?>