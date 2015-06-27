<?php
class ServerFunctions {
	function isTheServerFull() {
		if (abs(getsetting("OnlineCountLast",0) - strtotime("now")) > 60){
			$sql="SELECT count(acctid) as counter FROM " . db_prefix("accounts") . " WHERE locked=0 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."'";
			$result = db_query($sql);
			$onlinecount = db_fetch_assoc($result);
			$onlinecount = $onlinecount['counter'];
			savesetting("OnlineCount",$onlinecount);
			savesetting("OnlineCountLast",strtotime("now"));
		}else{
			$onlinecount = getsetting("OnlineCount",0);
		}
		if ($onlinecount>=getsetting("maxonline",0) && getsetting("maxonline",0)!=0) return true;
		return false;
	}

	function resetAllDragonkillPoints($acctid=false) {
		if ($acctid===false) {
			$where="";
		} elseif (is_array($acctid)) {
			$where="WHERE acctid IN (".implode(",",$acctid).")";
		} else  {
			$where="WHERE acctid=$acctid";
		}
			$sql="SELECT acctid,dragonpoints FROM ".db_prefix('accounts')." $where";
		$result=db_query($sql);
		//this is ugly, but fortunately only needed out of the ordinary
		while($row=db_fetch_assoc($result)) {
			$dkpoints=$row['dragonpoints'];
			if ($dkpoints=="") continue; // no action
			$dkpoints=unserialize(stripslashes($dkpoints));
			$distribution=array_count_values($dkpoints);
			$sets=array();
			foreach ($distribution as $key=>$val) {
				switch ($key) {
					case "str":
						$recalc=((int)$val);
						$sets[]="strength=strength-$recalc";
						break;
					case "con":
						$recalc=((int)$val);
						$sets[]="constitution=constitution-$recalc";
						break;
					case "int":
						$recalc=((int)$val);
						$sets[]="intelligence=intelligence-$recalc";
						break;
					case "wis":
						$recalc=((int)$val);
						$sets[]="wisdom=wisdom-$recalc";
						break;
					case "dex":
						$recalc=((int)$val);
						$sets[]="dexterity=dexterity-$recalc";
						break;
					case "hp":
						$recalc=((int)$val)*5;
						$sets[]="maxhitpoints=maxhitpoints-$recalc, hitpoints=hitpoints-$recalc";
						break;
					case "at":
						$recalc=((int)$val);
						$sets[]="attack=attack-$recalc";
						break;
					case "de":
						$recalc=((int)$val);
						$sets[]="defense=defense-$recalc";
						break;
				}
			}
			if (count($sets)>0) {
				$resetactions=",".implode(",",$sets);
			} else $resetactions="";

			$sql="UPDATE ".db_prefix('accounts')." SET dragonpoints=''$resetactions WHERE acctid=".$row['acctid'];
			db_query($sql);
			//adding a hook, nasty, but you don't call this too often
			modulehook("dragonpointreset",array($row));
		}

	}

}
