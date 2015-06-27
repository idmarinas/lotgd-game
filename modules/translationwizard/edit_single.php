<?php
if ($from=="") $from="module=translationwizard&op=list";
rawoutput("<form action='runmodule.php?".$from."&mode=save&from=".rawurlencode($from)."' method='post'>");
addnav("", "runmodule.php?".$from."&mode=save&from=".rawurlencode($from));
$sql = "SELECT namespace,count(*) AS c FROM " . db_prefix("untranslated") . " WHERE language='".$languageschema."' GROUP BY namespace ORDER BY namespace ASC";
$result = db_query($sql);
if ($op=="list")
{
output("Known Namespaces:");
rawoutput("<select name='ns' onChange='this.form.submit()'>");
while ($row = db_fetch_assoc($result))
	{
		if ($namespace=="") $namespace=$row['namespace']; //if this is the first execution, fetch the first entries
		rawoutput("<option value=\"".htmlentities($row['namespace'],ENT_COMPAT,$coding)."\"".((htmlentities($row['namespace'],ENT_COMPAT,$coding) == $namespace) ? "selected" : "").">".htmlentities($row['namespace'],ENT_COMPAT,$coding)." ({$row['c']})</option>");
	}
}
	rawoutput("</select>");
	//rawoutput("<input type='submit' class='button' value='". translate_inline("Show") ."'>"); //no longer necessary
	rawoutput("<br>");
	rawoutput(translate_inline("Text:"). "<br>");
	rawoutput("<textarea name='intext' class='input' cols='60' rows='5' readonly>".htmlentities(stripslashes(httpget('intext')),ENT_COMPAT,$coding)."</textarea><br/>");
	rawoutput(translate_inline("Translation:"). "<br>");
	rawoutput("<textarea name='outtext' class='input' cols='60' rows='5'>".htmlentities(stripslashes(httpget('outtext')),ENT_COMPAT,$coding)."</textarea><br/>");
	rawoutput("<input type='submit' value='". translate_inline("Save") ."' class='button'>");
	rawoutput("</form>");
?>