<?php
switch ($hookname)
{
case "superuser":
	if ($session['user']['superuser'] & SU_IS_TRANSLATOR) {
		addnav("Actions");
		addnav("Translation Wizard","runmodule.php?module=translationwizard&op=list");
		if (get_module_setting(blocktrans)) blocknav("untranslated.php");
	}
	break;
	
case "header-modules":

	if (get_module_setting("autoscan")) {
		if (httpget('op')=="install") {
			$languageschema=get_module_pref("language","translationwizard");
			if (!$languageschema) break;
			require_once("./modules/translationwizard/scanmodules_func.php");
			$content=wizard_scanfile("modules/".httpget('module').".php"); 
			wizard_insertfile($content,$languageschema);
		} elseif (httpget('op')=="mass" && httppost("install")) {
			$languageschema=get_module_pref("language","translationwizard");
			if (!$languageschema) break;
			require_once("./modules/translationwizard/scanmodules_func.php");
			$module = httppost("module");
			if (is_array($module)){
				$modules = $module;
				}else{
				if ($module) $modules = array($module);
					else $modules = array();
			}
			reset($modules);
			while (list($key,$module)=each($modules)){
				$content=wizard_scanfile("modules/$module.php");
				wizard_insertfile($content,$languageschema);
			}
		}
	}
	if (get_module_setting("translationdelete")) {
		if (httpget('op')=="uninstall") {
			$get=rawurlencode(serialize(httpallget()));
			require_once ("./modules/translationwizard/deleteuninstalled.php");
		} elseif (httpget('op')=="mass" && httppost("uninstall")) {
			$get=rawurlencode(serialize(httpallget()));
			$post=rawurlencode(serialize(httpallpost()));
			require_once ("./modules/translationwizard/deleteuninstalled.php");
		}
	}
	break;
	
	/*case "footer-modules":
		output_notl("Get:");
		debug(httpallget());
	break;
	*/
default:

	break;
}

?>