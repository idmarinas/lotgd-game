<?php
// translator ready
// addnews ready
// mail ready
define('ALLOW_ANONYMOUS', true);

require_once 'common.php';
require_once 'lib/showform.php';
require_once 'lib/http.php';

tlschema('about');

page_header('About Legend of the Green Dragon Core Engine');
$details = gametimedetails();

checkday();
$op = httpget('op');

if (file_exists("lib/about/about_$op.php"))
{
    require_once "lib/about/about_$op.php";
}
else
{
    /* NOTICE
	 * NOTICE This section may not be modified, please modify the (Original)
	 * NOTICE Server Specific section above.
	 * NOTICE
	 */
    rawoutput($lotgd_tpl->renderLotgdTemplate('about/original.twig', ['logd_version' => $logd_version]));

    /* NOTICE
	 * NOTICE This section is a especific for IDMarinas Edition
	 * NOTICE Please not modify/delete
	 * NOTICE Server Specific section above.
	 * NOTICE
	 */
    rawoutput($lotgd_tpl->renderLotgdTemplate('about/idmarinas.twig', ['logd_version' => $logd_version]));

    /* NOTICE
	 * NOTICE Server admins may put their own information here,
	 * NOTICE please leave the main about body untouched.
	 * NOTICE
	 */
    rawoutput($lotgd_tpl->renderThemeTemplate('pages/about.twig', ['logd_version' => $logd_version]));

    addnav('About LoGD');
    addnav('Game Setup Info', 'about.php?op=setup');
    addnav('Module Info', 'about.php?op=listmodules');
    addnav('License Info', 'about.php?op=license');

    modulehook('about');
}

if ($session['user']['loggedin'])
{
	addnav('Return to the news', 'news.php');
}
else
{
    addnav('Login page');
	addnav('Login Page', 'index.php');
}

page_footer();
