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

//-- Prepare service manager
LotgdLocator::setServiceManager(new \Lotgd\Core\ServiceManager());

//-- Configure Session
LotgdSession::instance(LotgdLocator::get(\Lotgd\Core\Session::class));

//-- Prepare LoTGD Kernel
try
{
    (new Dotenv())->bootEnv(\dirname(__DIR__).'/.env');

    LotgdKernel::instance(new Lotgd\Core\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']));
    LotgdKernel::boot();

    //-- Add Sql requests made by Doctrine in the Tracy debugger bar.
    if ($isDevelopment)
    {
        \MacFJA\Tracy\DoctrineSql::init(\LotgdKernel::get('doctrine.orm.entity_manager'), 'Symfony');
    }
}
catch (\Throwable $th)
{
    Tracy\Debugger::log($th);

    //-- Create a .env.local.php
    //-- This code will be deleted in future version
    if (\file_exists('config/autoload/local/dbconnect.php'))
    {
        //-- Check if exist old dbconnect.php and updated to new format
        //-- Only for upgrade from previous versions (1.0.0 IDMarinas edition and up / 2.7.0 IDMarinas edition and below)
        $dbconnect = include 'config/autoload/local/dbconnect.php';
        $doctrine  = $dbconnect['doctrine']['connection']['orm_default']['params'];
        $laminas   = $dbconnect['lotgd_core']['db'];

        //-- New configuration file ".env.local.php"
        $configuration = [
            'APP_ENV'           => 'prod',
            'APP_SECRET'        => \bin2hex(\random_bytes(16)),
            'DATABASE_NAME'     => $doctrine['dbname'],
            'DATABASE_PREFIX'   => $laminas['prefix'],
            'DATABASE_USER'     => $doctrine['user'],
            'DATABASE_PASSWORD' => $doctrine['password'],
            'DATABASE_HOST'     => 'localhost' == $laminas['adapter']['hostname'] ? '127.0.0.1' : $laminas['adapter']['hostname'],
            'DATABASE_DRIVER'   => $doctrine['driver'],
            'DATABASE_VERSION'  => '5.5',
        ];

        $file = Laminas\Code\Generator\FileGenerator::fromArray([
            'docblock' => Laminas\Code\Generator\DocBlockGenerator::fromArray([
                'shortDescription' => \sprintf('This file is automatically created in version %s.', Lotgd\Core\Kernel::VERSION),
                'longDescription'  => null,
                'tags'             => [
                    [
                        'name'        => 'important',
                        'description' => 'Remember configure DATABASE_VERSION with version of your DB Server',
                    ],
                    [
                        'name'        => 'created',
                        'description' => \date('M d, Y h:i a'),
                    ],
                ],
            ]),
            'body' => 'return '.new Laminas\Code\Generator\ValueGenerator($configuration, Laminas\Code\Generator\ValueGenerator::TYPE_ARRAY_SHORT).';',
        ]);

        $code   = $file->generate();
        $result = \file_put_contents('.env.local.php', $code);

        if (false !== $result)
        {
            $host = $_SERVER['HTTP_HOST'];

            \header(\sprintf('Location: //%s/%s',
                $host,
                'home.php'
            ));

            exit();
        }
        else
        {
            $message = 'Unfortunately, I was not able to write your ".env.local.php" file.<br>';
            $message .= 'You will have to create this file yourself, and upload it to your web server.<br>';
            $message .= 'The contents of this file should be as follows:<br>';
            $message .= '<blockquote><pre>'.\htmlentities($code, ENT_COMPAT, 'UTF-8').'</pre></blockquote>';
            $message .= 'Create a new file, past the entire contents from above into it.';
            $message .= "When you have that done, save the file as '.env.local.php' and upload this to the location you have LoGD at.";
            $message .= 'You can refresh this page to see if you were successful.';

            die($message);
        }
    }
    //-- End - This code will be deleted in future version
}

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
