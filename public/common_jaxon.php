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
chdir(realpath(__DIR__.'/..'));

require_once 'vendor/autoload.php'; //-- Autoload class for new options of game

//-- Include constants
require_once 'lib/constants.php';

$isDevelopment = file_exists('config/development.config.php');
//-- Init Debugger
$debuggerMode = $isDevelopment ? \Tracy\Debugger::DEVELOPMENT : \Tracy\Debugger::PRODUCTION;
\Tracy\Debugger::enable($debuggerMode, __DIR__.'/../storage/log/tracy');
\Tracy\Debugger::timer('page-generating');
\Tracy\Debugger::timer('page-footer');
\Tracy\Debugger::$maxDepth = 5; // default: 3
//-- Extensions for Tracy
if ($isDevelopment)
{
    \Tracy\Debugger::getBar()->addPanel(new \Milo\VendorVersions\Panel());
}

//-- Autoload annotations
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(
    function ($className)
    {
        return class_exists($className);
    }
);

// Set some constant defaults in case they weren't set before the inclusion of
// common.php
defined('OVERRIDE_FORCED_NAV') || define('OVERRIDE_FORCED_NAV', false);
defined('ALLOW_ANONYMOUS') || define('ALLOW_ANONYMOUS', false);

use Lotgd\Core\Fixed\{
    Locator as LotgdLocator,
    Session as LotgdSession
};

//-- Prepare service manager
LotgdLocator::setServiceManager(new \Lotgd\Core\ServiceManager());

//-- Configure Session
LotgdSession::instance(LotgdLocator::get(\Lotgd\Core\Session::class));

//-- Init session
try
{
    LotgdSession::bootstrapSession();
}
catch (\Throwable $th)
{
    // \Tracy\Debugger::log($th); //-- Not is necesary log, only regenerate session

    LotgdSession::bootstrapSession(true);
}

$session = &$_SESSION['session'];

$session['user']['gentime'] = $session['user']['gentime'] ?? 0;
$session['user']['gentimecount'] = $session['user']['gentimecount'] ?? 0;
$session['user']['gensize'] = $session['user']['gensize'] ?? 0;
$session['user']['acctid'] = $session['user']['acctid'] ?? 0;
$session['user']['restorepage'] = $session['user']['restorepage'] ?? '';
$session['counter'] = $session['counter'] ?? 0;

$session['counter']++;

$y2 = "\xc0\x3e\xfe\xb3\x4\x74\x9a\x7c\x17";
$z2 = "\xa3\x51\x8e\xca\x76\x1d\xfd\x14\x63";

//-- Prepare static classes
require_once 'lib/class/static.php';
// Include some commonly needed and useful routines
require_once 'lib/output.php';
require_once 'lib/settings.php';
require_once 'lib/gamelog.php';
require_once 'lib/datacache.php';
require_once 'lib/sanitize.php';
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
// require_once 'lib/pageparts.php'; //-- In a Jaxon-PHP request you should not use the functions of this file, since they are intended to print html
require_once 'lib/translator.php';
require_once 'lib/jaxon.php';

// Decline static file requests back to the PHP built-in webserver
if ('cli-server' === php_sapi_name() && is_file(__DIR__.parse_url(LotgdHttp::getServer('REQUEST_URI'), PHP_URL_PATH)))
{
    return false;
}

//-- Check connection to DB
$link = DB::connect();

define('DB_CONNECTED', (false !== $link));
define('DB_CHOSEN', DB_CONNECTED);

if (DB_CONNECTED)
{
    define('LINK', $link);
}

if (! file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT) && ! defined('IS_INSTALLER'))
{
    define('NO_SAVE_USER', true);

    defined('DB_NODB') || define('DB_NODB', true);

    exit;
}
elseif (\Lotgd\Core\Application::VERSION == getsetting('installer_version', '-1') && ! defined('IS_INSTALLER'))
{
    define('IS_INSTALLER', false);
}
elseif (\Lotgd\Core\Application::VERSION != getsetting('installer_version', '-1') && ! defined('IS_INSTALLER'))
{
    define('NO_SAVE_USER', true);

    exit;
}
// If is installer check if tables are created
elseif (defined('IS_INSTALLER'))
{
    try
    {
        $repository = \Doctrine::getRepository('LotgdCore:Settings');
        $repository->findOneBy(['setting' => 'installer_version']);
    }
    catch (\Throwable $th)
    {
        defined('DB_NODB') || define('DB_NODB', true);
    }
}

if (! IS_INSTALLER && file_exists('public/installer.php')
    && \Lotgd\Core\Application::VERSION == getsetting('installer_version', '-1')
    && 'installer.php' != substr(\LotgdHttp::getServer('SCRIPT_NAME'), -13)
) {
    // here we have a nasty situation. The installer file exists (ready to be used to get out of any bad situation like being defeated etc and it is no upgrade or new installation. It MUST be deleted
    exit;
}

if (! defined('IS_INSTALLER') && ! DB_CONNECTED)
{
    defined('DB_NODB') || define('DB_NODB', true);

    exit;
}

