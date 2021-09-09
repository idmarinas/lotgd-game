<?php

// **** NOTICE *****
// This series of scripts (collectively known as Legend of the Green Dragon
// or LotGD) is licensed according to the Creating Commons Attribution
// Non-commercial Share-alike license.  The terms of this license must be
// followed for you to legally use or distribute this software.   This
// license must be used on the distribution of any works derived from this
// work.
// Please see the file LICENSE for a full textual description of the license.txt.

use Lotgd\Core\Event\EveryRequest;

require_once 'common_common.php';

// Include some commonly needed and useful routines
require_once 'lib/redirect.php';
require_once 'lib/pageparts.php';

//-- Check if game is installed
if (
    'cli' != \substr(PHP_SAPI, 0, 3) //-- Not check if is a cli (CronJob)
    && ! \file_exists('data/installer/installed_version_data.data') //-- New file for save version of game
) {
    //-- New installer not check for update
    \LotgdResponse::pageStart('title.install', [], 'app_common');

    \LotgdNavigation::addNav('common.nav.installer', 'home.php');

    $tpl = \LotgdTheme::load('admin/_blocks/_common.html.twig');
    \LotgdResponse::pageAddContent($tpl->renderBlock('common_install', []));

    \LotgdResponse::pageEnd(false);
}

//-- This file not is necesary, so if find delete it.
elseif (\file_exists('public/installer.php') && 'cli' != \substr(PHP_SAPI, 0, 3))
{
    // here we have a nasty situation. The installer file exists (ready to be used to get out of any bad situation like being defeated etc and it is no upgrade or new installation. It MUST be deleted
    \LotgdResponse::pageStart('title.security', [], 'app_common');

    \LotgdNavigation::addNav('common.nav.home', 'index.php');

    $tpl = \LotgdTheme::load('admin/_blocks/_common.html.twig');
    \LotgdResponse::pageAddContent($tpl->renderBlock('common_security', []));

    \LotgdResponse::pageEnd(false);
}

if (isset($session['lasthit'], $session['loggedin']) && \strtotime('-'.LotgdSetting::getSetting('LOGINTIMEOUT', 900).' seconds') > $session['lasthit'] && $session['lasthit'] > 0 && $session['loggedin'])
{
    // force the abandoning of the session when the user should have been
    // sent to the fields.
    $session = [];

    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('session.timeout', [], 'app_default'));

    redirect('home.php', \LotgdTranslator::t('session.login.account.notLogged', [], 'app_default'));
}
$session['lasthit'] = \strtotime('now');

$cp = \Lotgd\Core\Kernel::COPYRIGHT;
$l  = \Lotgd\Core\Kernel::LICENSE;

do_forced_nav(ALLOW_ANONYMOUS, OVERRIDE_FORCED_NAV);

//-- Check if have a full maintenance mode activate force to log out all players
if (LotgdSetting::getSetting('fullmaintenance', 0))
{
    $title   = \LotgdTranslator::t('maintenance.server.closed.title', [], 'app_default');
    $message = \LotgdTranslator::t('maintenance.server.closed.message', [], 'app_default');

    \LotgdFlashMessages::addErrorMessage([
        'header'     => $title,
        'message'    => $message,
        'blockquote' => LotgdSetting::getSetting('maintenancenote', ''),
        'author'     => LotgdSetting::getSetting('maintenanceauthor', ''),
        'addClass'   => 'icon',
        'icon'       => 'cog loading',
        'close'      => false,
    ]);

    if (($session['user']['loggedin'] ?? false) && 0 >= ($session['user']['superuser'] & SU_DEVELOPER))
    {
        $session['user']['restorepage'] = 'news.php';

        if ($session['user']['location'] == $iname)
        {
            $session['user']['restorepage'] = 'inn.php?op=strolldown';
        }

        \LotgdKernel::get('cache.app')->delete('char-list-home-page');

        \LotgdTool::saveUser();

        \LotgdSession::invalidate();

        $session = [];

        redirect('index.php');
    }
}
//-- Check if have a maintenance mode that players cannot login anymore and show a message to log out immediateley at a safe location.
elseif (LotgdSetting::getSetting('maintenance', 0))
{
    $title   = \LotgdTranslator::t('maintenance.server.warning.title', [], 'app_default');
    $message = \LotgdTranslator::t('maintenance.server.warning.message', [], 'app_default');

    if ($session['user']['loggedin'])
    {
        $message .= \LotgdTranslator::t('maintenance.server.warning.loggedin', [], 'app_default');
    }

    \LotgdFlashMessages::addWarningMessage([
        'header'     => $title,
        'message'    => $message,
        'blockquote' => LotgdSetting::getSetting('maintenancenote', ''),
        'author'     => LotgdSetting::getSetting('maintenanceauthor', ''),
        'addClass'   => 'icon',
        'icon'       => 'cog loading',
        'close'      => false,
    ]);
}

