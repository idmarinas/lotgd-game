<?php
$datum = getdate(time());
$currentdate=$datum['mon']."-".$datum['mday']."-".$datum['year'];
$currenttime=$datum['hours'].":".$datum['minutes'].":".$datum['seconds'];
$selectedlanguage=httppost('pushlanguage');
if (!$selectedlanguage) $selectedlanguage=$languageschema;
if (httppost('pushall')||httppost('pushselected')) $mode="pushall"; //set in order to get the switch right... hard because no get reasonable
switch($mode)
{
case "push":
	if (httpget('pushlanguage')) $selectedlanguage=httpget('pushlanguage');
	$sql="SELECT intext,outtext,author,version FROM ".db_prefix("translations")." where uri='".$namespace."' AND language='".$selectedlanguage."' ORDER BY intext;";
	$result=db_query($sql);
	output("Please copy the following code to a file named `b`^%s.sql`0`b and give it to the admin for your language:",$namespace);
	output_notl("`n`n");
	$sql = "SELECT uri,count(*) AS c FROM " . db_prefix("translations") . " WHERE language='".$selectedlanguage."' GROUP BY uri ORDER BY uri ASC";
	$res=db_query($sql);
	rawoutput("<form action='runmodule.php?module=translationwizard&op=push&mode=push' method='post'>");
	addnav("", "runmodule.php?module=translationwizard&op=push&mode=push");
	rawoutput("<input type='hidden' name='op' value='push'>");
	rawoutput("<input type='hidden' name='mode' value='push'>");
	rawoutput("<input type='hidden' name='pushlanguage' value='$selectedlanguage'>");	
	rawoutput("<select name='ns' onChange='this.form.submit()' >");
	while ($row = db_fetch_assoc($res))
		{
		rawoutput("<option value=\"".htmlentities($row['uri'],ENT_COMPAT,$coding)."\"".((htmlentities($row['uri'],ENT_COMPAT,$coding) == $namespace) ? "selected" : "").">".htmlentities($row['uri'],ENT_COMPAT,$coding)." ({$row['c']})</option>");
		}
	rawoutput("</select>");
	rawoutput("</form>");
	output_notl("`n`n");
	output_notl($currentdate." Verified "."Uploader:".$session['user']['login']." Time:".$currenttime);
	$start="('";
	$middle="','";
	$end="'),";
	$i=0;
	$numend=db_num_rows($result);
	while($row=db_fetch_assoc($result))
		{
		$i++;
		output_notl("`n");
		if ($i==$numend) $end="');";
		rawoutput($start.$selectedlanguage.$middle.$namespace.$middle.htmlentities(addslashes($row['intext']),ENT_COMPAT,$coding).$middle.htmlentities(addslashes($row['outtext']),ENT_COMPAT,$coding).$middle.$row['author'].$middle.$row['version'].$end);
		}
	break;

	case "pushall": //get a grip :D
		if (httppost('pushselected'))
			{
			$trans = httppost('pusharray');
			if (is_array($trans))  //setting for any intexts you might receive
				{
				$pusharray = $trans;
				}else
				{
					if ($trans) $pusharray = array($trans);
					else $pusharray = array();
				}
			}
		$sql = "SELECT uri,count(*) AS c FROM " . db_prefix("translations") . " WHERE language='".$selectedlanguage."' GROUP BY uri ORDER BY uri ASC";
		$res=db_query($sql);
		$firstrow=$currentdate." Verified "."Uploader:".$session['user']['login']." Time:".$currenttime.chr(13).chr(10);
		$start="('";
		$middle="','";
		$dir=getsetting('datacachepath',0);
		output("Directory of your cache: %s",$dir);
		output_notl("`n");
		output("`bAll done... look at your datacache directoy :)`b");	
		if (!$dir) break;
		if (!$pusharray)
			{
			$pusharray=array();
			while ($rows = db_fetch_assoc($res))
				{
				array_push($pusharray,$rows['uri']);
				}
			}
		while (list($key,$rowspace) = each ($pusharray))
			{
			$end="'),".chr(13).chr(10);
			$sql="SELECT intext,outtext,author,version FROM ".db_prefix("translations")." where uri='".$rowspace."' AND language='".$selectedlanguage."' ORDER BY intext;";
			$result=db_query($sql);
			$i=0;
			$numend=db_num_rows($result);
			$filename=$dir."/".$rowspace.'.sql';
			$file=fopen($filename,'w-');
			fwrite($file,$firstrow);
			while($row=db_fetch_assoc($result))
				{
				$i++;
				output_notl("`n");
				if ($i==$numend) $end="');";
				fwrite($file,$start.$selectedlanguage.$middle.$rowspace.$middle.addslashes($row['intext']).$middle.addslashes($row['outtext']).$middle.$row['author'].$middle.$row['version'].$end);
				}
				$end="";
				fclose($file);
			}
		break;
default:
	output("Choose the language you want to push:");
	output_notl("`n");
	rawoutput("<form action='runmodule.php?module=translationwizard&op=push' name='pushi' method='post'>");
	addnav("", "runmodule.php?module=translationwizard&op=push");	
	rawoutput("<select name='pushlanguage' onChange='this.form.submit()'>");
	$sql = "SELECT language,count(*) AS c FROM " . db_prefix("translations") . " GROUP BY language ORDER BY language ASC";
	$result=db_query($sql);
	while ($row = db_fetch_assoc($result))
		{
		rawoutput("<option value=\"".htmlentities($row['language'],ENT_COMPAT,$coding)."\"".((htmlentities($row['language'],ENT_COMPAT,$coding) == $selectedlanguage) ? "selected" : "").">".htmlentities($row['language'],ENT_COMPAT,$coding)." ({$row['c']}) </option>");
		}
	rawoutput("</select>");
	output_notl("`n");	
	$sql = "SELECT uri,count(*) AS c FROM " . db_prefix("translations") . " WHERE language='".$selectedlanguage."' GROUP BY uri ORDER BY uri ASC";
	$result=db_query($sql);
	output("Following namespace were found on your LotGD:`n");
	output_notl("`n`n");
	rawoutput("<input type='submit' name='pushall' value='". translate_inline("Push entire translations") ."' class='button'>");
	rawoutput("<input type='submit' name='pushselected' value='". translate_inline("Push selected namespaces") ."' class='button'>");
	output_notl("`n");
	output("The buttons above dump your translations to sql files... only use this if you have a data caching dir specified!");
	output(" This takes some time...");
	output_notl("`n`n");
	output("Select the namespace from your translations you want to push.");
	output_notl("`n");
	rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
	rawoutput("<tr class='trhead'><td></td><td>". translate_inline("Namespace") ."</td><td>".translate_inline("# of rows")."</td><td>".translate_inline("Actions")."</td></tr>");
	while ($row = db_fetch_assoc($result))
		{
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
		rawoutput("<input type='checkbox' name='pusharray[]' value='".rawurlencode($row['uri'])."' >");
		rawoutput("</td><td>");
		rawoutput($row['uri']);
		rawoutput("</td><td>");
		rawoutput($row['c']);
		rawoutput("</td><td>");
		rawoutput("<a href='runmodule.php?module=translationwizard&op=push&mode=push&pushlanguage=$selectedlanguage&ns=". rawurlencode($row['uri'])."'>". translate_inline("Push")."</a>");
		addnav("", "runmodule.php?module=translationwizard&op=push&mode=push&pushlanguage=$selectedlanguage&ns=". rawurlencode($row['uri']));
		rawoutput("</td></tr>");
		}
	rawoutput("</table>");
	rawoutput("</form>");
}
?>