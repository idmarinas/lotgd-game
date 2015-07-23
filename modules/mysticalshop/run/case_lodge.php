<?php
	$passcost = (int)get_module_setting("pointsreq");
	$shopname = get_module_setting("shopname");
	$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
	switch(httpget('what')){
		case "buypass":
			if( $passcost == 0 )
				$pts = translate_inline( 'is free' );
			elseif( $passcost == 1 )
				$pts = translate_inline( 'costs `^1`& donation point' );
			else
				$pts = sprintf_translate( 'costs `^%s`& donation points', $passcost );
			output("`7J. C. Petersen glances at you for a moment, sizing you up. After a moment, he nods.`n`n");
			output("`&\"A pass to visit `3%s`& %s.", $shopname, $pts);
			if ($pointsavailable<$passcost){
				output(" `&I'm afraid, however, you don't have enough to afford it,\" he says.`0`n`n");
				addnav("L?Return to the Lodge","lodge.php");
			}else{
				output(" `&Interested?\" `7he asks you, holding up a shimmering green token.`0`n`n");
				addnav("Yes",$from."op=lodge&what=bought");
				addnav("No","lodge.php");
			}
			break;
		case "bought":
			$session['user']['donationspent']+=$passcost;
			set_module_pref("pass",1);
			output("`7J. C. makes a note on a ledger and then hands you the shimmering green token.`n`n");
			output("`&\"The pass is good for as long as you have it,\" `7he says.");
			output(" `&\"You'll find `3%s`& in `^%s`& by the way.\"`0`n`n", $shopname, get_module_setting( 'shoploc' ));
			debuglog( 'spent '.$passcost.' donation points on '.$shopname.' pass.' );
			addnav("L?Return to the Lodge","lodge.php");
			break;
	}
?>