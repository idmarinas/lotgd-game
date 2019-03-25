<?php

use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ValueGenerator;

$success = true;
$initial = false;
$params = [
    'initial' => $initial,
    'success' => $success
];
if (! file_exists(\Lotgd\Core\Application::FILE_DB_CONNECT))
{
    $initial = true;
    $configuration = [
        'lotgd_core' => [
            'db' => [
                'adapter' => [
                    'driver' => $session['installer']['dbinfo']['DB_DRIVER'],
                    'hostname' => $session['installer']['dbinfo']['DB_HOST'],
                    'database' => $session['installer']['dbinfo']['DB_NAME'],
                    'charset' => 'utf8',
                    'username' => $session['installer']['dbinfo']['DB_USER'],
                    'password' => $session['installer']['dbinfo']['DB_PASS']
                ],
                'prefix' => $session['installer']['dbinfo']['DB_PREFIX']
            ]
        ]
    ];

    $file = FileGenerator::fromArray([
        'docblock' => DocBlockGenerator::fromArray([
            'shortDescription' => 'This file is automatically created by installer.php',
            'longDescription' => null,
            'tags' => [
                [
                    'name' => 'create',
                    'description' => date('M d, Y h:i a'),
                ],
            ],
        ]),
        'body' => 'return '.new ValueGenerator($configuration, ValueGenerator::TYPE_ARRAY_SHORT).';',
    ]);
    unset($configuration);

    $code = $file->generate();

    $result = file_put_contents(\Lotgd\Core\Application::FILE_DB_CONNECT, $code);

    $failure = ! (false !== $result);
    $success = ! ($failure);
}

//-- Delete the old cache file from the Service Manager and force it to generate it again.
if (file_exists(\Lotgd\Core\ServiceManager::CACHE_FILE))
{
    unlink(\Lotgd\Core\ServiceManager::CACHE_FILE);
}

if (! $success)
{
    $session['installer']['stagecompleted'] = 5;
}

$params['initial'] = $initial;
$params['success'] = $success;
$params['failure'] = $failure ?? null;
$params['FILE_DB_CONNECT'] = \Lotgd\Core\Application::FILE_DB_CONNECT;
$params['CACHE_FILE'] = \Lotgd\Core\ServiceManager::CACHE_FILE;
$params['code'] = $code ?? null;

rawoutput(LotgdTheme::renderLotgdTemplate('core/pages/installer/stage-6.twig', $params));