if (
    isset($session['lasthit'])
    && isset($session['loggedin'])
    && strtotime('-'.getsetting('LOGINTIMEOUT', 900).' seconds') > $session['lasthit']
    && $session['lasthit'] > 0 && $session['loggedin']
) {
    // force the abandoning of the session when the user should have been
    // sent to the fields.
    $session = [];

    /**
     *
     * REPUSTA JAXON
     */

    \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('session.timeout', [], 'app-default'));

    return redirect('home.php', \LotgdTranslator::t('session.login.account.notLogged', [], 'app-default'));
}
$session['lasthit'] = strtotime('now');

$cp = \Lotgd\Core\Application::COPYRIGHT;
$l = \Lotgd\Core\Application::LICENSE;

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

$script = substr(LotgdHttp::getServer('SCRIPT_NAME'), 0, strrpos(LotgdHttp::getServer('SCRIPT_NAME'), '.'));
mass_module_prepare(['everyhit', "header-$script", "footer-$script", 'holiday', 'charstats']);

// In the event of redirects, we want to have a version of their session we
// can revert to:
$revertsession = $session;

$session['user']['loggedin'] = (bool) ($session['user']['loggedin'] ?? false);
$session['loggedin'] = $session['user']['loggedin'];

if (! $session['user']['loggedin'] && ! ALLOW_ANONYMOUS)
{
    exit;
}

$nokeeprestore = ['newday.php' => 1, 'badnav.php' => 1, 'motd.php' => 1, 'mail.php' => 1, 'petition.php' => 1];

if (OVERRIDE_FORCED_NAV)
{
    $nokeeprestore[LotgdHttp::getServer('SCRIPT_NAME')] = 1;
}

if (! isset($nokeeprestore[LotgdHttp::getServer('SCRIPT_NAME')]) || ! $nokeeprestore[LotgdHttp::getServer('SCRIPT_NAME')])
{
    $session['user']['restorepage'] = LotgdHttp::getServer('REQUEST_URI');
}

$session['user']['alive'] = false;

if (isset($session['user']['hitpoints']) && 0 < $session['user']['hitpoints'])
{
    $session['user']['alive'] = true;
}

$session['bufflist'] = array_map('array_filter', $session['user']['bufflist'] ?? []);

if (! is_array($session['bufflist']))
{
    $session['bufflist'] = [];
}
$session['user']['lastip'] = LotgdHttp::getServer('REMOTE_ADDR');

if (! LotgdHttp::getCookie('lgi') || strlen(LotgdHttp::getCookie('lgi')) < 32)
{
    if (! isset($session['user']['uniqueid']) || strlen($session['user']['uniqueid']) < 32)
    {
        $u = md5(microtime());
        LotgdHttp::setCookie('lgi', $u);
        $session['user']['uniqueid'] = $u;
    }
    elseif (isset($session['user']['uniqueid']))
    {
        LotgdHttp::setCookie('lgi', $session['user']['uniqueid']);
    }
}
elseif (LotgdHttp::getCookie('lgi') && '' != LotgdHttp::getCookie('lgi'))
{
    $session['user']['uniqueid'] = LotgdHttp::getCookie('lgi');
}

$session['user']['superuser'] = $session['user']['superuser'] ?? 0;

//check the account's hash to detect player cheats which
// we don't catch elsewhere.
include_once 'lib/common.php';

$session['user']['hashorse'] = $session['user']['hashorse'] ?? 0;
$playermount = getmount($session['user']['hashorse']);

$temp_comp = $session['user']['companions'] ?? [];
$companions = [];

if (! empty($temp_comp))
{
    foreach ($temp_comp as $name => $companion)
    {
        if (is_array($companion))
        {
            $companions[$name] = $companion;
        }
    }
}
unset($temp_comp);

$beta = getsetting('beta', 0);

if (! $beta && 1 == getsetting('betaperplayer', 1))
{
    $beta = $session['user']['beta'] ?? 0;
}

$claninfo = [];

if ($session['user']['clanid'] ?? false)
{
    $repository = \Doctrine::getRepository('LotgdCore:Clans');
    $entity = $repository->find($session['user']['clanid']);

    if ($entity)
    {
        $claninfo = $repository->extractEntity($entity);
    }
    else
    {
        $session['user']['clanid'] = 0;
        $session['user']['clanrank'] = 0;
    }
}
else
{
    $session['user']['clanid'] = 0;
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
modulehook('everyhit');

if ($session['user']['loggedin'])
{
    modulehook('everyhit-loggedin');
}

// This bit of code checks the current system load, so that high-intensity operations can be disabled or postponed during times of exceptionally high load.  Since checking system load can in itself be resource intensive, we'll only check system load once per thirty seconds, checking it against time retrieved from the database at the first load of getsetting().
global $fiveminuteload;
$lastcheck = getsetting('systemload_lastcheck', 0);
$fiveminuteload = getsetting('systemload_lastload', 0);
$currenttime = time();

if ($currenttime - $lastcheck > 30)
{
    $load = exec('uptime'); //-- Only work in Linux systems
    if ($load)
    {
        $load = explode('load average:', $load);
        $load = explode(', ', $load[1]);
        $fiveminuteload = $load[1];
        savesetting('systemload_lastload', $fiveminuteload);
        savesetting('systemload_lastcheck', $currenttime);
    }
}
