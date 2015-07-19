<?php
//some kind of header
if (httppost("deletechecked")) {
	require("./modules/translationwizard/deletechecked.php"); //if you want to delete the checked translations, this commences the deletion process
}
if (httppost("editchecked")) {
	rawoutput("<form action='runmodule.php?module=translationwizard&op=list&ns=".rawurlencode($namespace)."' method='post'>");
	addnav("", "runmodule.php?module=translationwizard&op=list&ns=".rawurlencode($namespace));
	$sql = "SELECT namespace,count(*) AS c FROM " . db_prefix("untranslated") . " WHERE language='".$languageschema."' GROUP BY namespace ORDER BY namespace ASC";
	$result = db_query($sql);
	rawoutput("<input type='hidden' name='op' value='list'>");
	output("Known Namespaces:");
	rawoutput("<select name='ns' onChange='this.form.submit()'>");
	while ($row = db_fetch_assoc($result))
		{
		rawoutput("<option value=\"".htmlentities($row['namespace'],ENT_COMPAT,$coding)."\"".((htmlentities($row['namespace'],ENT_COMPAT,$coding) == $namespace) ? "selected" : "").">".htmlentities($row['namespace'],ENT_COMPAT,$coding)." ({$row['c']})</option>");
		}
	rawoutput("</select>");
	//rawoutput("<input type='submit' class='button' value='". translate_inline("Show") ."'>"); //no longer necessary
	require("./modules/translationwizard/editchecked.php"); //if you want to edit some translations at a time
	addnav("R?Restart Translator", "runmodule.php?module=translationwizard");
	addnav("N?Translate by Namespace", "runmodule.php?module=translationwizard&op=list");
	require_once("lib/superusernav.php");
	tlschema();
	superusernav();
	page_footer();  //let's stop here
}
if (httppost("multichecked")) {
	$redirectonline=1;
	require("./modules/translationwizard/multichecked.php");//if you want to copy the checked translations with intext and the entered outtext, this commences the copy process
}
if (httppost("copychecked")) {
	require("./modules/translationwizard/copychecked.php");//if you want to copy the checked translations with intext=outtext, this commences the copy process
}
	//debug("Name of the module:".$namespace);
