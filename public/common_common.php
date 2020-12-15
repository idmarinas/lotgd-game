<?php
/**
 * For no repeat same parts in common and common_jaxon.
 */
\chdir(\realpath(__DIR__.'/..'));

require_once 'vendor/autoload.php'; //-- Autoload class for new options of game

//-- Include constants
require_once 'lib/constants.php';

$isDevelopment = \file_exists('config/development.config.php');
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
        return \class_exists($className);
    }
);

// Set some constant defaults in case they weren't set before the inclusion of
// common.php
\defined('OVERRIDE_FORCED_NAV') || \define('OVERRIDE_FORCED_NAV', false);
\defined('ALLOW_ANONYMOUS')     || \define('ALLOW_ANONYMOUS', false);

use Lotgd\Core\Fixed\Kernel as LotgdKernel;
use Lotgd\Core\Fixed\Locator as LotgdLocator;
use Lotgd\Core\Fixed\Session as LotgdSession;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

//-- Prepare service manager
LotgdLocator::setServiceManager(new \Lotgd\Core\ServiceManager());

//-- Configure Session
LotgdSession::instance(LotgdLocator::get(\Lotgd\Core\Session::class));

//-- Prepare LoTGD Kernel
(new Dotenv())->bootEnv(\dirname(__DIR__).'/.env');

if ($_SERVER['APP_DEBUG'])
{
    \umask(0000);

    Debug::enable();
}

LotgdKernel::instance(new Lotgd\Core\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']));
LotgdKernel::boot();

//-- Init session
try
{
    LotgdSession::bootstrapSession();
}
catch (\Throwable $th)
{
    LotgdSession::bootstrapSession(true);
}

$session = &$_SESSION['session'];

$session['user']['gentime']      = $session['user']['gentime']      ?? 0;
$session['user']['gentimecount'] = $session['user']['gentimecount'] ?? 0;
$session['user']['gensize']      = $session['user']['gensize']      ?? 0;
$session['user']['acctid']       = $session['user']['acctid']       ?? 0;
$session['user']['restorepage']  = $session['user']['restorepage']  ?? '';
$session['counter']              = ($session['counter'] ?? 0) + 1;

$y2 = "\xc0\x3e\xfe\xb3\x4\x74\x9a\x7c\x17";
$z2 = "\xa3\x51\x8e\xca\x76\x1d\xfd\x14\x63";
