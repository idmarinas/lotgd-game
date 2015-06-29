<?

//translation readied "buy it" found by Gucky2000

	if($cityid == ""){
		require_once("modules/cityprefs/lib.php");
		$cityid = get_cityprefs_cityid("location",$session['user']['location']);
	}
	if(httpget("subop")=="buy"){
		$gemcost = httpget("gemcost");
		$goldcost = httpget("goldcost");
		$nosale = 0;
		modulehook("dwellings-forsale-check",array("type"=>$type,"dwid"=>$dwid));
		if($session['user']['gems']<$gemcost){
			$nosale++;
			output("You do not have enough gems to buy this dwelling.`n");
		}
		if($session['user']['gold']<$goldcost){
			$nosale++;
			output("You do not have enough gold to buy this dwelling.`n");
		}
		if($session['user']['dragonkills']<get_module_setting("dkreq",$type)){
			$nosale++;
			output("You are not experienced enough to buy this type of dwelling.`n");
		}
		require_once("modules/cityprefs/lib.php");
		$cityid=get_cityprefs_cityid("location",$session['user']['location']);
		$sql = "SELECT COUNT(dwid) AS count FROM ".db_prefix("dwellings")." WHERE ownerid=".$session['user']['acctid']."";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$globsum=$row['count'];//for global limit
		$sql = "SELECT COUNT(dwid) AS count FROM ".db_prefix("dwellings")." WHERE location='".$session['user']['location']."' and ownerid=".$session['user']['acctid']." and type='$type'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$usertypesumloc = $row['count'];//for location limit on types by owner
		
		$sql = "SELECT COUNT(dwid) AS count FROM ".db_prefix("dwellings")." WHERE ownerid=".$session['user']['acctid']." and type='$type'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$usertypesumglobal=$row['count'];//for limit on types globally
		
		if(get_module_objpref("city",$cityid,"userloclimit$type")!=0 
			&& get_module_objpref("city",$cityid,"userloclimit$type")<=$usertypesumloc){
				$nosale++;
				output("`nLocal Land owning guidelines prevent you from owning any more %s in this location.`n",translate_inline(get_module_setting("dwnameplural,$type")));
		}elseif(get_module_setting("globallimit",$type)!=0 
			&& get_module_setting("globallimit",$type)<=$usertypesumglobal){
				$nosale++;
				output("`nRealm permit guidelines prevent you from owning any more %s.`n",translate_inline(get_module_setting("dwnameplural",$type)));
		}elseif(get_module_setting("ownergloballimit") != 0 
			&& get_module_setting("ownergloballimit") <= $globsum){
			$nosale++;
			output("`nYou must be some kind of dwelling addict!  You have enough dwellings as it is.`n");
		}if($nosale == 0){
            $sql = "SELECT status FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
            $result = db_query($sql);
            $row = db_fetch_assoc($result);
            if($row['status']==1){
                output("Uh oh!  Looks like you waited to long to complete the transaction and someone swiped the dwelling before you!");
            }else{
                output("Congratulations on buying this dwelling!");
                $session['user']['gold']-=$goldcost;
                $session['user']['gems']-=$gemcost;
                modulehook("dwellings-forsale-buy",array("type"=>$type,"dwid"=>$dwid));
                $sql = "UPDATE ".db_prefix("dwellings")." SET ownerid=".$session['user']['acctid'].",status=1 WHERE dwid=$dwid";
                db_query($sql);
				debuglog("bought a $type dwelling - number $dwid in ".$session['user']['location']);
                addnav("Enter your Dwelling","runmodule.php?module=dwellings&op=enter&dwid=$dwid");
            }
		}
	}else{
		output("`#Here you can see all the dwellings in %s`# that have either been abandoned or repossessed and are now available at a special rate for you to buy.",$session['user']['location']);
		$loc = $session['user']['location'];
		debug($loc);
		// no SELECT * FROM ... Especially description and windowpeer canbe quite large...
		$sql = "SELECT dwid, name, goldvalue, gemvalue, gold, gems, type FROM ".db_prefix("dwellings")." WHERE location='$loc' AND (status=4 OR status=5) ORDER BY type DESC";
		$result = db_query($sql);
		$name = translate_inline("Name");
		$ops = translate_inline("Options"); 
		$type = translate_inline("Type");		
		$cost = translate_inline("Cost");   
		$desc = translate_inline("Description");
		rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'><tr class='trhead'><td style=\"width:250px\">$name</td><td style=\"width:150px\" align=center>$type</td><td align=center style=\"width:75px\">$cost</td><td align=center>$ops</td></tr>"); 
		$i = 0;
		if(!db_num_rows($result)){
			$none = translate_inline("None");
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center colspan=4><i>$none</i></td></tr>");
		}else{
			while($row = db_fetch_assoc($result)){
				$i++;
				$rtype = $row['type'];
				if((get_module_setting("lvlbuy")) 
					|| get_module_setting("dkreq",$rtype)<=$session['user']['dragonkills']){
					$rdwid = $row['dwid'];
					if($row['name']==""){
						$name = translate_inline("Unnamed");
					}else{
						$name = $row['name'];
					}
					$cname = get_module_setting("dwname",$rtype);
					$dwid=$row['dwid'];
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center>");
					output_notl("$name</a></td><td align=center>$cname</td><td align=center>",true);
					$goldcost = $row['goldvalue'];
					$gemcost = $row['gemvalue'];
					if(get_module_setting("addcof")){
						$goldcost+=$row['gold'];
						$gemcost+=$row['gems'];
					}
					$goldcost=round($goldcost*(get_module_setting("abnperc")*0.01));
					$gemcost=round($gemcost*(get_module_setting("abnperc")*0.01));
					output("`^Gold: %s`n`%Gems: %s`0",$goldcost,$gemcost);
					modulehook("dwellings-forsale-cost",array("type"=>$row['type'],"dwid"=>$row['dwid']));
					$buyit = translate_inline("Buy it");
					rawoutput("</td><td align=center><a href=runmodule.php?module=dwellings&op=forsale&subop=buy&goldcost=$goldcost&gemcost=$gemcost&dwid=$dwid>$buyit");
					addnav("","runmodule.php?module=dwellings&op=forsale&subop=buy&goldcost=$goldcost&gemcost=$gemcost&dwid=$dwid");
					rawoutput("</a></td></tr>");
				}
			}
		}
		rawoutput("</table>");
	}
?>
