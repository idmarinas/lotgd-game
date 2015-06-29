<?php
function dwcostcopper_getmoduleinfo(){
	$info = array(
		"name"=>"Dwellings Cost Copper",
		"version"=>"5.01",
		"download"=>"",
		"author"=>"Sixf00t4, Modified by DaveS",
		"category"=>"Dwellings",
		"description"=>"Allows dwellings to cost copper",
		"requires"=>array(
		   "dwellings"=>"20051118|By Sixf00t4, available on DragonPrime",
		   "metalmine"=>"5.0|By DaveS, available on DragonPrime",
		), 
		"prefs-dwellings"=>array(
			"Dwelling Cost copper,title",
			"copperspent"=>"How many grams of copper have they spent on this dwelling?,int|0",
		),	
		"prefs-dwellingtypes"=>array(
			"Dwelling Type Prefs,title",
			"coppercost"=>"How many grams of copper does it cost to buy this type of dwelling?,int|2500",
		),
	);
		return $info;
}
function dwcostcopper_install(){
	module_addhook("dwellings-pay-costs"); 
	module_addhook("dwellings-pay-input");
	module_addhook("dwellings-buy-setup");
	module_addhook("dwellings-buy-valuecheck");	
	return true;
}
function dwcostcopper_uninstall() {
	return true;
}
function dwcostcopper_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
		case "dwellings-buy-valuecheck":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidcopper = abs((int)httppost('paidcopper'));
			if ($paidcopper < 0) $paidcopper = 0;
			$allprefs=unserialize(get_module_pref('allprefs','metalmine'));
			$copperavailable = $allprefs['metal2'];
			if($copperavailable<$paidcopper){
				$args['allowpay']=0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou do not have that much copper.");
			}elseif($paidcopper>(get_module_objpref("dwellingtypes",$typeid,"coppercost")-get_module_objpref("dwellings",$args['dwid'],"copperspent"))){
				$args['allowpay']=0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou have tried to spend more copper than you need to.");
			}
		break;
		case "dwellings-buy-setup":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidcopper = abs((int)httppost('paidcopper'));
			if ($paidcopper < 0) $paidcopper = 0;
			$copperspent=get_module_objpref("dwellings",$args['dwid'],"copperspent")+$paidcopper;				
			set_module_objpref("dwellings",$args['dwid'],"copperspent",$copperspent);
			$allprefs=unserialize(get_module_pref('allprefs','metalmine'));
			$allprefs['metal2']=$allprefs['metal2']-$paidcopper;
			set_module_pref('allprefs',serialize($allprefs),'metalmine');
			if($copperspent<get_module_objpref("dwellingtypes",$typeid,"coppercost")){
				$args['finished']=0;			
			}
		break;
		case "dwellings-pay-costs":
			$typeid=get_module_setting("typeid",$args['type']);
			$costcopper=get_module_objpref("dwellingtypes",$typeid,"coppercost") - get_module_objpref("dwellings",$args['dwid'],"copperspent");
			if($costcopper) output("`Q%s Grams of Copper`n`0",$costcopper);
		break;
		case "dwellings-pay-input":
			$typeid= get_module_setting("typeid",$args['type']);
			$costcopper=get_module_objpref("dwellingtypes",$typeid,"coppercost") - get_module_objpref("dwellings",$args['dwid'],"copperspent");
			$copper = translate_inline("Copper");
			if($costcopper) rawoutput("$copper: <input id='input' name='paidcopper' width=5><br>");		
		break;		
	}
	return $args;
}
?>