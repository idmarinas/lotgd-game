<?php
	require_once("modules/dwellings/lib.php");
	$oldacctid = $args['acctid'];
	$oldowner = $args['name'];
	$sql = "SELECT dwid FROM ".db_prefix("dwellings")." WHERE ownerid='$oldacctid'";
	$res = db_query($sql);
	$row = db_fetch_assoc($res);
	$dwid = $row['dwid'];
	dwellings_wipekeysforowner($oldacctid);
	
	if(get_module_setting("delete","dwellings")==0){//delete it
		dwellings_deleteforowner($oldacctid,$dwid);
	}elseif(get_module_setting("delete","dwellings")==1){//abandon it
		dwellings_abandonforowner($oldacctid,$dwid);
	}elseif(get_module_setting("delete","dwellings")==2){//give to first keyholder (with alternatives if null)
		$sql = "SELECT dwid,location,type,name 
				FROM ".db_prefix("dwellings")." 
				WHERE ownerid=$oldacctid";
		$result = db_query($sql) or die(db_error(LINK));	
		while ($row = db_fetch_assoc($result)) { 
			$sql2 = "SELECT keyowner 
					FROM ".db_prefix("dwellingkeys")." 
					WHERE dwid='{$row['dwid']} 
					AND dwidowner=$oldacctid 
					AND keyowner != $oldacctid 
					ORDER BY keyid DESC LIMIT 1";
			$result2 = db_query($sql2) or die(db_error(LINK));
			$row2 = db_fetch_assoc($result2);
			if($row2['keyowner']==0 && !isset($row2['keyowner'])){
				if(get_module_setting("delete2")==0) //delete it as a second option
					dwellings_deleteforowner($oldacctid,$row['dwid']);
				else //abandon as a second option
					dwellings_abandonforowner($oldacctid,$row['dwid']);
			}else{//give it to first keyholder
				$msg = "`2%s has left this realm, and you are listed as the beneficiary to their %s in %s.  You are now the owner of %s.";
				$mailmessage = array($msg, $oldowner, $row['type'], $row['location'], $row['name']);
				require_once("lib/systemmail.php");
				systemmail($row2['keyowner'], array("`2You own a %s`2", $row['type']), $mailmessage);
				$sql = "UPDATE ".db_prefix("dwellings")." SET ownerid = ".$row2['keyowner']." WHERE dwid=".$row['dwid'];
				db_query($sql);
			}
		}
	}elseif(get_module_setting("delete","dwellings")==3){//give to partner (with alternatives if null)
		$accounts = db_prefix("accounts");
		$dwellings = db_prefix("dwellings");
		$sql = "SELECT $accounts.marriedto AS marriedto,
				$dwellings.dwid AS dwid, 
				$dwellings.type AS type, 
				$dwellings.location AS location,
				$dwellings.name AS name
				FROM $accounts 
				INNER JOIN $dwellings 
				ON $dwellings.ownerid = $accounts.acctid 
				WHERE acctid=$oldacctid";
		$result = db_query($sql) or die(db_error(LINK));	 
		while ($row = db_fetch_assoc($result)){
			if ($row['marriedto']!=0 && $row['marriedto']!=INT_MAX) {//give to partner
				$msg = "`2%s has left this realm, and you are listed as the beneficiary to their %s in %s.  You are now the owner of %s.";
				$mailmessage = array($msg, $oldowner, $row['type'],	$row['location'], $row['name']);
				require_once("lib/systemmail.php");
				systemmail($row['marriedto'], array("`2You own a %s`2", $row['type']), $mailmessage);
				$sql = "UPDATE ".db_prefix("dwellings")." SET ownerid = ".$row['marriedto']." WHERE dwid=".$row['dwid']."";
				db_query($sql);
			} else{
				if(!get_module_setting("delete2")) //delete it as a second option
					dwellings_deleteforowner($oldacctid,$row['dwid']);
				else//abandon as a second option
					dwellings_abandonforowner($oldacctid,$row['dwid']);
			}
		}
	}
?>