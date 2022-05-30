<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Idmarinas\TracyPanel\Twig\TracyExtension;
use Idmarinas\TracyPanel\TwigBar;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\FileGenerator;
use Laminas\Code\Generator\ValueGenerator;
use Lotgd\Core\Exception\Exception;
use Lotgd\Core\Fixed\Doctrine;
use Lotgd\Core\Fixed\EventDispatcher as LotgdEventDispatcher;
use Lotgd\Core\Fixed\FlashMessages as LotgdFlashMessages;
use Lotgd\Core\Fixed\Format as LotgdFormat;
use Lotgd\Core\Fixed\Kernel as LotgdKernel;
use Lotgd\Core\Fixed\Log as LotgdLog;
use Lotgd\Core\Fixed\Navigation as LotgdNavigation;
use Lotgd\Core\Fixed\Request as LotgdRequest;
use Lotgd\Core\Fixed\Response as LotgdResponse;
use Lotgd\Core\Fixed\Sanitize as LotgdSanitize;
use Lotgd\Core\Fixed\Session as LotgdSession;
use Lotgd\Core\Fixed\Setting as LotgdSetting;
use Lotgd\Core\Fixed\Theme as LotgdTheme;
use Lotgd\Core\Fixed\Tool as LotgdTool;
use Lotgd\Core\Fixed\Translator as LotgdTranslator;
use MacFJA\Tracy\DoctrineSql;
use Milo\VendorVersions\Panel;
use Tracy\Debugger;
use Twig\Extension\ProfilerExtension;
use Twig\Profiler\Profile;

/*
 * For no repeat same parts in common and common_jaxon.
 */
chdir(realpath(__DIR__.'/..'));

require \dirname(__DIR__).'/config/bootstrap.php';
//-- Include constants
require_once 'src/constants.php';

//-- Autoload annotations
AnnotationRegistry::registerLoader(
    function ($className)
    {
        return class_exists($className);
    }
);

// Set some constant defaults in case they weren't set before the inclusion of
// common.php
\defined('OVERRIDE_FORCED_NAV') || \define('OVERRIDE_FORCED_NAV', false);
\defined('ALLOW_ANONYMOUS')     || \define('ALLOW_ANONYMOUS', false);

$isDevelopment = 'prod' != $_SERVER['APP_ENV'];
//-- Init Debugger
$debuggerMode = $isDevelopment ? Debugger::DEVELOPMENT : Debugger::PRODUCTION;
Debugger::enable($debuggerMode, __DIR__.'/../storage/log/tracy');
Debugger::timer('page-generating');
Debugger::timer('page-footer');
Debugger::$maxDepth = 5; // default: 3
//-- Extensions for Tracy
if ($isDevelopment)
{
    Debugger::getBar()->addPanel(new Panel());
}

//-- Prepare LoTGD Kernel
try
{
    LotgdKernel::instance(new Lotgd\Core\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']));
    LotgdKernel::boot();

    if ($isDevelopment)
    {
        //-- Add Twig template in the Tracy debugger bar.
        $profile = new Profile();
        $twig    = LotgdKernel::get('twig');
        $twig->addExtension(new ProfilerExtension($profile));
        $twig->addExtension(new TracyExtension());

        TwigBar::init($profile);

        //-- Add Sql requests made by Doctrine in the Tracy debugger bar.
        DoctrineSql::init(LotgdKernel::get('doctrine.orm.entity_manager'), 'Symfony');
    }
}
catch (Throwable $th)
{
    Debugger::log($th);
}

/*
 * Prepare static classes
 */

//-- Configure Session
LotgdSession::instance(LotgdKernel::get('session'));
//-- Init session
LotgdSession::start();

//-- Configure Doctrine
Doctrine::instance(LotgdKernel::get('doctrine.orm.entity_manager'));
//-- Configure Flash Messages
LotgdFlashMessages::instance(LotgdKernel::get('session')->getFlashBag());
//-- Configure format instance
LotgdFormat::instance(LotgdKernel::get(\Lotgd\Core\Output\Format::class));
//-- Configure Request instance
LotgdRequest::instance(LotgdKernel::get(\Lotgd\Core\Http\Request::class));
//-- Configure Response instance
LotgdResponse::instance(LotgdKernel::get(\Lotgd\Core\Http\Response::class));
//-- Configure Navigation instance
LotgdNavigation::instance(LotgdKernel::get(\Lotgd\Core\Navigation\Navigation::class));
//-- Configure Theme template
LotgdTheme::instance(LotgdKernel::get('twig'));
//-- Configure Sanitize instance
LotgdSanitize::instance(LotgdKernel::get(\Lotgd\Core\Tool\Sanitize::class));
//-- Configure Translator
LotgdTranslator::instance(LotgdKernel::get('translator'));
//-- Configure Event dispatcher instance
LotgdEventDispatcher::instance(LotgdKernel::get('event_dispatcher'));
//-- Configure Log instance
LotgdLog::instance(LotgdKernel::get('lotgd.core.log'));
//-- Configure Tool instance
LotgdTool::instance(LotgdKernel::get('lotgd.core.tools'));
//-- Configure Settings instance
LotgdSetting::instance(LotgdKernel::get('lotgd_core.settings'));

$session = &$_SESSION['session'];

$session['user']['gentime']      = $session['user']['gentime']      ?? 0;
$session['user']['gentimecount'] = $session['user']['gentimecount'] ?? 0;
$session['user']['gensize']      = $session['user']['gensize']      ?? 0;
$session['user']['acctid']       = $session['user']['acctid']       ?? 0;
$session['user']['restorepage']  = $session['user']['restorepage']  ?? '';
$session['counter']              = ($session['counter'] ?? 0) + 1;

$y2 = "\xc0\x3e\xfe\xb3\x4\x74\x9a\x7c\x17";
$z2 = "\xa3\x51\x8e\xca\x76\x1d\xfd\x14\x63";

// Include some commonly needed and useful routines
require_once 'lib/su_access.php';
require_once 'lib/modules.php';
require_once 'lib/forcednavigation.php';
require_once 'lib/lotgd_mail.php';

// Decline static file requests back to the PHP built-in webserver
if ('cli-server' === \PHP_SAPI && is_file(__DIR__.parse_url(LotgdRequest::getServer('REQUEST_URI'), PHP_URL_PATH)))
{
    return false;
}
