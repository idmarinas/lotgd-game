<?php
function dwcostwood_getmoduleinfo(){
	$info = array(
		"name"=>"Dwellings Cost Wood",
		"version"=>"5.01	",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		"author"=>"Sixf00t4, DaveS Upgrade",
		"category"=>"Dwellings",
		"description"=>"Allows dwellings to cost wood",
		"requires"=>array(
		   "dwellings"=>"20051118|By Sixf00t4, available on DragonPrime",
		   "lumberyard"=>"5.0|By DaveS, available on DragonPrime",
		), 
		"prefs-dwellings"=>array(
			"Dwelling Cost Wood,title",
			"woodspent"=>"How much wood have they spent on this dwelling?,int|0",
		),	
		"prefs-dwellingtypes"=>array(
			"Dwelling Type Prefs,title",
			"woodcost"=>"How much wood does it cost to buy this type of dwelling?,int|5",
		),
	);
		return $info;
}
function dwcostwood_install(){
	module_addhook("dwellings-pay-costs"); 
	module_addhook("dwellings-pay-input");
	module_addhook("dwellings-buy-setup");
	module_addhook("dwellings-buy-valuecheck");	
	return true;
}
function dwcostwood_uninstall() {
	return true;
}
function dwcostwood_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
		case "dwellings-buy-valuecheck":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidwood = abs((int)httppost('paidwood'));
			if ($paidwood < 0) $paidwood = 0;
			$allprefs=unserialize(get_module_pref('allprefs','lumberyard'));
			$woodavailable = $allprefs['squares'];
			if($woodavailable < $paidwood){
				$args['allowpay'] = 0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou do not have that much wood.");
			}elseif($paidwood > (get_module_objpref("dwellingtypes",$typeid,"woodcost")-get_module_objpref("dwellings",$args['dwid'],"woodspent"))){
				$args['allowpay'] = 0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou have tried to spend more wood than you need to.");
			}
		break;
		case "dwellings-buy-setup":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidwood = abs((int)httppost('paidwood'));
			if ($paidwood < 0) $paidwood = 0;
			$woodspent = get_module_objpref("dwellings",$args['dwid'],"woodspent")+$paidwood;				
			set_module_objpref("dwellings",$args['dwid'],"woodspent",$woodspent);
			$allprefs=unserialize(get_module_pref('allprefs','lumberyard'));
			$allprefs['squares']=$allprefs['squares']-$paidwood;
			set_module_pref('allprefs',serialize($allprefs),'lumberyard');	
			if($woodspent < get_module_objpref("dwellingtypes",$typeid,"woodcost")){
				$args['finished'] = 0;			
			}
		break;
		case "dwellings-pay-costs":
			$typeid = get_module_setting("typeid",$args['type']);
			$costwood = get_module_objpref("dwellingtypes",$typeid,"woodcost") - get_module_objpref("dwellings",$args['dwid'],"woodspent");
			if($costwood) output("`q%s Wood`n`0",$costwood);
		break;
		case "dwellings-pay-input":
			$typeid = get_module_setting("typeid",$args['type']);
			$costwood = get_module_objpref("dwellingtypes",$typeid,"woodcost") - get_module_objpref("dwellings",$args['dwid'],"woodspent");
			$wood = translate_inline("Wood");
			if($costwood) rawoutput("$wood: <input id='input' name='paidwood' width=5><br>");		
		break;		
	}
	return $args;
}
?>