$script = \substr(LotgdRequest::getServer('SCRIPT_NAME'), 0, \strrpos(LotgdRequest::getServer('SCRIPT_NAME'), '.'));
mass_module_prepare(['everyhit', "header-{$script}", "footer-{$script}", 'holiday', 'charstats']);

// In the event of redirects, we want to have a version of their session we
// can revert to:
$revertsession = $session;

$session['user']['loggedin'] = (bool) ($session['user']['loggedin'] ?? false);
$session['loggedin']         = $session['user']['loggedin'];

if ( ! $session['user']['loggedin'] && ! ALLOW_ANONYMOUS)
{
    redirect('login.php?op=logout');
}

$nokeeprestore = ['newday.php' => 1, 'badnav.php' => 1, 'motd.php' => 1, 'mail.php' => 1, 'petition.php' => 1];

if (OVERRIDE_FORCED_NAV)
{
    $nokeeprestore[LotgdRequest::getServer('SCRIPT_NAME')] = 1;
}

if ( ! isset($nokeeprestore[LotgdRequest::getServer('SCRIPT_NAME')]) || ! $nokeeprestore[LotgdRequest::getServer('SCRIPT_NAME')])
{
    $session['user']['restorepage'] = LotgdRequest::getServer('REQUEST_URI');
}

$session['user']['alive'] = false;

if (isset($session['user']['hitpoints']) && 0 < $session['user']['hitpoints'])
{
    $session['user']['alive'] = true;
}

$session['bufflist'] = \array_map('array_filter', $session['user']['bufflist'] ?? []);

if ( ! \is_array($session['bufflist']))
{
    $session['bufflist'] = [];
}
$session['user']['lastip'] = LotgdRequest::getServer('REMOTE_ADDR');

if ( ! LotgdRequest::getCookie('lgi') || \strlen(LotgdRequest::getCookie('lgi')) < 32)
{
    if ( ! isset($session['user']['uniqueid']) || \strlen($session['user']['uniqueid']) < 32)
    {
        $u = \md5(\microtime());
        LotgdResponse::setCookie('lgi', $u);
        $session['user']['uniqueid'] = $u;
    }
    elseif (isset($session['user']['uniqueid']))
    {
        LotgdResponse::setCookie('lgi', $session['user']['uniqueid']);
    }
}
elseif (LotgdRequest::getCookie('lgi') && '' != LotgdRequest::getCookie('lgi'))
{
    $session['user']['uniqueid'] = LotgdRequest::getCookie('lgi');
}

/**
 * Register HTTP REFERER.
 *
 * @TODO Add setting to configure if register or not.
 */
$url  = LotgdRequest::getServer('SERVER_NAME');
$uri  = LotgdRequest::getServer('HTTP_REFERER');
$site = $uri ? \parse_url($uri, PHP_URL_HOST) : '';

