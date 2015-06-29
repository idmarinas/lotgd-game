<?php
function metalmine_mining(){
	global $session;
	$allprefs=unserialize(get_module_pref('allprefs'));
	$marray=translate_inline(array("","`)Iron Ore`0","`QCopper`0","`&Mithril`0"));
	addnav("Metal Mine");
	output("`n`c`b`&Metal `)Mine`c`b`n`0");
	if (get_module_setting("losealign")==0) $chance=e_rand(1,8);
	else $chance=e_rand(1,7);
	if (get_module_setting("limitloc")<=1){
		if (is_module_active("alignment") && get_module_setting("losealign")<2){
			$align=get_module_pref("alignment","alignment");
			if($align>get_module_setting("goodalign","alignment")){
				if (get_module_setting("losealign")==0){
					if ($chance==1) $metal=1;
					elseif ($chance==2 ||$chance==3 || $chance==4) $metal=2;
					else $metal=3;
				}else{
					if ($chance==1 || $chance==2) $metal=1;
					elseif ($chance>2 && $chance<5) $metal=2;
					else $metal=3;
				}
			}elseif ($align<get_module_setting("evilalign","alignment")){
				if (get_module_setting("losealign")==0){
					if ($chance==1) $metal=3;
					elseif ($chance==2 ||$chance==3 || $chance==4) $metal=2;
					else $metal=1;
				}else{
					if ($chance==1 || $chance==2) $metal=3;
					elseif ($chance>2 && $chance<5) $metal=2;
					else $metal=1;
				}
			}else{
				if (get_module_setting("losealign")==0){
					if ($chance==1 ||$chance==2) $metal=1;
					elseif ($chance==7 ||$chance==8) $metal=3;
					else $metal=2;
				}else{
					if ($chance==1 || $chance==2) $metal=1;
					elseif ($chance>2 && $chance<5) $metal=3;
					else $metal=2;
				}
			}
		}else{
			$metal=e_rand(1,3);
		}
		$allprefs['metal']=$metal;
	}else{
		$metal=$allprefs['metal'];
	}
	$usedmts=$allprefs['usedmts'];
	$mineturnset=get_module_setting("mineturnset");
	$mineturns=$mineturnset-$usedmts;
	set_module_pref('allprefs',serialize($allprefs));
	output("You look around and find that you are in the %s section of the mine.",$marray[$metal]);
	if ($usedmts>=$mineturnset){
		output("You get ready to get some work done, but you've used up all your remaining turns in the mine.  It looks like you'll have to head out.");
	}else{
		output("You can work this section of the mine or you can try to travel to a different part if you'd like.");
		if (get_module_setting("limitloc")<=1) addnav("Travel","runmodule.php?module=metalmine&op=travel");
		addnav("Work the Mine","runmodule.php?module=metalmine&op=work&op2=arrive");
	}
	addnav("Leave Mine","runmodule.php?module=metalmine&op=leavemine");
}
?>