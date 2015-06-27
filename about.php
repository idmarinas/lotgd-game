<?php
// translator ready
// addnews ready
// mail ready


/** 
* \file about.php
* This file displays the 'About Lotgd' navigation and the appropriate Text including the license.
*
*
*
*/
define("ALLOW_ANONYMOUS",true);
require_once("common.php");
require_once("lib/showform.php");
require_once("lib/http.php");

tlschema("about");

page_header("About Legend of the Green Dragon Core Engine");
$details = gametimedetails();

checkday();
$op = httpget('op');

switch ($op) {
	case "setup": case "listmodules": case "license":
		require("lib/about/about_$op.php");
		break;
	default:
		require("lib/about/about_default.php");
		break;
}
if ($session['user']['loggedin']) {
	addnav("Return to the news","news.php");
}else{
	addnav("Login Page","index.php");
}
page_footer();
?>
