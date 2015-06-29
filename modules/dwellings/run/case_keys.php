<?php
	$subop = httpget('subop');
	$confirm = httpget('confirm');
	$keyid = httpget('keyid');
	$keyowner = httpget('keyowner');
	$sql = "SELECT ownerid FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
	$res = db_query($sql);
	$row = db_fetch_assoc($res);
	$ownerid=$row['ownerid'];
	if($session['user']['acctid']==$ownerid){
		addnav("Back to Management", "runmodule.php?module=dwellings&op=manage&dwid=$dwid"); 
	}
	addnav("Back to Dwelling","runmodule.php?module=dwellings&op=enter&dwid=$dwid");
	switch ($subop){
		case "":
			page_header("Key Management");
			output("Here you can see and manage who has keys to this dwelling.`n");
			$sql = "SELECT keyowner,keyid FROM ".db_prefix("dwellingkeys")."
					WHERE dwid = $dwid 
					AND keyowner != ".$session['user']['acctid']." 
					ORDER BY keyid ASC";
			$result = db_query($sql);
			$number = translate_inline("Number");
			$owner = translate_inline("Owner");
			$take = translate_inline("Take back");		
			$give = translate_inline("Give away");
			$ops = translate_inline("Options");
			rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'><tr class='trhead'><td style=\"width:35px\">$number</td><td style='width:250px' align=center>$owner</td><td align=center>$ops</td></tr>"); 
			$maxkeys = get_module_setting("maxkeys",$type);
			$return=modulehook("dwellings-maxkeys", array("type"=>$type,"dwid"=>$dwid,"maxkeys"=>$maxkeys));
			$maxkeys=$return['maxkeys'];
			for ($i=0;$i<$maxkeys;$i++){
				$row = db_fetch_assoc($result);			
				if($row['keyid']>0 && $row['keyowner']!=0){
					$sql2 = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=".$row['keyowner'];
					$result2 = db_query($sql2);
					$row2 = db_fetch_assoc($result2);			
					$name = $row2['name'];
					if($name == "") $name = translate_inline("No one");
					$num = $i+1;
					$keyid = $row['keyid'];
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
					output_notl($num);
					rawoutput("</td><td align=center>");
					output_notl($name);
					rawoutput("</td><td>");
					addnav("","runmodule.php?module=dwellings&op=keys&subop=takekey&keyid=$keyid&dwid=$dwid");
					rawoutput("<a href=runmodule.php?module=dwellings&op=keys&subop=takekey&keyid=$keyid&dwid=$dwid>");
					output_notl("`#[`&$take`#]`0");
					rawoutput("</a></td></tr>");
				}else{
					$name = translate_inline("No one");
					$num = $i+1;
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
					addnav("","runmodule.php?module=dwellings&op=keys&subop=givekey&dwid=$dwid");
					output_notl($num);
					rawoutput("</td><td align=center>");
					output_notl($name);
					rawoutput("</td><td>");
					rawoutput("<a href=runmodule.php?module=dwellings&op=keys&subop=givekey&dwid=$dwid>");
					output_notl("`#[`&$give`#]`0");
					rawoutput("</a></td></tr>");		   
				}
			}
			rawoutput("</table>");
			break;
		case "giveback":
			if($confirm){
				$dwellingkeys = db_prefix("dwellingkeys");
				$dwellings = db_prefix("dwellings");
				$sql = "SELECT $dwellingkeys.dwidowner AS dwidowner, 
								$dwellings.type AS type,
								$dwellings.location AS location
						FROM $dwellingkeys
						INNER JOIN $dwellings ON $dwellingkeys.dwid = $dwellings.ownerid
						WHERE $dwellingkeys.keyowner = {$session['user']['acctid']}
						AND $dwellingkeys.dwid = $dwid";
//				$sql = "SELECT dwidowner FROM ".db_prefix("dwellingkeys")." WHERE keyowner=".$session['user']['acctid']." AND dwid=$dwid";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				$msg = "`2%s has returned their key to your %s`2 in %s.";
				$mailmessage = array($msg,	$session['user']['name'], translate_inline(get_module_setting("dwname",$row['type'])), $row['location']);
				require_once("lib/systemmail.php");
				systemmail($row['dwidowner'], array("`2Key returned!"), $mailmessage);
				output("The key has been returned.");
				blocknav("runmodule.php?module=dwellings&op=enter&dwid=$dwid");
//				$sql2 = "SELECT type,location FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
//				$result2 = db_query($sql2);
//				$row2 = db_fetch_assoc($result2);
				$sql = "UPDATE ".db_prefix("dwellingkeys")." SET keyowner=0 WHERE keyowner=".$session['user']['acctid']." AND dwid=".$dwid;
				db_query($sql);
			}else{
				output("Are you sure you want to give back your key to this dwelling?");
				addnav("Yes","runmodule.php?module=dwellings&op=keys&subop=giveback&confirm=1&dwid=$dwid");
			}
			break;
		case "givekey":
			output("`2Who do you want to give the key to?`n`n");
			$submit = translate_inline("Submit");
			rawoutput("<form action='runmodule.php?module=dwellings&op=keys&subop=givekey2&keyid=$keyid&dwid=$dwid' method='POST'>");
			addnav("","runmodule.php?module=dwellings&op=keys&subop=givekey2&keyid=$keyid&dwid=$dwid");
			rawoutput("<input name='name' id='name'> <input type='submit' class='button' value='$submit'>");
			rawoutput("</form>");
			rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>");
			break;
		case "givekey2":
			// login is never used... and we should search for the login first... 
			// It's faster, more secure (except you can tell the difference of a white ctitle and
			// a not-colored ctitle by just looking at it in the commentary area :)
			//			$sql = "SELECT login,name,level,acctid FROM accounts WHERE name = '".addslashes($_POST['name'])."' AND acctid != ".$session['user']['acctid']." AND locked=0 ORDER BY level,login";
			$sql = "SELECT name,level,acctid FROM ".db_prefix("accounts")." WHERE login = '".addslashes($_POST['name'])."' AND acctid != ".$session['user']['acctid']." AND locked=0 ORDER BY level,login";
			$result = db_query($sql);
			if (db_num_rows($result) <> 1) {
				$string="%";
				for ($x=0;$x<strlen(httppost('name'));$x++){
					$string .= substr(httppost('name'),$x,1)."%";
				}
				// Here we may have a look at the name :)
				//				$sql = "SELECT login,name,level,acctid FROM accounts WHERE name LIKE '".addslashes($string)."' AND acctid != ".$session['user']['acctid']." AND locked=0 ORDER BY level,login";
				$sql = "SELECT name,level,acctid FROM ".db_prefix("accounts")." WHERE name LIKE '".addslashes($string)."' AND acctid != ".$session['user']['acctid']." AND locked=0 ORDER BY level,login";
				$result = db_query($sql);
			}
			if (db_num_rows($result)<=0){
				output("`2There is no one with that name.");
			}elseif(db_num_rows($result)>100){
				output("Found over 100 players that fit that description. Please try to be more exact.`n`n");
				rawoutput("<form action='runmodule.php?module=dwellings&op=keys&subop=givekey2&keyid=$keyid&dwid=$dwid' method='POST'>");
				addnav("","runmodule.php?module=dwellings&op=keys&subop=givekey2&keyid=$keyid&dwid=$dwid");
				output("`2Who do you want to give the key to?`n`n");
				rawoutput("<input name='name' id='name'> <input type='submit' class='button' value='Suchen'>",true);
				rawoutput("</form>");
				rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>");
			}else{
				output("Which player did you mean?`n`n");
				rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
				rawoutput("<tr class='trhead'><td>Name</td><td>Level</td></tr>");
				for ($i=0;$i<db_num_rows($result);$i++){
					$row = db_fetch_assoc($result);
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
					// A number is a number is a number... :)
					//					rawoutput("<a href='runmodule.php?module=dwellings&op=keys&subop=givekey3&keyid=$keyid&dwid=$dwid&keyowner=".HTMLEntities($row['acctid'])."'>");
					rawoutput("<a href='runmodule.php?module=dwellings&op=keys&subop=givekey3&keyid=$keyid&dwid=$dwid&keyowner=".$row['acctid']."'>");
					output_notl($row['name']);
					rawoutput("</a></td><td>");
					output_notl($row['level']);
					rawoutput("</td></tr>");
//					addnav("","runmodule.php?module=dwellings&op=keys&subop=givekey3&keyid=$keyid&dwid=$dwid&keyowner=".HTMLEntities($row['acctid']));
					addnav("","runmodule.php?module=dwellings&op=keys&subop=givekey3&keyid=$keyid&dwid=$dwid&keyowner=".$row['acctid']);
				}
				rawoutput("</table>",true);
			}
			break;
		case "givekey3":
			if($keyid == ""){
				$sql = "SELECT keyid FROM ".db_prefix("dwellingkeys")." WHERE keyowner = 0 AND dwid = $dwid LIMIT 1";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				$keyid = $row['keyid'];
			} 
			if($keyid==""){
				$sql = "INSERT INTO ".db_prefix("dwellingkeys")." (dwid,dwidowner,keyowner) VALUES ($dwid,".$session['user']['acctid'].",$keyowner)";
			}else{
				$sql = "UPDATE ".db_prefix("dwellingkeys")." SET keyowner = $keyowner WHERE keyid = $keyid";
			}
			db_query($sql);
			$sql2 = "SELECT location,type FROM ".db_prefix("dwellings")." WHERE dwid = ". $dwid;
			$row2 = db_fetch_assoc(db_query($sql2));
			require_once("lib/systemmail.php");
			$cname = translate_inline(get_module_setting("dwname",$row2['type']));
			output("The key will be delivered to them right away!");
			systemmail($keyowner,array("`^You have a key!`0"),array("`&%s`& has given you a key to their %s`0 in %s",$session['user']['name'],$cname,$row2['location']));
			break;
		case "takekey":
			$sql = "SELECT keyowner FROM ".db_prefix("dwellingkeys")." WHERE keyid=$keyid";
			$res = db_query($sql);
			$row1 = db_fetch_assoc($res);
			$sql = "UPDATE ".db_prefix("dwellingkeys")." SET keyowner = 0 WHERE keyid = $keyid AND dwid = $dwid";
			db_query($sql);
			$sql = "SELECT location,type FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
			$res = db_query($sql);
			$row = db_fetch_assoc($res);
			output("Key successfully taken back.");
			require_once("lib/systemmail.php");
			systemmail($row1['keyowner'], array("`^Key Taken!`0"),array("%s has taken back their key to their %s in %s!",$session['user']['name'],get_module_setting("dwname",$row['type']),$row['location']));
			break;
			}
?>