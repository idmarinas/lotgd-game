<?
if (is_module_active("jail")==0){
	if (get_module_pref('injail')){
		if ($session['user']['restorepage'] != "runmodule.php?module=djail&op=wakeup") addnav("To Your Cell", "runmodule.php?module=djail&op=wakeup");
		set_module_pref('injail', 0);
		blocknav("news.php");
		blocknav("village.php");
	}
}
set_module_pref("sheriff",0);
if (get_module_pref("deputy")==1 && get_module_pref("daysdeputy")>=25){
	output("`n`QYou've completed your term as deputy. However, as an honorarium, you can keep the title of `2Deputy`Q.`0`n");
	set_module_pref("deputy",0);
	set_module_pref("wasdeputy",1);
	set_module_pref("daysdeputy",0);
}
if (get_module_pref("wasdeputy")==1 && get_module_pref("daysdeputy")>=25){
	set_module_pref("wasdeputy",0);
}
if (get_module_pref("deputy")==1){
	output("`n`QAs deputy, you gain `@2 extra turns`Q for the day.  Keep up the good work!`n`0");
	$session['user']['turns']+=2;
}
?>