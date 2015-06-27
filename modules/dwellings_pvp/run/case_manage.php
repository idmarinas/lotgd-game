<?php
	$typeid = httpget('type');
	$gold_cost = get_module_objpref("dwellingtypes",$typeid,"cost-gold","dwellings_pvp");
	$gems_cost = get_module_objpref("dwellingtypes",$typeid,"cost-gems","dwellings_pvp");
	$gold_paid = get_module_objpref("dwellings",$dwid,"gold-paid","dwellings_pvp");
	$gems_paid = get_module_objpref("dwellings",$dwid,"gems-paid","dwellings_pvp");
	$days = get_module_objpref("dwellingtypes",$typeid,"guard-length","dwellings_pvp");
	addnav("Return to Management","runmodule.php?module=dwellings&op=manage&dwid=$dwid");
	switch (httpget("subop")){
		case "buy":
			$gold = abs(httppost('gold'));
			$gems = abs(httppost('gems'));
			$sub = httppost('submit');
			if (!$sub){
				output("`3You approach a desk that has a plaque mounted to the front of it, \"Dwellings Commission.\"");
				output("Picking up a pamphlet, you read about how you are able to purchase a Guard for your dwelling, that will detour people seeking to attack your residents.");
				output("This service costs `^%s `3gold and `%%s `3gems.`n`n",$gold_cost,$gems_cost);
				if ($gold_paid > 0 || $gems_paid > 0){
					output("Currently, you have paid:`n");
					if ($gold_paid > 0) output("`^%s `#Gold`n",$gold_paid);
					if ($gems_paid > 0) output("`^%s `#Gems`n",$gems_paid);
				}
				output("`n`3Would you like to make an installment?");
				rawoutput("<form action='runmodule.php?module=dwellings_pvp&op=manage&subop=buy&dwid=$dwid&type=$typeid' method='post'>");
				rawoutput("<input type='hidden' name='submit' value='1'>");
				if ($gold_paid < $gold_cost) rawoutput("Gold: <input type='text' name='gold'><br>");
				if ($gems_paid < $gems_cost) rawoutput("Gems: <input type='text' name='gems'><br>");
				rawoutput("<input type='submit' class='button' value='".translate_inline("Pay")."'></form>");
				if (!get_module_setting("refund")) output("`n`iNote: If you overpay, you will not be refunded, so count your gold and gems carefully.`i`0");
			}else{
				$fail = 0;
				if ($gold > $session['user']['gold'] || $gems > $session['user']['gems']) $fail = 1;
				if (!$fail){
					set_module_objpref("dwellings",$dwid,"gold-paid",$gold_paid+$gold,"dwellings_pvp");
					set_module_objpref("dwellings",$dwid,"gems-paid",$gems_paid+$gems,"dwellings_pvp");
					$session['user']['gold']-=$gold;
					$session['user']['gems']-=$gems;
					if (($gold_paid+$gold) >= $gold_cost && ($gems_paid+$gems) >= $gems_cost){
						output("`#Congratulations!");
						output("`3Your dwelling is now equipped with it's own personal guard.");
						output("This guard shall run out in `b%s`b days and you will be notified of it's dismissal.",$days);
						set_module_objpref("dwellings",$dwid,"run-out",$days,"dwellings_pvp");
						set_module_objpref("dwellings",$dwid,"bought",1,"dwellings_pvp");
						set_module_objpref("dwellings",$dwid,"gold-paid",0,"dwellings_pvp");
						set_module_objpref("dwellings",$dwid,"gems-paid",0,"dwellings_pvp");
						if (get_module_setting("refund")){
							if ($gold_paid+$gold > $gold_cost) $gold_ref = ($gold_paid+$gold)-$gold_cost;
							if ($gems_paid+$gems > $gems_cost) $gems_ref = ($gems_paid+$gems)-$gems_cost;
							if ($gold_ref > 0 || $gems_ref > 0){
								output("`n`n`3Due to you spending more than enough gold/gems for our services, we are to refund you `^%s `3gold and `%%s `3gems.",$gold_ref,$gems_ref);
								$session['user']['gold']+=$gold_ref;
								$session['user']['gems']+=$gems_ref;
							}
						}
					}else{
						output("`3Your current total is `^%s `3gold and `%%s `3gems.",$gold_paid+$gold,$gems_paid+$gems);
					}
				}else{
					output("We are sorry, but you either put in an incorrect value or didn't have the correct amount of resources.");
					addnav("Try Again","runmodule.php?module=dwellings_pvp&op=manage&subop=buy&dwid=$dwid&type=$type");
				}
			}
			addnav("","runmodule.php?module=dwellings_pvp&op=manage&subop=buy&dwid=$dwid&type=$typeid");
			break;
		case "renew": case "rehire":
			output("`#Congratulations!");
			output("`3Your dwelling is now equipped with it's own personal guard.");
			output("This guard shall run out in `b%s`b days and you will be notified of it's dismissal.",$days);
			if (httpget('subop') != "final"){
				$session['user']['gold']-=($gold_cost-$gold_paid);
				$session['user']['gems']-=($gems_cost-$gems_paid);
				set_module_objpref("dwellings",$dwid,"gold-paid",0,"dwellings_pvp");
				set_module_objpref("dwellings",$dwid,"gems-paid",0,"dwellings_pvp");
			}else{
				set_module_objpref("dwellings",$dwid,"gold-paid",$gold_paid-$gold_cost,"dwellings_pvp");
				set_module_objpref("dwellings",$dwid,"gems-paid",$gems_paid-$gems_cost,"dwellings_pvp");
			}
			set_module_objpref("dwellings",$dwid,"run-out",$days,"dwellings_pvp");
			set_module_objpref("dwellings",$dwid,"bought",1,"dwellings_pvp");
			break;
	}
?>