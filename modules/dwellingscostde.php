<?php
function dwellingscostde_getmoduleinfo(){
	$info = array(
		"name"=>"Dwellings Cost Dragon Egg Points",
		"version"=>"1.0",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1362",
		"author"=>"Sixf00t4, DaveS Modification",
		"category"=>"Dwellings",
		"prefs-dwellings"=>array(
			"Dwelling Cost Dragon Egg Points,title",
			"despent"=>"How many dragon egg points have they spent on this dwelling?,int|0",
		),
		"prefs-dwellingtypes"=>array(
			"Dwelling Type Prefs,title",
			"decost"=>"How many dragon egg points does it cost to buy this type of dwelling?,int|0",
		),
		"requires"=>array(
			"dwellings"=>"20051118|By Sixf00t4, available on DragonPrime",
			"dragoneggs"=>"1.0|Dragon Eggs Expansion by DaveS",
		),
	);
		return $info;
}

function dwellingscostde_install(){
	module_addhook("dwellings-pay-costs"); 
	module_addhook("dwellings-pay-input");
	module_addhook("dwellings-buy-setup");
	module_addhook("dwellings-buy-valuecheck");	
	return true;
}

function dwellingscostde_uninstall() {
	return true;
}

function dwellingscostde_dohook($hookname,$args) {
	global $session;

	switch ($hookname) {

		case "dwellings-buy-valuecheck":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidde = abs((int)httppost('paidde'));
			if ($paidde < 0) $paidde = 0;
			$pointsavailable = get_module_pref("dragoneggs","dragoneggpoints");
			if($pointsavailable<$paidse){
				$args['allowpay']=0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou do not have that many dragon egg points.");
			}elseif($paidde>(get_module_objpref("dwellingtypes",$typeid,"decost")-get_module_objpref("dwellings",$args['dwid'],"despent"))){
				$args['allowpay']=0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou have tried to spend more dragon egg points than you need to.");
			}
		break;
		
		case "dwellings-buy-setup":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidde = abs((int)httppost('paidde'));
			if ($paidde < 0) $paidde = 0;
			$spent=get_module_objpref("dwellings",$args['dwid'],"despent")+$paidde;				
			set_module_objpref("dwellings",$args['dwid'],"despent",$spent);
			increment_module_pref("dragoneggs",-$paidde,"dragoneggpoints");
			if($spent!=get_module_objpref("dwellingtypes",$typeid,"decost")){
				$args['finished']=0;			
				}
		break;

		case "dwellings-pay-costs":
			$typeid=get_module_setting("typeid",$args['type']);
			$costsde=get_module_objpref("dwellingtypes",$typeid,"decost") - get_module_objpref("dwellings",$args['dwid'],"despent");
			if($costsde) output("`#%s Dragon Egg Points`0`n",$costsde);
		break;
		
		case "dwellings-pay-input":
			$typeid= get_module_setting("typeid",$args['type']);
			$costsde=get_module_objpref("dwellingtypes",$typeid,"decost") - get_module_objpref("dwellings",$args['dwid'],"despent");
			$de = translate_inline("Dragon Egg Points");
			if($costsde) rawoutput("$de: <input id='input' name='paidde' width=5><br>");		
		break;		
	}
	return $args;
}

function dwellingscostde_run(){}
?>