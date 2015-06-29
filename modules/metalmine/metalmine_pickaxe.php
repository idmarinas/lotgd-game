<?php
function metalmine_pickaxe(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	addnav("General Store");
	output("`n`c`b`&General `)Store`0`c`b`n");
	$pick=$allprefs['pickaxe'];
	$pickaxe1=get_module_setting("pickaxe1");
	$pickaxe2=get_module_setting("pickaxe2");
	$pickaxe3=get_module_setting("pickaxe3");
	if ($pick==1) $type=translate_inline("general");
	elseif ($pick==2) $type=translate_inline("standard");
	else $type=translate_inline("quality");
	output("`Q'I have 3 main types of Pickaxes.  You got your Basic run-o-the-mill pick-axe.  That costs `^%s gold`Q.",$pickaxe1);
	output("You get your mid-range pickaxe, the Standard Pickaxe, for `^%s gold`Q.",$pickaxe2);
	output("Finally, your quality pickaxe will set you back `^%s gold`Q.'",$pickaxe3);
	output("`n`n`0You take a look at the pickaxes.");
	if  ($pick>0) {
		output("Currently you have a `#%s pickaxe`0.",$type);
		if ($pick<3) output("You could upgrade to a better one if you'd like.");
		else output("Grober says `Q'I'm sorry, we don't have anything better than the pickaxe you already have.'`0");
	}
	else output("You're not sure what the difference is, but you know you need one to do any mining.");
	if ($pick<3){
		addnav("Purchase");
		if ($pick==0) addnav("Purchase a Basic Pickaxe","runmodule.php?module=metalmine&op=purchase&op2=pickaxe&op3=1");
		if ($pick<=1) addnav("Purchase a Standard Pickaxe","runmodule.php?module=metalmine&op=purchase&op2=pickaxe&op3=2");
		if ($pick<=2) addnav("Purchase a Quality Pickaxe","runmodule.php?module=metalmine&op=purchase&op2=pickaxe&op3=3");
		addnav("Other");
	}
	blocknav("runmodule.php?module=metalmine&op=pickaxe");
	metalmine_storenavs();
}
?>