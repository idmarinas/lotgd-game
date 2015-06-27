<?php

require("common.php");

tlschema("corenews");
check_su_access(SU_MEGAUSER);

page_header("Core News");

require_once("lib/superusernav.php");
superusernav();

output("`4This section gets the newest information about the +nb Core Editions from the nb-core.org website.`n`n");

output("`\$You can delete the corenews.php file and the menu option for this will disappear. It's up to you. However, you will get notices about new Core Versions here - as well as critical security fixes.`n`n");

output("So I recommend you to keep this.`n`n");

$buttontext=translate_inline("Get the Latest News");
rawoutput("<form action='corenews.php?op=fetchnews' method='POST'>");
addnav("","corenews.php?op=fetchnews");
rawoutput("<input type='submit' class='button' value='".appoencode($buttontext)."'></form>");
$op=httpget('op');
switch ($op) {

	case "fetchnews";
		$file = file_get_contents(getsetting('corenewspath','http://corenews.nb-core.org/corenews.txt'));
		if ($file!="") {
			output_notl("`v".str_replace("\n","`n",$file));
		}
		break;
}

page_footer();

