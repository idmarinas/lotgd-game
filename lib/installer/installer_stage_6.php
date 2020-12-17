<?php

use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\FileGenerator;
use Laminas\Code\Generator\ValueGenerator;

$success = true;
$initial = false;
$params  = [
    'initial' => $initial,
    'success' => $success,
];

if ( ! \file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT))
{
    $session['installer']['dbinfo']['DB_HOST'] = 'localhost' == $session['installer']['dbinfo']['DB_HOST'] ? '127.0.0.1' : $session['installer']['dbinfo']['DB_HOST'];
    $initial                                   = true;
    $configuration                             = [
        'lotgd_core' => [
            'db' => [
                'adapter' => [
                    'driver'   => $session['installer']['dbinfo']['DB_DRIVER'],
                    'hostname' => $session['installer']['dbinfo']['DB_HOST'],
                    'database' => $session['installer']['dbinfo']['DB_NAME'],
                    'charset'  => 'utf8',
                    'collate'  => 'utf8_unicode_ci',
                    'username' => $session['installer']['dbinfo']['DB_USER'],
                    'password' => $session['installer']['dbinfo']['DB_PASS'],
                ],
                'prefix' => $session['installer']['dbinfo']['DB_PREFIX'],
            ],
        ],
        'doctrine' => [
            'connection' => [
                'orm_default' => [
                    'params' => [
                        'driver'   => \strtolower($session['installer']['dbinfo']['DB_DRIVER']),
                        'user'     => $session['installer']['dbinfo']['DB_USER'],
                        'password' => $session['installer']['dbinfo']['DB_PASS'],
                        'dbname'   => $session['installer']['dbinfo']['DB_NAME'],
                        'host'     => $session['installer']['dbinfo']['DB_HOST'],
                        'charset'  => 'utf8',
                        'collate'  => 'utf8_unicode_ci',
                    ],
                ],
            ],
        ],
    ];

    $file = FileGenerator::fromArray([
        'docblock' => DocBlockGenerator::fromArray([
            'shortDescription' => 'This file is automatically created by installer.php',
            'longDescription'  => null,
            'tags'             => [
                [
                    'name'        => 'created',
                    'description' => \date('M d, Y h:i a'),
                ],
            ],
        ]),
        'body' => 'return '.new ValueGenerator($configuration, ValueGenerator::TYPE_ARRAY_SHORT).';',
    ]);
    unset($configuration);

    $code   = $file->generate();
    $result = \file_put_contents(\Lotgd\Core\Application::FILE_DB_CONNECT, $code);

    $failure = ! (false !== $result);
    $success = ! ($failure);
}

//-- Create or recreate if not exist or dbconnect.php not exist
if ( ! \file_exists('.env.local.php') || ! \file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT))
{
    //-- New configuration file ".env.local.php"
    $configuration = [
        'APP_ENV'           => 'prod',
        'APP_SECRET'        => \bin2hex(\random_bytes(16)),
        'DATABASE_NAME'     => $session['installer']['dbinfo']['DB_NAME'],
        'DATABASE_PREFIX'   => $session['installer']['dbinfo']['DB_PREFIX'],
        'DATABASE_USER'     => $session['installer']['dbinfo']['DB_USER'],
        'DATABASE_PASSWORD' => $session['installer']['dbinfo']['DB_PASS'],
        'DATABASE_HOST'     => 'localhost' == $session['installer']['dbinfo']['DB_HOST'] ? '127.0.0.1' : $session['installer']['dbinfo']['DB_HOST'],
        'DATABASE_DRIVER'   => \strtolower($session['installer']['dbinfo']['DB_DRIVER']),
        'DATABASE_VERSION'  => '5.5',
    ];

    $fileEnv = FileGenerator::fromArray([
        'docblock' => DocBlockGenerator::fromArray([
            'shortDescription' => 'This file is automatically created by installer.php',
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
        'body' => 'return '.new ValueGenerator($configuration, ValueGenerator::TYPE_ARRAY_SHORT).';',
    ]);

    $codeEnv   = $fileEnv->generate();
    $resultEnv = \file_put_contents('.env.local.php', $codeEnv);

    $failureEnv = ! (false !== $resultEnv);
    $successEnv = ! ($failureEnv);
}

//-- Delete the old cache file from the Service Manager and force it to generate it again.
if (\file_exists(\Lotgd\Core\ServiceManager::CACHE_FILE))
{
    \unlink(\Lotgd\Core\ServiceManager::CACHE_FILE);
}

if ( ! $success)
{
    $session['installer']['stagecompleted'] = 5;
}

$params['initial']         = $initial;
$params['success']         = $success;
$params['failure']         = $failure ?? null;
$params['successEnv']      = $successEnv;
$params['failureEnv']      = $failureEnv ?? null;
$params['FILE_DB_CONNECT'] = \Lotgd\Core\Application::FILE_DB_CONNECT;
$params['CACHE_FILE']      = \Lotgd\Core\ServiceManager::CACHE_FILE;
$params['code']            = $code    ?? null;
$params['codeEnv']         = $codeEnv ?? null;

\LotgdResponse::pageAddContent(\LotgdTheme::render('@core/pages/installer/stage-6.html.twig', $params));
