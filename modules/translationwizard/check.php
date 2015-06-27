<?php
if (httppost("listing")) $mode="listing";
if (httppost("deleteall")) $mode="deleteall";
switch ($mode)
	{
	case "del": //single delete action
		$tid=httpget('tid');
		$sql = "DELETE FROM " . db_prefix("translations") . " WHERE tid = '$tid'";
		debug($tid);
		if ($tid<>0) db_query($sql);
		$mode=""; //reset
		redirect("runmodule.php?module=translationwizard&op=check&mode=listing"); //just redirecting so you go back to the previous page after the deletion
	break;
	case "delete": //autodelete more
		$minmax=httppost("firstlast");
		if ($minmax=="Delete first")
			{$minmax="min";}
			else
			{$minmax="max";}
		debug ($minmax);
		$sql= "SELECT count(  tid  )  AS counter, ".$minmax."(tid) as tid, intext, uri,language FROM ".db_prefix("translations")." GROUP  BY BINARY intext, uri, language HAVING counter >1;";
		$result = db_query($sql);
		output("`bOperation commenced, %s rows found, this make take some time, please wait`b`n`n",db_num_rows($result));
		if (db_num_rows($result)>0)
			{
			while ($row = db_fetch_assoc($result))    //doing this step by step, rather than one BIG query
				{
				$sql= "DELETE FROM ".db_prefix("translations")." WHERE tid=".$row['tid'].";";
				db_query($sql);
				//$res=db_fetch_assoc(db_query($sql));               //just test code if the delete would be a select to check if this routine works
				//output_notl(" ".$res['tid']);
				}
			}
		output("`b`nOperation finished, %s rows deleted`b`n`n",db_num_rows($result));
		$sql= "SELECT  count(  tid  )  AS counter, ".$minmax."(tid) as tid, intext, uri,language FROM ".db_prefix("translations")." GROUP  BY BINARY intext, uri, language HAVING counter >1;";
		$result = db_query($sql);
		output("Now there are %s rows who are still non-unique",db_num_rows($result));
		if (db_num_rows($result)>0) output("`n`n`bYou have to repeat the operation to kill the non-unique rows who are still left.`b");
	break;

	case "listing":  //if the user hits the button to work on a list, one by one
		$sql= "SELECT count(  tid  )  AS counter, min(tid) as tid, intext, uri,language FROM  ".db_prefix("translations")." GROUP  BY BINARY intext, uri, language HAVING counter >1;";
		$result = db_query($sql);
		output("`n`n %s rows have been found not to be unique within your translations table.`n`n",db_num_rows($result));
		$i = 0;
		output("`n`nFollowing rows are non-unique:");
		rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
		rawoutput("<tr class='trhead'><td>". translate_inline("Language")."</td><td>".translate_inline("Namespace")."</td><td>".translate_inline("Original") ."</td><td>".translate_inline("Translation")."</td><td>".translate_inline("Author")."</td><td>".translate_inline("Version")."</td><td>".translate_inline("Actions")."</td></tr>");
		while ($row = db_fetch_assoc($result))
			{
			$i++;
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
			$sql="SELECT * FROM ".db_prefix("translations")." WHERE intext='".addslashes($row['intext'])."' AND language='".$row['language']."' AND uri='".$row['uri']."';";
			$result2 = db_query($sql);
				while ($row2 = db_fetch_assoc($result2))
				{
				rawoutput("<td>");
				rawoutput(htmlentities($row2['language'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput(htmlentities($row2['uri'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput(htmlentities($row2['intext'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput(htmlentities($row2['outtext'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput(htmlentities($row2['author'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput(htmlentities($row2['version'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput("<a href='runmodule.php?module=translationwizard&op=check&mode=del&tid=". $row2['tid'] ."'>". translate_inline("Delete") ."</a>");
				addnav("", "runmodule.php?module=translationwizard&op=check&mode=del&tid=". $row2['tid']);
				rawoutput("</td></tr>");
				}
			if ($i>$page) break;
			}
		rawoutput("</table>");
	break;

	case "deleteall":
		$sql= "SELECT count(  tid  )  AS counter, min(tid) as tid, intext, uri,language FROM  ".db_prefix("translations")." GROUP  BY BINARY intext, uri, language HAVING counter >1;";
		$result = db_query($sql);
		rawoutput("<form action='runmodule.php?module=translationwizard&op=check&mode=delete' method='post'>");
		addnav("", "runmodule.php?module=translationwizard&op=check&mode=delete");
		rawoutput("<input type='hidden' name='op' value='check'>");
		output("`n`n %s rows have been found not to be unique within your translations table.`n`n",db_num_rows($result));
		output("`0This operation will delete one occurrence of each row. `n`n`b`$ CAUTION!`b`0`n`nDue to technical reasons, this operation can't let you select every single line.");
		output("The query would cost a lot of time and MySql might time out or the query block your game for more than just seconds.");
		output("So you have the choice to delete the first or last occurrence of a non-unique line.");
		output("(means: if there are twice the same row at position 1 und position 10 in the database, setting 'Delete first' will kill position 1, 'Delete last' will kill position 10)`n");
		output("What do you choose:");
		rawoutput("<select name='firstlast' size='2'>");
		rawoutput("<option label='min' selected>Delete first</option>");
		rawoutput("<option label='max'>Delete last</option>");
		rawoutput("</select>");
		rawoutput("<br><br><br><br>  <input type='submit' value='". translate_inline("Execute") ."' class='button'></form>");
		output("`b`i`$ Attention, no additional confirmation`i`b`0");

	break;

	default:  //if the user hits the button just to check for duplicates
		$sql= "SELECT count(  tid  )  AS counter, min(tid) as tid, intext, uri,language FROM  ".db_prefix("translations")." GROUP  BY BINARY intext, uri, language HAVING counter >1;";
		$result = db_query($sql);
		rawoutput("<form action='runmodule.php?module=translationwizard&op=check&mode=delete' method='post'>");
		addnav("", "runmodule.php?module=translationwizard&op=check&mode=delete");
		rawoutput("<input type='hidden' name='op' value='check'>");
		output("`n`n %s rows have been found not to be unique within your translations table.`n`n",db_num_rows($result));
		if (db_num_rows($result)==0) //table is fine, no redundant rows
			{
			output("Congratulations! Your translation table does not have any redundant entries!");
			rawoutput("</form");
			break;
			}
		output("What do you want to do?`n`n`n`n");
		rawoutput("<input type='submit' name='deleteall' value='". translate_inline("Delete multiple automatically") ."' class='button'>");
		rawoutput("<input type='submit' name='listing' value='". translate_inline("Delete manually") ."' class='button'></form>");
	break;
		}
?>
