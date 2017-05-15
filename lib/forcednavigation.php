<?php
// translator ready
// addnews ready
// mail ready

$baseaccount = array();
function do_forced_nav($anonymous,$overrideforced){
	global $baseaccount, $session,$REQUEST_URI;
	rawoutput("<!--\nAllowAnonymous: ".($anonymous?"True":"False")."\nOverride Forced Nav: ".($overrideforced?"True":"False")."\n-->");
	if (isset($session['loggedin']) && $session['loggedin']){
		$sql = "SELECT *  FROM ".DB::prefix("accounts")." WHERE acctid = '".$session['user']['acctid']."'";
		$result = DB::query($sql);
		if (DB::num_rows($result)==1){
			$session['user']=DB::fetch_assoc($result);
			$baseaccount = $session['user'];
			$session['bufflist']=unserialize($session['user']['bufflist']);
			if (!is_array($session['bufflist'])) $session['bufflist']=array();
			$session['user']['dragonpoints']=unserialize($session['user']['dragonpoints']);
			$session['user']['prefs']=unserialize($session['user']['prefs']);
			if (!is_array($session['user']['dragonpoints'])) $session['user']['dragonpoints']=array();


			//get allowednavs
			/*
			accounts_everypage table includes:
				acctid (primary key, unique)
				allowednavs
				laston
				gentime
				gentimecount
				gensize
			*/
			$sql = "SELECT allowednavs,laston,gentime,gentimecount,gensize FROM ".DB::prefix("accounts_everypage")." WHERE acctid = '".$session['user']['acctid']."'";
			$result = DB::query($sql);
			if (DB::num_rows($result)==1){
				//debug("Getting fresh info from accounts_everypage");
				$row = DB::fetch_assoc($result);
				$session['user']['allowednavs'] = $row['allowednavs'];
				$session['user']['laston'] = $row['laston'];
				$session['user']['gentime'] = $row['gentime'];
				$session['user']['gentimecount'] = $row['gentimecount'];
				$session['user']['gensize'] = $row['gensize'];
			} else {
				$sql = "INSERT INTO ".DB::prefix("accounts_everypage")." (acctid,allowednavs,laston,gentime,gentimecount,gensize) VALUES ('".$session['user']['acctid']."','".$session['user']['allowednavs']."','".$session['user']['laston']."','".$session['user']['gentime']."','".$session['user']['gentimecount']."','".$session['user']['gensize']."')";
				DB::query($sql);
			}

			if (is_array(unserialize($session['user']['allowednavs']))){
				$session['allowednavs']=unserialize($session['user']['allowednavs']);
			}else{
				$session['allowednavs']=array($session['user']['allowednavs']);
			}
			if (!$session['user']['loggedin'] || ( (date("U") - strtotime($session['user']['laston'])) > getsetting("LOGINTIMEOUT",900)) ){
				$session=array();
				redirect("index.php?op=timeout","Account not logged in but session thinks they are.");
			}
		}else{
			$session=array();
			$session['message']=translate_inline("`4Error, your login was incorrect`0","login");
			redirect("index.php","Account Disappeared!");
		}
		DB::free_result($result);
		if (isset($session['allowednavs'][$REQUEST_URI]) && $session['allowednavs'][$REQUEST_URI] && $overrideforced!==true){
			$session['allowednavs']=array();
		}else{
			if ($overrideforced!==true){
				redirect("badnav.php","Navigation not allowed to $REQUEST_URI");
			}
		}
	}else{
		if (!$anonymous){
			redirect("index.php?op=timeout","Not logged in: $REQUEST_URI");
		}
	}
}
?>
