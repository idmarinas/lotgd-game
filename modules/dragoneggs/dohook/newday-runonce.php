<?php
	$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='researches' and modulename='dragoneggs'";
	db_query($sql);
	$sql = "update ".db_prefix("module_userprefs")." set value=2 where value=1 and setting='retainer' and modulename='dragoneggs'";
	db_query($sql);
	$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='sold' and modulename='dragoneggs'";
	db_query($sql);

if (getsetting("installer_version","-1")=="1.1.1 Dragonprime Edition"){
	$innname=getsetting("innname", LOCATION_INN);
}else{
	$innname=translate_inline("The Boar's Head Inn");
}

if (is_module_active("pqgiftshop")) $gift=get_module_setting('gsowner','pqgiftshop')."'s Gift Shop";
else $gift="Gift Shop";
$array=array("","heal","bank","uni","inn","witch","hof","police","weapons","armor","diner","gypsy","heidi","library","jewelry","tattoo","magic","animal","gardens","rock","church","news","docks","bath");
$name=translate_inline(array("","Healer's Hut","Ye Olde Bank","Bluspring's Warrior Training",$innname,"Old House","Hall of Fame","Jail","MightyE's Weapons","Pegasus Armor","Hara's Bakery","Ze Gypsy Tent","Heidi's Place","Library","Oliver's Jewelry","Petra's Tattoo Parlor",$gift,"Merick's Stables","Gardens","Curious Looking Rock","Church","Daily News","The Docks","Outhouse"));

//Allow for random opening of all locations
if (get_module_setting("allopen")<=0 && e_rand(1,get_module_setting("randomallopen"))==2) set_module_setting("allopen",1);
	
$open="";
for ($i=1;$i<=23;$i++) {
	$loc=$array[$i];
	if (get_module_setting("allopen")>0){
		set_module_setting($loc."open",1);
	}else{
		$active=1;
		if (is_module_active("oldhouse")==0 && $i==5) $active=0;
		elseif (is_module_active("jail")==0 && is_module_active("djail")==0 && $i==7) $active=0;
		elseif (is_module_active("bakery")==0 && $i==10) $active=0;
		elseif (is_module_active("hiedi")==0 && $i==12) $active=0;
		elseif (is_module_active("library")==0 && is_module_active("dlibrary")==0 && $i==13) $active=0;
		elseif (is_module_active("jeweler")==0 && $i==14) $active=0;
		elseif (is_module_active("petra")==0 && $i==15) $active=0;
		elseif (is_module_active("pqgiftshop")==0 && $i==16) $active=0;
		elseif (is_module_active("oldchurch")==0 && $i==20) $active=0;
		elseif (is_module_active("docks")==0 && is_module_active("oceanquest")==0 && $i==22) $active=0;
		elseif (is_module_active("outhouse")==0 && $i==23) $active=0;
		
		if ($active==1){
			$place=$name[$i];
			set_module_setting($loc."open",0);
			if (get_module_setting($loc."min")==0) $min=1;
			else $min=get_module_setting($loc."min");
			if (get_module_setting($loc."lodge")==0) $lodge=1;
			else $lodge=get_module_setting($loc."lodge");
			if ($min+round($lodge/10)>50) $chance=50;
			elseif ($min+round($lodge/10)<5) $chance=5;
			else $chance=$min+round($lodge/10);
			if (e_rand(1,$chance)==1 && (get_module_setting($loc."min")>0 || get_module_setting($loc."lodge")>0)){
				set_module_setting($loc."open",1);
				$open.="`n".$place;
			}
		}
	}
}
$mindk=get_module_setting("mindk");
$dkT = translate_inline($mindk>1?"Dragon Kills":"Dragon Kill");
if (get_module_setting("allopen")>0){
	if ($mindk>0) addnews("`&`bAll locations are open for Dragon Egg Research today if you have at least `^%s `@%s`&.`b",$mindk,$dkT,true);
	else addnews("`&`bAll locations are open for Dragon Egg Research today.`b",true);
	$sql1 = "update ".db_prefix("module_userprefs")." set value=1 where value=0 and setting='inform' and modulename='dragoneggs'";
	db_query($sql1);
	increment_module_setting("allopen",-1);
	output("`n`&`bAll locations are open for Dragon Egg Research today.`b`n");
	set_module_pref("inform",0);
}else{
	if ($open!="") {
		if ($mindk>0) addnews("`@The following locations are open for Dragon Egg Research by all warriors that have at least `^%s `@%s:`c`&%s`c",$mindk,$dkT,$open,true);
		else addnews("`@The following locations are open for Dragon Egg Research by all warriors today:`c`&%s`c",$open,true);
	}
	$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='inform' and modulename='dragoneggs'";
	db_query($sql);	
}
if (get_module_setting("left")>0) increment_module_setting("left",-1);
if (get_module_setting("townegg")==1){
	set_module_setting("townegg",0);
	set_module_setting("deserter","");
}
?>