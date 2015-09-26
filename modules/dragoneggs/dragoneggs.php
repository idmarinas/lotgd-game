<?php
	global $session;
	$op = httpget("op");
	$op2 = httpget("op2");
	$op3 = httpget("op3");
	page_header("Dragon Egg Research");
if ($op=="retainer"){
	page_header("Retainers");
	output("`c`b`^Retainers`b`c`n");
	output("Retainers are a nice cushion for those lucky enough to obtain one.`n`nIf you get one, then once a system day you may receive a small stipend. If you have a lucky day, it will be more than the standard amount. On an unlucky day, you won't get anything.  If it's a REALLY bad day, you'll lose the retainer.`n`n");
	output("After all, nothing lasts for ever, does it?`n`nAnd how much is the standard retainer? Ah, you'll have to get one to find that out!");
	villagenav();
}elseif ($op=="explain"){
	page_header("Dragon Egg Research");
	output("`c`b`^Research`b`c`n");
	output("One of the keys to defeating the `@Green Dragon`^ is to try and prevent future dragons from taking her place. The key to this is to destroy every dragon egg in the kingdom. However, it takes a lot of work to not only find the eggs but to destroy them.");
	output("`n`nEach day you will have `&%s Research Turns`^ that you can use.",get_module_setting("research"));
	output("As you become a greater warrior, you will learn where you want to look around and where you want to focus your time."); 
	output("`n`nEach system new day you will get a whole new set of research turns. As you kill more of the `@Green Dragons`^ you will find you can research at more and more locations.");
	output("In addition, some locations can only be researchd by using Lodge Points.");
	output("`n`nMany of the encounters are easier at higher levels.  Don't get discouraged if you notice failure earlier on.`n`n");
	output("`b`cAbout `@Link `^C`!o`Ql`@o`^r`!s`b`c`n");
	output("`^You will notice that the text around the Dragon Egg Research Link may vary in different locations.");
	output("The color will indicate your ability to research at this location.");
	output("`n`n`@If a link is `bGreen`b you can research there right away.");
	output("`n`^If a link is `bYellow`b you won't be able to research at that location until you've killed more `@Green Dragons`^.  Some locations can take many kills before you can access them, so keep checking back!");
	output("`n`!If a link is `bBlue`b you won't be able to research there unless you purchase a Research Pass at the Lodge. See the Hunter's Lodge for further details.");
	output("`n`QIf a link is `bOrange`b you will need both more `@Green Dragon`Q kills in addition to purchasing access through the Lodge.");
	output("`n`&If a link is `bWhite`b then the location is available for all players to visit today.");
	villagenav();
}elseif ($op=="exchange"){
	require_once("modules/dragoneggs/dragoneggs_exchange.php");
	dragoneggs_exchange();
}elseif ($op=="lodge"){
	require_once("modules/dragoneggs/dragoneggs_lodge.php");
	dragoneggs_lodge();
}elseif ($op=="hospital"){
	require_once("modules/dragoneggs/dragoneggs_hospital.php");
	dragoneggs_hospital();
}elseif ($op=="hospital1"){
	require_once("modules/dragoneggs/dragoneggs_hospital1.php");
	dragoneggs_hospital1();
}elseif ($op=="hospital9"){
	require_once("modules/dragoneggs/dragoneggs_hospital9.php");
	dragoneggs_hospital9();
}elseif ($op=="hospital13"){
	require_once("modules/dragoneggs/dragoneggs_hospital13.php");
	dragoneggs_hospital13();
}elseif ($op=="bank"){
	require_once("modules/dragoneggs/dragoneggs_bank.php");
	dragoneggs_bank();
}elseif ($op=="bank5"){
	require_once("modules/dragoneggs/dragoneggs_bank5.php");
	dragoneggs_bank5();
}elseif ($op=="bank7"){
	require_once("modules/dragoneggs/dragoneggs_bank7.php");
	dragoneggs_bank7();
}elseif ($op=="bank21"){
	require_once("modules/dragoneggs/dragoneggs_bank21.php");
	dragoneggs_bank21();
}elseif ($op=="bank23"){
	require_once("modules/dragoneggs/dragoneggs_bank23.php");
	dragoneggs_bank23();
}elseif ($op=="bank25"){
	require_once("modules/dragoneggs/dragoneggs_bank25.php");
	dragoneggs_bank25();
}elseif ($op=="bank27"){
	require_once("modules/dragoneggs/dragoneggs_bank27.php");
	dragoneggs_bank27();
}elseif ($op=="train"){
	require_once("modules/dragoneggs/dragoneggs_train.php");
	dragoneggs_train();
}elseif ($op=="train3"){
	require_once("modules/dragoneggs/dragoneggs_train3.php");
	dragoneggs_train3();
}elseif ($op=="train7"){
	require_once("modules/dragoneggs/dragoneggs_train7.php");
	dragoneggs_train7();
}elseif ($op=="train9"){
	require_once("modules/dragoneggs/dragoneggs_train9.php");
	dragoneggs_train9();
}elseif ($op=="inn"){
	require_once("modules/dragoneggs/dragoneggs_inn.php");
	dragoneggs_inn();
}elseif ($op=="inn1"){
	require_once("modules/dragoneggs/dragoneggs_inn1.php");
	dragoneggs_inn1();
}elseif ($op=="inn15"){
	require_once("modules/dragoneggs/dragoneggs_inn15.php");
	dragoneggs_inn15();
}elseif ($op=="sanctum"){
	require_once("modules/dragoneggs/dragoneggs_sanctum.php");
	dragoneggs_sanctum();
}elseif ($op=="sanctum3"){
	require_once("modules/dragoneggs/dragoneggs_sanctum3.php");
	dragoneggs_sanctum3();
}elseif ($op=="sanctum15"){
	require_once("modules/dragoneggs/dragoneggs_sanctum15.php");
	dragoneggs_sanctum15();
}elseif ($op=="sanctum21"){
	require_once("modules/dragoneggs/dragoneggs_sanctum21.php");
	dragoneggs_sanctum21();
}elseif ($op=="sanctum23"){
	require_once("modules/dragoneggs/dragoneggs_sanctum23.php");
	dragoneggs_sanctum23();
}elseif ($op=="sanctum235"){
	require_once("modules/dragoneggs/dragoneggs_sanctum235.php");
	dragoneggs_sanctum235();
}elseif ($op=="sanctum27"){
	require_once("modules/dragoneggs/dragoneggs_sanctum27.php");
	dragoneggs_sanctum27();
}elseif ($op=="witch"){
	require_once("modules/dragoneggs/dragoneggs_witch.php");
	dragoneggs_witch();
}elseif ($op=="witch25"){
	require_once("modules/dragoneggs/dragoneggs_witch25.php");
	dragoneggs_witch25();
}elseif ($op=="town"){
	require_once("modules/dragoneggs/dragoneggs_town.php");
	dragoneggs_town();
}elseif ($op=="town1"){
	require_once("modules/dragoneggs/dragoneggs_town1.php");
	dragoneggs_town1();
}elseif ($op=="town1b"){
	require_once("modules/dragoneggs/dragoneggs_town1b.php");
	dragoneggs_town1b();
}elseif ($op=="town13"){
	require_once("modules/dragoneggs/dragoneggs_town13.php");
	dragoneggs_town13();
}elseif ($op=="town15"){
	require_once("modules/dragoneggs/dragoneggs_town15.php");
	dragoneggs_town15();
}elseif ($op=="town17"){
	require_once("modules/dragoneggs/dragoneggs_town17.php");
	dragoneggs_town17();
}elseif ($op=="police"){
	require_once("modules/dragoneggs/dragoneggs_police.php");
	dragoneggs_police();
}elseif ($op=="police5"){
	require_once("modules/dragoneggs/dragoneggs_police5.php");
	dragoneggs_police5();
}elseif ($op=="police13"){
	require_once("modules/dragoneggs/dragoneggs_police13.php");
	dragoneggs_police13();
}elseif ($op=="police23"){
	require_once("modules/dragoneggs/dragoneggs_police23.php");
	dragoneggs_police23();
}elseif ($op=="police25"){
	require_once("modules/dragoneggs/dragoneggs_police25.php");
	dragoneggs_police25();
}elseif ($op=="weapons"){
	require_once("modules/dragoneggs/dragoneggs_weapons.php");
	dragoneggs_weapons();
}elseif ($op=="weapons1"){
	require_once("modules/dragoneggs/dragoneggs_weapons1.php");
	dragoneggs_weapons1();
}elseif ($op=="weapons3"){
	require_once("modules/dragoneggs/dragoneggs_weapons3.php");
	dragoneggs_weapons3();
}elseif ($op=="weapons13"){
	require_once("modules/dragoneggs/dragoneggs_weapons13.php");
	dragoneggs_weapons13();
}elseif ($op=="weapons21"){
	require_once("modules/dragoneggs/dragoneggs_weapons21.php");
	dragoneggs_weapons21();
}elseif ($op=="weapons25"){
	require_once("modules/dragoneggs/dragoneggs_weapons25.php");
	dragoneggs_weapons25();
}elseif ($op=="diner"){
	require_once("modules/dragoneggs/dragoneggs_diner.php");
	dragoneggs_diner();
}elseif ($op=="diner1"){
	require_once("modules/dragoneggs/dragoneggs_diner1.php");
	dragoneggs_diner1();
}elseif ($op=="diner5"){
	require_once("modules/dragoneggs/dragoneggs_diner5.php");
	dragoneggs_diner5();
}elseif ($op=="diner15"){
	require_once("modules/dragoneggs/dragoneggs_diner15.php");
	dragoneggs_diner15();
}elseif ($op=="diner17"){
	require_once("modules/dragoneggs/dragoneggs_diner17.php");
	dragoneggs_diner17();
}elseif ($op=="diner21"){
	require_once("modules/dragoneggs/dragoneggs_diner21.php");
	dragoneggs_diner21();
}elseif ($op=="diner27"){
	require_once("modules/dragoneggs/dragoneggs_diner27.php");
	dragoneggs_diner27();
}elseif ($op=="gypsy"){
	require_once("modules/dragoneggs/dragoneggs_gypsy.php");
	dragoneggs_gypsy();
}elseif ($op=="gypsy15"){
	require_once("modules/dragoneggs/dragoneggs_gypsy15.php");
	dragoneggs_gypsy15();
}elseif ($op=="gypsy25"){
	require_once("modules/dragoneggs/dragoneggs_gypsy25.php");
	dragoneggs_gypsy25();
}elseif ($op=="jewelry"){
	require_once("modules/dragoneggs/dragoneggs_jewelry.php");
	dragoneggs_jewelry();
}elseif ($op=="jewelry3"){
	require_once("modules/dragoneggs/dragoneggs_jewelry3.php");
	dragoneggs_jewelry3();
}elseif ($op=="jewelry7"){
	require_once("modules/dragoneggs/dragoneggs_jewelry7.php");
	dragoneggs_jewelry7();
}elseif ($op=="jewelry17"){
	require_once("modules/dragoneggs/dragoneggs_jewelry17.php");
	dragoneggs_jewelry17();
}elseif ($op=="jewelry21"){
	require_once("modules/dragoneggs/dragoneggs_jewelry21.php");
	dragoneggs_jewelry21();
}elseif ($op=="armor"){
	require_once("modules/dragoneggs/dragoneggs_armor.php");
	dragoneggs_armor();
}elseif ($op=="armor1"){
	require_once("modules/dragoneggs/dragoneggs_armor1.php");
	dragoneggs_armor1();
}elseif ($op=="armor5"){
	require_once("modules/dragoneggs/dragoneggs_armor5.php");
	dragoneggs_armor5();
}elseif ($op=="armor7"){
	require_once("modules/dragoneggs/dragoneggs_armor7.php");
	dragoneggs_armor7();
}elseif ($op=="armor9"){
	require_once("modules/dragoneggs/dragoneggs_armor9.php");
	dragoneggs_armor9();
}elseif ($op=="armor11"){
	require_once("modules/dragoneggs/dragoneggs_armor11.php");
	dragoneggs_armor11();
}elseif ($op=="armor21"){
	require_once("modules/dragoneggs/dragoneggs_armor21.php");
	dragoneggs_armor21();
}elseif ($op=="armor25"){
	require_once("modules/dragoneggs/dragoneggs_armor25.php");
	dragoneggs_armor25();
}elseif ($op=="tattoo"){
	require_once("modules/dragoneggs/dragoneggs_tattoo.php");
	dragoneggs_tattoo();
}elseif ($op=="tattoo3"){
	require_once("modules/dragoneggs/dragoneggs_tattoo3.php");
	dragoneggs_tattoo3();
}elseif ($op=="tattoo9"){
	require_once("modules/dragoneggs/dragoneggs_tattoo9.php");
	dragoneggs_tattoo9();
}elseif ($op=="tattoo19"){
	require_once("modules/dragoneggs/dragoneggs_tattoo19.php");
	dragoneggs_tattoo19();
}elseif ($op=="tattoo21"){
	require_once("modules/dragoneggs/dragoneggs_tattoo21.php");
	dragoneggs_tattoo21();
}elseif ($op=="tattoo25"){
	require_once("modules/dragoneggs/dragoneggs_tattoo25.php");
	dragoneggs_tattoo25();
}elseif ($op=="magic"){
	require_once("modules/dragoneggs/dragoneggs_magic.php");
	dragoneggs_magic();
}elseif ($op=="magic3"){
	require_once("modules/dragoneggs/dragoneggs_magic3.php");
	dragoneggs_magic3();
}elseif ($op=="magic7"){
	require_once("modules/dragoneggs/dragoneggs_magic7.php");
	dragoneggs_magic7();
}elseif ($op=="magic15"){
	require_once("modules/dragoneggs/dragoneggs_magic15.php");
	dragoneggs_magic15();
}elseif ($op=="magic17"){
	require_once("modules/dragoneggs/dragoneggs_magic17.php");
	dragoneggs_magic17();
}elseif ($op=="magic19"){
	require_once("modules/dragoneggs/dragoneggs_magic19.php");
	dragoneggs_magic19();
}elseif ($op=="magic26"){
	require_once("modules/dragoneggs/dragoneggs_magic26.php");
	dragoneggs_magic26();
}elseif ($op=="heidi"){
	require_once("modules/dragoneggs/dragoneggs_heidi.php");
	dragoneggs_heidi();
}elseif ($op=="heidi1"){
	require_once("modules/dragoneggs/dragoneggs_heidi1.php");
	dragoneggs_heidi1();
}elseif ($op=="heidi3"){
	require_once("modules/dragoneggs/dragoneggs_heidi3.php");
	dragoneggs_heidi3();
}elseif ($op=="heidi9"){
	require_once("modules/dragoneggs/dragoneggs_heidi9.php");
	dragoneggs_heidi9();
}elseif ($op=="heidi13"){
	require_once("modules/dragoneggs/dragoneggs_heidi13.php");
	dragoneggs_heidi13();
}elseif ($op=="heidi15"){
	require_once("modules/dragoneggs/dragoneggs_heidi15.php");
	dragoneggs_heidi15();
}elseif ($op=="heidi17"){
	require_once("modules/dragoneggs/dragoneggs_heidi17.php");
	dragoneggs_heidi17();
}elseif ($op=="heidi21"){
	require_once("modules/dragoneggs/dragoneggs_heidi21.php");
	dragoneggs_heidi21();
}elseif ($op=="heidi25"){
	require_once("modules/dragoneggs/dragoneggs_heidi25.php");
	dragoneggs_heidi25();
}elseif ($op=="animal"){
	require_once("modules/dragoneggs/dragoneggs_animal.php");
	dragoneggs_animal();
}elseif ($op=="animal3"){
	require_once("modules/dragoneggs/dragoneggs_animal3.php");
	dragoneggs_animal3();
}elseif ($op=="animal13"){
	require_once("modules/dragoneggs/dragoneggs_animal13.php");
	dragoneggs_animal13();
}elseif ($op=="animal21"){
	require_once("modules/dragoneggs/dragoneggs_animal21.php");
	dragoneggs_animal21();
}elseif ($op=="animal25"){
	require_once("modules/dragoneggs/dragoneggs_animal25.php");
	dragoneggs_animal25();
}elseif ($op=="gardens"){
	require_once("modules/dragoneggs/dragoneggs_gardens.php");
	dragoneggs_gardens();
}elseif ($op=="gardens3"){
	require_once("modules/dragoneggs/dragoneggs_gardens3.php");
	dragoneggs_gardens3();
}elseif ($op=="gardens25"){
	require_once("modules/dragoneggs/dragoneggs_gardens25.php");
	dragoneggs_gardens25();
}elseif ($op=="rock"){
	require_once("modules/dragoneggs/dragoneggs_rock.php");
	dragoneggs_rock();
}elseif ($op=="rock5"){
	require_once("modules/dragoneggs/dragoneggs_rock5.php");
	dragoneggs_rock5();
}elseif ($op=="rock7"){
	require_once("modules/dragoneggs/dragoneggs_rock7.php");
	dragoneggs_rock7();
}elseif ($op=="rock13"){
	require_once("modules/dragoneggs/dragoneggs_rock13.php");
	dragoneggs_rock13();
}elseif ($op=="rock25"){
	require_once("modules/dragoneggs/dragoneggs_rock25.php");
	dragoneggs_rock25();
}elseif ($op=="historical"){
	require_once("modules/dragoneggs/dragoneggs_historical.php");
	dragoneggs_historical();
}elseif ($op=="historical3"){
	require_once("modules/dragoneggs/dragoneggs_historical3.php");
	dragoneggs_historical3();
}elseif ($op=="historical5"){
	require_once("modules/dragoneggs/dragoneggs_historical5.php");
	dragoneggs_historical5();
}elseif ($op=="historical13"){
	require_once("modules/dragoneggs/dragoneggs_historical13.php");
	dragoneggs_historical13();
}elseif ($op=="historical17"){
	require_once("modules/dragoneggs/dragoneggs_historical17.php");
	dragoneggs_historical17();
}elseif ($op=="historical23"){
	require_once("modules/dragoneggs/dragoneggs_historical23.php");
	dragoneggs_historical23();
}elseif ($op=="historical25"){
	require_once("modules/dragoneggs/dragoneggs_historical25.php");
	dragoneggs_historical25();
}elseif ($op=="church"){
	require_once("modules/dragoneggs/dragoneggs_church.php");
	dragoneggs_church();
}elseif ($op=="church3"){
	require_once("modules/dragoneggs/dragoneggs_church3.php");
	dragoneggs_church3();
}elseif ($op=="church5"){
	require_once("modules/dragoneggs/dragoneggs_church5.php");
	dragoneggs_church5();
}elseif ($op=="church9"){
	require_once("modules/dragoneggs/dragoneggs_church9.php");
	dragoneggs_church9();
}elseif ($op=="church11"){
	require_once("modules/dragoneggs/dragoneggs_church11.php");
	dragoneggs_church11();
}elseif ($op=="church17"){
	require_once("modules/dragoneggs/dragoneggs_church17.php");
	dragoneggs_church17();
}elseif ($op=="news"){
	require_once("modules/dragoneggs/dragoneggs_news.php");
	dragoneggs_news();
}elseif ($op=="news7"){
	require_once("modules/dragoneggs/dragoneggs_news7.php");
	dragoneggs_news7();
}elseif ($op=="news21"){
	require_once("modules/dragoneggs/dragoneggs_news21.php");
	dragoneggs_news21();
}elseif ($op=="docks"){
	require_once("modules/dragoneggs/dragoneggs_docks.php");
	dragoneggs_docks();
}elseif ($op=="docks5"){
	require_once("modules/dragoneggs/dragoneggs_docks5.php");
	dragoneggs_docks5();
}elseif ($op=="docks7"){
	require_once("modules/dragoneggs/dragoneggs_docks7.php");
	dragoneggs_docks7();
}elseif ($op=="docks15"){
	require_once("modules/dragoneggs/dragoneggs_docks15.php");
	dragoneggs_docks15();
}elseif ($op=="docks17"){
	require_once("modules/dragoneggs/dragoneggs_docks17.php");
	dragoneggs_docks17();
}elseif ($op=="docks19"){
	require_once("modules/dragoneggs/dragoneggs_docks19.php");
	dragoneggs_docks19();
}elseif ($op=="docks21"){
	require_once("modules/dragoneggs/dragoneggs_docks21.php");
	dragoneggs_docks21();
}elseif ($op=="bath"){
	require_once("modules/dragoneggs/dragoneggs_bath.php");
	dragoneggs_bath();
}elseif ($op=="bath1"){
	require_once("modules/dragoneggs/dragoneggs_bath1.php");
	dragoneggs_bath1();
}elseif ($op=="bath3"){
	require_once("modules/dragoneggs/dragoneggs_bath3.php");
	dragoneggs_bath3();
}elseif ($op=="bath11"){
	require_once("modules/dragoneggs/dragoneggs_bath11.php");
	dragoneggs_bath11();
}elseif ($op=="bath13"){
	require_once("modules/dragoneggs/dragoneggs_bath13.php");
	dragoneggs_bath13();
}elseif ($op=="bath25"){
	require_once("modules/dragoneggs/dragoneggs_bath25.php");
	dragoneggs_bath25();
}elseif ($op=="library"){
	require_once("modules/dragoneggs/dragoneggs_library.php");
	dragoneggs_library();
}elseif ($op=="library15"){
	require_once("modules/dragoneggs/dragoneggs_library15.php");
	dragoneggs_library15();
}elseif ($op=="library17"){
	require_once("modules/dragoneggs/dragoneggs_library17.php");
	dragoneggs_library17();
}
if ($op=="attack"){
	require_once("modules/dragoneggs/dragoneggs_attack.php");
	dragoneggs_attack();
	$op="fight";
}
if ($op=="fight"){ $battle=true; }
if ($battle){
	include("battle.php");
	if ($victory){
		require_once("modules/dragoneggs/dragoneggs_victory.php");
		dragoneggs_victory();
	}elseif($defeat){
		require_once("modules/dragoneggs/dragoneggs_defeat.php");
		dragoneggs_defeat();
	}else{
		require_once("lib/fightnav.php");
		fightnav(true,false,"runmodule.php?module=dragoneggs");
    }
}
page_footer();
?>