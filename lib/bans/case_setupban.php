<?php
$sql = "SELECT name,lastip,uniqueid FROM " . DB::prefix("accounts") . " WHERE acctid=\"$userid\"";
$result = DB::query($sql);
$row = DB::fetch_assoc($result);
if ($row['name']!="")
	output("Setting up ban information based on `\$%s`0", $row['name']);
rawoutput("<form action='bans.php?op=saveban' method='POST'>");
output("Set up a new ban by IP or by ID.`n");
output("`qWe recommended ID as this bans all users who are sitting on THAT machine with THAT browser. A cookie can be deleted, but the char stays locked anyway, regardless of that.`n`n");
output("If you ban via IP and if you have several different users behind a NAT(sharing IPs, many big providers do this currently), you will ban much more users. However, you can ban multichars from different PCs too.`n`0");
rawoutput("<input type='radio' value='ip' id='ipradio' name='type'>");
output("IP: ");
rawoutput("<input name='ip' id='ip' value=\"".HTMLEntities($row['lastip'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">");
output_notl("`n");
rawoutput("<input type='radio' value='id' name='type' checked>");
output("ID: ");
rawoutput("<input name='id' value=\"".HTMLEntities($row['uniqueid'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">");
output("`nDuration: ");
rawoutput("<input name='duration' id='duration' size='3' value='14'>");
output("Days (0 for permanent)`n");
$reason = httpget("reason");
if ($reason == "")
	$reason=translate_inline("Don't mess with me.");
output("Reason for the ban: ");
rawoutput("<input name='reason' size=50 value=\"$reason\">");
output_notl("`n");
$pban = translate_inline("Post ban");
$conf = translate_inline("Are you sure you wish to issue a permanent ban?");
rawoutput("<input type='submit' class='button' value='$pban' onClick='if (document.getElementById(\"duration\").value==0) {return confirm(\"$conf\");} else {return true;}'>");
rawoutput("</form>");
output("For an IP ban, enter the beginning part of the IP you wish to ban if you wish to ban a range, or simply a full IP to ban a single IP`n`n");
addnav("","bans.php?op=saveban");
if ($row['name']!=""){
	$id = $row['uniqueid'];
	$ip = $row['lastip'];
	$name = $row['name'];
	output("`0To help locate similar users to `@%s`0, here are some other users who are close:`n", $name);
	output("`bSame ID (%s):`b`n", $id);
	$sql = "SELECT name, lastip, uniqueid, laston, gentimecount FROM " . DB::prefix("accounts") . " WHERE uniqueid='".addslashes($id)."' ORDER BY lastip";
	$result = DB::query($sql);
	while ($row = DB::fetch_assoc($result)){
		output("`0* (%s) `%%s`0 - %s hits, last: %s`n", $row['lastip'],
				$row['name'], $row['gentimecount'],
				reltime(strtotime($row['laston'])));
	}
	output_notl("`n");
		$oip = "";
	$dots = 0;
	output("`bSimilar IP's`b`n");
	for ($x=strlen($ip); $x>0; $x--){
		if ($dots>1) break;
		$thisip = substr($ip,0,$x);
		$sql = "SELECT name, lastip, uniqueid, laston, gentimecount FROM " . DB::prefix("accounts") . " WHERE lastip LIKE '$thisip%' AND NOT (lastip LIKE '$oip') ORDER BY uniqueid";
		//output("$sql`n");
		$result = DB::query($sql);
		if (DB::num_rows($result)>0){
			output("IP Filter: %s ", $thisip);
			rawoutput("<a href='#' onClick=\"document.getElementById('ip').value='$thisip'; document.getElementById('ipradio').checked = true; return false\">");
			output("Use this filter");
			rawoutput("</a>");
			output_notl("`n");
			while ($row=DB::fetch_assoc($result)){
				output("&nbsp;&nbsp;",true);
				output("(%s) [%s] `%%s`0 - %s hits, last: %s`n",
						$row['lastip'], $row['uniqueid'], $row['name'],
						$row['gentimecount'],
						reltime(strtotime($row['laston'])));
			}
			output_notl("`n");
		}
		if (substr($ip,$x-1,1)==".") {
			$x--;
			$dots++;
		}
		$oip = $thisip."%";
	}
}
?>
