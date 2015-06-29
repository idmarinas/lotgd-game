<?php
function dwcostiron_getmoduleinfo(){
	$info = array(
		"name"=>"Dwellings Cost Iron",
		"version"=>"5.01",
		"download"=>"",
		"author"=>"Sixf00t4, Modified by DaveS",
		"category"=>"Dwellings",
		"description"=>"Allows dwellings to cost iron",
		"requires"=>array(
		   "dwellings"=>"20051118|By Sixf00t4, available on DragonPrime",
		   "metalmine"=>"5.0|By DaveS, available on DragonPrime",
		), 
		"prefs-dwellings"=>array(
			"Dwelling Cost iron,title",
			"ironspent"=>"How many grams of iron have they spent on this dwelling?,int|0",
		),	
		"prefs-dwellingtypes"=>array(
			"Dwelling Type Prefs,title",
			"ironcost"=>"How many grams of iron does it cost to buy this type of dwelling?,int|1500",
		),
	);
		return $info;
}
function dwcostiron_install(){
	module_addhook("dwellings-pay-costs"); 
	module_addhook("dwellings-pay-input");
	module_addhook("dwellings-buy-setup");
	module_addhook("dwellings-buy-valuecheck");	
	return true;
}
function dwcostiron_uninstall() {
	return true;
}
function dwcostiron_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
		case "dwellings-buy-valuecheck":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidiron = abs((int)httppost('paidiron'));
			if ($paidiron < 0) $paidiron = 0;
			$allprefs=unserialize(get_module_pref('allprefs','metalmine'));
			$ironavailable = $allprefs['metal1'];
			if($ironavailable<$paidiron){
				$args['allowpay']=0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou do not have that much iron.");
			}elseif($paidiron>(get_module_objpref("dwellingtypes",$typeid,"ironcost")-get_module_objpref("dwellings",$args['dwid'],"ironspent"))){
				$args['allowpay']=0;
				blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
				output("`nYou have tried to spend more iron than you need to.");
			}
		break;
		case "dwellings-buy-setup":
			$typeid= get_module_setting("typeid",$args['type']);
			$paidiron = abs((int)httppost('paidiron'));
			if ($paidiron < 0) $paidiron = 0;
			$ironspent=get_module_objpref("dwellings",$args['dwid'],"ironspent")+$paidiron;				
			set_module_objpref("dwellings",$args['dwid'],"ironspent",$ironspent);
			$allprefs=unserialize(get_module_pref('allprefs','metalmine'));
			$allprefs['metal1']=$allprefs['metal1']-$paidiron;
			set_module_pref('allprefs',serialize($allprefs),'metalmine');
			if($ironspent<get_module_objpref("dwellingtypes",$typeid,"ironcost")){
				$args['finished']=0;			
			}
		break;
		case "dwellings-pay-costs":
			$typeid=get_module_setting("typeid",$args['type']);
			$costiron=get_module_objpref("dwellingtypes",$typeid,"ironcost") - get_module_objpref("dwellings",$args['dwid'],"ironspent");
			if($costiron) output("`)%s Grams of Iron`n`0",$costiron);
		break;
		case "dwellings-pay-input":
			$typeid= get_module_setting("typeid",$args['type']);
			$costiron=get_module_objpref("dwellingtypes",$typeid,"ironcost") - get_module_objpref("dwellings",$args['dwid'],"ironspent");
			$iron = translate_inline("Iron");
			if($costiron) rawoutput("$iron: <input id='input' name='paidiron' width=5><br>");		
		break;		
	}
	return $args;
}
?>