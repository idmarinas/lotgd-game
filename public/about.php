<?php

// translator ready
// addnews ready
// mail ready
define('ALLOW_ANONYMOUS', true);
require_once 'common.php';
require_once 'lib/showform.php';

page_header('title', [], 'page-about');

checkday();
$op = LotgdRequest::getQuery('op');

if ($session['user']['loggedin'])
{
    LotgdNavigation::addNav('common.nav.news', 'news.php');
}
else
{
    LotgdNavigation::addHeader('common.category.login');
    LotgdNavigation::addNav('common.nav.login', 'index.php');
}

LotgdNavigation::addHeader('about.category.about');
LotgdNavigation::addNav('about.nav.about', 'about.php');
LotgdNavigation::addNav('about.nav.setup', 'about.php?op=setup');
LotgdNavigation::addNav('about.nav.module', 'about.php?op=listmodules');
LotgdNavigation::addNav('about.nav.license', 'about.php?op=license');

$params = [];
if ('listmodules' == $op)
{
    LotgdNavigation::blockLink('about.php?op=listmodules');

    $repository = \Doctrine::getRepository(\Lotgd\Core\Entity\Modules::class);
    $params['result'] = $repository->findBy(['active' => 1], ['category' => 'ASC', 'formalname' => 'ASC']);

    $params = modulehook('page-about-modules-tpl-params', $params);
    rawoutput(LotgdTheme::renderThemeTemplate('page/about/modules.twig', $params));
}
elseif ('setup' == $op)
{
    LotgdNavigation::blockLink('about.php?op=setup');

    $details = gametimedetails();
    $secstonextday = secondstonextgameday($details);
    $useful_vals = [
        'dayduration' => round(($details['dayduration'] / 60 / 60), 0).' hours',
        'curgametime' => getgametime(),
        'curservertime' => date('Y-m-d h:i:s a'),
        'lastnewday' => date('h:i:s a', strtotime("-{$details['realsecssofartoday']} seconds")),
        'nextnewday' => date('h:i:s a', strtotime("+{$details['realsecstotomorrow']} seconds")).' ('.date('H\\h i\\m s\\s', $secstonextday).')'
    ];

    $localsettings = $settings->getArray();

    $vals = array_merge($localsettings, $useful_vals);

    $form = \LotgdLocator::get('Lotgd\Core\Form\About');
    $data = [
        'game_setup' => $vals,
        'newday' => $vals,
        'bank' => $vals,
        'forest' => $vals,
        'mail' => $vals,
        'content' => $vals,
        'info' => $vals,
    ];
    $form->setData($data);

    $params['form'] = $form;

    $params = modulehook('page-about-setup-tpl-params', $params);
    rawoutput(LotgdTheme::renderThemeTemplate('page/about/setup.twig', $params));
}
elseif ('license' == $op)
{
    LotgdNavigation::blockLink('about.php?op=license');

    $params = modulehook('page-about-license-tpl-params', $params);
    rawoutput(LotgdTheme::renderThemeTemplate('page/about/license.twig', $params));
}
else
{
    LotgdNavigation::blockLink('about.php');

    $results = modulehook('about', []);
    if(is_array($results) && count($results))
    {
        $params['hookAbout'] = $results;
    }

    $params = modulehook('page-about-tpl-params', $params);
    rawoutput(LotgdTheme::renderThemeTemplate('page/about.twig', $params));
}

page_footer();
