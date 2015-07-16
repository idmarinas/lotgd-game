<?php
function friendlist_faq() {
	$c = translate_inline("Return to the Contents");
	output_notl("`#<strong><center><a href='petition.php?op=faq'>$c</a></center></strong>`0",true);
	addnav("","petition.php?op=faq");
	rawoutput("<hr>");
	output("`c`&`bQuestions about Friend Lists`b`c`n");
	output("`^1. Where is it?`n");
	output("`@Just click the link at the top of the mail page.`n`n");
	output("`^2. What is it for?`n");
	output("`@You can send requests to add someone to both of your lists.`nIf the other user accepts, you will be able to see their status, location, and whether or not they are logged in.`n`n");
	output("`^3. Anything else?`n");
	output("`@You can ignore players that harass you, to prevent them from sending you mail.`nYou can ignore Admin, however their mails will still come through.`n`n");
	if (get_module_setting('allowStat')) {
		output("`^4. Sure that's it?`n");
		output("`@Oh, I forgot!;) You can turn on a preference to see how many of your friends are online.");
	}
	rawoutput("<hr>");
	output_notl("`#<strong><center><a href='petition.php?op=faq'>$c</a></center></strong>`0",true);
}
?>