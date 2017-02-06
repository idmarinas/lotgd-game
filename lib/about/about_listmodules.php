<?php
addnav("About LoGD");
addnav("About LoGD","about.php");
addnav("Game Setup Info","about.php?op=setup");
addnav("License Info", "about.php?op=license");
$sql = "SELECT * from " . DB::prefix("modules") . " WHERE active=1 ORDER BY category,formalname";
$result = DB::query($sql);
$mname = translate_inline("Module Name");
$mver = translate_inline("Version");
$mauth = translate_inline("Module Author");
$mdown = translate_inline("Download Location");
rawoutput("<table class='ui very compact striped table'>",true);
if (DB::num_rows($result) == 0) {
	rawoutput("<tr><td colspan='4' class='center aligned'>");
	output("`i-- No modules installed --`i");
	rawoutput("</td></tr>");
}
$cat = "";
$i=0;
while ($row = DB::fetch_assoc($result)) {
	$i++;
	if ($cat != $row['category']) {
		rawoutput("<thead><tr><th colspan='4'>");
		output($row['category']);
		rawoutput(":</th></tr>");
		$cat = $row['category'];
		rawoutput("<tr><th>$mname</th><th>$mver</th><th>$mauth</th><th>$mdown</th></tr></thead>",true);
	}

	rawoutput("<tr>");
	rawoutput("<td>");
	output_notl("`&%s`0", $row['formalname']);
	rawoutput("<td>",true);
	output_notl("`^%s`0", $row['version']);
	rawoutput("</td><td>");
	output_notl("`^%s`0", $row['moduleauthor'], true);
	rawoutput("</td><td nowrap>");
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