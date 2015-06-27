<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/commentary.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");

check_su_access(SU_IS_TRANSLATOR);
addcommentary();
tlschema("translatorlounge");

require_once("lib/superusernav.php");
superusernav();

$op = httpget('op');
page_header("Translator Lounge");

output("`^You duck into a secret cave that few know about. ");
if ($session['user']['sex']){
  	output("Inside you are greeted by the sight of numerous muscular bare-chested men who wave palm fronds at you and offer to feed you grapes as you lounge on Greco-Roman couches draped with silk.`n`n");
}else{
	output("Inside you are greeted by the sight of numerous scantily clad buxom women who wave palm fronds at you and offer to feed you grapes as you lounge on Greco-Roman couches draped with silk.`n`n");
}
commentdisplay("", "trans-lounge","Engage in idle conversation with other translators:",25);
addnav("Actions");
if ($session['user']['superuser'] & SU_IS_TRANSLATOR) addnav("U?Untranslated Texts", "untranslated.php");

page_footer();
?>
