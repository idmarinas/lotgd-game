<?php

/**
 * The intention of duplicating the "common.php" file is to remove everything
 * that does not make sense in a Jaxon-PHP request.
 * This will speed up the processing of a Jaxon-PHP request a bit.
 */

// **** NOTICE *****
// This series of scripts (collectively known as Legend of the Green Dragon
// or LotGD) is licensed according to the Creating Commons Attribution
// Non-commercial Share-alike license.  The terms of this license must be
// followed for you to legally use or distribute this software.   This
// license must be used on the distribution of any works derived from this
// work.
// Please see the file LICENSE for a full textual description of the license.txt.
require_once './common_common.php';

//-- Prepare static classes
require_once 'lib/class/static.php';
// Include some commonly needed and useful routines
require_once 'lib/output.php';
require_once 'lib/settings.php';
require_once 'lib/gamelog.php';
require_once 'lib/e_rand.php';
require_once 'lib/holiday_texts.php';
require_once 'lib/arrayutil.php';
require_once 'lib/redirect.php';
require_once 'lib/debuglog.php';
require_once 'lib/su_access.php';
require_once 'lib/datetime.php';
require_once 'lib/modules.php';
require_once 'lib/tempstat.php';
require_once 'lib/buffs.php';
require_once 'lib/saveuser.php';
require_once 'lib/addnews.php';
require_once 'lib/forcednavigation.php';
require_once 'lib/mounts.php';
require_once 'lib/lotgd_mail.php';
require_once 'lib/playerfunctions.php';
require_once 'lib/translator.php';

// Decline static file requests back to the PHP built-in webserver
if ('cli-server' === \PHP_SAPI && \is_file(__DIR__.\parse_url(LotgdRequest::getServer('REQUEST_URI'), PHP_URL_PATH)))
{
    return false;
}

//-- Check connection to DB
$link = Doctrine::isConnected();

\define('DB_CONNECTED', (false !== $link));
\define('DB_CHOSEN', DB_CONNECTED);

if (DB_CONNECTED)
{
    \define('LINK', $link);
}

if ( ! \file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT) && ! \defined('IS_INSTALLER'))
{
    \define('NO_SAVE_USER', true);

    \defined('DB_NODB') || \define('DB_NODB', true);

    exit;
}
elseif (\Lotgd\Core\Application::VERSION == getsetting('installer_version', '-1') && ! \defined('IS_INSTALLER'))
{
    \define('IS_INSTALLER', false);
}
elseif (\Lotgd\Core\Application::VERSION != getsetting('installer_version', '-1') && ! \defined('IS_INSTALLER'))
{
    \define('NO_SAVE_USER', true);

    exit;
}
// If is installer check if tables are created
elseif (\defined('IS_INSTALLER'))
{
    try
    {
        $repository = \Doctrine::getRepository('LotgdCore:Settings');
        $repository->findOneBy(['setting' => 'installer_version']);
    }
    catch (\Throwable $th)
    {
        \defined('DB_NODB') || \define('DB_NODB', true);
    }
}

if ( ! IS_INSTALLER && \file_exists('public/installer.php')
    && \Lotgd\Core\Application::VERSION == getsetting('installer_version', '-1')
    && 'installer.php' != \substr(\LotgdRequest::getServer('SCRIPT_NAME'), -13)
) {
    // here we have a nasty situation. The installer file exists (ready to be used to get out of any bad situation like being defeated etc and it is no upgrade or new installation. It MUST be deleted
    exit;
}

if ( ! \defined('IS_INSTALLER') && ! DB_CONNECTED)
{
    \defined('DB_NODB') || \define('DB_NODB', true);

    exit;
}

if (
    isset($session['lasthit'], $session['loggedin'])

    && \strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds') > $session['lasthit']
    && $session['lasthit'] > 0 && $session['loggedin']
) {
    // force the abandoning of the session when the user should have been
    // sent to the fields.
    \LotgdSession::sessionLogOut();

    $session = [];

    $response = new \Jaxon\Response\Response();

    $response->dialog->warning(\LotgdTranslator::t('session.timeout', [], 'app-default'));

    return $response;
}
$session['lasthit'] = \strtotime('now');

$cp = \Lotgd\Core\Application::COPYRIGHT;
$l  = \Lotgd\Core\Application::LICENSE;

do_forced_nav(ALLOW_ANONYMOUS, OVERRIDE_FORCED_NAV);

//-- Check if have a full maintenance mode activate force to log out all players
if (getsetting('fullmaintenance', 0))
{
    if (($session['user']['loggedin'] ?? false) && 0 >= ($session['user']['superuser'] & SU_DEVELOPER))
    {
        $session['user']['restorepage'] = 'news.php';

        if ($session['user']['location'] == $iname)
        {
            $session['user']['restorepage'] = 'inn.php?op=strolldown';
        }

        LotgdCache::removeItem('charlisthomepage');
        LotgdCache::removeItem('list.php-warsonline');

        saveuser();

        \LotgdSession::sessionLogOut();

        $session = [];
    }

    exit;
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
    exit;
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
$session['user']['lastip']    = LotgdRequest::getServer('REMOTE_ADDR');
$session['user']['superuser'] = $session['user']['superuser'] ?? 0;

//check the account's hash to detect player cheats which
// we don't catch elsewhere.
include_once 'lib/common.php';

$session['user']['hashorse'] = $session['user']['hashorse'] ?? 0;
$playermount                 = getmount($session['user']['hashorse']);

$temp_comp  = $session['user']['companions'] ?? [];
$companions = [];

if ( ! empty($temp_comp))
{
    foreach ($temp_comp as $name => $companion)
    {
        if (\is_array($companion))
        {
            $companions[$name] = $companion;
        }
    }
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

if ($session['user']['superuser'] & SU_MEGAUSER)
{
    $session['user']['superuser'] = $session['user']['superuser'] | SU_EDIT_USERS;
}

// WARNING:
// do not hook on these modulehooks unless you really need your module to run
// on every single page hit.  This is called even when the user is not
// logged in!!!
// This however is the only context where blockmodule can be called safely!
// You should do as LITTLE as possible here and consider if you can hook on
// a page header instead.
\LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_EVERYHIT);
modulehook('everyhit');

if ($session['user']['loggedin'])
{
    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CORE_EVERYHIT_LOGGEDIN);
    modulehook('everyhit-loggedin');
}
