<?php
function dragoneggs_magic3(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	$storename=get_module_setting('gsowner','pqgiftshop');
	$header = color_sanitize($storename);
	page_header("%s's Ol' Gifte Shoppe",$header);
	output("`c`b`&Ye Ol' Gifte Shoppe`b`c`7`n`n");
	output("You pay `^500 gold`7 and sneak away to open the box. You smile and look inside to find...`n`n");
	$session['user']['gold']-=500;
	$success=0;
	if (e_rand(1,2)==1) $success++;
	if ($session['user']['level']>5 && e_rand(1,3)==1) $success++;
	elseif ($session['user']['level']<=5 && e_rand(1,5)==1) $success++;
	if ($success==0){
		output("Nothing!! That completely sucks.");
		debuglog("spent 500 gold for an empty box while researching dragon eggs at Ye Ol' Gifte Shoppe.");
	}elseif ($success==1){
		$one=e_rand(1,6);
		$two=e_rand(1,6);
		$prize=($one+$two)*100;
		output("Excellent! It's full of `^%s gold`7!!",$prize);
		$session['user']['gold']+=$prize;
		debuglog("spent $prize gold for a box while researching dragon eggs at Ye Ol' Gifte Shoppe.");
	}else{
		output("You find a huge red ruby.  As you touch it, you feel a strange power shoot through you.");
		output("`n`nYou gain `&1 defense`7 and `&1 attack`7.");
		$session['user']['attack']++;
		$session['user']['defense']++;
		debuglog("gained an attack and defense from a box while researching dragon eggs at Ye Ol' Gifte Shoppe.");
	}
	addnav(array("Return to %s's Gift Shop",get_module_setting('gsowner','pqgiftshop')),"runmodule.php?module=pqgiftshop");
	villagenav();
}
?>