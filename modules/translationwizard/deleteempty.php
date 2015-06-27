<?php	
if (httppost("listing")) $mode="listing";
if (httppost("deleteall")) $mode="delete";
switch ($mode)
	{
	case "del": //single delete action
		$intext=rawurldecode(httpget('intext'));
		$language=httpget('deletelanguage');
		$sql = "DELETE FROM " . db_prefix("untranslated") . " WHERE intext = '$intext' AND namespace='' AND language='$language'";
		if ($intext<>'') db_query($sql);
		redirect("runmodule.php?module=translationwizard&op=deleteempty&mode=listing"); //just redirecting so you go back to the previous page after the deletion
	break;
	case "delete": //autodelete more
	$sql= "DELETE FROM  ".db_prefix("untranslated")." WHERE namespace=''";
	$result = db_query($sql);
	output("`bOperation commenced, %s rows found and deleted`b`n`n",db_affected_rows($result));

	break;
	
	case "listing":  //if the user hits the button to work on a list, one by one
		$sql= "SELECT intext, language FROM  ".db_prefix("untranslated")." WHERE namespace='' GROUP  BY BINARY intext, language";
		$result = db_query($sql);
		output("`n`n %s rows have been found with no namespace in your untranslated table.`n`n",db_num_rows($result));
		$i = 0;
		output("`n`nFollowing rows have no namespace:");
		rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
		rawoutput("<tr class='trhead'><td>". translate_inline("Language")."</td><td>".translate_inline("Namespace")."</td><td>".translate_inline("Original") ."</td><td>".translate_inline("Actions")."</td></tr>");						
		while ($row = db_fetch_assoc($result))
			{
			$i++;
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
			rawoutput(htmlentities($row['language']),ENT_COMPAT,$coding);
			rawoutput("</td><td>");
			rawoutput(htmlentities($row['namespace']),ENT_COMPAT,$coding);
			rawoutput("</td><td>");
			rawoutput(htmlentities($row['intext']),ENT_COMPAT,$coding);
			rawoutput("</td><td>");
			rawoutput("<a href='runmodule.php?module=translationwizard&op=deleteempty&mode=del&intext=". rawurlencode($row['intext'])."&deletelanguage=".$row['language']."'>". translate_inline("Delete") ."</a>");
			addnav("", "runmodule.php?module=translationwizard&op=deleteempty&mode=del&intext=". rawurlencode($row['intext'])."&deletelanguage=".$row['language']);					
			rawoutput("</td></tr>");

			if ($i>$page) break;
			}
		rawoutput("</table>");
	break;
	
	
	default:  //if the user hits the button just to check for duplicates
		$sql= "SELECT intext, language FROM  ".db_prefix("untranslated")." WHERE namespace='' GROUP  BY BINARY intext, language";
		$result = db_query($sql);
		rawoutput("<form action='runmodule.php?module=translationwizard&op=deleteempty&mode=delete' method='post'>");
		addnav("", "runmodule.php?module=translationwizard&op=deleteempty&mode=delete");
		rawoutput("<input type='hidden' name='op' value='check'>");
		output("`n`n %s rows have been found with no namespace in your untranslated table.`n`n",db_num_rows($result));
		if (db_num_rows($result)==0) //table is fine, no redundant rows
			{
			output("Congratulations! Your untranslated table does not have any rows with an empty namespace!");
			rawoutput("</form");
			break;
			}
		output("What do you want to do?`n`n`n`n");
		rawoutput("<input type='submit' name='deleteall' value='". translate_inline("Delete multiple automatically") ."' class='button'>");
		rawoutput("<input type='submit' name='listing' value='". translate_inline("Delete manually") ."' class='button'></form>");
		output("`b`i`$ Attention, no additional confirmation`i`b`0");
	break;
		}
?>