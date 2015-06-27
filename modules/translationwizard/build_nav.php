<?php

	addnav(array("`bSwitch to %s view`b",translate_inline($viewsimple?"simple":"advanced")),"runmodule.php?module=translationwizard&op=switchview&from=".rawurlencode("module=translationwizard&op=$op&mode=$mode"));
	addnav("Operations");
	addnav("H?Help","runmodule.php?module=translationwizard&op=help");
	addnav("O?Overview","runmodule.php?module=translationwizard&op=overview");
	addnav("R?Restart Translator", "runmodule.php?module=translationwizard");
	addnav("N?Translate by Namespace", "runmodule.php?module=translationwizard&op=list");
	addnav("C?Check for duplicate entries", "runmodule.php?module=translationwizard&op=check");
	addnav("F?Fix already translated in table untranslated", "runmodule.php?module=translationwizard&op=fix");
	addnav("k?Check for known translations","runmodule.php?module=translationwizard&op=known");
	if (get_module_setting('restricted')&& $viewsimple)	{
		if (get_module_pref('allowed'))	{
				addnav(array("Search+Edit the translations table %s",""),"runmodule.php?module=translationwizard&op=searchandedit");
				addnav(array("Search+Replace in the translations table %s",""),"runmodule.php?module=translationwizard&op=searchandreplace");
				addnav(array("Truncate untranslated table %s",""),"runmodule.php?module=translationwizard&op=truncate");
				addnav(array("Delete empty namespace rows from untranslated %s",""),"runmodule.php?module=translationwizard&op=deleteempty");
			} else {
				$norights=translate_inline("(you don't have rights)");
				addnav(array("Search+Edit the translations table %s",$norights),"");
				addnav(array("Search+Replace in the translations table %s",$norights),"");
				addnav(array("Truncate untranslated table %s",$norights),"");
				addnav(array("Delete empty namespace rows from untranslated %s",""),"runmodule.php?module=translationwizard&op=deleteempty");
			}
		} else if ($viewsimple)	{
			addnav(array("Search+Edit the translations table %s",""),"runmodule.php?module=translationwizard&op=searchandedit");
			addnav(array("Search+Replace in the translations table %s",""),"runmodule.php?module=translationwizard&op=searchandreplace");
			addnav(array("Truncate untranslated table %s",""),"runmodule.php?module=translationwizard&op=truncate");
			addnav(array("Delete empty namespace rows from untranslated %s",""),"runmodule.php?module=translationwizard&op=deleteempty");
		}
	if ($viewsimple & !get_module_setting('blockcentral')) {
		addnav("Central Translations");
		addnav("Pull","runmodule.php?module=translationwizard&op=pull");
		addnav("Push","runmodule.php?module=translationwizard&op=push");
		addnav("Insert missing translations","runmodule.php?module=translationwizard&op=insert_central");
		addnav("Check for known translations","runmodule.php?module=translationwizard&op=known&central=1");
		addnav("Truncate pulled translations table","runmodule.php?module=translationwizard&op=truncate&central=1");
	}
	addnav("Your current scheme:");
	if ($languageschema<>'') {
			addnav($languageschema,"!!!addraw!!!",true);
		} else {
			addnav(translate_inline("None, please select a scheme!"),"!!!addraw!!!",true);
			$languageschema="en"; //in case some dummy just wants to click ahead he gets English as a default
		}
	addnav("Change current scheme","runmodule.php?module=translationwizard&op=changescheme");
	addnav("Miscellaneous");
	addnav("Show Versionlog","runmodule.php?module=translationwizard&op=showversionlog");
	if ($viewsimple) addnav("Scan Modules","runmodule.php?module=translationwizard&op=scanmodules");
	
?>