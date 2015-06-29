<?php
// Already on the top of the run function...
//		checkday();
		if($cityid == ""){
			require_once("modules/cityprefs/lib.php");
			$cityid = get_cityprefs_cityid("location",$session['user']['location']);
		}
		$subop = httpget('subop');
		$costgems = get_module_setting("gemcost",$type);
		$costgold = get_module_setting("goldcost",$type);
		$dwname = translate_inline(get_module_setting("dwname",$type));

		$sql = "SELECT COUNT(dwid) AS count FROM ".db_prefix("dwellings")." WHERE location='".$session['user']['location']."' and type='".$type."'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$typesumloc = $row['count'];//for location limit on types

		$sql = "SELECT COUNT(dwid) AS count FROM ".db_prefix("dwellings")." WHERE ownerid=".$session['user']['acctid']."";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$globsum = $row['count'];//for global limit

		$sql = "SELECT COUNT(dwid) AS count FROM ".db_prefix("dwellings")." WHERE location='".$session['user']['location']."' and ownerid=".$session['user']['acctid']." and type='$type'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$usertypesumloc = $row['count'];//for location limit on types by owner
		
		$sql = "SELECT COUNT(dwid) AS count FROM ".db_prefix("dwellings")." WHERE type='$type'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$usertypesumglobal = $row['count'];//for limit on types globally

		if(get_module_objpref("city",$cityid,"loclimit$type") != 0 
			&& get_module_objpref("city",$cityid,"loclimit$type") <= $typesumloc 
			&& $subop == "presetup"){
			output("`#The Dwellings Commission has decreed that no more %s `#may be built in this area.",translate_inline(get_module_setting("dwnameplural")));
			addnav("Back to Dwellings","runmodule.php?module=dwellings");
		}elseif(get_module_objpref("city",$cityid,"userloclimit$type") != 0 
			&& get_module_objpref("city",$cityid,"userloclimit$type") <= $usertypesumloc 
			&& $subop == "presetup"){
			output("`#The Dwellings Commission has decreed that you may not build any more %s `#in this location.",translate_inline(get_module_setting("dwnameplural")));
			addnav("Back to Dwellings","runmodule.php?module=dwellings");
		}elseif(get_module_setting("globallimit",$type) != 0 
			&& get_module_setting("globallimit",$type) <= $usertypesumglobal 
			&& $subop == "presetup"){
			output("`#The Dwellings Commission has decreed that no more %s `#be built in the realm..",translate_inline(get_module_setting("dwnameplural",$type)));
			addnav("Back to Dwellings","runmodule.php?module=dwellings");
		}elseif(get_module_setting("ownergloballimit")!=0 
			&& get_module_setting("ownergloballimit")<=$globsum 
			&& $dwid==""){
			output("`nYou must be some kind of dwelling addict!  You have enough dwellings as it is.");
		}else{
			if($dwid > 0){
				if($subop == ""){//value check then make payment on existing dwelling
					$paidgold = abs((int)httppost('gold'));
					$paidgems = abs((int)httppost('gems'));
					// Redundant... abs() will handle this...
					// if ($paidgold < 0) $paidgold = 0;
					// if ($paidgems < 0) $paidgems = 0;
					$allowpay = 1;
					$buyargs = modulehook("dwellings-buy-valuecheck",
						array('type'=>$type,'dwid'=>$dwid,'allowpay'=>$allowpay,'costgems'=>$costgems,'costgold'=>$costgold));			   
					$costgold = $buyargs['costgold'];
					$costgems = $buyargs['costgems'];
					$carrygems = httpget('gems');
					$carrygold = httpget('gold');
					$allowpay = $buyargs['allowpay'];
					// Changed this a little... 
					// Nothing really great, but it'll show you now, if you paid too much gems and haven't got enough gold...
					if($paidgems > $session['user']['gems']) {
						output("You do not have enough gems to make that payment.`n");
						$paidgems = $session['user']['gems'];
					}
					if($paidgold > $session['user']['gold']){
						output("You do not have enough gold to make that payment.`n");
						$paidgold = $session['user']['gold'];
					}
					if($allowpay){ 
						$session['user']['gold']-=$paidgold;
						$session['user']['gems']-=$paidgems;
						$sql = "UPDATE ".db_prefix("dwellings")." SET gold=gold+$paidgold, gems=gems+$paidgems WHERE dwid=$dwid";
						db_query($sql);
						$sql = "SELECT gold,gems FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
						$result = db_query($sql);
						$row = db_fetch_assoc($result);
						$neededgold = $costgold - $row['gold'];
						$neededgems = $costgems - $row['gems']; 
						$finished = 1;
						$buyargs = modulehook("dwellings-buy-setup",array('type'=>$type,'dwid'=>$dwid,'finished'=>$finished));
						$finished = $buyargs['finished'];
						if(($neededgold < 1 && $neededgems < 1) && ($finished)){
							$goldvalue = round($row['gold'] * get_module_setting("valueper")/100);
							$gemvalue = round($row['gems'] * get_module_setting("valueper")/100);
							output("You have finally finished paying off your dwelling!");
							if(get_module_objpref("dwelling",$dwid,"buildturns")<=get_module_setting("turncost",$type)){
								output("Now it's time to build it!");
								debuglog("finished payments on their $type dwelling - number $dwid in ".$session['user']['location']);
								$setturn = "status=3,";
								addnav(array("Build your %s",$dwname),"runmodule.php?module=dwellings&op=build&type=$type&dwid=$dwid");
							}else{
								debuglog("Completely finished their $type dwelling - number $dwid in ".$session['user']['location']);
								$setturn = "status=1,";
								addnav(array("Enter your %s",$dwname),"runmodule.php?module=dwellings&op=enter&type=$type&dwid=$dwid");
							}
							$sql = "UPDATE ".db_prefix("dwellings")." SET goldvalue=$goldvalue,$setturn gemvalue=$gemvalue where dwid=$dwid";
							db_query($sql);
						}elseif($finished == 0 || $neededgold > 0 || $neededgems > 0){
							output("`@You are now one step closer to finishing the payments on your %s`@.",$dwname);
						}
					}
				}elseif($subop == "payment"){ //enter values for payment on existing dwelling
					$sql = "SELECT gold,gems FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
					$result = db_query($sql);
					$row = db_fetch_assoc($result);
					output("How much are you able to pay today?  You still owe the following:`n`n");
					$buyargs = modulehook("dwellings-pay-costs",
						array('type'=>$type,'dwid'=>$dwid,'costgems'=>$costgems,'costgold'=>$costgold));
					$costgold = $buyargs['costgold'];
					$costgems = $buyargs['costgems'];
					$neededgold = $costgold - $row['gold'];
					$neededgems = $costgems - $row['gems'];
					if($neededgems > 0) output("`%%s `7Gems`0`n",$neededgems);
					if($neededgold > 0) output("`^%s `7Gold`0`n",$neededgold);
					output_notl("`n");
					rawoutput("<form action='runmodule.php?module=dwellings&op=buy&type=$type&dwid=$dwid&gems=$neededgems&gold=$neededgold' method='POST'>");
					addnav("","runmodule.php?module=dwellings&op=buy&type=$type&dwid=$dwid&gems=$neededgems&gold=$neededgold");
					modulehook("dwellings-pay-input",array('type'=>$type,'dwid'=>$dwid));
					if($neededgems > 0) rawoutput(translate_inline("Gems:")." <input id='input' name='gems' width=5><br>"); 
					if($neededgold > 0) rawoutput(translate_inline("Gold:")." <input id='input' name='gold' width=5><br>");		
					$submit = translate_inline("Submit");
					output_notl("`n");
					rawoutput("<input type='submit' class='button' value='$submit'>");
					rawoutput("</form>");
				}			
			}else{//start the initial purchase
				if($subop == "presetup"){
					output("If you want to build a %s`0, you'll need the following:`n`n",$dwname);
					$buyargs = modulehook("dwellings-pay-costs",
						array('type'=>$type,'dwid'=>$dwid,'costgems'=>$costgems,'costgold'=>$costgold));
					$costgold = $buyargs['costgold'];
					$costgems = $buyargs['costgems'];
					if($costgems) output("`%%s `7Gems`0`n",$costgems);
					if($costgold) output("`^%s `7Gold`0`n",$costgold);
					rawoutput("<br><form action='runmodule.php?module=dwellings&op=buy&type=$type' method='POST'>");
					addnav("","runmodule.php?module=dwellings&op=buy&type=$type");
					modulehook("dwellings-pay-input",array('type'=>$type,'dwid'=>$dwid));
//Changed this, so we can _always_ translate these
					$gems = translate_inline("Gems:");
					$gold = translate_inline("Gold:");
					$submit = translate_inline("Submit");
					if($costgems) rawoutput("$gems <input id='input' name='gems' width=5><br>"); 
					if($costgold) rawoutput("$gold <input id='input' name='gold' width=5><br>");		
					rawoutput("<input type='submit' class='button' value='$submit'>");
					rawoutput("</form>");
				}else{  //create it in the db
					$paidgold = abs((int)httppost('gold'));
					$paidgems = abs((int)httppost('gems'));
					if ($paidgold < 0) $paidgold = 0;
					if ($paidgems < 0) $paidgems = 0;
					$allowpay = 1;
					$buyargs = modulehook("dwellings-buy-valuecheck",
						array('type'=>$type,'dwid'=>$dwid,'allowpay'=>$allowpay,'costgems'=>$costgems,'costgold'=>$costgold));				
					$costgold = $buyargs['costgold'];
					$costgems = $buyargs['costgems'];
					$allowpay = $buyargs['allowpay'];
					if ($paidgems > $session['user']['gems'] || $paidgold > $session['user']['gold']){
						$allowpay = 0;
						if($paidgems > $session['user']['gems']) output("You do not have enough gems to make that payment.`n");
						if($paidgold > $session['user']['gold']) output("You do not have enough gold to make that payment.`n");					
					}
					if (($paidgems > $costgems) || ($paidgold > $costgold)){
						$allowpay = 0;
						if($paidgems > $costgems) output("You have offered too many gems.");
						if($paidgold > $costgold) output("You have offered too much gold.");					
					}
					if($allowpay == 1){
						$session['user']['gold']-=$paidgold;
						$session['user']['gems']-=$paidgems;
						$goldvalue = round($paidgold * get_module_setting("valueper")/100);
						$gemvalue = round($paidgems * get_module_setting("valueper")/100);
						$sql = "INSERT INTO ".db_prefix("dwellings")." (gold,gems,goldvalue,gemvalue,ownerid,type,location,status) VALUES ($paidgold,$paidgems,$goldvalue,$gemvalue,".$session['user']['acctid'].",'$type','".$session['user']['location']."',2)";
						$result = db_query($sql);
// Now, here comes the trick... really nice one... No additional queries needed...
//						$sql = "SELECT dwid FROM ".db_prefix("dwellings")." WHERE location='".$session['user']['location']."' AND ownerid=".$session['user']['acctid']." ORDER BY dwid DESC LIMIT 1";
//						db_query($sql);
//						$result = db_query($sql);
//						$row = db_fetch_assoc($result);
						$dwid = db_insert_id($result);
						$finished = 1;
						$buyargs = modulehook("dwellings-buy-setup",
							array('type'=>$type,'dwid'=>$dwid,'finished'=>$finished));
						$finished = $buyargs['finished'];
						if(($paidgold == $costgold && $paidgems == $costgems) && ($finished == 1)){
							output("You have paid for your dwelling in full!");
							if(get_module_objpref("dwelling",$dwid,"buildturns")<=get_module_setting("turncost",$type)){
								output("Now it's time to build it!");
								$status = 3;
								addnav(array("Build your %s",$dwname),"runmodule.php?module=dwellings&op=build&type=$type&dwid=$dwid");
							}else{
								$status = 1;
								addnav(array("Enter your %s",$dwname),"runmodule.php?module=dwellings&op=enter&type=$type&dwid=$dwid");
							}
							$sql = "UPDATE ".db_prefix("dwellings")." SET status=$status WHERE dwid=$dwid";
							db_query($sql);
						}else{
							output("`@You are now one step closer to finishing the payments on your %s`@.",$dwname);
						}
					}
				}
			}
		}
?>