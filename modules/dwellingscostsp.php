<?php
function dwellingscostsp_getmoduleinfo(){
    $info = array(
        "name"=>"Dwellings Cost Site Points",
		"version"=>"20051218",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=162",
		"vertxtloc"=>"http://dragonprime.net/users/sixf00t4/",
        "author"=>"<a href='http://www.joshuadhall.com'>Sixf00t4</a>",
        "category"=>"Dwellings",
        "description"=>"Allows dwellings to cost site points",
        "requires"=>array(
	       "dwellings"=>"20051118|By Sixf00t4, available on DragonPrime",
        ), 
		"prefs-dwellings"=>array(
			"Dwelling Cost SP,title",
			"spspent"=>"How many site points have they spent on this dwelling?,int|0",
		),    
		"prefs-dwellingtypes"=>array(
			"Dwelling Type Prefs,title",
			"spcost"=>"How many site points does it cost to buy this type of dwelling?,int|5",
		),
	);
        return $info;
}

function dwellingscostsp_install(){
    module_addhook("dwellings-pay-costs"); 
    module_addhook("dwellings-pay-input");
    module_addhook("dwellings-buy-setup");
    module_addhook("dwellings-buy-valuecheck");    
    return true;
}

function dwellingscostsp_uninstall() {
	return true;
}

function dwellingscostsp_dohook($hookname,$args) {
	global $session;
    

	switch ($hookname) {

        case "dwellings-buy-valuecheck":
            $typeid= get_module_setting("typeid",$args['type']);
            $paidsp = abs((int)httppost('paidsp'));
            if ($paidsp < 0) $paidsp = 0;
            $pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
            if($pointsavailable<$paidsp){
                $args['allowpay']=0;
                blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
                output("`nYou do not have that many site points.");
            }elseif($paidsp>(get_module_objpref("dwellingtypes",$typeid,"spcost")-get_module_objpref("dwellings",$args['dwid'],"spspent"))){
                $args['allowpay']=0;
                blocknav("runmodule.php?module=dwellings&op=build&type=".$args['type']."&dwid=".$args['dwid']."");
                output("`nYou have tried to spend more site points than you need to.");
            }
        break;
        
        case "dwellings-buy-setup":
            $typeid= get_module_setting("typeid",$args['type']);
            $paidsp = abs((int)httppost('paidsp'));
            if ($paidsp < 0) $paidsp = 0;
            $spent=get_module_objpref("dwellings",$args['dwid'],"spspent")+$paidsp;                
            set_module_objpref("dwellings",$args['dwid'],"spspent",$spent);
			$session['user']['donationspent']+=$paidsp;
			if($spent!=get_module_objpref("dwellingtypes",$typeid,"spcost")){
				$args['finished']=0;            
                }
        break;

        case "dwellings-pay-costs":
            $typeid=get_module_setting("typeid",$args['type']);
            $costsp=get_module_objpref("dwellingtypes",$typeid,"spcost") - get_module_objpref("dwellings",$args['dwid'],"spspent");
            if($costsp) output("`#%s Site Points`0`n",$costsp);
        break;
        
        case "dwellings-pay-input":
            $typeid= get_module_setting("typeid",$args['type']);
            $costsp=get_module_objpref("dwellingtypes",$typeid,"spcost") - get_module_objpref("dwellings",$args['dwid'],"spspent");
			$sp = translate_inline("Site Points");
            if($costsp) rawoutput("$sp: <input id='input' name='paidsp' width=5><br>");		
        break;        
	}
	return $args;
}

function dwellingscostsp_run(){}
?>