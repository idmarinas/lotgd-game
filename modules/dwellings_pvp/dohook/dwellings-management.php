<?php
	$dwid = $args['dwid'];
	$typeid = get_module_setting("typeid",$args['type']);
	addnav("Dwellings PvP Guard");
	$isauto = get_module_objpref("dwellings",$dwid,"isauto","dwellings_pvp");
	$allow = get_module_objpref("dwellingtypes",$typeid,"buy-guard","dwellings_pvp");
	if ($allow){
		if (get_module_setting("whatif")){
			if ($isauto){
				addnav("Turn `bOFF`b Auto-Purchase",
					"runmodule.php?module=dwellings_pvp&op=auto&act=0&dwid=$dwid");
			}else{
				addnav("Turn `bON`b Auto-Purchase",
					"runmodule.php?module=dwellings_pvp&op=auto&act=1&dwid=$dwid");
			}
		}
	}
	$doot_guard = translate_inline("Dwelling's Guard Information");
	rawoutput("<tr><td colspan=2 class='trhead' style='text-align:center;'>$doot_guard</td></tr>");
	$dwguards = translate_inline("Terms of Contract");
	rawoutput("<tr height=30px class='trlight'><td>$dwguards</td><td>");
	$guards = get_module_objpref("dwellings", $dwid, "bought", "dwellings_pvp");
	if($guards == 0) 
		output("There is no one guarding your dwelling");
	else 
		output("The most recent contract runs out in `^%s `0days.",
				get_module_objpref("dwellings", 
				$dwid, 
				"run-out", 
				"dwellings_pvp")
		);
	rawoutput("</td></tr>");
	
	$gold_cost = get_module_objpref("dwellingtypes",$typeid,"cost-gold","dwellings_pvp");
	$gems_cost = get_module_objpref("dwellingtypes",$typeid,"cost-gems","dwellings_pvp");
	$gold_paid = get_module_objpref("dwellings",$dwid,"gold-paid","dwellings_pvp");
	$gems_paid = get_module_objpref("dwellings",$dwid,"gems-paid","dwellings_pvp");
	$tgold = appoencode(translate_inline("`0Gold: `^"));
	$tgems = appoencode(translate_inline("`0Gems:`%"));
	if (get_module_objpref("dwellingtypes",$typeid,"buy-guard","dwellings_pvp")){
		$costhire = translate_inline("Cost of hiring guards for this dwelling");
		rawoutput("<tr height=30px class='trdark'><td>$costhire</td><td>$tgold $gold_cost<br>$tgems $gems_cost</td></tr>");
		$installments = translate_inline("Installments made for new guards");
		rawoutput("<tr height=30px class='trlight'><td>$installments</td><td>$tgold $gold_paid<br>$tgems $gems_paid</td></tr>");
		rawoutput("<tr height=30px class='trdark'><td colspan=2>");
		if (!get_module_objpref("dwellings",$dwid,"bought","dwellings_pvp")){
			if($session['user']['gold'] >= $gold_cost-$gold_paid && $session['user']['gems'] >= $gems_cost-$gems_paid){ 
				output("`2You have enough cash with you to ");
				$hirenew = translate_inline("hire new guards");
				rawoutput("[<a href='runmodule.php?module=dwellings_pvp&op=manage&subop=rehire&dwid=$dwid&type=$typeid'>$hirenew</a>].</td></tr>");
				addnav("","runmodule.php?module=dwellings_pvp&op=manage&subop=rehire&dwid=$dwid&type=$typeid");
			}else{
				$visitguard = translate_inline("visit the dwellings commission and make an installment");
				output("You don't have enough cash to hire new guards. You could ");
				rawoutput("[<a href='runmodule.php?module=dwellings_pvp&op=manage&subop=buy&dwid=$dwid&type=$typeid'>$visitguard</a>]</td></tr>");
				addnav("","runmodule.php?module=dwellings_pvp&op=manage&subop=buy&dwid=$dwid&type=$typeid");
			}
		}elseif($session['user']['gold'] >= $gold_cost-$gold_paid 
			&& $session['user']['gems'] >= $gems_cost-$gems_paid){ 
			$renewcont = translate_inline("renew the contract with your present guards");
			output("`2You have enough cash with you to ");
			rawoutput("[<a href='runmodule.php?module=dwellings_pvp&op=manage&subop=renew&dwid=$dwid&type=$typeid'>$renewcont</a>].</td></tr>");			
			addnav("","runmodule.php?module=dwellings_pvp&op=manage&subop=renew&dwid=$dwid&type=$typeid");
		}elseif($gold_paid >= $gold_cost && $gems_paid >= $gems_cost){
			$renewcont = translate_inline("finalize your contract");
			output("`2You have invested enough cash to ");
			rawoutput("[<a href='runmodule.php?module=dwellings_pvp&op=manage&subop=final&dwid=$dwid&type=$typeid'>$renewcont</a>].</td></tr>");			
			addnav("","runmodule.php?module=dwellings_pvp&op=manage&subop=final&dwid=$dwid&type=$typeid");
		}else{
			output("You don't have enough cash to renew the contract with your present guards.");
			rawoutput("</td></tr>");
			addnav("","runmodule.php?module=dwellings_pvp&op=manage&subop=buy&dwid=$dwid&type=$typeid");
		}
	}else{
		$noguards = translate_inline("No guards will protect this dwelling type.");
		rawoutput("<tr height=30px class='trdark'><td colspan=2>$noguards</td></tr>");
	}
?>