switch ($mode)
{
case "save":		//if you want to save a single translation (called not from the checkboxes form)
	$from="module=translationwizard&op=list&ns=".$namespace;
	require("./modules/translationwizard/save_single.php");
	break; //just in case
case "edit": //for one translation via the edit button
	require("./modules/translationwizard/edit_single.php");
	break;
case "del": //to delete one via the delete button
	$intext=rawurldecode(httpget('intext'));
	$sql = "DELETE FROM " . db_prefix("untranslated") . " WHERE intext = '$intext' AND language = '$languageschema' AND namespace = '$namespace'";
	//debug($sql); break;
	db_query($sql);
	$mode=""; //reset
	redirect("runmodule.php?module=translationwizard&op=list&ns=".$namespace); //just redirecting so you go back to the previous page after the deletion
	break;
default: //if there is any other mode, i.e. "" go on and display what's necessary including checkboxes and so on, just the main list
	rawoutput("<form action='runmodule.php?module=translationwizard&op=list' name='listenauswahl' method='post'>");
	addnav("", "runmodule.php?module=translationwizard&op=list");
	$sql = "SELECT namespace,count(*) AS c FROM " . db_prefix("untranslated") . " WHERE language='".$languageschema."' GROUP BY namespace ORDER BY namespace ASC";
	$result = db_query($sql);
	rawoutput("<input type='hidden' name='op' value='list'>");
	output("Known Namespaces:");
	rawoutput("<select name='ns' onChange='this.form.submit()' >");
	while ($row = db_fetch_assoc($result))
		{
			if ($namespace=="") $namespace=$row['namespace'];
			rawoutput("<option value=\"".htmlentities($row['namespace'],ENT_COMPAT,$coding)."\"".((htmlentities($row['namespace'],ENT_COMPAT,$coding) == $namespace) ? "selected" : "").">".htmlentities($row['namespace'],ENT_COMPAT,$coding)." ({$row['c']})</option>");
		}
	rawoutput("</select>");
	rawoutput("<a href='runmodule.php?module=translationwizard&op=pull&mode=pull&ns=". rawurlencode($namespace)."'>". translate_inline("Pull")."</a>");
	addnav("", "runmodule.php?module=translationwizard&op=pull&mode=pull&ns=". rawurlencode($namespace));
	//rawoutput("<input type='submit' class='button' name='dummy' value='". translate_inline("Show") ."'>"); //no longer necessary
	output_notl("`n");
	rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
	rawoutput("<tr class='trhead'><td>". translate_inline("Ops") ."</td><td>". translate_inline("Text") ."</td><td>".translate_inline("Actions")."</td></tr>");
	$sql = "SELECT * FROM " . db_prefix("untranslated") . " WHERE language='".$languageschema."' AND namespace='".$namespace."'";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		$i = 0;
		while ($row = db_fetch_assoc($result))
		{
			$i++;
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
			rawoutput("<input type='checkbox' name='transtext[]' value='".rawurlencode($row['intext'])."' >");
			addnav("", "runmodule.php?module=translationwizard&op=list&mode=edit&ns=". rawurlencode($row['namespace']));
			rawoutput("</td><td>");
			rawoutput(htmlentities($row['intext'],ENT_COMPAT,$coding));
			rawoutput("</td><td>");
			rawoutput("<a href='runmodule.php?module=translationwizard&op=list&mode=edit&ns=". rawurlencode($row['namespace']) ."&intext=". rawurlencode($row['intext']) ."'>". translate_inline("Edit") ."</a>");
			addnav("", "runmodule.php?module=translationwizard&op=list&mode=edit&ns=". rawurlencode($row['namespace']) ."&intext=". rawurlencode($row['intext']));
			rawoutput("<a href='runmodule.php?module=translationwizard&op=list&mode=del&ns=". rawurlencode($row['namespace']) ."&intext=". rawurlencode($row['intext']) ."'>". translate_inline("Delete") ."</a>");
			addnav("", "runmodule.php?module=translationwizard&op=list&mode=del&ns=". rawurlencode($row['namespace']) ."&intext=". rawurlencode($row['intext']));
			rawoutput("</td></tr>");
		}
	}else
		{
			rawoutput("<tr><td colspan='2'>". translate_inline("No rows found") ."</td></tr>");
			if ($namespace<>"")
				{
				$namespace="";
				redirect("runmodule.php?module=translationwizard&op=list&ns=".$namespace); //safety if the rows are empty but the namespace showed up
				}
		}
	rawoutput("</table>");
	//some check/uncheck all
	$all=translate_inline("Check all");
	$none=translate_inline("Uncheck all");
	rawoutput("<script type='text/javascript' language='JavaScript'>
				<!-- Begin
				var checkflag = 'false';
				cb = document.forms['listenauswahl'].elements['transtext[]'];
				function check() {
				if (checkflag == 'false') {
				for (i = 0; i < cb.length; i++) {
				cb[i].checked=true;}
				checkflag = 'true';
				return ' $none '; }
				else {
				for (i = 0; i < cb.length; i++) {
				cb[i].checked=false; }
				checkflag = 'false';
				return ' $all '; }
				}
					//  End -->
				</script>");
	//end
	break;
}
//add some buttons if necessary
output_notl("`n`n");
if (!$mode=="save" && $namespace<>"")
	{
	if (db_num_rows($result)>1) rawoutput("<input type='button' onClick='this.value=check()' name='allcheck' value='". $all ."' class='button'>");
	output_notl("`n`n");
	rawoutput("<input type='submit' name='copychecked' value='". translate_inline("Copy checked to translation table") ."' class='button'>");
	rawoutput("<input type='submit' name='editchecked' value='". translate_inline("Edit selected") ."' class='button'>");
	rawoutput("<input type='submit' name='deletechecked' value='". translate_inline("Delete selected") ."' class='button'>");
	}
rawoutput("</form>");
?>