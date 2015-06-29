<?php
function dwcostmithril_getmoduleinfo(){
	$info = array(
		"name"=>"Dwellings Cost Mithril",
		"version"=>"5.01",
		"download"=>"",
		"author"=>"Sixf00t4, Modified by DaveS",
		"category"=>"Dwellings",
		"description"=>"Allows dwellings to cost mithril",
		"requires"=>array(
		   "dwellings"=>"20051118|By Sixf00t4, available on DragonPrime",
		   "metalmine"=>"5.0|By DaveS, available on DragonPrime",
		), 
		"prefs-dwellings"=>array(
			"Dwelling Cost mithril,title",
			"mithrilspent"=>"How many grams of mithril have they spent on this dwelling?,int|0",
		),	
		"prefs-dwellingtypes"=>array(
			"Dwelling Type Prefs,title",
			"mithrilcost"=>"How many grams of mithril does it cost to buy this type of dwelling?,int|1500",
		),
	);
		return $info;
}
function dwcostmithril_install(){
	module_addhook("dwellings-pay-costs"); 
	module_addhook("dwellings-pay-input");
	module_addhook("dwellings-buy-setup");
	module_addhook("dwellings-buy-valuecheck");	
	return true;
}

function dwcostmithril_uninstall() {
	return true;
}
function dwcostmithril_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
		case "dwellings-buy-valuecheck":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidmithril = abs((int)httppost('paidmithril'));
			if ($paidmithril < 0) $paidmithril = 0;
			$allprefs=unserialize(get_module_pref('allprefs','metalmine'));
			$mithrilavailable = $allprefs['metal3'];
			if($mithrilavailable<$paidmithril){
				$args['allowpay']=0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou do not have that much mithril.");
			}elseif($paidmithril>(get_module_objpref("dwellingtypes",$typeid,"mithrilcost")-get_module_objpref("dwellings",$args['dwid'],"mithrilspent"))){
				$args['allowpay']=0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou have tried to spend more mithril than you need to.");
			}
		break;
		case "dwellings-buy-setup":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidmithril = abs((int)httppost('paidmithril'));
			if ($paidmithril < 0) $paidmithril = 0;
			$mithrilspent=get_module_objpref("dwellings",$args['dwid'],"mithrilspent")+$paidmithril;				
			set_module_objpref("dwellings",$args['dwid'],"mithrilspent",$mithrilspent);
			$allprefs=unserialize(get_module_pref('allprefs','metalmine'));
			$allprefs['metal3']=$allprefs['metal3']-$paidmithril;
			set_module_pref('allprefs',serialize($allprefs),'metalmine');
			if($mithrilspent<get_module_objpref("dwellingtypes",$typeid,"mithrilcost")){
				$args['finished']=0;			
			}
		break;
		case "dwellings-pay-costs":
			$typeid=get_module_setting("typeid",$args['type']);
			$costmithril=get_module_objpref("dwellingtypes",$typeid,"mithrilcost") - get_module_objpref("dwellings",$args['dwid'],"mithrilspent");
			if($costmithril) output("`&%s Grams of Mithril`n`0",$costmithril);
		break;
		case "dwellings-pay-input":
			$typeid= get_module_setting("typeid",$args['type']);
			$costmithril=get_module_objpref("dwellingtypes",$typeid,"mithrilcost") - get_module_objpref("dwellings",$args['dwid'],"mithrilspent");
			$mithril = translate_inline("Mithril");
			if($costmithril) rawoutput("$mithril: <input id='input' name='paidmithril' width=5><br>");		
		break;		
	}
	return $args;
}
?>