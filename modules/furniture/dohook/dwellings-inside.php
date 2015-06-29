<?php
	$dwid=$args['dwid'];
	addnav("Furniture");
	if (get_module_objpref("dwellings",$dwid,"chair","furniture")>0) {
		if (get_module_objpref("dwellings",$dwid,"chair","furniture")==1) addnav("Use Chair","runmodule.php?module=furniture&loc=inside&op=furniture&op2=chair&dwid=$dwid");
		else addnav(array("Use Chair: %s",stripslashes(get_module_objpref("dwellings",$dwid,"custchair","furniture"))),"runmodule.php?module=furniture&loc=inside&op=furniture&op2=chair&dwid=$dwid");
	}
	if (get_module_objpref("dwellings",$dwid,"table","furniture")>0) {
		if (get_module_objpref("dwellings",$dwid,"table","furniture")==1) addnav("Use Table","runmodule.php?module=furniture&loc=inside&op=furniture&op2=table&dwid=$dwid");
		else addnav(array("Use Table: %s",stripslashes(get_module_objpref("dwellings",$dwid,"custtable","furniture"))),"runmodule.php?module=furniture&loc=inside&op=furniture&op2=table&dwid=$dwid");
	}
	if (get_module_objpref("dwellings",$dwid,"bed","furniture")>0) {
		if (get_module_objpref("dwellings",$dwid,"bed","furniture")==1) addnav("Use Bed","runmodule.php?module=furniture&loc=inside&op=furniture&op2=bed&dwid=$dwid");
		else addnav(array("Use Bed: %s",stripslashes(get_module_objpref("dwellings",$dwid,"custbed","furniture"))),"runmodule.php?module=furniture&loc=inside&op=furniture&op2=bed&dwid=$dwid");
	}
?>