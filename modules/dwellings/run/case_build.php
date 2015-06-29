<?php
	$working = httpget('working');
	$subop = httpget('subop');
	$turncost = get_module_setting("turncost",$type);	
	$spentturns = get_module_objpref("dwellings",$dwid,"buildturns");
	$turnstogo = $turncost-$spentturns;
	$dwname = translate_inline(get_module_setting("dwname",$type));
	modulehook("dwellings-build-intercept",array("type"=>$type,"dwid"=>$dwid));
	if($working=="" && $turnstogo>0){
		output("You estimate that it will take %s turns before you can finish building your %s`0.`n`n",$turnstogo,$dwname);	
		output("How many turns are you willing to spend working today?`n");
		modulehook("dwellings-build-spendturns",array("type"=>$type,"dwid"=>$dwid));
		rawoutput("<form action='runmodule.php?module=dwellings&op=build&type=$type&dwid=$dwid&working=1' method='POST'>");
		addnav("","runmodule.php?module=dwellings&op=build&type=$type&dwid=$dwid&working=1");
		output("Turns to work:");
		rawoutput("<input id='input' name='turns' width=5><br>");
		modulehook("dwellings-build-input",array("type"=>$type,"dwid"=>$dwid));
		$submit = translate_inline("Submit");
		rawoutput("<input type='submit' class='button' value='$submit'>");
		rawoutput("</form>");
	}else{
		$turns = abs((int)httppost('turns'));
		if ($turns > $session['user']['turns']){
			output("`nYou do not have enough turns.");
		}elseif($turnstogo < $turns){
			output("`nAre you trying to over work yourself?");
			output("You only need to work for %s turns.",$turnstogo);
 // Needed... Otherwise the next step will throw some output, which is simply not correct.
		}elseif ($turns==0){
			output("`nDoing some work is quite refreshening, isn't it.");
			output("Especially if you are not doing anything.");
		}else{
			modulehook("dwellings-build-final",array("type"=>$type,"dwid"=>$dwid));
			$session['user']['turns']-=$turns;
			set_module_objpref("dwellings",$dwid,"buildturns",$spentturns+$turns);					   
			$turnstogo = $turncost-get_module_objpref("dwellings",$dwid,"buildturns");
			if(!$turnstogo){
				$sql = "UPDATE ".db_prefix("dwellings")." SET status=1,gold=0,gems=0 WHERE dwid=$dwid";
				db_query($sql);
				output("`nStanding back, you take a look at all your hard work.  Your dwelling is done!");
				addnav("Enter your Dwelling","runmodule.php?module=dwellings&op=enter&dwid=$dwid");
			}else{
				output("`@You are now one step closer to finishing your %s`@.",$dwname);
			}
		}		
	}
?>