<?php

// translator ready
// addnews ready
// mail ready
\define('OVERRIDE_FORCED_NAV', true);

require_once 'common.php';

if (($session['user']['loggedin'] ?? false) && ($session['loggedin'] ?? false))
{
    if (isset($session['output']) && str_contains($session['output'], '<!--CheckNewDay()-->'))
    {
        LotgdKernel::get('lotgd_core.tool.date_time')->checkDay();
    }

    foreach ($session['user']['allowednavs'] as $key => $val)
    {
        //hack-tastic.
        if ('' === trim((string) $key) || 0 === $key || str_starts_with((string) $key, 'motd.php') || str_starts_with((string) $key, 'mail.php'))
        {
            unset($session['user']['allowednavs'][$key]);
        }
    }

    $repository = Doctrine::getRepository('LotgdCore:AccountsOutput');
    $outputHtml = $repository->getOutput($session['user']['acctid']);

    if ('' != $outputHtml)
    {
        $outputHtml = gzuncompress($outputHtml);
    }

    //check if the output needs to be unzipped again
    //and make sure '' is not within gzuncompress -> error
    if ('' != $outputHtml && str_contains('HTML', (string) $outputHtml))
    {
        $outputHtml = gzuncompress($outputHtml);
    }

    if ( ! \is_array($session['user']['allowednavs']) || 0 == \count($session['user']['allowednavs']) || '' == $outputHtml)
    {
        $session['user']['allowednavs'] = [];
        //-- Init page
        LotgdResponse::pageStart('title', [], 'page_badnav');

        if ($session['user']['alive'])
        {
            LotgdNavigation::villageNav();
        }
        else
        {
            LotgdNavigation::addNav('badnav.shades', 'shades.php');
        }

        LotgdResponse::pageAddContent(LotgdTheme::render('page/badnav.html.twig', []));

        //-- Finalize page
        LotgdResponse::pageEnd();
    }

    echo $outputHtml;

    if (($session['user']['superuser'] & SU_MEGAUSER) !== 0)
    {
        LotgdNavigation::addNavAllow("user.php?op=special&userid={$session['user']['acctid']}");
        echo '<br><br>';
        echo sprintf('<a href="%s">Fix your own broken navs</a>', "user.php?op=special&userid={$session['user']['acctid']}", true);
    }

    $session['debug'] = '';
    LotgdTool::saveUser();

    exit;
}
else
{
    $session = [];

    redirect('index.php');
}
