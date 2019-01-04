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
    if (isset($session['output']) && strpos($session['output'], '<!--CheckNewDay()-->'))
    {
        checkday();
    }

    foreach ($session['user']['allowednavs'] as $key => $val)
    {
        //hack-tastic.
        if ('' == trim($key) || 0 === $key || 'motd.php' == substr($key, 0, 8) || 'mail.php' == substr($key, 0, 8))
        {
            unset($session['user']['allowednavs'][$key]);
        }
    }
    $select = DB::select('accounts_output');
    $select->columns(['output'])
        ->where->equalto('acctid', $session['user']['acctid'])
    ;
    $row = DB::execute($select)->current();

    if ('' != $row['output'])
    {
        $row['output'] = gzuncompress($row['output']);
    }
    //check if the output needs to be unzipped again
    //and make sure '' is not within gzuncompress -> error
    if ('' != $row['output'] && false !== strpos('HTML', $row['output']))
    {
        $row['output'] = gzuncompress($row['output']);
    }

    if (! is_array($session['user']['allowednavs']) || 0 == count($session['user']['allowednavs']) || '' == $row['output'])
    {
        $session['user']['allowednavs'] = [];
        page_header('Your Navs Are Corrupted');

        if ($session['user']['alive'])
        {
            villagenav();
            output('Your navs are corrupted, please return to %s.', $session['user']['location']);
        }
        else
        {
            addnav('Return to Shades', 'shades.php');
            output('Your navs are corrupted, please return to the Shades.');
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
    saveuser();
}
else
{
    $session = [];
    translator_setup();

    redirect('index.php');
}
