<?php
//debug($session['user']['specialmisc']);
switch ($mode)
{
case "select":
	if (!$session['user']['specialmisc'])
	{
		$sql="Select * from ".db_prefix("translations")." WHERE";
		$sql2=$sql;
		if (!httppost('exactly')) $p="%";
		if (httppost('tid')) $sql.=" tid LIKE '$p".httppost('tid')."$p' AND";
		if (httppost('language')) $sql.=" language LIKE '$p".httppost('language')."$p' AND";
		if (httppost('uri')) $sql.=" uri LIKE '$p".httppost('uri')."$p' AND";
		if (httppost('intext')) $sql.=" intext LIKE '$p".httppost('intext')."$p' AND";
		if (httppost('outtext')) $sql.=" outtext LIKE '$p".httppost('outtext')."$p' AND";
		if (httppost('author')) $sql.=" author LIKE '$p".httppost('author')."$p' AND";
		if (httppost('version')) $sql.=" version LIKE '$p".httppost('version')."$p' AND";
		if ($sql==$sql2) redirect('runmodule.php?module=translationwizard&op=searchandedit&error=1'); //back to the roots if nothing was entered
		$session['user']['specialmisc']=serialize(array("number"=>httppost('number'),"orderbyascdesc"=>httppost('orderbyascdesc'),"orderby"=>httppost('orderby'),"exactly"=>httppost('exactly'),"tid"=>httppost('tid'),"language"=>httppost('language'),"uri"=>httppost('uri'),"intext"=>httppost('intext'),"outtext"=>httppost('outtext'),"author"=>httppost('author'),"version"=>httppost('version'))); //serialize to let the user return to his results and continue
		$orderbyascdesc=(!httppost('orderbyascdesc')?"ASC":"DESC");
		$forswitch=httppost('orderby');
		$numberof=httppost('number');
	} else {
		$query=unserialize($session['user']['specialmisc']);
		$sql="Select * from ".db_prefix("translations")." WHERE";
		$sql2=$sql;
		if (!$query['exactly']) $p="%";
		if ($query['tid']) $sql.=" tid LIKE '$p".$query['tid']."$p' AND";
		if ($query['language']) $sql.=" language LIKE '$p".$query['language']."$p' AND";
		if ($query['uri']) $sql.=" uri LIKE '$p".$query['uri']."$p' AND";
		if ($query['intext']) $sql.=" intext LIKE '$p".$query['intext']."$p' AND";
		if ($query['outtext']) $sql.=" outtext LIKE '$p".$query['outtext']."$p' AND";
		if ($query['author']) $sql.=" author LIKE '$p".$query['author']."$p' AND";
		if ($query['version']) $sql.=" version LIKE '$p".$query['version']."$p' AND";
		$orderbyascdesc=(!$query['orderbyascdesc']?"ASC":"DESC");
		$forswitch=$query['orderby'];
		$numberof=$query['number'];
		}
	$sql=substr($sql,0,strlen($sql)-3);
	$presql=$sql;
	switch ($forswitch)
		{
		case 0:
		$sql.="ORDER BY tid $orderbyascdesc LIMIT ";
		break;
		case 1:
		$sql.="ORDER BY language $orderbyascdesc LIMIT ";
		break;		
		case 2:
		$sql.="ORDER BY uri $orderbyascdesc LIMIT ";
		break;
		case 3:
		$sql.="ORDER BY intext $orderbyascdesc LIMIT ";
		break;
		case 4:
		$sql.="ORDER BY outtext $orderbyascdesc LIMIT ";
		break;		
		case 5:
		$sql.="ORDER BY author $orderbyascdesc LIMIT ";
		break;
		case 6:
		$sql.="ORDER BY version $orderbyascdesc LIMIT ";
		break;
		default:
		$sql.="ORDER BY intext LIMIT ";
		}
	$start=httpget('pageop');
	if (!$start) $start=0;
	$result=db_query($presql.";");
	$numberofallrows=db_num_rows($result);
	$sql.=$start.",".round($numberof).";";
	//debug($sql);break;
	$result=db_query($sql);
	$rownumber=db_num_rows($result);
	if ($rownumber==0) redirect('runmodule.php?module=translationwizard&op=searchandedit&error=2'); //back to the roots if nothing was found
	rawoutput("<form action='runmodule.php?module=translationwizard&op=searchandedit&mode=delete' name='editfeld' method='post' >");
	addnav("", "runmodule.php?module=translationwizard&op=searchandedit&mode=delete");
	output("%s rows have been found (Displaylimit was %s).",$numberofallrows,$numberof);
	output_notl("`n");
	output("Now viewing rows %s to %s",$start,$start+(min($numberof,$numberofallrows-$start)));
	output_notl("`n`n");
	rawoutput("<h4 align='left'>");
	if ($start>=$numberof) {
		rawoutput("<a href='runmodule.php?module=translationwizard&op=searchandedit&mode=select&pageop=".($start-$numberof)."'>". translate_inline("Previous Page")."</a>");
		addnav("", "runmodule.php?module=translationwizard&op=searchandedit&mode=select&pageop=".($start-$numberof)."");
		}
	if ($rownumber==$numberof) {
		rawoutput("<a href='runmodule.php?module=translationwizard&op=searchandedit&mode=select&pageop=".($numberof+$start)."'>". translate_inline("Next Page")."</a>");
		addnav("", "runmodule.php?module=translationwizard&op=searchandedit&mode=select&pageop=".($numberof+$start)."");
		}
	rawoutput("</h4>");
	rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
	rawoutput("<tr class='trhead'><td></td><td>". translate_inline("Tid") ."</td><td>". translate_inline("Language")."</td><td>".translate_inline("Namespace")."</td><td>".translate_inline("Intext")."</td><td>".translate_inline("Outtext")."</td><td>".translate_inline("Author")."</td><td>".translate_inline("Version")."</td><td>".translate_inline("Actions")."</td><td></td></tr>");	
	$i=0;
	while($row=db_fetch_assoc($result))
		{
		$i++;
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
		rawoutput("<input type='checkbox' name='deletetext[]' value='".$row['tid']."' >");
		rawoutput("</td><td>");
		rawoutput($row['tid']);
		rawoutput("</td><td>");
		rawoutput($row['language']);
		rawoutput("</td><td>");
		rawoutput($row['uri']);		
		rawoutput("</td><td>");
		rawoutput(htmlentities(stripslashes($row['intext']),ENT_COMPAT,$coding));
		rawoutput("</td><td>");
		rawoutput(htmlentities(stripslashes($row['outtext']),ENT_COMPAT,$coding));
		rawoutput("</td><td>");
		rawoutput(sanitize($row['author']));
		rawoutput("</td><td>");
		rawoutput($row['version']);
		rawoutput("</td><td>");
		rawoutput("<a href='runmodule.php?module=translationwizard&op=searchandedit&mode=edit&tid=".$row['tid']."'>". translate_inline("Edit")."</a>");
		addnav("", "runmodule.php?module=translationwizard&op=searchandedit&mode=edit&tid=".$row['tid']);	
		rawoutput("</td><td>");
		rawoutput("<a href='runmodule.php?module=translationwizard&op=searchandedit&mode=delete&tid=".$row['tid']."'>". translate_inline("Delete")."</a>");
		addnav("", "runmodule.php?module=translationwizard&op=searchandedit&mode=delete&tid=".$row['tid']);
		rawoutput("</td></tr>");
		}
		rawoutput("</table>");
	//some check/uncheck all
	$all=translate_inline("Check all");
	$none=translate_inline("Uncheck all");
	rawoutput("<script type='text/javascript' language='JavaScript'>
				<!-- Begin
				var checkflag = 'false';
				cb = document.forms['editfeld'].elements['deletetext[]'];
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
	if (db_num_rows($result)>1) rawoutput("<input type='button' onClick='this.value=check()' name='allcheck' value='". $all ."' class='button'>");
	rawoutput("<input type='submit' name='deletechecked' value='". translate_inline("Delete selected") ."' class='button'>");
	rawoutput("</form>");
break;

case "edit":
	if (!httpget('tid')) redirect('runmodule.php?module=translationwizard&op=searchandedit&error=3'); //back to the roots 
	$sql="SELECT * FROM ".db_prefix("translations")." WHERE tid=".httpget('tid').";";
	$result=db_query($sql);
	if (db_num_rows($result)>1) redirect('runmodule.php?module=translationwizard&op=searchandedit&error=3'); //back to the roots 
	$row=db_fetch_assoc($result);
	output("Please edit the following row. If you hit save, all values will be saved.");
	output_notl(" ");
	output("If you want to abort, just click abort (or any other navigation except 'save'.");
	output_notl("`n`n");
	rawoutput("<form action='runmodule.php?module=translationwizard&op=searchandedit&mode=save' method='post'>");
	addnav("", "runmodule.php?module=translationwizard&op=searchandedit&mode=save");
	output("TID of the row:");
	rawoutput("<input id='input' name='tid' width=5 maxlength=5 value='".$row['tid']."'>");
	output_notl("`n`n");
	output("Language of the row:");
	rawoutput("<input id='input' name='language' width=2 maxlength=2 value='".$row['language']."'>");
	output_notl("`n`n");
	output("Namespace of the row:");
	rawoutput("<input id='input' name='uri' width=65 maxlength=255 value='".$row['uri']."'>");
	output_notl("`n`n");
	output("Intext of the row:");
	output_notl("`n");
	rawoutput("<textarea name='intext' class='input' cols='60' rows='5'>".htmlentities(stripslashes($row['intext']),ENT_COMPAT,$coding)."</textarea>");
	output_notl("`n`n");
	output("Outtext of the row:");
	output_notl("`n");
	rawoutput("<textarea name='outtext' class='input' cols='60' rows='5'>".htmlentities(stripslashes($row['outtext']),ENT_COMPAT,$coding)."</textarea>");
	output_notl("`n`n");	
	output("Author of the row:");
	rawoutput("<input id='input' name='author' width=50 maxlength=50 value='".$row['author']."'>");
	output_notl("`n`n");
	output("Version of the row:");
	rawoutput("<input id='input' name='version' width=50 maxlength=50 value='".$row['version']."'>");
	output_notl("`n`n");
	rawoutput("<input type='submit' name='select' value='". translate_inline("Save")."' class='button'>");
	output("`b`$ ATTENTION`b`0");
	rawoutput("<input type='submit' name='abort' value='". translate_inline("Abort")."' class='button'>");
	rawoutput("</form>");	
break;

case "save":
	if (httppost('abort')) redirect('runmodule.php?module=translationwizard&op=searchandedit'); //back to the roots
	$sql="UPDATE ".db_prefix("translations")." set language='".httppost('language')."', uri='".httppost('uri')."', intext='".httppost('intext')."', outtext='".httppost('outtext')."', author='".httppost('author')."', version='".httppost('version')."' WHERE tid=".httppost('tid').";";
	$result=db_query($sql);
	debug($sql);
	if (!result) redirect('runmodule.php?module=translationwizard&op=searchandedit&error=4&mode=select'); //back to the roots 
	redirect('runmodule.php?module=translationwizard&op=searchandedit&error=5&mode=select'); //back to the roots, no error but success
	
break;

case "delete":
	$delrows = httppost("deletetext");
	$earlyexit=false;
	if (is_array($delrows))  //setting for any intexts you might receive
		{
		$deleterows = $delrows;
		}else
		{
		if ($delrows) $deleterows = array($delrows);
		else 
			{
			$deleterows = array();
			}
		}
	while (list($key,$val)=each($deleterows))
		{
		$earlyexit=true;		
		$sql = "DELETE FROM " . db_prefix("translations") . " WHERE tid=".$val.";";
		db_query($sql);
		//debug($sql);
		}
	//debug($sql);debug("Early:".$earlyexit);debug("tidget:".httpget('tid'));
	if ($earlyexit) redirect('runmodule.php?module=translationwizard&op=searchandedit&error=7&mode=select'); //back to the roots, no error but success
	if (!httpget('tid')) redirect('runmodule.php?module=translationwizard&op=searchandedit&error=6&mode=select'); //back to the roots 
	$sql="DELETE FROM ".db_prefix("translations")." WHERE tid=".httpget('tid').";";
	$result=db_query($sql);
	if (!result) redirect('runmodule.php?module=translationwizard&op=searchandedit&error=6&mode=select'); //back to the roots 
	redirect('runmodule.php?module=translationwizard&op=searchandedit&error=7&mode=select'); //back to the roots, no error but success
	break;

default:

	$query=unserialize($session['user']['specialmisc']);
	$session['user']['specialmisc']='';
	$orderby=array(translate_inline("Tid"),translate_inline("Language"),translate_inline("Namespace"),translate_inline("Intext"),translate_inline("Outtext"),translate_inline("Author"),translate_inline("Version"));
	output("This lets you search your translations table for a single row you want to edit or delete.");
	output_notl("`n");
	output("Usually you do this to correct a wrong translation your users encountered.");
	output_notl("`n");
	output("For those proficient with sql: The statement automatically has % at the end and the beginning of the word.");
	output_notl(" ");
	output("If you don't want that, just hit the checkbox below. You may use ?,% or the like in the text."); 
	output_notl("`n`n");
	rawoutput("<form action='runmodule.php?module=translationwizard&op=searchandedit&mode=select' method='post'>");
	addnav("", "runmodule.php?module=translationwizard&op=searchandedit&mode=select");
	output("What do you want to search for (select enter one or more criteria):");
	output_notl("`n`n");
	output("Maximum number of results:");
	rawoutput("<input id='input' name='number' width=3 maxlength=3 value='".($query['number']?$query['number']:'30')."'>");
	output_notl("`n`n");
	output("Order results by:");
	rawoutput("<select name='orderby'>");
	while (list($key,$val) = each ($orderby))
		{
		rawoutput("<option value=\"".$key."\"".($val == ($query['orderby']?httppost("orderby"):"Intext")?"selected" : "").">".$val."</option>");
		}
	rawoutput("</select>");
	rawoutput("<select name='orderbyascdesc'>");
	rawoutput("<option value=\"0\" ".(!$query['orderbyascdesc']?"selected":"").">".translate_inline("Ascending")."</option>");
	rawoutput("<option value=\"1\" ".($query['orderbyascdesc']?"selected":"").">".translate_inline("Descending")."</option>");
	rawoutput("</select>");
	output_notl("`n`n");
	output("Search exactly: ");
	rawoutput("<input type='checkbox' name='exactly' ".($query['exactly']?"checked":"").">");	
	output_notl("`n`n");
	output("TID of the row:");
	rawoutput("<input id='input' name='tid' width=5 maxlength=5 value='".$query['tid']."'>");
	output_notl("`n`n");
	output("Language of the row:");
	rawoutput("<input id='input' name='language' width=2 maxlength=2 value='".$query['language']."'>");
	output_notl("`n`n");
	output("Namespace of the row:");
	rawoutput("<input id='input' name='uri' width=65 maxlength=255 value='".$query['uri']."'>");
	output_notl("`n`n");
	output("Intext of the row:");
	output_notl("`n");
	rawoutput("<textarea name='intext' class='input' cols='60' rows='5'>".$query['intext']."</textarea>");
	output_notl("`n`n");
	output("Outtext of the row:");
	output_notl("`n");
	rawoutput("<textarea name='outtext' class='input' cols='60' rows='5'>".$query['outtext']."</textarea>");
	output_notl("`n`n");	
	output("Author of the row:");
	rawoutput("<input id='input' name='author' width=50 maxlength=50 value='".$query['author']."'>");
	output_notl("`n`n");
	output("Version of the row:");
	rawoutput("<input id='input' name='version' width=50 maxlength=50 value='".$query['version']."'>");
	output_notl("`n`n");
	rawoutput("<input type='submit' name='select' value='". translate_inline("Search")."' class='button'>");
	rawoutput("</form>");
}
?>