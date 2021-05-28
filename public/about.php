<?php

// translator ready
// addnews ready
// mail ready

use Lotgd\Core\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

\define('ALLOW_ANONYMOUS', true);
require_once 'common.php';
require_once 'lib/showform.php';

//-- Init page
\LotgdResponse::pageStart('title', [], 'page_about');

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

$params = [
    'block_tpl' => 'about_home',
];

if ('listmodules' == $op)
{
    LotgdNavigation::blockLink('about.php?op=listmodules');

    $params['block_tpl'] = 'about_modules';

    $repository       = \Doctrine::getRepository(\Lotgd\Core\Entity\Modules::class);
    $params['result'] = $repository->findBy(['active' => 1], ['category' => 'ASC', 'formalname' => 'ASC']);
}
elseif ('setup' == $op)
{
    LotgdNavigation::blockLink('about.php?op=setup');

    $params['block_tpl'] = 'about_setup';

    $details       = gametimedetails();
    $secstonextday = secondstonextgameday($details);
    $useful_vals   = [
        'dayduration'   => \round(($details['dayduration'] / 60 / 60), 0).' hours',
        'curgametime'   => getgametime(),
        'curservertime' => \date('Y-m-d h:i:s a'),
        'lastnewday'    => \date('h:i:s a', \strtotime("-{$details['realsecssofartoday']} seconds")),
        'nextnewday'    => \date('h:i:s a', \strtotime("+{$details['realsecstotomorrow']} seconds")).' ('.\date('H\\h i\\m s\\s', $secstonextday).')',
    ];

    $settings = \LotgdKernel::get(Lotgd\Core\Lib\Settings::class);
    $localsettings = $settings->getArray();

    $vals = \array_merge($localsettings, $useful_vals);

    $data = [
        'game_setup' => $vals,
        'newday'     => $vals,
        'bank'       => $vals,
        'forest'     => $vals,
        'mail'       => $vals,
        'content'    => $vals,
        'info'       => $vals,
    ];

    $lotgdFormFactory = \LotgdKernel::get('form.factory');

    $form = $lotgdFormFactory->create(Lotgd\Core\Form\AboutType::class, $data, [
        'action' => 'none',
        'method' => 'none'
    ]);

    $params['form'] = $form->createView();
}
elseif ('license' == $op)
{
    LotgdNavigation::blockLink('about.php?op=license');

    $params['block_tpl'] = 'about_license';
}
else
{
    LotgdNavigation::blockLink('about.php');

    $args = new GenericEvent();
    \LotgdEventDispatcher::dispatch($args, Events::PAGE_ABOUT);
    $results = modulehook('about', $args->getArguments());

    if (\is_array($results) && \count($results))
    {
        $params['hookAbout'] = $results;
    }
}

$args = new GenericEvent(null, $params);
\LotgdEventDispatcher::dispatch($args, Events::PAGE_ABOUT_POST);
$params = modulehook('page-about-tpl-params', $args->getArguments());
\LotgdResponse::pageAddContent(\LotgdTheme::render('admin/page/about.html.twig', $params));

//-- Finalize page
\LotgdResponse::pageEnd();
