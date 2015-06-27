<?php			
$central=httpget('central');
if ($central)
	{
	$redirect="&central=1";
	} else {
	$redirect="";
	}
switch ($mode)
	{
	case "truncate":
		$sql = "TRUNCATE TABLE ";
		if ($central) 
			{
			$sql.= db_prefix("temp_translations").";";
			output("Pulled translations table has been truncated.");
			} else {
			$sql.= db_prefix("untranslated").";";
			output("Untranslated table has been truncated.");
			}
		$result = db_query($sql);
			break;
		default:  //if the user hits the button just to check for duplicates
		rawoutput("<form action='runmodule.php?module=translationwizard&op=truncate$redirect&mode=truncate' method='post'>");
		addnav("", "runmodule.php?module=translationwizard&op=truncate$redirect&mode=truncate");
			if (!$central) output("`0This operation will truncate the untranslated table.`n`n`b`$ This operation can't be made undone!`b`0`n`n");
		if ($central)  output("`0This operation will truncate the pulled translations table.`n`n`b`$ This operation can't be made undone!`b`0`n`n");
		rawoutput("<input type='submit' value='". translate_inline("Execute") ."' class='button'>");
		break;
	}
?>