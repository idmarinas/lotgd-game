<?php
global $session;
$loc=httpget('loc');

//Inside the Dwelling: Using the furniture
if ($loc=="inside"){
	$op = httpget('op');
	$op2=httpget('op2');
	$dwid = httpget('dwid');
	$acctid=httpget('acctid');
	if ($op=="furniture"){
		$sql = "SELECT name,ownerid,type,location FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
		$result = db_query($sql);
		$row = db_fetch_assoc($result); 
		$ownerid=$row['ownerid'];
		$location=$row['location'];
		$type = $row['type'];
		$ctype = translate_inline(get_module_setting("dwname",$row['type']));
		if($row['name']=="") $name = translate_inline("Unnamed");
		else $name = $row['name'];
		$sql2 = "SELECT name,sex FROM ".db_prefix("accounts")." WHERE acctid='$ownerid'";
		$result2 = db_query($sql2);
		$row2 = db_fetch_assoc($result2);  
		$ownname=$row2['name'];
		$ownsex=$row2['sex'];
		page_header("%s",sanitize($name));
		if ($op2=="chair") {
			$furnt=get_module_objpref("dwellings",$dwid,"chair");
			if ($furnt==1) $furn="Basic Chair";
			if ($furnt>1) $furn=get_module_objpref("dwellings",$dwid,"custchair");
		}
		if ($op2=="table") {
			$furnt=get_module_objpref("dwellings",$dwid,"table");
			if ($furnt==1) $furn="Basic Table";
			if ($furnt>1) $furn=get_module_objpref("dwellings",$dwid,"custtable");
		}
		if ($op2=="bed") {
			$furnt=get_module_objpref("dwellings",$dwid,"bed");
			if ($furnt==1) $furn="Basic Bed";
			if ($furnt>1) $furn=get_module_objpref("dwellings",$dwid,"custbed");
		}
		if ($furnt==1) {
			$low=5;
			$high=26;
		}
		if ($furnt==2){
			$low=3;
			$high=28;
		}
		if ($furnt==3){
			$low=1;
			$high=30;
		}
		$rand=e_rand(1,200);
		
		if ($op2=='chair'){
			output("`n`@You sit down in the `2%s`@ and reflect on what is happening in the world.`n`n",$furn);
			if (get_module_pref("usedchair")>=1){
				increment_module_pref("usedchair",1);
				if ($session['user']['turns']>0 && $rand<=100 && $rand>1){
					output("You `\$lose`@ one turn resting in the chair.");
					$session['user']['turns']--;
				}elseif ($rand==1){
					output("You feel a huge surge of energy from the chair.  You realize you sat on a nail that was sticking out!");
					output("`n`nYou `bGain 3 Turns`b but you `\$lose all your hitpoints except 1`@.");
					$session['user']['turns']+=3;
					$session['user']['hitpoints']=1;
				}elseif($rand>185){
					output("You sit awkwardly on the chair and hear a cracking sound.");
					set_module_objpref("dwellings",$dwid,"chairstress",get_module_objpref("dwellings",$dwid,"chairstress")+1);
					$chairstress=get_module_objpref("dwellings",$dwid,"chairstress");
					$finalstress=get_module_setting("chairstress","furniture")+$furnt-1;
					if ($chairstress>=$finalstress){
						output("`n`nOh no! You've broken the chair!");
						set_module_objpref("dwellings",$dwid,"chair",0);
						set_module_objpref("dwellings",$dwid,"chairstress",0);
						if ($ownerid==$session['user']['acctid']) output("It looks like you'll have to get a new one now.");
						else{
							require_once("lib/systemmail.php");
							output("It's not even your chair! You better feel guilty now, because %s`@ is going to find out through YoM that you broke %s chair!",$ownname,translate_inline($ownsex?"her":"his"));
							$subj = sprintf("`&Broken Chair Report");
							$body = sprintf("`c`&Maintenance Report`c`n`^Dwelling Name: `@%s`n`^Dwelling Type: %s`n`^Location: `0%s`n`^Report: `6Broken Chair`n`nThis is a note from your Maintenance Department from your dwelling.  We have received a report that `&%s`6 broke your %s`6.  You will need to purchase a new one to replace it.",$name,$ctype,$location,$session['user']['name'],$furn);
							systemmail($ownerid,$subj,$body);
						}
					}
				}else output("You get up from the chair a little refreshed, but not much.");
			}else{
				set_module_pref("usedchair",1);
				switch(e_rand($low,$high)){
					case 1: 
						output("You wiggle around in the chair and hear a clinking sound.  You've found a little bag of gold!");
						output("`n`nYou count out `^15 gold pieces`@! Yay!");
						$session['user']['gold']+=15;
					break;
					case 2: case 3: case 4: case 5:
						output("You wiggle around in the chair and hear a clinking sound.  It looks like someone dropped a gold piece!");
						$session['user']['gold']++;
					break;
					case 6: 
						output("You sit awkwardly on the chair and hear a cracking sound.");
						set_module_objpref("dwellings",$dwid,"chairstress",get_module_objpref("dwellings",$dwid,"chairstress")+1);
						$chairstress=get_module_objpref("dwellings",$dwid,"chairstress");
						$finalstress=get_module_setting("chairstress","furniture")+$furnt-1;
						if ($chairstress>=$finalstress){
							output("`n`nOh no! You've broken the chair!");
							set_module_objpref("dwellings",$dwid,"chair",0);
							set_module_objpref("dwellings",$dwid,"chairstress",0);
							if ($ownerid==$session['user']['acctid']) output("It looks like you'll have to get a new one now.");
							else{
								require_once("lib/systemmail.php");
								output("It's not even your chair! You better feel guilty now, because %s`@ is going to find out through YoM that you broke %s chair!",$ownname,translate_inline($ownsex?"her":"his"));
								$subj = sprintf("`&Broken Chair Report");
								$body = sprintf("`c`&Maintenance Report`c`n`^Dwelling Name: `@%s`n`^Dwelling Type: %s`n`^Location: `0%s`n`^Report: `6Broken Chair`n`nThis is a note from your Maintenance Department from your dwelling.  We have received a report that `&%s`6 broke your %s`6.  You will need to purchase a new one to replace it.",$name,$ctype,$location,$session['user']['name'],$furn);
								systemmail($ownerid,$subj,$body);
							}
						}
					break;
					case 7: case 8: case 9: case 10: case 11: case 12: case 13: case 14: case 15: case 16: case 17: 
						output("You sit comfortably in the chair.`n`nThat was nice.");
					break;
					case 18: case 19: case 20: case 21: case 22: case 23: case 24: case 25: 
						output("You sit comfortably in the chair.`n`nThat was nice.");
						output("`n`nYou gain a turn!");
						$session['user']['turns']++;
					break;
					case 26: case 27:
						output("You get a nice groove in the chair and pleasure spreads through your whole body.");
						output("`n`nYou gain two turns!");
						$session['user']['turns']+=2;
					break;
					case 28: case 29: case 30:
						output("You get a nice groove in the chair and pleasure spreads through your whole body.");
						output("`n`nYou gain two turns!`n`nIn addition, you appear more charming because of the smile on your face. You also gain `&one charm`@!");
						$session['user']['turns']+=2;
						$session['user']['charm']++;
					break;
				}
			}
		}
		if ($op2=="table"){
			output("`n`@You sit down in the `2%s`@ and decide to have a snack.`n`n",$furn);
			if (get_module_pref("usedtable")>=1){
				increment_module_pref("usedtable",1);
				if ($session['user']['turns']>0 && $rand<=100 && $rand>1){
					output("You `\$lose`@ one turn trying to find some food because you can't find any.");
					$session['user']['turns']--;
				}elseif ($rand==1){
					output("You find some `6dried bananas`@.  What a great source of potassium!");
					output("`n`nYou `bGain 4 Turns`b but you accidentally ate a bad banana.  That will haunt you for a little while.");
					$session['user']['turns']+=4;
					apply_buff('badbanana',array("name"=>"Bad Banana","rounds"=>10,"wearoff"=>"`4The bad banana leaves your system.","atkmod"=>.95,"defmod"=>.95));
				}elseif($rand>187){
					output("You sit awkwardly at the table and hear a cracking sound.");
					set_module_objpref("dwellings",$dwid,"tablestress",get_module_objpref("dwellings",$dwid,"tablestress")+1);
					$tablestress=get_module_objpref("dwellings",$dwid,"tablestress");
					$finalstress=get_module_setting("tablestress","furniture")+$furnt-1;
					if ($tablestress>=$finalstress){
						output("`n`nOh no! You've broken the table!");
						set_module_objpref("dwellings",$dwid,"table",0);
						set_module_objpref("dwellings",$dwid,"tablestress",0);
						if ($ownerid==$session['user']['acctid']) output("It looks like you'll have to get a new one now.");
						else{
							require_once("lib/systemmail.php");
							output("It's not even your table! You better feel guilty now, because %s`@ is going to find out through YoM that you broke %s table!",$ownname,translate_inline($ownsex?"her":"his"));
							$subj = sprintf("`&Broken Table Report");
							$body = sprintf("`c`&Maintenance Report`c`n`^Dwelling Name: `@%s`n`^Dwelling Type: %s`n`^Location: `0%s`n`^Report: `6Broken Table`n`nThis is a note from your Maintenance Department from your dwelling.  We have received a report that `&%s`6 broke your %s`6.  You will need to purchase a new one to replace it.",$name,$ctype,$location,$session['user']['name'],$furn);
							systemmail($ownerid,$subj,$body);
						}
					}
				}else output("You leave the table a little refreshed, but not much.");
			}else{
				set_module_pref("usedtable",1);
				switch(e_rand($low,$high)){
					case 1: 
						output("You feel the table wobble and realize there's something under one of the legs.  You find a little bag of gold that's causing it to wobble!");
						output("`n`nYou count out `^25 gold pieces`@! Yay!");
						$session['user']['gold']+=25;
					break;
					case 2: case 3: case 4: case 5:
						output("The table is kind of wobbly.");
						if ($session['user']['gold']>0){
							$session['user']['gold']--;
							output("You stabilize it with a gold piece.`n`nYou `\$lose `^one gold`@.");
						}else output("There's not much you can do to improve it.  Oh well.");
					break;
					case 6: 
						output("You sit awkwardly at the table and hear a cracking sound.");
						set_module_objpref("dwellings",$dwid,"tablestress",get_module_objpref("dwellings",$dwid,"tablestress")+1);
						$tablestress=get_module_objpref("dwellings",$dwid,"tablestress");
						$finalstress=get_module_setting("tablestress","furniture")+$furnt-1;
						if ($tablestress>=$finalstress){
							output("`n`nOh no! You've broken the table!");
							set_module_objpref("dwellings",$dwid,"table",0);
							set_module_objpref("dwellings",$dwid,"tablestress",0);
							if ($ownerid==$session['user']['acctid']) output("It looks like you'll have to get a new one now.");
							else{
								require_once("lib/systemmail.php");
								output("It's not even your table! You better feel guilty now, because %s`@ is going to find out through YoM that you broke %s table!",$ownname,translate_inline($ownsex?"her":"his"));
								$subj = sprintf("`&Broken Table Report");
								$body = sprintf("`c`&Maintenance Report`c`n`^Dwelling Name: `@%s`n`^Dwelling Type: %s`n`^Location: `0%s`n`^Report: `6Broken Table`n`nThis is a note from your Maintenance Department from your dwelling.  We have received a report that `&%s`6 broke your %s`6.  You will need to purchase a new one to replace it.",$name,$ctype,$location,$session['user']['name'],$furn);
								systemmail($ownerid,$subj,$body);
							}
						}
					break;
					case 7: case 8: case 9: case 10: case 11: case 12: case 13: case 14: case 15: case 16: case 17: 
						output("You sit comfortably in the table and have an `4apple`@.`n`nThat was nice. You gain a hitpoint.");
						$session['user']['hitpoints']++;
					break;
					case 18: case 19: case 20: case 21: case 22: case 23: case 24: case 25: 
						output("You sit comfortably at the table and have a nice `Qorange`@.`n`n");
						output("You gain a turn!");
						$session['user']['turns']++;
					break;
					case 26: case 27:
						output("You sit at the table and have an amazing `6pear`@.  Pleasure spreads through your whole body.");
						output("`n`nYou gain two turns!");
						$session['user']['turns']+=2;
					break;
					case 28: case 29: case 30:
						output("You sit down for a piece of `^grapefruit`@.  That's the power fruit, you know? Pleasure spreads through your whole body.");
						output("`n`nYou gain two turns!`n`nIn addition, you appear more charming because of the smile on your face. You gain `&one charm`@!");
						$session['user']['turns']+=2;
						$session['user']['charm']++;
					break;
				}
			}
		}
		if ($op2=="bed"){
			output("`n`@You decide to jump up and down on the `2%s`@.`n`n",$furn);
			if (get_module_pref("usedbed")>=1){
				increment_module_pref("usedbed",1);
				if ($session['user']['turns']>0 && $rand<=100 && $rand>1){
					output("You jump up and down for a little while but realize that you've made a mess. You `\$lose`@ one turn making the bed.");
					$session['user']['turns']--;
				}elseif ($rand==1){
					output("You tuck and do a full flip! How cool is that??? However, you fail to stick the landing and twist your ankle.");
					output("`n`nYou `bGain 5 Turns`b but you `4lose all your hitpoints except 1`@.");
					$session['user']['turns']+=5;
					$session['user']['hitpoints']=1;
				}elseif($rand>190){
					output("You jump up and down on the bed and hear a cracking sound.");
					set_module_objpref("dwellings",$dwid,"bedstress",get_module_objpref("dwellings",$dwid,"bedstress")+1);
					$bedstress=get_module_objpref("dwellings",$dwid,"bedstress");
					$finalstress=get_module_setting("bedstress","furniture")+$furnt-1;
					if ($bedstress>=$finalstress){
						output("`n`nOh no! You've broken the bed!");
						set_module_objpref("dwellings",$dwid,"bed",0);
						set_module_objpref("dwellings",$dwid,"bedstress",0);
						if ($ownerid==$session['user']['acctid']) output("It looks like you'll have to get a new one now.");
						else{
							require_once("lib/systemmail.php");
							output("It's not even your bed! You better feel guilty now, because %s`@ is going to find out through YoM that you broke %s bed!",$ownname,translate_inline($ownsex?"her":"his"));
							$subj = sprintf("`&Broken Bed Report");
							$body = sprintf("`c`&Maintenance Report`c`n`^Dwelling Name: `@%s`n`^Dwelling Type: %s`n`^Location: `0%s`n`^Report: `6Broken Bed`n`nThis is a note from your Maintenance Department from your dwelling.  We have received a report that `&%s`6 broke your %s`6.  You will need to purchase a new one to replace it.",$name,$ctype,$location,$session['user']['name'],$furn);
							systemmail($ownerid,$subj,$body);
						}
					}
				}else output("You feel a little refreshed after your workout, but not much.");
			}else{
				set_module_pref("usedbed",1);
				switch(e_rand($low,$high)){
					case 1: 
						output("You jump up and down for a little while and get bored. Instead, you decide to look under the bed.  You find a secret stash of gold!");
						output("`n`nYou count out `^50 gold pieces`@! Yay!");
						$session['user']['gold']+=50;
					break;
					case 2: case 3: case 4: case 5:
						output("The bed is kind of wobbly.");
						if ($session['user']['gold']>=5){
							$session['user']['gold']-=5;
							output("You stabilize it with 5 gold pieces.`n`nYou `\$lose `^five gold`@.");
						}else output("There's not much you can do to improve it.  Oh well.");
					break;
					case 6: 
						output("You jump up and down on the bed and hear a cracking sound.");
						set_module_objpref("dwellings",$dwid,"bedstress",get_module_objpref("dwellings",$dwid,"bedstress")+1);
						$bedstress=get_module_objpref("dwellings",$dwid,"bedstress");
						$finalstress=get_module_setting("bedstress","furniture")+$furnt-1;
						if ($bedstress>=$finalstress){
							output("`n`nOh no! You've broken the bed!");
							set_module_objpref("dwellings",$dwid,"bed",0);
							set_module_objpref("dwellings",$dwid,"bedstress",0);
							if ($ownerid==$session['user']['acctid']) output("It looks like you'll have to get a new one now.");
							else{
								require_once("lib/systemmail.php");
								output("It's not even your bed! You better feel guilty now, because %s`@ is going to find out through YoM that you broke %s bed!",$ownname,translate_inline($ownsex?"her":"his"));
								$subj = sprintf("`&Broken Bed Report");
								$body = sprintf("`c`&Maintenance Report`c`n`^Dwelling Name: `@%s`n`^Dwelling Type: %s`n`^Location: `0%s`n`^Report: `6Broken Bed`n`nThis is a note from your Maintenance Department from your dwelling.  We have received a report that `&%s`6 broke your %s`6.  You will need to purchase a new one to replace it.",$name,$ctype,$location,$session['user']['name'],$furn);
								systemmail($ownerid,$subj,$body);
							}
						}
					break;
					case 7: case 8: case 9: case 10: case 11: case 12: case 13: case 14: case 15: case 16: case 17: 
						output("You practice jumping and find you're getting better at it.  This is great fun!");
					break;
					case 18: case 19: case 20: case 21: case 22: case 23: case 24: case 25: 
						output("You practice jumping and find you're getting better at it.  This is great fun!");
						output("`n`nYou gain a turn!");
						$session['user']['turns']++;
					break;
					case 26: case 27:
						output("You jump on the bed and jump off, sticking your landing perfectly.  If someone had been watching, they'd be applauding you right now!");
						addnews("`#Everyone should applaud %s`# for sticking a perfect landing after jumping off the bed.",$session['user']['name']);
						output("`n`nYou gain two turns!");
						$session['user']['turns']+=2;
					break;
					case 28: case 29: case 30:
						output("You jump up and down and up and down.  It's like a workout! You feel your muscles bulge!");
						output("`n`nYou gain two turns!`n`nIn addition, you appear more charming because you're more physically fit now. You gain `&one charm`@!");
						$session['user']['turns']+=2;
						$session['user']['charm']++;
					break;
				}
			}
		}
		addnav("Continue","runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	}
}
//Inside the store: Purchasing the furniture
if ($loc=="store"){
	$op = httpget('op');
	$op2 = httpget('op2');
	$subop = httpget('subop');
	$dwid = httpget('dwid');
	$acctid = httpget('acctid');
	$type = httpget('type');
	$gold=httpget('gold');
	$gems=httpget('gems');
	$storename=get_module_setting("storename");
	$header = color_sanitize($storename);
	page_header("%s %s",$session['user']['location'],$header);
	addnav("Return to Hamlet","runmodule.php?module=dwellings");
	$dks=$session['user']['dragonkills'];
	
	$id1=get_module_setting("modulename1");
	$id2=get_module_setting("modulename2");
	$id3=get_module_setting("modulename3");
	$id4=get_module_setting("modulename4");
	$id5=get_module_setting("modulename5");
	$id6=get_module_setting("modulename6");
	$id7=get_module_setting("modulename7");
	$id8=get_module_setting("modulename8");
	$c1=get_module_setting("chair1");
	$c2=get_module_setting("chair2");
	$c3=get_module_setting("chair3");
	$c4=get_module_setting("chair4");
	$c5=get_module_setting("chair5");
	$c6=get_module_setting("chair6");
	$c7=get_module_setting("chair7");
	$c8=get_module_setting("chair8");
	$t1=get_module_setting("table1");
	$t2=get_module_setting("table2");
	$t3=get_module_setting("table3");
	$t4=get_module_setting("table4");
	$t5=get_module_setting("table5");
	$t6=get_module_setting("table6");
	$t7=get_module_setting("table7");
	$t8=get_module_setting("table8");
	$b1=get_module_setting("bed1");
	$b2=get_module_setting("bed2");
	$b3=get_module_setting("bed3");
	$b4=get_module_setting("bed4");
	$b5=get_module_setting("bed5");
	$b6=get_module_setting("bed6");
	$b7=get_module_setting("bed7");
	$b8=get_module_setting("bed8");
	
	output("`c`b%s %s`b`c`n",$session['user']['location'],$storename);
	if ($op=="enter"){
		$owner=get_module_setting("owner");
		$nothing=0;
		if ($c1==0 && $c2==0 && $c3==0 && $c4==0 && $c5==0 && $c6==0 && $c7==0 && $c8==0 && $t1==0 && $t2==0 && $t3==0 && $t4==0 && $t5==0 && $t6==0 && $t7==0 && $t8==0 && $b1==0 && $b2==0 && $b3==0 && $b4==0 && $b5==0 && $b6==0 && $b7==0 && $b8==0) $nothing=1;
	
		output("`3You enter a store with furniture all over the place. The store owner walks over and makes formal introductions.");
		output("`n`n`%'My name is %s`%.  Welcome to the furniture store. Very simply, my furniture is for use in your dwellings.'",$owner);
		if ($nothing==1) output("`n`n'Sadly, none of the dwellings can use my furniture right now.'");
		else{
			output("`n`n'Certain dwellings can support certain types of furniture. Here's the breakdown:'");
			$fname=translate_inline("`bDwelling Type`b");
			$chairs=translate_inline("`bChairs`b");
			$tables=translate_inline("`bTables`b");
			$beds=translate_inline("`bBeds`b");
			output_notl("`c");
			rawoutput("<table border=0 cellpadding=0 cellspacing=1 bgcolor='#999999'>");
			rawoutput("<tr class='trhead'>");
			rawoutput("<td align=center style=\"width:125px\">");
			output_notl($fname);
			rawoutput("</td><td align=center style=\"width:75px\"><center>");
			output_notl($chairs);
			rawoutput("</center></td><td align=center style=\"width:75px\"><center>");
			output_notl($tables);
			rawoutput("</center></td><td align=center style=\"width:75px\"><center>");
			output_notl($beds);
			rawoutput("</center></td></tr>");
			$sql1="select module from ".db_prefix("dwellingtypes")."";
			$result1 = db_query($sql1);
			for ($i = 0; $i < db_num_rows($result1); $i++){
				$row1 = db_fetch_assoc($result1);
				$module=$row1['module'];
				for ($h = 0; $h < 8; $h++){
					if (get_module_setting("modulename".$h)==$module && is_module_active($module)){
						$name = strtoupper(get_module_setting("dwnameplural",$module));
						if (get_module_setting("chair".$h)>0) $chairs=translate_inline("`&Yes");
						else $chairs=translate_inline("No");
						if (get_module_setting("table".$h)>0) $tables=translate_inline("`&Yes");
						else $tables=translate_inline("No");
						if (get_module_setting("bed".$h)>0) $beds=translate_inline("`&Yes");
						else $beds=translate_inline("No");
						rawoutput("<tr class='trdark'><td>");
						output_notl("%s",$name);
						rawoutput("</center></td><td><center>");
						output_notl("%s",$chairs);
						rawoutput("</center></td><td><center>");
						output_notl("%s",$tables);
						rawoutput("</center></td><td><center>");
						output_notl("%s",$beds);
						rawoutput("</center></td></tr>");
					}
				}
			}
			if ($id8==1){
				$name = "All Other Dwellings";
				if ($c8>0) $chairs=translate_inline("`&Yes");
				else $chairs=translate_inline("No");
				if ($t8>0) $tables=translate_inline("`&Yes");
				else $tables=translate_inline("No");
				if ($b8>0) $beds=translate_inline("`&Yes");
				else $beds=translate_inline("No");
				rawoutput("<tr class='trdark'><td>");
				output_notl("%s",$name);
				rawoutput("</center></td><td><center>");
				output_notl("%s",$chairs);
				rawoutput("</center></td><td><center>");
				output_notl("%s",$tables);
				rawoutput("</center></td><td><center>");
				output_notl("%s",$beds);
				rawoutput("</center></td></tr>");
			}
			rawoutput("</table>");
			output_notl("`c");
			output_notl("`n`n");
			$ownerid = $session['user']['acctid'];
			//thanks to Chris Vorndran for help with the next line
			$sql = "SELECT name,type,dwid FROM ".db_prefix("dwellings")." WHERE status=1  AND location='".$session['user']['location']."' AND ownerid='".$session['user']['acctid']."'ORDER BY type";
			$result = db_query($sql);
			$name = translate_inline("Name");
			$type = translate_inline("Type");
			if (db_num_rows($result)>0){
				output("`%'You have the following dwelling%s in this village:'`n`n",translate_inline(db_num_rows($result)>1?"s":""));
				output_notl("`c");
				rawoutput("<table border=0 cellpadding=0 cellspacing=1 bgcolor='#999999'>");
				rawoutput("<tr class='trhead'>");
				rawoutput("<td align=center style=\"width:150px\">");
				rawoutput("$name</td>");
				rawoutput("<td align=center style=\"width:100px\">");
				rawoutput("$type</td>");
				for ($i = 0; $i < db_num_rows($result); $i++){ 
					$row = db_fetch_assoc($result);
					$ctype = translate_inline(get_module_setting("dwname",$row['type']));
					$name = color_sanitize($row['name']);
					$dwid = $row['dwid'];
					if($name == "") $name = translate_inline("Unnamed");
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");			
					output_notl("<a href='runmodule.php?module=furniture&loc=store&op=review&dwid=$dwid'>$name</a>",true);
					addnav("","runmodule.php?module=furniture&loc=store&op=review&dwid=$dwid");
					rawoutput("</td><td align=center>");
					output_notl("<a href='runmodule.php?module=furniture&loc=store&op=review&dwid=$dwid'>$ctype</a>",true);
					addnav("","runmodule.php?module=furniture&loc=store&op=review&dwid=$dwid");
					rawoutput("</td></tr>");
					addnav("Name / Type");
					addnav(array("%s`^ / %s",$name,$ctype),"runmodule.php?module=furniture&loc=store&op=review&dwid=$dwid");
				}
				rawoutput("</table>");
				output_notl("`c");
				output("`n`%'If you'd like to choose some furniture, please pick your dwelling for the furniture.'");
				if ($dks<get_module_setting("dktable") || $dks<get_module_setting("dkbed") || get_module_setting("perdk")==1) {
					output("`n`n`^*Note:");
					if ($dks<get_module_setting("dktable") || $dks<get_module_setting("dkbed")) {
						output("You will not be able to purchase all types of furniture until you have excelled at killing the `@Green Dragon`^.");
						if (get_module_setting("perdk")==1) output("In Addition:");
					}
					if (get_module_setting("perdk")==1) output("You may only purchase one of each type of furniture per dragon kill, so choose wisely.");
				}
			}else{
				output("`%'You don't seem to have any dwellings in this city that are completed.  Please feel free to stop by when you've completed a dwelling here.'");
			}
		}
	}
	if ($op=="review"){
		addnav("Store Entrance","runmodule.php?module=furniture&loc=store&op=enter");
		$sql = "SELECT name,type FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$name = $row['name'];
		if($name == "") $name = translate_inline("Unnamed");
		$type = $row['type'];
		$ctype = translate_inline(get_module_setting("dwname",$row['type']));
		for ($i = 0; $i < 8; $i++){
			$j=$i+1;
			if ($type==get_module_setting("modulename".$j)) $a=$j;
		}
		$c="c".$a;
		$t="t".$a;
		$b="b".$a;
		$perdk=get_module_setting("perdk");
		if ($$c==0 && $$t==0 && $$b==0) output("`%'As I mentioned earlier, a %s`% can't support any furniture.'`n`n",$ctype);
		else {
			if (get_module_objpref("dwellings",$dwid,"chair")>0 || get_module_objpref("dwellings",$dwid,"table")>0 || get_module_objpref("dwellings",$dwid,"bed")>0){
				output("`#You recall what furnishings are already in your %s`#:`n`n",$ctype);
				if (get_module_objpref("dwellings",$dwid,"chair")==1) output("`cBasic Chair`c");
				elseif (get_module_objpref("dwellings",$dwid,"chair")==2) output("`cGeneral Chair: `&%s`#`c",stripslashes(get_module_objpref("dwellings",$dwid,"custchair")));
				elseif (get_module_objpref("dwellings",$dwid,"chair")==3) output("`cHeirloom Chair: `%%s`#`c",stripslashes(get_module_objpref("dwellings",$dwid,"custchair")));
				if (get_module_objpref("dwellings",$dwid,"table")==1) output("`cBasic Table`c");
				elseif (get_module_objpref("dwellings",$dwid,"table")==2) output("`cGeneral Table: `&%s`#`c",stripslashes(get_module_objpref("dwellings",$dwid,"custtable")));
				elseif (get_module_objpref("dwellings",$dwid,"table")==3) output("`cHeirloom Table: `%%s`#`c",stripslashes(get_module_objpref("dwellings",$dwid,"custtable")));
				if (get_module_objpref("dwellings",$dwid,"bed")==1) output("`cBasic Bed`c");
				elseif (get_module_objpref("dwellings",$dwid,"bed")==2) output("`cGeneral Bed: `&%s`#`c",stripslashes(get_module_objpref("dwellings",$dwid,"custbed")));
				elseif (get_module_objpref("dwellings",$dwid,"bed")==3) output("`cHeirloom Bed: `%%s`#`c",stripslashes(get_module_objpref("dwellings",$dwid,"custbed")));
				output_notl("`n");
			}
			if ($perdk==1 && get_module_pref("buychair")==1 && get_module_pref("buytable")==1 && get_module_pref("buybed")==1) output("You've already purchased all the furniture this dragon kill`n`n");
			else{
				//Basic Furniture
				if ($$c==1 || $$t==1|| $$b==1){
					output("`#You look over the furniture for your %s %s`#.  Here's what's available:`n`n",$name,$ctype);
					output_notl("`c");
					$fname=translate_inline("`bFurniture`b");
					$gold=translate_inline("`bGold Cost`b");
					$gems=translate_inline("`bGem Cost`b");
					rawoutput("<table border='0' cellpadding='0'>");
					rawoutput("<tr class='trhead'><td>");
					output_notl($fname);
					rawoutput("</td><td><center>");
					output_notl($gold);
					rawoutput("</center></td><td><center>");
					output_notl($gems);
					rawoutput("</center></td></tr>");
				}
				if ($perdk==1 && get_module_pref("buychair")==1) output("You've already purchased a chair this dragon kill.");
				elseif ($$c==1){
					$gold=get_module_setting("bchairgo");
					$gems=get_module_setting("bchairge");
					rawoutput("<tr class='trlight'><td>");
					rawoutput("<a href='runmodule.php?module=furniture&loc=store&op=buy&op2=1&gold=$gold&gems=$gems&dwid=$dwid'>");
					addnav("","runmodule.php?module=furniture&loc=store&op=buy&op2=1&gold=$gold&gems=$gems&dwid=$dwid");
					addnav("Basic Furniture");
					addnav("Basic Chair","runmodule.php?module=furniture&loc=store&op=buy&op2=1&gold=$gold&gems=$gems&dwid=$dwid");
					output("Basic Chair");
					rawoutput("</a>");
					rawoutput("</td><td><center>");
					output_notl("`^%s",$gold);
					rawoutput("</center></td><td><center>");
					output_notl("`%%s",$gems);
					rawoutput("</center></td></tr>");
				}
				if ($perdk==1 && get_module_pref("buytable")==1) output("You've already purchased a table this dragon kill.");
				elseif ($$t==1 && $dks>=get_module_setting("dktable")){
					$gold=get_module_setting("btablego");
					$gems=get_module_setting("btablege");
					rawoutput("<tr class='trdark'><td>");
					rawoutput("<a href='runmodule.php?module=furniture&loc=store&op=buy&op2=2&gold=$gold&gems=$gems&dwid=$dwid'>");
					addnav("","runmodule.php?module=furniture&loc=store&op=buy&op2=2&gold=$gold&gems=$gems&dwid=$dwid");
					addnav("Basic Furniture");
					addnav("Basic Table","runmodule.php?module=furniture&loc=store&op=buy&op2=2&gold=$gold&gems=$gems&dwid=$dwid");
					output("Basic Table");
					rawoutput("</a>");
					rawoutput("</td><td><center>");
					output_notl("`^%s",$gold);
					rawoutput("</center></td><td><center>");
					output_notl("`%%s",$gems);
					rawoutput("</center></td></tr>");
				}
				if ($perdk==1 && get_module_pref("buybed")==1) output("You've already purchased a bed this dragon kill.");
				elseif ($$b==1 && $dks>=get_module_setting("dkbed")){
					$gold=get_module_setting("bbedgo");
					$gems=get_module_setting("bbedge");
					rawoutput("<tr class='trlight'><td>");
					rawoutput("<a href='runmodule.php?module=furniture&loc=store&op=buy&op2=3&gold=$gold&gems=$gems&dwid=$dwid'>");
					addnav("","runmodule.php?module=furniture&loc=store&op=buy&op2=3&gold=$gold&gems=$gems&dwid=$dwid");
					addnav("Basic Furniture");
					addnav("Basic Bed","runmodule.php?module=furniture&loc=store&op=buy&op2=3&gold=$gold&gems=$gems&dwid=$dwid");
					output("Basic Bed");
					rawoutput("</a>");
					rawoutput("</td><td><center>");
					output_notl("`^%s",$gold);
					rawoutput("</center></td><td><center>");
					output_notl("`%%s",$gems);
					rawoutput("</center></td></tr>");
				}
				if ($$c==1 || $$t==1 || $$b==1){
					rawoutput("</table>");
					output_notl("`c`n");
				}
				//Furniture Count
				$genchair=0;
				$heirchair=0;
				$gentable=0;
				$heirtable=0;
				$genbed=0;
				$heirbed=0;
				$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
				$res = db_query($sql);
				for ($i=0;$i<db_num_rows($res);$i++){
					$row = db_fetch_assoc($res);			
					$allprefs=unserialize(get_module_pref('allprefs','jobs',$row['acctid']));
					if ($allprefs['cust1']==1) $genchair=$genchair+1;
					if ($allprefs['cust2']==1) $heirchair=$heirchair+1;
					if ($allprefs['cust3']==1) $gentable=$gentable+1;
					if ($allprefs['cust4']==1) $heirtable=$heirtable+1;
					if ($allprefs['cust5']==1) $genbed=$genbed+1;
					if ($allprefs['cust6']==1) $heirbed=$heirbed+1;
				}
				//General Furniture Heading
				if ((($perdk==1 && (get_module_pref("buychair")==0 || get_module_pref("buytable")==0 || get_module_pref("buybed")==0)) ||$perdk==0)&&(($genchair>0 && $$c==1)||($gentable>0 && $$t==1 && $dks>=get_module_setting("dktable"))||($genbed>0 && $$b==1 && $dks>=get_module_setting("dkbed")))) {
					output_notl("`c");
					$fname=translate_inline("`bGeneral Furniture`b");
					$gold=translate_inline("`bGold Cost`b");
					$gems=translate_inline("`bGem Cost`b");
					rawoutput("<table border='0' cellpadding='0'>");
					rawoutput("<tr class='trhead'><td>");
					output_notl($fname);
					rawoutput("</td><td><center>");
					output_notl($gold);
					rawoutput("</center></td><td><center>");
					output_notl($gems);
					rawoutput("</center></td></tr>");
				}
				//General Chair
				if ($$c==1 && $genchair>0 && (($perdk==1 && get_module_pref("buychair")==0)||$perdk==0)){
					$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
					$res = db_query($sql);
					for ($i=0;$i<db_num_rows($res);$i++){
						$row = db_fetch_assoc($res);
						$acctid=$row['acctid'];
						$allprefs=unserialize(get_module_pref('allprefs','jobs',$acctid));
						$gold=round(get_module_setting("custchairgo1")*e_rand(100,103)/100);
						$gems=get_module_setting("custchairge1")+e_rand(0,1);
						$fname=$allprefs['name1'];
						if ($fname>""){
							rawoutput("<tr class='trlight'><td>");
							rawoutput("<a href=\"runmodule.php?module=furniture&loc=store&op=buy&op2=4&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid\">",true);
							output("`0Chair:");
							output_notl($fname);
							rawoutput("</a>");
							addnav("","runmodule.php?module=furniture&loc=store&op=buy&op2=4&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							rawoutput("</td><td><center>");
							addnav("General Furniture");
							addnav(array("Chair: %s", $fname),"runmodule.php?module=furniture&loc=store&op=buy&op2=4&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							output_notl("`^%s",$gold);
							rawoutput("</center></td><td><center>");
							output_notl("`%%s",$gems);
							rawoutput("</center></td></tr>");
						}
					}
				}
				//General Table
				if ($$t==1 && $gentable>0 && $dks>=get_module_setting("dktable") && (($perdk==1 && get_module_pref("buytable")==0)||$perdk==0)){
					$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
					$res = db_query($sql);
					for ($i=0;$i<db_num_rows($res);$i++){
						$row = db_fetch_assoc($res);
						$acctid=$row['acctid'];
						$allprefs=unserialize(get_module_pref('allprefs','jobs',$acctid));
						$gold=round(get_module_setting("custtablego1")*e_rand(100,105)/100);
						$gems=get_module_setting("custtablege1")+e_rand(0,2);
						$fname=$allprefs['name3'];
						if ($fname>""){
							rawoutput("<tr class='trdark'><td>");
							rawoutput("<a href=\"runmodule.php?module=furniture&loc=store&op=buy&op2=6&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid\">",true);
							output("`0Table:");
							output_notl($fname);
							rawoutput("</a>");
							addnav("","runmodule.php?module=furniture&loc=store&op=buy&op2=6&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							addnav("General Furniture");
							addnav(array("Table: %s", $fname),"runmodule.php?module=furniture&loc=store&op=buy&op2=6&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							rawoutput("</td><td><center>");
							output_notl("`^%s",$gold);
							rawoutput("</center></td><td><center>");
							output_notl("`%%s",$gems);
							rawoutput("</center></td></tr>");
						}
					}
				}
				//General Bed
				if ($$b==1 && $genbed>0 && $dks>=get_module_setting("dkbed") && (($perdk==1 && get_module_pref("buybed")==0)||$perdk==0)){
					$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
					$res = db_query($sql);
					for ($i=0;$i<db_num_rows($res);$i++){
						$row = db_fetch_assoc($res);
						$acctid=$row['acctid'];
						$allprefs=unserialize(get_module_pref('allprefs','jobs',$acctid));
						$gold=round(get_module_setting("custbedgo1")*e_rand(100,110)/100);
						$gems=get_module_setting("custbedge1")+e_rand(0,2);
						$fname=$allprefs['name5'];
						if ($fname>""){
							rawoutput("<tr class='trlight'><td>");
							rawoutput("<a href=\"runmodule.php?module=furniture&loc=store&op=buy&op2=8&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid\">",true);
							output("`0Bed:");
							addnav("General Furniture");
							addnav(array("Bed: %s", $fname),"runmodule.php?module=furniture&loc=store&op=buy&op2=8&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							output_notl($fname);
							rawoutput("</a>");
							addnav("","runmodule.php?module=furniture&loc=store&op=buy&op2=8&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							rawoutput("</td><td><center>");
							output_notl("`^%s",$gold);
							rawoutput("</center></td><td><center>");
							output_notl("`%%s",$gems);
							rawoutput("</center></td></tr>");
						}
					}
				}
				if ((($perdk==1 && (get_module_pref("buychair")==0 || get_module_pref("buytable")==0 || get_module_pref("buybed")==0)) ||$perdk==0)&&(($genchair>0 && $$c==1)||($gentable>0 && $$t==1 && $dks>=get_module_setting("dktable"))||($genbed>0 && $$b==1 && $dks>=get_module_setting("dkbed")))) {
					rawoutput("</table>");
					output_notl("`c`n");
				}
				//Heirloom Heading
				if ((($perdk==1 && (get_module_pref("buychair")==0 || get_module_pref("buytable")==0 || get_module_pref("buybed")==0)) ||$perdk==0)&&(($heirchair>0 && $$c==1)||($heirtable>0 && $$t==1 && $dks>=get_module_setting("dktable"))||($heirbed>0 && $$b==1 && $dks>=get_module_setting("dkbed")))){
					output_notl("`c");
					$fname=translate_inline("`bHeirloom Furniture`b");
					$gold=translate_inline("`bGold Cost`b");
					$gems=translate_inline("`bGem Cost`b");
					rawoutput("<table border='0' cellpadding='0'>");
					rawoutput("<tr class='trhead'><td>");
					output_notl($fname);
					rawoutput("</td><td><center>");
					output_notl($gold);
					rawoutput("</center></td><td><center>");
					output_notl($gems);
					rawoutput("</center></td></tr>");
				}
				//Heirloom Chair
				if (($$c==1 && $heirchair>0) && (($perdk==1 && get_module_pref("buychair")==0)||$perdk==0)){
					$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
					$res = db_query($sql);
					for ($i=0;$i<db_num_rows($res);$i++){
						$row = db_fetch_assoc($res);
						$acctid=$row['acctid'];
						$allprefs=unserialize(get_module_pref('allprefs','jobs',$acctid));
						$gold=round(get_module_setting("custchairgo2")*e_rand(100,110)/100);
						$gems=get_module_setting("custchairge2")+e_rand(0,1);
						$fname=$allprefs['name2'];
						if ($fname>""){
							rawoutput("<tr class='trlight'><td>");
							rawoutput("<a href=\"runmodule.php?module=furniture&loc=store&op=buy&op2=5&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid\">",true);
							addnav("Heirloom Furniture");
							addnav(array("Chair: %s", $fname),"runmodule.php?module=furniture&loc=store&op=buy&op2=5&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							output("`0Chair:");
							output_notl($fname);
							rawoutput("</a>");
							addnav("","runmodule.php?module=furniture&loc=store&op=buy&op2=5&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							rawoutput("</td><td><center>");
							output_notl("`^%s",$gold);
							rawoutput("</center></td><td><center>");
							output_notl("`%%s",$gems);
							rawoutput("</center></td></tr>");
						}
					}
				}
				//Heirloom Table
				if (($$t==1 && $heirtable>0 && $dks>=get_module_setting("dktable")) && (($perdk==1 && get_module_pref("buybed")==0)||$perdk==0)){
					$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
					$res = db_query($sql);
					for ($i=0;$i<db_num_rows($res);$i++){
						$row = db_fetch_assoc($res);
						$acctid=$row['acctid'];
						$allprefs=unserialize(get_module_pref('allprefs','jobs',$acctid));
						$gold=round(get_module_setting("custtablego2")*e_rand(100,115)/100);
						$gems=get_module_setting("custtablege2")+e_rand(0,2);
						$fname=$allprefs['name4'];
						if ($fname>""){
							rawoutput("<tr class='trdark'><td>");
							rawoutput("<a href=\"runmodule.php?module=furniture&loc=store&op=buy&op2=7&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid\">",true);
							addnav("Heirloom Furniture");
							addnav(array("Table: %s", $fname),"runmodule.php?module=furniture&loc=store&op=buy&op2=7&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							output("`0Table:");
							output_notl($fname);
							rawoutput("</a>");
							addnav("","runmodule.php?module=furniture&loc=store&op=buy&op2=7&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							rawoutput("</td><td><center>");
							output_notl("`^%s",$gold);
							rawoutput("</center></td><td><center>");
							output_notl("`%%s",$gems);
							rawoutput("</center></td></tr>");
						}
					}
				}
				//Heirloom Bed
				if (($$b==1 && $heirbed>0 && $dks>=get_module_setting("dkbed")) && (($perdk==1 && get_module_pref("buybed")==0)||$perdk==0)){
					$sql = "SELECT acctid FROM ".db_prefix("accounts")."";
					$res = db_query($sql);
					for ($i=0;$i<db_num_rows($res);$i++){
						$row = db_fetch_assoc($res);
						$acctid=$row['acctid'];
						$allprefs=unserialize(get_module_pref('allprefs','jobs',$acctid));
						$gold=round(get_module_setting("custbedgo2")*e_rand(100,120)/100);
						$gems=get_module_setting("custbedge2")+e_rand(0,3);
						$fname=$allprefs['name6'];
						if ($fname>""){
							rawoutput("<tr class='trlight'><td>");
							rawoutput("<a href=\"runmodule.php?module=furniture&loc=store&op=buy&op2=9&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid\">",true);
							addnav("Heirloom Furniture");
							addnav(array("Bed: %s", $fname),"runmodule.php?module=furniture&loc=store&op=buy&op2=9&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							output("`0Bed:");
							output_notl($fname);
							rawoutput("</a>");
							addnav("","runmodule.php?module=furniture&loc=store&op=buy&op2=9&gold=$gold&gems=$gems&dwid=$dwid&acctid=$acctid");
							rawoutput("</td><td><center>");
							output_notl("`^%s",$gold);
							rawoutput("</center></td><td><center>");
							output_notl("`%%s",$gems);
							rawoutput("</center></td></tr>");
						}
					}			
				}
				if ((($perdk==1 && (get_module_pref("buychair")==0 || get_module_pref("buytable")==0 || get_module_pref("buybed")==0)) ||$perdk==0)&&(($heirchair>0 && $$c==1)||($heirtable>0 && $$t==1 && $dks>=get_module_setting("dktable"))||($heirbed>0 && $$b==1 && $dks>=get_module_setting("dkbed")))){
					rawoutput("</table>");
					output_notl("`c`n");		
				}
			}
		}
	}
	if ($op=="buy"){
		$sql = "SELECT name,type FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$name = $row['name'];
		if($name == "") $name = translate_inline("Unnamed");
		$type = $row['type'];
		$ctype = translate_inline(get_module_setting("dwname",$row['type']));
		if($op2==1 || $op2==2 || $op2==3) $quality=1;
		elseif ($op2==4 || $op2==6 || $op2==8) $quality=2;
		else $quality=3;
		// Watch out here... the chair is op2= 1,2,and 4.  Table is op2=2,6,7.  Bed is op2=3,8,9
		if ($op2==1 || $op2==4 || $op2==5) $item="chair";
		elseif ($op2==2 || $op2==6 || $op2==7) $item="table";
		else $item="bed";
		if (get_module_objpref("dwellings",$dwid,$item)>=$quality) output("`%'Well, it seems like you've had a %s`%  of at least this quality delivered to your %s`% already. Perhaps you'd like to look at some other furniture?",$itemid,$ctype);
		elseif ($session['user']['gold']>=$gold && $session['user']['gems']>=$gems){
			$session['user']['gold']-=$gold;
			$session['user']['gems']-=$gems;	
			if ($op2>3){
				//make $quality correlate with general (2) or heirloom (3) for the buyer's dwelling preference
				if ($op2==4 || $op2==6 || $op2==8) $quality=2;
				elseif ($op2==5 || $op2==7 || $op2==9) $quality=3;
				//make $furnitem correlate with the type of furniture to set the buyer's dwelling preference 
				if($op2==4 || $op2==5){
					$furnitem="chair";
					set_module_pref("buychair",1);
				}elseif($op2==6 || $op2==7){ 
					$furnitem="table";
					set_module_pref("buytable",1);
				}elseif($op2==8 || $op2==9){
					$furnitem="bed";
					set_module_pref("buybed",1);
				}
				//make op2 correlate with the custom item number from the Wood Shop Module
				$op2=$op2-3;
				//This is the customized name of the piece of furniture that was sold
				$allprefs=unserialize(get_module_pref('allprefs','jobs',$acctid));
				$item=$allprefs['name'.$op2];
				//Set the buyer's dwelling furniture type to the purchased type
				set_module_objpref("dwellings",$dwid,$furnitem,$quality);
				//Set the buyer's dwelling furniture name to the purchased type
				set_module_objpref("dwellings",$dwid,"cust".$furnitem,$item);
				$storename=get_module_setting("storename");
				if (get_module_pref("user_stat","jobs",$acctid)==1){
					require_once("lib/systemmail.php");
					$subj = sprintf("Successful Furniture Sale");
					$body = sprintf("`&`cCongratulations!!`c`n%s`6 recently purchased your `&%s`6 from the %s Store`6.",$session['user']['name'],$item,$storename);
					systemmail($acctid,$subj,$body);
				}
				//Reset the seller's furniture type to 0 and clear the furniture name that was sold to 0 in the Wood shop Module
				$allprefs['cust'.$op2]=0;
				$allprefs['name'.$op2]="";
				set_module_pref('allprefs',serialize($allprefs),'jobs',$acctid);
			}else{
				set_module_objpref("dwellings",$dwid,$item,$quality);
				if ($op2==1) set_module_pref("buychair",1);
				elseif ($op2==2) set_module_pref("buytable",1);
				else set_module_pref("buybed",1);
			}
			output("`%'That sounds like an excellent addition to your %s`%.  I will have my people deliver your brand new `&%s`% to you right away. If you have any other needs, please feel free to peruse our showroom once again.'",$ctype,$item);
		}else output("`%'Unfortunately, you don't have enough money on hand to purchase a %s`%.  Please come back when you have more funds.'",$item);
		addnav("Store Entrance","runmodule.php?module=furniture&loc=store&op=enter");
	}
}
page_footer();

?>