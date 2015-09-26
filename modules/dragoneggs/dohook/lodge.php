<?php
if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
	$innname=getsetting("innname", LOCATION_INN);
}else{
	$innname=translate_inline("The Boar's Head Inn");
}
if (is_module_active("pqgiftshop")) $gift=get_module_setting('gsowner','pqgiftshop')."'s Gift Shop";
else $gift=translate_inline("Gift Shop");
$array=array("","heal","bank","uni","inn","witch","hof","police","weapons","armor","diner","gypsy","heidi","library","jewelry","tattoo","magic","animal","gardens","rock","church","news","docks","bath");
$name=translate_inline(array("","Healer's Hut","Ye Olde Bank","Bluspring's Warrior Training",$innname,"Old House","Hall of Fame","Jail","MightyE's Weapons","Pegasus Armor","Hara's Bakery","Ze Gypsy Tent","Heidi's Place","Library","Oliver's Jewelry","Petra's Tattoo Parlor",$gift,"Merick's Stables","Gardens","Curious Looking Rock","Church","Daily News","The Docks","Outhouse"));
if ($session['user']['dragonkills']>=get_module_setting("mindk")){
	for ($i=1;$i<=23;$i++) {
		$loc=$array[$i];
		$place=$name[$i];
		$cost=get_module_setting($loc."lodge");
		$open=1;
		if (is_module_active("oldhouse")==0 && $i==5) $open=0;
		elseif (is_module_active("jail")==0 && is_module_active("djail")==0 && $i==7) $open=0;
		elseif (is_module_active("bakery")==0 && $i==10) $open=0;
		elseif (is_module_active("heidi")==0 && $i==12) $open=0;
		elseif (is_module_active("dlibrary")==0 && is_module_active("dlibrary")==0 && $i==13) $open=0;
		elseif (is_module_active("jeweler")==0 && $i==14) $open=0;
		elseif (is_module_active("petra")==0 && $i==15) $open=0;
		elseif (is_module_active("pqgiftshop")==0 && $i==16) $open=0;
		elseif (is_module_active("oldchurch")==0 && $i==20) $open=0;
		elseif ((is_module_active("docks")==0 || (is_module_active("docks")>0 && $session['user']['dragonkills']<get_module_setting("dockdks","docks"))) && (is_module_active("oceanquest")==0 || (is_module_active("oceanquest")>0 && $session['user']['dragonkills']<get_module_setting("dockdks","oceanquest"))) && $i==22) $open=0;
		elseif (is_module_active("outhouse")==0 && $i==23) $open=0;
		addnav("Use Points - Dragon Eggs Research");
		if ($cost>0 && get_module_pref($loc."access")==0 && $open==1){
			addnav(array("%s (%s Points)",$place,$cost),"runmodule.php?module=dragoneggs&op=lodge&op2=$i");
		}
	}
}
?>