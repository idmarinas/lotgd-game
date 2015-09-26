<?
if (get_module_pref("deputy")==1){
	set_module_pref("deputy",0);
	set_module_pref("wasdeputy",1);
}elseif (get_module_pref("wasdeputy")==1){
	set_module_pref("wasdeputy",0);
}
?>