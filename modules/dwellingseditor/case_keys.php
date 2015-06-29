<?php
$subop = httpget('subop');
        $confirm = httpget('confirm');
        $keyid = httpget('keyid');
		$submit = translate_inline("Submit");		
        if($subop==""){
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
            $return = modulehook("dwellings-maxkeys", array(
                    "type"=>$type,
                    "dwid"=>$dwid,
                    "maxkeys"=>$maxkeys
                    )
                );
            $maxkeys = $return['maxkeys'];    
            for ($i = 0; $i < $maxkeys; $i++){
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
                    addnav("","runmodule.php?module=dwellingseditor&op=keys&subop=takekey&keyid=$keyid&dwid=$dwid");
                    rawoutput("<a href=runmodule.php?module=dwellingseditor&op=keys&subop=takekey&keyid=$keyid&dwid=$dwid>");
                    output_notl("`#[`&$take`#]`0");
                    rawoutput("</a></td></tr>");
                }else{
                    $name = translate_inline("No one");
                    $num = $i+1;
                    rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
                    addnav("","runmodule.php?module=dwellingseditor&op=keys&subop=givekey&dwid=$dwid");
                    output_notl($num);
                    rawoutput("</td><td align=center>");
                    output_notl($name);
                    rawoutput("</td><td>");
                    rawoutput("<a href=runmodule.php?module=dwellingseditor&op=keys&subop=givekey&dwid=$dwid>");
                    output_notl("`#[`&$give`#]`0");
                    rawoutput("</a></td></tr>");           
                }
            }
            rawoutput("</table>");
        }
        if($subop == "giveback"){
            if($confirm){
                $sql = "SELECT dwidowner FROM ".db_prefix("dwellingkeys")." 
						WHERE keyowner=".$session['user']['acctid']." 
						AND dwid=$dwid";
                $result = db_query($sql);
                $row = db_fetch_assoc($result);
                $sql = "UPDATE ".db_prefix("dwellingkeys")." 
						SET keyowner=0 
						WHERE keyowner=".$session['user']['acctid']." 
						AND dwid=".$dwid;
                db_query($sql);
                $msg = "`2%s has returned their key to your %s in %s.";
                $mailmessage = array($msg,
                        $session['user']['name'], $row['type'],
                        $row['location']);
				require_once("lib/systemmail.php");
                systemmail($row['dwidowner'],
                        array("`2Key returned!`2"),
                        $mailmessage);
                output("The key has been returned.");
                blocknav("runmodule.php?module=dwellingseditor&op=enter&dwid=$dwid");
            }else{
				output("Are you sure you want to give back your key to this dwelling?");
				addnav("Yes,","runmodule.php?module=dwellingseditor&op=keys&subop=giveback&confirm=1&dwid=$dwid");
            }
        }
        if($subop == "givekey"){
            output("`2Who do you want to give the key to?`n`n");
            $submit = translate_inline("Submit");
            rawoutput("<form action='runmodule.php?module=dwellingseditor&op=keys&subop=givekey2&keyid=$keyid&dwid=$dwid' method='POST'>");
            addnav("","runmodule.php?module=dwellingseditor&op=keys&subop=givekey2&keyid=$keyid&dwid=$dwid");
            rawoutput("<input name='name' id='name'> <input type='submit' class='button' value='$submit'>");
            rawoutput("</form>");
            rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>");
        }
        if($subop == "givekey2"){
            $string = "%";
            for ($x = 0; $x < strlen(httppost('name')); $x++){
                $string .= substr(httppost('name'),$x,1)."%";
            }
            $sql = "SELECT login,name,level,acctid FROM ".db_prefix("accounts")."
					WHERE (login LIKE '".addslashes($string)."' OR name LIKE '".addslashes($string)."') 
					AND acctid != ".$session['user']['acctid']." 
					AND locked=0 
					ORDER BY level,login";
            $result = db_query($sql);
            if (db_num_rows($result)<=0){
                output("`2There is no one with that name.");
            }elseif(db_num_rows($result)>100){
                output("Found over 100 players that fit that description. Please try to be more exact.`n`n");
                rawoutput("<form action='runmodule.php?module=dwellingseditor&op=keys&subop=givekey2&keyid=$keyid&dwid=$dwid' method='POST'>");
                addnav("","runmodule.php?module=dwellingseditor&op=keys&subop=givekey2&keyid=$keyid&dwid=$dwid");
                output("`2Who do you want to give the key to?`n`n");
                rawoutput("<input name='name' id='name'> <input type='submit' class='button' value='$submit'>");
                rawoutput("</form>");
                rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>");
            }else{
                output("Which player did you mean?`n`n");
                rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
                rawoutput("<tr class='trhead'><td>Name</td><td>Level</td></tr>");
                for ($i = 0; $i < db_num_rows($result); $i++){
                    $row = db_fetch_assoc($result);
                    rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
                    rawoutput("<a href='runmodule.php?module=dwellingseditor&op=keys&subop=givekey3&keyid=$keyid&dwid=$dwid&keyowner=".HTMLEntities($row['acctid'])."'>");
                    output_notl($row['name']);
                    rawoutput("</a></td><td>");
                    output_notl($row['level']);
                    rawoutput("</td></tr>");
                    addnav("","runmodule.php?module=dwellingseditor&op=keys&subop=givekey3&keyid=$keyid&dwid=$dwid&keyowner=".HTMLEntities($row['acctid']));
                }
                rawoutput("</table>");
            }
        }
        if($subop=="givekey3"){
            $keyowner = httpget('keyowner');
			if($keyid == ""){
                $sql = "SELECT keyid FROM ".db_prefix("dwellingkeys")." WHERE keyowner = 0 AND dwid = $dwid LIMIT 1";
                $result = db_query($sql);
                $row = db_fetch_assoc($result);
                $keyid = $row['keyid'];
            } 
            if($keyid == ""){
				$sql = "INSERT INTO ".db_prefix("dwellingkeys")." (dwid,dwidowner,keyowner) VALUES ($dwid,".$session['user']['acctid'].",$keyowner)";
            }else{
				$sql = "UPDATE ".db_prefix("dwellingkeys")." SET keyowner = $keyowner WHERE keyid = $keyid";
            }
            db_query($sql);
            $sql2 = "SELECT location,type FROM ".db_prefix("dwellings")." WHERE dwid = ". $dwid;
            $row2 = db_fetch_assoc(db_query($sql2));
            $cname = translate_inline(get_module_setting("dwname",$row2['type']));
            output("The key will be delivered to them right away!");
			require_once("lib/systemmail.php");
            systemmail($keyowner,"`^You Have a key!`0",array("`&%s`& has given you a key to their %s`& in %s",$session['user']['name'],$cname,$row2['location']));
        }
        if($subop == "takekey"){
            $sql = "UPDATE ".db_prefix("dwellingkeys")." SET keyowner = 0 WHERE keyid = $keyid AND dwid = " . $dwid;
            db_query($sql);
            output("Key successfully taken back.");
			require_once("lib/systemmail.php");
            systemmail($keyowner, "`^Key Taken!`0",array("%s has taken back one of their keys!",$session['user']['name']));
        }
?>