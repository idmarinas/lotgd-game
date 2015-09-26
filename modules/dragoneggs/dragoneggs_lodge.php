<?php
function dragoneggs_lodge(){
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Hunter's Lodge");
	if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
		$innname=getsetting("innname", LOCATION_INN);
	}else{
		$innname=translate_inline("The Boar's Head Inn");
	}
	$gift=get_module_setting('gsowner','pqgiftshop')."'s Gift Shop";
	$vname = getsetting("villagename", LOCATION_FIELDS)." Square";
	$array=array("","heal","bank","uni","inn","witch","hof","police","weapons","armor","diner","gypsy","heidi","library","jewelry","tattoo","magic","animal","gardens","rock","oldchurch","news","docks","bath","square");
	$name=translate_inline(array("","Healer's Hut","Ye Olde Bank","Bluspring's Warrior Training",$innname,"Old House","Hall of Fame","Jail","MightyE's Weapons","Pegasus Armor","Hara's Bakery","Ze Gypsy Tent","Heidi's Place","Library","Oliver's Jewelry","Petra's Tattoo Parlor",$gift,"Merick's Stables","Gardens","Curious Looking Rock","Church","Daily News","The Docks","Outhouse",$vname));
	$loc=$array[$op2];
	$place=$name[$op2];
	$cost=get_module_setting($loc."lodge");
	output("`c`b`&%s Dragon Egg Research Pass`b`c`n`7",$place);
	if ($op3==""){
		$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
		output("J.c. Petersen checks the records. `&'I see you're interested in searching for Dragon Eggs at `#%s`&.'`7`n`n",$place);
		if (get_module_setting($loc."min")+get_module_setting("mindk")>$session['user']['dragonkills']){
			output("He frowns before continuing, `&'Unfortunately, you haven't killed enough `@Green Dragon Kills`& to research at `#%s`&.  There's no point in you wasting Lodge Points. I'm sorry.'",$place);
		}elseif ($pointsavailable <$cost){
			output("He looks at the book twice before looking up at you, `&'You don't have enough Lodge Points to purchase a Research Pass to `#%s`&.  You need `^%s`& points and you only have `^%s`& points.",$place,$cost,$pointsavailable);
		}else{
			$level=$session['user']['level'];
			output("He smiles before continuing,`& 'It looks like everything should be in order.  Just confirm your purchase and you'll be able to start researching at `#%s`& until you defeat the `bGreen Dragon`b.'",$place);
			if ($level>12) output("`n`n`&'I just want to warn you, since you're level `^%s`& already, you may not have a lot of time to research there.'",$level);
			addnav(array("Purchase Pass to %s (%s Points)",$place,$cost),"runmodule.php?module=dragoneggs&op=lodge&op2=$op2&op3=confirm");
		}
	}elseif ($op3=="confirm"){
		$session['user']['donationspent']+=$cost;
		set_module_pref($loc."access",1);
		output("J.C. Petersen hands you a pass to `#%s`7. `&'Be careful and good luck!'`7 he says.",$place);
	}
	addnav("Return to the Lodge","lodge.php");
}
?>