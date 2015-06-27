<?php
addnav("About LoGD");
addnav("About LoGD","about.php");
addnav("Game Setup Info","about.php?op=setup");
addnav("License Info", "about.php?op=license");
$sql = "SELECT * from " . db_prefix("modules") . " WHERE active=1 ORDER BY category,formalname";
$result = db_query($sql);
$mname = translate_inline("Module Name");
$mver = translate_inline("Version");
$mauth = translate_inline("Module Author");
$mdown = translate_inline("Download Location");
rawoutput("<table border='0' cellpadding='2' cellspacing='1' bgcolor='#999999'>",true);
rawoutput("<tr class='trhead'><td>$mname</td><td>$mver</td><td>$mauth</td><td>$mdown</td></tr>",true);
if (db_num_rows($result) == 0) {
	rawoutput("<tr class='trlight'><td colspan='4' align='center'>");
	output("`i-- No modules installed --`i");
	rawoutput("</td></tr>");
}
$cat = "";
$i=0;
while ($row = db_fetch_assoc($result)) {
	$i++;
	if ($cat != $row['category']) {
		rawoutput("<tr class='trhead'><td colspan='4' align='left'>");
		output($row['category']);
		rawoutput(":</td></tr>");
		$cat = $row['category'];
	}

	rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
	rawoutput("<td valign='top'>");
	output_notl("`&%s`0", $row['formalname']);
	rawoutput("<td valign='top'>",true);
	output_notl("`^%s`0", $row['version']);
	rawoutput("</td><td valign='top'>");
	output_notl("`^%s`0", $row['moduleauthor'], true);
	rawoutput("</td><td nowrap valign='top'>");
	if ($row['download'] == "core_module") {
		rawoutput("<a href='http://dragonprime.net/index.php?op=download;id=8' target='_blank'>");
		output("Core Distribution");
		rawoutput("</a>");
	} elseif ($row['download']) {
		// This will take care of download strings such as: not publically released or contact admin
		if (strpos($row['download'],"http://") === FALSE){
			output("`\$Contact Admin for Release");
		}else{
			rawoutput("<a href='{$row['download']}' target='_blank'>");
			output("Download");
			rawoutput("</a>");
		}
	} else {
		output("`\$Not publically released.`0");
	}
	rawoutput("</td>");
	rawoutput("</tr>");
}
rawoutput("</table>");
?>