if ($url != $site && $uri && $site)
{
    $url = \sprintf('%s://%s/%s', LotgdRequest::getServer('REQUEST_SCHEME'), $url, LotgdRequest::getServer('REQUEST_URI'));

    $refererRepository = Doctrine::getRepository(\Lotgd\Core\Entity\Referers::class);
    $referers          = $refererRepository->findOneByUri($uri);
    $referers          = $referers ?: new \Lotgd\Core\Entity\Referers();

    $referers->setUri($uri)
        ->incrementCount()
        ->setLast(new DateTime('now'))
        ->setSite($site)
        ->setDest($url)
        ->setIp(LotgdRequest::getServer('REMOTE_ADDR'))
    ;

    \Doctrine::merge($referers);
    \Doctrine::flush();
    \Doctrine::clear();

    unset($referers, $refererRepository);
}
unset($url, $site, $uri);

$session['user']['superuser'] = $session['user']['superuser'] ?? 0;

//check the account's hash to detect player cheats which
// we don't catch elsewhere.
include_once 'lib/common.php';

$session['user']['hashorse'] = $session['user']['hashorse'] ?? 0;
$playermount                 = LotgdTool::getMount($session['user']['hashorse']);

$temp_comp  = $session['user']['companions'] ?? [];
$companions = [];

if ( ! empty($temp_comp))
{
    $companions = array_filter($temp_comp, 'is_array');
}
unset($temp_comp);

$claninfo = [];

if ($session['user']['clanid'] ?? false)
{
    $repository = \Doctrine::getRepository('LotgdCore:Clans');
    $entity     = $repository->find($session['user']['clanid']);

    if ($entity)
    {
        $claninfo = $repository->extractEntity($entity);
    }
    else
    {
        $session['user']['clanid']   = 0;
        $session['user']['clanrank'] = 0;
    }
}
else
{
    $session['user']['clanid']   = 0;
    $session['user']['clanrank'] = 0;
}

if (($session['user']['superuser'] & SU_MEGAUSER) !== 0)
{
    $session['user']['superuser'] |= SU_EDIT_USERS;
}

//Server runs in Debug mode, tell the superuser about it
if (LotgdSetting::getSetting('debug', 0) && SU_EDIT_CONFIG == ($session['user']['superuser'] & SU_EDIT_CONFIG))
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('maintenance.debug.mode', [], 'app_default'));
}

// WARNING:
// do not hook on these modulehooks unless you really need your module to run
// on every single page hit.  This is called even when the user is not
// logged in!!!
// This however is the only context where blockmodule can be called safely!
// You should do as LITTLE as possible here and consider if you can hook on
// a page header instead.
\LotgdEventDispatcher::dispatch(new EveryRequest(), EveryRequest::HIT);
modulehook('everyhit');

if ($session['user']['loggedin'])
{
    \LotgdEventDispatcher::dispatch(new EveryRequest(), EveryRequest::HIT_AUTHENTICATED);
    modulehook('everyhit-loggedin');
}

// This bit of code checks the current system load, so that high-intensity operations can be disabled or postponed during times of exceptionally high load.  Since checking system load can in itself be resource intensive, we'll only check system load once per thirty seconds, checking it against time retrieved from the database at the first load of LotgdSetting::getSetting().
global $fiveminuteload;
$lastcheck      = LotgdSetting::getSetting('systemload_lastcheck', 0);
$fiveminuteload = LotgdSetting::getSetting('systemload_lastload', 0);
$currenttime    = \time();

if ($currenttime - $lastcheck > 30)
{
    $load = \exec('uptime'); //-- Only work in Linux systems

    if ($load !== '' && $load !== '0')
    {
        $load           = \explode('load average:', $load);
        $load           = \explode(', ', $load[1]);
        $fiveminuteload = $load[1];
        LotgdSetting::saveSetting('systemload_lastload', $fiveminuteload);
        LotgdSetting::saveSetting('systemload_lastcheck', $currenttime);
    }
}
