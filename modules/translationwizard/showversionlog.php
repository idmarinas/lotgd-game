<?php
require_once("lib/pullurl.php");
$log=file("modules/translationwizard/versions.txt");
while (list($key,$val) = each ($log))
	{
	rawoutput($val);
	output_notl("`n");
	}
?>