<?php			
$central=httpget('central');
if ($central)
	{
	$redirect="&central=1";
	} else {
	$redirect="";
	}

switch($mode)
{
case "save":		//if you want to save a single translation from Edit+Insert
	require("./modules/translationwizard/save_single.php");
break; //just in case

case "picked": //save the picked one
	$intext=rawurldecode(httpget('intext'));
	$outtext=rawurldecode(httpget('outtext'));
	$login = rawurldecode(httpget('author'));
	if ($central) {
		$version=rawurldecode(httpget('version'));
		} else {
		$version=$logd_version;
		}
	$sql = "SELECT * FROM " . db_prefix("untranslated") . " WHERE BINARY intext = '$intext' AND language = '$languageschema' AND namespace = '$namespace'";
	$query=db_query($sql);
	$result=db_num_rows($query);
	if ($result==1)
		{
		$sql = "DELETE FROM " . db_prefix("untranslated") . " WHERE BINARY intext = '$intext' AND language = '$languageschema' AND namespace = '$namespace'";
		//debug($sql); break;				
		$result=db_query($sql);
		$sql2 = "INSERT INTO " . db_prefix("translations") . " (language,uri,intext,outtext,author,version) VALUES" . " ('$languageschema','$namespace','$intext','$outtext','$login','$version')";
		$result2=db_query($sql2);
		redirect("runmodule.php?module=translationwizard&op=known$redirect&error=5"); //just redirecting so you go back to the previous page after the choice
		} else
		{
		output("There was an error while processing your selected translation.");
		output_notl(" ");
		output("Please edit the translation you selected manually or delete it.");
		output_notl(" ");
		output("This might be because of an situation like a '%D' in the translation which causes errors with this kind of insert.");
		output_notl("`n");
		output("%s rows were found for the given data",$result);
		output_notl("`n");
		output("Query:");
		
		rawoutput(htmlentities($sql,ENT_COMPAT,$coding)); 		
		}
break;

case "delete": //to delete one via the delete button
	//$intext= stripslashes(rawurldecode(httpget('intext')));
	//$intext = str_replace("%", "%%", rawurldecode(httpget('intext')));
	$intext=rawurldecode(httpget('intext'));
	$sql = "DELETE FROM " . db_prefix("untranslated") . " WHERE intext = '$intext' AND language = '$languageschema' AND namespace = '$namespace'";
	//debug($sql); break;
	db_query($sql);
	$mode=""; //reset
	redirect("runmodule.php?module=translationwizard&op=known$redirect"); //just redirecting so you go back to the previous page after the deletion
break;

case "radioinsert": //insert all first occurences
	//debug($_POST);
	$table=($central?"temp_translations":"translations");	 
	$alrighty=true;
	while (list($key,$val) = each ($_POST)) {
		$original=unserialize($key);
		$translation=$val;
		//debug($original);
		//debug($translation);
		$lang=$original[0];
		$namespace=$original[1];
		//$intext=(($original[2]));  //this won't be passed over correct, due to blanks you have to rawurlencode, yet the dot becomes an underline, so it's not correct
		$sql = "SELECT intext,outtext FROM " . db_prefix($table) . " WHERE BINARY tid=".$val.";";
		$query=db_query($sql);
		$row=db_fetch_assoc($query);
		$outtext=addslashes($row['outtext']);
		$intext=addslashes($row['intext']);
		//security check
		$sql = "SELECT * FROM " . db_prefix("untranslated") . " WHERE BINARY intext = '$intext' AND language = '$lang' AND namespace = '$namespace'";
		$query=db_query($sql);
		$result=db_num_rows($query);		
		if ($result==1)
			{
			$sql = "DELETE FROM " . db_prefix("untranslated") . " WHERE BINARY intext = '$intext' AND language = '$lang' AND namespace = '$namespace'";
			debug($sql);		
			$result=db_query($sql);
			$sql2 = "INSERT INTO " . db_prefix("translations") . " (language,uri,intext,outtext,author,version) VALUES" . " ('$lang','$namespace','$intext','$outtext','$author','$version')";
			$result2=db_query($sql2);
			//debug($sql2);
			} else
			{
			output("There was an error while processing your selected translation.");
			output_notl(" ");
			output("Please edit the translation you selected manually or delete it.");
			output_notl(" ");
			output("This might be because of an situation like a '%D' in the translation which causes errors with this kind of insert.");
			output_notl("`n");
			output("%s rows were found for the given data",$result);
			output_notl("`n");
			output("Query:");
			rawoutput(htmlentities($sql,ENT_COMPAT,$coding));
			$alrighty=false;
			}
	}
	if (alrighty) redirect("runmodule.php?module=translationwizard&op=known$redirect&error=5"); //just redirecting so you go back to the previous page after the insert

break;
default:
$start=httpget('pageop');
if (!$start) $start=0;

if ($central) 
	{
	$sql= "SELECT intext AS counter FROM  ".db_prefix("temp_translations").";";
	$result = db_query($sql);
	output("%s rows are in your pulled translations table.`n`n",db_num_rows($result));
	//setup the new query
	$sql="Select ".db_prefix("untranslated").".intext, ".db_prefix("temp_translations").".language as t, ".db_prefix("untranslated").".language as u, ".db_prefix("temp_translations").".tid, ".db_prefix("temp_translations").".outtext,".db_prefix("temp_translations").".author,".db_prefix("untranslated").".namespace,".db_prefix("temp_translations").".version  from ".db_prefix("temp_translations").",".db_prefix("untranslated")." where ".db_prefix("temp_translations").".intext=".db_prefix("untranslated").".intext AND ".db_prefix("temp_translations").".language=".db_prefix("untranslated").".language ORDER BY ".db_prefix("untranslated").".intext";
	} else {
	$sql= "SELECT intext AS counter FROM ".db_prefix("untranslated").";";
	$result = db_query($sql);
	output("%s rows are in your untranslated table.`n",db_num_rows($result));
	//set up the new query
	$sql="Select ".db_prefix("untranslated").".intext, ".db_prefix("translations").".language as t, ".db_prefix("untranslated").".language as u, ".db_prefix("translations").".tid,".db_prefix("translations").".outtext,".db_prefix("translations").".author,".db_prefix("untranslated").".namespace  from ".db_prefix("translations").",".db_prefix("untranslated")." where ".db_prefix("translations").".intext=".db_prefix("untranslated").".intext AND ".db_prefix("translations").".language=".db_prefix("untranslated").".language ORDER BY ".db_prefix("untranslated").".intext";
	}
if (db_num_rows($result)==0) //table is fine, no redundant rows
	{
	output("There are no untranslated texts in the database!");
	output("Congratulations!!!");
	break;
	}
$result=db_query($sql);
$numberofallrows=db_num_rows($result);	
if ($numberofallrows==0)
	{
	if (!$central) output("`nSorry, all rows in the untranslated table have no match in any intext in your translations table.");
	if ($central) output("`nSorry, all rows in the pulled translations table have no match in any intext in your translations table.");
	break;
	}
rawoutput("<form action='runmodule.php?module=translationwizard&op=known$redirect' method='post'>");
addnav("", "runmodule.php?module=translationwizard&op=known$redirect");
if (!httppost('quickinsert')) 
	{
	output("It is recommended that you `%fix your untranslated table`0 first to prevent double rows`n`n");
	output("If you choose to 'quick insert', you insert `ball`b already known translations `bbut`b only with the first found translation.`n");
	output("You will get a preview by clicking the 'quick insert' button below.`n`n");
	rawoutput("<input type='submit' name='quickinsert' value='". translate_inline("Quick Insert") ."' class='button'>");
	output_notl("`n`n");
	output("Pick the translation for the entry in the untranslated table:`n`n");
	} else if (!httppost('quickinsertexecute'))
	{
	output("These are the entries that will be inserted. Click the button below to insert them all.");
	output_notl("`n`n");
	rawoutput("<input type='submit' name='quickinsertexecute' value='". translate_inline("Quick Insert Execution") ."' class='button'>");
	rawoutput("<input type='hidden' name='quickinsert' value='1' class='button'>");
	}
rawoutput("</form>");
$fastinsert=$result; //use the full result for insert purposes if the user wishes for, related to button -quick insert-
$sql.=" LIMIT $start,$page;";
//debug("Start: $start and $page and $numberofallrows");
//debug($result);
if ($numberofallrows>$page) $result = db_query($sql);
$rownumber=db_num_rows($result);
rawoutput("<h4 align='left'>");
if ($start>=$page && !httppost('quickinsert')) { //just display the pages if necessary and not quick insert selected
	rawoutput("<a href='runmodule.php?module=translationwizard&op=known$redirect&pageop=".($start-$page)."'>". translate_inline("Previous Page")."</a>");
	addnav("", "runmodule.php?module=translationwizard&op=known$redirect&pageop=".($start-$page)."");
	}
if ($rownumber==$page && !httppost('quickinsert')) {
	rawoutput("<a href='runmodule.php?module=translationwizard&op=known$redirect&pageop=".($page+$start)."'>". translate_inline("Next Page")."</a>");
	addnav("", "runmodule.php?module=translationwizard&op=known$redirect&pageop=".($page+$start)."");
	}
rawoutput("</h4>");
$alttext= "abcdefgh-dummy-dummy-dummy"; //hopefully this text is in no module to translate ;) as the first text
	if (httppost('quickinsert')) {
		if (httppost('quickinsertexecute')) {
			while($row=db_fetch_assoc($fastinsert))
				{
				if ($row['t']==$row['u'] && $row['t']==$languageschema && $alttext<>$row['intext'])
				{
				if ($central) {
					$version=$row['version'];
					} else {
					$version=$logd_version;
					}
				$sql = "DELETE FROM " . db_prefix("untranslated") . " WHERE BINARY intext = '".addslashes($row['intext'])."' AND language = '$languageschema' AND namespace = '{$row['namespace']}'";
				//debug($sql);				
				$result=db_query($sql);
				$sql2 = "INSERT INTO " . db_prefix("translations") . " (language,uri,intext,outtext,author,version) VALUES" . " ('$languageschema','{$row['namespace']}','".addslashes($row['intext'])."','".addslashes($row['outtext'])."','$login','$version')";
				$result2=db_query($sql2);
				//debug($sql2);
				$alttext=$row['intext'];
				}
				}
			output("All rows have been inserted.");
			} else {
			rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
			rawoutput("<tr class='trhead'><td>". translate_inline("Language") ."</td><td>". translate_inline("Original") ."</td><td>".translate_inline("Module")."</td><td>".translate_inline("Translation")."</td><td>".translate_inline("Author")."</td></tr>");	
			while($row=db_fetch_assoc($fastinsert))
				{
				if ($row['t']==$row['u'] && $row['t']==$languageschema && $alttext<>$row['intext'])
				{
				$i++;
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
				rawoutput(htmlentities($row['t'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput(htmlentities($row['intext'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput(htmlentities($row['namespace'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput(htmlentities($row['outtext'],ENT_COMPAT,$coding));				
				rawoutput("</td><td>");
				rawoutput(htmlentities($row['author'],ENT_COMPAT,$coding));
				rawoutput("</td></tr>");
				$alttext=$row['intext'];					
				}
				}
			}
			rawoutput("</table>");
		
	    } else if (db_num_rows($result)>0) {
		rawoutput("<form action='runmodule.php?module=translationwizard&op=known&mode=radioinsert$redirect' method='post'>");
		addnav("", "runmodule.php?module=translationwizard&op=known&mode=radioinsert$redirect");
		rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
		rawoutput("<tr class='trhead'><td>". translate_inline("Language") ."</td><td>". translate_inline("Original") ."</td><td>".translate_inline("Module / Translation")."</td><td>".translate_inline("Author")."</td><td>".translate_inline("Actions")."</td><td></td></tr>");			
		while($row=db_fetch_assoc($result))	{
			if ($row['t']==$row['u'] && $row['t']==$languageschema)	{
				if ($alttext<>$row['intext']) {
					//$i++;
					rawoutput("<tr class='trdark'>");
					rawoutput("<td>");
					rawoutput(htmlentities($row['t'],ENT_COMPAT,$coding));
					rawoutput("</td><td>");
					rawoutput(htmlentities($row['intext'],ENT_COMPAT,$coding));
					rawoutput("</td><td>");
					rawoutput(htmlentities($row['namespace'],ENT_COMPAT,$coding));								
					rawoutput("</td><td>");
					//rawoutput(htmlentities($row['author'],ENT_COMPAT,$coding));
					rawoutput("</td><td>");
					rawoutput("<a href='runmodule.php?module=translationwizard&op=known$redirect&mode=delete&ns=". rawurlencode($row['namespace']) ."&intext=". rawurlencode($row['intext'])."'>". translate_inline("Delete") ."</a>");
					addnav("", "runmodule.php?module=translationwizard&op=known$redirect&mode=delete&ns=". rawurlencode($row['namespace']) ."&intext=". rawurlencode($row['intext']));				
					rawoutput("</td><td>");
					rawoutput("</td></tr>");
				}
				$alttext=$row['intext'];					
				rawoutput("<tr class='trlight'>");
				rawoutput("<td>");
				rawoutput(htmlentities($row['t'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput("<input type='radio' name='".serialize(array($row['t'],$row['namespace'],rawurlencode($row['intext'])))."' value='".$row['tid']."' class='button'>");
				rawoutput("</td><td>");
				rawoutput(htmlentities($row['outtext'],ENT_COMPAT,$coding));								
				rawoutput("</td><td>");
				rawoutput(htmlentities($row['author'],ENT_COMPAT,$coding));
				rawoutput("</td><td>");
				rawoutput("<a href='runmodule.php?module=translationwizard&op=known$redirect&mode=picked&ns=". rawurlencode($row['namespace']) ."&intext=". rawurlencode($row['intext'])."&outtext=". rawurlencode($row['outtext'])."&author=". rawurlencode($row['author'])."&version=". rawurlencode($row['version']) ."'>". translate_inline("Choose") ."</a>");
				addnav("", "runmodule.php?module=translationwizard&op=known$redirect&mode=picked&ns=". rawurlencode($row['namespace']) ."&intext=". rawurlencode($row['intext'])."&outtext=". rawurlencode($row['outtext'])."&author=". rawurlencode($row['author'])."&version=". rawurlencode($row['version']));
				rawoutput("</td><td>");
				rawoutput("<a href='runmodule.php?module=translationwizard&op=edit_single&mode=save&from=".rawurlencode("module=translationwizard&op=known$redirect&ns=".$row['namespace'])."&ns=". rawurlencode($row['namespace']) ."&intext=". rawurlencode($row['intext'])."&outtext=". rawurlencode($row['outtext'])."&author=". rawurlencode($row['author'])."&version=". rawurlencode($row['version']) ."'>". translate_inline("Edit+Insert") ."</a>");
				addnav("", "runmodule.php?module=translationwizard&op=edit_single&mode=save&from=".rawurlencode("module=translationwizard&op=known$redirect&ns=".$row['namespace'])."&ns=". rawurlencode($row['namespace']) ."&intext=". rawurlencode($row['intext'])."&outtext=". rawurlencode($row['outtext'])."&author=". rawurlencode($row['author'])."&version=". rawurlencode($row['version']));	
				rawoutput("</td></tr>");
				//if ($i>$page) break;  //would need previous/next page and one more if which needs too much time. better to get all now
			}
			}
			rawoutput("</table>");
			rawoutput("<input type='submit' value='". translate_inline("Insert checked translations") ."' class='button'>");
			rawoutput("</form>");			
		}
	
}
?>