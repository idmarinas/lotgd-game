<?php
function quarry_rules(){
	villagenav();
	$ruler=get_module_setting("ruler");
	$allprefs=unserialize(get_module_pref('allprefs'));
	if (is_module_active('lostruins') && get_module_setting("usequarry")==0) output("`n`c`b`@T`3he %s `@Q`3uarry `@R`3ules`c`b`n",get_module_setting("quarryfinder"));
	else output("`n`c`b`@T`3he `@Q`3uarry `@R`3ules`c`b`n");
	output("`&Slatemaker `\$S`7h`&y`\$l`7l`&e`% takes you over to the board showing `@T`3he `@R`3ules`%. In large print you read the following:`n`n");
	output("`#1. NO Running!  This is not a playground.  `@T`#he `@Q`#uarry can be deadly.  Please be careful at all times.`n`n");
	output("`32. Hardhats must be worn at all times.`n`n");
	output("`#3. Due to the dangers, consider purchasing insurance at the office.  It's available every day.`n`n");
	output("`34. You may work the `@Q`3uarry up to`& %s times a day`3.`n`n",get_module_setting("quarryturns"));
	output("`#5. Your goal is to quarry`) Blocks of Stone`#.  You can complete `)one block`# in `@one turn`#.`n`n");
	output("`36. All of your work is carefully tracked and an updated ledger is available for your review at the office.`n`n");
	output("`#7. Stone may be sold at the office to `&Slatemaker `\$S`7h`&y`\$l`7l`&e`#.  Please see her for the current price being offered.`n`n");
	output("`38. Stone that is quarried here may have other uses.  Please check with `&%s`3 or his `&Staff`3.`n`n",$ruler);
	if (is_module_active('lostruins') && get_module_setting("usequarry")==0) output("`#9. The Quarry is probably going to run out of quality stone at some time. Please check at the office for when this may happen.`n`n");
	if (get_module_setting("leveladj")==1 || get_module_setting("levelreq")>1 || get_module_setting("maximumsell")>0) output("`&`n`n`b`cCalculation of Stone Reimbursement:`c`b");
	if (get_module_setting("leveladj")==1) output("`nPay for stone is based on your current level.  The higher your level, the higher price you'll be able to negotiate for your stone.`n");
	if (get_module_setting("levelreq")>1) output("`nYou will need to be at least `^level %s`& to sell your stone.`n",get_module_setting("levelreq"));
	if (get_module_setting("maximumsell")>0) Output("`nYou may sell up to `^%s`& %s of stone per day.`n",get_module_setting("maximumsell"),translate_inline(get_module_setting("maximumsell")>1?"blocks":"block"));
	if ($allprefs['usedqts']<get_module_setting("quarryturns")) addnav("Work the Quarry","runmodule.php?module=quarry&op=work");
	addnav("Office","runmodule.php?module=quarry&op=office");
}
?>