<?php
$sql = "SELECT count(*) AS count FROM " . db_prefix("untranslated");
$count = db_fetch_assoc(db_query($sql));
if ($count['count'] > 0) {
	$sql = "SELECT * FROM " . db_prefix("untranslated") . " WHERE language = '" . $languageschema . "' ORDER BY rand(".e_rand().") LIMIT 1";
	$result = db_query($sql);
	if (db_num_rows($result) == 1) {
		$row = db_fetch_assoc($result);
		$row['intext'] = stripslashes($row['intext']);
		$submit = translate_inline("Save Translation");
		$skip = translate_inline("Skip Translation");
		rawoutput("<form action='runmodule.php?module=translationwizard&op=randomsave' method='post'>");
		output("`^`cThere are `&%s`^ untranslated texts in the database.`c`n`n", $count['count']);
		rawoutput("<table width='80%'>");
		rawoutput("<tr><td width='30%'>");
		output("Target Language: %s", $row['language']);
		rawoutput("</td><td></td></tr>");
		rawoutput("<tr><td width='30%'>");
		output("Namespace: %s", $row['namespace']);
		rawoutput("</td><td></td></tr>");
		rawoutput("<tr><td width='30%'><textarea cols='35' rows='4' name='intext' readonly>".$row['intext']."</textarea></td>");
		rawoutput("<td width='30%'><textarea cols='25' rows='4' name='outtext'></textarea></td></tr></table>");
		rawoutput("<input type='hidden' name='id' value='{$row['id']}'>");
		rawoutput("<input type='hidden' name='language' value='{$row['language']}'>");
		rawoutput("<input type='hidden' name='namespace' value='{$row['namespace']}'>");
		rawoutput("<input type='submit' value='$submit' class='button'>");
		rawoutput("</form>");
		rawoutput("<form action='runmodule.php?module=translationwizard' method='post'>");
		rawoutput("<input type='submit' value='$skip' class='button'>");
		rawoutput("</form>");
		addnav("", "runmodule.php?module=translationwizard&op=randomsave");
		addnav("", "runmodule.php?module=translationwizard");
	} else {
		output("There are `&%s`0 untranslated texts in the database, but none for your selected language.", $count['count']);
		output("Please change your language to translate these texts.");
	}
} else
	{
	output("There are no untranslated texts in the database!");
	output("Congratulations!!!");
	} // end if
?>