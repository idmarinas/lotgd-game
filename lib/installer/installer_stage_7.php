<?php
$sql_upgrade_statements = include_once 'lib/installer/installer_sqlstatements.php';

if (httppost('type') > '')
{
	if (httppost('type') == 'install')
	{
		$session['fromversion'] = '-1';
		$session['dbinfo']['upgrade'] = false;
	}
	else
	{
		$session['fromversion'] = httppost('version');
		$session['dbinfo']['upgrade'] = true;
	}
}

if (! isset($session['fromversion']) || $session['fromversion'] == '')
{
	output("`@`c`bConfirmation`b`c");
	output("`2Please confirm the following:`0`n");
	rawoutput("<form class='ui form' action='installer.php?stage=7' method='POST'>");
	rawoutput("<table class='ui very basic table'><tr><td>");
	output("`2I should:`0");
	rawoutput("</td><td>");
	$version = getsetting('installer_version', '-1');
	if ($version != '-1') $session['dbinfo']['upgrade'] = true;
	else $session['dbinfo']['upgrade'] = false;
	rawoutput("<input type='radio' value='upgrade' name='type' ".($session['dbinfo']['upgrade']? 'checked' : '').">");
	output(" `2Perform an upgrade from ");
	// output('`$For now cant do a upgrade installation.`2`n');
	if ($version == '-1') $version = '0.9.7';
	reset($sql_upgrade_statements);
    unset($sql_upgrade_statements['-1']);
	rawoutput("<select name='version' class='ui search dropdown'>");
	foreach($sql_upgrade_statements as $key => $val)
	{
		rawoutput("<option value='$key'".($version==$key?" selected":"").">$key</option>");
	}
	rawoutput("</select>");
	rawoutput("<br><input type='radio' value='install' name='type' ".($session['dbinfo']['upgrade']? '' : 'checked').">");
	output(" `2Perform a clean install.");
	rawoutput("</td></tr></table>");
	$submit = translate_inline("Submit");
	rawoutput("<input type='submit' value='$submit' class='ui button'>");
	rawoutput("</form>");

	$session['stagecompleted'] = $stage - 1;
}
else
{
	$session['stagecompleted'] = $stage;
	header("Location: installer.php?stage=".($stage+1));
	exit();
}
?>
