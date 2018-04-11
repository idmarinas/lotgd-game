<?php
// translator ready
// addnews ready
// mail ready
define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';
require_once 'lib/villagenav.php';

tlschema('badnav');

if ($session['user']['loggedin'] && $session['loggedin'])
{
    if (isset($session['output']) && strpos($session['output'],"<!--CheckNewDay()-->"))
    {
		checkday();
    }

    foreach ($session['allowednavs'] as $key => $val)
    {
		//hack-tastic.
		if (trim($key) == '' || $key === 0 || substr($key, 0, 8) == 'motd.php' || substr($key, 0, 8) == 'mail.php') unset($session['allowednavs'][$key]);
	}
    $select = DB::select('accounts_output');
    $select->columns(['output'])
        ->where->equalto('acctid', $session['user']['acctid'])
    ;
    $row = DB::execute($select)->current();

    if ($row['output'] > '') $row['output'] = gzuncompress($row['output']);
    //check if the output needs to be unzipped again
    //and make sure '' is not within gzuncompress -> error
	if (strpos('HTML', $row['output']) !== false && $row['output'] != '') $row['output'] = gzuncompress($row['output']);
    if (! is_array($session['allowednavs']) || count($session['allowednavs']) == 0 || $row['output'] == '')
    {
		$session['allowednavs'] = [];
		page_header("Your Navs Are Corrupted");
        if ($session['user']['alive'])
        {
			villagenav();
			output("Your navs are corrupted, please return to %s.", $session['user']['location']);
        }
        else
        {
			addnav("Return to Shades", "shades.php");
			output("Your navs are corrupted, please return to the Shades.");
		}
		page_footer();
    }

	echo $row['output'];

	if ($session['user']['superuser'] & SU_MEGAUSER)
	{
        addnav('', "user.php?op=special&userid={$session['user']['acctid']}");
		echo '<br><br>';
		echo sprintf('<a href="%s">Fix your own broken navs</a>', "user.php?op=special&userid={$session['user']['acctid']}", true);
	}

	$session['debug'] = '';
	$session['user']['allowednavs'] = $session['allowednavs'];
	saveuser();
}
else
{
	$session = [];
    translator_setup();

	redirect('index.php');
}
