<?php
$sql = "INSERT INTO " . DB::prefix("bans") . " (banner,";
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
	$result=DB::query($sql);
	output("%s ban rows entered.`n`n", DB::affected_rows($result));
	output_notl("%s", DB::error());//Eliminado el LINK, ya no es necesario
	debuglog("entered a ban: " .  ($type=="ip"?  "IP: ".httppost("ip"): "ID: ".httppost("id")) . " Ends after: $duration  Reason: \"" .  httppost("reason")."\"");
	/* log out affected players */
	$sql = "SELECT acctid FROM ".DB::prefix('accounts')." WHERE $key='$key_value'";
	$result=DB::query($sql);
	$acctids=array();
	while ($row=DB::fetch_assoc($result)) {
		$acctids[]=$row['acctid'];
	}
	if ($acctids!=array()) {
		$sql= " UPDATE ".DB::prefix('accounts')." SET loggedin=0 WHERE acctid IN (".implode(",",$acctids).")";
		$result=DB::query($sql);
		if ($result) {
			output("`\$%s people have been logged out!`n`n`0",DB::affected_rows($result));
		} else {
			output("`\$Nobody was logged out. Acctids (%s) did not return rows!`n`n`0",implode(",",$acctids));
		}
	} else output("`\$No account-ids found for that IP/ID!`n`n`0");

}
?>
