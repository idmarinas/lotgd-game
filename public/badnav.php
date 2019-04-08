<?php

// translator ready
// addnews ready
// mail ready
define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';
require_once 'lib/villagenav.php';

tlschema('badnav');

if (($session['user']['loggedin'] ?? false) && ($session['loggedin'] ?? false))
{
    if (isset($session['output']) && false !== strpos($session['output'], '<!--CheckNewDay()-->'))
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

    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\AccountsOutput::class);
    $output = $repository->getOutput($session['user']['acctid']);

    if ('' != $output)
    {
        $output = gzuncompress($output);
    }
    //check if the output needs to be unzipped again
    //and make sure '' is not within gzuncompress -> error
    if ('' != $output && false !== strpos('HTML', $output))
    {
        $output = gzuncompress($output);
    }

    if (! is_array($session['user']['allowednavs']) || 0 == count($session['user']['allowednavs']) || '' == $output)
    {
        $session['user']['allowednavs'] = [];
        page_header('title', [], 'page-badnav');

        if ($session['user']['alive'])
        {
            \LotgdNavigation::villageNav();
        }
        else
        {
            \LotgdNavigation::addNav('badnav.shades', 'shades.php');
        }

        rawoutput(LotgdTheme::renderThemeTemplate('page/badnav.twig', []));

        page_footer();
    }

    echo $output;

    if ($session['user']['superuser'] & SU_MEGAUSER)
    {
        \LotgdNavigation::addNavAllow("user.php?op=special&userid={$session['user']['acctid']}");
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

    return redirect('index.php');
}
