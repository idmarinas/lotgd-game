<?php
switch($mode)
{
case "continue":
	output("Commencing...");
	//$sql="DELETE temp_translations FROM ".db_prefix("translations").",temp_translations WHERE ".db_prefix("translations").".intext=temp_translations.intext AND ".db_prefix("translations").".language=temp_translations.language AND ".db_prefix("translations").".uri=temp_translations.uri;";
	$sql="DELETE from ".db_prefix("temp_translations")." using ".db_prefix("translations")." inner join ".db_prefix("temp_translations")." on  ".db_prefix("translations").".intext=".db_prefix("temp_translations").".intext AND ".db_prefix("translations").".language=".db_prefix("temp_translations").".language AND ".db_prefix("translations").".uri=".db_prefix("temp_translations").".uri;";
	$result=db_query($sql);
	debug("Result for the delete:".$result);
	$sql="Select language,uri,intext,outtext,author,version from ".db_prefix("temp_translations").";";
	$result=db_query($sql);
	if (db_num_rows($result)<>0)
	{
	$copyrows="INSERT INTO ".db_prefix("translations")." (language, uri, intext, outtext, author, version) VALUES ";
	while($row=db_fetch_assoc($result))
		{
		$copyrows=$copyrows."('".$row['language']."','".$row['uri']."','".addslashes($row['intext'])."','".addslashes($row['outtext'])."','".addslashes($row['author'])."','".$row['version']."'),";
		}
	$res=db_query(substr($copyrows,0,strlen-1).";");
	invalidatedatacache("translations-".$namespace."-".$languageschema);
	db_query("TRUNCATE ".db_prefix("temp_translations").";");
	output("%s rows have been inserted and the pulled translations table has been cleared.",db_num_rows($result));
	output_notl("`n`n");
	output("The insert has been %s.",($res==1?translate_inline("successful"):translate_inline("`$ not successful`0")));
	output_notl("`n");
	output("Please `%fix`0 your untranslated table now.");
	} else
	{
	output("The pulled translations is now empty, all pulled rows are already in your translations table.");
	}
	break;
	
default:
	output("You may hereby insert `b`$ ALL `0`b rows from the pulled translations table who are not in your current translations yet.");
	output_notl(" ");
	output("This would be wise if you just installed the game and pulled a few translations down.");
	output_notl("`n`n");
	$sql="Select * from ".db_prefix("temp_translations").";";
	$result=db_query($sql);
	if (db_num_rows($result))
	{
		output("You have %s entries in your pulled translations table.",db_num_rows($result));
		output_notl("`n");
		output("This may take some time.");
		output_notl("`n`n");
		rawoutput("<form action='runmodule.php?module=translationwizard&op=insert_central&mode=continue' method='post'>");
		addnav("", "runmodule.php?module=translationwizard&op=insert_central&mode=continue");
		rawoutput("<input type='submit' name='continue' value='". translate_inline("Commence the process")."' class='button'>");
		rawoutput("</form>");
	} else {
		output("The pulled translations is empty, there is nothing to do!");
	}



}


?>