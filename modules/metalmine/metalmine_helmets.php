<?php
function metalmine_helmets(){
	$allprefs=unserialize(get_module_pref('allprefs'));
	$helmet1=get_module_setting("helmet1");
	$helmet2=get_module_setting("helmet2");
	$helmet3=get_module_setting("helmet3");
	addnav("General Store");
	output("`n`c`b`&General `)Store`0`c`b`n");
	$helmet=$allprefs['helmet'];
	if ($helmet==1) $type="general";
	elseif ($helmet==2) $type="standard";
	elseif ($helmet==3) $type="quality";
	output("`Q'I have 3 main types of Helmets.  You got your Basic run-o-the-mill helmet.  It protects you well, but the light isn't quite as bright and it may be a little more difficult to see.  That costs `^%s gold`Q.",$helmet1);
	output("You get your mid-range helmet, the Standard Helmet, for `^%s gold`Q.",$helmet2);
	output("Finally, your quality helmet will set you back `^%s gold`Q. In addition, your helmet comes pre-filled with oil. If your helmet ever runs out of oil I can supply that to you for only `^5 gold`Q.'",$helmet3);
	output("`n`n`0You take a look at the helmets.");
	if  ($helmet>0) {
		output("Currently you have a `#%s helmet`0.",$type);
		if ($helmet<3) output("You could upgrade to a better one if you'd like.");
		else output("Grober says `Q'I'm sorry, we don't have anything better than the helmet you already have.'`0");
	}
	else output("You're not sure what the difference is, but you will probably need one to get anywhere in the mine.");
	if ($helmet<3){
		addnav("Purchase");
		if ($helmet==0) addnav("Purchase a Basic Helmet","runmodule.php?module=metalmine&op=purchase&op2=helmet&op3=1");
		if ($helmet<=1) addnav("Purchase a Standard Helmet","runmodule.php?module=metalmine&op=purchase&op2=helmet&op3=2");
		if ($helmet<=2) addnav("Purchase a Quality Helmet","runmodule.php?module=metalmine&op=purchase&op2=helmet&op3=3");
		addnav("Other");
	}
	blocknav("runmodule.php?module=metalmine&op=helmets");
	metalmine_storenavs();
}
?>