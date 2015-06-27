<?php
$sql = "INSERT INTO " . db_prefix("bans") . " (banner,";
$type = httppost("type");
if ($type=="ip"){
	$sql.="ipfilter";
	$key = "lastip";
	$key_value = httppost('ip');
}else{
	$sql.="uniqueid";
	$key = "uniqueid";
	$key_value = httppost('id');
}
$sql.=",banexpire,banreason) VALUES ('" . addslashes($session['user']['name']) . "',";
if ($type=="ip"){
	$sql.="\"".httppost("ip")."\"";
}else{
	$sql.="\"".httppost("id")."\"";
}
$duration = (int)httppost("duration");
if ($duration == 0) $duration="0000-00-00";
else $duration = date("Y-m-d", strtotime("+$duration days"));
	$sql.=",\"$duration\",";
$sql.="\"".httppost("reason")."\")";
if ($type=="ip"){
	if (substr($_SERVER['REMOTE_ADDR'],0,strlen(httppost("ip"))) ==
			httppost("ip")){
		$sql = "";
		output("You don't really want to ban yourself now do you??");
		output("That's your own IP address!");
	}
}else{
	if ($_COOKIE['lgi']==httppost("id")){
		$sql = "";
		output("You don't really want to ban yourself now do you??");
		output("That's your own ID!");
	}
}
if ($sql!=""){
	$result=db_query($sql);
	output("%s ban rows entered.`n`n", db_affected_rows($result));
	output_notl("%s", db_error(LINK));
	debuglog("entered a ban: " .  ($type=="ip"?  "IP: ".httppost("ip"): "ID: ".httppost("id")) . " Ends after: $duration  Reason: \"" .  httppost("reason")."\"");
	/* log out affected players */
	$sql = "SELECT acctid FROM ".db_prefix('accounts')." WHERE $key='$key_value'";
	$result=db_query($sql);
	$acctids=array();
	while ($row=db_fetch_assoc($result)) {
		$acctids[]=$row['acctid'];
	}
	if ($acctids!=array()) {
		$sql= " UPDATE ".db_prefix('accounts')." SET loggedin=0 WHERE acctid IN (".implode(",",$acctids).")";	
		$result=db_query($sql);
		if ($result) {
			output("`\$%s people have been logged out!`n`n`0",db_affected_rows($result));
		} else {
			output("`\$Nobody was logged out. Acctids (%s) did not return rows!`n`n`0",implode(",",$acctids));
		}
	} else output("`\$No account-ids found for that IP/ID!`n`n`0");

}
?>
