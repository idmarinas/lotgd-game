<?php
function dwcoststone_getmoduleinfo(){
	$info = array(
		"name"=>"Dwellings Cost Stone",
		"version"=>"5.01",
		"download"=>"",
        "author"=>"Sixf00t4, DaveS Upgrade",
		"category"=>"Dwellings",
		"description"=>"Allows dwellings to cost stone",
		"requires"=>array(
		   "dwellings"=>"20051118|By Sixf00t4, available on DragonPrime",
		   "quarry"=>"5.0|By DaveS, available on DragonPrime",
		), 
		"prefs-dwellings"=>array(
			"Dwelling Cost Stone,title",
			"stonespent"=>"How much stone have they spent on this dwelling?,int|0",
		),	
		"prefs-dwellingtypes"=>array(
			"Dwelling Type Prefs,title",
			"stonecost"=>"How much stone does it cost to buy this type of dwelling?,int|5",
		),
	);
		return $info;
}
function dwcoststone_install(){
	module_addhook("dwellings-pay-costs"); 
	module_addhook("dwellings-pay-input");
	module_addhook("dwellings-buy-setup");
	module_addhook("dwellings-buy-valuecheck");	
	return true;
}
function dwcoststone_uninstall() {
	return true;
}
function dwcoststone_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
		case "dwellings-buy-valuecheck":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidstone = abs((int)httppost('paidstone'));
			if ($paidstone < 0) $paidstone = 0;
			$allprefs=unserialize(get_module_pref('allprefs','quarry'));
			$stoneavailable = $allprefs['blocks'];
			if($stoneavailable<$paidstone){
				$args['allowpay']=0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou do not have that much stone.");
			}elseif($paidstone>(get_module_objpref("dwellingtypes",$typeid,"stonecost")-get_module_objpref("dwellings",$args['dwid'],"stonespent"))){
				$args['allowpay']=0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou have tried to spend more stone than you need to.");
			}
		break;
		case "dwellings-buy-setup":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidstone = abs((int)httppost('paidstone'));
			if ($paidstone < 0) $paidstone = 0;
			$stonespent=get_module_objpref("dwellings",$args['dwid'],"stonespent")+$paidstone;				
			set_module_objpref("dwellings",$args['dwid'],"stonespent",$stonespent);
			$allprefs=unserialize(get_module_pref('allprefs','quarry'));
			$allprefs['blocks']=$allprefs['blocks']-$paidstone;
			set_module_pref('allprefs',serialize($allprefs),'quarry');
			if($stonespent<get_module_objpref("dwellingtypes",$typeid,"stonecost")){
				$args['finished']=0;			
			}
		break;
		case "dwellings-pay-costs":
			$typeid=get_module_setting("typeid",$args['type']);
			$coststone=get_module_objpref("dwellingtypes",$typeid,"stonecost") - get_module_objpref("dwellings",$args['dwid'],"stonespent");
			if($coststone) output("%s Stone`n",$coststone);
		break;
		case "dwellings-pay-input":
			$typeid= get_module_setting("typeid",$args['type']);
			$coststone=get_module_objpref("dwellingtypes",$typeid,"stonecost") - get_module_objpref("dwellings",$args['dwid'],"stonespent");
			$stone = translate_inline("Stone");
			if($coststone) rawoutput("$stone: <input id='input' name='paidstone' width=5><br>");		
		break;		
	}
	return $args;
}
?>